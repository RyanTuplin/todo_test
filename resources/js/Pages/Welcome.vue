<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();
</script>

<template>
    <Head title="Welcome to Todo App" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8 space-y-8">
                <!-- Header -->
                <div class="text-center">
                    <div class="inline-block p-3 bg-blue-100 rounded-full mb-4">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Todo App</h1>
                    <p class="text-gray-600">Organize your tasks and boost productivity</p>
                </div>

                <!-- Auth Buttons -->
                <div v-if="canLogin" class="space-y-3">
                    <Link
                        href="/login"
                        class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition duration-200 shadow-md hover:shadow-lg"
                    >
                        Sign In
                    </Link>

                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="block w-full bg-white hover:bg-gray-50 text-blue-600 font-semibold py-3 px-4 rounded-lg text-center transition duration-200 border-2 border-blue-600"
                    >
                        Create Account
                    </Link>
                </div>

                <!-- Features -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Create and organize todos
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Track completion status
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Personal and secure
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-xs text-gray-500 pt-4">
                    <p>Laravel v{{ laravelVersion }} • PHP v{{ phpVersion }}</p>
                </div>
            </div>
        </div>
    </div>
</template>