<?php

namespace App\Filament\Resources\Quizzes\Tables;

use App\Filament\Resources\Quizzes\QuizResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizzesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable(),
                TextColumn::make('number_of_questions')
                    ->label('Số câu hỏi')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('time_limit')
                    ->label('Thời gian (phút)')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                IconColumn::make('shuffle_questions')
                    ->label('Xáo trộn câu hỏi')
                    ->alignCenter()
                    ->boolean(),
                IconColumn::make('shuffle_answers')
                    ->label('Xáo trộn đáp án')
                    ->alignCenter()
                    ->boolean(),
                IconColumn::make('allow_multiple_attempts')
                    ->label('Cho phép làm lại')
                    ->alignCenter()
                    ->boolean(),
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
                Action::make('take')
                    ->label('Làm bài')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->url(fn ($record) => QuizResource::getUrl('take', ['record' => $record])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
