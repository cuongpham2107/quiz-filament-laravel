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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('number_of_questions')->default(0);
            $table->integer('time_limit')->default(300);
            $table->boolean('shuffle_questions')->default(false);// Randomize question order for each attempt
            $table->boolean('shuffle_answers')->default(false);// Randomize answer options for each question
            $table->boolean('allow_multiple_attempts')->default(false);// Allow users to retake the quiz
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
