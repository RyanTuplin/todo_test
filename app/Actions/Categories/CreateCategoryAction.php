<?php

namespace App\Actions\Categories;

use App\DataTransferObjects\CategoryData;
use App\Models\Category;
use App\Models\User;

class CreateCategoryAction
{
   public function execute(User $user, CategoryData $categoryData): Category
   {
      return $user->categories()->create($categoryData->toArray());
   }
}
