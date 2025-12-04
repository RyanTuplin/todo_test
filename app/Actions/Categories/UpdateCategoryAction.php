<?php

namespace App\Actions\Categories;

use App\DataTransferObjects\CategoryData;
use App\Models\Category;

class UpdateCategoryAction
{
   public function execute(Category $category, CategoryData $data): Category
   {
      $category->update($data->toArray());

      return $category->fresh();
   }
}
