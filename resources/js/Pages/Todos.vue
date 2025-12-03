<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";
import type { Todo, TodoFormData } from "@/types/todo";

const todos = ref<Todo[]>([]);
const isLoading = ref(false);
const error = ref<string | null>(null);

const form = ref<TodoFormData>({
  title: "",
  description: "",
  completed: false,
});

const editingId = ref<number | null>(null);

const fetchTodos = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.get("/api/todos");
    todos.value = response.data.data;
  } catch (err) {
    error.value = "Failed to fetch todos";
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const createTodo = async () => {
  if (!form.value.title.trim()) return;

  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.post("/api/todos", {
      title: form.value.title,
      description: form.value.description,
      completed: form.value.completed || false, // Explicitly ensure it's false
    });
    todos.value.unshift(response.data.data);
    resetForm();
  } catch (err) {
    error.value = "Failed to create todo";
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const updateTodo = async (todo: Todo) => {
  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.put(`/api/todos/${todo.id}`, {
      title: todo.title,
      description: todo.description,
      completed: todo.completed,
    });

    const index = todos.value.findIndex((t) => t.id === todo.id);
    if (index !== -1) {
      todos.value[index] = response.data.data;
    }
  } catch (err) {
    error.value = "Failed to update todo";
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const toggleComplete = async (todo: Todo) => {
  const updated = { ...todo, completed: !todo.completed };
  await updateTodo(updated);
};

const deleteTodo = async (id: number) => {
  if (!confirm("Are you sure you want to delete this todo?")) return;

  isLoading.value = true;
  error.value = null;

  try {
    await axios.delete(`/api/todos/${id}`);
    todos.value = todos.value.filter((t) => t.id !== id);
  } catch (err) {
    error.value = "Failed to delete todo";
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const startEdit = (todo: Todo) => {
  editingId.value = todo.id;
  form.value = {
    title: todo.title,
    description: todo.description || "",
    completed: todo.completed,
  };
};

const saveEdit = async () => {
  if (editingId.value === null) return;

  const todo = todos.value.find((t) => t.id === editingId.value);
  if (!todo) return;

  await updateTodo({
    ...todo,
    ...form.value,
  });

  cancelEdit();
};

const cancelEdit = () => {
  editingId.value = null;
  resetForm();
};

const resetForm = () => {
  form.value = {
    title: "",
    description: "",
    completed: false,
  };
};

onMounted(() => {
  fetchTodos();
});
</script>

<template>
  <div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-3xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Todo List</h1>

      <!-- Error Message -->
      <div
        v-if="error"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
      >
        {{ error }}
      </div>

      <!-- Create/Edit Form -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingId ? "Edit Todo" : "Create New Todo" }}
        </h2>

        <form @submit.prevent="editingId ? saveEdit() : createTodo()">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2"> Title </label>
            <input
              v-model="form.title"
              type="text"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              placeholder="Enter todo title"
              required
            />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
              Description
            </label>
            <textarea
              v-model="form.description"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              placeholder="Enter description (optional)"
              rows="3"
            ></textarea>
          </div>

          <div class="flex items-center justify-between">
            <button
              type="submit"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
              :disabled="isLoading"
            >
              {{ editingId ? "Update" : "Create" }} Todo
            </button>

            <button
              v-if="editingId"
              type="button"
              @click="cancelEdit"
              class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>

      <!-- Todos List -->
      <div class="space-y-4">
        <div
          v-for="todo in todos"
          :key="todo.id"
          class="bg-white rounded-lg shadow-md p-6"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center mb-2">
                <input
                  type="checkbox"
                  :checked="todo.completed"
                  @change="toggleComplete(todo)"
                  class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                />
                <h3
                  class="ml-3 text-lg font-semibold"
                  :class="{ 'line-through text-gray-500': todo.completed }"
                >
                  {{ todo.title }}
                </h3>
              </div>

              <p
                v-if="todo.description"
                class="text-gray-600 ml-8"
                :class="{ 'line-through': todo.completed }"
              >
                {{ todo.description }}
              </p>
            </div>

            <div class="flex space-x-2 ml-4">
              <button @click="startEdit(todo)" class="text-blue-600 hover:text-blue-800">
                Edit
              </button>
              <button
                @click="deleteTodo(todo.id)"
                class="text-red-600 hover:text-red-800"
              >
                Delete
              </button>
            </div>
          </div>
        </div>

        <div
          v-if="todos.length === 0 && !isLoading"
          class="text-center text-gray-500 py-8"
        >
          No todos yet. Create your first one!
        </div>
      </div>
    </div>
  </div>
</template>
