<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('routes.{routeId}', function ($user = null, int $routeId = 0) {
    return true;
});
