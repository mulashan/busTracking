<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    protected $fillable = [
        'bus_id',
        'route_id',
        'status',
        'scheduled_start_at',
        'started_at',
        'completed_at',
        'predicted_eta_minutes',
        'actual_duration_minutes',
        'eta_variance_minutes',
        'students_picked',
        'students_remaining',
        'distance_km',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_start_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'predicted_eta_minutes' => 'integer',
            'actual_duration_minutes' => 'integer',
            'eta_variance_minutes' => 'integer',
            'students_picked' => 'integer',
            'students_remaining' => 'integer',
            'distance_km' => 'float',
        ];
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
}
