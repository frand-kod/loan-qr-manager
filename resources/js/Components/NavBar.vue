<template>
    <header
        class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-6 lg:px-10 sticky top-0 z-10"
    >
        <div class="flex items-center">
            <button
                @click="$emit('toggle-sidebar')"
                class="p-2 mr-4 text-slate-500 lg:hidden bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors"
            >
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

            <div class="hidden sm:block">
                <h2
                    class="text-xs font-semibold text-slate-400 uppercase tracking-widest"
                >
                    Workspace
                </h2>
                <p class="text-lg font-bold text-slate-800">{{ pageTitle }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div
                class="flex items-center bg-slate-100 px-4 py-2 rounded-l border border-slate-100"
            >
                <div class="flex flex-col items-end mr-3">
                    <span
                        class="text-sm font-bold text-slate-8s00 leading-none"
                        >{{ user.name }}</span
                    >
                    <span
                        class="text-[10px] font-medium text-green-500 mt-1 uppercase tracking-tighter"
                        >Verified Account</span
                    >
                </div>
                <div
                    class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm"
                >
                    {{ user.name.charAt(0).toUpperCase() }}
                </div>
            </div>

            <!-- flash -->

            <Link
                href="/logout"
                method="post"
                as="button"
                class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all duration-200"
                title="Log Out"
            >
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                </svg>
            </Link>
        </div>
    </header>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    user: Object,
    pageTitle: String,
});

defineEmits(["toggle-sidebar"]);
</script>
