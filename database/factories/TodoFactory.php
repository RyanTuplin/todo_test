<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
   protected $model = Todo::class;

   public function definition(): array
   {
      return [
         'user_id' => User::factory(),
         'title' => fake()->sentence(),
         'description' => fake()->optional()->paragraph(),
         'completed' => fake()->boolean(30),
         'priority' => fake()->optional()->randomElement([
            Priority::HIGH->value,
            Priority::MEDIUM->value,
            Priority::LOW->value,
         ]),
         'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
      ];
   }

   public function completed(): static
   {
      return $this->state(fn(array $attributes) => [
         'completed' => 1,
      ]);
   }

   public function incomplete(): static
   {
      return $this->state(fn(array $attributes) => [
         'completed' => 0,
      ]);
   }

   public function highPriority(): static
   {
      return $this->state(fn(array $attributes) => [
         'priority' => Priority::HIGH->value,
      ]);
   }

   public function mediumPriority(): static
   {
      return $this->state(fn(array $attributes) => [
         'priority' => Priority::MEDIUM->value,
      ]);
   }

   public function lowPriority(): static
   {
      return $this->state(fn(array $attributes) => [
         'priority' => Priority::LOW->value,
      ]);
   }

   public function overdue(): static
   {
      return $this->state(fn(array $attributes) => [
         'due_date' => now()->subDays(fake()->numberBetween(1, 7)),
         'completed' => 0,
      ]);
   }

   public function dueToday(): static
   {
      return $this->state(fn(array $attributes) => [
         'due_date' => now(),
         'completed' => 0,
      ]);
   }

   public function dueSoon(): static
   {
      return $this->state(fn(array $attributes) => [
         'due_date' => now()->addDays(fake()->numberBetween(1, 7)),
         'completed' => 0,
      ]);
   }
}
