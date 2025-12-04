<?php

namespace Tests\Unit;

use App\Actions\UpdateTodoAction;
use App\DataTransferObjects\TodoData;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTodoActionTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_updates_a_todo()
   {
      $todo = Todo::create([
         'title' => 'Original Title',
         'description' => 'Original Description',
         'completed' => false,
      ]);

      $todoData = new TodoData(
         title: 'Updated Title',
         description: 'Updated Description',
         completed: true
      );

      $action = new UpdateTodoAction();
      $updatedTodo = $action->execute($todo, $todoData);

      $this->assertEquals('Updated Title', $updatedTodo->title);
      $this->assertEquals('Updated Description', $updatedTodo->description);
      $this->assertTrue($updatedTodo->completed);
      $this->assertDatabaseHas('todos', [
         'id' => $todo->id,
         'title' => 'Updated Title',
      ]);
   }

   /** @test */
   public function it_can_toggle_completed_status()
   {
      $todo = Todo::create([
         'title' => 'Test Todo',
         'completed' => false,
      ]);

      $todoData = new TodoData(
         title: 'Test Todo',
         description: null,
         completed: true
      );

      $action = new UpdateTodoAction();
      $updatedTodo = $action->execute($todo, $todoData);

      $this->assertTrue($updatedTodo->completed);
   }
}
