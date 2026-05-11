<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\Stop;
use Illuminate\Database\Eloquent\Factories\Factory;

class StopFactory extends Factory
{
    protected $model = Stop::class;

    public function definition(): array
    {
        return [
            'route_id' => Route::factory(),
            'name' => fake()->streetName() . ' Duragi',
            'sequence' => fake()->numberBetween(1, 99),
            'lat' => fake()->randomFloat(7, 38.48, 38.76),
            'lng' => fake()->randomFloat(7, 27.20, 27.70),
            'avg_travel_seconds_from_prev' => fake()->numberBetween(70, 180),
        ];
    }
}
