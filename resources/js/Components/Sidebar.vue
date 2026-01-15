<template>
    <Transition
        enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isOpen"
            @click="$emit('close')"
            class="fixed inset-0 z-20 bg-white/30 backdrop-blur-md lg:hidden"
        ></div>
    </Transition>
    <div
        v-if="isOpen"
        @click="$emit('close')"
        class="fixed inset-0 z-20 bg-slate-900/10 backdrop-blur-sm lg:hidden"
    ></div>

    <aside
        :class="[
            'fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-100 transition-transform duration-300 lg:static lg:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <div class="p-6">
            <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">
                PIUTANG<span class="text-blue-600">KU</span>
            </h1>
        </div>

        <nav class="mt-2 px-4 space-y-1.5">
            <Link
                v-for="item in menuItems"
                :key="item.href"
                :href="item.href"
                :class="[
                    'flex items-center px-4 py-2.5 rounded-xl transition-all duration-200',
                    $page.url.startsWith(item.href)
                        ? 'bg-blue-50 text-blue-600 font-semibold'
                        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                ]"
            >
                <component :is="item.icon" class="w-5 h-5 mr-3 opacity-70" />
                <span class="text-sm">{{ item.label }}</span>
            </Link>
        </nav>
        <div class="p-4 border-t border-slate-50">
            <div class="flex items-center justify-between px-2">
                <span class="text-[11px] text-slate-400 font-medium">
                    System Version
                </span>
                <span
                    class="px-2 py-0.5 bg-blue-100 text-slate-500 text-[11px] font-mono rounded-full"
                >
                    v0.0.24
                </span>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({ isOpen: Boolean });
defineEmits(["close"]);

const menuItems = [
    {
        label: "Dashboard",
        href: "/dashboard",
        iconPath:
            "M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z",
    },
    {
        label: "Customers",
        href: "/customers",
        iconPath:
            "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
    },
    {
        label: "Debts",
        href: "/debts",
        iconPath:
            "M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
    },
    {
        label: "Settings",
        href: "#",
        iconPath:
            "M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
    },
];
</script>
