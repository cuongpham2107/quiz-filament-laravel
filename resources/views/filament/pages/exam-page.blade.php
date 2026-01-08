<x-filament-panels::page>
    <div class="mx-auto">
        @php
            $examSessions = $this->getExamSessions();
        @endphp

        @if ($examSessions->isEmpty())
            <div class="rounded-lg border border-gray-200 bg-white p-12 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                    <x-heroicon-o-academic-cap class="h-8 w-8 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                    Chưa có đợt thi nào
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Bạn chưa được phân công tham gia đợt thi nào. Vui lòng liên hệ quản trị viên.
                </p>
            </div>
        @else
            <div class="grid grid-cols-3 gap-4">
                @foreach ($examSessions as $examSession)
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div class="p-6">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $examSession->name }}
                                    </h3>
                                    @if ($examSession->description)
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $examSession->description }}
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            Không có mô tả
                                        </p>
                                    @endif
                                </div>

                                <div class="ml-4">
                                    <x-filament::badge color="success">
                                        Đang diễn ra
                                    </x-filament::badge>
                                </div>
                            </div>

                            <div class="mb-6 grid gap-4 sm:grid-cols-2">
                                <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-900">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                                        <x-heroicon-o-calendar class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Thời gian bắt đầu</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $examSession->start_date->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-900">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-danger-100 dark:bg-danger-900">
                                        <x-heroicon-o-calendar class="h-5 w-5 text-danger-600 dark:text-danger-400" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Thời gian kết thúc</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $examSession->end_date->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>

                                @php
                                    $pivot = $examSession->users->where('id', auth()->id())->first()->pivot ?? null;
                                    $attemptsUsed = $pivot?->attempts_used ?? 0;
                                    $maxAttempts = $examSession->max_attempts;
                                @endphp

                                <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-900">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-info-100 dark:bg-info-900">
                                        <x-heroicon-o-clipboard-document-check class="h-5 w-5 text-info-600 dark:text-info-400" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Số lượt thi</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $attemptsUsed }} / {{ $maxAttempts }} lượt
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-900">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-warning-100 dark:bg-warning-900">
                                        <x-heroicon-o-document-text class="h-5 w-5 text-warning-600 dark:text-warning-400" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Số bài thi</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $examSession->quizzes->count() }} bài
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
                                <div class="flex items-center gap-2">
                                    @if ($examSession->randomize_quiz)
                                        <x-filament::badge color="info" icon="heroicon-o-arrow-path">
                                            Random đề thi
                                        </x-filament::badge>
                                    @endif

                                    @if ($examSession->allow_retake)
                                        <x-filament::badge color="success" icon="heroicon-o-check-circle">
                                            Cho phép thi lại
                                        </x-filament::badge>
                                    @endif
                                </div>

                                <x-filament::button
                                    color="primary"
                                    size="lg"
                                    icon="heroicon-o-play"
                                    wire:click="startExam({{ $examSession->id }})"
                                >
                                    Bắt đầu thi
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
