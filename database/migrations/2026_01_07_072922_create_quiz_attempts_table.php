<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->integer('score')->default(0); // Điểm số đạt được
            $table->integer('total_questions')->default(0); // Tổng số câu hỏi
            $table->integer('correct_answers')->default(0); // Số câu trả lời đúng
            $table->json('answers')->nullable(); // Lưu các câu trả lời của user
            $table->timestamp('started_at')->nullable(); // Thời gian bắt đầu
            $table->timestamp('completed_at')->nullable(); // Thời gian hoàn thành
            $table->integer('time_taken')->nullable(); // Thời gian làm bài (giây)
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->timestamps();

            // Index để query nhanh hơn
            $table->index(['user_id', 'quiz_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
