<?php

namespace Tests\Unit;

use App\Actions\Todos\SetTodoPriorityAction;
use App\Enums\Priority;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetTodoPriorityActionTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_sets_a_priority_on_a_todo()
   {
      $user = User::factory()->create();
      $todo = Todo::factory()->for($user)->create([
         'priority' => null,
      ]);

      $action = new SetTodoPriorityAction();
      $updatedTodo = $action->execute($todo, Priority::HIGH);

      $this->assertEquals(Priority::HIGH, $updatedTodo->priority);
      $this->assertDatabaseHas('todos', [
         'id' => $todo->id,
         'priority' => Priority::HIGH->value,
      ]);
   }

   /** @test */
   public function it_can_update_priority()
   {
      $user = User::factory()->create();
      $todo = Todo::factory()->for($user)->create([
         'priority' => Priority::LOW->value,
      ]);

      $action = new SetTodoPriorityAction();
      $updatedTodo = $action->execute($todo, Priority::HIGH);

      $this->assertEquals(Priority::HIGH, $updatedTodo->priority);
   }

   /** @test */
   public function it_can_remove_priority()
   {
      $user = User::factory()->create();
      $todo = Todo::factory()->for($user)->create([
         'priority' => Priority::HIGH->value,
      ]);

      $action = new SetTodoPriorityAction();
      $updatedTodo = $action->execute($todo, null);

      $this->assertNull($updatedTodo->priority);
   }
}
