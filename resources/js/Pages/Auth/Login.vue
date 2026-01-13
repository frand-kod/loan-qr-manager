<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2
                class="text-2xl font-bold mb-6 text-center text-blue-900 italic"
            >
                PIUTANGKU
            </h2>
            <form @submit.prevent="form.post('/login')">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Email</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        :class="{ ' -red-500': form.errors.email }"
                    />
                    <div
                        v-if="form.errors.email"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Password</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    />
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
                >
                    Login
                </button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Belum punya akun?
                <Link href="/register" class="text-blue-600">Daftar</Link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/vue3";

const form = useForm({
    email: "",
    password: "",
});
</script>
