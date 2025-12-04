<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import type { Category, CategoryFormData } from "@/types/todo";

const categories = ref<Category[]>([]);
const isLoading = ref(false);
const error = ref<string | null>(null);
const showForm = ref(false);
const editingId = ref<number | null>(null);

const form = ref<CategoryFormData>({
  name: "",
  color: "#3B82F6",
});

const presetColors = [
  { name: "Blue", value: "#3B82F6" },
  { name: "Red", value: "#EF4444" },
  { name: "Green", value: "#10B981" },
  { name: "Yellow", value: "#F59E0B" },
  { name: "Purple", value: "#8B5CF6" },
  { name: "Pink", value: "#EC4899" },
  { name: "Indigo", value: "#6366F1" },
  { name: "Teal", value: "#14B8A6" },
];

const logout = () => {
  router.post("/logout");
};

const fetchCategories = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.get("/api/categories");
    categories.value = response.data.data;
  } catch (err) {
    error.value = "Failed to fetch categories";
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const createCategory = async () => {
  if (!form.value.name.trim()) return;

  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.post("/api/categories", form.value);
    categories.value.push(response.data.data);
    resetForm();
    showForm.value = false;
  } catch (err: any) {
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(", ");
    } else {
      error.value = "Failed to create category";
    }
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const updateCategory = async () => {
  if (editingId.value === null) return;

  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.put(`/api/categories/${editingId.value}`, form.value);
    const index = categories.value.findIndex((c) => c.id === editingId.value);
    if (index !== -1) {
      categories.value[index] = response.data.data;
    }
    resetForm();
    showForm.value = false;
  } catch (err: any) {
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(", ");
    } else {
      error.value = "Failed to update category";
    }
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const deleteCategory = async (id: number) => {
  if (!confirm("Are you sure? This will remove the category from all todos.")) return;

  isLoading.value = true;
  error.value = null;

  try {
    await axios.delete(`/api/categories/${id}`);
    categories.value = categories.value.filter((c) => c.id !== id);
  } catch (err) {
    error.value = "Failed to delete category";
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const startEdit = (category: Category) => {
  editingId.value = category.id;
  form.value = {
    name: category.name,
    color: category.color,
  };
  showForm.value = true;
};

const cancelForm = () => {
  resetForm();
  showForm.value = false;
};

const resetForm = () => {
  editingId.value = null;
  form.value = {
    name: "",
    color: "#3B82F6",
  };
};

const handleSubmit = () => {
  if (editingId.value !== null) {
    updateCategory();
  } else {
    createCategory();
  }
};

onMounted(() => {
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
            <a
              href="/todos"
              class="text-gray-700 hover:text-gray-900 font-medium transition"
            >
              Todos
            </a>
            <a href="/categories" class="text-blue-600 font-medium"> Categories </a>
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
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-3xl font-bold text-gray-900">My Categories</h2>
          <button
            @click="showForm = true"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            + New Category
          </button>
        </div>

        <!-- Error Message -->
        <div
          v-if="error"
          class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
        >
          {{ error }}
        </div>

        <!-- Category Form Modal -->
        <div
          v-if="showForm"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
          @click.self="cancelForm"
        >
          <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">
              {{ editingId ? "Edit Category" : "Create New Category" }}
            </h3>

            <form @submit.prevent="handleSubmit">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2"> Name </label>
                <input
                  v-model="form.name"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  placeholder="e.g., Work, Personal, Shopping"
                  required
                  maxlength="50"
                />
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2"> Color </label>
                <div class="flex items-center space-x-2 mb-2">
                  <input
                    v-model="form.color"
                    type="color"
                    class="w-12 h-12 rounded cursor-pointer border-2 border-gray-300"
                  />
                  <input
                    v-model="form.color"
                    type="text"
                    class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="#3B82F6"
                    pattern="^#[0-9A-Fa-f]{6}$"
                  />
                </div>
                <div class="grid grid-cols-8 gap-2">
                  <button
                    v-for="preset in presetColors"
                    :key="preset.value"
                    type="button"
                    @click="form.color = preset.value"
                    :style="{ backgroundColor: preset.value }"
                    class="w-8 h-8 rounded-full border-2 hover:scale-110 transition"
                    :class="
                      form.color === preset.value
                        ? 'border-gray-900 ring-2 ring-offset-2 ring-gray-900'
                        : 'border-gray-300'
                    "
                    :title="preset.name"
                  ></button>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <button
                  type="submit"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                  :disabled="isLoading"
                >
                  {{ editingId ? "Update" : "Create" }} Category
                </button>

                <button
                  type="button"
                  @click="cancelForm"
                  class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="category in categories"
            :key="category.id"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div
                  class="w-8 h-8 rounded-full"
                  :style="{ backgroundColor: category.color }"
                ></div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ category.name }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    {{ category.todos_count || 0 }} todo(s)
                  </p>
                </div>
              </div>
            </div>

            <div class="flex space-x-2">
              <button
                @click="startEdit(category)"
                class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-2 px-4 rounded transition"
              >
                Edit
              </button>
              <button
                @click="deleteCategory(category.id)"
                class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-4 rounded transition"
              >
                Delete
              </button>
            </div>
          </div>

          <div
            v-if="categories.length === 0 && !isLoading"
            class="col-span-full text-center text-gray-500 py-12"
          >
            <p class="text-lg mb-2">No categories yet.</p>
            <p class="text-sm">Create your first category to organize your todos!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
