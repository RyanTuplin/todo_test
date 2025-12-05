<?php

namespace Tests\Unit;

use App\Actions\Todos\SetTodoDueDateAction;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetTodoDueDateActionTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_sets_a_due_date_on_a_todo()
   {
      $user = User::factory()->create();
      $todo = Todo::factory()->for($user)->create([
         'due_date' => null,
      ]);

      $dueDate = Carbon::tomorrow();

      $action = new SetTodoDueDateAction();
      $updatedTodo = $action->execute($todo, $dueDate);

      $this->assertTrue($updatedTodo->due_date->isSameDay($dueDate));
   }

   /** @test */
   public function it_can_update_due_date()
   {
      $user = User::factory()->create();
      $todo = Todo::factory()->for($user)->create([
         'due_date' => Carbon::today(),
      ]);

      $newDueDate = Carbon::tomorrow();

      $action = new SetTodoDueDateAction();
      $updatedTodo = $action->execute($todo, $newDueDate);

      $this->assertTrue($updatedTodo->due_date->isSameDay($newDueDate));
   }

   /** @test */
   public function it_can_remove_due_date()
   {
      $user = User::factory()->create();
      $todo = Todo::factory()->for($user)->create([
         'due_date' => Carbon::tomorrow(),
      ]);

      $action = new SetTodoDueDateAction();
      $updatedTodo = $action->execute($todo, null);

      $this->assertNull($updatedTodo->due_date);
   }
}
