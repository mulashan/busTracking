<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'class_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_parent', 'student_id', 'parent_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function buses(): BelongsToMany
    {
        return $this->belongsToMany(Bus::class, 'student_bus')
            ->withPivot(['bus_stop_id', 'starts_on', 'ends_on'])
            ->withTimestamps();
    }
}
