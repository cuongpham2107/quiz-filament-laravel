<?php

namespace App\Filament\Widgets;

use App\Models\QuizAttempt;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class QuizAttemptsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public ?string $filter = 'year';
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '500px';

    public function getHeading(): ?string
    {
        return 'Biểu đồ lượt thi theo tháng';
    }

    protected function getData(): array
    {
        $data = $this->getAttemptsPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Lượt thi hoàn thành',
                    'data' => $data['counts'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Lượt thi đạt (≥70%)',
                    'data' => $data['passed'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => '7 ngày qua',
            'month' => '30 ngày qua',
            'year' => 'Năm nay',
        ];
    }

    private function getAttemptsPerMonth(): array
    {
        $filter = $this->filter;

        $query = QuizAttempt::where('status', 'completed');

        $dates = match ($filter) {
            'week' => collect(range(6, 0))->map(fn ($day) => now()->subDays($day)),
            'month' => collect(range(29, 0))->map(fn ($day) => now()->subDays($day)),
            default => collect(range(11, 0))->map(fn ($month) => now()->subMonths($month)),
        };

        $format = match ($filter) {
            'week', 'month' => 'd/m',
            default => 'M Y',
        };

        $labels = $dates->map(fn (Carbon $date) => $date->format($format))->toArray();

        $counts = $dates->map(function (Carbon $date) use ($filter) {
            $query = QuizAttempt::where('status', 'completed');

            if ($filter === 'year') {
                return $query->whereYear('completed_at', $date->year)
                    ->whereMonth('completed_at', $date->month)
                    ->count();
            }

            return $query->whereDate('completed_at', $date->toDateString())->count();
        })->toArray();

        $passed = $dates->map(function (Carbon $date) use ($filter) {
            $query = QuizAttempt::where('status', 'completed')
                ->whereRaw('(correct_answers / total_questions * 100) >= 70');

            if ($filter === 'year') {
                return $query->whereYear('completed_at', $date->year)
                    ->whereMonth('completed_at', $date->month)
                    ->count();
            }

            return $query->whereDate('completed_at', $date->toDateString())->count();
        })->toArray();

        return [
            'labels' => $labels,
            'counts' => $counts,
            'passed' => $passed,
        ];
    }
}
