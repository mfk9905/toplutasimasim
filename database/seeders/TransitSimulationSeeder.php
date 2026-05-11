<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusLog;
use App\Models\Route;
use App\Models\Stop;
use App\Models\StopDensity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TransitSimulationSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        BusLog::query()->delete();
        StopDensity::query()->delete();
        Bus::query()->delete();
        Stop::query()->delete();
        Route::query()->delete();
        Schema::enableForeignKeyConstraints();

        $routeDefinitions = [
            [
                'name' => 'Muradiye Kampüs - Otogar',
                'code' => '45M1',
                'stops' => [
                    ['name' => 'Muradiye Son Durak', 'lat' => 38.658900, 'lng' => 27.341800],
                    ['name' => 'Muradiye İstasyon', 'lat' => 38.653600, 'lng' => 27.354600],
                    ['name' => 'CBÜ Muradiye Kampüsü', 'lat' => 38.648400, 'lng' => 27.368000],
                    ['name' => 'OSB Kavşağı', 'lat' => 38.641300, 'lng' => 27.384800],
                    ['name' => 'Uncubozköy Meydan', 'lat' => 38.633700, 'lng' => 27.400300],
                    ['name' => 'Magnesia AVM', 'lat' => 38.626600, 'lng' => 27.414300],
                    ['name' => 'Manisa Gar', 'lat' => 38.620600, 'lng' => 27.421800],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Doğum ve Çocuk Hastanesi', 'lat' => 38.611800, 'lng' => 27.452000],
                    ['name' => 'Şehirlerarası Otogar', 'lat' => 38.605400, 'lng' => 27.468200],
                ],
            ],
            [
                'name' => 'Horozköy - Laleli',
                'code' => '45M2',
                'stops' => [
                    ['name' => 'Horozköy Son Durak', 'lat' => 38.642800, 'lng' => 27.482000],
                    ['name' => 'Horozköy Meydan', 'lat' => 38.638900, 'lng' => 27.473000],
                    ['name' => 'Hafsa Sultan Mahallesi', 'lat' => 38.632200, 'lng' => 27.458600],
                    ['name' => 'Devlet Hastanesi', 'lat' => 38.625100, 'lng' => 27.446700],
                    ['name' => 'Mimar Sinan Bulvarı', 'lat' => 38.620900, 'lng' => 27.438200],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Kültür Sitesi', 'lat' => 38.614000, 'lng' => 27.419200],
                    ['name' => 'Laleli Kavşağı', 'lat' => 38.608300, 'lng' => 27.407400],
                    ['name' => 'Laleli Merkez', 'lat' => 38.602800, 'lng' => 27.397800],
                    ['name' => 'Mesir Mahallesi', 'lat' => 38.596200, 'lng' => 27.388900],
                ],
            ],
            [
                'name' => 'Keçiliköy - Karaköy - Alaybey',
                'code' => '45M3',
                'stops' => [
                    ['name' => 'Keçiliköy Son Durak', 'lat' => 38.584700, 'lng' => 27.450000],
                    ['name' => 'Keçiliköy Merkez', 'lat' => 38.590400, 'lng' => 27.446700],
                    ['name' => 'Spil Kavşağı', 'lat' => 38.599200, 'lng' => 27.440000],
                    ['name' => 'Ulupark', 'lat' => 38.610000, 'lng' => 27.433500],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Sultan Camii', 'lat' => 38.623300, 'lng' => 27.423900],
                    ['name' => 'Karaköy Pazaryeri', 'lat' => 38.628700, 'lng' => 27.417400],
                    ['name' => 'Karaköy Merkez', 'lat' => 38.634800, 'lng' => 27.411000],
                    ['name' => 'Alaybey Camii', 'lat' => 38.641200, 'lng' => 27.405500],
                    ['name' => 'Alaybey Son Durak', 'lat' => 38.647000, 'lng' => 27.399800],
                ],
            ],
            [
                'name' => 'Uncubozköy - Merkez - Hafsa Sultan',
                'code' => '45M4',
                'stops' => [
                    ['name' => 'Uncubozköy Son Durak', 'lat' => 38.636800, 'lng' => 27.389000],
                    ['name' => 'Merkez Efendi', 'lat' => 38.631800, 'lng' => 27.398600],
                    ['name' => 'Manisa Park', 'lat' => 38.626100, 'lng' => 27.411200],
                    ['name' => 'Gar Meydanı', 'lat' => 38.620400, 'lng' => 27.421800],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Valilik', 'lat' => 38.616000, 'lng' => 27.437800],
                    ['name' => 'Mimar Sinan', 'lat' => 38.620700, 'lng' => 27.443900],
                    ['name' => 'Devlet Hastanesi', 'lat' => 38.624800, 'lng' => 27.449900],
                    ['name' => 'Hafsa Sultan Bulvarı', 'lat' => 38.629800, 'lng' => 27.459500],
                    ['name' => 'Horozköy Aktarma', 'lat' => 38.638700, 'lng' => 27.477000],
                ],
            ],
            [
                'name' => 'OSB - Merkez - Otogar',
                'code' => '45M5',
                'stops' => [
                    ['name' => 'OSB Ana Kapı', 'lat' => 38.675500, 'lng' => 27.390800],
                    ['name' => 'OSB 2. Kısım', 'lat' => 38.666800, 'lng' => 27.397600],
                    ['name' => 'Kenan Evren Sanayi', 'lat' => 38.654000, 'lng' => 27.405000],
                    ['name' => 'Küçük Sanayi', 'lat' => 38.643000, 'lng' => 27.413000],
                    ['name' => 'Uncubozköy Kavşağı', 'lat' => 38.632000, 'lng' => 27.420000],
                    ['name' => 'Manisa Gar', 'lat' => 38.620600, 'lng' => 27.421900],
                    ['name' => 'Cumhuriyet Meydanı', 'lat' => 38.619099, 'lng' => 27.428921],
                    ['name' => 'Turgut Özal Mahallesi', 'lat' => 38.613800, 'lng' => 27.444300],
                    ['name' => 'Doğum ve Çocuk Hastanesi', 'lat' => 38.608000, 'lng' => 27.456200],
                    ['name' => 'Şehirlerarası Otogar', 'lat' => 38.603300, 'lng' => 27.469200],
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

        $lastStop = $stops[$stopCount - 1];
        $polyline[] = [
            'lat' => $lastStop['lat'],
            'lng' => $lastStop['lng'],
        ];

        return $polyline;
    }
}
