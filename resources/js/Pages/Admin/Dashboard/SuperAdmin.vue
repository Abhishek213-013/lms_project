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
      <!-- Welcome Message -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome back, {{ $page.props.auth.user?.name || 'Admin' }}!</h1>
        <p class="text-gray-600">Here's what's happening in your LMS today.</p>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <Link href="/admin/users/super-admins" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-blue-500 transition-colors text-left block">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Manage Users</h3>
              <p class="text-sm text-gray-600">View and manage all users</p>
            </div>
          </div>
        </Link>

        <Link href="/admin/courses/all-courses" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-green-500 transition-colors text-left block">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Courses</h3>
              <p class="text-sm text-gray-600">Manage courses & content</p>
            </div>
          </div>
        </Link>

        <Link href="/admin/admissions/applications" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-purple-500 transition-colors text-left block">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Admissions</h3>
              <p class="text-sm text-gray-600">Process applications</p>
            </div>
          </div>
        </Link>

        <Link href="/admin/reports" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-orange-500 transition-colors text-left block">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-orange-100 rounded-lg">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">Reports</h3>
              <p class="text-sm text-gray-600">View system reports</p>
            </div>
          </div>
        </Link>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">Total Students</p>
              <h3 class="text-3xl font-bold text-blue-600">{{ stats.totalStudents || '2,847' }}</h3>
              <p class="text-xs text-green-600 mt-1">
                <span class="inline-flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                  </svg>
                  12.5%
                </span>
              </p>
            </div>
            <div class="p-3 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">Active Teachers</p>
              <h3 class="text-3xl font-bold text-green-600">{{ stats.totalTeachers || '156' }}</h3>
              <p class="text-xs text-green-600 mt-1">
                <span class="inline-flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                  </svg>
                  8.2%
                </span>
              </p>
            </div>
            <div class="p-3 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">Pending Approvals</p>
              <h3 class="text-3xl font-bold text-yellow-600">{{ stats.pendingApprovals || '23' }}</h3>
              <p class="text-xs text-red-600 mt-1">
                <span class="inline-flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                  </svg>
                  5.1%
                </span>
              </p>
            </div>
            <div class="p-3 bg-yellow-100 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">Active Courses</p>
              <h3 class="text-3xl font-bold text-purple-600">{{ stats.activeCourses || '89' }}</h3>
              <p class="text-xs text-green-600 mt-1">
                <span class="inline-flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                  </svg>
                  15.3%
                </span>
              </p>
            </div>
            <div class="p-3 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts and Additional Content -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Admissions Activity</h3>
          <div class="space-y-4">
            <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
              <div class="flex items-center space-x-4">
                <div :class="`w-10 h-10 rounded-full flex items-center justify-center ${activity.iconBg}`">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path :d="activity.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-800">{{ activity.title }}</p>
                  <p class="text-sm text-gray-600">{{ activity.description }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-medium text-gray-800">{{ activity.status }}</p>
                <p class="text-xs text-gray-500">{{ activity.time }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Stats</h3>
          <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
              <span class="text-sm font-medium text-blue-800">New Students This Week</span>
              <span class="font-bold text-blue-600">{{ stats.newStudentsThisWeek || '42' }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
              <span class="text-sm font-medium text-green-800">Course Completions</span>
              <span class="font-bold text-green-600">{{ stats.courseCompletions || '127' }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
              <span class="text-sm font-medium text-yellow-800">Pending Teacher Approvals</span>
              <span class="font-bold text-yellow-600">{{ stats.pendingTeacherApprovals || '8' }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
              <span class="text-sm font-medium text-purple-800">Active Classes</span>
              <span class="font-bold text-purple-600">{{ stats.activeClasses || '34' }}</span>
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

// Initialize function
const initializeApp = async () => {
  try {
    // Get CSRF token
    await apiClient.get('/sanctum/csrf-cookie')
    console.log('🛡️ CSRF token obtained')
    
    // Test API connection
    const response = await apiClient.get('/api/sanctum/csrf-cookie')
    console.log('✅ API connection successful')
  } catch (error) {
    console.error('❌ API initialization failed:', error)
  }
}

// Handle search
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
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
import { onMounted } from 'vue'
onMounted(() => {
  initializeApp()
})
</script>