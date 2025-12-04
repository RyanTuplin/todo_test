<?php

namespace App\Http\Controllers\Api\Categories;

use App\Actions\Categories\AttachCategoryToTodoAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use App\Models\Category;
use App\Models\Todo;

class AttachCategoryController extends Controller
{
    public function __invoke(
        Todo $todo,
        Category $category,
        AttachCategoryToTodoAction $action
    ) {
        $this->authorize('update', $todo);
        $this->authorize('view', $category);

        $action->execute($category, $todo);

        $todo->load('categories');

        return new TodoResource($todo);
    }
}
