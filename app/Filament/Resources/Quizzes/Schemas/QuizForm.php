<?php

namespace App\Filament\Resources\Quizzes\Schemas;

use App\Models\Category;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Actions\Action;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Grid::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Thông tin bài kiểm tra')
                            ->description('Nhập thông tin cơ bản về bài kiểm tra')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Tiêu đề')
                                    ->required()
                                    ->columnSpanFull(),
                                Textarea::make('description')
                                    ->label('Mô tả')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Cấu hình bài thi')
                            ->description('Thiết lập các tham số cho bài thi')
                            ->schema([
                                TextInput::make('number_of_questions')
                                    ->label('Số câu hỏi')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('time_limit')
                                    ->label('Thời gian (Phút)')
                                    ->required()
                                    ->numeric()
                                    ->default(5)
                                    ->suffix('phút')
                                    ->helperText('Thời gian tối đa cho bài thi'),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Tùy chọn nâng cao')
                            ->description('Cài đặt các tùy chọn cho bài thi')
                            ->schema([
                                Toggle::make('shuffle_questions')
                                    ->label('Xáo trộn câu hỏi')
                                    ->helperText('Thứ tự câu hỏi sẽ khác nhau cho mỗi lần thi')
                                    ->default(false)
                                    ->columnSpan(2),
                                Toggle::make('shuffle_answers')
                                    ->label('Xáo trộn đáp án')
                                    ->helperText('Thứ tự đáp án sẽ khác nhau cho mỗi lần thi')
                                    ->default(false)
                                    ->columnSpan(2),
                                Toggle::make('allow_multiple_attempts')
                                    ->label('Cho phép làm lại')
                                    ->helperText('Nhân viên có thể làm bài nhiều lần')
                                    ->default(false)
                                    ->columnSpan(2),
                            ])
                            ->columns(6)
                            ->columnSpanFull()
                            ->collapsible(),
                    ]),

                Section::make('Chọn câu hỏi')
                    ->description('Chọn các câu hỏi cho bài kiểm tra này')
                    ->schema([
                        Select::make('selected_category')
                            ->label('Lọc theo danh mục')
                            ->live()
                            ->dehydrated(false)
                            ->options(Category::all()->pluck('name', 'id'))
                            ->placeholder('Tất cả danh mục')
                            ->searchable(),
                        CheckboxList::make('questions')
                            ->label('Chọn câu hỏi')
                            ->bulkToggleable()
                            ->selectAllAction(
                                fn (Action $action) => $action->label('Chọn tất cả câu hỏi'),
                            )
                            ->relationship(
                                name: 'questions',
                                titleAttribute: 'question',
                                modifyQueryUsing: fn ($query, Get $get) => $get('selected_category')
                                        ? $query->where('category_id', $get('selected_category'))
                                        : $query
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, ?array $state) => $set('number_of_questions', count($state ?? []))
                            )
                            ->columns(1)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(1)
                    ->collapsible(),
            ]);
    }
}
