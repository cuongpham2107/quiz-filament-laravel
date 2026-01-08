<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function completedQuizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class)->where('status', 'completed');
    }

    public function hasAttemptedQuiz(int $quizId): bool
    {
        return $this->quizAttempts()->where('quiz_id', $quizId)->exists();
    }

    public function canAttemptQuiz(Quiz $quiz): bool
    {
        if ($quiz->allow_multiple_attempts) {
            return true;
        }

        return ! $this->hasAttemptedQuiz($quiz->id);
    }

    public function examSessions(): BelongsToMany
    {
        return $this->belongsToMany(ExamSession::class)
            ->withPivot('attempts_used', 'assigned_quiz_id')
            ->withTimestamps();
    }
}
