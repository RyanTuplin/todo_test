<?php

namespace App\Http\Controllers\Api;

use App\Enums\Priority;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use Illuminate\Http\Request;

class ListTodosController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->user()
            ->todos()
            ->with('categories');

        // Filter by priority
        if ($request->has('priority') && $request->priority !== null) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->has('status')) {
            match ($request->status) {
                'overdue' => $query->overdue(),
                'due_today' => $query->dueToday(),
                'due_soon' => $query->dueSoon(),
                default => null,
            };
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder);

        $todos = $query->get();

        return TodoResource::collection($todos);
    }
}
