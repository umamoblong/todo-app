<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                console.log('📡 Mengambil data dari API...');
                const response = await fetch(API_URL);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('📦 Data dari API:', data);
                
                // ✅ Pastikan data adalah array
                if (Array.isArray(data)) {
                    todos = data;
                } else {
                    todos = [];
                }
                
                renderTodos();
                
            } catch (error) {
                console.error('❌ Error fetchTodos:', error);
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
                console.log('📤 Menambah todo:', title);
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const newTodo = await response.json();
                console.log('✅ Todo berhasil ditambah:', newTodo);
                
                todos.push(newTodo);
                input.value = '';
                renderTodos();

            } catch (error) {
                console.error('❌ Error addTodo:', error);
                alert('Gagal menambah todo!');
            }
        }

        // ==============================================
        // TOGGLE DONE TODO
        // ==============================================
        async function toggleTodo(id) {
            try {
                console.log('🔄 Toggle todo:', id);
                const response = await fetch(`${API_URL}/${id}/toggle`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const updatedTodo = await response.json();
                console.log('✅ Todo berhasil di-update:', updatedTodo);
                
                const index = todos.findIndex(t => t.id === id);
                if (index !== -1) {
                    todos[index] = updatedTodo;
                    renderTodos();
                }

            } catch (error) {
                console.error('❌ Error toggleTodo:', error);
                alert('Gagal update todo!');
            }
        }

        // ==============================================
        // DELETE TODO
        // ==============================================
        async function deleteTodo(id) {
            if (!confirm('Yakin mau hapus todo ini?')) return;

            try {
                console.log('🗑️ Hapus todo:', id);
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                console.log('✅ Todo berhasil dihapus');
                todos = todos.filter(t => t.id !== id);
                renderTodos();

            } catch (error) {
                console.error('❌ Error deleteTodo:', error);
                alert('Gagal hapus todo!');
            }
        }

        // ==============================================
        // RENDER TODOS
        // ==============================================
        function renderTodos() {
            const list = document.getElementById('todo-list');
            const count = document.getElementById('todo-count');

            if (!list || !count) {
                console.error('❌ Elemen HTML tidak ditemukan!');
                return;
            }

            console.log('🎨 Render todos:', todos.length);

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
        // AMBIL DATA SAAT HALAMAN DIMUAT
        // ==============================================
        fetchTodos();
    });
</script>