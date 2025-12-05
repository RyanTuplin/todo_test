<?php

namespace Tests\Feature;

use App\Enums\Priority;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
   use RefreshDatabase;

   protected User $user;

   protected function setUp(): void
   {
      parent::setUp();

      // Create a user for all tests
      $this->user = User::factory()->create();
   }

   /** @test */
   public function it_can_list_all_todos()
   {
      Todo::factory()->count(3)->for($this->user)->create();

      $response = $this->actingAs($this->user)->getJson('/api/todos');

      $response->assertStatus(200)
         ->assertJsonCount(3, 'data');
   }

   /** @test */
   public function it_only_lists_authenticated_users_todos()
   {
      // Create todos for this user
      Todo::factory()->count(2)->for($this->user)->create();

      // Create todos for another user
      $otherUser = User::factory()->create();
      Todo::factory()->count(3)->for($otherUser)->create();

      $response = $this->actingAs($this->user)->getJson('/api/todos');

      $response->assertStatus(200)
         ->assertJsonCount(2, 'data'); // Should only see own todos
   }

   /** @test */
   public function it_requires_authentication_to_list_todos()
   {
      $response = $this->getJson('/api/todos');

      $response->assertStatus(401);
   }

   /** @test */
   public function it_returns_empty_array_when_no_todos_exist()
   {
      $response = $this->actingAs($this->user)->getJson('/api/todos');

      $response->assertStatus(200)
         ->assertJson(['data' => []]);
   }

   /** @test */
   public function it_can_create_a_todo()
   {
      $data = [
         'title' => 'New Todo',
         'description' => 'Todo Description',
         'completed' => false,
      ];

      $response = $this->actingAs($this->user)->postJson('/api/todos', $data);

      $response->assertStatus(201)
         ->assertJson([
            'data' => [
               'title' => 'New Todo',
               'description' => 'Todo Description',
               'completed' => false,
            ]
         ]);

      $this->assertDatabaseHas('todos', [
         'user_id' => $this->user->id,
         'title' => 'New Todo',
      ]);
   }

   /** @test */
   public function it_requires_authentication_to_create_todo()
   {
      $response = $this->postJson('/api/todos', [
         'title' => 'New Todo',
      ]);

      $response->assertStatus(401);
   }

   /** @test */
   public function it_can_create_a_todo_without_description()
   {
      $data = [
         'title' => 'New Todo',
      ];

      $response = $this->actingAs($this->user)->postJson('/api/todos', $data);

      $response->assertStatus(201)
         ->assertJson([
            'data' => [
               'title' => 'New Todo',
               'description' => null,
               'completed' => false,
            ]
         ]);
   }

   /** @test */
   public function it_validates_required_title_when_creating()
   {
      $response = $this->actingAs($this->user)->postJson('/api/todos', [
         'description' => 'Description only',
      ]);

      $response->assertStatus(422)
         ->assertJsonValidationErrors(['title']);
   }

   /** @test */
   public function it_validates_title_max_length()
   {
      $response = $this->actingAs($this->user)->postJson('/api/todos', [
         'title' => str_repeat('a', 256),
      ]);

      $response->assertStatus(422)
         ->assertJsonValidationErrors(['title']);
   }

   /** @test */
   public function it_can_show_a_single_todo()
   {
      $todo = Todo::factory()->for($this->user)->create([
         'title' => 'Test Todo',
         'description' => 'Test Description',
      ]);

      $response = $this->actingAs($this->user)->getJson("/api/todos/{$todo->id}");

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'id' => $todo->id,
               'title' => 'Test Todo',
               'description' => 'Test Description',
            ]
         ]);
   }

   /** @test */
   public function it_cannot_show_another_users_todo()
   {
      $otherUser = User::factory()->create();
      $todo = Todo::factory()->for($otherUser)->create();

      $response = $this->actingAs($this->user)->getJson("/api/todos/{$todo->id}");

      $response->assertStatus(403); // Forbidden
   }

   /** @test */
   public function it_returns_404_for_non_existent_todo()
   {
      $response = $this->actingAs($this->user)->getJson('/api/todos/999');

      $response->assertStatus(404);
   }

   /** @test */
   public function it_can_update_a_todo()
   {
      $todo = Todo::factory()->for($this->user)->create([
         'title' => 'Original Title',
         'completed' => 0,
      ]);

      $response = $this->actingAs($this->user)->putJson("/api/todos/{$todo->id}", [
         'title' => 'Updated Title',
         'description' => 'Updated Description',
         'completed' => true,
      ]);

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'id' => $todo->id,
               'title' => 'Updated Title',
               'description' => 'Updated Description',
               'completed' => true,
            ]
         ]);

      $this->assertDatabaseHas('todos', [
         'id' => $todo->id,
         'title' => 'Updated Title',
         'completed' => 1,
      ]);
   }

   /** @test */
   public function it_cannot_update_another_users_todo()
   {
      $otherUser = User::factory()->create();
      $todo = Todo::factory()->for($otherUser)->create();

      $response = $this->actingAs($this->user)->putJson("/api/todos/{$todo->id}", [
         'title' => 'Hacked Title',
      ]);

      $response->assertStatus(403);
   }

   /** @test */
   public function it_can_partially_update_a_todo()
   {
      $todo = Todo::factory()->for($this->user)->create([
         'title' => 'Original Title',
         'description' => 'Original Description',
         'completed' => 0,
      ]);

      $response = $this->actingAs($this->user)->putJson("/api/todos/{$todo->id}", [
         'completed' => true,
      ]);

      $response->assertStatus(200);

      $this->assertDatabaseHas('todos', [
         'id' => $todo->id,
         'title' => 'Original Title',
         'completed' => 1,
      ]);
   }

   /** @test */
   public function it_can_delete_a_todo()
   {
      $todo = Todo::factory()->for($this->user)->create();

      $response = $this->actingAs($this->user)->deleteJson("/api/todos/{$todo->id}");

      $response->assertStatus(204);

      $this->assertDatabaseMissing('todos', [
         'id' => $todo->id,
      ]);
   }

   /** @test */
   public function it_cannot_delete_another_users_todo()
   {
      $otherUser = User::factory()->create();
      $todo = Todo::factory()->for($otherUser)->create();

      $response = $this->actingAs($this->user)->deleteJson("/api/todos/{$todo->id}");

      $response->assertStatus(403);

      $this->assertDatabaseHas('todos', [
         'id' => $todo->id,
      ]);
   }

   /** @test */
   public function it_returns_404_when_deleting_non_existent_todo()
   {
      $response = $this->actingAs($this->user)->deleteJson('/api/todos/999');

      $response->assertStatus(404);
   }
   /** @test */
   public function it_can_create_a_todo_with_priority()
   {
      $data = [
         'title' => 'High Priority Task',
         'priority' => Priority::HIGH->value,
      ];

      $response = $this->actingAs($this->user)->postJson('/api/todos', $data);

      $response->assertStatus(201)
         ->assertJson([
            'data' => [
               'title' => 'High Priority Task',
               'priority' => Priority::HIGH->value,
               'priority_label' => 'High',
            ]
         ]);

      $this->assertDatabaseHas('todos', [
         'user_id' => $this->user->id,
         'title' => 'High Priority Task',
         'priority' => Priority::HIGH->value,
      ]);
   }

   /** @test */
   public function it_can_create_a_todo_with_due_date()
   {
      $dueDate = Carbon::tomorrow()->format('Y-m-d');

      $data = [
         'title' => 'Task with deadline',
         'due_date' => $dueDate,
      ];

      $response = $this->actingAs($this->user)->postJson('/api/todos', $data);

      $response->assertStatus(201)
         ->assertJson([
            'data' => [
               'title' => 'Task with deadline',
               'due_date' => $dueDate,
            ]
         ]);

      $todo = Todo::where('title', 'Task with deadline')->first();
      $this->assertTrue($todo->due_date->isSameDay(Carbon::parse($dueDate)));
   }

   /** @test */
   public function it_validates_priority_must_be_valid_enum()
   {
      $response = $this->actingAs($this->user)->postJson('/api/todos', [
         'title' => 'Test',
         'priority' => 'invalid',
      ]);

      $response->assertStatus(422)
         ->assertJsonValidationErrors(['priority']);
   }

   /** @test */
   public function it_validates_due_date_must_not_be_in_past()
   {
      $response = $this->actingAs($this->user)->postJson('/api/todos', [
         'title' => 'Test',
         'due_date' => Carbon::yesterday()->format('Y-m-d'),
      ]);

      $response->assertStatus(422)
         ->assertJsonValidationErrors(['due_date']);
   }

   /** @test */
   public function it_can_update_todo_priority()
   {
      $todo = Todo::factory()->for($this->user)->create([
         'priority' => Priority::LOW->value,
      ]);

      $response = $this->actingAs($this->user)->putJson("/api/todos/{$todo->id}", [
         'priority' => Priority::HIGH->value,
      ]);

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'priority' => Priority::HIGH->value,
            ]
         ]);

      $this->assertDatabaseHas('todos', [
         'id' => $todo->id,
         'priority' => Priority::HIGH->value,
      ]);
   }

   /** @test */
   public function it_can_update_todo_due_date()
   {
      $todo = Todo::factory()->for($this->user)->create([
         'due_date' => Carbon::today(),
      ]);

      $newDueDate = Carbon::tomorrow()->format('Y-m-d');

      $response = $this->actingAs($this->user)->putJson("/api/todos/{$todo->id}", [
         'due_date' => $newDueDate,
      ]);

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'due_date' => $newDueDate,
            ]
         ]);

      $this->assertTrue($todo->fresh()->due_date->isSameDay(Carbon::parse($newDueDate)));
   }

   /** @test */
   public function it_can_remove_priority_from_todo()
   {
      $todo = Todo::factory()->for($this->user)->create([
         'priority' => Priority::HIGH->value,
      ]);

      $response = $this->actingAs($this->user)->putJson("/api/todos/{$todo->id}", [
         'priority' => null,
      ]);

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'priority' => null,
            ]
         ]);

      $todo->refresh();
      $this->assertNull($todo->priority);
   }

   /** @test */
   public function it_can_remove_due_date_from_todo()
   {
      $todo = Todo::factory()->for($this->user)->create([
         'due_date' => Carbon::tomorrow(),
      ]);

      $response = $this->actingAs($this->user)->putJson("/api/todos/{$todo->id}", [
         'due_date' => null,
      ]);

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'due_date' => null,
            ]
         ]);

      $todo->refresh();
      $this->assertNull($todo->due_date);
   }

   /** @test */
   public function it_includes_overdue_status_in_response()
   {
      $todo = Todo::factory()->for($this->user)->overdue()->create();

      $response = $this->actingAs($this->user)->getJson("/api/todos/{$todo->id}");

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'is_overdue' => true,
            ]
         ]);
   }

   /** @test */
   public function it_includes_due_today_status_in_response()
   {
      $todo = Todo::factory()->for($this->user)->dueToday()->create();

      $response = $this->actingAs($this->user)->getJson("/api/todos/{$todo->id}");

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'is_due_today' => true,
            ]
         ]);
   }

   /** @test */
   public function it_can_filter_todos_by_priority()
   {
      Todo::factory()->for($this->user)->highPriority()->create();
      Todo::factory()->for($this->user)->highPriority()->create();
      Todo::factory()->for($this->user)->mediumPriority()->create();

      $response = $this->actingAs($this->user)
         ->getJson('/api/todos?priority=' . Priority::HIGH->value);

      $response->assertStatus(200)
         ->assertJsonCount(2, 'data');
   }

   /** @test */
   public function it_can_filter_overdue_todos()
   {
      Todo::factory()->for($this->user)->overdue()->create();
      Todo::factory()->for($this->user)->overdue()->create();
      Todo::factory()->for($this->user)->dueSoon()->create();

      $response = $this->actingAs($this->user)->getJson('/api/todos?status=overdue');

      $response->assertStatus(200)
         ->assertJsonCount(2, 'data');
   }

   /** @test */
   public function it_can_filter_due_today_todos()
   {
      Todo::factory()->for($this->user)->dueToday()->create();
      Todo::factory()->for($this->user)->dueSoon()->create();

      $response = $this->actingAs($this->user)->getJson('/api/todos?status=due_today');

      $response->assertStatus(200)
         ->assertJsonCount(1, 'data');
   }

   /** @test */
   public function it_can_sort_todos_by_due_date()
   {
      $todo1 = Todo::factory()->for($this->user)->create([
         'title' => 'First',
         'due_date' => Carbon::today()->addDays(3),
      ]);

      $todo2 = Todo::factory()->for($this->user)->create([
         'title' => 'Second',
         'due_date' => Carbon::today()->addDays(1),
      ]);

      $todo3 = Todo::factory()->for($this->user)->create([
         'title' => 'Third',
         'due_date' => Carbon::today()->addDays(2),
      ]);

      $response = $this->actingAs($this->user)
         ->getJson('/api/todos?sort_by=due_date&sort_order=asc');

      $response->assertStatus(200);

      $data = $response->json('data');
      $this->assertEquals('Second', $data[0]['title']);
      $this->assertEquals('Third', $data[1]['title']);
      $this->assertEquals('First', $data[2]['title']);
   }

   /** @test */
   public function it_includes_priority_color_in_response()
   {
      $todo = Todo::factory()->for($this->user)->highPriority()->create();

      $response = $this->actingAs($this->user)->getJson("/api/todos/{$todo->id}");

      $response->assertStatus(200)
         ->assertJson([
            'data' => [
               'priority_color' => '#EF4444',
            ]
         ]);
   }
}
