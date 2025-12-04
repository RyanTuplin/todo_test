<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import type { Todo, TodoFormData, Category } from "@/types/todo";

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const todos = ref<Todo[]>([]);
const categories = ref<Category[]>([]);
const isLoading = ref(false);
const error = ref<string | null>(null);
const selectedCategoryFilter = ref<number | null>(null);

const form = ref<TodoFormData>({
  title: "",
  description: "",
  completed: false,
});

const editingId = ref<number | null>(null);

const logout = () => {
  router.post("/logout");
};

const filteredTodos = computed(() => {
  if (selectedCategoryFilter.value === null) {
    return todos.value;
  }

  return todos.value.filter((todo) =>
    todo.categories?.some((cat) => cat.id === selectedCategoryFilter.value)
  );
});

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

const fetchCategories = async () => {
  try {
    const response = await axios.get("/api/categories");
    categories.value = response.data.data;
  } catch (err) {
    console.error("Failed to fetch categories", err);
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
      completed: form.value.completed || false,
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

const attachCategory = async (todoId: number, categoryId: number, event: Event) => {
  try {
    const response = await axios.post(`/api/todos/${todoId}/categories/${categoryId}`);
    const index = todos.value.findIndex((t) => t.id === todoId);
    if (index !== -1) {
      todos.value[index] = response.data.data;
    }

    // Close the dropdown by finding the parent details element and closing it
    const detailsElement = (event.target as HTMLElement).closest("details");
    if (detailsElement) {
      detailsElement.removeAttribute("open");
    }
  } catch (err) {
    error.value = "Failed to add category";
    console.error(err);
  }
};

const detachCategory = async (todoId: number, categoryId: number) => {
  try {
    const response = await axios.delete(`/api/todos/${todoId}/categories/${categoryId}`);
    const index = todos.value.findIndex((t) => t.id === todoId);
    if (index !== -1) {
      todos.value[index] = response.data.data;
    }
  } catch (err) {
    error.value = "Failed to remove category";
    console.error(err);
  }
};

const hasCategoryAttached = (todo: Todo, categoryId: number): boolean => {
  return todo.categories?.some((cat) => cat.id === categoryId) || false;
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
  fetchCategories();
});
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-900">My Todo App</h1>
          </div>
          <div class="flex items-center space-x-4">
            <a href="/todos" class="text-blue-600 font-medium"> Todos </a>
            <a
              href="/categories"
              class="text-gray-700 hover:text-gray-900 font-medium transition"
            >
              Categories
            </a>
            <a
              href="/profile"
              class="text-gray-700 hover:text-gray-900 font-medium transition"
            >
              Profile
            </a>
            <button
              @click="logout"
              class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded transition"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="py-8 px-4">
      <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">My Todos</h2>

        <!-- Category Filter -->
        <div v-if="categories.length > 0" class="mb-6">
          <div class="flex flex-wrap gap-2">
            <button
              @click="selectedCategoryFilter = null"
              class="px-4 py-2 rounded-full font-medium transition"
              :class="
                selectedCategoryFilter === null
                  ? 'bg-blue-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-100'
              "
            >
              All Todos
            </button>
            <button
              v-for="category in categories"
              :key="category.id"
              @click="selectedCategoryFilter = category.id"
              class="px-4 py-2 rounded-full font-medium transition flex items-center space-x-2"
              :class="
                selectedCategoryFilter === category.id
                  ? 'text-white ring-2 ring-offset-2'
                  : 'bg-white hover:opacity-80'
              "
              :style="
                selectedCategoryFilter === category.id
                  ? { backgroundColor: category.color, borderColor: category.color }
                  : { backgroundColor: category.color + '20', color: category.color }
              "
            >
              <span
                class="w-3 h-3 rounded-full"
                :style="{ backgroundColor: category.color }"
              ></span>
              <span>{{ category.name }}</span>
            </button>
          </div>
        </div>

        <!-- Error Message -->
        <div
          v-if="error"
          class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
        >
          {{ error }}
        </div>

        <!-- Create/Edit Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h3 class="text-xl font-semibold mb-4">
            {{ editingId ? "Edit Todo" : "Create New Todo" }}
          </h3>

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
            v-for="todo in filteredTodos"
            :key="todo.id"
            class="bg-white rounded-lg shadow-md p-6"
          >
            <div class="flex items-start justify-between mb-3">
              <div class="flex-1">
                <div class="flex items-center mb-2">
                  <input
                    type="checkbox"
                    :checked="todo.completed"
                    @change="toggleComplete(todo)"
                    class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                  />
                  <h4
                    class="ml-3 text-lg font-semibold"
                    :class="{ 'line-through text-gray-500': todo.completed }"
                  >
                    {{ todo.title }}
                  </h4>
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
                <button
                  @click="startEdit(todo)"
                  class="text-blue-600 hover:text-blue-800"
                >
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

            <!-- Categories -->
            <div v-if="categories.length > 0" class="ml-8 mt-3">
              <div class="flex flex-wrap gap-2 mb-2">
                <span
                  v-for="category in todo.categories"
                  :key="category.id"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white"
                  :style="{ backgroundColor: category.color }"
                >
                  {{ category.name }}
                  <button
                    @click="detachCategory(todo.id, category.id)"
                    class="ml-2 hover:bg-black hover:bg-opacity-20 rounded-full p-0.5"
                  >
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      ></path>
                    </svg>
                  </button>
                </span>
              </div>

              <!-- Add Category Dropdown -->
              <details class="relative">
                <summary
                  class="text-sm text-blue-600 hover:text-blue-800 cursor-pointer list-none"
                >
                  + Add Category
                </summary>
                <div
                  class="absolute z-10 mt-2 bg-white rounded-lg shadow-lg border border-gray-200 p-2 min-w-[200px]"
                >
                  <button
                    v-for="category in categories.filter(
                      (c) => !hasCategoryAttached(todo, c.id)
                    )"
                    :key="category.id"
                    @click="attachCategory(todo.id, category.id, $event)"
                    class="w-full text-left px-3 py-2 rounded hover:bg-gray-100 flex items-center space-x-2"
                  >
                    <span
                      class="w-4 h-4 rounded-full"
                      :style="{ backgroundColor: category.color }"
                    ></span>
                    <span>{{ category.name }}</span>
                  </button>
                  <div
                    v-if="
                      categories.filter((c) => !hasCategoryAttached(todo, c.id))
                        .length === 0
                    "
                    class="text-sm text-gray-500 px-3 py-2"
                  >
                    All categories added
                  </div>
                </div>
              </details>
            </div>
          </div>

          <div
            v-if="filteredTodos.length === 0 && !isLoading"
            class="text-center text-gray-500 py-8"
          >
            <p v-if="selectedCategoryFilter">No todos in this category.</p>
            <p v-else>No todos yet. Create your first one!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
