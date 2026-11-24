<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bus extends Model
{
    protected $fillable = [
        'registration_number',
        'device_id',
        'device_token_hash',
        'capacity',
        'driver_id',
        'is_active',
    ];

    protected $hidden = ['device_token_hash'];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function gpsLocations(): HasMany
    {
        return $this->hasMany(GpsLocation::class);
    }

    public function latestLocation(): HasOne
    {
        return $this->hasOne(GpsLocation::class)->latestOfMany('recorded_at');
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'bus_route')
            ->withPivot('service_date')
            ->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_bus')
            ->withPivot(['bus_stop_id', 'starts_on', 'ends_on'])
            ->withTimestamps();
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
