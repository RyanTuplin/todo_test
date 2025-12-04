<?php

namespace Tests\Unit;

use App\Actions\DeleteTodoAction;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTodoActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_a_todo()
    {
        $user = User::factory()->create();

        $todo = Todo::factory()->for($user)->create([
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'completed' => 0,
        ]);

        $action = new DeleteTodoAction();
        $action->execute($todo);

        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }
}
