<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Approval Statistics" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-900">Course Categories</h1>
          <p class="text-gray-600">Manage courses by educational categories</p>
          <p v-if="dataSource === 'mock'" class="text-yellow-600 text-sm mt-1">
            ⚠️ Using demonstration data
          </p>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700">{{ error }}</span>
          </div>
        </div>

        <!-- Info Display for Mock Data -->
        <div v-if="dataSource === 'mock' && !error" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
              <p class="text-blue-700 font-medium">Demo Mode</p>
              <p class="text-blue-600 text-sm">Showing demonstration data. Add classes to your database to see real data.</p>
            </div>
          </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Primary Education (Class 1-5) -->
          <div 
            class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 group"
            @click="viewCategory('primary')"
          >
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                Primary Education
              </h3>
              <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">
                Class 1-5
              </span>
            </div>
            <p class="text-gray-600 mb-4">Elementary education foundation for young learners</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                5 classes
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getStudentCount([1, 2, 3, 4, 5]) }} students
              </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Capacity Utilization</span>
                <span class="font-medium">{{ calculateCapacityPercentage([1, 2, 3, 4, 5]) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-500 ease-out"
                  :class="getCapacityColor([1, 2, 3, 4, 5])"
                  :style="{ width: `${calculateCapacityPercentage([1, 2, 3, 4, 5])}%` }"
                ></div>
              </div>
            </div>

            <!-- View Details Button -->
            <div class="mt-4 pt-4 border-t border-gray-100">
              <button class="w-full flex items-center justify-center space-x-2 text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                <span>View Classes</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Junior Education (Class 6-8) -->
          <div 
            class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 group"
            @click="viewCategory('junior')"
          >
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                Junior Education
              </h3>
              <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                Class 6-8
              </span>
            </div>
            <p class="text-gray-600 mb-4">Middle school education and skill development</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                3 classes
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getStudentCount([6, 7, 8]) }} students
              </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Capacity Utilization</span>
                <span class="font-medium">{{ calculateCapacityPercentage([6, 7, 8]) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-500 ease-out"
                  :class="getCapacityColor([6, 7, 8])"
                  :style="{ width: `${calculateCapacityPercentage([6, 7, 8])}%` }"
                ></div>
              </div>
            </div>

            <!-- View Details Button -->
            <div class="mt-4 pt-4 border-t border-gray-100">
              <button class="w-full flex items-center justify-center space-x-2 text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                <span>View Classes</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Secondary Education (Class 9-10) -->
          <div 
            class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 group"
            @click="viewCategory('secondary')"
          >
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                Secondary Education
              </h3>
              <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full font-medium">
                Class 9-10
              </span>
            </div>
            <p class="text-gray-600 mb-4">High school preparation and career guidance</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                2 classes
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getStudentCount([9, 10]) }} students
              </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Capacity Utilization</span>
                <span class="font-medium">{{ calculateCapacityPercentage([9, 10]) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-500 ease-out"
                  :class="getCapacityColor([9, 10])"
                  :style="{ width: `${calculateCapacityPercentage([9, 10])}%` }"
                ></div>
              </div>
            </div>

            <!-- View Details Button -->
            <div class="mt-4 pt-4 border-t border-gray-100">
              <button class="w-full flex items-center justify-center space-x-2 text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                <span>View Classes</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Higher Secondary Education (Class 11-12) -->
          <div 
            class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 group"
            @click="viewCategory('higher-secondary')"
          >
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                Higher Secondary
              </h3>
              <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full font-medium">
                Class 11-12
              </span>
            </div>
            <p class="text-gray-600 mb-4">College preparation and advanced studies</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                2 classes
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getStudentCount([11, 12]) }} students
              </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Capacity Utilization</span>
                <span class="font-medium">{{ calculateCapacityPercentage([11, 12]) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-500 ease-out"
                  :class="getCapacityColor([11, 12])"
                  :style="{ width: `${calculateCapacityPercentage([11, 12])}%` }"
                ></div>
              </div>
            </div>

            <!-- View Details Button -->
            <div class="mt-4 pt-4 border-t border-gray-100">
              <button class="w-full flex items-center justify-center space-x-2 text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                <span>View Classes</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Other Courses -->
          <div 
            class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 group"
            @click="viewCategory('other-courses')"
          >
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                Other Courses
              </h3>
              <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full font-medium">
                Skill Development
              </span>
            </div>
            <p class="text-gray-600 mb-4">Life skills, spoken English, and specialized courses</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                {{ otherCourses.length }} courses
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getOtherCoursesStudentCount() }} students
              </span>
            </div>
            
            <!-- Course List -->
            <div class="mt-4 space-y-2">
              <div v-for="course in otherCourses.slice(0, 3)" :key="course.id" class="flex items-center justify-between text-sm">
                <span class="text-gray-700 truncate">{{ course.name }}</span>
                <span class="text-gray-500 text-xs">{{ course.studentCount }} students</span>
              </div>
              <div v-if="otherCourses.length > 3" class="text-xs text-gray-500 text-center">
                +{{ otherCourses.length - 3 }} more courses
              </div>
            </div>

            <!-- View Details Button -->
            <div class="mt-4 pt-4 border-t border-gray-100">
              <button class="w-full flex items-center justify-center space-x-2 text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                <span>View Courses</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading categories...</p>
        </div>

        <!-- Database Setup Instructions -->
        <div v-if="!loading && dataSource === 'mock'" class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800 mb-3">Setup Your Database</h3>
          <p class="text-gray-600 mb-4">To use real data, you need to:</p>
          <div class="space-y-2 text-sm text-gray-600">
            <p>1. Run migrations: <code class="bg-gray-200 px-2 py-1 rounded font-mono text-xs">php artisan migrate</code></p>
            <p>2. Seed the database: <code class="bg-gray-200 px-2 py-1 rounded font-mono text-xs">php artisan db:seed</code></p>
            <p>3. Or manually add classes, subjects, and teachers to your database</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../../api/client'
import Sidebar from '../Layout/Sidebar.vue'
import Navbar from '../Layout/Navbar.vue'
const router = useRouter()

// Sidebar and navbar state
const activeMenu = ref('courses')
const userMenuOpen = ref(false)
const user = ref(null)
const isDark = ref(false)

// Course management state
const classes = ref([])
const otherCourses = ref([])
const loading = ref(true)
const error = ref('')
const dataSource = ref('database') // 'database' or 'mock'

// Authentication check
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    router.push('/login')
    return
  }
  
  user.value = userData
}


const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

// Check if user is super admin
const isSuperAdmin = computed(() => {
  return user.value?.role === 'super_admin'
})

// Get user initials for profile picture
const userInitials = computed(() => {
  if (!user.value?.name) return 'AD'
  return user.value.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

// Sidebar and navbar functions
const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
}

const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
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
    router.push('/login')
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Course management functions
const fetchClasses = async () => {
  try {
    loading.value = true
    error.value = ''
    dataSource.value = 'database'
    
    console.log('📡 Fetching classes from API...')
    const response = await apiClient.get('/courses/classes')
    
    console.log('✅ API Response:', response.data)
    
    if (response.data.success) {
      classes.value = response.data.data
      dataSource.value = response.data.source || 'database'
      
      // Fetch other courses separately
      await fetchOtherCourses()
      
      if (response.data.message) {
        console.log('ℹ️ API Message:', response.data.message)
      }
    } else {
      error.value = response.data.message || 'Failed to fetch classes'
      dataSource.value = 'mock'
      // Fallback to mock data on API error
      classes.value = getMockClasses()
      otherCourses.value = getMockOtherCourses()
    }
  } catch (err) {
    console.error('💥 Error fetching classes:', err)
    
    if (err.response) {
      console.error('Response details:', {
        status: err.response.status,
        data: err.response.data
      })
      error.value = err.response.data?.message || `Server error: ${err.response.status}`
    } else if (err.request) {
      console.error('No response received:', err.request)
      error.value = 'Cannot connect to server. Please check if the backend is running.'
    } else {
      console.error('Request setup error:', err.message)
      error.value = 'Request failed: ' + err.message
    }
    
    dataSource.value = 'mock'
    // Fallback to mock data
    classes.value = getMockClasses()
    otherCourses.value = getMockOtherCourses()
  } finally {
    loading.value = false
  }
}

const fetchOtherCourses = async () => {
  try {
    const response = await apiClient.get('/courses/other-courses')
    if (response.data.success) {
      otherCourses.value = response.data.data
    } else {
      otherCourses.value = getMockOtherCourses()
    }
  } catch (err) {
    console.error('Error fetching other courses:', err)
    otherCourses.value = getMockOtherCourses()
  }
}

const getMockClasses = () => {
  return Array.from({ length: 12 }, (_, i) => ({
    id: i + 1,
    grade: i + 1,
    name: `Class ${i + 1}`,
    subjectCount: Math.floor(Math.random() * 8) + 5,
    studentCount: Math.floor(Math.random() * 40) + 20,
    capacity: 30,
    teachers: []
  }))
}

const getMockOtherCourses = () => {
  return [
    { id: 1, name: 'Life Skills', studentCount: 45, capacity: 50 },
    { id: 2, name: 'Spoken English', studentCount: 38, capacity: 40 },
    { id: 3, name: 'Computer Basics', studentCount: 52, capacity: 60 },
    { id: 4, name: 'Art & Craft', studentCount: 28, capacity: 35 },
    { id: 5, name: 'Music Class', studentCount: 32, capacity: 40 }
  ]
}

// Helper functions for category calculations
const getStudentCount = (grades) => {
  const categoryClasses = classes.value.filter(cls => grades.includes(cls.grade))
  return categoryClasses.reduce((total, cls) => total + (cls.studentCount || 0), 0)
}

const getOtherCoursesStudentCount = () => {
  return otherCourses.value.reduce((total, course) => total + (course.studentCount || 0), 0)
}

const calculateCapacityPercentage = (grades) => {
  const categoryClasses = classes.value.filter(cls => grades.includes(cls.grade))
  const totalStudents = categoryClasses.reduce((total, cls) => total + (cls.studentCount || 0), 0)
  const totalCapacity = categoryClasses.length * 40 // Assuming 40 students per class
  const percentage = totalCapacity > 0 ? (totalStudents / totalCapacity) * 100 : 0
  return Math.min(Math.round(percentage), 100)
}

const getCapacityColor = (grades) => {
  const percentage = calculateCapacityPercentage(grades)
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 75) return 'bg-yellow-500'
  if (percentage >= 50) return 'bg-green-500'
  return 'bg-blue-500'
}

const viewCategory = (categoryId) => {
  console.log('Navigating to category:', categoryId)
  router.push(`/admin/courses/category/${categoryId}`)
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  checkAuthentication()
  fetchClasses()
})
</script>

<style scoped>
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