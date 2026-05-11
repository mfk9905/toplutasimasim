<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusFactory extends Factory
{
    protected $model = Bus::class;

    public function definition(): array
    {
        return [
            'route_id' => Route::factory(),
            'plate_number' => fake()->unique()->bothify('34 ??? ##'),
            'driver_name' => fake()->name(),
            'speed_kmh' => fake()->randomFloat(2, 18, 38),
            'status' => 'idle',
            'simulation_state' => [
                'segment_index' => 0,
                'segment_progress' => 0,
                'direction' => 1,
            ],
        ];
    }
}
