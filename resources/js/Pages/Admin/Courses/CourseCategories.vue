<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Course Categories" @search="handleSearch" />

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
                {{ getCategoryName('primary') }}
              </h3>
              <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">
                {{ getCategoryGrades('primary') }}
              </span>
            </div>
            <p class="text-gray-600 mb-4">{{ getCategoryDescription('primary') }}</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                {{ getCategoryClassCount('primary') }} classes
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getCategoryStudentCount('primary') }} students
              </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Capacity Utilization</span>
                <span class="font-medium">{{ calculateCategoryCapacityPercentage('primary') }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-500 ease-out"
                  :class="getCapacityColor(calculateCategoryCapacityPercentage('primary'))"
                  :style="{ width: `${calculateCategoryCapacityPercentage('primary')}%` }"
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
                {{ getCategoryName('junior') }}
              </h3>
              <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                {{ getCategoryGrades('junior') }}
              </span>
            </div>
            <p class="text-gray-600 mb-4">{{ getCategoryDescription('junior') }}</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                {{ getCategoryClassCount('junior') }} classes
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getCategoryStudentCount('junior') }} students
              </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Capacity Utilization</span>
                <span class="font-medium">{{ calculateCategoryCapacityPercentage('junior') }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-500 ease-out"
                  :class="getCapacityColor(calculateCategoryCapacityPercentage('junior'))"
                  :style="{ width: `${calculateCategoryCapacityPercentage('junior')}%` }"
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
                {{ getCategoryName('secondary') }}
              </h3>
              <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full font-medium">
                {{ getCategoryGrades('secondary') }}
              </span>
            </div>
            <p class="text-gray-600 mb-4">{{ getCategoryDescription('secondary') }}</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                {{ getCategoryClassCount('secondary') }} classes
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getCategoryStudentCount('secondary') }} students
              </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Capacity Utilization</span>
                <span class="font-medium">{{ calculateCategoryCapacityPercentage('secondary') }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-500 ease-out"
                  :class="getCapacityColor(calculateCategoryCapacityPercentage('secondary'))"
                  :style="{ width: `${calculateCategoryCapacityPercentage('secondary')}%` }"
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
                {{ getCategoryName('higher-secondary') }}
              </h3>
              <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full font-medium">
                {{ getCategoryGrades('higher-secondary') }}
              </span>
            </div>
            <p class="text-gray-600 mb-4">{{ getCategoryDescription('higher-secondary') }}</p>
            <div class="flex justify-between text-sm text-gray-500 mb-2">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                {{ getCategoryClassCount('higher-secondary') }} classes
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ getCategoryStudentCount('higher-secondary') }} students
              </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Capacity Utilization</span>
                <span class="font-medium">{{ calculateCategoryCapacityPercentage('higher-secondary') }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full transition-all duration-500 ease-out"
                  :class="getCapacityColor(calculateCategoryCapacityPercentage('higher-secondary'))"
                  :style="{ width: `${calculateCategoryCapacityPercentage('higher-secondary')}%` }"
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

        <!-- Empty State -->
        <div v-if="!loading && categories.length === 0 && !error" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Categories Found</h3>
          <p class="text-gray-600 mb-4">There are no course categories available.</p>
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
import { router } from '@inertiajs/vue3'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Define props that will be passed from Inertia controller
const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  otherCourses: {
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
  }
})

// Local state
const loading = ref(false)
const activeMenu = ref('courses')
const userMenuOpen = ref(false)
const user = ref(null)
const isDark = ref(false)

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

// Authentication check
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
    // You might want to use Inertia for logout too
    await router.post('/logout')
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.visit('/login')
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Helper functions for category data
const getCategoryData = (categoryId) => {
  return props.categories.find(cat => cat.id === categoryId) || {}
}

const getCategoryName = (categoryId) => {
  const category = getCategoryData(categoryId)
  return category.name || categoryId.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const getCategoryGrades = (categoryId) => {
  const category = getCategoryData(categoryId)
  return category.grades || 'Class 1-12'
}

const getCategoryDescription = (categoryId) => {
  const category = getCategoryData(categoryId)
  return category.description || 'Educational program for students'
}

const getCategoryClassCount = (categoryId) => {
  const category = getCategoryData(categoryId)
  return category.classCount || 0
}

const getCategoryStudentCount = (categoryId) => {
  const category = getCategoryData(categoryId)
  return category.studentCount || 0
}

const getOtherCoursesStudentCount = () => {
  return props.otherCourses.reduce((total, course) => total + (course.studentCount || 0), 0)
}

const calculateCategoryCapacityPercentage = (categoryId) => {
  const studentCount = getCategoryStudentCount(categoryId)
  const classCount = getCategoryClassCount(categoryId)
  const totalCapacity = classCount * 40 // Assuming 40 students per class
  const percentage = totalCapacity > 0 ? (studentCount / totalCapacity) * 100 : 0
  return Math.min(Math.round(percentage), 100)
}

const getCapacityColor = (percentage) => {
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 75) return 'bg-yellow-500'
  if (percentage >= 50) return 'bg-green-500'
  return 'bg-blue-500'
}

const viewCategory = (categoryId) => {
  console.log('Navigating to category:', categoryId)
  if (categoryId === 'other-courses') {
    // Use Inertia router for navigation
    router.visit('/admin/courses/skill-courses')
  } else {
    // Use Inertia router for navigation
    router.visit(`/admin/courses/category/${categoryId}`)
  }
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