<?php

namespace App\Actions;

use App\DataTransferObjects\TodoData;
use App\Models\Todo;

class CreateTodoAction
{
   public function execute(TodoData $data): Todo
   {
      return Todo::create($data->toArray());
   }
}
