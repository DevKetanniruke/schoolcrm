<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'author_id',
        'note',
        'is_confidential',
        'category',
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
    ];

    public function getContentAttribute(): string
    {
        return $this->note ?? '';
    }

    public function setContentAttribute($value): void
    {
        $this->attributes['note'] = $value;
    }

    public function getTypeAttribute(): string
    {
        return $this->category ?? 'general';
    }

    public function setTypeAttribute($value): void
    {
        $this->attributes['category'] = $value;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

}
