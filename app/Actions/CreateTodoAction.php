<?php

namespace App\Actions;

use App\DataTransferObjects\TodoData;
use App\Models\Todo;
use App\Models\User;

class CreateTodoAction
{
   public function execute(TodoData $data, User $user): Todo
   {
      return $user->todos()->create($data->toArray());
   }
}
