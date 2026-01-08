<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'allow_retake',
        'max_attempts',
        'randomize_quiz',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'allow_retake' => 'boolean',
        'randomize_quiz' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('attempts_used', 'assigned_quiz_id')
            ->withTimestamps();
    }

    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class)
            ->withTimestamps();
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date);
    }

    public function canUserParticipate(User $user): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;

        if (! $pivot) {
            return false;
        }

        if (! $this->allow_retake && $pivot->attempts_used > 0) {
            return false;
        }

        return $pivot->attempts_used < $this->max_attempts;
    }

    public function assignRandomQuizToUser(User $user): ?Quiz
    {
        if (! $this->randomize_quiz) {
            return $this->quizzes->first();
        }

        $quiz = $this->quizzes->random();

        $this->users()->updateExistingPivot($user->id, [
            'assigned_quiz_id' => $quiz->id,
        ]);

        return $quiz;
    }

    public function getAssignedQuiz(User $user): ?Quiz
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;

        if ($pivot && $pivot->assigned_quiz_id) {
            return Quiz::find($pivot->assigned_quiz_id);
        }

        return null;
    }

    public function getCompletedAttemptsCount(): int
    {
        return $this->quizAttempts()->where('status', 'completed')->count();
    }

    public function getAverageScore(): float
    {
        return $this->quizAttempts()
            ->where('status', 'completed')
            ->avg('score') ?? 0;
    }

    public function getPassRate(int $passingScore = 70): float
    {
        $total = $this->getCompletedAttemptsCount();

        if ($total === 0) {
            return 0;
        }

        $passed = $this->quizAttempts()
            ->where('status', 'completed')
            ->whereRaw('(correct_answers / total_questions * 100) >= ?', [$passingScore])
            ->count();

        return round(($passed / $total) * 100, 2);
    }
}
