<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Quizzes\QuizResource;
use App\Models\ExamSession;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ExamPage extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Đợt thi của tôi';

    protected static ?string $title = 'Đợt thi của tôi';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.exam-page';

    public function getExamSessions()
    {
        $user = Auth::user();

        return ExamSession::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['quizzes', 'users'])
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function startExam($examSessionId)
    {
        $user = Auth::user();
        $examSession = ExamSession::find($examSessionId);

        if (! $examSession) {
            Notification::make()
                ->title('Lỗi')
                ->body('Không tìm thấy đợt thi.')
                ->danger()
                ->send();

            return;
        }

        if (! $examSession->canUserParticipate($user)) {
            Notification::make()
                ->title('Không thể tham gia')
                ->body('Bạn đã hết lượt thi hoặc đợt thi đã kết thúc.')
                ->warning()
                ->send();

            return;
        }

        // Lấy hoặc gán quiz
        $assignedQuiz = $examSession->getAssignedQuiz($user);

        if (! $assignedQuiz) {
            $assignedQuiz = $examSession->assignRandomQuizToUser($user);
        }

        if (! $assignedQuiz) {
            Notification::make()
                ->title('Lỗi')
                ->body('Không thể gán bài thi. Vui lòng liên hệ quản trị viên.')
                ->danger()
                ->send();

            return;
        }

        // Tăng số lần thi
        $examSession->users()->updateExistingPivot($user->id, [
            'attempts_used' => $examSession->users()->where('user_id', $user->id)->first()->pivot->attempts_used + 1,
        ]);

        // Chuyển đến trang làm bài với exam_session_id
        $this->redirect(QuizResource::getUrl('take', [
            'record' => $assignedQuiz->id,
            'exam_session_id' => $examSession->id,
        ]));
    }
}
