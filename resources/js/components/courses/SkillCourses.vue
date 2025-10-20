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
            <h1 class="text-2xl font-bold text-gray-900">Skill Based Courses</h1>
            <p class="text-gray-600">Manage life skills, spoken English, and other skill courses</p>
          </div>
          <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            <router-link to="/admin/courses/all-courses">← Back to Dashboard</router-link>
          </button>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="course in skillCourses" 
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
            <p class="text-gray-600 text-sm mb-4 description-truncate">{{ course.description || 'No description available' }}</p>
            
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
                <span class="text-gray-600">Teachers:</span>
                <span class="font-medium text-gray-800">{{ course.teachers?.length || 0 }}</span>
              </div>
            </div>

            <!-- Teachers Preview -->
            <div v-if="course.teachers && course.teachers.length > 0" class="mt-4 pt-4 border-t border-gray-100">
              <p class="text-xs text-gray-500 mb-2">ASSIGNED TEACHERS</p>
              <div class="flex -space-x-2">
                <div 
                  v-for="teacher in course.teachers.slice(0, 3)" 
                  :key="teacher.id"
                  class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-semibold border-2 border-white"
                  :title="teacher.name"
                >
                  {{ getInitials(teacher.name) }}
                </div>
                <div 
                  v-if="course.teachers.length > 3"
                  class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-xs font-semibold border-2 border-white"
                  title="More teachers"
                >
                  +{{ course.teachers.length - 3 }}
                </div>
              </div>
            </div>

            <!-- Course Status -->
            <div class="mt-4 pt-4 border-t border-gray-100">
              <span 
                :class="`px-2 py-1 text-xs rounded-full ${
                  course.status === 'active' 
                    ? 'bg-green-100 text-green-800' 
                    : course.status === 'inactive'
                    ? 'bg-gray-100 text-gray-800'
                    : 'bg-yellow-100 text-yellow-800'
                }`"
              >
                {{ course.status || 'active' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && skillCourses.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No skill courses found</h3>
          <p class="text-gray-500 mb-4">There are no skill-based courses available.</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading courses...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../../api/client'
import Sidebar from '../Layout/Sidebar.vue'
import Navbar from '../Layout/Navbar.vue'
const router = useRouter()

// Sidebar and navbar state
const activeMenu = ref('courses')
const userMenuOpen = ref(false)
const user = ref(null)
const isDark = ref(false)

// Course management state
const classes = ref([])
const loading = ref(true)
const error = ref('')

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

// Authentication check
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    router.push('/login')
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
    await apiClient.post('/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Course management functions
const skillCourses = computed(() => {
  return classes.value.filter(course => course.type === 'other')
})

const fetchClasses = async () => {
  try {
    loading.value = true
    const response = await apiClient.get('/courses/classes')
    if (response.data.success) {
      classes.value = response.data.data
    }
  } catch (err) {
    console.error('Error fetching classes:', err)
  } finally {
    loading.value = false
  }
}

const viewCourseDetails = (course) => {
  router.push(`/admin/courses/course/${course.id}/details`)
}

const getInitials = (name) => {
  return name ? name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2) : 'T'
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  checkAuthentication()
  fetchClasses()
})
</script>

<style scoped>
/* Modern line-clamp with full browser compatibility */
.description-truncate {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
  box-orient: vertical;
}

/* Fallback for older browsers */
@supports not (line-clamp: 2) {
  .description-truncate {
    max-height: 2.8em;
    line-height: 1.4em;
    overflow: hidden;
    position: relative;
  }
  
  .description-truncate::after {
    content: '...';
    position: absolute;
    bottom: 0;
    right: 0;
    background: white;
    padding-left: 4px;
  }
}

/* Ensure proper styling for all elements */
.submenu-link {
  display: block;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  color: #4b5563;
  border-radius: 0.5rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
}

.rotate-180 {
  transform: rotate(180deg);
}
</style>