<?php

namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        // Manisa city center
        $centerLat = 38.619099;
        $centerLng = 27.428921;
        $pointCount = fake()->numberBetween(24, 40);

        $polyline = [];
        for ($i = 0; $i < $pointCount; $i++) {
            $polyline[] = [
                'lat' => $centerLat + sin($i / 4) * 0.04 + fake()->randomFloat(6, -0.0015, 0.0015),
                'lng' => $centerLng + cos($i / 4) * 0.04 + fake()->randomFloat(6, -0.0015, 0.0015),
            ];
        }

        return [
            'name' => 'Hat ' . fake()->numberBetween(1, 99),
            'code' => fake()->unique()->bothify('##??'),
            'polyline' => $polyline,
            'is_active' => true,
        ];
    }
}
