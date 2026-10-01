<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGuardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'relationship',
        'phone',
        'email',
        'occupation',
        'is_primary_contact',
        'is_emergency_contact',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
        'is_emergency_contact' => 'boolean',
    ];

    public function getFirstNameAttribute(): string
    {
        $parts = explode(' ', $this->name ?? '', 2);
        return $parts[0] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        $parts = explode(' ', $this->name ?? '', 2);
        return $parts[1] ?? '';
    }

    public function getIsPrimaryAttribute(): bool
    {
        return (bool) $this->is_primary_contact;
    }

    public function setIsPrimaryAttribute($value): void
    {
        $this->attributes['is_primary_contact'] = $value;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

}
