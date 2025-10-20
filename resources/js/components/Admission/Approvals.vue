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
            <h1 class="text-2xl font-bold text-gray-900">Approval Statistics</h1>
            <p class="text-gray-600">Overview of student application approvals</p>
          </div>
          <div class="flex space-x-3">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
              Generate Report
            </button>
          </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Today</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ stats.today.approved }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                  {{ stats.today.pending }} pending • {{ stats.today.rejected }} rejected
                </p>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">This Week</p>
                <h3 class="text-3xl font-bold text-green-600">{{ stats.this_week.approved }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                  {{ stats.this_week.pending }} pending • {{ stats.this_week.rejected }} rejected
                </p>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                <h3 class="text-3xl font-bold text-purple-600">{{ stats.this_month.approved }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                  {{ stats.this_month.pending }} pending • {{ stats.this_month.rejected }} rejected
                </p>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total</p>
                <h3 class="text-3xl font-bold text-orange-600">{{ stats.total.approved }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                  {{ stats.total.pending }} pending • {{ stats.total.rejected }} rejected
                </p>
              </div>
              <div class="p-3 bg-orange-100 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Approval Rate Chart -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Approval Rate</h3>
            <div class="space-y-4">
              <div v-for="period in approvalRates" :key="period.name" class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">{{ period.name }}</span>
                <div class="flex items-center space-x-3">
                  <div class="w-32 bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-green-500 h-2 rounded-full" 
                      :style="{ width: `${period.rate}%` }"
                    ></div>
                  </div>
                  <span class="text-sm font-medium text-gray-800">{{ period.rate }}%</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Application Trends -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Application Trends</h3>
            <div class="space-y-4">
              <div v-for="trend in applicationTrends" :key="trend.period" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                  <span class="text-sm font-medium text-gray-800">{{ trend.period }}</span>
                  <p class="text-xs text-gray-600">{{ trend.description }}</p>
                </div>
                <span :class="`px-2 py-1 text-xs font-medium rounded-full ${
                  trend.change >= 0 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'
                }`">
                  {{ trend.change >= 0 ? '+' : '' }}{{ trend.change }}%
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Approval Activity</h3>
          <div class="space-y-4">
            <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
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
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import apiClient from '../../api/client'
import Sidebar from '../Layout/Sidebar.vue'
import Navbar from '../Layout/Navbar.vue'

const stats = ref({
  today: { approved: 0, pending: 0, rejected: 0 },
  this_week: { approved: 0, pending: 0, rejected: 0 },
  this_month: { approved: 0, pending: 0, rejected: 0 },
  total: { approved: 0, pending: 0, rejected: 0 }
})

const approvalRates = ref([
  { name: 'Today', rate: 85 },
  { name: 'This Week', rate: 78 },
  { name: 'This Month', rate: 82 },
  { name: 'Overall', rate: 80 }
])
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

const applicationTrends = ref([
  { period: 'Last 7 Days', change: 12, description: 'Compared to previous week' },
  { period: 'Last 30 Days', change: 8, description: 'Compared to previous month' },
  { period: 'Last Quarter', change: 15, description: 'Compared to previous quarter' }
])

const recentActivities = ref([
  {
    id: 1,
    title: 'Application Approved',
    description: 'Rahul Sharma - Class 10',
    status: 'Approved',
    time: '2 hours ago',
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    iconBg: 'bg-green-500'
  },
  {
    id: 2,
    title: 'Application Rejected',
    description: 'Priya Patel - Class 8',
    status: 'Rejected',
    time: '4 hours ago',
    icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    iconBg: 'bg-red-500'
  },
  {
    id: 3,
    title: 'Application Approved',
    description: 'Amit Kumar - Life Skills',
    status: 'Approved',
    time: '6 hours ago',
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    iconBg: 'bg-green-500'
  }
])

const fetchApprovalStats = async () => {
  try {
    const response = await apiClient.get('/admissions/approval-stats')
    
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching approval stats:', error)
  }
}

onMounted(() => {
  fetchApprovalStats()
})
</script>