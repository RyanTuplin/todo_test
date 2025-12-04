<?php

namespace App\Actions;

use App\DataTransferObjects\TodoData;
use App\Models\Todo;
use Illuminate\Support\Facades\Log;

class UpdateTodoAction
{
   public function execute(Todo $todo, TodoData $data): Todo
   {
      $updateData = $data->toArray();

      Log::info('About to update todo', [
         'todo_id' => $todo->id,
         'update_data' => $updateData,
         'current_completed' => $todo->completed,
      ]);

      $todo->update($updateData);

      Log::info('After update', [
         'completed_from_model' => $todo->completed,
         'completed_from_attributes' => $todo->getAttributes()['completed'],
      ]);

      $fresh = $todo->fresh();

      Log::info('After fresh', [
         'completed_from_fresh' => $fresh->completed,
         'completed_from_fresh_attributes' => $fresh->getAttributes()['completed'],
      ]);

      return $fresh;
   }
}
