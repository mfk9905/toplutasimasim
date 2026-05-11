<?php

namespace Database\Seeders;

use App\Models\Stop;
use App\Models\StopDensity;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class StopDensitySeeder extends Seeder
{
    public function run(): void
    {
        $today = CarbonImmutable::today();
        $isWeekday = $today->isWeekday();

        Stop::query()->chunkById(200, function ($stops) use ($today, $isWeekday) {
            foreach ($stops as $stop) {
                $isCenterStop = $stop->sequence >= 4 && $stop->sequence <= 10;

                for ($hour = 0; $hour < 24; $hour++) {
                    $base = $this->hourlyBaseDensity($hour, $isCenterStop);
                    $weekdayBoost = $isWeekday ? 1.10 : 0.90;
                    $jitter = fake()->randomFloat(2, 0.85, 1.15);

                    $density = max(3, min(100, round($base * $weekdayBoost * $jitter, 2)));
                    $passengers = (int) round(($density / 100) * fake()->numberBetween(25, 120));

                    StopDensity::updateOrCreate(
                        [
                            'stop_id' => $stop->id,
                            'date' => $today->toDateString(),
                            'hour' => $hour,
                        ],
                        [
                            'density_score' => $density,
                            'passenger_estimate' => $passengers,
                        ]
                    );
                }
            }
        });
    }

    private function hourlyBaseDensity(int $hour, bool $isCenterStop): float
    {
        return match (true) {
            $hour >= 6 && $hour <= 9 => $isCenterStop ? fake()->randomFloat(2, 72, 95) : fake()->randomFloat(2, 55, 82),
            $hour >= 10 && $hour <= 16 => $isCenterStop ? fake()->randomFloat(2, 45, 68) : fake()->randomFloat(2, 30, 58),
            $hour >= 17 && $hour <= 20 => $isCenterStop ? fake()->randomFloat(2, 70, 92) : fake()->randomFloat(2, 52, 78),
            $hour >= 21 && $hour <= 23 => fake()->randomFloat(2, 15, 40),
            default => fake()->randomFloat(2, 5, 20),
        };
    }
}
