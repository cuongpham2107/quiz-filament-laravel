<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'number_of_questions',
        'time_limit',
        'shuffle_questions',
        'shuffle_answers',
        'allow_multiple_attempts',
    ];

    protected function casts(): array
    {
        return [
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
        ];
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class)
            ->withPivot('order')
            ->orderBy('order')
            ->withTimestamps();
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function completedAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class)->where('status', 'completed');
    }

    public function getTotalAttemptsAttribute(): int
    {
        return $this->attempts()->count();
    }

    public function getUniqueParticipantsAttribute(): int
    {
        return $this->attempts()->distinct('user_id')->count('user_id');
    }
}
