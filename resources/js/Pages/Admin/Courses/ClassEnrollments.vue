<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Class Enrollments" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Class {{ grade }} - Enrollments</h1>
            <p class="text-gray-600">Manage student enrollments and teacher assignments</p>
            <p v-if="dataSource === 'mock'" class="text-yellow-600 text-sm mt-1">
              ⚠️ Using demonstration data
            </p>
          </div>
          <Link 
            href="/admin/courses/enrollments" 
            class="flex items-center space-x-1 text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Enrollments</span>
          </Link>
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

        <!-- Class Summary -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
              <p class="text-sm font-medium text-gray-600">Class</p>
              <h3 class="text-xl font-bold text-gray-800">{{ enrollmentData.className }}</h3>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium text-gray-600">Subjects</p>
              <p class="text-xl font-bold text-blue-600">{{ enrollmentData.subjects.length }}</p>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium text-gray-600">Total Students</p>
              <p class="text-xl font-bold text-green-600">{{ totalStudents }}</p>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium text-gray-600">Capacity</p>
              <p class="text-xl font-bold" :class="capacityColor">
                {{ Math.round((totalStudents / 40) * 100) }}%
              </p>
            </div>
          </div>
        </div>

        <!-- Subjects and Enrollments -->
        <div class="space-y-6">
          <div 
            v-for="subject in enrollmentData.subjects" 
            :key="subject.id"
            class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200"
          >
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                  <h3 class="text-lg font-semibold text-gray-800">{{ subject.subject }}</h3>
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                    {{ subject.code || getSubjectCode(subject.subject) }}
                  </span>
                </div>
                <div class="flex items-center space-x-4">
                  <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                    {{ subject.students.length }} students
                  </span>
                  <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                    {{ subject.teacher ? 'Teacher Assigned' : 'No Teacher' }}
                  </span>
                </div>
              </div>
            </div>

            <div class="p-6">
              <!-- Teacher Information -->
              <div class="mb-6">
                <h4 class="font-medium text-gray-800 mb-3">Teacher Information</h4>
                <div v-if="subject.teacher" class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                  <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                      <span class="text-white font-semibold text-sm">{{ getInitials(subject.teacher.name) }}</span>
                    </div>
                    <div>
                      <h4 class="font-medium text-gray-800">{{ subject.teacher.name }}</h4>
                      <p class="text-sm text-gray-600">{{ subject.teacher.email }}</p>
                      <p class="text-xs text-gray-500">{{ subject.teacher.qualification || 'No qualification specified' }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-600">Experience</p>
                    <p class="font-medium text-gray-900">{{ subject.teacher.experience || 'N/A' }} years</p>
                    <Link 
                      :href="`/admin/courses/class/${grade}/subject/${subject.id}/teachers`"
                      class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
                    >
                      Manage Teacher
                    </Link>
                  </div>
                </div>
                <div v-else class="p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                      </svg>
                      <div>
                        <p class="text-yellow-800 font-medium">No teacher assigned</p>
                        <p class="text-yellow-700 text-sm">This subject doesn't have an assigned teacher</p>
                      </div>
                    </div>
                    <Link 
                      :href="`/admin/courses/class/${grade}/subject/${subject.id}/teachers`"
                      class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    >
                      Assign Teacher
                    </Link>
                  </div>
                </div>
              </div>

              <!-- Students List -->
              <div>
                <div class="flex justify-between items-center mb-4">
                  <h4 class="font-medium text-gray-800">Enrolled Students ({{ subject.students.length }})</h4>
                  <button 
                    v-if="subject.students.length > 0"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
                  >
                    Export List
                  </button>
                </div>
                
                <div v-if="subject.students.length > 0" class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll Number</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr 
                        v-for="student in subject.students" 
                        :key="student.id"
                        class="hover:bg-gray-50 transition-colors"
                      >
                        <td class="px-4 py-3 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                              <span class="text-white text-xs font-semibold">{{ getInitials(student.name) }}</span>
                            </div>
                            <div>
                              <div class="text-sm font-medium text-gray-900">{{ student.name }}</div>
                              <div class="text-sm text-gray-500">{{ student.email }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                          {{ student.roll_number || 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                          {{ student.parent_name || 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                          {{ student.parent_contact || 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                            Active
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Empty Students State -->
                <div v-else class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                  </svg>
                  <p class="text-gray-500 mb-2">No students enrolled in this subject</p>
                  <p class="text-gray-400 text-sm">Students will appear here once they are enrolled</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading enrollment details...</p>
        </div>

        <!-- Database Setup Instructions -->
        <div v-if="!loading && dataSource === 'mock'" class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800 mb-3">Setup Your Database</h3>
          <p class="text-gray-600 mb-4">To use real enrollment data, you need to:</p>
          <div class="space-y-2 text-sm text-gray-600">
            <p>1. Run migrations: <code class="bg-gray-200 px-2 py-1 rounded font-mono text-xs">php artisan migrate</code></p>
            <p>2. Seed the database: <code class="bg-gray-200 px-2 py-1 rounded font-mono text-xs">php artisan db:seed</code></p>
            <p>3. Add students, teachers, and enrollments to your database</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3' // Import Inertia router and Link
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Define props that will be passed from Inertia controller
const props = defineProps({
  grade: {
    type: [String, Number],
    required: true
  },
  enrollmentData: {
    type: Object,
    default: () => ({
      grade: 0,
      className: '',
      subjects: []
    })
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

const totalStudents = computed(() => {
  return props.enrollmentData.subjects.reduce((total, subject) => total + subject.students.length, 0)
})

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

const capacityColor = computed(() => {
  const percentage = (totalStudents.value / 40) * 100
  if (percentage >= 90) return 'text-red-600'
  if (percentage >= 75) return 'text-yellow-600'
  if (percentage >= 50) return 'text-green-600'
  return 'text-blue-600'
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

const getSubjectCode = (subjectName) => {
  const codes = {
    'mathematics': 'MATH',
    'science': 'SCI',
    'english': 'ENG',
    'social studies': 'SST',
    'computer science': 'CS'
  }
  return codes[subjectName.toLowerCase()] || subjectName.substring(0, 3).toUpperCase()
}

const getInitials = (name) => {
  return name ? name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2) : '??'
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})
</script>