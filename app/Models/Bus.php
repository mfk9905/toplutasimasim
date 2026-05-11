<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'plate_number',
        'driver_name',
        'current_lat',
        'current_lng',
        'speed_kmh',
        'status',
        'simulation_state',
        'last_position_at',
    ];

    protected $casts = [
        'current_lat' => 'float',
        'current_lng' => 'float',
        'speed_kmh' => 'float',
        'simulation_state' => 'array',
        'last_position_at' => 'datetime',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BusLog::class);
    }
}
