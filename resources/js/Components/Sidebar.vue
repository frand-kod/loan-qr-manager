<template>
  <aside class="w-64 bg-white border-r min-h-screen">
    <div class="p-4 flex items-center space-x-3 border-b border-gray-200">
      <img
        :src="logo"
        alt="Pituangku Logo"
        class="h-8 w-8"
      />
      <div class="text-xl font-bold text-blue-900" >PIUTANGKU</div>
    </div>
    <nav class="p-4 space-y-1">
      <Link
        href="/dashboard"
        :class="['block px-4 py-2 rounded hover:bg-gray-100', { 'bg-blue-100 text-blue-700': isActive('/dashboard') }]"
      >
        Dashboard
      </Link>
      <Link
        href="/customers"
        :class="['block px-4 py-2 rounded hover:bg-gray-100', { 'bg-blue-100 text-blue-700': isActive('/customers') }]"
      >
        Customers
      </Link>
      <Link
        href="/debts"
        :class="['block px-4 py-2 rounded hover:bg-gray-100', { 'bg-blue-100 text-blue-700': isActive('/debts') }]"
      >
        Debts
      </Link>
      <Link
        href="/payments"
        :class="['block px-4 py-2 rounded hover:bg-gray-100', { 'bg-blue-100 text-blue-700': isActive('/payments') }]"
      >
        Payments
      </Link>
    </nav>
  </aside>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { usePage } from '@inertiajs/vue3'
import logo from "../../assets/images/logos/piutangku-logo.svg";

const page = usePage()

const isActive = (url) => {
  return page.url === url
}
</script>
