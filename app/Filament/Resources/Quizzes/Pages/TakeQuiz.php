<?php

namespace App\Filament\Resources\Quizzes\Pages;

use App\Filament\Resources\Quizzes\QuizResource;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class TakeQuiz extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = QuizResource::class;

    protected string $view = 'filament.resources.quizzes.pages.take-quiz';

    public ?array $data = [];

    public ?QuizAttempt $attempt = null;

    public array $questions = [];

    public int $currentQuestionIndex = 0;

    public int $timeRemaining = 0;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load('questions');

        // Kiểm tra quyền làm bài
        $user = Auth::user();
        if (! $user->canAttemptQuiz($this->record)) {
            Notification::make()
                ->title('Không thể làm bài')
                ->body('Bạn đã hoàn thành bài kiểm tra này và không được phép làm lại.')
                ->danger()
                ->send();

            $this->redirect(QuizResource::getUrl('index'));

            return;
        }

        // Lấy câu hỏi
        $this->questions = $this->record->questions->toArray();

        // Xáo trộn câu hỏi nếu cần
        if ($this->record->shuffle_questions) {
            shuffle($this->questions);
        }

        // Xáo trộn đáp án nếu cần
        if ($this->record->shuffle_answers) {
            foreach ($this->questions as &$question) {
                if (isset($question['options']) && is_array($question['options'])) {
                    shuffle($question['options']);
                }
            }
        }

        // Lấy exam_session_id từ query string
        $examSessionId = request()->query('exam_session_id');

        // Tạo attempt
        $this->attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $this->record->id,
            'exam_session_id' => $examSessionId,
            'started_at' => now(),
            'status' => 'in_progress',
            'total_questions' => count($this->questions),
        ]);

        // Chuyển time_limit từ phút sang giây
        $this->timeRemaining = $this->record->time_limit * 60;

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        if (empty($this->questions)) {
            return $schema->components([]);
        }

        $currentQuestion = $this->questions[$this->currentQuestionIndex] ?? null;

        if (! $currentQuestion) {
            return $schema->components([]);
        }

        $options = [];
        if (isset($currentQuestion['options']) && is_array($currentQuestion['options'])) {
            foreach ($currentQuestion['options'] as $index => $option) {
                $text = is_array($option) && isset($option['data']['text'])
                    ? $option['data']['text']
                    : (is_array($option) && isset($option['text']) ? $option['text'] : "Đáp án {$index}");

                $options[$index] = $text;
            }
        }

        // Lấy câu trả lời đã lưu (nếu có)
        $savedAnswer = null;
        if (isset($this->attempt->answers[$currentQuestion['id']])) {
            $savedAnswer = $this->attempt->answers[$currentQuestion['id']];
        }

        return $schema
            ->components([
                Section::make('Câu hỏi '.($this->currentQuestionIndex + 1).' / '.count($this->questions))
                    ->description($currentQuestion['question'] ?? '')
                    ->schema([
                        Radio::make('answer_' . $currentQuestion['id'])
                            ->label('Chọn đáp án')
                            ->options($options)
                            ->default($savedAnswer)
                            ->live()
                            ->afterStateUpdated(function ($state) use ($currentQuestion) {
                                // Tự động lưu khi chọn đáp án
                                if ($state !== null) {
                                    $answers = $this->attempt->answers ?? [];
                                    $answers[$currentQuestion['id']] = $state;
                                    $this->attempt->update(['answers' => $answers]);
                                    
                                    // Reload attempt để cập nhật state
                                    $this->attempt->refresh();
                                }
                            }),
                    ]),
            ])
            ->statePath('data');
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        } else {
            $this->submitQuiz();
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    protected function saveAnswer(): void
    {
        // Không cần nữa vì đã tự động lưu trong afterStateUpdated
    }

    public function submitQuiz(): void
    {
        // Tính điểm
        $correctAnswers = 0;
        $answers = $this->attempt->answers ?? [];

        foreach ($this->questions as $question) {
            $userAnswerIndex = $answers[$question['id']] ?? null;

            if ($userAnswerIndex !== null && isset($question['options'][$userAnswerIndex])) {
                $userAnswer = $question['options'][$userAnswerIndex];
                $isCorrect = is_array($userAnswer) && isset($userAnswer['data']['is_correct'])
                    ? $userAnswer['data']['is_correct']
                    : false;

                if ($isCorrect) {
                    $correctAnswers++;
                }
            }
        }

        $score = round(($correctAnswers / count($this->questions)) * 100, 2);

        // Tính thời gian làm bài (completed_at - started_at)
        $timeTaken = now()->timestamp - $this->attempt->started_at->timestamp;

        $this->attempt->update([
            'completed_at' => now(),
            'status' => 'completed',
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'time_taken' => $timeTaken,
        ]);

        Notification::make()
            ->title('Hoàn thành bài thi!')
            ->body("Bạn đã đạt {$score}% ({$correctAnswers}/{$this->attempt->total_questions} câu đúng)")
            ->success()
            ->send();

        $this->redirect(QuizResource::getUrl('index'));
    }

    
}
