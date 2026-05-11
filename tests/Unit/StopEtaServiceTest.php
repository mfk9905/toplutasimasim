<?php

namespace Tests\Unit;

use App\Domain\Transit\Services\Eta\EtaEstimator;
use App\Domain\Transit\Services\Eta\StopEtaService;
use App\Models\Bus;
use App\Models\Stop;
use PHPUnit\Framework\TestCase;

class StopEtaServiceTest extends TestCase
{
    public function test_forward_bus_does_not_keep_nearby_passed_stop_as_next_arrival(): void
    {
        $etas = $this->estimateForBusDirection(1);

        $this->assertGreaterThan($etas[3], $etas[2]);
    }

    public function test_backward_bus_prefers_stops_behind_its_current_route_position(): void
    {
        $etas = $this->estimateForBusDirection(-1);

        $this->assertGreaterThan($etas[2], $etas[3]);
    }

    /**
     * @return array<int, int|null>
     */
    private function estimateForBusDirection(int $direction): array
    {
        $polyline = [
            ['lat' => 38.0, 'lng' => 27.0],
            ['lat' => 38.0, 'lng' => 27.01],
            ['lat' => 38.0, 'lng' => 27.02],
        ];

        $stops = collect([
            $this->stop(1, 1, 38.0, 27.0),
            $this->stop(2, 2, 38.0, 27.01),
            $this->stop(3, 3, 38.0, 27.02),
        ]);

        $bus = new Bus([
            'current_lat' => 38.0,
            'current_lng' => 27.011,
            'speed_kmh' => 36,
            'simulation_state' => [
                'segment_index' => 1,
                'segment_progress' => 0.1,
                'direction' => $direction,
            ],
        ]);

        return (new StopEtaService(new EtaEstimator))->estimateForStops(
            buses: collect([$bus]),
            stops: $stops,
            densityByStopId: collect([1 => 0, 2 => 0, 3 => 0]),
            polyline: $polyline
        );
    }

    private function stop(int $id, int $sequence, float $lat, float $lng): Stop
    {
        $stop = new Stop([
            'name' => "Durak {$sequence}",
            'sequence' => $sequence,
            'lat' => $lat,
            'lng' => $lng,
            'avg_travel_seconds_from_prev' => 0,
        ]);
        $stop->setAttribute('id', $id);

        return $stop;
    }
}
