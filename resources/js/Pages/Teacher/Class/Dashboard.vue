<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <TeacherSidebar 
      :classData="classData"
      @showUploadModal="showUploadModal = true"
      @goBackToTeacherPortal="goBackToTeacherPortal"
    />

    <!-- Main Content -->
    <div class="flex-1 ml-64 min-w-0">
      <!-- Top Navbar -->
      <Navbar :pageTitle="`${classData?.name || 'Class'} - Class Dashboard`" />

      <!-- Page Content -->
      <div class="p-6 max-w-full overflow-x-hidden">
        <!-- Main Content -->
        <div class="min-h-screen bg-gray-50">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ classData?.name || 'Class' }}</h1>
              <p class="text-gray-600">{{ classData?.subject || 'Subject' }} • {{ classData?.studentCount || 0 }} students</p>
              <p class="text-sm text-gray-500">Teacher: {{ classData?.teacher_name || 'Teacher' }}</p>
            </div>
            <button 
              @click="goBackToTeacherPortal"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center space-x-1 no-underline"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              <span>Back to Teacher Portal</span>
            </button>
          </div>

          <!-- Three Main Tiles -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Give Assignment Tile -->
            <div 
              @click="navigateToAssignments"
              class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Give Assignment</h3>
                  <p class="text-sm text-gray-600">Create and manage assignments</p>
                </div>
              </div>
            </div>

            <!-- Provide Resources Tile -->
            <div 
              @click="navigateToResources"
              class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Provide Resources</h3>
                  <p class="text-sm text-gray-600">Upload and share learning materials</p>
                </div>
              </div>
            </div>

            <!-- Schedule Class Tile -->
            <div 
              @click="navigateToSchedule"
              class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Schedule Class</h3>
                  <p class="text-sm text-gray-600">Manage class schedule and timings</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Assignments</p>
                  <h3 class="text-xl font-bold text-gray-800">{{ stats?.totalAssignments || 0 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Resources</p>
                  <h3 class="text-xl font-bold text-gray-800">{{ stats?.totalResources || 0 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Scheduled</p>
                  <h3 class="text-xl font-bold text-gray-800">{{ stats?.totalSchedules || 0 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Students</p>
                  <h3 class="text-xl font-bold text-gray-800">{{ classData?.studentCount || 0 }}</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
            <div class="space-y-4">
              <div 
                v-for="activity in recentActivities" 
                :key="activity.id"
                class="flex items-center justify-between p-4 border border-gray-100 rounded-lg"
              >
                <div class="flex items-center space-x-4">
                  <div :class="`p-3 rounded-lg ${getActivityColor(activity.type)}`">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="activity.type === 'assignment'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                      <path v-else-if="activity.type === 'resource'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-medium text-gray-900">{{ activity.title }}</h4>
                    <p class="text-sm text-gray-600">{{ activity.description }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(activity.created_at) }}</p>
                  </div>
                </div>
                <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(activity.status)}`">
                  {{ activity.status }}
                </span>
              </div>
              <div v-if="!recentActivities || recentActivities.length === 0" class="text-center py-8 text-gray-500">
                No recent activity. Create your first assignment or resource!
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Upload Resource Modal -->
  <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Upload Resource</h3>
      </div>

      <form @submit.prevent="uploadResource" class="p-6 space-y-4">
        <!-- Resource Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Resource Type *</label>
          <select 
            v-model="newResource.type"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Select type</option>
            <option value="video">Video Link</option>
            <option value="pdf">PDF Document</option>
            <option value="document">Other Document</option>
            <option value="link">External Link</option>
          </select>
        </div>

        <!-- Resource Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
          <input 
            v-model="newResource.title"
            type="text" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter resource title"
          >
        </div>

        <!-- Resource Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
          <textarea 
            v-model="newResource.description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter resource description"
          ></textarea>
        </div>

        <!-- Resource Content based on type -->
        <div v-if="newResource.type === 'video'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Video URL *</label>
          <input 
            v-model="newResource.content"
            type="url" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="https://www.youtube.com/watch?v=..."
          >
        </div>

        <div v-else-if="newResource.type === 'pdf' || newResource.type === 'document'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Upload File *</label>
          <input 
            type="file"
            @change="handleFileUpload"
            accept=".pdf,.doc,.docx,.txt"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
        </div>

        <div v-else-if="newResource.type === 'link'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Link URL *</label>
          <input 
            v-model="newResource.content"
            type="url" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="https://example.com"
          >
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button 
            type="button"
            @click="showUploadModal = false"
            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors no-underline"
          >
            Cancel
          </button>
          <button 
            type="submit"
            :disabled="uploading"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed no-underline"
          >
            {{ uploading ? 'Uploading...' : 'Upload Resource' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Props from Laravel with default values
const props = defineProps({
  classData: {
    type: Object,
    default: () => ({
      id: null,
      name: 'Class',
      subject: 'Subject',
      studentCount: 0,
      teacher_name: 'Teacher',
      teacher_id: null
    })
  },
  stats: {
    type: Object,
    default: () => ({
      totalAssignments: 0,
      totalResources: 0,
      totalSchedules: 0
    })
  },
  recentActivities: {
    type: Array,
    default: () => []
  },
  teacher: {
    type: Object,
    default: () => ({
      name: 'Teacher',
      id: null
    })
  }
})

// UI State
const showUploadModal = ref(false)
const uploading = ref(false)

// Forms
const newResource = ref({
  type: '',
  title: '',
  description: '',
  content: '',
  file: null
})

// Navigation methods
const goBackToTeacherPortal = () => {
  const teacherId = props.classData?.teacher_id || props.teacher?.id
  if (teacherId) {
    router.visit(`/admin/teacher-portal/${teacherId}`)
  } else {
    router.visit('/teacher/portal')
  }
}

const navigateToAssignments = () => {
  if (props.classData?.id) {
    router.visit(`/teacher/class/${props.classData.id}/assignments`)
  }
}

const navigateToResources = () => {
  if (props.classData?.id) {
    router.visit(`/teacher/class/${props.classData.id}/resources`)
  }
}

const navigateToSchedule = () => {
  if (props.classData?.id) {
    router.visit(`/teacher/class/${props.classData.id}/schedule`)
  }
}

// Helper methods
const getActivityColor = (type) => {
  const colors = {
    assignment: 'bg-blue-500',
    resource: 'bg-green-500',
    schedule: 'bg-purple-500'
  }
  return colors[type] || 'bg-gray-500'
}

const getStatusColor = (status) => {
  const colors = {
    active: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-blue-100 text-blue-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Modal Methods
const uploadResource = async () => {
  if (!newResource.value.type || !newResource.value.title) {
    alert('Please fill in all required fields')
    return
  }
  uploading.value = true
  // Simulate upload
  setTimeout(() => {
    uploading.value = false
    showUploadModal.value = false
    alert('Resource uploaded successfully!')
    resetResourceForm()
  }, 1500)
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    newResource.value.file = file
    newResource.value.content = file.name
  }
}

const resetResourceForm = () => {
  newResource.value = {
    type: '',
    title: '',
    description: '',
    content: '',
    file: null
  }
}
</script>

<style scoped>

/* Use deep selector to override */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}
/* Remove underline from all elements */
.no-underline {
  text-decoration: none !important;
}

/* Ensure no underline appears on hover for any element */
button:hover,
a:hover {
  text-decoration: none !important;
}
</style>