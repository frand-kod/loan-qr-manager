<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold">Dashboard</h1>
      <p class="text-gray-600">Welcome back! Here's an overview of your loan management.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Customers</p>
            <p class="text-2xl font-bold text-gray-900">{{ summary.total_customers }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Paid</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ formatCurrency(summary.total_paid) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-2 bg-yellow-100 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Pending Amount</p>
            <p class="text-2xl font-bold text-yellow-600">Rp {{ formatCurrency(summary.total_pending) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-2 bg-red-100 rounded-lg">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Overdue Amount</p>
            <p class="text-2xl font-bold text-red-600">Rp {{ formatCurrency(summary.total_overdue) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Debts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Pending Debts -->
      <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Recent Pending Debts</h3>
        </div>
        <div class="p-6">
          <div v-if="recentPendingDebts.length === 0" class="text-center text-gray-500 py-4">
            No pending debts
          </div>
          <div v-else class="space-y-4">
            <div v-for="debt in recentPendingDebts" :key="debt.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
              <div>
                <p class="font-medium text-gray-900">{{ debt.customer.name }}</p>
                <p class="text-sm text-gray-600">Due: {{ formatDate(debt.due_date) }}</p>
              </div>
              <div class="text-right">
                <p class="font-bold text-gray-900">Rp {{ formatCurrency(debt.amount) }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                  Pending
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Paid Debts -->
      <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Recent Paid Debts</h3>
        </div>
        <div class="p-6">
          <div v-if="recentPaidDebts.length === 0" class="text-center text-gray-500 py-4">
            No paid debts
          </div>
          <div v-else class="space-y-4">
            <div v-for="debt in recentPaidDebts" :key="debt.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
              <div>
                <p class="font-medium text-gray-900">{{ debt.customer.name }}</p>
                <p class="text-sm text-gray-600">Paid on: {{ formatDate(debt.updated_at) }}</p>
              </div>
              <div class="text-right">
                <p class="font-bold text-gray-900">Rp {{ formatCurrency(debt.amount) }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Paid
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Customers by Debt -->
    <div class="mt-6 bg-white rounded-lg shadow-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Top Customers by Total Debt</h3>
      </div>
      <div class="p-6">
        <div v-if="topCustomers.length === 0" class="text-center text-gray-500 py-4">
          No customers with debts
        </div>
        <div v-else class="space-y-4">
          <div v-for="(customer, index) in topCustomers" :key="customer.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">
                {{ index + 1 }}
              </div>
              <div class="ml-4">
                <p class="font-medium text-gray-900">{{ customer.name }}</p>
                <p class="text-sm text-gray-600">{{ customer.debts_count }} debts</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-bold text-gray-900">Rp {{ formatCurrency(customer.total_debt) }}</p>
              <p class="text-sm text-gray-600">
                Pending: Rp {{ formatCurrency(customer.pending_debt) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from "../Layouts/DashboardLayout.vue";

defineOptions({
  layout: DashboardLayout,
});

// Reactive data
const summary = ref({
  total_customers: 0,
  total_paid: 0,
  total_pending: 0,
  total_overdue: 0
})
const recentPendingDebts = ref([])
const recentPaidDebts = ref([])
const topCustomers = ref([])

// Methods
const fetchDashboardData = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch('/api/dashboard/summary', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      const data = await response.json()
      summary.value = data.summary
      recentPendingDebts.value = data.recent_pending_debts
      recentPaidDebts.value = data.recent_paid_debts
      topCustomers.value = data.top_customers
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  }
}

// Utility functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}

// Lifecycle
onMounted(async () => {
  await fetchDashboardData()
})
</script>
