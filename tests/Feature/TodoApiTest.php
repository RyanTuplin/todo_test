<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_can_list_all_todos()
   {
      Todo::factory()->count(3)->create();

      $response = $this->getJson('/api/todos');

      $response->assertStatus(200)
         ->assertJsonCount(3, 'data')
         ->assertJsonStructure([
            'data' => [
               '*' => [
                  'id',
                  'title',
                  'description',
                  'completed',
                  'created_at',
                  'updated_at',
               ]
            ]
         ]);
   }

   /** @test */
   public function it_returns_empty_array_when_no_todos_exist()
   {
      $response = $this->getJson('/api/todos');

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

      $response = $this->postJson('/api/todos', $data);

      $response->assertStatus(201)
         ->assertJsonStructure([
            'data' => [
               'id',
               'title',
               'description',
               'completed',
               'created_at',
               'updated_at',
            ]
         ])
         ->assertJson([
            'data' => [
               'title' => 'New Todo',
               'description' => 'Todo Description',
               'completed' => false,
            ]
         ]);

      $this->assertDatabaseHas('todos', [
         'title' => 'New Todo',
         'description' => 'Todo Description',
      ]);
   }

   /** @test */
   public function it_can_create_a_todo_without_description()
   {
      $data = [
         'title' => 'New Todo',
      ];

      $response = $this->postJson('/api/todos', $data);

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
      $response = $this->postJson('/api/todos', [
         'description' => 'Description only',
      ]);

      $response->assertStatus(422)
         ->assertJsonValidationErrors(['title']);
   }

   /** @test */
   public function it_validates_title_max_length()
   {
      $response = $this->postJson('/api/todos', [
         'title' => str_repeat('a', 256), // 256 characters, exceeds 255 max
      ]);

      $response->assertStatus(422)
         ->assertJsonValidationErrors(['title']);
   }

   /** @test */
   public function it_can_show_a_single_todo()
   {
      $todo = Todo::factory()->create([
         'title' => 'Test Todo',
         'description' => 'Test Description',
      ]);

      $response = $this->getJson("/api/todos/{$todo->id}");

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
   public function it_returns_404_for_non_existent_todo()
   {
      $response = $this->getJson('/api/todos/999');

      $response->assertStatus(404);
   }

   /** @test */
   public function it_can_update_a_todo()
   {
      $todo = Todo::factory()->create([
         'title' => 'Original Title',
         'completed' => false,
      ]);

      $response = $this->putJson("/api/todos/{$todo->id}", [
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
         'completed' => true,
      ]);
   }

   /** @test */
   public function it_can_partially_update_a_todo()
   {
      $todo = Todo::factory()->create([
         'title' => 'Original Title',
         'description' => 'Original Description',
         'completed' => false,
      ]);

      $response = $this->putJson("/api/todos/{$todo->id}", [
         'completed' => true,
      ]);

      $response->assertStatus(200);

      $this->assertDatabaseHas('todos', [
         'id' => $todo->id,
         'title' => 'Original Title', // Should remain unchanged
         'completed' => true,
      ]);
   }

   /** @test */
   public function it_can_delete_a_todo()
   {
      $todo = Todo::factory()->create();

      $response = $this->deleteJson("/api/todos/{$todo->id}");

      $response->assertStatus(204);

      $this->assertDatabaseMissing('todos', [
         'id' => $todo->id,
      ]);
   }

   /** @test */
   public function it_returns_404_when_deleting_non_existent_todo()
   {
      $response = $this->deleteJson('/api/todos/999');

      $response->assertStatus(404);
   }
}
