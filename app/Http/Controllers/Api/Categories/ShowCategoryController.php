<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class ShowCategoryController extends Controller
{
    public function __invoke(Request $request, Category $category)
    {
        $this->authorize('view', $category);

        $category->loadCount('todos');

        return new CategoryResource($category);
    }
}
