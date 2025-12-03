<?php

namespace App\Actions;

use App\DataTransferObjects\TodoData;
use App\Models\Todo;

class UpdateTodoAction
{
   public function execute(Todo $todo, TodoData $data): Todo
   {
      $todo->update($data->toArray());

      return $todo->fresh();
   }
}
