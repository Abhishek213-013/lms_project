<template>
  <div class="min-h-screen bg-gray-50 flex" style="font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol' !important;">
    <!-- Mobile Menu Overlay -->
    <div 
      v-if="isMobileMenuOpen" 
      class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
      @click="closeMobileMenu"
    ></div>

    <!-- Sidebar -->
    <Sidebar 
      :is-mobile-menu-open="isMobileMenuOpen" 
      @menu-click="closeMobileMenu" 
    />

    <!-- Main Content -->
    <div class="flex-1 w-full lg:ml-64 transition-all duration-300">
      <!-- Top Navbar -->
      <Navbar 
        page-title="Payment Verification" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />
      
      <!-- Page Content -->
      <div class="p-3 sm:p-4 lg:p-6">
        <!-- Header -->
        <div class="mb-4 sm:mb-6">
          <h2 class="mb-1 sm:mb-2 text-base sm:text-lg lg:text-xl">Payment Verification</h2>
          <p class="text-gray-600 text-xs sm:text-sm lg:text-base">Review and verify pending bank transfer payments.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Pending Payments</p>
                <h3 class="text-xl font-bold text-yellow-600">{{ stats.pendingPayments || '0' }}</h3>
              </div>
              <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Verified Today</p>
                <h3 class="text-xl font-bold text-green-600">{{ stats.verifiedToday || '0' }}</h3>
              </div>
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Total Amount Pending</p>
                <h3 class="text-xl font-bold text-blue-600">৳ {{ stats.totalPendingAmount || '0' }}</h3>
              </div>
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Bank Transfers</p>
                <h3 class="text-xl font-bold text-purple-600">{{ stats.bankTransfers || '0' }}</h3>
              </div>
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Payments Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Pending Payments</h3>
            <p class="text-sm text-gray-600 mt-1">Review and verify bank transfer payments</p>
          </div>

          <div class="p-4">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-8">
              <div class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-600">Loading payments...</span>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="pendingPayments.length === 0" class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No pending payments</h3>
              <p class="mt-1 text-sm text-gray-500">All payments have been verified.</p>
            </div>

            <!-- Payments Table -->
            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="payment in pendingPayments" :key="payment.id" class="hover:bg-gray-50">
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      #{{ payment.id }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ payment.student_name }}</div>
                      <div class="text-sm text-gray-500">{{ payment.student_email }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ payment.course_name }}</div>
                      <div class="text-sm text-gray-500">{{ payment.course_subject }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                      ৳ {{ payment.amount }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                      <button 
                        @click="viewReceipt(payment.receipt_url)" 
                        class="text-blue-600 hover:text-blue-900 font-medium"
                        :disabled="!payment.receipt_url"
                      >
                        View Receipt
                      </button>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(payment.created_at) }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <button 
                          @click="verifyPayment(payment.id)" 
                          class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                          :disabled="verifyingPayment === payment.id"
                        >
                          <span v-if="verifyingPayment === payment.id" class="animate-spin -ml-1 mr-2 h-3 w-3 text-white">
                            <svg class="h-3 w-3" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                          </span>
                          {{ verifyingPayment === payment.id ? 'Verifying...' : 'Verify' }}
                        </button>
                        <button 
                          @click="rejectPayment(payment.id)" 
                          class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                          Reject
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '../Layout/Navbar.vue';
import Sidebar from '../Layout/Sidebar.vue';
import { ref, onMounted } from 'vue'

// Reactive data
const isMobileMenuOpen = ref(false)
const loading = ref(true)
const verifyingPayment = ref(null)
const pendingPayments = ref([])
const stats = ref({})

// Mobile menu functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

// Handle search
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Format date
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// View receipt
const viewReceipt = (receiptUrl) => {
  if (receiptUrl) {
    window.open(receiptUrl, '_blank')
  }
}

// Verify payment
const verifyPayment = async (paymentId) => {
  verifyingPayment.value = paymentId
  
  try {
    const response = await fetch(`/api/payments/verify/${paymentId}`, { // FIXED: Correct endpoint
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    
    const result = await response.json()
    
    if (result.success) {
      // Remove from pending list
      pendingPayments.value = pendingPayments.value.filter(p => p.id !== paymentId)
      // Update stats
      await fetchStats()
      alert('Payment verified successfully! Student has been enrolled in the course.')
    } else {
      alert('Verification failed: ' + result.message)
    }
  } catch (error) {
    console.error('Error verifying payment:', error)
    alert('Verification failed. Please try again.')
  } finally {
    verifyingPayment.value = null
  }
}

// Reject payment
const rejectPayment = async (paymentId) => {
  if (!confirm('Are you sure you want to reject this payment? This action cannot be undone.')) {
    return
  }
  
  const reason = prompt('Please provide a reason for rejection:', 'Payment details could not be verified');
  
  if (reason === null) return; // User cancelled
  
  try {
    const response = await fetch(`/api/payments/reject/${paymentId}`, { // FIXED: Correct endpoint
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ reason })
    })
    
    const result = await response.json()
    
    if (result.success) {
      // Remove from pending list
      pendingPayments.value = pendingPayments.value.filter(p => p.id !== paymentId)
      // Update stats
      await fetchStats()
      alert('Payment rejected successfully!')
    } else {
      alert('Rejection failed: ' + result.message)
    }
  } catch (error) {
    console.error('Error rejecting payment:', error)
    alert('Rejection failed. Please try again.')
  }
}

const fetchPendingPayments = async () => {
  try {
    loading.value = true
    const response = await fetch('/api/payments/admin/pending-payments') // FIXED: Correct endpoint
    const result = await response.json()
    
    if (result.success) {
      pendingPayments.value = result.data
    } else {
      console.error('Failed to fetch pending payments:', result.message)
    }
  } catch (error) {
    console.error('Error fetching pending payments:', error)
    alert('Failed to load pending payments. Please try again.')
  } finally {
    loading.value = false
  }
}

// Fetch stats
const fetchStats = async () => {
  try {
    const response = await fetch('/api/payments/admin/payment-stats') // FIXED: Correct endpoint
    const result = await response.json()
    
    if (result.success) {
      stats.value = result.data
    } else {
      console.error('Failed to fetch payment stats:', result.message)
    }
  } catch (error) {
    console.error('Error fetching payment stats:', error)
  }
}

// Initialize
onMounted(async () => {
  await Promise.all([fetchPendingPayments(), fetchStats()])
})
</script>