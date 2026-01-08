<?php

namespace App\Filament\Resources\ExamSessions\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\GridDirection;

class ExamSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Grid::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Thông tin đợt thi')
                            ->description('Thông tin cơ bản về đợt thi')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên đợt thi')
                                    ->required()
                                    ->columnSpanFull(),
                                Textarea::make('description')
                                    ->label('Mô tả')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Thời gian & Trạng thái')
                            ->description('Cấu hình thời gian và trạng thái đợt thi')
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->label('Thời gian bắt đầu')
                                    ->required()
                                    ->seconds(false),
                                DateTimePicker::make('end_date')
                                    ->label('Thời gian kết thúc')
                                    ->required()
                                    ->seconds(false)
                                    ->after('start_date'),
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options([
                                        'draft' => 'Nháp',
                                        'active' => 'Đang diễn ra',
                                        'completed' => 'Đã hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                    ])
                                    ->required()
                                    ->default('draft'),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Cấu hình thi')
                            ->description('Thiết lập các quy tắc cho đợt thi')
                            ->schema([
                                Toggle::make('randomize_quiz')
                                    ->label('Random bài thi')
                                    ->helperText('Mỗi người sẽ được gán ngẫu nhiên 1 bài thi từ danh sách')
                                    ->default(true),
                                Toggle::make('allow_retake')
                                    ->label('Cho phép thi lại')
                                    ->helperText('Người dùng có thể thi lại')
                                    ->default(false)
                                    ->live(),
                                TextInput::make('max_attempts')
                                    ->label('Số lần thi tối đa')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->helperText('Số lần được phép thi'),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->collapsible(),
                    ]),

                Grid::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Bài kiểm tra')
                            ->description('Chọn các bài kiểm tra cho đợt thi')
                            ->schema([
                                CheckboxList::make('quizzes')
                                    ->label('Chọn bài kiểm tra')
                                    ->relationship('quizzes', 'title')
                                    ->bulkToggleable()
                                    ->searchable()
                                    ->required()
                                    ->columns(2)
                                    ->gridDirection(GridDirection::Row)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                           
                            ->collapsible(),

                        Section::make('Người tham gia')
                            ->description('Chọn người dùng được phép tham gia đợt thi')
                            ->schema([
                                CheckboxList::make('users')
                                    ->label('Chọn người dùng')
                                    ->relationship('users', 'name')
                                    ->bulkToggleable()
                                    ->searchable()
                                    ->required()
                                    ->columns(3)
                                    ->gridDirection(GridDirection::Row)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),
                    ]),
            ]);
    }
}
