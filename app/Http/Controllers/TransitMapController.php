<?php

namespace App\Http\Controllers;

use App\Domain\Transit\Services\Eta\StopEtaService;
use App\Models\Bus;
use App\Models\Route;
use App\Models\StopDensity;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TransitMapController extends Controller
{
    public function __construct(private readonly StopEtaService $stopEtaService)
    {
    }

    public function index(): View
    {
        return view('transit-map');
    }

    public function bootstrap(): JsonResponse
    {
        $buses = Bus::query()
            ->select(['id', 'route_id', 'plate_number', 'current_lat', 'current_lng', 'speed_kmh', 'status', 'simulation_state', 'last_position_at'])
            ->with('route:id,code,name')
            ->whereIn('status', ['idle', 'in_service'])
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->get()
            ->map(fn (Bus $bus): array => [
                'id' => $bus->id,
                'route_id' => $bus->route_id,
                'route_code' => $bus->route?->code,
                'route_name' => $bus->route?->name,
                'route_color' => $this->routeColor($bus->route?->code),
                'plate_number' => $bus->plate_number,
                'current_lat' => $bus->current_lat,
                'current_lng' => $bus->current_lng,
                'speed_kmh' => $bus->speed_kmh,
                'status' => $bus->status,
                'direction' => ((int) ($bus->simulation_state['direction'] ?? 1)) === 1 ? 'güzergâh ileri' : 'güzergâh geri',
                'last_position_at' => $bus->last_position_at?->toIso8601String(),
            ]);

        return response()->json([
            'buses' => $buses,
        ]);
    }

    public function layers(): JsonResponse
    {
        $now = CarbonImmutable::now();
        $today = $now->toDateString();
        $hour = (int) $now->format('G');

        $routes = Route::query()
            ->select(['id', 'name', 'code', 'polyline'])
            ->where('is_active', true)
            ->with(['stops' => function ($query): void {
                $query->select([
                    'id',
                    'route_id',
                    'name',
                    'sequence',
                    'lat',
                    'lng',
                    'avg_travel_seconds_from_prev',
                ])->orderBy('sequence');
            }])
            ->get();

        $busesByRoute = Bus::query()
            ->select(['id', 'route_id', 'current_lat', 'current_lng', 'speed_kmh', 'status', 'last_position_at'])
            ->whereIn('status', ['idle', 'in_service'])
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->get()
            ->groupBy('route_id');

        $densities = StopDensity::query()
            ->whereDate('date', $today)
            ->where('hour', $hour)
            ->get()
            ->keyBy('stop_id');

        $result = $routes->map(function ($route) use ($busesByRoute, $densities): array {
            $stops = $route->stops;
            $buses = $busesByRoute->get($route->id, collect());

            $densityByStopId = $stops->mapWithKeys(function ($stop) use ($densities): array {
                $density = $densities->get($stop->id);

                return [$stop->id => (float) ($density->density_score ?? 20)];
            });

            $etaByStopId = $this->stopEtaService->estimateForStops(
                buses: $buses,
                stops: $stops,
                densityByStopId: $densityByStopId
            );

            $stopPayload = $stops->map(function ($stop) use ($densityByStopId, $etaByStopId, $densities): array {
                $density = $densities->get($stop->id);
                $densityScore = (float) ($densityByStopId[$stop->id] ?? 20);

                return [
                    'id' => $stop->id,
                    'name' => $stop->name,
                    'sequence' => $stop->sequence,
                    'lat' => $stop->lat,
                    'lng' => $stop->lng,
                    'density_score' => $densityScore,
                    'density_level' => $this->densityLevel($densityScore),
                    'passenger_estimate' => (int) ($density->passenger_estimate ?? 0),
                    'eta_seconds' => $etaByStopId[$stop->id] ?? null,
                ];
            })->values();

            $validEtas = collect($etaByStopId)->filter(fn ($eta): bool => $eta !== null);
            $averageDensity = round((float) $densityByStopId->avg(), 1);
            $peakStop = $stopPayload->sortByDesc('density_score')->first();
            $averageEta = $validEtas->isNotEmpty() ? (int) round($validEtas->avg()) : null;
            $activeBusCount = $buses->count();

            return [
                'id' => $route->id,
                'name' => $route->name,
                'code' => $route->code,
                'color' => $this->routeColor($route->code),
                'polyline' => $route->polyline,
                'stops' => $stopPayload,
                'analytics' => [
                    'active_bus_count' => $activeBusCount,
                    'average_density' => $averageDensity,
                    'density_level' => $this->densityLevel($averageDensity),
                    'peak_stop' => $peakStop ? [
                        'id' => $peakStop['id'],
                        'name' => $peakStop['name'],
                        'density_score' => $peakStop['density_score'],
                        'passenger_estimate' => $peakStop['passenger_estimate'],
                    ] : null,
                    'average_eta_seconds' => $averageEta,
                    'operation_action' => $this->operationAction($averageDensity, $averageEta, $activeBusCount),
                    'feedback_summary' => $this->feedbackSummary($averageDensity),
                    'model_note' => 'Simüle edilmiş talep tahmini',
                ],
            ];
        })->values();

        return response()->json([
            'routes' => $result,
            'meta' => [
                'generated_at' => $now->toIso8601String(),
                'route_count' => $result->count(),
                'stop_count' => $result->sum(fn (array $route): int => count($route['stops'])),
                'active_bus_count' => $busesByRoute->flatten(1)->count(),
                'average_density' => round((float) $result->pluck('analytics.average_density')->avg(), 1),
            ],
        ]);
    }

    private function routeColor(?string $code): string
    {
        return match ($code) {
            '45M1' => '#f97316',
            '45M2' => '#0ea5e9',
            '45M3' => '#22c55e',
            '45M4' => '#e11d48',
            '45M5' => '#8b5cf6',
            default => '#64748b',
        };
    }

    private function densityLevel(float $score): string
    {
        return match (true) {
            $score >= 75 => 'Yüksek',
            $score >= 50 => 'Orta-yüksek',
            $score >= 30 => 'Orta',
            default => 'Düşük',
        };
    }

    private function operationAction(float $averageDensity, ?int $averageEta, int $activeBusCount): string
    {
        if ($averageDensity >= 75 || ($averageEta !== null && $averageEta >= 900)) {
            return 'Takviye araç önerilir';
        }

        if ($averageDensity >= 55 && $activeBusCount <= 2) {
            return 'Ek sefer planlanabilir';
        }

        if ($averageDensity <= 25) {
            return 'Normal sefer yeterli';
        }

        return 'Mevcut sefer dengesi korunabilir';
    }

    private function feedbackSummary(float $averageDensity): string
    {
        return match (true) {
            $averageDensity >= 75 => 'Yolcu geri bildirimi: yoğunluk algısı yüksek',
            $averageDensity >= 50 => 'Yolcu geri bildirimi: belirli duraklarda yoğunluk var',
            $averageDensity >= 30 => 'Yolcu geri bildirimi: kabul edilebilir yoğunluk',
            default => 'Yolcu geri bildirimi: düşük talep',
        };
    }
}
