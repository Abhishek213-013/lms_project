<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
      <button 
        @click="toggleMobileMenu"
        class="p-2 bg-white rounded-lg shadow-md border border-gray-200"
      >
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Mobile Overlay -->
    <div 
      v-if="isMobileMenuOpen"
      @click="closeMobileMenu"
      class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
    ></div>

    <!-- Sidebar -->
    <TeacherSidebar 
      :is-mobile-menu-open="isMobileMenuOpen"
      @close-mobile="closeMobileMenu"
    />

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64 min-w-0 w-full transition-all duration-300">
      <!-- Page Content -->
      <div class="p-4 sm:p-6 max-w-full overflow-x-hidden">
        <!-- Header -->
        <div class="mb-4 sm:mb-6">
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900">My Classes</h1>
          <p class="text-gray-600 text-sm sm:text-base">Manage all your teaching classes</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Total Classes</p>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-600">{{ classes.length }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-blue-100 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Total Students</p>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-green-600">{{ totalStudents }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-green-100 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Active Classes</p>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-purple-600">{{ activeClasses }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-purple-100 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Upcoming Classes</p>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-orange-600">{{ upcomingClasses }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-orange-100 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Classes Grid -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
              <h3 class="text-lg font-semibold text-gray-800">All Classes</h3>
              <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <select 
                  v-model="filterStatus" 
                  class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                >
                  <option value="all">All Status</option>
                  <option value="active">Active</option>
                  <option value="upcoming">Upcoming</option>
                  <option value="completed">Completed</option>
                </select>
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search classes..." 
                  class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64 text-sm sm:text-base"
                >
              </div>
            </div>
          </div>

          <div class="p-4 sm:p-6">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-8">
              <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-600">Loading classes...</p>
            </div>

            <!-- Classes Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
              <div 
                v-for="classItem in filteredClasses" 
                :key="classItem.id"
                class="border border-gray-200 rounded-lg p-4 sm:p-6 hover:shadow-lg transition-shadow cursor-pointer"
                @click="viewClass(classItem.id)"
              >
                <div class="flex justify-between items-start mb-3 sm:mb-4">
                  <div class="min-w-0 flex-1">
                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ classItem.name }}</h4>
                    <p class="text-gray-600 text-sm truncate">{{ classItem.subject }} • Grade {{ classItem.grade }}</p>
                  </div>
                  <span :class="`px-2 py-1 text-xs font-semibold rounded-full flex-shrink-0 ml-2 ${getStatusColor(classItem.status)}`">
                    {{ classItem.status }}
                  </span>
                </div>
                
                <div class="space-y-2 text-sm text-gray-600">
                  <div class="flex justify-between">
                    <span class="truncate">Students:</span>
                    <span class="font-medium ml-2">{{ classItem.studentCount }} enrolled</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="truncate">Schedule:</span>
                    <span class="font-medium ml-2 text-right">{{ classItem.schedule || 'Not scheduled' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="truncate">Last Activity:</span>
                    <span class="font-medium ml-2">{{ formatDate(classItem.last_activity) }}</span>
                  </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                  <button 
                    @click.stop="viewClass(classItem.id)"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                  >
                    View Class
                  </button>
                  <div class="flex space-x-2">
                    <button 
                      @click.stop="manageResources(classItem.id)"
                      class="text-green-600 hover:text-green-800 text-sm font-medium p-1"
                      title="Manage Resources"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </button>
                    <button 
                      @click.stop="manageAssignments(classItem.id)"
                      class="text-purple-600 hover:text-purple-800 text-sm font-medium p-1"
                      title="Manage Assignments"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && filteredClasses.length === 0" class="text-center py-8 sm:py-12 text-gray-500">
              <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <p class="text-base sm:text-lg font-medium mb-2">No classes found</p>
              <p class="text-sm">Your classes will appear here once they're created.</p>
              <div v-if="searchQuery || filterStatus !== 'all'" class="mt-4">
                <button 
                  @click="clearFilters"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  Clear filters
                </button>
              </div>
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
const isMobileMenuOpen = ref(false)

// Mobile menu functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

// Computed properties
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
    },
    {
      id: 4,
      name: 'Biology Fundamentals',
      subject: 'Biology',
      grade: '10',
      studentCount: 28,
      schedule: 'Wed, Fri 1:00 PM',
      status: 'Active',
      last_activity: new Date().toISOString()
    },
    {
      id: 5,
      name: 'Computer Science',
      subject: 'Computer Science',
      grade: '12',
      studentCount: 22,
      schedule: 'Tue, Thu 3:00 PM',
      status: 'Upcoming',
      last_activity: new Date().toISOString()
    },
    {
      id: 6,
      name: 'English Literature',
      subject: 'English',
      grade: '11',
      studentCount: 24,
      schedule: 'Mon, Wed, Fri 9:00 AM',
      status: 'Active',
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

const clearFilters = () => {
  searchQuery.value = ''
  filterStatus.value = 'all'
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
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

// Handle escape key to close mobile menu
const handleEscape = (event) => {
  if (event.key === 'Escape' && isMobileMenuOpen.value) {
    closeMobileMenu()
  }
}

// Lifecycle
onMounted(() => {
  fetchClasses()
  document.addEventListener('keydown', handleEscape)
})
</script>

<style scoped>
/* Use deep selector to override */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

/* Ensure smooth transitions */
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Z-index for proper layering */
.z-40 {
  z-index: 40;
}

.z-50 {
  z-index: 50;
}

/* Mobile-specific adjustments */
@media (max-width: 640px) {
  .custom-heading {
    font-size: 1.125rem;
  }
}
</style>