<?php

namespace App\Domain\Transit\Services\Eta;

class EtaEstimator
{
    public function estimateSeconds(
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
