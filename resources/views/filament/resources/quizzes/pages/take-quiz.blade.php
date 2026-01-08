<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Timer --}}
        <div class="flex justify-between items-center p-4 bg-primary-50 dark:bg-primary-900 rounded-lg">
            <div>
                <h3 class="text-lg font-semibold">{{ $record->title }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Tổng số câu: {{ count($questions) }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-primary-600" x-data="timer({{ $timeRemaining }})" x-init="startTimer()">
                    <span x-text="formatTime(timeRemaining)"></span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Thời gian còn lại</p>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div class="bg-primary-600 h-2.5 rounded-full" 
                 style="width: {{ ($currentQuestionIndex + 1) / count($questions) * 100 }}%">
            </div>
        </div>

        {{-- Question Form --}}
        <form wire:submit="nextQuestion">
            {{ $this->form }}

            {{-- Question Navigator --}}
            <div class="mt-8">
                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between gap-4 mb-6">
                    {{-- Previous Button --}}
                    @if($currentQuestionIndex > 0)
                        <x-filament::button
                            color="gray"
                            wire:click="previousQuestion"
                            type="button"
                        >
                            ← Câu trước
                        </x-filament::button>
                    @else
                        <div></div>
                    @endif

                    {{-- Question Overview --}}
                    <div class="text-center">
                        <span class="text-lg font-semibold">
                            Câu {{ $currentQuestionIndex + 1 }} / {{ count($questions) }}
                        </span>
                    </div>

                    {{-- Next Button --}}
                    @if($currentQuestionIndex < count($questions) - 1)
                        <x-filament::button
                            color="gray"
                            type="button"
                            wire:click="nextQuestion"
                        >
                            Câu tiếp theo →
                        </x-filament::button>
                    @else
                        <x-filament::modal 
                            icon="heroicon-o-question-mark-circle"
                            iconColor="warning"
                            id="submit-quiz-modal" width="md">
                            <x-slot name="trigger">
                                <x-filament::button type="button">
                                    Nộp bài
                                </x-filament::button>
                            </x-slot>

                            <x-slot name="heading">
                                Xác nhận nộp bài
                            </x-slot>

                            <x-slot name="description">
                                Bạn có chắc chắn muốn nộp bài không? Bạn sẽ không thể thay đổi câu trả lời sau khi nộp.
                            </x-slot>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <span class="text-sm font-medium">Tổng số câu:</span>
                                    <span class="text-sm font-bold">{{ count($questions) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <span class="text-sm font-medium">Đã trả lời:</span>
                                    <span class="text-sm font-bold text-success-600">{{ count($attempt->answers ?? []) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <span class="text-sm font-medium">Chưa trả lời:</span>
                                    <span class="text-sm font-bold text-danger-600">{{ count($questions) - count($attempt->answers ?? []) }}</span>
                                </div>
                            </div>

                            <x-slot name="footerActions">
                                <x-filament::button
                                    color="gray"
                                    x-on:click="close"
                                >
                                    Hủy
                                </x-filament::button>

                                <x-filament::button
                                    wire:click="submitQuiz"
                                    x-on:click="close"
                                >
                                    Xác nhận nộp bài
                                </x-filament::button>
                            </x-slot>
                        </x-filament::modal>
                    @endif
                </div>

                {{-- Question Numbers Grid --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h4 class="font-semibold mb-3 text-sm text-gray-700 dark:text-gray-300">Chuyển đến câu hỏi:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($questions as $index => $question)
                            @php
                                $isAnswered = isset($attempt->answers[$question['id']]);
                                $isCurrent = $index === $currentQuestionIndex;
                            @endphp
                            <button
                                type="button"
                                wire:click="$set('currentQuestionIndex', {{ $index }})"
                                @class([
                                    'relative w-10 h-10 rounded-lg font-semibold transition-all',
                                    'bg-primary-600 text-white ring-2 ring-primary-300' => $isCurrent && !$isAnswered,
                                    'bg-success-600 text-white ring-2 ring-success-300' => $isCurrent && $isAnswered,
                                    'bg-success-500 text-white' => !$isCurrent && $isAnswered,
                                    'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-primary-100 dark:hover:bg-primary-900' => !$isCurrent && !$isAnswered,
                                ])
                                title="Câu {{ $index + 1 }}{{ $isAnswered ? ' (Đã trả lời)' : '' }}"
                            >
                                {{ $index + 1 }}
                                
                                @if($isAnswered)
                                    <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 bg-white dark:bg-gray-900 rounded-full">
                                        <svg class="w-3 h-3 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>

    @script
    <script>
        Alpine.data('timer', (initialTime) => ({
            timeRemaining: initialTime,
            interval: null,
            
            startTimer() {
                this.interval = setInterval(() => {
                    this.timeRemaining--;
                    
                    if (this.timeRemaining <= 0) {
                        clearInterval(this.interval);
                        $wire.submitQuiz();
                    }
                }, 1000);
            },
            
            formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }
        }));
    </script>
    @endscript
</x-filament-panels::page>

