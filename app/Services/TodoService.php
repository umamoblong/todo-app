<?php

namespace App\Services;

use App\Models\Todo;

class TodoService
{
    // ✅ GET ALL TODOS
    public function getAll()
    {
        return Todo::all();
    }
    
    // ✅ CREATE TODO
    public function create(array $data)
    {
        return Todo::create([
            'title' => $data['title'],
            'done' => false,
        ]);
    }
    
    // ✅ TOGGLE DONE (UPDATE)
    public function toggleDone($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->done = !$todo->done;
        $todo->save();
        return $todo;
    }
    
    // ✅ DELETE TODO
    public function delete($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();
        return true;
    }
}