<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo 10 users
        User::factory(10)->create();

        // Tạo user admin test
        User::factory()->create([
            'name' => 'Admin Hệ Thống',
            'email' => 'admin@company.vn',
        ]);

        // Tạo 10 categories
        $categories = Category::factory(10)->create();

        // Tạo 20 questions với categories đã có
        $questions = Question::factory(20)->recycle($categories)->create();

        // Tạo 10 quizzes
        $quizzes = Quiz::factory(10)->create();

        // Gán ngẫu nhiên câu hỏi vào từng quiz
        foreach ($quizzes as $quiz) {
            $randomQuestions = $questions->random(rand(5, 10));
            
            $quiz->questions()->attach(
                $randomQuestions->pluck('id')->mapWithKeys(function ($id, $index) {
                    return [$id => ['order' => $index]];
                })->toArray()
            );
        }

        $this->command->info('✅ Đã tạo dữ liệu mẫu cho hệ thống trắc nghiệm nội bộ:');
        $this->command->info('   - 11 Nhân viên (bao gồm admin@company.vn)');
        $this->command->info('   - 10 Danh mục kiến thức');
        $this->command->info('   - 20 Câu hỏi trắc nghiệm');
        $this->command->info('   - 10 Bài kiểm tra (mỗi bài có 5-10 câu hỏi)');
        $this->command->info('');
        $this->command->info('🔐 Đăng nhập với:');
        $this->command->info('   Email: admin@company.vn');
        $this->command->info('   Password: password');
    }
}
