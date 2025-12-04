<?php

namespace App\Actions\Categories;

use App\Models\Category;
use App\Models\Todo;

class AttachCategoryToTodoAction
{
   public function execute(Category $category, Todo $todo): void
   {

      if (!$todo->categories->contains($category->id)) {
         $todo->categories()->attach($category->id);
      }
   }
}
