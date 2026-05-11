<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'lat',
        'lng',
        'speed_kmh',
        'recorded_at',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'speed_kmh' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }
}
