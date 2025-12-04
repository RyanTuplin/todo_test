<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
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
}
