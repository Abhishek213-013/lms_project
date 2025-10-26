<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Course Details" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading course details...</p>
        </div>

        <!-- Error Display -->
        <div v-if="error && !loading" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700">{{ error }}</span>
          </div>
          <div class="mt-2">
            <button 
              @click="fetchCourseDetails"
              class="text-red-700 hover:text-red-900 text-sm font-medium underline"
            >
              Try Again
            </button>
          </div>
        </div>

        <!-- Course Content -->
        <div v-if="!loading && !error">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ course.name }}</h1>
              <p class="text-gray-600">{{ course.description || 'Course details and management' }}</p>
              <div class="flex items-center space-x-4 mt-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                  {{ course.category || 'General' }}
                </span>
                <span :class="`px-3 py-1 text-sm rounded-full ${
                  course.status === 'active' 
                    ? 'bg-green-100 text-green-800' 
                    : course.status === 'inactive'
                    ? 'bg-red-100 text-red-800'
                    : course.status === 'upcoming'
                    ? 'bg-yellow-100 text-yellow-800'
                    : 'bg-gray-100 text-gray-800'
                }`">
                  {{ course.status || 'active' }}
                </span>
              </div>
            </div>
            <div class="flex space-x-3">
              <Link 
                href="/admin/courses/all-courses" 
                class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center space-x-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Courses</span>
              </Link>
              <button 
                @click="editCourse"
                class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Course</span>
              </button>
            </div>
          </div>

          <!-- Stats Overview -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-4">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Total Students</p>
                  <h3 class="text-2xl font-bold text-gray-800">{{ course.studentCount || 0 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-4">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Assigned Teachers</p>
                  <h3 class="text-2xl font-bold text-gray-800">{{ course.teachers ? course.teachers.length : 0 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-4">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Course Capacity</p>
                  <h3 class="text-2xl font-bold text-gray-800">{{ course.capacity || 30 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg mr-4">
                  <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Available Spots</p>
                  <h3 class="text-2xl font-bold text-gray-800">{{ Math.max(0, (course.capacity || 30) - (course.studentCount || 0)) }}</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Course Information -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Course Details -->
            <div class="lg:col-span-2">
              <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Course Information</h3>
                <div class="space-y-4">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="text-sm font-medium text-gray-600">Course Name</label>
                      <p class="text-gray-900">{{ course.name }}</p>
                    </div>
                    <div>
                      <label class="text-sm font-medium text-gray-600">Category</label>
                      <p class="text-gray-900">{{ course.category || 'N/A' }}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="text-sm font-medium text-gray-600">Course Code</label>
                      <p class="text-gray-900">{{ course.code || 'N/A' }}</p>
                    </div>
                    <div>
                      <label class="text-sm font-medium text-gray-600">Status</label>
                      <p class="text-gray-900 capitalize">{{ course.status || 'active' }}</p>
                    </div>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-600">Description</label>
                    <p class="text-gray-900 mt-1">{{ course.description || 'No description available.' }}</p>
                  </div>
                </div>
              </div>

              <!-- Assigned Teachers -->
              <div class="bg-white rounded-lg border border-gray-200 p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-semibold text-gray-800">Assigned Teachers</h3>
                  <button 
                    @click="manageTeachers"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                  >
                    Manage Teachers
                  </button>
                </div>
                <div v-if="course.teachers && course.teachers.length > 0" class="space-y-4">
                  <div 
                    v-for="teacher in course.teachers" 
                    :key="teacher.id"
                    class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <div class="flex items-center space-x-4">
                      <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ getInitials(teacher.name) }}
                      </div>
                      <div>
                        <p class="font-medium text-gray-900">{{ teacher.name }}</p>
                        <p class="text-sm text-gray-600">{{ teacher.email }}</p>
                        <p class="text-xs text-gray-500">{{ teacher.qualification || 'No qualification specified' }}</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-gray-600">Experience</p>
                      <p class="font-medium text-gray-900">{{ teacher.experience || 'N/A' }} years</p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-8">
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                  </svg>
                  <p class="text-gray-600">No teachers assigned to this course</p>
                  <button 
                    @click="manageTeachers"
                    class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
                  >
                    Assign a teacher
                  </button>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6">
              <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                  <button 
                    @click="manageTeachers"
                    class="w-full flex items-center space-x-3 p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <div class="p-2 bg-blue-100 rounded-lg">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">Manage Teachers</p>
                      <p class="text-sm text-gray-600">Assign or remove teachers</p>
                    </div>
                  </button>
                  
                  <button 
                    @click="viewEnrollments"
                    class="w-full flex items-center space-x-3 p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <div class="p-2 bg-green-100 rounded-lg">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">View Enrollments</p>
                      <p class="text-sm text-gray-600">Manage student enrollments</p>
                    </div>
                  </button>
                  
                  <button 
                    @click="editCourse"
                    class="w-full flex items-center space-x-3 p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <div class="p-2 bg-purple-100 rounded-lg">
                      <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">Edit Course</p>
                      <p class="text-sm text-gray-600">Update course information</p>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Course Status -->
              <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Course Status</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Students Enrolled</span>
                    <span class="font-medium text-gray-900">{{ course.studentCount || 0 }}/{{ course.capacity || 30 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Capacity</span>
                    <span class="font-medium text-gray-900" :class="getCapacityColor()">
                      {{ Math.round(((course.studentCount || 0) / (course.capacity || 30)) * 100) }}%
                    </span>
                  </div>
                  <div class="pt-3 border-t border-gray-200">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div 
                        class="h-2 rounded-full transition-all duration-300"
                        :class="getCapacityBarColor()"
                        :style="{ width: `${Math.min(Math.round(((course.studentCount || 0) / (course.capacity || 30)) * 100), 100)}%` }"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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

// Get props from Inertia
const props = defineProps({
  courseId: {
    type: [String, Number],
    required: true
  }
})

const course = ref({})
const loading = ref(true)
const error = ref('')

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

// Fetch course details
const fetchCourseDetails = async () => {
  try {
    loading.value = true
    error.value = ''
    
    console.log('📡 Fetching course details for ID:', props.courseId)
    const response = await apiClient.get(`/courses/${props.courseId}`)
    
    console.log('✅ Course details response:', response.data)
    
    if (response.data.success) {
      course.value = response.data.data
    } else {
      error.value = response.data.message || 'Failed to fetch course details'
      // Fallback to mock data
      course.value = getMockCourseData()
    }
  } catch (err) {
    console.error('💥 Error fetching course details:', err)
    
    if (err.response) {
      console.error('Response details:', {
        status: err.response.status,
        data: err.response.data
      })
      
      if (err.response.status === 500) {
        error.value = 'Server error. The course might not exist or there might be a database issue.'
      } else if (err.response.status === 404) {
        error.value = 'Course not found. It may have been deleted or does not exist.'
      } else {
        error.value = err.response.data?.message || `Server error: ${err.response.status}`
      }
    } else if (err.request) {
      console.error('No response received:', err.request)
      error.value = 'Cannot connect to server. Please check if the backend is running.'
    } else {
      console.error('Request setup error:', err.message)
      error.value = 'Request failed: ' + err.message
    }
    
    // Fallback to mock data for development
    console.log('🔄 Using fallback mock data')
    course.value = getMockCourseData()
  } finally {
    loading.value = false
  }
}

const getMockCourseData = () => {
  // Enhanced mock data that matches your database structure
  const mockCourses = {
    1: {
      id: 1,
      name: 'Mathematics',
      description: 'Advanced mathematics course covering algebra, geometry, and calculus.',
      category: 'Academic',
      code: 'MATH101',
      status: 'active',
      studentCount: 35,
      capacity: 40,
      type: 'regular',
      grade: 10,
      teachers: [
        {
          id: 1,
          name: 'Dr. Sarah Johnson',
          email: 'sarah.johnson@school.com',
          qualification: 'PhD in Mathematics',
          experience: '12 years'
        }
      ]
    },
    2: {
      id: 2,
      name: 'Science',
      description: 'Comprehensive science education covering physics, chemistry, and biology.',
      category: 'Academic',
      code: 'SCI201',
      status: 'active',
      studentCount: 28,
      capacity: 35,
      type: 'regular',
      grade: 9,
      teachers: [
        {
          id: 2,
          name: 'Mr. David Chen',
          email: 'david.chen@school.com',
          qualification: 'M.Sc. Physics',
          experience: '8 years'
        }
      ]
    },
    3: {
      id: 3,
      name: 'Life Skills Development',
      description: 'Comprehensive life skills training including communication, problem-solving, and emotional intelligence.',
      category: 'Life Skills',
      code: 'LS101',
      status: 'active',
      studentCount: 25,
      capacity: 30,
      type: 'other',
      teachers: [
        {
          id: 3,
          name: 'Ms. Emily Wilson',
          email: 'emily.wilson@school.com',
          qualification: 'M.A. Psychology',
          experience: '6 years'
        }
      ]
    },
    4: {
      id: 4,
      name: 'Spoken English Mastery',
      description: 'Improve English speaking fluency, pronunciation, and conversational skills.',
      category: 'Language',
      code: 'ENG401',
      status: 'active',
      studentCount: 32,
      capacity: 40,
      type: 'other',
      teachers: [
        {
          id: 4,
          name: 'Mrs. Maria Garcia',
          email: 'maria.garcia@school.com',
          qualification: 'M.A. English Literature',
          experience: '10 years'
        }
      ]
    }
  }
  
  return mockCourses[props.courseId] || {
    id: props.courseId,
    name: 'Unknown Course',
    description: 'Course details not available.',
    category: 'General',
    code: 'N/A',
    status: 'unknown',
    studentCount: 0,
    capacity: 0,
    teachers: []
  }
}

const manageTeachers = () => {
  if (course.value.id) {
    router.visit(`/admin/courses/course/${course.value.id}/teachers`)
  } else {
    console.error('Course ID not available')
  }
}

const viewEnrollments = () => {
  if (course.value.id) {
    // You might need to create this route or use existing one
    router.visit(`/admin/courses/enrollments`)
  } else {
    console.error('Course ID not available')
  }
}

const editCourse = () => {
  if (course.value.id) {
    // You might need to create an edit route
    alert('Edit course functionality would go here')
    // router.visit(`/admin/courses/course/${course.value.id}/edit`)
  } else {
    console.error('Course ID not available')
  }
}

const getInitials = (name) => {
  return name ? name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2) : 'T'
}

const getCapacityColor = () => {
  const percentage = ((course.value.studentCount || 0) / (course.value.capacity || 30)) * 100
  if (percentage >= 90) return 'text-red-600'
  if (percentage >= 75) return 'text-yellow-600'
  if (percentage >= 50) return 'text-green-600'
  return 'text-blue-600'
}

const getCapacityBarColor = () => {
  const percentage = ((course.value.studentCount || 0) / (course.value.capacity || 30)) * 100
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 75) return 'bg-yellow-500'
  if (percentage >= 50) return 'bg-green-500'
  return 'bg-blue-500'
}

onMounted(() => {
  console.log('🚀 CourseDetails component mounted')
  console.log('📊 Course ID:', props.courseId)
  
  fetchCourseDetails()
})
</script>

<style scoped>
/* Ensure proper styling for all elements */
.submenu-link {
  display: block;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  color: #4b5563;
  border-radius: 0.5rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
}

.rotate-180 {
  transform: rotate(180deg);
}
</style>