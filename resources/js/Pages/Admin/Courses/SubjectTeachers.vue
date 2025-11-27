<template>
  <div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
    <!-- Mobile Sidebar Overlay -->
    <div 
      v-if="isMobileMenuOpen"
      @click="closeMobileMenu"
      class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
    ></div>

    <!-- Sidebar -->
    <Sidebar :is-mobile-menu-open="isMobileMenuOpen" @close-mobile="closeMobileMenu" />

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64 w-full">
      <!-- Top Navbar -->
      <Navbar 
        page-title="Subject Teachers" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />

      <!-- Your page content -->
      <div class="p-4 lg:p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
          <div class="flex-1">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ subject?.name }} - Teachers</h1>
            <p class="text-gray-600 text-sm sm:text-base">Manage teacher assignments for this subject</p>
            <div class="flex flex-wrap gap-2 mt-2">
              <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                {{ subject?.className }}
              </span>
              <span v-if="subject?.type === 'regular'" class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                Class {{ grade }}
              </span>
              <span v-else class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                {{ getCourseType(subject?.category) }} Course
              </span>
            </div>
          </div>
          <div class="flex flex-col xs:flex-row gap-2 sm:gap-3">
            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium order-2 xs:order-1">
              <Link :href="`/admin/courses/class/${grade}/subjects`">← Back to Subjects</Link>
            </button>
            <button 
              @click="showAssignModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg text-sm font-medium order-1 xs:order-2 flex items-center space-x-2"
              :disabled="assignedTeachers.length > 0"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <span>Assign Teacher</span>
            </button>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-green-700 text-sm">{{ successMessage }}</span>
          </div>
          <p class="text-green-600 text-xs mt-1">
            An announcement has been generated and will appear on the home page.
          </p>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700 text-sm">{{ error }}</span>
          </div>
        </div>

        <!-- Current Teacher -->
        <div class="bg-white rounded-lg border border-gray-200 mb-6">
          <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Assigned Teacher</h3>
            <p class="text-sm text-gray-600 mt-1">
              Teacher currently assigned to {{ subject?.name }}
              <span v-if="subject?.type === 'regular'">in {{ subject?.className }}</span>
            </p>
          </div>
          <div class="p-4 sm:p-6">
            <div v-if="assignedTeachers.length > 0" class="space-y-4">
              <div 
                v-for="teacher in assignedTeachers" 
                :key="teacher.id"
                class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-gray-200 rounded-lg gap-3 hover:shadow-md transition-shadow"
              >
                <div class="flex items-center space-x-3 sm:space-x-4">
                  <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-semibold text-sm">{{ getInitials(teacher.name) }}</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <h4 class="font-medium text-gray-800 text-sm sm:text-base truncate">{{ teacher.name }}</h4>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">{{ teacher.email }}</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                      <span class="text-xs text-gray-500">Experience: {{ teacher.experience || 'Not specified' }} years</span>
                      <span v-if="teacher.qualification" class="text-xs text-blue-600 font-medium">
                        {{ teacher.qualification }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center justify-between sm:justify-end space-x-2 w-full sm:w-auto">
                  <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full whitespace-nowrap">
                    Active
                  </span>
                  <button 
                    @click="unassignTeacher(teacher.id)"
                    class="text-red-600 hover:text-red-800 text-sm font-medium whitespace-nowrap flex items-center space-x-1"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Unassign</span>
                  </button>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No teacher assigned</h3>
              <p class="text-gray-500 text-sm sm:text-base mb-4">
                Assign a teacher to {{ subject?.name }}
                <span v-if="subject?.type === 'regular'">in {{ subject?.className }}</span>
                to get started.
              </p>
            </div>
          </div>
        </div>

        <!-- Available Teachers -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Available Teachers</h3>
            <p class="text-sm text-gray-600 mt-1">Teachers available for assignment</p>
          </div>
          <div class="p-4 sm:p-6">
            <div v-if="availableTeachers.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div 
                v-for="teacher in availableTeachers" 
                :key="teacher.id"
                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
              >
                <div class="flex items-center space-x-3 mb-3">
                  <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-semibold text-xs sm:text-sm">{{ getInitials(teacher.name) }}</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <h4 class="font-medium text-gray-800 text-sm sm:text-base truncate">{{ teacher.name }}</h4>
                    <p class="text-xs text-gray-500 truncate">{{ teacher.qualification || 'No qualification specified' }}</p>
                  </div>
                </div>
                <div class="space-y-2 text-xs sm:text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Experience:</span>
                    <span class="font-medium">{{ teacher.experience || 'N/A' }} years</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium text-xs truncate ml-2">{{ teacher.email }}</span>
                  </div>
                </div>
                <button 
                  @click="assignTeacher(teacher.id)"
                  class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center space-x-2"
                  :disabled="assignedTeachers.length > 0"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  <span>Assign to Subject</span>
                </button>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <p class="text-gray-500 text-sm sm:text-base">No available teachers found.</p>
              <p class="text-gray-400 text-xs mt-2">All teachers are currently assigned to subjects.</p>
            </div>
          </div>
        </div>

        <!-- Assign Teacher Modal -->
        <div v-if="showAssignModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Assign Teacher</h3>
              <p class="text-sm text-gray-600 mt-1">
                Select a teacher to assign to {{ subject?.name }}
                <span v-if="subject?.type === 'regular'">in {{ subject?.className }}</span>
              </p>
            </div>
            <div class="p-4 sm:p-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                  <select 
                    v-model="selectedTeacher" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                  >
                    <option value="">Choose a teacher</option>
                    <option 
                      v-for="teacher in availableTeachers" 
                      :key="teacher.id" 
                      :value="teacher.id"
                      class="truncate"
                    >
                      {{ teacher.name }} - {{ teacher.qualification || 'No qualification' }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
                <button 
                  @click="showAssignModal = false" 
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm sm:text-base order-2 sm:order-1"
                >
                  Cancel
                </button>
                <button 
                  @click="confirmAssignTeacher"
                  :disabled="!selectedTeacher"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-sm sm:text-base order-1 sm:order-2 flex items-center space-x-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  <span>Assign Teacher</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Get props from Inertia
const props = defineProps({
  grade: {
    type: [String, Number],
    required: true
  },
  subjectId: {
    type: [String, Number],
    required: true
  }
})

const isMobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  console.log('🍔 Toggling mobile menu')
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  console.log('❌ Closing mobile menu')
  isMobileMenuOpen.value = false
}

// Component state
const subject = ref(null)
const assignedTeachers = ref([])
const availableTeachers = ref([])
const showAssignModal = ref(false)
const selectedTeacher = ref('')
const error = ref('')
const successMessage = ref('')
const loading = ref(true)

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Helper function to get course type name
const getCourseType = (category) => {
  const categories = {
    'life_skills': 'Life Skills',
    'spoken_english': 'Spoken English',
    'computer_basics': 'Computer Basics',
    'art_craft': 'Art & Craft',
    'music': 'Music',
    'sports': 'Sports',
    'dance': 'Dance',
    'yoga': 'Yoga & Meditation',
    'career_counseling': 'Career Counseling',
    'other': 'Skill Development'
  }
  return categories[category] || 'Skill'
}

// Component functions
const fetchSubjectData = async () => {
  try {
    loading.value = true
    error.value = ''
    successMessage.value = ''
    
    console.log('📡 Fetching subject teachers for subject ID:', props.subjectId)
    console.log('📡 Grade:', props.grade)
    
    const response = await apiClient.get(`/courses/subject/${props.subjectId}/teachers`)
    
    console.log('✅ Subject Teachers API Response:', response.data)
    
    if (response.data.success) {
      subject.value = response.data.data.subject
      assignedTeachers.value = response.data.data.assignedTeachers || []
      availableTeachers.value = response.data.data.availableTeachers || []
      
      console.log(`📊 Loaded subject: ${subject.value.name}`)
      console.log(`📊 Class name: ${subject.value.className}`)
      console.log(`📊 Assigned teachers: ${assignedTeachers.value.length}`)
      console.log(`📊 Available teachers: ${availableTeachers.value.length}`)
    } else {
      error.value = response.data.message || 'Failed to fetch subject data'
      console.error('❌ API returned error:', response.data)
    }
  } catch (err) {
    console.error('💥 Error fetching subject data:', err)
    
    if (err.response) {
      console.error('📡 Response error details:', {
        status: err.response.status,
        data: err.response.data
      })
      error.value = err.response.data?.message || `Server error: ${err.response.status}`
    } else if (err.request) {
      console.error('🌐 No response received')
      error.value = 'No response from server. Please check your connection.'
    } else {
      console.error('⚡ Request setup error:', err.message)
      error.value = 'Request failed: ' + err.message
    }
  } finally {
    loading.value = false
  }
}

const assignTeacher = async (teacherId) => {
  try {
    console.log(`📡 Assigning teacher ${teacherId} to subject ${props.subjectId}`)
    
    const response = await apiClient.post(`/courses/subject/${props.subjectId}/assign-teacher`, {
      teacher_id: teacherId
    })
    
    console.log('📡 Assign teacher response:', response.data)
    
    if (response.data.success) {
      console.log('✅ Teacher assigned successfully')
      
      // Update the local state with the response data
      if (response.data.data.assignedTeachers && response.data.data.availableTeachers) {
        assignedTeachers.value = response.data.data.assignedTeachers
        availableTeachers.value = response.data.data.availableTeachers
        
        // Get the assigned teacher info
        const assignedTeacher = assignedTeachers.value.find(t => t.id === teacherId)
        
        // Show success message with announcement info
        successMessage.value = `Teacher ${assignedTeacher?.name || ''} assigned successfully! Announcement has been generated.`
        
        // Trigger announcements refresh
        window.dispatchEvent(new CustomEvent('announcements-updated'))
        
        // Trigger parent refresh
        localStorage.setItem('teacherAssignmentUpdate', JSON.stringify({
          subjectId: props.subjectId,
          grade: props.grade,
          teacher: assignedTeacher,
          timestamp: Date.now()
        }))
        
        window.dispatchEvent(new CustomEvent('teacherAssigned', {
          detail: {
            subjectId: props.subjectId,
            grade: props.grade,
            teacher: assignedTeacher,
            timestamp: Date.now()
          }
        }))
      }
      
    } else {
      error.value = 'Failed to assign teacher: ' + response.data.message
    }
  } catch (err) {
    console.error('💥 Error assigning teacher:', err)
    console.error('💥 Error details:', err.response?.data)
    
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error assigning teacher. Please try again.'
    }
  }
}

const unassignTeacher = async (teacherId) => {
  try {
    console.log(`📡 Unassigning teacher ${teacherId} from subject ${props.subjectId}`)
    
    const response = await apiClient.delete(`/courses/subject/${props.subjectId}/teacher/${teacherId}`)
    
    if (response.data.success) {
      console.log('✅ Teacher unassigned successfully')
      
      // Find the teacher in assigned teachers
      const teacher = assignedTeachers.value.find(t => t.id === teacherId)
      if (teacher) {
        // Add back to available teachers
        availableTeachers.value.push(teacher)
        // Remove from assigned teachers
        assignedTeachers.value = assignedTeachers.value.filter(t => t.id !== teacherId)
        
        successMessage.value = `Teacher ${teacher.name} unassigned successfully!`
        
        // Trigger parent refresh
        localStorage.setItem('teacherAssignmentUpdate', JSON.stringify({
          subjectId: props.subjectId,
          grade: props.grade,
          teacherId: teacherId,
          timestamp: Date.now()
        }))
        
        window.dispatchEvent(new CustomEvent('teacherUnassigned', {
          detail: {
            subjectId: props.subjectId,
            grade: props.grade,
            teacherId: teacherId,
            timestamp: Date.now()
          }
        }))
      }
      
    } else {
      error.value = 'Failed to unassign teacher: ' + response.data.message
    }
  } catch (err) {
    console.error('💥 Error unassigning teacher:', err)
    
    if (err.response?.status === 404) {
      error.value = 'API endpoint not found. Please check the server routes.'
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error unassigning teacher. Please try again.'
    }
  }
}

const confirmAssignTeacher = () => {
  if (selectedTeacher.value) {
    assignTeacher(selectedTeacher.value)
    showAssignModal.value = false
    selectedTeacher.value = ''
  }
}

const getInitials = (name) => {
  if (!name) return '??'
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
}

onMounted(() => {
  console.log('🚀 SubjectTeachers component mounted')
  console.log('📊 Props:', { grade: props.grade, subjectId: props.subjectId })
  
  // Fetch component data
  fetchSubjectData()
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
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
}

/* Use deep selector to override */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

/* Custom breakpoint for extra small screens */
@media (min-width: 475px) {
  .xs\:flex-row {
    flex-direction: row;
  }
}
</style>