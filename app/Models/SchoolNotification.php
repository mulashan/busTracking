<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'student_id',
        'bus_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }
}
