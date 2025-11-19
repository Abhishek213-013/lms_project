<template>
  <div class="min-h-screen bg-gray-50 flex" style="font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol' !important;">
    <!-- Mobile Menu Overlay -->
    <div 
      v-if="isMobileMenuOpen" 
      class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
      @click="closeMobileMenu"
    ></div>

    <!-- Sidebar -->
    <Sidebar 
      :is-mobile-menu-open="isMobileMenuOpen" 
      @menu-click="closeMobileMenu" 
    />

    <!-- Main Content -->
    <div class="flex-1 w-full lg:ml-64 transition-all duration-300">
      <!-- Top Navbar -->
      <Navbar 
        page-title="Super Admin Dashboard" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />
      
      <!-- Page Content -->
      <div class="p-4 lg:p-6">
        <!-- Welcome Message -->
        <div class="mb-6">
          <h2 class="mb-2 text-lg lg:text-xl">Welcome back, {{ $page.props.auth.user?.name || 'Admin' }}!</h2>
          <p class="text-gray-600 text-sm lg:text-base">Here's what's happening in your LMS today.</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <Link href="/admin/users/super-admins" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-blue-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Manage Users</h3>
                <p class="text-xs text-gray-600">View and manage all users</p>
              </div>
            </div>
          </Link>

          <Link href="/admin/courses/all-courses" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-green-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Courses</h3>
                <p class="text-xs text-gray-600">Manage courses & content</p>
              </div>
            </div>
          </Link>

          <Link href="/admin/admissions/applications" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-purple-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Admissions</h3>
                <p class="text-xs text-gray-600">Process applications</p>
              </div>
            </div>
          </Link>

          <Link href="/admin/content-management" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-indigo-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Update Text</h3>
                <p class="text-xs text-gray-600">Manage frontend content</p>
              </div>
            </div>
          </Link>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Total Students</p>
                <h3 class="text-xl lg:text-2xl font-bold text-blue-600">{{ stats.totalStudents || '2,847' }}</h3>
                <p class="text-xs text-green-600 mt-1">
                  <span class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    12.5%
                  </span>
                </p>
              </div>
              <div class="p-2 lg:p-3 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Active Teachers</p>
                <h3 class="text-xl lg:text-2xl font-bold text-green-600">{{ stats.totalTeachers || '156' }}</h3>
                <p class="text-xs text-green-600 mt-1">
                  <span class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    8.2%
                  </span>
                </p>
              </div>
              <div class="p-2 lg:p-3 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
            </div>
          </div>

          <Link href="/admin/instructor-requests/pending" class="block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 hover:border-yellow-400 transition-colors cursor-pointer">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-xs font-medium text-gray-600 mb-1">Pending Instructor Approvals</p>
                  <h3 class="text-xl lg:text-2xl font-bold text-yellow-600">{{ stats.pendingApprovals || '0' }}</h3>
                  <p class="text-xs text-yellow-600 mt-1">
                    Click to review requests
                  </p>
                </div>
                <div class="p-2 lg:p-3 bg-yellow-100 rounded-lg">
                  <svg class="w-5 h-5 lg:w-6 lg:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </Link>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Active Courses</p>
                <h3 class="text-xl lg:text-2xl font-bold text-purple-600">{{ stats.activeCourses || '89' }}</h3>
                <p class="text-xs text-green-600 mt-1">
                  <span class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    15.3%
                  </span>
                </p>
              </div>
              <div class="p-2 lg:p-3 bg-purple-100 rounded-lg">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts and Additional Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
          <!-- Recent Activity -->
          <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6">
            <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Recent Admissions Activity</h3>
            <div class="space-y-3 lg:space-y-4">
              <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center justify-between p-3 lg:p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3">
                  <div :class="`w-8 h-8 lg:w-10 lg:h-10 rounded-full flex items-center justify-center ${activity.iconBg}`">
                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path :d="activity.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 text-sm truncate">{{ activity.title }}</p>
                    <p class="text-xs text-gray-600 truncate">{{ activity.description }}</p>
                  </div>
                </div>
                <div class="text-right ml-2">
                  <p class="text-sm font-medium text-gray-800">{{ activity.status }}</p>
                  <p class="text-xs text-gray-500">{{ activity.time }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6">
            <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Quick Stats</h3>
            <div class="space-y-3 lg:space-y-4">
              <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                <span class="text-xs font-medium text-blue-800">New Students This Week</span>
                <span class="font-bold text-blue-600 text-sm">{{ stats.newStudentsThisWeek || '42' }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                <span class="text-xs font-medium text-green-800">Course Completions</span>
                <span class="font-bold text-green-600 text-sm">{{ stats.courseCompletions || '127' }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                <span class="text-xs font-medium text-yellow-800">Pending Teacher Approvals</span>
                <span class="font-bold text-yellow-600 text-sm">{{ stats.pendingTeacherApprovals || '8' }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                <span class="text-xs font-medium text-purple-800">Active Classes</span>
                <span class="font-bold text-purple-600 text-sm">{{ stats.activeClasses || '34' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import apiClient from '../../../api/client.js'
import Navbar from '../../Layout/Navbar.vue';
import Sidebar from '../../Layout/Sidebar.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue'

// Props from Laravel backend
const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  },
  recentActivities: {
    type: Array,
    default: () => []
  }
});

// Mobile menu state
const isMobileMenuOpen = ref(false)

// Mobile menu functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

// Handle escape key to close mobile menu
const handleEscape = (event) => {
  if (event.key === 'Escape' && isMobileMenuOpen.value) {
    closeMobileMenu()
  }
}

// Initialize function
const initializeApp = async () => {
  try {
    await apiClient.get('/sanctum/csrf-cookie')
    console.log('🛡️ CSRF token obtained')
    
    const response = await apiClient.get('/api/sanctum/csrf-cookie')
    console.log('✅ API connection successful')
  } catch (error) {
    console.error('❌ API initialization failed:', error)
  }
}

// Handle search
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Use provided data or fallback to mock data
const recentActivities = props.recentActivities.length > 0 ? props.recentActivities : [
  {
    id: 1,
    title: 'New Student Registration',
    description: 'Sarah Johnson applied for Web Development',
    status: 'Pending',
    time: '10 min ago',
    icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
    iconBg: 'bg-blue-500'
  },
  {
    id: 2,
    title: 'Teacher Application',
    description: 'Dr. Michael Brown - Computer Science',
    status: 'Approved',
    time: '1 hour ago',
    icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    iconBg: 'bg-green-500'
  },
  {
    id: 3,
    title: 'Course Enrollment',
    description: '25 students enrolled in Python Programming',
    status: 'Completed',
    time: '2 hours ago',
    icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    iconBg: 'bg-purple-500'
  }
];

// Initialize when component mounts
onMounted(() => {
  initializeApp()
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>

<style scoped>
/* Use deep selector to override */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 300;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}
</style>