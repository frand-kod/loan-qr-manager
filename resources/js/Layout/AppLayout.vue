<template>
    <div class="flex h-screen bg-slate-50 overflow-hidden font-sans">
        <Sidebar :isOpen="isSidebarOpen" @close="isSidebarOpen = false" />

        <div class="flex-1 flex flex-col min-w-0">
            <Navbar
                :user="$page.props.auth.user"
                :pageTitle="$page.component"
                @toggle-sidebar="isSidebarOpen = true"
            />

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import Sidebar from "@/Components/Sidebar.vue";
import Navbar from "@/Components/Navbar.vue";

const isSidebarOpen = ref(false);
</script>
