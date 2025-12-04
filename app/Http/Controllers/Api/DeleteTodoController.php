<?php

namespace App\Http\Controllers\Api;

use App\Actions\DeleteTodoAction;
use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Response;

class DeleteTodoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Todo $todo, DeleteTodoAction $action): Response
    {
        $this->authorize('delete', $todo);

        $action->execute($todo);

        return response()->noContent();
    }
}
