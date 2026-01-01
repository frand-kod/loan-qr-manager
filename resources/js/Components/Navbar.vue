<template>
  <header class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="text-lg font-semibold">{{ pageTitle }}</div>
    <div class="flex items-center space-x-4">
      <div class="text-sm text-gray-600">Hi, User</div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const pageTitle = computed(() => {
  const url = page.url
  if (url === '/dashboard') return 'Dashboard'
  if (url === '/customers') return 'Customers'
  if (url === '/debts') return 'Debts'
  if (url === '/payments') return 'Payments'
  return 'Dashboard'
})
</script>
