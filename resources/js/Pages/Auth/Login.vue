<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    // Sementara kita kosongkan dulu logikanya
    alert("Tombol masuk ditekan!");
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center">
        <div
            class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-100"
        >
            <div class="text-center mb-8">
                <h1 class="text-3xl font-medium text-gray-700">PIUTANGKU</h1>
                <h2>!! logo gambar !!</h2>
                <p class="text-gray-500 text-sm">Silakan masuk ke akun Anda</p>
            </div>

            <form @submit.prevent="submit">
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Email</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="nama@email.com"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    />
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Password</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="••••••••"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 transition duration-200"
                >
                    Masuk Sekarang
                </button>
            </form>
        </div>
    </div>
</template>
