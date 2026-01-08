<?php

namespace App\Filament\Resources\ExamSessions;

use App\Filament\Resources\ExamSessions\Pages\CreateExamSession;
use App\Filament\Resources\ExamSessions\Pages\EditExamSession;
use App\Filament\Resources\ExamSessions\Pages\ListExamSessions;
use App\Filament\Resources\ExamSessions\Schemas\ExamSessionForm;
use App\Filament\Resources\ExamSessions\Tables\ExamSessionsTable;
use App\Filament\Resources\ExamSessions\RelationManagers\QuizAttemptsRelationManager;
use App\Models\ExamSession;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;


class ExamSessionResource extends Resource
{
    protected static ?string $model = ExamSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Đợt thi';

    protected static ?string $modelLabel = 'Đợt thi';

    protected static ?string $pluralModelLabel = 'Đợt thi';

    protected static  UnitEnum|string|null $navigationGroup = 'Quản lý nội dung';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExamSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            QuizAttemptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamSessions::route('/'),
            'create' => CreateExamSession::route('/create'),
            'edit' => EditExamSession::route('/{record}/edit'),
        ];
    }
}
