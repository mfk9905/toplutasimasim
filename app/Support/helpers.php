<?php

if (! function_exists('haversine_meters')) {
    function haversine_meters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }
}

if (! function_exists('estimate_eta_seconds')) {
    function estimate_eta_seconds(
        float $distanceMeters,
        float $speedKmh,
        int $avgTravelSecondsFromPrev,
        float $densityScore
    ): int {
        $speedMs = max(3.0, ($speedKmh * 1000) / 3600);
        $movementSeconds = (int) round($distanceMeters / $speedMs);
        $densityPenalty = (int) round(($densityScore / 100) * 90);

        return max(30, $movementSeconds + $avgTravelSecondsFromPrev + $densityPenalty);
    }
}
