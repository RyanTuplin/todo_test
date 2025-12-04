<?php

namespace App\Actions;

use App\DataTransferObjects\TodoData;
use App\Models\Todo;

class CreateTodoAction
{
   public function execute(TodoData $data): Todo
   {
      // For create, ensure we have required fields
      return Todo::create([
         'title' => $data->title ?? '',
         'description' => $data->description,
         'completed' => $data->completed ?? false,
      ]);
   }
}
