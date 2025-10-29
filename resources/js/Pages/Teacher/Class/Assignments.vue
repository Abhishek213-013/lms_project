<template>
  <div class="min-h-screen bg-gray-50 flex transition-colors duration-200" :class="{ 'dark bg-gray-900': isDark }">
    <!-- Sidebar -->
    <TeacherSidebar 
      @showUploadModal="showUploadModal = true"
      @createAssignment="createAssignment"
      @goBackToAdmin="goBackToAdmin"
    />

    <!-- Main Content -->
    <div class="flex-1 ml-64 min-w-0 transition-all duration-200">
      <!-- Top Navbar -->
      <Navbar 
        :pageTitle="`Assignments - ${classData.name}`"
        @toggleTheme="toggleTheme"
      />

      <!-- Page Content -->
      <div class="p-6 max-w-full overflow-x-hidden">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Assignments - {{ classData.name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">Create and manage assignments for your students</p>
          </div>
          <div class="flex space-x-3">
            <Link 
              :href="`/teacher/class/${classId}`"
              class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-200 no-underline"
            >
              ← Back to Class
            </Link>
            <button 
              @click="showCreateModal = true"
              class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 no-underline"
            >
              Create Assignment
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600 dark:text-gray-400">Loading assignments...</p>
        </div>

        <!-- Assignments List -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">All Assignments</h3>
          </div>
          
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div 
              v-for="assignment in assignments" 
              :key="assignment.id"
              class="p-6 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors duration-200"
            >
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-2">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ assignment.title }}</h4>
                    <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(assignment.status)}`">
                      {{ assignment.status }}
                    </span>
                  </div>
                  <p class="text-gray-600 dark:text-gray-400 mb-3">{{ assignment.description }}</p>
                  
                  <div class="flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      <span>Due: {{ formatDate(assignment.due_date) }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      <span>{{ assignment.submitted_count }}/{{ classData.studentCount }} submitted</span>
                    </div>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span>Points: {{ assignment.points }}</span>
                    </div>
                  </div>
                </div>
                
                <div class="flex space-x-2">
                  <button 
                    @click="viewSubmissions(assignment.id)"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-200 no-underline"
                  >
                    View Submissions
                  </button>
                  <button 
                    @click="editAssignment(assignment)"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 text-sm font-medium transition-colors duration-200 no-underline"
                  >
                    Edit
                  </button>
                  <button 
                    @click="deleteAssignment(assignment.id)"
                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium transition-colors duration-200 no-underline"
                  >
                    Delete
                  </button>
                </div>
              </div>
              
              <!-- Assignment Resources -->
              <div v-if="assignment.attachments && assignment.attachments.length > 0" class="mt-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments:</p>
                <div class="flex flex-wrap gap-2">
                  <div 
                    v-for="attachment in assignment.attachments" 
                    :key="attachment.id"
                    class="flex items-center space-x-1 px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm text-gray-600 dark:text-gray-400 transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <span>{{ attachment.name }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="assignments.length === 0" class="p-12 text-center text-gray-500 dark:text-gray-400">
              <svg class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              <p class="text-lg font-medium mb-2">No assignments yet</p>
              <p class="text-sm mb-4">Create your first assignment to get started</p>
              <button 
                @click="showCreateModal = true"
                class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 no-underline"
              >
                Create Assignment
              </button>
            </div>
          </div>
        </div>

        <!-- Create/Edit Assignment Modal -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto transition-colors duration-200">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ showEditModal ? 'Edit Assignment' : 'Create New Assignment' }}
              </h3>
            </div>

            <form @submit.prevent="saveAssignment" class="p-6 space-y-4">
              <!-- Assignment Title -->
              <div>
                <label class="block text-sm font-medium text-black dark:text-black mb-2">Assignment Title *</label>
                <input 
                  v-model="assignmentForm.title"
                  type="text" 
                  required
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-black dark:text-black transition-colors duration-200"
                  placeholder="Enter assignment title"
                >
              </div>

              <!-- Assignment Description -->
              <div>
                <label class="block text-sm font-medium text-black dark:text-black mb-2">Instructions</label>
                <textarea 
                  v-model="assignmentForm.description"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-black transition-colors duration-200"
                  placeholder="Provide instructions for the assignment..."
                ></textarea>
              </div>

              <!-- Due Date and Points -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-black dark:text-gray-300 mb-2">Due Date *</label>
                  <input 
                    v-model="assignmentForm.due_date"
                    type="datetime-local" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-black transition-colors duration-200"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-black mb-2">Points</label>
                  <input 
                    v-model="assignmentForm.points"
                    type="number" 
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-black transition-colors duration-200"
                    placeholder="100"
                  >
                </div>
              </div>

              <!-- Attachments -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments</label>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center transition-colors duration-200">
                  <input 
                    type="file"
                    @change="handleFileUpload"
                    multiple
                    class="hidden"
                    ref="fileInput"
                    id="fileInput"
                  >
                  <label for="fileInput" class="cursor-pointer no-underline">
                    <svg class="w-8 h-8 mx-auto text-gray-400 dark:text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload files or drag and drop</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">PDF, DOC, PPT, images up to 10MB each</p>
                  </label>
                </div>
                
                <!-- Uploaded files preview -->
                <div v-if="assignmentForm.attachments.length > 0" class="mt-3 space-y-2">
                  <div 
                    v-for="(file, index) in assignmentForm.attachments" 
                    :key="index"
                    class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg transition-colors duration-200"
                  >
                    <div class="flex items-center space-x-2">
                      <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                      </svg>
                      <span class="text-sm text-gray-700 dark:text-gray-300">{{ file.name }}</span>
                    </div>
                    <button 
                      @click="removeAttachment(index)"
                      type="button"
                      class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors duration-200 no-underline"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 no-underline"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="saving"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed no-underline"
                >
                  {{ saving ? 'Saving...' : (showEditModal ? 'Update Assignment' : 'Create Assignment') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, onUnmounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import apiClient from '../../../api/client'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Get classId from Inertia props
const props = defineProps({
  classId: {
    type: [String, Number],
    required: true
  },
  errors: Object,
  auth: Object,
  flash: Object
})

// ==================== THEME STATE ====================
const isDark = ref(localStorage.getItem('darkMode') === 'true')

// Current user data
const currentUser = computed(() => {
  return props.auth?.user || JSON.parse(localStorage.getItem('user') || '{}')
})

// ==================== ASSIGNMENTS STATE ====================
const classData = ref({
  id: props.classId,
  name: 'Loading...',
  studentCount: 0
})

const assignments = ref([])
const showCreateModal = ref(false)
const showEditModal = ref(false)
const saving = ref(false)
const loading = ref(true)

const assignmentForm = ref({
  id: null,
  title: '',
  description: '',
  due_date: '',
  points: 100,
  attachments: []
})

const fileInput = ref(null)

// ==================== THEME METHODS ====================

// Theme management
watch(isDark, (newVal) => {
  localStorage.setItem('darkMode', newVal)
  document.documentElement.classList.toggle('dark', newVal)
})

const toggleTheme = () => {
  isDark.value = !isDark.value
}

// Navigation methods
const navigateToSettings = () => {
  router.visit('/teacher/settings')
}

const goBackToAdmin = () => {
  router.visit('/admin/users/other-users')
}

const editProfile = () => {
  router.visit('/teacher/profile/edit')
}

const logout = async () => {
  try {
    const token = localStorage.getItem('token')
    await apiClient.post('/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('darkMode')
    router.visit('/login')
  }
}

// ==================== ASSIGNMENTS METHODS ====================

// Fetch assignments from API
const fetchAssignments = async () => {
  try {
    loading.value = true
    const response = await apiClient.get(`/assignments/class/${props.classId}`)
    
    if (response.data.success) {
      assignments.value = response.data.data
    } else {
      console.error('Failed to fetch assignments:', response.data.message)
      assignments.value = []
    }
  } catch (error) {
    console.error('Error fetching assignments:', error)
    assignments.value = []
  } finally {
    loading.value = false
  }
}

// Fetch class data
const fetchClassData = async () => {
  try {
    const response = await apiClient.get(`/courses/${props.classId}`)
    if (response.data.success) {
      classData.value = {
        id: response.data.data.id,
        name: response.data.data.name,
        studentCount: response.data.data.studentCount || 0
      }
    }
  } catch (error) {
    console.error('Error fetching class data:', error)
    classData.value = {
      id: props.classId,
      name: `Class ${props.classId}`,
      studentCount: 0
    }
  }
}

// Save assignment to API
const saveAssignment = async () => {
  saving.value = true
  try {
    const formData = {
      title: assignmentForm.value.title,
      description: assignmentForm.value.description,
      points: parseInt(assignmentForm.value.points),
      due_date: assignmentForm.value.due_date,
      attachments: assignmentForm.value.attachments.map(att => ({
        id: att.id,
        name: att.name
      }))
    }

    let response
    if (showEditModal.value) {
      response = await apiClient.put(`/assignments/${assignmentForm.value.id}/class/${props.classId}`, formData)
    } else {
      response = await apiClient.post(`/assignments/class/${props.classId}`, formData)
    }

    if (response.data.success) {
      await fetchAssignments()
      closeModal()
    } else {
      alert('Failed to save assignment: ' + response.data.message)
    }
  } catch (error) {
    console.error('Error saving assignment:', error)
    alert('Error saving assignment. Please try again.')
  } finally {
    saving.value = false
  }
}

// Delete assignment from API
const deleteAssignment = async (assignmentId) => {
  if (confirm('Are you sure you want to delete this assignment?')) {
    try {
      const response = await apiClient.delete(`/assignments/${assignmentId}/class/${props.classId}`)
      if (response.data.success) {
        await fetchAssignments()
      } else {
        alert('Failed to delete assignment: ' + response.data.message)
      }
    } catch (error) {
      console.error('Error deleting assignment:', error)
      alert('Error deleting assignment. Please try again.')
    }
  }
}

// Handle file upload
const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    assignmentForm.value.attachments.push({
      id: Date.now() + Math.random(),
      name: file.name,
      file: file
    })
  })
  event.target.value = ''
}

// Remove attachment
const removeAttachment = (index) => {
  assignmentForm.value.attachments.splice(index, 1)
}

// Edit assignment
const editAssignment = (assignment) => {
  assignmentForm.value = { 
    ...assignment,
    due_date: assignment.due_date.slice(0, 16)
  }
  showEditModal.value = true
}

// Close modal and reset form
const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  resetForm()
}

// Reset form
const resetForm = () => {
  assignmentForm.value = {
    id: null,
    title: '',
    description: '',
    due_date: '',
    points: 100,
    attachments: []
  }
}

// View submissions (placeholder)
const viewSubmissions = (assignmentId) => {
  console.log('View submissions for assignment:', assignmentId)
}

// Helper methods
const getStatusColor = (status) => {
  const colors = {
    active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
  }
  return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// ==================== LIFECYCLE ====================
onMounted(async () => {
  document.documentElement.classList.toggle('dark', isDark.value)
  
  await fetchClassData()
  await fetchAssignments()
})
</script>

<style scoped>
.rotate-180 {
  transform: rotate(180deg);
}

.submenu-link {
  display: block;
  padding: 0.5rem 0.75rem;
  color: #4b5563;
  border-radius: 0.5rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
  text-decoration: none;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
  text-decoration: none;
}

.dark .submenu-link:hover {
  background-color: #374151;
  text-decoration: none;
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

/* Custom dark mode styles */
.dark .bg-gray-750 {
  background-color: #2d3748;
}
</style>