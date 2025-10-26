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
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ getCategoryTitle() }}</h1>
            <p class="text-gray-600">{{ getCategoryDescription() }}</p>
            <p v-if="dataSource === 'mock'" class="text-yellow-600 text-sm mt-1">
              ⚠️ Using demonstration data
            </p>
          </div>
          <div>
            <Link 
              href="/admin/courses/categories" 
              class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center space-x-1"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              <span>Back to Categories</span>
            </Link>
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
              <p class="text-blue-600 text-sm">Showing demonstration data. Add classes to your database to see real data.</p>
            </div>
          </div>
        </div>

        <!-- Regular Classes Grid (for grade-based categories) -->
        <div v-if="currentCategory !== 'other-courses'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
          <div 
            v-for="classItem in categoryClasses" 
            :key="classItem.id"
            class="bg-white rounded-lg border border-gray-200 p-6 text-center cursor-pointer hover:shadow-lg transition-shadow duration-200 group"
            @click="viewSubjects(classItem.grade)"
          >
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
              <span class="text-2xl font-bold text-blue-600">{{ classItem.grade }}</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">{{ classItem.name }}</h3>
            <p class="text-sm text-gray-600">{{ classItem.subjectCount }} subjects</p>
            <p class="text-xs text-gray-500 mt-1">{{ classItem.studentCount }} students</p>
            
            <!-- Capacity Indicator -->
            <div class="mt-3 pt-3 border-t border-gray-100">
              <div class="flex justify-between items-center text-xs">
                <span class="text-gray-500">Capacity</span>
                <span class="font-medium" :class="getCapacityColor(classItem)">
                  {{ Math.round((classItem.studentCount / (classItem.capacity || 30)) * 100) }}%
                </span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                <div 
                  class="h-1.5 rounded-full transition-all duration-300"
                  :class="getCapacityBarColor(classItem)"
                  :style="{ width: `${Math.min((classItem.studentCount / (classItem.capacity || 30)) * 100, 100)}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Other Courses Grid (for skill-based courses) -->
        <div v-if="currentCategory === 'other-courses'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="course in otherCourses" 
            :key="course.id"
            class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 group"
            @click="viewCourseDetails(course)"
          >
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                {{ course.category || 'Skill Course' }}
              </span>
            </div>
            
            <h3 class="font-semibold text-gray-800 mb-2 text-lg group-hover:text-green-600 transition-colors">{{ course.name }}</h3>
            <p class="text-gray-600 text-sm mb-4 description-text">{{ course.description || 'No description available' }}</p>
            
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Students:</span>
                <span class="font-medium text-gray-800">{{ course.studentCount || 0 }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Capacity:</span>
                <span class="font-medium text-gray-800">{{ course.capacity || 30 }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Status:</span>
                <span class="font-medium" :class="getStatusColor(course.status)">{{ course.status || 'active' }}</span>
              </div>
            </div>

            <!-- Course Status -->
            <div class="mt-4 pt-4 border-t border-gray-100">
              <span 
                :class="`px-2 py-1 text-xs rounded-full ${getStatusBadgeColor(course.status)}`"
              >
                {{ course.status || 'active' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading courses...</p>
        </div>

        <!-- Empty State for Regular Classes -->
        <div v-if="!loading && currentCategory !== 'other-courses' && categoryClasses.length === 0 && !error" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Classes Found</h3>
          <p class="text-gray-600 mb-4">There are no classes available in this category.</p>
        </div>

        <!-- Empty State for Other Courses -->
        <div v-if="!loading && currentCategory === 'other-courses' && otherCourses.length === 0 && !error" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Skill Courses Found</h3>
          <p class="text-gray-600 mb-4">There are no skill-based courses available.</p>
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
import { router, Link } from '@inertiajs/vue3' // Import Inertia router and Link
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Define props that will be passed from Inertia controller
const props = defineProps({
  categoryClasses: {
    type: Array,
    default: () => []
  },
  otherCourses: {
    type: Array,
    default: () => []
  },
  currentCategory: {
    type: String,
    default: ''
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

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

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

// Get category title and description
const getCategoryTitle = () => {
  const titles = {
    'primary': 'Primary Education (Class 1-5)',
    'junior': 'Junior Education (Class 6-8)',
    'secondary': 'Secondary Education (Class 9-10)',
    'higher-secondary': 'Higher Secondary (Class 11-12)',
    'other-courses': 'Skill Based Courses'
  }
  return titles[props.currentCategory] || 'Category Classes'
}

const getCategoryDescription = () => {
  const descriptions = {
    'primary': 'Elementary education foundation for young learners',
    'junior': 'Middle school education and skill development',
    'secondary': 'High school preparation and career guidance',
    'higher-secondary': 'College preparation and advanced studies',
    'other-courses': 'Life skills, spoken English, and specialized courses'
  }
  return descriptions[props.currentCategory] || 'Manage classes in this category'
}

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

// Navigation functions using Inertia
const viewSubjects = (grade) => {
  console.log('Navigating to class subjects for grade:', grade)
  // Use Inertia router for navigation
  router.visit(`/admin/courses/class/${grade}/subjects`)
}

const viewCourseDetails = (course) => {
  console.log('Navigating to course details:', course)
  // Use Inertia router for navigation
  router.visit(`/admin/courses/course/${course.id}/details`)
}

const getCapacityColor = (classItem) => {
  const percentage = (classItem.studentCount / (classItem.capacity || 30)) * 100
  if (percentage >= 90) return 'text-red-600'
  if (percentage >= 75) return 'text-yellow-600'
  if (percentage >= 50) return 'text-green-600'
  return 'text-blue-600'
}

const getCapacityBarColor = (classItem) => {
  const percentage = (classItem.studentCount / (classItem.capacity || 30)) * 100
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 75) return 'bg-yellow-500'
  if (percentage >= 50) return 'bg-green-500'
  return 'bg-blue-500'
}

const getStatusColor = (status) => {
  switch (status) {
    case 'active': return 'text-green-600'
    case 'inactive': return 'text-red-600'
    case 'upcoming': return 'text-yellow-600'
    default: return 'text-gray-600'
  }
}

const getStatusBadgeColor = (status) => {
  switch (status) {
    case 'active': return 'bg-green-100 text-green-800'
    case 'inactive': return 'bg-gray-100 text-gray-800'
    case 'upcoming': return 'bg-yellow-100 text-yellow-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.description-text {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
  /* Standard property for future compatibility */
  line-clamp: 2;
}

/* Fallback for browsers that don't support line-clamp */
@supports not (line-clamp: 2) {
  .description-text {
    max-height: 2.8em;
    line-height: 1.4em;
  }
}
</style>