<?php

namespace App\Console\Commands;

use App\Domain\Transit\Services\Simulation\BusSimulationEngine;
use App\Models\Bus;
use Illuminate\Console\Command;

class SimulateBusesCommand extends Command
{
    protected $signature = 'simulate:buses {--interval=1 : Tick interval in seconds}';

    protected $description = 'Runs infinite loop bus simulation and broadcasts positions';

    public function handle(BusSimulationEngine $engine): int
    {
        $interval = max(1, (int) $this->option('interval'));
        $this->info("Simulation started with {$interval}s interval.");

        while (true) {
            Bus::query()
                ->with('route:id,polyline')
                ->whereIn('status', ['idle', 'in_service'])
                ->chunkById(200, function ($buses) use ($engine): void {
                    foreach ($buses as $bus) {
                        $engine->tick($bus);
                    }
                });

            sleep($interval);
        }
    }
}
