<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(6)
            ->components([
                Textarea::make('question')
                    ->label('Câu hỏi')
                    ->required()
                    ->rows(10)
                    ->columnSpan(3),
                MarkdownEditor::make('explanation')
                    ->label('Giải thích')

                    ->columnSpan(3),
                FileUpload::make('image')
                    ->disk('s3')
                    ->directory('form-attachments')
                    ->visibility('public')
                    ->label('Hình ảnh')
                    ->columnSpanFull()
                    ->columnSpan(2),
                Select::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->required()
                    ->columnSpan(2),

                Radio::make('difficulty')
                    ->label('Độ khó')
                    ->options([
                        'easy' => 'Dễ',
                        'medium' => 'Trung bình',
                        'hard' => 'Khó',
                    ])
                    ->required()
                    ->default('medium')
                    ->columnSpan(2),

                Builder::make('options')
                    ->label('Đáp án')
                    ->columnSpanFull()
                    ->blocks([
                        Builder\Block::make('option')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Nội dung')
                                    ->live(onBlur: true)
                                    ->required(),
                                Toggle::make('is_correct')
                                    ->label('Đáp án đúng')
                                    ->default(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get, $component) {
                                        // Nếu toggle này được bật (chọn là đáp án đúng)
                                        if ($state === true) {
                                            // Lấy statePath: "data.options.UUID.data.is_correct"
                                            $statePath = $component->getStatePath();
                                            $pathParts = explode('.', $statePath);

                                            // Lấy UUID của item hiện tại (vị trí thứ 2 trong path)
                                            $currentUuid = $pathParts[2] ?? null;

                                            // Lấy tất cả options bằng cách đi lên đúng level
                                            $livewire = $component->getLivewire();
                                            $allOptions = data_get($livewire, 'data.options');

                                            if (is_array($allOptions) && $currentUuid !== null) {
                                                // Tắt tất cả toggle khác
                                                foreach ($allOptions as $uuid => $option) {
                                                    if ($uuid !== $currentUuid) {
                                                        data_set($livewire, "data.options.{$uuid}.data.is_correct", false);
                                                    }
                                                }
                                            }
                                        }
                                    }),
                            ])->label(function (?array $state): string {
                                if ($state === null) {
                                    return 'Đáp án';
                                }

                                return $state['text'] ?? 'Đáp án';
                            }),
                    ])
                    ->default([
                        [
                            'type' => 'option',
                            'data' => ['text' => '', 'is_correct' => false],
                        ],
                        [
                            'type' => 'option',
                            'data' => ['text' => '', 'is_correct' => false],
                        ],
                        [
                            'type' => 'option',
                            'data' => ['text' => '', 'is_correct' => false],
                        ],
                        [
                            'type' => 'option',
                            'data' => ['text' => '', 'is_correct' => false],
                        ],
                    ])
                    ->blockIcons()
                    ->minItems(2)
                    ->maxItems(5)
                    ->cloneable()
                    ->reorderableWithButtons()
                    ->addActionLabel('Thêm đáp án'),
            ]);
    }
}
