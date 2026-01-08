<?php

namespace App\Filament\Resources\QuizAttempts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuizAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Grid::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Thông tin người thi')
                            ->description('Thông tin về người làm bài và bài kiểm tra')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Người thi')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpanFull(),
                                Select::make('quiz_id')
                                    ->label('Bài kiểm tra')
                                    ->relationship('quiz', 'title')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options([
                                        'in_progress' => 'Đang làm bài',
                                        'completed' => 'Đã hoàn thành',
                                    ])
                                    ->required()
                                    ->default('in_progress')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Kết quả thi')
                            ->description('Điểm số và thống kê')
                            ->schema([
                                TextInput::make('score')
                                    ->label('Điểm số')
                                    ->required()
                                    ->numeric()
                                    ->suffix('điểm')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('total_questions')
                                    ->label('Tổng số câu')
                                    ->required()
                                    ->numeric()
                                    ->suffix('câu')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('correct_answers')
                                    ->label('Số câu đúng')
                                    ->required()
                                    ->numeric()
                                    ->suffix('câu')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Thời gian')
                            ->description('Thông tin thời gian làm bài')
                            ->schema([
                                DateTimePicker::make('started_at')
                                    ->label('Bắt đầu')
                                    ->format('d/m/Y H:i:s')
                                    ->disabled()
                                    ->dehydrated(),
                                DateTimePicker::make('completed_at')
                                    ->label('Hoàn thành')
                                    ->format('d/m/Y H:i:s') 
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('time_taken')
                                    ->label('Thời gian làm bài')
                                    ->numeric()
                                    ->suffix('giây')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Thời gian thực tế làm bài'),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->collapsible(),
                    ]),

                Section::make('Chi tiết câu trả lời')
                    ->description('Các câu hỏi và đáp án đã chọn')
                    ->schema([
                        Placeholder::make('answers_display')
                            ->label('')
                            ->content(function ($record) {
                                if (! $record || ! $record->answers) {
                                    return 'Chưa có câu trả lời nào';
                                }

                                $answers = $record->answers;
                                $html = '<div class="space-y-4">';

                                // Lấy tất cả question IDs từ answers
                                $questionIds = array_keys($answers);
                                
                                // Lấy questions từ database theo IDs
                                $questions = \App\Models\Question::whereIn('id', $questionIds)->get()->keyBy('id');

                                $index = 0;
                                foreach ($answers as $questionId => $userAnswerIndex) {
                                    $index++;
                                    $question = $questions->get($questionId);

                                    if (! $question) {
                                        continue;
                                    }

                                    // Kiểm tra đúng/sai
                                    $isCorrect = false;
                                    $correctAnswerText = '';
                                    $userAnswerText = 'Chưa trả lời';

                                    if ($userAnswerIndex !== null && isset($question->options[$userAnswerIndex])) {
                                        $userAnswer = $question->options[$userAnswerIndex];
                                        $userAnswerText = $userAnswer['data']['text'] ?? 'N/A';
                                        $isCorrect = $userAnswer['data']['is_correct'] ?? false;
                                    }

                                    // Tìm đáp án đúng
                                    foreach ($question->options as $option) {
                                        if ($option['data']['is_correct'] ?? false) {
                                            $correctAnswerText = $option['data']['text'];
                                            break;
                                        }
                                    }

                                    $iconClass = $isCorrect ? 'text-success-600' : 'text-danger-600';
                                    $icon = $isCorrect ? '✓' : '✗';
                                    $bgClass = $isCorrect ? 'bg-success-50 dark:bg-success-950' : 'bg-danger-50 dark:bg-danger-950';

                                    $html .= "
                                        <div class='p-4 rounded-lg {$bgClass}'>
                                            <div class='flex items-start gap-3'>
                                                <div class='shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-gray-900'>
                                                    <span class='text-lg font-bold {$iconClass}'>{$icon}</span>
                                                </div>
                                                <div class='flex-1 space-y-2'>
                                                    <p class='font-semibold text-sm'>Câu {$index}: {$question->question}</p>
                                                    <div class='text-sm space-y-1'>
                                                        <p><strong>Đáp án đã chọn:</strong> <span class='{$iconClass}'>{$userAnswerText}</span></p>
                                                        " . (!$isCorrect ? "<p><strong>Đáp án đúng:</strong> <span class='text-success-600'>{$correctAnswerText}</span></p>" : "") . "
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ";
                                }

                                $html .= '</div>';

                                return new \Illuminate\Support\HtmlString($html);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(1)
                    ->collapsible(),
            ]);
    }
}
