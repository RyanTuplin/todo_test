<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use Illuminate\Http\Request;

class ListTodosController extends Controller
{
    public function __invoke(Request $request)
    {
        $todos = $request->user()
            ->todos()
            ->with('categories')
            ->orderBy('created_at', 'desc')
            ->get();

        return TodoResource::collection($todos);
    }
}
