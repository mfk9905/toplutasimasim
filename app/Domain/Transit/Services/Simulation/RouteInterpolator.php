<?php

namespace App\Domain\Transit\Services\Simulation;

class RouteInterpolator
{
    public function nextPosition(array $polyline, array $state, float $metersPerTick): array
    {
        $segmentIndex = (int) ($state['segment_index'] ?? 0);
        $segmentProgress = (float) ($state['segment_progress'] ?? 0.0);
        $direction = (int) ($state['direction'] ?? 1);

        if ($segmentIndex < 0) {
            $segmentIndex = 0;
        }

        if ($segmentIndex >= count($polyline) - 1) {
            $segmentIndex = count($polyline) - 2;
        }

        $from = $polyline[$segmentIndex];
        $to = $polyline[$segmentIndex + 1];
        $segmentMeters = $this->haversine($from['lat'], $from['lng'], $to['lat'], $to['lng']);
        $progressDelta = $segmentMeters > 0 ? $metersPerTick / $segmentMeters : 1.0;
        $newProgress = $segmentProgress + $progressDelta;

        while ($newProgress >= 1.0) {
            $newProgress -= 1.0;
            $segmentIndex += $direction;

            if ($segmentIndex >= count($polyline) - 1) {
                $segmentIndex = count($polyline) - 2;
                $direction = -1;
            } elseif ($segmentIndex <= 0) {
                $segmentIndex = 0;
                $direction = 1;
            }
        }

        $from = $polyline[$segmentIndex];
        $to = $polyline[$segmentIndex + 1];

        return [
            'lat' => $from['lat'] + (($to['lat'] - $from['lat']) * $newProgress),
            'lng' => $from['lng'] + (($to['lng'] - $from['lng']) * $newProgress),
            'state' => [
                'segment_index' => $segmentIndex,
                'segment_progress' => $newProgress,
                'direction' => $direction,
            ],
        ];
    }

    public function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }
}
