<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Course Teachers" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading teachers data...</p>
        </div>

        <!-- Error Display -->
        <div v-if="error && !loading" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700">{{ error }}</span>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-green-700">{{ successMessage }}</span>
          </div>
        </div>

        <div v-if="!loading && !error" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Assigned Teachers -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800">Assigned Teacher</h3>
              <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">
                {{ assignedTeachers.length }} assigned
              </span>
            </div>
            
            <div v-if="assignedTeachers.length > 0" class="space-y-4">
              <div 
                v-for="teacher in assignedTeachers" 
                :key="teacher.id"
                class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ getInitials(teacher.name) }}
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">{{ teacher.name }}</p>
                    <p class="text-sm text-gray-600">{{ teacher.email }}</p>
                    <p class="text-sm text-gray-500">{{ teacher.qualification || 'No qualification specified' }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm text-gray-600">Experience</p>
                  <p class="font-medium text-gray-900">{{ teacher.experience || 'N/A' }} years</p>
                  <button 
                    @click="removeTeacher(teacher.id)"
                    class="mt-2 text-red-600 hover:text-red-800 text-sm font-medium transition-colors"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <p class="text-gray-600">No teacher assigned to this course</p>
              <p class="text-sm text-gray-500 mt-1">Assign a teacher from the available list</p>
            </div>
          </div>

          <!-- Available Teachers -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800">Available Teachers</h3>
              <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                {{ availableTeachers.length }} available
              </span>
            </div>
            
            <div v-if="availableTeachers.length > 0" class="space-y-4 max-h-96 overflow-y-auto">
              <div 
                v-for="teacher in availableTeachers" 
                :key="teacher.id"
                class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ getInitials(teacher.name) }}
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">{{ teacher.name }}</p>
                    <p class="text-sm text-gray-600">{{ teacher.email }}</p>
                    <p class="text-sm text-gray-500">{{ teacher.qualification || 'No qualification specified' }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm text-gray-600">Experience</p>
                  <p class="font-medium text-gray-900">{{ teacher.experience || 'N/A' }} years</p>
                  <button 
                    @click="assignTeacher(teacher.id)"
                    class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                  >
                    Assign
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <p class="text-gray-600">No available teachers</p>
              <p class="text-sm text-gray-500 mt-1">All teachers are currently assigned to courses</p>
            </div>
          </div>
        </div>

        <!-- Back Button -->
        <div class="mt-6 flex justify-start">
          <Link 
            :href="`/admin/courses/course/${courseId}/details`" 
            class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Course Details</span>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Get courseId from Inertia props
const props = defineProps({
  courseId: {
    type: [String, Number],
    required: true
  }
})

const course = ref({})
const assignedTeachers = ref([])
const availableTeachers = ref([])
const loading = ref(true)
const error = ref('')
const successMessage = ref('')

// Authentication check
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    router.visit('/login')
    return
  }
}

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Fetch course teachers data
const fetchCourseTeachers = async () => {
  try {
    loading.value = true
    error.value = ''
    successMessage.value = ''
    
    console.log('📡 Fetching course teachers data for course ID:', props.courseId)
    
    // Get course details and assigned teachers
    const courseResponse = await apiClient.get(`/courses/${props.courseId}/teachers`)
    
    console.log('✅ Course teachers response:', courseResponse.data)
    
    if (courseResponse.data.success) {
      course.value = courseResponse.data.data.course || {}
      assignedTeachers.value = courseResponse.data.data.assignedTeachers || []
      availableTeachers.value = courseResponse.data.data.availableTeachers || []
    } else {
      error.value = courseResponse.data.message || 'Failed to fetch course teachers data'
    }
  } catch (err) {
    console.error('💥 Error fetching course teachers:', err)
    
    if (err.response) {
      console.error('Response details:', {
        status: err.response.status,
        data: err.response.data
      })
      error.value = err.response.data?.message || `Server error: ${err.response.status}`
      
      // If the specific endpoint doesn't exist, try alternative approach
      if (err.response.status === 404) {
        await fetchTeachersAlternative()
      }
    } else if (err.request) {
      console.error('No response received:', err.request)
      error.value = 'Cannot connect to server. Please check if the backend is running.'
    } else {
      console.error('Request setup error:', err.message)
      error.value = 'Request failed: ' + err.message
    }
  } finally {
    loading.value = false
  }
}

// Alternative method to fetch teachers if the specific endpoint doesn't exist
const fetchTeachersAlternative = async () => {
  try {
    console.log('🔄 Using alternative method to fetch teachers...')
    
    // Get course details
    const courseResponse = await apiClient.get(`/courses/${props.courseId}`)
    if (courseResponse.data.success) {
      course.value = courseResponse.data.data
      assignedTeachers.value = courseResponse.data.data.teachers || []
    }
    
    // Get all teachers
    const teachersResponse = await apiClient.get('/users/other-users?role=teacher')
    if (teachersResponse.data.success) {
      // Filter out already assigned teachers
      const assignedTeacherIds = assignedTeachers.value.map(t => t.id)
      availableTeachers.value = teachersResponse.data.data.filter(teacher => 
        !assignedTeacherIds.includes(teacher.id)
      )
    }
  } catch (err) {
    console.error('Error in alternative fetch method:', err)
    throw err
  }
}

const assignTeacher = async (teacherId) => {
  try {
    error.value = ''
    successMessage.value = ''
    
    console.log('📡 Assigning teacher:', teacherId, 'to course:', props.courseId)
    
    const response = await apiClient.post(`/courses/${props.courseId}/assign-teacher`, {
      teacher_id: teacherId
    })
    
    console.log('✅ Assign teacher response:', response.data)
    
    if (response.data.success) {
      successMessage.value = 'Teacher assigned successfully'
      // Refresh the data
      await fetchCourseTeachers()
      
      // Auto-hide success message after 3 seconds
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    } else {
      error.value = response.data.message || 'Failed to assign teacher'
    }
  } catch (err) {
    console.error('💥 Error assigning teacher:', err)
    
    if (err.response) {
      error.value = err.response.data?.message || `Server error: ${err.response.status}`
    } else if (err.request) {
      error.value = 'Cannot connect to server. Please check if the backend is running.'
    } else {
      error.value = 'Request failed: ' + err.message
    }
  }
}

const removeTeacher = async (teacherId) => {
  try {
    error.value = ''
    successMessage.value = ''
    
    console.log('📡 Removing teacher:', teacherId, 'from course:', props.courseId)
    
    const response = await apiClient.delete(`/courses/${props.courseId}/teacher/${teacherId}`)
    
    console.log('✅ Remove teacher response:', response.data)
    
    if (response.data.success) {
      successMessage.value = 'Teacher removed successfully'
      // Refresh the data
      await fetchCourseTeachers()
      
      // Auto-hide success message after 3 seconds
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    } else {
      error.value = response.data.message || 'Failed to remove teacher'
    }
  } catch (err) {
    console.error('💥 Error removing teacher:', err)
    
    if (err.response) {
      error.value = err.response.data?.message || `Server error: ${err.response.status}`
    } else if (err.request) {
      error.value = 'Cannot connect to server. Please check if the backend is running.'
    } else {
      error.value = 'Request failed: ' + err.message
    }
  }
}

const getInitials = (name) => {
  return name ? name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2) : 'T'
}

onMounted(() => {
  fetchCourseTeachers()
})
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
</style>