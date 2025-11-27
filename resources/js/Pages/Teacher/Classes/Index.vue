<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Use your TeacherSidebar component -->
    <TeacherSidebar />
    
    <div class="ml-64 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">My Classes</h1>
          <p class="text-gray-600">Manage all your teaching classes</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Total Classes</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ classes.length }}</h3>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Total Students</p>
                <h3 class="text-3xl font-bold text-green-600">{{ totalStudents }}</h3>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Active Classes</p>
                <h3 class="text-3xl font-bold text-purple-600">{{ activeClasses }}</h3>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Upcoming Classes</p>
                <h3 class="text-3xl font-bold text-orange-600">{{ upcomingClasses }}</h3>
              </div>
              <div class="p-3 bg-orange-100 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Classes Grid -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-semibold text-gray-800">All Classes</h3>
              <div class="flex space-x-3">
                <select v-model="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="all">All Status</option>
                  <option value="active">Active</option>
                  <option value="upcoming">Upcoming</option>
                  <option value="completed">Completed</option>
                </select>
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search classes..." 
                  class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
                >
              </div>
            </div>
          </div>

          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div 
                v-for="classItem in filteredClasses" 
                :key="classItem.id"
                class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer"
                @click="viewClass(classItem.id)"
              >
                <div class="flex justify-between items-start mb-4">
                  <div>
                    <h4 class="text-lg font-semibold text-gray-900">{{ classItem.name }}</h4>
                    <p class="text-gray-600">{{ classItem.subject }} • Grade {{ classItem.grade }}</p>
                  </div>
                  <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(classItem.status)}`">
                    {{ classItem.status }}
                  </span>
                </div>
                
                <div class="space-y-2 text-sm text-gray-600">
                  <div class="flex justify-between">
                    <span>Students:</span>
                    <span class="font-medium">{{ classItem.studentCount }} enrolled</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Schedule:</span>
                    <span class="font-medium">{{ classItem.schedule || 'Not scheduled' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Last Activity:</span>
                    <span class="font-medium">{{ formatDate(classItem.last_activity) }}</span>
                  </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between">
                  <button 
                    @click.stop="viewClass(classItem.id)"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                  >
                    View Class
                  </button>
                  <div class="flex space-x-2">
                    <button 
                      @click.stop="manageResources(classItem.id)"
                      class="text-green-600 hover:text-green-800 text-sm font-medium"
                    >
                      Resources
                    </button>
                    <button 
                      @click.stop="manageAssignments(classItem.id)"
                      class="text-purple-600 hover:text-purple-800 text-sm font-medium"
                    >
                      Assignments
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="filteredClasses.length === 0" class="text-center py-12 text-gray-500">
              <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <p class="text-lg font-medium mb-2">No classes found</p>
              <p class="text-sm">Your classes will appear here once they're created.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'

// State
const classes = ref([])
const searchQuery = ref('')
const filterStatus = ref('all')
const loading = ref(true)

// Computed properties (keep the same)
const totalStudents = computed(() => {
  return classes.value.reduce((sum, classItem) => sum + (classItem.studentCount || 0), 0)
})

const activeClasses = computed(() => {
  return classes.value.filter(c => c.status === 'Active').length
})

const upcomingClasses = computed(() => {
  return classes.value.filter(c => c.status === 'Upcoming').length
})

const filteredClasses = computed(() => {
  let filtered = classes.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(classItem => 
      classItem.name.toLowerCase().includes(query) ||
      classItem.subject.toLowerCase().includes(query) ||
      classItem.grade.toLowerCase().includes(query)
    )
  }

  if (filterStatus.value !== 'all') {
    filtered = filtered.filter(classItem => 
      classItem.status.toLowerCase() === filterStatus.value.toLowerCase()
    )
  }

  return filtered
})

// Methods
const fetchClasses = async () => {
  try {
    loading.value = true
    console.log('Fetching teacher classes...')
    
    const response = await fetch('/api/courses/teacher/classes', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'include'
    })
    
    console.log('Response status:', response.status)
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const contentType = response.headers.get('content-type')
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text()
      console.error('Non-JSON response:', text.substring(0, 200))
      throw new Error('Server returned non-JSON response')
    }
    
    const result = await response.json()
    console.log('API response:', result)
    
    if (result.success) {
      classes.value = result.data
    } else {
      console.error('API returned error:', result.message)
      // Fallback to mock data if API fails
      classes.value = getMockClasses()
    }
  } catch (error) {
    console.error('Error fetching classes:', error)
    // Fallback to mock data
    classes.value = getMockClasses()
  } finally {
    loading.value = false
  }
}

// Mock data fallback
const getMockClasses = () => {
  return [
    {
      id: 1,
      name: 'Advanced Mathematics',
      subject: 'Mathematics',
      grade: '10',
      studentCount: 25,
      schedule: 'Mon, Wed, Fri 10:00 AM',
      status: 'Active',
      last_activity: new Date().toISOString()
    },
    {
      id: 2,
      name: 'Physics 101',
      subject: 'Physics',
      grade: '11',
      studentCount: 30,
      schedule: 'Tue, Thu 2:00 PM',
      status: 'Active',
      last_activity: new Date().toISOString()
    },
    {
      id: 3,
      name: 'Chemistry Basics',
      subject: 'Chemistry',
      grade: '9',
      studentCount: 20,
      schedule: 'Mon, Fri 11:00 AM',
      status: 'Upcoming',
      last_activity: new Date().toISOString()
    }
  ]
}

const viewClass = (classId) => {
  router.visit(`/teacher/class/${classId}`)
}

const manageResources = (classId) => {
  router.visit(`/teacher/class/${classId}/resources`)
}

const manageAssignments = (classId) => {
  router.visit(`/teacher/class/${classId}/assignments`)
}

const getStatusColor = (status) => {
  const colors = {
    'Active': 'bg-green-100 text-green-800',
    'Upcoming': 'bg-blue-100 text-blue-800',
    'Completed': 'bg-gray-100 text-gray-800',
    'Inactive': 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'No activity'
  return new Date(dateString).toLocaleDateString()
}

// Lifecycle
onMounted(() => {
  fetchClasses()
})
</script>