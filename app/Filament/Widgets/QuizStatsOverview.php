<?php

namespace App\Filament\Widgets;

use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuizStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalQuizzes = Quiz::count();
        $totalQuestions = Question::count();
        $totalAttempts = QuizAttempt::where('status', 'completed')->count();
        $averageScore = QuizAttempt::where('status', 'completed')->avg('score') ?? 0;
        $activeExamSessions = ExamSession::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        // Lấy dữ liệu 7 ngày gần nhất cho chart
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            return QuizAttempt::where('status', 'completed')
                ->whereDate('completed_at', now()->subDays($daysAgo))
                ->count();
        })->toArray();

        return [
            Stat::make('Tổng người dùng', number_format($totalUsers))
                ->description('Người dùng trong hệ thống')
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->chart($last7Days),

            Stat::make('Tổng bài kiểm tra', number_format($totalQuizzes))
                ->description('Bài kiểm tra có sẵn')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Tổng câu hỏi', number_format($totalQuestions))
                ->description('Câu hỏi trong ngân hàng')
                ->descriptionIcon('heroicon-o-question-mark-circle')
                ->color('warning'),

            Stat::make('Lượt thi hoàn thành', number_format($totalAttempts))
                ->description('Tổng lượt thi đã hoàn thành')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart($last7Days),

            Stat::make('Điểm trung bình', number_format($averageScore, 1) . '/100')
                ->description('Điểm trung bình các bài thi')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color($averageScore >= 70 ? 'success' : ($averageScore >= 50 ? 'warning' : 'danger')),

            Stat::make('Đợt thi đang diễn ra', number_format($activeExamSessions))
                ->description('Các đợt thi hiện tại')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),
        ];
    }
}
