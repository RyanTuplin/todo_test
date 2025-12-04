<?php

namespace App\Http\Controllers\Api\Categories;

use App\Actions\Categories\CreateCategoryAction;
use App\DataTransferObjects\CategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;

class StoreCategoryController extends Controller
{
    public function __invoke(
        StoreCategoryRequest $request,
        CreateCategoryAction $action
    ) {
        $categoryData = CategoryData::fromRequest($request->validated());

        $category = $action->execute($request->user(), $categoryData);

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }
}
