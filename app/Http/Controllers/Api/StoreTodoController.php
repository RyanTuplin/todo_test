<?php


namespace App\Http\Controllers\Api;

use App\Actions\CreateTodoAction;
use App\DataTransferObjects\TodoData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Resources\TodoResource;

class StoreTodoController extends Controller
{

    public function __invoke(StoreTodoRequest $request, CreateTodoAction $action)
    {
        $todoData = TodoData::fromRequest($request->validated());

        $todo = $action->execute($todoData, $request->user());
        
        return (new TodoResource($todo))->response()->setStatusCode(201);
    }
}
