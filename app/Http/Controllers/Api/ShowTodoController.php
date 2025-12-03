<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use App\Models\Todo;

class ShowTodoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Todo $todo)
    {
        return new TodoResource($todo);
    }
}
