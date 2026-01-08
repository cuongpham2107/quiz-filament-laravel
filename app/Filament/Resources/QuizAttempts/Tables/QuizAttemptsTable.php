<?php

namespace App\Filament\Resources\QuizAttempts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Người dùng')
                    ->searchable(),
                TextColumn::make('quiz.title')
                    ->label('Bài kiểm tra')
                    ->searchable(),
                TextColumn::make('score')
                    ->label('Điểm số')
                    ->alignCenter()
                    ->numeric(),
                TextColumn::make('total_questions')
                    ->label('Số câu hỏi')
                    ->alignCenter()
                    ->numeric(),
                TextColumn::make('correct_answers')
                    ->label('Trả lời đúng')
                    ->numeric()
                    ->alignCenter(),
                TextColumn::make('started_at')
                    ->label('Bắt đầu')
                    ->dateTime('d/m/Y H:i:s')
                    ->alignCenter(),
                TextColumn::make('completed_at')
                    ->label('Hoàn thành')
                    ->dateTime('d/m/Y H:i:s')
                    ->alignCenter(),
                TextColumn::make('time_taken')
                    ->label('Thời gian làm bài')
                    ->alignCenter()
                    ->numeric()
                    ->formatStateUsing(fn (int $state) => gmdate('H:i:s', $state))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'in_progress' => 'Đang làm bài',
                        'completed' => 'Đã hoàn thành',
                        'abandoned' => 'Bỏ dở',
                        default => $state,
                    })
                    ->badge()
                    ->alignCenter()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
