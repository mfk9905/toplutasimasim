<?php

namespace App\Domain\Transit\Services\Eta;

use App\Models\Bus;
use App\Models\Stop;
use Illuminate\Support\Collection;

class StopEtaService
{
    public function __construct(private readonly EtaEstimator $etaEstimator) {}

    /**
     * @param  Collection<int, Bus>  $buses
     * @param  Collection<int, Stop>  $stops
     * @param  Collection<int, float>  $densityByStopId
     * @return array<int, int|null>
     */
    public function estimateForStops(Collection $buses, Collection $stops, Collection $densityByStopId, array $polyline = []): array
    {
        $etas = [];
        $routeMetrics = $this->buildRouteMetrics($polyline);
        $stopDistanceById = $routeMetrics === null
            ? collect()
            : $stops->mapWithKeys(fn (Stop $stop): array => [
                $stop->id => $this->distanceAlongRoute(
                    (float) $stop->lat,
                    (float) $stop->lng,
                    $routeMetrics
                ),
            ]);

        foreach ($stops as $stop) {
            $best = null;
            $densityScore = (float) ($densityByStopId[$stop->id] ?? 25.0);

            foreach ($buses as $bus) {
                if ($bus->current_lat === null || $bus->current_lng === null) {
                    continue;
                }

                $distance = $this->routeDistanceToStop($bus, (float) ($stopDistanceById[$stop->id] ?? 0), $routeMetrics)
                    ?? haversine_meters(
                        (float) $bus->current_lat,
                        (float) $bus->current_lng,
                        (float) $stop->lat,
                        (float) $stop->lng
                    );

                $eta = $this->etaEstimator->estimateSeconds(
                    distanceMeters: $distance,
                    speedKmh: (float) $bus->speed_kmh,
                    avgTravelSecondsFromPrev: (int) $stop->avg_travel_seconds_from_prev,
                    densityScore: $densityScore
                );

                $best = $best === null ? $eta : min($best, $eta);
            }

            $etas[$stop->id] = $best;
        }

        return $etas;
    }

    /**
     * @return array{points: array<int, array{lat: float, lng: float}>, segment_lengths: array<int, float>, cumulative: array<int, float>, total: float}|null
     */
    private function buildRouteMetrics(array $polyline): ?array
    {
        $points = collect($polyline)
            ->map(fn (array $point): array => [
                'lat' => (float) ($point['lat'] ?? 0),
                'lng' => (float) ($point['lng'] ?? 0),
            ])
            ->values()
            ->all();

        if (count($points) < 2) {
            return null;
        }

        $segmentLengths = [];
        $cumulative = [0.0];
        $total = 0.0;

        for ($i = 0; $i < count($points) - 1; $i++) {
            $length = haversine_meters(
                $points[$i]['lat'],
                $points[$i]['lng'],
                $points[$i + 1]['lat'],
                $points[$i + 1]['lng']
            );

            $segmentLengths[$i] = $length;
            $total += $length;
            $cumulative[$i + 1] = $total;
        }

        return $total > 0 ? [
            'points' => $points,
            'segment_lengths' => $segmentLengths,
            'cumulative' => $cumulative,
            'total' => $total,
        ] : null;
    }

    /**
     * @param  array{points: array<int, array{lat: float, lng: float}>, segment_lengths: array<int, float>, cumulative: array<int, float>, total: float}  $routeMetrics
     */
    private function routeDistanceToStop(Bus $bus, float $stopDistance, ?array $routeMetrics): ?float
    {
        if ($routeMetrics === null) {
            return null;
        }

        $busDistance = $this->busDistanceAlongRoute($bus, $routeMetrics);

        if ($busDistance === null) {
            return null;
        }

        $direction = ((int) ($bus->simulation_state['direction'] ?? 1)) === -1 ? -1 : 1;
        $routeLength = $routeMetrics['total'];

        if ($direction === 1) {
            return $stopDistance >= $busDistance - 1
                ? max(0, $stopDistance - $busDistance)
                : ($routeLength - $busDistance) + ($routeLength - $stopDistance);
        }

        return $stopDistance <= $busDistance + 1
            ? max(0, $busDistance - $stopDistance)
            : $busDistance + $stopDistance;
    }

    /**
     * @param  array{points: array<int, array{lat: float, lng: float}>, segment_lengths: array<int, float>, cumulative: array<int, float>, total: float}  $routeMetrics
     */
    private function busDistanceAlongRoute(Bus $bus, array $routeMetrics): ?float
    {
        $state = $bus->simulation_state ?? [];
        $segmentCount = count($routeMetrics['segment_lengths']);

        if (array_key_exists('segment_index', $state)) {
            $segmentIndex = max(0, min($segmentCount - 1, (int) $state['segment_index']));
            $segmentProgress = max(0, min(1, (float) ($state['segment_progress'] ?? 0)));

            return $routeMetrics['cumulative'][$segmentIndex]
                + ($routeMetrics['segment_lengths'][$segmentIndex] * $segmentProgress);
        }

        return $this->distanceAlongRoute((float) $bus->current_lat, (float) $bus->current_lng, $routeMetrics);
    }

    /**
     * @param  array{points: array<int, array{lat: float, lng: float}>, segment_lengths: array<int, float>, cumulative: array<int, float>, total: float}  $routeMetrics
     */
    private function distanceAlongRoute(float $lat, float $lng, array $routeMetrics): float
    {
        $bestDistanceAlong = 0.0;
        $bestSquaredDistance = PHP_FLOAT_MAX;
        $originLat = $lat;
        $earthRadius = 6371000;

        [$pointX, $pointY] = $this->projectToMeters($lat, $lng, $originLat, $earthRadius);

        for ($i = 0; $i < count($routeMetrics['points']) - 1; $i++) {
            $from = $routeMetrics['points'][$i];
            $to = $routeMetrics['points'][$i + 1];
            [$fromX, $fromY] = $this->projectToMeters($from['lat'], $from['lng'], $originLat, $earthRadius);
            [$toX, $toY] = $this->projectToMeters($to['lat'], $to['lng'], $originLat, $earthRadius);

            $dx = $toX - $fromX;
            $dy = $toY - $fromY;
            $lengthSquared = ($dx * $dx) + ($dy * $dy);
            $t = $lengthSquared > 0
                ? max(0, min(1, ((($pointX - $fromX) * $dx) + (($pointY - $fromY) * $dy)) / $lengthSquared))
                : 0;

            $projectedX = $fromX + ($dx * $t);
            $projectedY = $fromY + ($dy * $t);
            $squaredDistance = (($pointX - $projectedX) ** 2) + (($pointY - $projectedY) ** 2);

            if ($squaredDistance < $bestSquaredDistance) {
                $bestSquaredDistance = $squaredDistance;
                $bestDistanceAlong = $routeMetrics['cumulative'][$i] + ($routeMetrics['segment_lengths'][$i] * $t);
            }
        }

        return max(0, min($routeMetrics['total'], $bestDistanceAlong));
    }

    /**
     * @return array{0: float, 1: float}
     */
    private function projectToMeters(float $lat, float $lng, float $originLat, float $earthRadius): array
    {
        return [
            deg2rad($lng) * $earthRadius * cos(deg2rad($originLat)),
            deg2rad($lat) * $earthRadius,
        ];
    }
}
