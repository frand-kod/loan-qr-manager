<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Debts Management</h1>
      <button
        @click="showAddModal = true"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add New Debt
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600">Total Pending</div>
        <div class="text-2xl font-bold text-orange-600">Rp {{ formatCurrency(stats.total_pending) }}</div>
        <div class="text-sm text-gray-500">{{ stats.count_pending }} debts</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600">Total Paid</div>
        <div class="text-2xl font-bold text-green-600">Rp {{ formatCurrency(stats.total_paid) }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600">Total Debts</div>
        <div class="text-2xl font-bold text-blue-600">{{ debts.length }}</div>
      </div>
    </div>

    <!-- Debts Table -->
    <div class="bg-white rounded shadow overflow-hidden">
      <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">All Debts</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="debt in debts" :key="debt.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ debt.reference_id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ debt.customer.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                Rp {{ formatCurrency(debt.amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(debt.due_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="getStatusClass(debt.status)"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                >
                  {{ debt.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="editDebt(debt)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="deleteDebt(debt.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="debts.length === 0" class="px-6 py-12 text-center text-gray-500">
        No debts found. Click "Add New Debt" to create your first debt.
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Edit Debt' : 'Add New Debt' }}
          </h3>

          <form @submit.prevent="showEditModal ? updateDebt() : createDebt()">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
              <div class="flex gap-4 mb-2">
                <label class="flex items-center">
                  <input
                    v-model="useExistingCustomer"
                    type="radio"
                    value="true"
                    class="mr-2"
                  />
                  Select existing customer
                </label>
                <label class="flex items-center">
                  <input
                    v-model="useExistingCustomer"
                    type="radio"
                    value="false"
                    class="mr-2"
                  />
                  Add new customer
                </label>
              </div>

              <div v-if="useExistingCustomer === 'true'">
                <select
                  v-model="form.customer_id"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                >
                  <option value="">Select Customer</option>
                  <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                    {{ customer.name }}
                  </option>
                </select>
              </div>

              <div v-else>
                <input
                  v-model="newCustomerName"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter customer name"
                  required
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
              <input
                v-model="form.amount"
                type="number"
                min="1000"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="100000"
                required
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
              <input
                v-model="form.due_date"
                type="date"
                :min="minDate"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>

            <div v-if="showEditModal" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="form.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
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
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ loading ? 'Saving...' : (showEditModal ? 'Update' : 'Create') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import DashboardLayout from "../Layouts/DashboardLayout.vue";
import { usePage } from '@inertiajs/vue3'

defineOptions({
  layout: DashboardLayout,
});

const page = usePage()

// Reactive data
const debts = ref([])
const customers = ref([])
const stats = ref({
  total_pending: 0,
  count_pending: 0,
  total_paid: 0
})
const showAddModal = ref(false)
const showEditModal = ref(false)
const loading = ref(false)
const editingDebt = ref(null)

// Form data
const form = ref({
  customer_id: '',
  amount: '',
  due_date: '',
  status: 'pending'
})

// Customer management
const useExistingCustomer = ref('true')
const newCustomerName = ref('')

// Computed
const minDate = computed(() => {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow.toISOString().split('T')[0]
})

// Methods
const fetchDebts = async () => {
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
      debts.value = data.data || []
      calculateStats()
    }
  } catch (error) {
    console.error('Error fetching debts:', error)
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

const calculateStats = () => {
  stats.value = {
    total_pending: debts.value.filter(d => d.status === 'pending').reduce((sum, d) => sum + parseFloat(d.amount), 0),
    count_pending: debts.value.filter(d => d.status === 'pending').length,
    total_paid: debts.value.filter(d => d.status === 'paid').reduce((sum, d) => sum + parseFloat(d.amount), 0)
  }
}

const createDebt = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('api_token')

    let customerId = form.value.customer_id

    // If adding new customer, create it first
    if (useExistingCustomer.value === 'false') {
      const customerResponse = await fetch('/api/customers', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name: newCustomerName.value
        })
      })

      const customerData = await customerResponse.json()

      if (customerResponse.ok) {
        customerId = customerData.data.id
        // Refresh customers list
        await fetchCustomers()
      } else {
        alert(customerData.message || 'Error creating customer')
        loading.value = false
        return
      }
    }

    // Create the debt
    const debtResponse = await fetch('/api/debts', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        ...form.value,
        customer_id: customerId
      })
    })

    const debtData = await debtResponse.json()

    if (debtResponse.ok) {
      await fetchDebts()
      closeModal()
      resetForm()
    } else {
      alert(debtData.message || 'Error creating debt')
    }
  } catch (error) {
    console.error('Error creating debt:', error)
    alert('Error creating debt')
  } finally {
    loading.value = false
  }
}

const editDebt = (debt) => {
  editingDebt.value = debt
  form.value = {
    customer_id: debt.customer_id,
    amount: debt.amount,
    due_date: debt.due_date.split('T')[0], // Format for date input
    status: debt.status
  }
  showEditModal.value = true
}

const updateDebt = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch(`/api/debts/${editingDebt.value.id}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form.value)
    })

    const data = await response.json()

    if (response.ok) {
      await fetchDebts()
      closeModal()
      resetForm()
    } else {
      alert(data.message || 'Error updating debt')
    }
  } catch (error) {
    console.error('Error updating debt:', error)
    alert('Error updating debt')
  } finally {
    loading.value = false
  }
}

const deleteDebt = async (id) => {
  if (!confirm('Are you sure you want to delete this debt?')) return

  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch(`/api/debts/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      await fetchDebts()
    } else {
      alert('Error deleting debt')
    }
  } catch (error) {
    console.error('Error deleting debt:', error)
    alert('Error deleting debt')
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingDebt.value = null
  resetForm()
}

const resetForm = () => {
  form.value = {
    customer_id: '',
    amount: '',
    due_date: '',
    status: 'pending'
  }
  useExistingCustomer.value = 'true'
  newCustomerName.value = ''
}

// Utility functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}

const getStatusClass = (status) => {
  switch (status) {
    case 'paid':
      return 'bg-green-100 text-green-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'overdue':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

// Lifecycle
onMounted(async () => {
  await Promise.all([fetchDebts(), fetchCustomers()])
})
</script>
