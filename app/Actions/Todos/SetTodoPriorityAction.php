<?php

namespace App\Actions\Todos;

use App\Enums\Priority;
use App\Models\Todo;

class SetTodoPriorityAction
{
   public function execute(Todo $todo, ?Priority $priority): Todo
   {
      $todo->update(['priority' => $priority?->value]);

      return $todo->fresh();
   }
}
