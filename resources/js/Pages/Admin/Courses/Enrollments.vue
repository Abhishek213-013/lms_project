<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Enrollments" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Enrollments</h1>
            <p class="text-gray-600">Manage student enrollments across all classes</p>
            <p v-if="dataSource === 'mock'" class="text-yellow-600 text-sm mt-1">
              ⚠️ Using demonstration data
            </p>
          </div>
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
              <p class="text-blue-600 text-sm">Showing demonstration data. Add enrollments to your database to see real data.</p>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Students</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ totalStudents }}</h3>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Across all classes</p>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Active Classes</p>
                <h3 class="text-3xl font-bold text-green-600">{{ classes.length }}</h3>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Grades 1-12</p>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Average Class Size</p>
                <h3 class="text-3xl font-bold text-purple-600">{{ averageClassSize }}</h3>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Students per class</p>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Capacity Used</p>
                <h3 class="text-3xl font-bold" :class="capacityColor">{{ capacityPercentage }}%</h3>
              </div>
              <div class="p-3 bg-orange-100 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Of total capacity</p>
          </div>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
          <div 
            v-for="classItem in classes" 
            :key="classItem.id"
            class="bg-white rounded-lg border border-gray-200 p-6 text-center cursor-pointer hover:shadow-lg transition-all duration-200 group"
            @click="viewClassEnrollments(classItem.grade)"
          >
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
              <span class="text-2xl font-bold text-blue-600">{{ classItem.grade }}</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">{{ classItem.name }}</h3>
            <p class="text-sm text-gray-600">{{ classItem.studentCount }} students</p>
            
            <!-- Enrollment Progress -->
            <div class="mt-3">
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-300"
                  :class="getClassCapacityColor(classItem)"
                  :style="{ width: `${Math.min((classItem.studentCount / 40) * 100, 100)}%` }"
                ></div>
              </div>
              <p class="text-xs text-gray-500 mt-1">{{ Math.round((classItem.studentCount / 40) * 100) }}% full</p>
            </div>

            <!-- Subjects Count -->
            <div class="mt-2 flex items-center justify-center space-x-1 text-xs text-gray-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <span>{{ classItem.subjectCount || 6 }} subjects</span>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading enrollment data...</p>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && classes.length === 0 && !error" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Classes Found</h3>
          <p class="text-gray-600 mb-4">There are no classes available in the system.</p>
        </div>

        <!-- Database Setup Instructions -->
        <div v-if="!loading && dataSource === 'mock'" class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800 mb-3">Setup Your Database</h3>
          <p class="text-gray-600 mb-4">To use real enrollment data, you need to:</p>
          <div class="space-y-2 text-sm text-gray-600">
            <p>1. Run migrations: <code class="bg-gray-200 px-2 py-1 rounded font-mono text-xs">php artisan migrate</code></p>
            <p>2. Seed the database: <code class="bg-gray-200 px-2 py-1 rounded font-mono text-xs">php artisan db:seed</code></p>
            <p>3. Add classes, students, and enrollments to your database</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3' // Import Inertia router
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Define props that will be passed from Inertia controller
const props = defineProps({
  classes: {
    type: Array,
    default: () => []
  },
  dataSource: {
    type: String,
    default: 'database'
  },
  error: {
    type: String,
    default: ''
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Local state
const activeMenu = ref('courses')
const userMenuOpen = ref(false)
const user = ref(null)
const isDark = ref(false)

// Authentication check - Remove Vue Router dependency
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    // Use Inertia router for navigation
    router.visit('/login')
    return
  }
  
  user.value = userData
}

// Check if user is super admin
const isSuperAdmin = computed(() => {
  return user.value?.role === 'super_admin'
})

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

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
    // Use Inertia for logout
    await router.post('/logout')
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    // Use Inertia router for navigation
    router.visit('/login')
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Enrollment statistics
const totalStudents = computed(() => {
  return props.classes.reduce((total, classItem) => total + classItem.studentCount, 0)
})

const averageClassSize = computed(() => {
  return props.classes.length > 0 ? Math.round(totalStudents.value / props.classes.length) : 0
})

const capacityPercentage = computed(() => {
  return Math.round((totalStudents.value / (props.classes.length * 40)) * 100)
})

const capacityColor = computed(() => {
  if (capacityPercentage.value >= 90) return 'text-red-600'
  if (capacityPercentage.value >= 75) return 'text-yellow-600'
  if (capacityPercentage.value >= 50) return 'text-green-600'
  return 'text-blue-600'
})

const getClassCapacityColor = (classItem) => {
  const percentage = (classItem.studentCount / 40) * 100
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 75) return 'bg-yellow-500'
  if (percentage >= 50) return 'bg-green-500'
  return 'bg-blue-500'
}

const viewClassEnrollments = (grade) => {
  console.log('Navigating to class enrollments for grade:', grade)
  // Use Inertia router for navigation
  router.visit(`/admin/courses/enrollments/class/${grade}`)
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
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