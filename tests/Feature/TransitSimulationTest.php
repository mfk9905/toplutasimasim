<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Events\BusPositionUpdated;
use App\Models\Route;
use App\Models\Stop;
use Database\Seeders\TransitSimulationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransitSimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_transit_seed_creates_five_routes_and_fifty_stops(): void
    {
        $this->seed(TransitSimulationSeeder::class);

        $this->assertSame(5, Route::query()->count());
        $this->assertSame(50, Stop::query()->count());

        Route::query()->withCount(['stops', 'buses'])->get()->each(function (Route $route): void {
            $this->assertSame(10, $route->stops_count);
            $this->assertGreaterThanOrEqual(2, $route->buses_count);
        });

        $this->assertSame(10, Bus::query()->count());
    }

    public function test_live_map_endpoints_return_bootstrap_and_layer_data(): void
    {
        $this->seed(TransitSimulationSeeder::class);

        $this->getJson('/api/live-map/bootstrap')
            ->assertOk()
            ->assertJsonCount(10, 'buses')
            ->assertJsonStructure([
                'buses' => [
                    '*' => [
                        'id',
                        'route_id',
                        'route_code',
                        'route_name',
                        'route_color',
                        'plate_number',
                        'current_lat',
                        'current_lng',
                        'speed_kmh',
                        'status',
                        'direction',
                        'last_position_at',
                    ],
                ],
            ]);

        $this->getJson('/api/live-map/layers')
            ->assertOk()
            ->assertJsonCount(5, 'routes')
            ->assertJsonPath('meta.route_count', 5)
            ->assertJsonPath('meta.stop_count', 50)
            ->assertJsonStructure([
                'routes' => [
                    '*' => [
                        'id',
                        'name',
                        'code',
                        'color',
                        'polyline',
                        'stops' => [
                            '*' => [
                                'id',
                                'name',
                                'sequence',
                                'lat',
                                'lng',
                                'density_score',
                                'density_level',
                                'passenger_estimate',
                                'eta_seconds',
                            ],
                        ],
                        'analytics' => [
                            'active_bus_count',
                            'average_density',
                            'density_level',
                            'peak_stop',
                            'average_eta_seconds',
                            'operation_action',
                            'feedback_summary',
                            'model_note',
                        ],
                    ],
                ],
                'meta' => [
                    'generated_at',
                    'route_count',
                    'stop_count',
                    'active_bus_count',
                    'average_density',
                ],
            ]);
    }

    public function test_bus_position_event_includes_real_plate_number(): void
    {
        $event = new BusPositionUpdated(
            busId: 1,
            routeId: 1,
            plateNumber: '45 M1 01',
            direction: 'güzergâh ileri',
            lat: 38.619099,
            lng: 27.428921,
            speedKmh: 28.5,
            timestamp: now()->toIso8601String()
        );

        $this->assertSame('45 M1 01', $event->plateNumber);
        $this->assertSame('güzergâh ileri', $event->direction);
    }
}
