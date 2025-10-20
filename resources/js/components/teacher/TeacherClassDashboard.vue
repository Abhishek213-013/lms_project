<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ classData.name }}</h1>
        <p class="text-gray-600">{{ classData.subject }} • {{ classData.studentCount }} students</p>
      </div>
      <button 
        @click="$router.back()"
        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
      >
        ← Back to Teacher Portal
      </button>
    </div>

    <!-- Three Main Tiles -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Give Assignment Tile -->
      <div 
        @click="navigateToAssignments"
        class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
      >
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Give Assignment</h3>
            <p class="text-sm text-gray-600">Create and manage assignments</p>
          </div>
        </div>
      </div>

      <!-- Provide Resources Tile -->
      <div 
        @click="navigateToResources"
        class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
      >
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Provide Resources</h3>
            <p class="text-sm text-gray-600">Upload and share learning materials</p>
          </div>
        </div>
      </div>

      <!-- Schedule Class Tile -->
      <div 
        @click="navigateToSchedule"
        class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
      >
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Schedule Class</h3>
            <p class="text-sm text-gray-600">Manage class schedule and timings</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg mr-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Assignments</p>
            <h3 class="text-xl font-bold text-gray-800">{{ stats.totalAssignments }}</h3>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg mr-4">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Resources</p>
            <h3 class="text-xl font-bold text-gray-800">{{ stats.totalResources }}</h3>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg mr-4">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Scheduled</p>
            <h3 class="text-xl font-bold text-gray-800">{{ stats.totalSchedules }}</h3>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="p-2 bg-orange-100 rounded-lg mr-4">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Students</p>
            <h3 class="text-xl font-bold text-gray-800">{{ classData.studentCount }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
      <div class="space-y-4">
        <div 
          v-for="activity in recentActivities" 
          :key="activity.id"
          class="flex items-center justify-between p-4 border border-gray-100 rounded-lg"
        >
          <div class="flex items-center space-x-4">
            <div :class="`p-3 rounded-lg ${getActivityColor(activity.type)}`">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="activity.type === 'assignment'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                <path v-else-if="activity.type === 'resource'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">{{ activity.title }}</h4>
              <p class="text-sm text-gray-600">{{ activity.description }}</p>
              <p class="text-xs text-gray-500">{{ formatDate(activity.created_at) }}</p>
            </div>
          </div>
          <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(activity.status)}`">
            {{ activity.status }}
          </span>
        </div>
        <div v-if="recentActivities.length === 0" class="text-center py-8 text-gray-500">
          No recent activity. Create your first assignment or resource!
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const classData = ref({
  id: route.params.classId,
  name: 'Loading...',
  subject: '',
  studentCount: 0
})

const stats = ref({
  totalAssignments: 0,
  totalResources: 0,
  totalSchedules: 0
})

const recentActivities = ref([])

// Navigation methods
const navigateToAssignments = () => {
  router.push(`/teacher/class/${classData.value.id}/assignments`)
}

const navigateToResources = () => {
  router.push(`/teacher/class/${classData.value.id}/resources`)
}

const navigateToSchedule = () => {
  router.push(`/teacher/class/${classData.value.id}/schedule`)
}

// Helper methods
const getActivityColor = (type) => {
  const colors = {
    assignment: 'bg-blue-500',
    resource: 'bg-green-500',
    schedule: 'bg-purple-500'
  }
  return colors[type] || 'bg-gray-500'
}

const getStatusColor = (status) => {
  const colors = {
    active: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-blue-100 text-blue-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Fetch class data
const fetchClassData = async () => {
  try {
    // Mock data - replace with actual API call
    classData.value = {
      id: route.params.classId,
      name: 'Advanced Mathematics',
      subject: 'Mathematics',
      studentCount: 25
    }
    
    stats.value = {
      totalAssignments: 5,
      totalResources: 8,
      totalSchedules: 12
    }
    
    recentActivities.value = [
      {
        id: 1,
        type: 'assignment',
        title: 'Calculus Homework #3',
        description: 'Due: Next Monday',
        status: 'active',
        created_at: new Date().toISOString()
      },
      {
        id: 2,
        type: 'resource',
        title: 'Integration Techniques PDF',
        description: 'Uploaded new study material',
        status: 'active',
        created_at: new Date(Date.now() - 86400000).toISOString()
      },
      {
        id: 3,
        type: 'schedule',
        title: 'Extra Class Scheduled',
        description: 'Friday 3:00 PM - 4:30 PM',
        status: 'active',
        created_at: new Date(Date.now() - 172800000).toISOString()
      }
    ]
  } catch (error) {
    console.error('Error fetching class data:', error)
  }
}

onMounted(() => {
  fetchClassData()
})
</script>