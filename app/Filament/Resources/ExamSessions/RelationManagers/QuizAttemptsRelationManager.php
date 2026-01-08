<?php

namespace App\Filament\Resources\ExamSessions\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizAttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'quizAttempts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('quiz_id')
                    ->relationship('quiz', 'title')
                    ->required(),
                TextInput::make('score')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_questions')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('correct_answers')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('answers')
                    ->columnSpanFull(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                TextInput::make('time_taken')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('in_progress'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('score')
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
                TextColumn::make('score')
                    ->label('Điểm')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->alignCenter()
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
                    ->alignCenter()
                    ->color(fn ($record): string => match (true) {
                        $record->percentage >= 70 => 'success',
                        $record->percentage >= 50 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('correct_answers')
                    ->label('Số câu đúng')
                    ->formatStateUsing(fn ($record) => "{$record->correct_answers}/{$record->total_questions}")
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('time_taken')
                    ->label('Thời gian')
                    ->formatStateUsing(fn ($state) => $state ? gmdate('H:i:s', $state) : '-')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'abandoned' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'Hoàn thành',
                        'in_progress' => 'Đang làm',
                        'abandoned' => 'Bỏ cuộc',
                        default => $state,
                    }),
                TextColumn::make('completed_at')
                    ->label('Hoàn thành lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                // EditAction::make(),
                // DissociateAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
