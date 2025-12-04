<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use Illuminate\Http\Request;
use App\Models\Todo;

class ListTodosController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $todos = $request->user()->todos()->orderBy('created_at', 'desc')->get();

        return TodoResource::collection($todos);
    }
}
