<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      email_verified_at: string | null;
    };
  };
  mustVerifyEmail?: boolean;
  status?: string;
}>();

const logout = () => {
  router.post("/logout");
};
</script>

<template>
  <Head title="Profile" />

  <div class="min-h-screen bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-900">My Todo App</h1>
          </div>
          <div class="flex items-center space-x-4">
            <Link
              href="/todos"
              class="text-gray-700 hover:text-gray-900 font-medium transition"
            >
              Todos
            </Link>
            <Link href="/profile" class="text-blue-600 font-medium"> Profile </Link>
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
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="max-w-xl">
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
            <p class="mt-1 text-sm text-gray-600">
              View your account's profile information and email address.
            </p>

            <div class="mt-6 space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ props.auth.user.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="mt-1 text-sm text-gray-900">{{ props.auth.user.email }}</p>
              </div>

              <div
                v-if="props.mustVerifyEmail && !props.auth.user.email_verified_at"
                class="mt-2"
              >
                <p class="text-sm text-gray-800">Your email address is unverified.</p>
              </div>
            </div>

            <div class="mt-6">
              <p class="text-sm text-gray-600">
                Profile editing is coming soon! For now, you can view your information
                here.
              </p>
            </div>
          </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="max-w-xl">
            <h2 class="text-lg font-medium text-gray-900">Account Actions</h2>
            <p class="mt-1 text-sm text-gray-600">Manage your account settings.</p>

            <div class="mt-6">
              <Link
                href="/todos"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Back to Todos
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
