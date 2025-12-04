<?php

namespace Database\Factories;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
   protected $model = Todo::class;

   public function definition(): array
   {
      return [
         'title' => fake()->sentence(),
         'description' => fake()->optional()->paragraph(),
         'completed' => fake()->boolean(30),
      ];
   }

   public function completed(): static
   {
      return $this->state(fn(array $attributes) => [
         'completed' => true,
      ]);
   }

   public function incomplete(): static
   {
      return $this->state(fn(array $attributes) => [
         'completed' => false,
      ]);
   }
}
