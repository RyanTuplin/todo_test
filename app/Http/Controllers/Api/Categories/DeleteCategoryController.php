<?php

namespace App\Http\Controllers\Api\Categories;

use App\Actions\Categories\DeleteCategoryAction;
use App\Http\Controllers\Controller;
use App\Models\Category;

class DeleteCategoryController extends Controller
{
    public function __invoke(Category $category, DeleteCategoryAction $action)
    {
        $this->authorize('delete', $category);

        $action->execute($category);

        return response()->noContent();
    }
}
