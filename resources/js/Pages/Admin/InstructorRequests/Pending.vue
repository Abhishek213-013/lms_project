<!-- resources/js/Pages/Admin/InstructorRequests/Pending.vue -->
<template>
  <div class="min-h-screen bg-gray-50 flex">
    <Sidebar />
    <div class="flex-1 ml-64">
      <Navbar page-title="Pending Instructor Requests" />
      
      <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Pending Instructor Requests</h1>
          <p class="text-gray-600">Review and manage instructor applications.</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ stats.pending || 0 }}</div>
              <div class="text-sm text-gray-600">Pending</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ stats.approved || 0 }}</div>
              <div class="text-sm text-gray-600">Approved</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="text-center">
              <div class="text-2xl font-bold text-red-600">{{ stats.rejected || 0 }}</div>
              <div class="text-sm text-gray-600">Rejected</div>
            </div>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">{{ stats.total_requests || 0 }}</div>
              <div class="text-sm text-gray-600">Total Requests</div>
            </div>
          </div>
        </div>

        <!-- Error Alert -->
        <div v-if="errorMessage" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Error</h3>
              <div class="text-sm text-red-700 mt-1">{{ errorMessage }}</div>
            </div>
          </div>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Pending Applications</h3>
          </div>
          
          <div v-if="loading" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Loading requests...</p>
          </div>

          <div v-else-if="requests.length === 0" class="p-8 text-center">
            <div class="text-gray-400 mb-4">
              <i class="fas fa-users fa-3x"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-900 mb-2">No Pending Requests</h4>
            <p class="text-gray-600">All instructor applications have been processed.</p>
          </div>

          <div v-else class="divide-y divide-gray-200">
            <div v-for="request in requests" :key="request.id" class="p-6">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h4 class="text-lg font-semibold text-gray-900">{{ request.name }}</h4>
                  <p class="text-gray-600">{{ request.email }}</p>
                  <p class="text-sm text-gray-500">Applied on {{ formatDate(request.created_at) }}</p>
                </div>
                <div class="flex space-x-2">
                  <button 
                    @click="approveRequest(request.id)"
                    :disabled="processing"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 flex items-center space-x-2"
                  >
                    <i class="fas fa-check"></i>
                    <span>Approve</span>
                  </button>
                  <button 
                    @click="openRejectModal(request)"
                    :disabled="processing"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 flex items-center space-x-2"
                  >
                    <i class="fas fa-times"></i>
                    <span>Reject</span>
                  </button>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                  <strong>Username:</strong> {{ request.username }}
                </div>
                <div>
                  <strong>Date of Birth:</strong> {{ formatDate(request.dob) }}
                </div>
                <div>
                  <strong>Qualification:</strong> {{ getQualificationLabel(request.education_qualification) }}
                </div>
                <div>
                  <strong>Institute:</strong> {{ request.institute }}
                </div>
                <div v-if="request.experience" class="md:col-span-2">
                  <strong>Experience:</strong> {{ request.experience }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="rejectModalVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold mb-4">Reject Instructor Application</h3>
        <p class="text-gray-600 mb-4">Please provide a reason for rejecting {{ selectedRequest?.name }}'s application.</p>
        
        <textarea 
          v-model="rejectionReason"
          placeholder="Enter rejection reason..."
          class="w-full p-3 border border-gray-300 rounded-lg mb-4"
          rows="4"
        ></textarea>
        
        <div class="flex justify-end space-x-3">
          <button 
            @click="closeRejectModal"
            class="px-4 py-2 text-gray-600 hover:text-gray-800"
          >
            Cancel
          </button>
          <button 
            @click="rejectRequest"
            :disabled="!rejectionReason.trim() || processing"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
          >
            Reject Application
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '../../Layout/Navbar.vue'
import Sidebar from '../../Layout/Sidebar.vue'

const props = defineProps({
  pendingRequests: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  }
})

const requests = ref(props.pendingRequests)
const stats = ref(props.stats)
const loading = ref(false)
const processing = ref(false)
const rejectModalVisible = ref(false)
const selectedRequest = ref(null)
const rejectionReason = ref('')
const errorMessage = ref('')

const qualifications = {
  'HSC': 'Higher Secondary Certificate',
  'BSC': 'Bachelor of Science',
  'BA': 'Bachelor of Arts',
  'MA': 'Master of Arts',
  'MSC': 'Master of Science',
  'PhD': 'Doctor of Philosophy',
  'Other': 'Other Qualification'
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not specified'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getQualificationLabel = (qualification) => {
  return qualifications[qualification] || qualification || 'Not specified'
}

// Improved API request handler
const makeApiRequest = async (url, options = {}) => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  
  const defaultOptions = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'X-Requested-With': 'XMLHttpRequest'
    },
    credentials: 'same-origin'
  }

  const mergedOptions = {
    ...defaultOptions,
    ...options,
    headers: {
      ...defaultOptions.headers,
      ...options.headers
    }
  }

  try {
    const response = await fetch(url, mergedOptions)
    
    // Check if response is HTML instead of JSON
    const contentType = response.headers.get('content-type')
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text()
      if (text.startsWith('<!DOCTYPE') || text.startsWith('<html')) {
        throw new Error('Server returned HTML instead of JSON. This might be an authentication issue or route problem.')
      }
      throw new Error(`Unexpected response format: ${contentType}`)
    }

    const data = await response.json()
    
    if (!response.ok) {
      throw new Error(data.message || `HTTP error! status: ${response.status}`)
    }
    
    return data
  } catch (error) {
    console.error('API request failed:', error)
    throw error
  }
}

const approveRequest = async (requestId) => {
  if (!confirm('Are you sure you want to approve this instructor?')) return
  
  processing.value = true
  errorMessage.value = ''
  
  try {
    const data = await makeApiRequest(`/api/instructor-requests/${requestId}/approve`, {
      method: 'POST'
    })
    
    if (data.success) {
      // Remove from list
      requests.value = requests.value.filter(req => req.id !== requestId)
      // Update stats
      stats.value.pending = (stats.value.pending || 1) - 1
      stats.value.approved = (stats.value.approved || 0) + 1
      alert('Instructor approved successfully!')
    } else {
      errorMessage.value = data.message || 'Failed to approve instructor request'
    }
  } catch (error) {
    errorMessage.value = error.message || 'Failed to approve instructor request. Please check your authentication and try again.'
    console.error('Approve request error:', error)
  } finally {
    processing.value = false
  }
}

const openRejectModal = (request) => {
  selectedRequest.value = request
  rejectionReason.value = ''
  rejectModalVisible.value = true
  errorMessage.value = ''
}

const closeRejectModal = () => {
  rejectModalVisible.value = false
  selectedRequest.value = null
  rejectionReason.value = ''
}

const rejectRequest = async () => {
  if (!rejectionReason.value.trim()) {
    errorMessage.value = 'Please provide a rejection reason'
    return
  }
  
  processing.value = true
  errorMessage.value = ''
  
  try {
    const data = await makeApiRequest(`/api/instructor-requests/${selectedRequest.value.id}/reject`, {
      method: 'POST',
      body: JSON.stringify({
        rejection_reason: rejectionReason.value
      })
    })
    
    if (data.success) {
      // Remove from list
      requests.value = requests.value.filter(req => req.id !== selectedRequest.value.id)
      // Update stats
      stats.value.pending = (stats.value.pending || 1) - 1
      stats.value.rejected = (stats.value.rejected || 0) + 1
      closeRejectModal()
      alert('Instructor application rejected!')
    } else {
      errorMessage.value = data.message || 'Failed to reject application'
    }
  } catch (error) {
    errorMessage.value = error.message || 'Failed to reject application. Please check your authentication and try again.'
    console.error('Reject request error:', error)
  } finally {
    processing.value = false
  }
}

const refreshStats = async () => {
  try {
    const data = await makeApiRequest('/api/instructor-requests/stats')
    if (data.success) {
      stats.value = data.data
    }
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

onMounted(() => {
  // Refresh stats periodically
  setInterval(refreshStats, 30000) // Every 30 seconds
})
</script>