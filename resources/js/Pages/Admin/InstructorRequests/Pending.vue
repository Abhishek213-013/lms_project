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
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
                  >
                    Approve
                  </button>
                  <button 
                    @click="openRejectModal(request)"
                    :disabled="processing"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
                  >
                    Reject
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
                <!-- Removed bio section since it doesn't exist -->
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

const approveRequest = async (requestId) => {
  if (!confirm('Are you sure you want to approve this instructor?')) return
  
  processing.value = true
  try {
    const response = await fetch(`/api/instructor-requests/${requestId}/approve`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    
    const data = await response.json()
    
    if (data.success) {
      // Remove from list
      requests.value = requests.value.filter(req => req.id !== requestId)
      // Update stats
      stats.value.pending = (stats.value.pending || 1) - 1
      stats.value.approved = (stats.value.approved || 0) + 1
      alert('Instructor approved successfully!')
    } else {
      alert('Failed to approve instructor: ' + data.message)
    }
  } catch (error) {
    alert('Error approving instructor: ' + error.message)
  } finally {
    processing.value = false
  }
}

const openRejectModal = (request) => {
  selectedRequest.value = request
  rejectionReason.value = ''
  rejectModalVisible.value = true
}

const closeRejectModal = () => {
  rejectModalVisible.value = false
  selectedRequest.value = null
  rejectionReason.value = ''
}

const rejectRequest = async () => {
  if (!rejectionReason.value.trim()) {
    alert('Please provide a rejection reason')
    return
  }
  
  processing.value = true
  try {
    const response = await fetch(`/api/instructor-requests/${selectedRequest.value.id}/reject`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        rejection_reason: rejectionReason.value
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      // Remove from list
      requests.value = requests.value.filter(req => req.id !== selectedRequest.value.id)
      // Update stats
      stats.value.pending = (stats.value.pending || 1) - 1
      stats.value.rejected = (stats.value.rejected || 0) + 1
      closeRejectModal()
      alert('Instructor application rejected!')
    } else {
      alert('Failed to reject application: ' + data.message)
    }
  } catch (error) {
    alert('Error rejecting application: ' + error.message)
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  // Refresh stats periodically
  setInterval(async () => {
    try {
      const response = await fetch('/api/instructor-requests/stats')
      const data = await response.json()
      if (data.success) {
        stats.value = data.data
      }
    } catch (error) {
      console.error('Error fetching stats:', error)
    }
  }, 30000) // Every 30 seconds
})
</script>