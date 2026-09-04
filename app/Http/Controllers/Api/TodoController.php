<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TodoService;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    protected $todoService;
    
    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }
    
    // ✅ GET /api/todos
    public function index()
    {
        $todos = $this->todoService->getAll();
        return response()->json($todos);
    }
    
    // ✅ POST /api/todos
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $todo = $this->todoService->create($request->all());
        return response()->json($todo, 201);
    }
    
    // ✅ PUT /api/todos/{id}/toggle
    public function toggle($id)
    {
        $todo = $this->todoService->toggleDone($id);
        return response()->json($todo);
    }
    
    // ✅ DELETE /api/todos/{id}
    public function destroy($id)
    {
        $this->todoService->delete($id);
        return response()->json(['message' => 'Todo deleted']);
    }
}