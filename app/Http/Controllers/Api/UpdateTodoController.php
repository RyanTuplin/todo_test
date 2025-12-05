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
        $this->authorize('update', $todo);

        // Start with existing todo data
        $data = [
            'title' => $todo->title,
            'description' => $todo->description,
            'completed' => $todo->completed,
            'priority' => $todo->priority?->value,
            'due_date' => $todo->due_date?->format('Y-m-d'),
        ];

        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            $data[$key] = $value;
        }

        $todoData = TodoData::fromRequest($data);

        $todo = $action->execute($todo, $todoData);

        return new TodoResource($todo);
    }
}
