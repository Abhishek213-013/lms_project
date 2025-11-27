<template>
  <div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
    <!-- Sidebar - Hidden on mobile, shown on desktop -->
    <div class="hidden lg:block">
      <Sidebar />
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div v-if="mobileSidebarOpen" class="fixed inset-0 z-40 lg:hidden">
      <div class="fixed inset-0 bg-gray-600 opacity-75" @click="closeMobileMenu"></div>
      <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
        <Sidebar 
          :isMobileMenuOpen="mobileSidebarOpen"
          @close-mobile="closeMobileMenu"
          @menu-click="closeMobileMenu"
        />
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64">
      <!-- Top Navbar with Mobile Menu Button -->
      <Navbar 
        page-title="Course Teachers" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />

      <!-- Page Content -->
      <div class="p-4 lg:p-6">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8 lg:py-12">
          <div class="animate-spin rounded-full h-10 w-10 lg:h-12 lg:w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-3 lg:mt-4 text-sm lg:text-base">Loading teachers data...</p>
        </div>

        <!-- Error Display -->
        <div v-if="error && !loading" class="mb-4 lg:mb-6 p-3 lg:p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700 text-sm lg:text-base">{{ error }}</span>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="mb-4 lg:mb-6 p-3 lg:p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-green-700 text-sm lg:text-base">{{ successMessage }}</span>
          </div>
        </div>

        <div v-if="!loading && !error" class="grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6">
          <!-- Assigned Teachers -->
          <div class="bg-white rounded-lg border border-gray-200 p-4 lg:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
              <h3 class="text-lg font-semibold text-gray-800">Assigned Teacher</h3>
              <span class="px-2 lg:px-3 py-1 bg-blue-100 text-blue-800 text-xs lg:text-sm rounded-full font-medium w-fit">
                {{ assignedTeachers.length }} assigned
              </span>
            </div>
            
            <div v-if="assignedTeachers.length > 0" class="space-y-3 lg:space-y-4">
              <div 
                v-for="teacher in assignedTeachers" 
                :key="teacher.id"
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between p-3 lg:p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors space-y-3 lg:space-y-0"
              >
                <div class="flex items-center space-x-3 lg:space-x-4">
                  <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                    {{ getInitials(teacher.name) }}
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900 text-sm lg:text-base">{{ teacher.name }}</p>
                    <p class="text-xs lg:text-sm text-gray-600 truncate">{{ teacher.email }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ teacher.qualification || 'No qualification specified' }}</p>
                  </div>
                </div>
                <div class="text-left lg:text-right">
                  <p class="text-xs lg:text-sm text-gray-600">Experience</p>
                  <p class="font-medium text-gray-900 text-sm lg:text-base">{{ teacher.experience || 'N/A' }} years</p>
                  <button 
                    @click="removeTeacher(teacher.id)"
                    class="mt-1 lg:mt-2 text-red-600 hover:text-red-800 text-xs lg:text-sm font-medium transition-colors"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-6 lg:py-8">
              <svg class="w-8 h-8 lg:w-12 lg:h-12 text-gray-400 mx-auto mb-2 lg:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <p class="text-gray-600 text-sm lg:text-base">No teacher assigned to this course</p>
              <p class="text-xs lg:text-sm text-gray-500 mt-1">Assign a teacher from the available list</p>
            </div>
          </div>

          <!-- Available Teachers -->
          <div class="bg-white rounded-lg border border-gray-200 p-4 lg:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
              <h3 class="text-lg font-semibold text-gray-800">Available Teachers</h3>
              <span class="px-2 lg:px-3 py-1 bg-green-100 text-green-800 text-xs lg:text-sm rounded-full font-medium w-fit">
                {{ availableTeachers.length }} available
              </span>
            </div>
            
            <div v-if="availableTeachers.length > 0" class="space-y-3 lg:space-y-4 max-h-80 lg:max-h-96 overflow-y-auto">
              <div 
                v-for="teacher in availableTeachers" 
                :key="teacher.id"
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between p-3 lg:p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors space-y-3 lg:space-y-0"
              >
                <div class="flex items-center space-x-3 lg:space-x-4">
                  <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                    {{ getInitials(teacher.name) }}
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900 text-sm lg:text-base">{{ teacher.name }}</p>
                    <p class="text-xs lg:text-sm text-gray-600 truncate">{{ teacher.email }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ teacher.qualification || 'No qualification specified' }}</p>
                  </div>
                </div>
                <div class="text-left lg:text-right">
                  <p class="text-xs lg:text-sm text-gray-600">Experience</p>
                  <p class="font-medium text-gray-900 text-sm lg:text-base">{{ teacher.experience || 'N/A' }} years</p>
                  <button 
                    @click="assignTeacher(teacher.id)"
                    class="mt-1 lg:mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded-lg text-xs lg:text-sm font-medium transition-colors w-full lg:w-auto"
                  >
                    Assign
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-6 lg:py-8">
              <svg class="w-8 h-8 lg:w-12 lg:h-12 text-gray-400 mx-auto mb-2 lg:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <p class="text-gray-600 text-sm lg:text-base">No available teachers</p>
              <p class="text-xs lg:text-sm text-gray-500 mt-1">All teachers are currently assigned to courses</p>
            </div>
          </div>
        </div>

        <!-- Back Button -->
        <div class="mt-4 lg:mt-6 flex justify-start">
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
const mobileSidebarOpen = ref(false)

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Mobile menu functions
const toggleMobileMenu = () => {
  mobileSidebarOpen.value = !mobileSidebarOpen.value
}

const closeMobileMenu = () => {
  mobileSidebarOpen.value = false
}

// Authentication check
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    router.visit('/login')
    return
  }
}

// Fetch course teachers data
const fetchCourseTeachers = async () => {
  try {
    loading.value = true
    error.value = ''
    successMessage.value = ''
    
    console.log('📡 Fetching course teachers data for course ID:', props.courseId)
    
    // Simulate API call - replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Mock data for demonstration
    assignedTeachers.value = [
      {
        id: 1,
        name: 'Dr. Sarah Johnson',
        email: 'sarah.johnson@school.com',
        qualification: 'PhD in Mathematics',
        experience: '12 years'
      }
    ]
    
    availableTeachers.value = [
      {
        id: 2,
        name: 'Mr. David Chen',
        email: 'david.chen@school.com',
        qualification: 'M.Sc. Physics',
        experience: '8 years'
      },
      {
        id: 3,
        name: 'Ms. Emily Wilson',
        email: 'emily.wilson@school.com',
        qualification: 'M.A. Psychology',
        experience: '6 years'
      },
      {
        id: 4,
        name: 'Mrs. Maria Garcia',
        email: 'maria.garcia@school.com',
        qualification: 'M.A. English Literature',
        experience: '10 years'
      }
    ]
    
  } catch (err) {
    console.error('💥 Error fetching course teachers:', err)
    error.value = 'Failed to load teachers data. Please try again.'
  } finally {
    loading.value = false
  }
}

const assignTeacher = async (teacherId) => {
  try {
    error.value = ''
    successMessage.value = ''
    
    console.log('📡 Assigning teacher:', teacherId, 'to course:', props.courseId)
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    
    const teacher = availableTeachers.value.find(t => t.id === teacherId)
    if (teacher) {
      assignedTeachers.value.push(teacher)
      availableTeachers.value = availableTeachers.value.filter(t => t.id !== teacherId)
      successMessage.value = 'Teacher assigned successfully'
    }
    
    // Auto-hide success message after 3 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (err) {
    console.error('💥 Error assigning teacher:', err)
    error.value = 'Failed to assign teacher. Please try again.'
  }
}

const removeTeacher = async (teacherId) => {
  try {
    error.value = ''
    successMessage.value = ''
    
    console.log('📡 Removing teacher:', teacherId, 'from course:', props.courseId)
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    
    const teacher = assignedTeachers.value.find(t => t.id === teacherId)
    if (teacher) {
      availableTeachers.value.push(teacher)
      assignedTeachers.value = assignedTeachers.value.filter(t => t.id !== teacherId)
      successMessage.value = 'Teacher removed successfully'
    }
    
    // Auto-hide success message after 3 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (err) {
    console.error('💥 Error removing teacher:', err)
    error.value = 'Failed to remove teacher. Please try again.'
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