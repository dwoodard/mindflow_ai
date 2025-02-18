<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // List all tasks
    public function index(Request $request)
    {
        $tasks = Task::query();

        // Optional filtering by status (e.g., completed, pending)
        if ($request->has('status')) {
            $tasks->where('status', $request->get('status'));
        }

        // Paginate the results
        $paginatedTasks = $tasks->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'data' => $paginatedTasks->items(),
            'links' => [
                'first' => $paginatedTasks->url(1),
                'last' => $paginatedTasks->url($paginatedTasks->lastPage()),
                'prev' => $paginatedTasks->previousPageUrl(),
                'next' => $paginatedTasks->nextPageUrl(),
            ],
            'meta' => [

            ],
        ]);
    }

    // Store a new task
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|in:pending,completed',
        ]);

        $task = Task::create($validatedData);

        return response()->json($task, 201);
    }

    // Show a specific task
    public function show(Task $task)
    {
        return response()->json($task);
    }

    // Update a task
    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return response()->json($task, 200);
    }

    // Delete a specific task
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }
}
