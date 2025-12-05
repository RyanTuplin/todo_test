<?php

namespace Tests\Unit;

use App\DataTransferObjects\TodoData;
use PHPUnit\Framework\TestCase;

class TodoDataTest extends TestCase
{
   /** @test */
   public function it_can_be_created_from_request_data()
   {
      $data = [
         'title' => 'Test Todo',
         'description' => 'Test Description',
         'completed' => true,
      ];

      $todoData = TodoData::fromRequest($data);

      $this->assertEquals('Test Todo', $todoData->title);
      $this->assertEquals('Test Description', $todoData->description);
      $this->assertTrue($todoData->completed);
   }

   /** @test */
   public function it_handles_missing_description()
   {
      $data = [
         'title' => 'Test Todo',
      ];

      $todoData = TodoData::fromRequest($data);

      $this->assertEquals('Test Todo', $todoData->title);
      $this->assertNull($todoData->description);
      $this->assertFalse($todoData->completed);
   }

   /** @test */
   public function it_handles_missing_completed_field()
   {
      $data = [
         'title' => 'Test Todo',
         'description' => 'Test Description',
      ];

      $todoData = TodoData::fromRequest($data);

      $this->assertFalse($todoData->completed);
   }

   /** @test */
   public function it_can_be_converted_to_array()
   {
      $todoData = new TodoData(
         title: 'Test Todo',
         description: 'Test Description',
         completed: true,
         priority: null,
         due_date: null,
      );

      $array = $todoData->toArray();

      $this->assertEquals([
         'title' => 'Test Todo',
         'description' => 'Test Description',
         'completed' => true,
         'priority' => null,
         'due_date' => null,
      ], $array);
   }
}
