<?php

namespace App\Filament\Resources\ExamSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExamSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên đợt thi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Nháp',
                        'active' => 'Đang diễn ra',
                        'completed' => 'Đã hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Bắt đầu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Kết thúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('quizzes_count')
                    ->label('Số bài thi')
                    ->counts('quizzes')
                    ->badge()
                    ->color('primary')
                    ->suffix(' bài'),
                TextColumn::make('users_count')
                    ->label('Số người tham gia')
                    ->counts('users')
                    ->badge()
                    ->color('warning')
                    ->suffix(' người'),
                IconColumn::make('randomize_quiz')
                    ->label('Random Quiz')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-arrow-path')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('max_attempts')
                    ->label('Số lần thi')
                    ->numeric()
                    ->suffix(' lần')
                    ->sortable(),
                IconColumn::make('allow_retake')
                    ->label('Cho phép thi lại')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Nháp',
                        'active' => 'Đang diễn ra',
                        'completed' => 'Đã hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

