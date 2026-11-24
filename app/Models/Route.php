<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $fillable = [
        'name',
        'start_location',
        'end_location',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function buses(): BelongsToMany
    {
        return $this->belongsToMany(Bus::class, 'bus_route')
            ->withPivot('service_date')
            ->withTimestamps();
    }

    public function stops(): HasMany
    {
        return $this->hasMany(BusStop::class)->orderBy('stop_order');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
