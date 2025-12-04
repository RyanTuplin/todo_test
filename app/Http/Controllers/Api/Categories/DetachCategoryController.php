<?php

namespace App\Http\Controllers\Api\Categories;

use App\Actions\Categories\DetachCategoryToTodoAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use App\Models\Category;
use App\Models\Todo;

class DetachCategoryController extends Controller
{
    public function __invoke(
        Todo $todo,
        Category $category,
        DetachCategoryToTodoAction $action
    ) {
        $this->authorize('update', $todo);

        $action->execute($category, $todo);

        $todo->load('categories');

        return new TodoResource($todo);
    }
}
