<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\QuizAttempts\QuizAttemptResource;
use App\Models\QuizAttempt;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestQuizAttempts extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                QuizAttempt::query()
                    ->where('status', 'completed')
                    ->latest('completed_at')
                    ->limit(10)
            )
            ->heading('Lượt thi gần nhất')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Người thi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quiz.title')
                    ->label('Bài kiểm tra')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('examSession.name')
                    ->label('Đợt thi')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->limit(20),

                TextColumn::make('score')
                    ->label('Điểm')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('percentage')
                    ->label('Tỷ lệ đúng')
                    ->suffix('%')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        $record->percentage >= 70 => 'success',
                        $record->percentage >= 50 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('time_taken')
                    ->label('Thời gian')
                    ->formatStateUsing(fn ($state) => $state ? gmdate('H:i:s', $state) : '-')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Hoàn thành lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordUrl(
                fn (QuizAttempt $record): string => QuizAttemptResource::getUrl('edit', ['record' => $record])
            );
    }
}
