<template>
  <div class="min-h-screen bg-gray-50 flex" style="font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol' !important;">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Admin Dashboard" @search="handleSearch" />
      
      <!-- Page Content -->
      <div class="p-6">
        <!-- Welcome Message -->
        <div class="mb-6">
          <h2 class="mb-2">Welcome back, {{ user?.name || 'Admin' }}!</h2>
          <p class="text-gray-600">Here's what's happening in your system today.</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <Link href="/admin/users" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-blue-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Users</h3>
                <p class="text-xs text-gray-600">Manage system users</p>
              </div>
            </div>
          </Link>

          <Link href="/admin/content" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-green-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9m0 0a2 2 0 01-2-2V2a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Content</h3>
                <p class="text-xs text-gray-600">Manage site content</p>
              </div>
            </div>
          </Link>

          <Link href="/admin/settings" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-purple-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Settings</h3>
                <p class="text-xs text-gray-600">System configuration</p>
              </div>
            </div>
          </Link>

          <Link href="/admin/analytics" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:border-indigo-500 transition-colors text-left block">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-sm">Analytics</h3>
                <p class="text-xs text-gray-600">View reports & insights</p>
              </div>
            </div>
          </Link>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">Total Users</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ stats.totalUsers || '2,847' }}</h3>
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
                <p class="text-xs font-medium text-gray-600 mb-1">Active Sessions</p>
                <h3 class="text-2xl font-bold text-green-600">{{ stats.activeSessions || '156' }}</h3>
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Pending Actions Card -->
          <Link href="/admin/pending" class="block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:border-yellow-400 transition-colors cursor-pointer">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-xs font-medium text-gray-600 mb-1">Pending Actions</p>
                  <h3 class="text-2xl font-bold text-yellow-600">{{ stats.pendingActions || '23' }}</h3>
                  <p class="text-xs text-yellow-600 mt-1">
                    Click to review
                  </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                  <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </Link>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-medium text-gray-600 mb-1">System Uptime</p>
                <h3 class="text-2xl font-bold text-purple-600">{{ stats.systemUptime || '99.8%' }}</h3>
                <p class="text-xs text-green-600 mt-1">
                  <span class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    All systems normal
                  </span>
                </p>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts and Additional Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Recent Activity -->
          <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
            <div class="space-y-4">
              <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-4">
                  <div :class="`w-10 h-10 rounded-full flex items-center justify-center ${activity.iconBg}`">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path :d="activity.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-800 text-sm">{{ activity.title }}</p>
                    <p class="text-xs text-gray-600">{{ activity.description }}</p>
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Overview</h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                <span class="text-xs font-medium text-blue-800">New Users This Week</span>
                <span class="font-bold text-blue-600 text-sm">{{ stats.newUsersThisWeek || '42' }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                <span class="text-xs font-medium text-green-800">Tasks Completed</span>
                <span class="font-bold text-green-600 text-sm">{{ stats.tasksCompleted || '127' }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                <span class="text-xs font-medium text-yellow-800">Pending Reviews</span>
                <span class="font-bold text-yellow-600 text-sm">{{ stats.pendingReviews || '8' }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                <span class="text-xs font-medium text-purple-800">Active Features</span>
                <span class="font-bold text-purple-600 text-sm">{{ stats.activeFeatures || '34' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Performance Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
          <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">System Performance</h3>
            <p class="text-sm text-gray-600">Overview of system metrics and performance</p>
          </div>
          
          <div class="border-t border-gray-200 pt-4">
            <h4 class="text-md font-medium text-gray-900 mb-4">Last 30 Days</h4>
            <!-- Placeholder for performance chart -->
            <div class="h-64 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center">
              <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="text-gray-500">Performance metrics visualization</p>
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
import { useRouter } from 'vue-router'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'
import { Link } from '@inertiajs/vue3'

const router = useRouter()
const user = ref(null)

// Authentication check
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    router.push('/login')
    return
  }
  
  user.value = userData
  console.log('User authenticated:', userData)
}

// Handle search
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

// Stats data
const stats = ref({
  totalUsers: '2,847',
  activeSessions: '156',
  pendingActions: '23',
  systemUptime: '99.8%',
  newUsersThisWeek: '42',
  tasksCompleted: '127',
  pendingReviews: '8',
  activeFeatures: '34'
})

// Recent activities data
const recentActivities = ref([
  {
    id: 1,
    title: 'New User Registration',
    description: 'User account created successfully',
    status: 'Completed',
    time: '10 min ago',
    icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
    iconBg: 'bg-blue-500'
  },
  {
    id: 2,
    title: 'System Backup',
    description: 'Daily backup completed',
    status: 'Success',
    time: '1 hour ago',
    icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    iconBg: 'bg-green-500'
  },
  {
    id: 3,
    title: 'Content Update',
    description: 'Homepage content modified',
    status: 'Pending Review',
    time: '2 hours ago',
    icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
    iconBg: 'bg-yellow-500'
  }
])

onMounted(() => {
  checkAuthentication()
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