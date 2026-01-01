<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Payment Management</h1>
      <button
        @click="showPaymentModal = true"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Record New Payment
      </button>
    </div>

    <!-- Payment Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600">Total Received</div>
        <div class="text-2xl font-bold text-green-600">Rp {{ formatCurrency(stats.total_received) }}</div>
        <div class="text-sm text-gray-500">{{ stats.completed_count }} payments</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600">Pending Payments</div>
        <div class="text-2xl font-bold text-yellow-600">Rp {{ formatCurrency(stats.total_pending) }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600">This Month</div>
        <div class="text-2xl font-bold text-blue-600">Rp {{ formatCurrency(stats.this_month) }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600">Total Payments</div>
        <div class="text-2xl font-bold text-purple-600">{{ stats.payment_count }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded shadow mb-6">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
          <select
            v-model="filters.customer_id"
            @change="fetchPayments"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Customers</option>
            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
              {{ customer.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="filters.status"
            @change="fetchPayments"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Status</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
          <input
            v-model="filters.date_from"
            @change="fetchPayments"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
          <input
            v-model="filters.date_to"
            @change="fetchPayments"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>
        <div class="flex items-end">
          <button
            @click="clearFilters"
            class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
          >
            Clear Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded shadow overflow-hidden">
      <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">Payment History</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debt Ref</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ formatDate(payment.paid_at) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ payment.debt.customer.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ payment.debt.reference_id }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">Rp {{ formatCurrency(payment.amount_paid) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 capitalize">{{ payment.payment_method || 'N/A' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusClass(payment.status)"
                >
                  {{ payment.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="viewPayment(payment)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  View
                </button>
                <button
                  @click="editPayment(payment)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="deletePayment(payment)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="payments.data && payments.data.length > 0" class="px-6 py-4 border-t bg-gray-50">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ payments.from }} to {{ payments.to }} of {{ payments.total }} payments
          </div>
          <div class="flex space-x-1">
            <button
              v-for="page in payments.links"
              :key="page.label"
              @click="goToPage(page.url)"
              :class="[
                'px-3 py-1 text-sm border rounded',
                page.active
                  ? 'bg-blue-500 text-white border-blue-500'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
              ]"
              v-html="page.label"
            ></button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Edit Payment' : 'Record New Payment' }}
          </h3>

          <form @submit.prevent="showEditModal ? updatePayment() : createPayment()">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Debt</label>
              <select
                v-model="form.debt_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Debt</option>
                <option v-for="debt in availableDebts" :key="debt.id" :value="debt.id">
                  {{ debt.reference_id }} - {{ debt.customer.name }} (Rp {{ formatCurrency(debt.amount) }})
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
              <input
                v-model="form.amount_paid"
                type="number"
                min="1000"
                step="1000"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="100000"
                required
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
              <select
                v-model="form.payment_method"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Method</option>
                <option value="cash">Cash</option>
                <option value="transfer">Bank Transfer</option>
                <option value="tripay">QRIS/Tripay</option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number (Optional)</label>
              <input
                v-model="form.reference_number"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Transaction reference"
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
              <input
                v-model="form.paid_at"
                type="datetime-local"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="form.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
              </select>
            </div>

            <div class="flex justify-end gap-3 mt-6">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 disabled:opacity-50"
              >
                {{ loading ? 'Saving...' : (showEditModal ? 'Update' : 'Record Payment') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View Payment Modal -->
    <div v-if="showViewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>

          <div v-if="viewingPayment" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Customer</label>
              <p class="text-sm text-gray-900">{{ viewingPayment.debt.customer.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Debt Reference</label>
              <p class="text-sm text-gray-900">{{ viewingPayment.debt.reference_id }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Amount Paid</label>
              <p class="text-sm text-gray-900">Rp {{ formatCurrency(viewingPayment.amount_paid) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Payment Method</label>
              <p class="text-sm text-gray-900 capitalize">{{ viewingPayment.payment_method || 'N/A' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Reference Number</label>
              <p class="text-sm text-gray-900">{{ viewingPayment.reference_number || 'N/A' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Payment Date</label>
              <p class="text-sm text-gray-900">{{ formatDateTime(viewingPayment.paid_at) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Status</label>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getStatusClass(viewingPayment.status)"
              >
                {{ viewingPayment.status }}
              </span>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineOptions } from 'vue'
import DashboardLayout from "../Layouts/DashboardLayout.vue";

defineOptions({
  layout: DashboardLayout,
});

// Reactive data
const payments = ref({ data: [] })
const customers = ref([])
const availableDebts = ref([])
const stats = ref({
  total_received: 0,
  total_pending: 0,
  payment_count: 0,
  completed_count: 0,
  this_month: 0
})

const showPaymentModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const loading = ref(false)
const editingPayment = ref(null)
const viewingPayment = ref(null)

const filters = ref({
  customer_id: '',
  status: '',
  date_from: '',
  date_to: ''
})

// Form data
const form = ref({
  debt_id: '',
  amount_paid: '',
  payment_method: '',
  reference_number: '',
  paid_at: '',
  status: 'completed'
})

// Methods
const fetchPayments = async (url = null) => {
  try {
    const token = localStorage.getItem('api_token')
    const queryParams = new URLSearchParams()

    if (filters.value.customer_id) queryParams.append('customer_id', filters.value.customer_id)
    if (filters.value.status) queryParams.append('status', filters.value.status)
    if (filters.value.date_from) queryParams.append('date_from', filters.value.date_from)
    if (filters.value.date_to) queryParams.append('date_to', filters.value.date_to)

    const endpoint = url || `/api/payments?${queryParams.toString()}`

    const response = await fetch(endpoint, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      payments.value = await response.json()
    }
  } catch (error) {
    console.error('Error fetching payments:', error)
  }
}

const fetchStats = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch('/api/payments/statistics', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      stats.value = await response.json()
    }
  } catch (error) {
    console.error('Error fetching payment stats:', error)
  }
}

const fetchCustomers = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch('/api/customers', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      const data = await response.json()
      customers.value = data.data || []
    }
  } catch (error) {
    console.error('Error fetching customers:', error)
  }
}

const fetchAvailableDebts = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch('/api/debts', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      const data = await response.json()
      availableDebts.value = data.data || []
    }
  } catch (error) {
    console.error('Error fetching debts:', error)
  }
}

const createPayment = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('api_token')

    // Format the datetime for the API
    const formData = { ...form.value }
    if (formData.paid_at) {
      formData.paid_at = new Date(formData.paid_at).toISOString().slice(0, 19).replace('T', ' ')
    }

    const response = await fetch('/api/payments', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formData)
    })

    const data = await response.json()

    if (response.ok) {
      await Promise.all([fetchPayments(), fetchStats()])
      closeModal()
      resetForm()
    } else {
      alert(data.message || 'Error recording payment')
    }
  } catch (error) {
    console.error('Error creating payment:', error)
    alert('Error recording payment')
  } finally {
    loading.value = false
  }
}

const editPayment = (payment) => {
  editingPayment.value = payment
  form.value = {
    debt_id: payment.debt_id,
    amount_paid: payment.amount_paid,
    payment_method: payment.payment_method || '',
    reference_number: payment.reference_number || '',
    paid_at: payment.paid_at ? new Date(payment.paid_at).toISOString().slice(0, 16) : '',
    status: payment.status
  }
  showEditModal.value = true
}

const updatePayment = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('api_token')

    const formData = { ...form.value }
    if (formData.paid_at) {
      formData.paid_at = new Date(formData.paid_at).toISOString().slice(0, 19).replace('T', ' ')
    }

    const response = await fetch(`/api/payments/${editingPayment.value.id}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formData)
    })

    const data = await response.json()

    if (response.ok) {
      await Promise.all([fetchPayments(), fetchStats()])
      closeModal()
      resetForm()
    } else {
      alert(data.message || 'Error updating payment')
    }
  } catch (error) {
    console.error('Error updating payment:', error)
    alert('Error updating payment')
  } finally {
    loading.value = false
  }
}

const deletePayment = async (payment) => {
  if (!confirm(`Are you sure you want to delete this payment of Rp ${formatCurrency(payment.amount_paid)}?`)) {
    return
  }

  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch(`/api/payments/${payment.id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      await Promise.all([fetchPayments(), fetchStats()])
    } else {
      const data = await response.json()
      alert(data.message || 'Error deleting payment')
    }
  } catch (error) {
    console.error('Error deleting payment:', error)
    alert('Error deleting payment')
  }
}

const viewPayment = (payment) => {
  viewingPayment.value = payment
  showViewModal.value = true
}

const closeModal = () => {
  showPaymentModal.value = false
  showEditModal.value = false
  showViewModal.value = false
  editingPayment.value = null
  viewingPayment.value = null
  resetForm()
}

const resetForm = () => {
  form.value = {
    debt_id: '',
    amount_paid: '',
    payment_method: '',
    reference_number: '',
    paid_at: '',
    status: 'completed'
  }
}

const clearFilters = () => {
  filters.value = {
    customer_id: '',
    status: '',
    date_from: '',
    date_to: ''
  }
  fetchPayments()
}

const goToPage = (url) => {
  if (url) {
    fetchPayments(url)
  }
}

// Utility functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('id-ID')
}

const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'failed':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

// Lifecycle
onMounted(async () => {
  await Promise.all([
    fetchPayments(),
    fetchStats(),
    fetchCustomers(),
    fetchAvailableDebts()
  ])
})
</script>
