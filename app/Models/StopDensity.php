<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StopDensity extends Model
{
    use HasFactory;

    protected $fillable = [
        'stop_id',
        'date',
        'hour',
        'density_score',
        'passenger_estimate',
    ];

    protected $casts = [
        'date' => 'date',
        'density_score' => 'float',
    ];

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }
}
