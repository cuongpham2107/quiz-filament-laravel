<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Quy định nội bộ công ty',
            'An toàn lao động',
            'Kỹ năng giao tiếp',
            'Quản lý thời gian',
            'Văn hóa doanh nghiệp',
            'Công nghệ thông tin cơ bản',
            'Quy trình làm việc',
            'Chính sách nhân sự',
            'Bảo mật thông tin',
            'Kỹ năng làm việc nhóm',
            'Dịch vụ khách hàng',
            'Marketing & Sales',
        ]);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'count_questions' => 0,
            'parent_id' => null,
        ];
    }
}
