<?php

namespace App\Actions\Todos;

use App\Models\Todo;
use Carbon\Carbon;

class SetTodoDueDateAction
{
   public function execute(Todo $todo, ?Carbon $dueDate): Todo
   {
      $todo->update(['due_date' => $dueDate]);

      return $todo->fresh();
   }
}
