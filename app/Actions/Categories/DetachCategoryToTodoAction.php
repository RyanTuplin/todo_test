<?php

namespace App\Actions\Categories;

use App\Models\Category;
use App\Models\Todo;

class DetachCategoryToTodoAction
{
   public function execute(Category $category, Todo $todo): void
   {
      $todo->categories()->detach($category->id);
   }
}
