<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['user', 'tags'])->latest()->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::all();
        $tags = Tag::all();
        return view('tasks.create', compact('users', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'priority' => 'required|in:baja,media,alta',
            'user_id' => 'nullable|exists:users,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'priority' => $request->priority,
            'completed' => false,
            'user_id' => $request->user_id,
        ]);

        $task->tags()->attach($request->tags);

        return redirect()->route('tasks.index')->with('success', 'Tarea creada exitosamente.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $users = User::all();
        $tags = Tag::all();
        return view('tasks.edit', compact('task', 'users', 'tags'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|max:255',
            'priority' => 'required|in:baja,media,alta',
            'completed' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task->update([
            'title' => $request->title,
            'priority' => $request->priority,
            'completed' => $request->completed,
            'user_id' => $request->user_id,
        ]);

        $task->tags()->sync($request->tags);

        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tare eliminada exitosamente.');
    }

    public function complete(Task $task)
    {
        $task->update(['completed' => true]);
        return redirect()->route('tasks.index')->with('success', 'Tarea marcada como completada.');
    }
}
