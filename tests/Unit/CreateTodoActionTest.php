<?php

namespace Tests\Unit;

use App\Actions\CreateTodoAction;
use App\DataTransferObjects\TodoData;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTodoActionTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_creates_a_todo()
   {
      $user = User::factory()->create();

      $todoData = new TodoData(
         title: 'Test Todo',
         description: 'Test Description',
         completed: false
      );

      $action = new CreateTodoAction();
      $todo = $action->execute($todoData, $user);

      $this->assertInstanceOf(Todo::class, $todo);
      $this->assertEquals('Test Todo', $todo->title);
      $this->assertEquals('Test Description', $todo->description);
      $this->assertFalse($todo->completed);
      $this->assertEquals($user->id, $todo->user_id);
      $this->assertDatabaseHas('todos', [
         'user_id' => $user->id,
         'title' => 'Test Todo',
         'description' => 'Test Description',
         'completed' => 0,
      ]);
   }

   /** @test */
   public function it_creates_a_todo_without_description()
   {
      $user = User::factory()->create();

      $todoData = new TodoData(
         title: 'Test Todo',
         description: null,
         completed: false
      );

      $action = new CreateTodoAction();
      $todo = $action->execute($todoData, $user);

      $this->assertNull($todo->description);
      $this->assertDatabaseHas('todos', [
         'user_id' => $user->id,
         'title' => 'Test Todo',
         'description' => null,
      ]);
   }
}
