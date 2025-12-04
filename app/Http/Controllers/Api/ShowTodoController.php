<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;

class ShowTodoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Todo $todo)
    {
        $this->authorize('view', $todo);
        return new TodoResource($todo);
    }
}
