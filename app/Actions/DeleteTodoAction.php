<?php

namespace App\Actions;

use App\Models\Todo;

class DeleteTodoAction
{
   public function execute(Todo $todo): void
   {
      $todo->delete();
   }
}
