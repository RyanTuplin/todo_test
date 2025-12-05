<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import type { Todo, TodoFormData, Category, Priority } from "@/types/todo";

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const todos = ref<Todo[]>([]);
const categories = ref<Category[]>([]);
const isLoading = ref(false);
const error = ref<string | null>(null);
const selectedCategoryFilter = ref<number | null>(null);
const selectedPriorityFilter = ref<Priority | null>(null);
const selectedStatusFilter = ref<string | null>(null);

const form = ref<TodoFormData>({
  title: "",
  description: "",
  completed: false,
  priority: null,
  due_date: null,
});

const editingId = ref<number | null>(null);

const logout = () => {
  router.post("/logout");
};

const filteredTodos = computed(() => {
  let filtered = todos.value;

  // Filter by category
  if (selectedCategoryFilter.value !== null) {
    filtered = filtered.filter((todo) =>
      todo.categories?.some((cat) => cat.id === selectedCategoryFilter.value)
    );
  }

  // Filter by priority
  if (selectedPriorityFilter.value !== null) {
    filtered = filtered.filter((todo) => todo.priority === selectedPriorityFilter.value);
  }

  // Filter by status
  if (selectedStatusFilter.value) {
    if (selectedStatusFilter.value === "overdue") {
      filtered = filtered.filter((todo) => todo.is_overdue);
    } else if (selectedStatusFilter.value === "due_today") {
      filtered = filtered.filter((todo) => todo.is_due_today);
    }
  }

  return filtered;
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
      priority: form.value.priority,
      due_date: form.value.due_date,
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
      priority: todo.priority,
      due_date: todo.due_date,
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
    priority: todo.priority,
    due_date: todo.due_date,
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
    priority: null,
    due_date: null,
  };
};

const clearFilters = () => {
  selectedCategoryFilter.value = null;
  selectedPriorityFilter.value = null;
  selectedStatusFilter.value = null;
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

        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
            <button
              v-if="
                selectedCategoryFilter || selectedPriorityFilter || selectedStatusFilter
              "
              @click="clearFilters"
              class="text-sm text-blue-600 hover:text-blue-800"
            >
              Clear All
            </button>
          </div>

          <!-- Status Filter -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="flex flex-wrap gap-2">
              <button
                @click="selectedStatusFilter = null"
                class="px-4 py-2 rounded-full font-medium text-sm transition"
                :class="
                  selectedStatusFilter === null
                    ? 'bg-blue-600 text-white'
                    : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                "
              >
                All
              </button>
              <button
                @click="selectedStatusFilter = 'overdue'"
                class="px-4 py-2 rounded-full font-medium text-sm transition"
                :class="
                  selectedStatusFilter === 'overdue'
                    ? 'bg-red-600 text-white'
                    : 'bg-white border border-red-300 text-red-600 hover:bg-red-50'
                "
              >
                Overdue
              </button>
              <button
                @click="selectedStatusFilter = 'due_today'"
                class="px-4 py-2 rounded-full font-medium text-sm transition"
                :class="
                  selectedStatusFilter === 'due_today'
                    ? 'bg-orange-600 text-white'
                    : 'bg-white border border-orange-300 text-orange-600 hover:bg-orange-50'
                "
              >
                Due Today
              </button>
            </div>
          </div>

          <!-- Priority Filter -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
            <div class="flex flex-wrap gap-2">
              <button
                @click="selectedPriorityFilter = null"
                class="px-4 py-2 rounded-full font-medium text-sm transition"
                :class="
                  selectedPriorityFilter === null
                    ? 'bg-blue-600 text-white'
                    : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                "
              >
                All Priorities
              </button>
              <button
                @click="selectedPriorityFilter = 'high'"
                class="px-4 py-2 rounded-full font-medium text-sm transition"
                :class="
                  selectedPriorityFilter === 'high'
                    ? 'bg-red-500 text-white'
                    : 'bg-white border border-red-300 text-red-600 hover:bg-red-50'
                "
              >
                High
              </button>
              <button
                @click="selectedPriorityFilter = 'medium'"
                class="px-4 py-2 rounded-full font-medium text-sm transition"
                :class="
                  selectedPriorityFilter === 'medium'
                    ? 'bg-orange-500 text-white'
                    : 'bg-white border border-orange-300 text-orange-600 hover:bg-orange-50'
                "
              >
                Medium
              </button>
              <button
                @click="selectedPriorityFilter = 'low'"
                class="px-4 py-2 rounded-full font-medium text-sm transition"
                :class="
                  selectedPriorityFilter === 'low'
                    ? 'bg-green-500 text-white'
                    : 'bg-white border border-green-300 text-green-600 hover:bg-green-50'
                "
              >
                Low
              </button>
            </div>
          </div>

          <!-- Category Filter -->
          <div v-if="categories.length > 0">
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <div class="flex flex-wrap gap-2">
              <button
                @click="selectedCategoryFilter = null"
                class="px-4 py-2 rounded-full font-medium text-sm transition"
                :class="
                  selectedCategoryFilter === null
                    ? 'bg-blue-600 text-white'
                    : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                "
              >
                All Categories
              </button>
              <button
                v-for="category in categories"
                :key="category.id"
                @click="selectedCategoryFilter = category.id"
                class="px-4 py-2 rounded-full font-medium text-sm transition flex items-center space-x-2"
                :class="
                  selectedCategoryFilter === category.id
                    ? 'text-white ring-2 ring-offset-2'
                    : 'bg-white hover:opacity-80'
                "
                :style="
                  selectedCategoryFilter === category.id
                    ? { backgroundColor: category.color, borderColor: category.color }
                    : {
                        backgroundColor: category.color + '20',
                        color: category.color,
                        border: `1px solid ${category.color}`,
                      }
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

            <!-- Priority Selection -->
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2"> Priority </label>
              <div class="flex space-x-2">
                <button
                  type="button"
                  @click="form.priority = null"
                  class="flex-1 py-2 px-4 rounded font-medium transition"
                  :class="
                    form.priority === null
                      ? 'bg-gray-200 text-gray-800'
                      : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50'
                  "
                >
                  None
                </button>
                <button
                  type="button"
                  @click="form.priority = 'high'"
                  class="flex-1 py-2 px-4 rounded font-medium transition"
                  :class="
                    form.priority === 'high'
                      ? 'bg-red-500 text-white'
                      : 'bg-white border border-red-300 text-red-600 hover:bg-red-50'
                  "
                >
                  High
                </button>
                <button
                  type="button"
                  @click="form.priority = 'medium'"
                  class="flex-1 py-2 px-4 rounded font-medium transition"
                  :class="
                    form.priority === 'medium'
                      ? 'bg-orange-500 text-white'
                      : 'bg-white border border-orange-300 text-orange-600 hover:bg-orange-50'
                  "
                >
                  Medium
                </button>
                <button
                  type="button"
                  @click="form.priority = 'low'"
                  class="flex-1 py-2 px-4 rounded font-medium transition"
                  :class="
                    form.priority === 'low'
                      ? 'bg-green-500 text-white'
                      : 'bg-white border border-green-300 text-green-600 hover:bg-green-50'
                  "
                >
                  Low
                </button>
              </div>
            </div>

            <!-- Due Date -->
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2"> Due Date </label>
              <input
                v-model="form.due_date"
                type="date"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
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
            :class="{
              'border-l-4 border-red-500': todo.is_overdue,
              'border-l-4 border-orange-500': todo.is_due_today && !todo.is_overdue,
            }"
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

                  <!-- Priority Badge -->
                  <span
                    v-if="todo.priority"
                    class="ml-3 px-3 py-1 rounded-full text-xs font-bold text-white"
                    :style="{ backgroundColor: todo.priority_color }"
                  >
                    {{ todo.priority_label }}
                  </span>

                  <!-- Overdue Badge -->
                  <span
                    v-if="todo.is_overdue"
                    class="ml-2 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800"
                  >
                    OVERDUE
                  </span>

                  <!-- Due Today Badge -->
                  <span
                    v-else-if="todo.is_due_today"
                    class="ml-2 px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800"
                  >
                    DUE TODAY
                  </span>
                </div>

                <!-- Due Date Display -->
                <div v-if="todo.due_date" class="ml-8 text-sm text-gray-600 mb-2">
                  <span class="font-medium">Due:</span> {{ todo.due_date_formatted }}
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
            <p
              v-if="
                selectedCategoryFilter || selectedPriorityFilter || selectedStatusFilter
              "
            >
              No todos match your filters.
            </p>
            <p v-else>No todos yet. Create your first one!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
