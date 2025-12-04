<?php

namespace Tests\Unit;

use App\Actions\CreateTodoAction;
use App\DataTransferObjects\TodoData;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTodoActionTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_creates_a_todo()
   {
      $todoData = new TodoData(
         title: 'Test Todo',
         description: 'Test Description',
         completed: false
      );

      $action = new CreateTodoAction();
      $todo = $action->execute($todoData);

      $this->assertInstanceOf(Todo::class, $todo);
      $this->assertEquals('Test Todo', $todo->title);
      $this->assertEquals('Test Description', $todo->description);
      $this->assertFalse($todo->completed);
      $this->assertDatabaseHas('todos', [
         'title' => 'Test Todo',
         'description' => 'Test Description',
         'completed' => false,
      ]);
   }

   /** @test */
   public function it_creates_a_todo_without_description()
   {
      $todoData = new TodoData(
         title: 'Test Todo',
         description: null,
         completed: false
      );

      $action = new CreateTodoAction();
      $todo = $action->execute($todoData);

      $this->assertNull($todo->description);
      $this->assertDatabaseHas('todos', [
         'title' => 'Test Todo',
         'description' => null,
      ]);
   }
}
