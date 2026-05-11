<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusPositionUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public int $busId,
        public int $routeId,
        public string $plateNumber,
        public string $direction,
        public float $lat,
        public float $lng,
        public float $speedKmh,
        public string $timestamp
    ) {
    }

    public function broadcastOn(): Channel
    {
        return new Channel("routes.{$this->routeId}");
    }

    public function broadcastAs(): string
    {
        return 'bus.position.updated';
    }
}
