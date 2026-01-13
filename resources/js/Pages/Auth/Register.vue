<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2
                class="text-2xl font-bold mb-2 text-center text-blue-900 italic"
            >
                PIUTANGKU
            </h2>
            <p class="text-center text-gray-500 mb-6 text-sm">
                Buat akun untuk mulai mengelola hutang.
            </p>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700"
                        >Nama Lengkap</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full p-2 rounded mt-1 shadow-sm"
                        :class="{ 'border-red-500': form.errors.name }"
                        placeholder="Nama Anda"
                    />
                    <div
                        v-if="form.errors.name"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.name }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700"
                        >Email</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full p-2 rounded mt-1 shadow-sm"
                        :class="{ 'border-red-500': form.errors.email }"
                        placeholder="email@contoh.com"
                    />
                    <div
                        v-if="form.errors.email"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700"
                        >Password</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        class="w-full p-2 rounded mt-1 shadow-sm"
                        :class="{ '  -red-500': form.errors.password }"
                    />
                    <div
                        v-if="form.errors.password"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700"
                        >Konfirmasi Password</label
                    >
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        class="w-full p-2 rounded mt-1 shadow-sm"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 transition disabled:opacity-50"
                >
                    {{ form.processing ? "Mendaftar..." : "Daftar Sekarang" }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                Sudah punya akun?
                <Link :href="route('login')" class="text-blue-600 font-bold"
                    >Login</Link
                >
            </p>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "", // Wajib ada agar validasi 'confirmed' di Laravel jalan
});

const submit = () => {
    // form.post(route("register"), {
    //     onFinish: () => form.reset("password", "password_confirmation"),
    // });

    form.post("/register", {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>
