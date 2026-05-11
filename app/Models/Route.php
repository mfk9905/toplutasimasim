<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'polyline',
        'is_active',
    ];

    protected $casts = [
        'polyline' => 'array',
        'is_active' => 'boolean',
    ];

    public function stops(): HasMany
    {
        return $this->hasMany(Stop::class);
    }

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }
}
