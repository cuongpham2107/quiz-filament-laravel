<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $questions = [
            [
                'question' => 'Giờ làm việc chính thức của công ty là từ mấy giờ đến mấy giờ?',
                'options' => [
                    ['text' => '8:00 - 17:00', 'correct' => true],
                    ['text' => '9:00 - 18:00', 'correct' => false],
                    ['text' => '7:30 - 16:30', 'correct' => false],
                    ['text' => '8:30 - 17:30', 'correct' => false],
                ],
                'explanation' => 'Theo quy định công ty, giờ làm việc chính thức là 8:00 - 17:00, nghỉ trưa từ 12:00 - 13:00.',
            ],
            [
                'question' => 'Khi phát hiện sự cố về an toàn lao động, nhân viên cần làm gì?',
                'options' => [
                    ['text' => 'Báo ngay cho quản lý trực tiếp và bộ phận an toàn', 'correct' => true],
                    ['text' => 'Tự xử lý nếu có thể', 'correct' => false],
                    ['text' => 'Chờ đến cuối ca mới báo cáo', 'correct' => false],
                    ['text' => 'Không cần báo cáo nếu sự cố nhỏ', 'correct' => false],
                ],
                'explanation' => 'Mọi sự cố về an toàn cần được báo cáo ngay lập tức để có biện pháp xử lý kịp thời.',
            ],
            [
                'question' => 'Số ngày phép năm của nhân viên chính thức là bao nhiêu?',
                'options' => [
                    ['text' => '12 ngày', 'correct' => true],
                    ['text' => '10 ngày', 'correct' => false],
                    ['text' => '15 ngày', 'correct' => false],
                    ['text' => '14 ngày', 'correct' => false],
                ],
                'explanation' => 'Theo Luật lao động và quy định công ty, nhân viên được hưởng 12 ngày phép năm.',
            ],
            [
                'question' => 'Mật khẩu của hệ thống nội bộ công ty cần được thay đổi sau bao lâu?',
                'options' => [
                    ['text' => '3 tháng một lần', 'correct' => true],
                    ['text' => '6 tháng một lần', 'correct' => false],
                    ['text' => '1 năm một lần', 'correct' => false],
                    ['text' => 'Không cần thay đổi', 'correct' => false],
                ],
                'explanation' => 'Để đảm bảo an ninh thông tin, mật khẩu cần được thay đổi định kỳ 3 tháng một lần.',
            ],
            [
                'question' => 'Khi nhận được email từ người lạ có file đính kèm, bạn nên làm gì?',
                'options' => [
                    ['text' => 'Không mở file, báo cho bộ phận IT', 'correct' => true],
                    ['text' => 'Mở file để kiểm tra nội dung', 'correct' => false],
                    ['text' => 'Chuyển tiếp cho đồng nghiệp', 'correct' => false],
                    ['text' => 'Xóa email ngay lập tức', 'correct' => false],
                ],
                'explanation' => 'File đính kèm từ nguồn không rõ có thể chứa mã độc. Cần báo IT để xử lý.',
            ],
            [
                'question' => 'Giá trị cốt lõi nào KHÔNG phải là giá trị của công ty?',
                'options' => [
                    ['text' => 'Lợi nhuận trên hết', 'correct' => true],
                    ['text' => 'Đoàn kết', 'correct' => false],
                    ['text' => 'Sáng tạo', 'correct' => false],
                    ['text' => 'Trách nhiệm', 'correct' => false],
                ],
                'explanation' => 'Công ty đề cao các giá trị: Đoàn kết, Sáng tạo, Trách nhiệm, Tôn trọng và Chính trực.',
            ],
            [
                'question' => 'Thời gian thử việc tiêu chuẩn cho vị trí nhân viên là bao lâu?',
                'options' => [
                    ['text' => '2 tháng', 'correct' => true],
                    ['text' => '1 tháng', 'correct' => false],
                    ['text' => '3 tháng', 'correct' => false],
                    ['text' => '6 tháng', 'correct' => false],
                ],
                'explanation' => 'Thời gian thử việc chuẩn là 2 tháng, có thể kéo dài tùy vị trí công việc.',
            ],
            [
                'question' => 'Khi làm việc với khách hàng, điều quan trọng nhất là gì?',
                'options' => [
                    ['text' => 'Lắng nghe và thấu hiểu nhu cầu', 'correct' => true],
                    ['text' => 'Bán được sản phẩm', 'correct' => false],
                    ['text' => 'Nhanh chóng kết thúc cuộc gọi', 'correct' => false],
                    ['text' => 'Giới thiệu nhiều sản phẩm nhất có thể', 'correct' => false],
                ],
                'explanation' => 'Hiểu nhu cầu khách hàng là nền tảng để cung cấp dịch vụ tốt nhất.',
            ],
        ];

        $randomQuestion = fake()->randomElement($questions);

        return [
            'question' => $randomQuestion['question'],
            'explanation' => $randomQuestion['explanation'],
            'category_id' => Category::factory(),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'image' => null,
            'options' => collect($randomQuestion['options'])->map(function ($option) {
                return [
                    'type' => 'option',
                    'data' => [
                        'text' => $option['text'],
                        'is_correct' => $option['correct'],
                    ],
                ];
            })->toArray(),
        ];
    }
}
