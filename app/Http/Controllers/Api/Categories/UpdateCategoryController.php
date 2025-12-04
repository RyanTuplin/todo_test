<?php

namespace App\Http\Controllers\Api\Categories;

use App\Actions\Categories\UpdateCategoryAction;
use App\DataTransferObjects\CategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class UpdateCategoryController extends Controller
{
    public function __invoke(
        UpdateCategoryRequest $request,
        Category $category,
        UpdateCategoryAction $action
    ) {
        $this->authorize('update', $category);

        $data = [
            'name' => $category->name,
            'color' => $category->color,
        ];

        foreach ($request->validated() as $key => $value) {
            $data[$key] = $value;
        }

        $categoryData = CategoryData::fromRequest($data);

        $category = $action->execute($category, $categoryData);

        return new CategoryResource($category);
    }
}
