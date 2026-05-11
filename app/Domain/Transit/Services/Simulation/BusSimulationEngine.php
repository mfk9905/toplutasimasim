<?php

namespace App\Domain\Transit\Services\Simulation;

use App\Events\BusPositionUpdated;
use App\Models\Bus;
use App\Models\BusLog;
use Carbon\CarbonImmutable;

class BusSimulationEngine
{
    public function __construct(private readonly RouteInterpolator $interpolator)
    {
    }

    public function tick(Bus $bus): void
    {
        $polyline = $bus->route?->polyline ?? [];

        if (count($polyline) < 2) {
            return;
        }

        $speedKmh = max(10, (float) $bus->speed_kmh);
        $metersPerTick = ($speedKmh * 1000) / 3600;

        $position = $this->interpolator->nextPosition(
            polyline: $polyline,
            state: $bus->simulation_state ?? [],
            metersPerTick: $metersPerTick
        );

        $now = CarbonImmutable::now();

        $bus->update([
            'current_lat' => $position['lat'],
            'current_lng' => $position['lng'],
            'simulation_state' => $position['state'],
            'status' => 'in_service',
            'last_position_at' => $now,
        ]);

        if (((int) $now->format('s')) % 3 === 0) {
            BusLog::create([
                'bus_id' => $bus->id,
                'lat' => $position['lat'],
                'lng' => $position['lng'],
                'speed_kmh' => $speedKmh,
                'recorded_at' => $now,
            ]);
        }

        broadcast(new BusPositionUpdated(
            busId: $bus->id,
            routeId: $bus->route_id,
            plateNumber: $bus->plate_number,
            direction: ((int) ($position['state']['direction'] ?? 1)) === 1 ? 'güzergâh ileri' : 'güzergâh geri',
            lat: (float) $position['lat'],
            lng: (float) $position['lng'],
            speedKmh: $speedKmh,
            timestamp: $now->toIso8601String()
        ));
    }
}
