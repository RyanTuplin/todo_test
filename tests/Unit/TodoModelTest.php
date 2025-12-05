<?php

namespace Tests\Unit;

use App\Enums\Priority;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoModelTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_can_determine_if_todo_is_overdue()
   {
      $user = User::factory()->create();

      $overdueTodo = Todo::factory()->for($user)->create([
         'due_date' => Carbon::yesterday(),
         'completed' => false,
      ]);

      $this->assertTrue($overdueTodo->isOverdue());
   }

   /** @test */
   public function it_returns_false_for_overdue_if_completed()
   {
      $user = User::factory()->create();

      $completedTodo = Todo::factory()->for($user)->create([
         'due_date' => Carbon::yesterday(),
         'completed' => true,
      ]);

      $this->assertFalse($completedTodo->isOverdue());
   }

   /** @test */
   public function it_returns_false_for_overdue_if_no_due_date()
   {
      $user = User::factory()->create();

      $todo = Todo::factory()->for($user)->create([
         'due_date' => null,
         'completed' => false,
      ]);

      $this->assertFalse($todo->isOverdue());
   }

   /** @test */
   public function it_can_determine_if_todo_is_due_today()
   {
      $user = User::factory()->create();

      $todayTodo = Todo::factory()->for($user)->create([
         'due_date' => Carbon::today(),
         'completed' => false,
      ]);

      $this->assertTrue($todayTodo->isDueToday());
   }

   /** @test */
   public function it_returns_false_for_due_today_if_completed()
   {
      $user = User::factory()->create();

      $completedTodo = Todo::factory()->for($user)->create([
         'due_date' => Carbon::today(),
         'completed' => true,
      ]);

      $this->assertFalse($completedTodo->isDueToday());
   }

   /** @test */
   public function it_can_scope_overdue_todos()
   {
      $user = User::factory()->create();

      Todo::factory()->for($user)->overdue()->create();
      Todo::factory()->for($user)->dueToday()->create();
      Todo::factory()->for($user)->dueSoon()->create();

      $overdueTodos = Todo::overdue()->get();

      $this->assertCount(1, $overdueTodos);
   }

   /** @test */
   public function it_can_scope_due_today_todos()
   {
      $user = User::factory()->create();

      Todo::factory()->for($user)->overdue()->create();
      Todo::factory()->for($user)->dueToday()->create();
      Todo::factory()->for($user)->dueSoon()->create();

      $todayTodos = Todo::dueToday()->get();

      $this->assertCount(1, $todayTodos);
   }

   /** @test */
   public function it_can_scope_due_soon_todos()
   {
      $user = User::factory()->create();

      Todo::factory()->for($user)->overdue()->create();
      Todo::factory()->for($user)->dueToday()->create();
      Todo::factory()->for($user)->dueSoon()->create();

      $dueSoonTodos = Todo::dueSoon()->get();

      $this->assertGreaterThanOrEqual(1, $dueSoonTodos->count());
   }

   /** @test */
   public function it_can_scope_by_priority()
   {
      $user = User::factory()->create();

      Todo::factory()->for($user)->highPriority()->create();
      Todo::factory()->for($user)->mediumPriority()->create();
      Todo::factory()->for($user)->lowPriority()->create();

      $highPriorityTodos = Todo::byPriority(Priority::HIGH)->get();

      $this->assertCount(1, $highPriorityTodos);
      $this->assertEquals(Priority::HIGH, $highPriorityTodos->first()->priority);
   }

   /** @test */
   public function it_casts_priority_to_enum()
   {
      $user = User::factory()->create();

      $todo = Todo::factory()->for($user)->create([
         'priority' => Priority::HIGH->value,
      ]);

      $this->assertInstanceOf(Priority::class, $todo->priority);
      $this->assertEquals(Priority::HIGH, $todo->priority);
   }

   /** @test */
   public function it_casts_due_date_to_carbon()
   {
      $user = User::factory()->create();

      $todo = Todo::factory()->for($user)->create([
         'due_date' => '2025-12-31',
      ]);

      $this->assertInstanceOf(Carbon::class, $todo->due_date);
   }
}
