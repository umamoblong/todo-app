<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="max-w-2xl mx-auto p-6" id="app">
        <h1 class="text-3xl font-bold text-center mb-6">📋 Todo List</h1>

        <!-- Form Tambah Todo -->
        <div class="flex gap-2 mb-6">
            <input type="text" 
                   id="todo-input"
                   placeholder="Tambahkan todo..."
                   class="flex-1 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button onclick="addTodo()" 
                    class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition">
                Tambah
            </button>
        </div>

        <!-- List Todo -->
        <div id="todo-list" class="space-y-3"></div>

        <!-- Total -->
        <div class="mt-6 text-gray-600 text-center" id="todo-count">Total: 0 todo</div>
    </div>

    <script>
        // ==============================================
        // API CONFIG
        // ==============================================
        const API_URL = '/api/v1/todos';
        let todos = [];

        // ==============================================
        // GET TODOS DARI API
        // ==============================================
        async function fetchTodos() {
            try {
                const response = await fetch(API_URL);
                if (!response.ok) {
                    throw new Error('Gagal mengambil data');
                }
                todos = await response.json();
                renderTodos();
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mengambil data todo!');
            }
        }

        // ==============================================
        // POST TODO KE API
        // ==============================================
        async function addTodo() {
            const input = document.getElementById('todo-input');
            const title = input.value.trim();

            if (title === '') {
                alert('Todo tidak boleh kosong!');
                return;
            }

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title })
                });

                if (!response.ok) {
                    throw new Error('Gagal menambah todo');
                }

                const newTodo = await response.json();
                todos.push(newTodo);
                input.value = '';
                renderTodos();

            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menambah todo!');
            }
        }

        // ==============================================
        // TOGGLE DONE TODO
        // ==============================================
        async function toggleTodo(id) {
            try {
                const response = await fetch(`${API_URL}/${id}/toggle`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal update todo');
                }

                const updatedTodo = await response.json();
                const index = todos.findIndex(t => t.id === id);
                todos[index] = updatedTodo;
                renderTodos();

            } catch (error) {
                console.error('Error:', error);
                alert('Gagal update todo!');
            }
        }

        // ==============================================
        // DELETE TODO
        // ==============================================
        async function deleteTodo(id) {
            if (!confirm('Yakin mau hapus todo ini?')) return;

            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal hapus todo');
                }

                todos = todos.filter(t => t.id !== id);
                renderTodos();

            } catch (error) {
                console.error('Error:', error);
                alert('Gagal hapus todo!');
            }
        }

        // ==============================================
        // RENDER TODOS
        // ==============================================
        function renderTodos() {
            const list = document.getElementById('todo-list');
            const count = document.getElementById('todo-count');

            if (todos.length === 0) {
                list.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        Belum ada todo. Yuk tambahkan!
                    </div>
                `;
            } else {
                list.innerHTML = todos.map(todo => `
                    <div class="bg-white p-4 rounded-lg shadow flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" 
                                   ${todo.done ? 'checked' : ''}
                                   onchange="toggleTodo(${todo.id})"
                                   class="w-5 h-5">
                            <span class="${todo.done ? 'line-through text-gray-400' : ''}">
                                ${todo.title}
                            </span>
                        </div>
                        <button onclick="deleteTodo(${todo.id})" 
                                class="text-red-500 hover:text-red-700">
                            🗑️
                        </button>
                    </div>
                `).join('');
            }

            count.textContent = `Total: ${todos.length} todo`;
        }

        // ==============================================
        // LOAD TODOS SAAT HALAMAN DIBUKA
        // ==============================================
        document.addEventListener('DOMContentLoaded', fetchTodos);
    </script>

</body>
</html>