<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\UpdateTodoAction;
use App\DataTransferObjects\TodoData;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;

class UpdateTodoController extends Controller
{
    /**
     * Handle the incoming request.
     */

    public function __invoke(UpdateTodoRequest $request, Todo $todo, UpdateTodoAction $action)
    {
        $todoData = TodoData::fromRequest($request->validated());
        $todo = $action->execute($todo, $todoData);
        return new TodoResource($todo);
    }
}
