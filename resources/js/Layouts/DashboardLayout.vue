<template>
  <div class="min-h-screen flex bg-gray-50">
    <!-- Sidebar -->
    <div class="w-64 bg-white border-r shadow-sm">
      <Sidebar />
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <Navbar />
      <main class="flex-1 p-6 overflow-auto">
        <div class="max-w-7xl mx-auto">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import Navbar from "../Components/Navbar.vue";
import Sidebar from "../Components/Sidebar.vue";
</script>
