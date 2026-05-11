<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stop extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'name',
        'sequence',
        'lat',
        'lng',
        'avg_travel_seconds_from_prev',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function densities(): HasMany
    {
        return $this->hasMany(StopDensity::class);
    }
}
