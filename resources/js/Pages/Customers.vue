<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Customers Management</h1>
      <button
        @click="showAddModal = true"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add New Customer
      </button>
    </div>

    <!-- Sort Controls -->
    <div class="bg-white p-4 rounded shadow mb-6">
      <div class="flex gap-4 items-center">
        <label class="text-sm font-medium text-gray-700">Sort by:</label>
        <select
          v-model="sortBy"
          @change="sortCustomers"
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="name">Name (A-Z)</option>
          <option value="total_debt">Total Debt (High to Low)</option>
          <option value="pending_debt">Pending Debt (High to Low)</option>
          <option value="paid_debt">Paid Debt (High to Low)</option>
        </select>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded shadow overflow-hidden">
      <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">All Customers</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Debt</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="customer in sortedCustomers" :key="customer.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ customer.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">Rp {{ formatCurrency(customer.total_debt) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Rp {{ formatCurrency(customer.pending_debt) }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Rp {{ formatCurrency(customer.paid_debt) }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="editCustomer(customer)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="deleteCustomer(customer)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Edit Customer' : 'Add New Customer' }}
          </h3>

          <form @submit.prevent="showEditModal ? updateCustomer() : createCustomer()">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Customer name"
                required
              />
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

defineOptions({
  layout: DashboardLayout,
});

// Reactive data
const customers = ref([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const loading = ref(false)
const editingCustomer = ref(null)
const sortBy = ref('name')

// Form data
const form = ref({
  name: ''
})

// Computed
const sortedCustomers = computed(() => {
  const sorted = [...customers.value]

  switch (sortBy.value) {
    case 'total_debt':
      return sorted.sort((a, b) => b.total_debt - a.total_debt)
    case 'pending_debt':
      return sorted.sort((a, b) => b.pending_debt - a.pending_debt)
    case 'paid_debt':
      return sorted.sort((a, b) => b.paid_debt - a.paid_debt)
    case 'name':
    default:
      return sorted.sort((a, b) => a.name.localeCompare(b.name))
  }
})

// Methods
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
      calculateCustomerStats()
    }
  } catch (error) {
    console.error('Error fetching customers:', error)
  }
}

const calculateCustomerStats = () => {
  customers.value.forEach(customer => {
    // Calculate total debt, pending debt, and paid debt for each customer
    const customerDebts = customer.debts || []
    customer.total_debt = customerDebts.reduce((sum, debt) => sum + parseFloat(debt.amount), 0)
    customer.pending_debt = customerDebts.filter(debt => debt.status === 'pending').reduce((sum, debt) => sum + parseFloat(debt.amount), 0)
    customer.paid_debt = customerDebts.filter(debt => debt.status === 'paid').reduce((sum, debt) => sum + parseFloat(debt.amount), 0)
  })
}

const createCustomer = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch('/api/customers', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form.value)
    })

    const data = await response.json()

    if (response.ok) {
      await fetchCustomers()
      closeModal()
      resetForm()
    } else {
      alert(data.message || 'Error creating customer')
    }
  } catch (error) {
    console.error('Error creating customer:', error)
    alert('Error creating customer')
  } finally {
    loading.value = false
  }
}

const editCustomer = (customer) => {
  editingCustomer.value = customer
  form.value = {
    name: customer.name
  }
  showEditModal.value = true
}

const updateCustomer = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch(`/api/customers/${editingCustomer.value.id}`, {
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
      await fetchCustomers()
      closeModal()
      resetForm()
    } else {
      alert(data.message || 'Error updating customer')
    }
  } catch (error) {
    console.error('Error updating customer:', error)
    alert('Error updating customer')
  } finally {
    loading.value = false
  }
}

const deleteCustomer = async (customer) => {
  if (!confirm(`Are you sure you want to delete ${customer.name}? This will also delete all their debts.`)) {
    return
  }

  try {
    const token = localStorage.getItem('api_token')
    const response = await fetch(`/api/customers/${customer.id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      await fetchCustomers()
    } else {
      const data = await response.json()
      alert(data.message || 'Error deleting customer')
    }
  } catch (error) {
    console.error('Error deleting customer:', error)
    alert('Error deleting customer')
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingCustomer.value = null
  resetForm()
}

const resetForm = () => {
  form.value = {
    name: ''
  }
}

const sortCustomers = () => {
  // Sorting is handled by the computed property
}

// Utility functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount)
}

// Lifecycle
onMounted(async () => {
  await fetchCustomers()
})
</script>
