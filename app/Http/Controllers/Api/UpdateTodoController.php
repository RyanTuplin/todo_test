<?php

namespace App\Http\Controllers\Api;

use App\Actions\UpdateTodoAction;
use App\DataTransferObjects\TodoData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;

class UpdateTodoController extends Controller
{
    public function __invoke(
        UpdateTodoRequest $request,
        Todo $todo,
        UpdateTodoAction $action
    ) {
        $data = array_merge([
            'title' => $todo->title,
            'description' => $todo->description,
            'completed' => $todo->completed,
        ], $request->validated());

        $todoData = TodoData::fromRequest($data);

        $todo = $action->execute($todo, $todoData);

        return new TodoResource($todo);
    }
}
