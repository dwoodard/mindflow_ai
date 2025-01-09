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

        return response()->json($paginatedTasks);
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

    // Delete a specific task
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }
}
