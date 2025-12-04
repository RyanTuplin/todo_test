<?php

namespace Tests\Unit;

use App\Actions\DeleteTodoAction;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTodoActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_a_todo()
    {
        $todo = Todo::create([
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'completed' => false,
        ]);

        $action = new DeleteTodoAction();
        $action->execute($todo);

        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }
}