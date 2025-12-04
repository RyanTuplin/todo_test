<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $colors = [
            '#EF4444', // Red
            '#F59E0B', // Amber
            '#10B981', // Green
            '#3B82F6', // Blue
            '#8B5CF6', // Purple
            '#EC4899', // Pink
        ];

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Work', 'Personal', 'Shopping', 'Health', 'Finance', 'Home']),
            'color' => fake()->randomElement($colors),
        ];
    }
}
