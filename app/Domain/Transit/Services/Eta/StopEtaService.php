<?php

namespace App\Domain\Transit\Services\Eta;

use App\Models\Bus;
use App\Models\Stop;
use Illuminate\Support\Collection;

class StopEtaService
{
    public function __construct(private readonly EtaEstimator $etaEstimator)
    {
    }

    /**
     * @param Collection<int, Bus> $buses
     * @param Collection<int, Stop> $stops
     * @param Collection<int, float> $densityByStopId
     * @return array<int, int|null>
     */
    public function estimateForStops(Collection $buses, Collection $stops, Collection $densityByStopId): array
    {
        $etas = [];

        foreach ($stops as $stop) {
            $best = null;
            $densityScore = (float) ($densityByStopId[$stop->id] ?? 25.0);

            foreach ($buses as $bus) {
                if ($bus->current_lat === null || $bus->current_lng === null) {
                    continue;
                }

                $distance = haversine_meters(
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
}
