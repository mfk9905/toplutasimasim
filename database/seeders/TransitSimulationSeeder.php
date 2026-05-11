<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Stop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TransitSimulationSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        \App\Models\BusLog::query()->delete();
        \App\Models\StopDensity::query()->delete();
        Bus::query()->delete();
        Stop::query()->delete();
        Route::query()->delete();
        Schema::enableForeignKeyConstraints();

        $routeDefinitions = [
            [
                'name' => 'Muradiye - Merkez',
                'code' => '45M1',
                'stops' => [
                    ['name' => 'Muradiye Meydan', 'lat' => 38.659740, 'lng' => 27.340940],
                    ['name' => 'Muradiye İstasyon', 'lat' => 38.652260, 'lng' => 27.355420],
                    ['name' => 'Celal Bayar Kampüs', 'lat' => 38.646120, 'lng' => 27.369680],
                    ['name' => 'Organize Giriş', 'lat' => 38.638840, 'lng' => 27.383660],
                    ['name' => 'Uncubozköy Kavşak', 'lat' => 38.630780, 'lng' => 27.399940],
                    ['name' => 'Manisa Park', 'lat' => 38.624900, 'lng' => 27.414180],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Valilik', 'lat' => 38.615400, 'lng' => 27.440300],
                    ['name' => 'Dogum Evi', 'lat' => 38.610950, 'lng' => 27.452500],
                    ['name' => 'Otogar', 'lat' => 38.606280, 'lng' => 27.466080],
                ],
            ],
            [
                'name' => 'Horozköy - Laleli',
                'code' => '45M2',
                'stops' => [
                    ['name' => 'Horozköy Meydan', 'lat' => 38.640120, 'lng' => 27.476640],
                    ['name' => 'Horozköy Sağlık', 'lat' => 38.634260, 'lng' => 27.464820],
                    ['name' => 'Hafsa Sultan', 'lat' => 38.628200, 'lng' => 27.452780],
                    ['name' => 'Mimar Sinan Bulvari', 'lat' => 38.623560, 'lng' => 27.442880],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Kültür Sitesi', 'lat' => 38.613920, 'lng' => 27.418540],
                    ['name' => 'Laleli Kavşak', 'lat' => 38.608260, 'lng' => 27.407680],
                    ['name' => 'Laleli Merkez', 'lat' => 38.602780, 'lng' => 27.398240],
                    ['name' => 'Mesir Mahallesi', 'lat' => 38.596880, 'lng' => 27.388360],
                    ['name' => 'Yeni Mahalle', 'lat' => 38.591760, 'lng' => 27.378880],
                ],
            ],
            [
                'name' => 'Keçiliköy - Karaköy',
                'code' => '45M3',
                'stops' => [
                    ['name' => 'Keçiliköy', 'lat' => 38.584200, 'lng' => 27.449500],
                    ['name' => 'Keçiliköy Yol Ayrımı', 'lat' => 38.592320, 'lng' => 27.444120],
                    ['name' => 'Spil Kavşak', 'lat' => 38.601180, 'lng' => 27.438460],
                    ['name' => 'Ulupark', 'lat' => 38.610060, 'lng' => 27.433520],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Sultan Cami', 'lat' => 38.623880, 'lng' => 27.423220],
                    ['name' => 'Karaköy Pazaryeri', 'lat' => 38.629940, 'lng' => 27.417260],
                    ['name' => 'Karaköy Merkez', 'lat' => 38.636120, 'lng' => 27.411240],
                    ['name' => 'Karaköy Lise', 'lat' => 38.642380, 'lng' => 27.405300],
                    ['name' => 'Karaköy Son Durak', 'lat' => 38.648540, 'lng' => 27.399120],
                ],
            ],
            [
                'name' => 'Uncubozköy - Hafsa Sultan',
                'code' => '45M4',
                'stops' => [
                    ['name' => 'Uncubozköy Meydan', 'lat' => 38.633500, 'lng' => 27.391100],
                    ['name' => 'Merkez Efendi', 'lat' => 38.629220, 'lng' => 27.400860],
                    ['name' => 'Manisa Park', 'lat' => 38.625080, 'lng' => 27.411360],
                    ['name' => 'Gar', 'lat' => 38.621260, 'lng' => 27.420620],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Devlet Hastanesi', 'lat' => 38.621820, 'lng' => 27.439260],
                    ['name' => 'Hafsa Sultan Bulvari', 'lat' => 38.625600, 'lng' => 27.449860],
                    ['name' => 'Hafsa Sultan Mahallesi', 'lat' => 38.630240, 'lng' => 27.460360],
                    ['name' => 'Horozköy Giriş', 'lat' => 38.635480, 'lng' => 27.470700],
                    ['name' => 'Horozköy Aktarma', 'lat' => 38.641100, 'lng' => 27.481200],
                ],
            ],
            [
                'name' => 'OSB - Otogar',
                'code' => '45M5',
                'stops' => [
                    ['name' => 'OSB Ana Giriş', 'lat' => 38.676200, 'lng' => 27.389900],
                    ['name' => 'OSB 2 Cadde', 'lat' => 38.664880, 'lng' => 27.397320],
                    ['name' => 'Kenan Evren Sanayi', 'lat' => 38.654200, 'lng' => 27.405100],
                    ['name' => 'Küçük Sanayi', 'lat' => 38.643360, 'lng' => 27.412860],
                    ['name' => 'Uncubozköy Kavşak', 'lat' => 38.632340, 'lng' => 27.420220],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Mimar Sinan', 'lat' => 38.615120, 'lng' => 27.438980],
                    ['name' => 'Turgut Özal', 'lat' => 38.611060, 'lng' => 27.449620],
                    ['name' => 'Dogum Evi', 'lat' => 38.607200, 'lng' => 27.459340],
                    ['name' => 'Otogar', 'lat' => 38.603300, 'lng' => 27.469200],
                ],
            ],
        ];

        foreach ($routeDefinitions as $routeDef) {
            $stops = $routeDef['stops'];

            $polyline = $this->buildPolylineFromStops($stops, 10);

            $route = Route::query()->create([
                'name' => $routeDef['name'],
                'code' => $routeDef['code'],
                'polyline' => $polyline,
                'is_active' => true,
            ]);

            foreach ($stops as $index => $stop) {
                $isCenter = $index >= 3 && $index <= 6;

                Stop::query()->create([
                    'route_id' => $route->id,
                    'name' => $stop['name'],
                    'sequence' => $index + 1,
                    'lat' => $stop['lat'],
                    'lng' => $stop['lng'],
                    'avg_travel_seconds_from_prev' => $isCenter
                        ? fake()->numberBetween(65, 115)
                        : fake()->numberBetween(105, 190),
                ]);
            }

            for ($busNo = 0; $busNo < 2; $busNo++) {
                $segmentStart = min(
                    count($polyline) - 2,
                    ($busNo + 1) * intdiv(count($polyline), 3)
                );

                Bus::query()->create([
                    'route_id' => $route->id,
                    'plate_number' => sprintf('45 %s %02d', substr($routeDef['code'], -2), $busNo + 1),
                    'driver_name' => fake()->name(),
                    'current_lat' => $polyline[$segmentStart]['lat'],
                    'current_lng' => $polyline[$segmentStart]['lng'],
                    'speed_kmh' => fake()->randomFloat(2, 24, 38),
                    'status' => 'idle',
                    'simulation_state' => [
                        'segment_index' => $segmentStart,
                        'segment_progress' => 0,
                        'direction' => 1,
                    ],
                ]);
            }
        }

        $this->call(StopDensitySeeder::class);
    }

    private function buildPolylineFromStops(array $stops, int $samplesPerSegment): array
    {
        $polyline = [];
        $stopCount = count($stops);

        for ($i = 0; $i < $stopCount - 1; $i++) {
            $from = $stops[$i];
            $to = $stops[$i + 1];

            for ($step = 0; $step < $samplesPerSegment; $step++) {
                $t = $step / $samplesPerSegment;
                $polyline[] = [
                    'lat' => round($from['lat'] + (($to['lat'] - $from['lat']) * $t), 7),
                    'lng' => round($from['lng'] + (($to['lng'] - $from['lng']) * $t), 7),
                ];
            }
        }

        $polyline[] = $stops[$stopCount - 1];

        return $polyline;
    }
}
