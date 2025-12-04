<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class ListCategoriesController extends Controller
{
    public function __invoke(Request $request)
    {
        $categories = $request->user()
            ->categories()
            ->withCount('todos')
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }
}
