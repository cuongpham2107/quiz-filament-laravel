<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Đào tạo nhân viên mới - Tuần 1',
            'Đào tạo nhân viên mới - Tuần 2',
            'Kiểm tra định kỳ - Quy định công ty',
            'Kiến thức An toàn lao động',
            'Đánh giá năng lực - Quý 1/2026',
            'Bảo mật thông tin - Bắt buộc',
            'Kỹ năng giao tiếp nội bộ',
            'Quy trình làm việc chuẩn',
            'Văn hóa doanh nghiệp',
            'Đào tạo định kỳ - Tháng 1',
            'Kiểm tra cuối khóa',
            'Đánh giá năng lực - Quý 2/2026',
            'Bảo mật thông tin nâng cao',
            'Kỹ năng làm việc nhóm',
            'Dịch vụ khách hàng xuất sắc',
        ];

        return [
            'title' => fake()->randomElement($titles) . ' - ' . fake()->unique()->numberBetween(1, 999),
            'description' => 'Bài kiểm tra này giúp đánh giá kiến thức và kỹ năng của nhân viên. Vui lòng hoàn thành trong thời gian quy định.',
            'number_of_questions' => fake()->numberBetween(5, 15),
            'time_limit' => fake()->randomElement([600, 900, 1200, 1800]), // 10, 15, 20, 30 phút
            'shuffle_questions' => fake()->boolean(70), // 70% là có shuffle
            'shuffle_answers' => fake()->boolean(70),
            'allow_multiple_attempts' => fake()->boolean(30), // 30% cho phép làm lại
        ];
    }
}
