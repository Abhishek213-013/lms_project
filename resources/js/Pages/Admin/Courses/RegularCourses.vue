<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar with Mobile Support -->
    <Sidebar 
      :isMobileMenuOpen="isMobileMenuOpen" 
      @menu-click="closeMobileMenu"
      @close-mobile="closeMobileMenu"
    />

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64">
      <!-- Top Navbar with Hamburger -->
      <Navbar 
        page-title="Regular Academic Courses" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />

      <!-- Page Content -->
      <div class="p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 space-y-4 md:space-y-0">
          <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Regular Academic Courses</h1>
            <p class="text-sm md:text-base text-gray-600">Manage classes and subjects for grades 1-12</p>
          </div>
          <button class="text-blue-600 hover:text-blue-800 text-sm font-medium self-start md:self-auto">
            <Link href="/admin/courses/all-courses">← Back to Dashboard</Link>
          </button>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 md:gap-4">
          <div 
            v-for="classItem in regularClasses" 
            :key="classItem.id"
            class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 text-center cursor-pointer hover:shadow-lg transition-shadow duration-200"
            @click="viewClassDetails(classItem)"
          >
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
              <span class="text-lg md:text-2xl font-bold text-blue-600">{{ classItem.grade }}</span>
            </div>
            <h3 class="text-sm md:text-base font-semibold text-gray-800 mb-1 md:mb-2">{{ classItem.name }}</h3>
            <p class="text-xs md:text-sm text-gray-600">{{ classItem.subjectCount }} subjects</p>
            <p class="text-xs text-gray-500 mt-1">{{ classItem.studentCount }} students</p>
            
            <!-- Teachers Preview -->
            <div v-if="classItem.teachers && classItem.teachers.length > 0" class="mt-2 md:mt-3">
              <div class="flex justify-center -space-x-1 md:-space-x-2">
                <div 
                  v-for="teacher in classItem.teachers.slice(0, 3)" 
                  :key="teacher.id"
                  class="w-5 h-5 md:w-6 md:h-6 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-semibold border-2 border-white"
                  :title="teacher.name"
                >
                  {{ getInitials(teacher.name) }}
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">{{ classItem.teachers.length }} teachers</p>
            </div>

            <!-- Class Status -->
            <div class="mt-2 md:mt-3">
              <span 
                :class="`px-2 py-1 text-xs rounded-full ${
                  classItem.status === 'active' 
                    ? 'bg-green-100 text-green-800' 
                    : classItem.status === 'inactive'
                    ? 'bg-red-100 text-red-800'
                    : 'bg-gray-100 text-gray-800'
                }`"
              >
                {{ classItem.status || 'Active' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && regularClasses.length === 0" class="text-center py-8 md:py-12">
          <svg class="w-12 h-12 md:w-16 md:h-16 text-gray-400 mx-auto mb-3 md:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
          </svg>
          <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No regular classes found</h3>
          <p class="text-sm md:text-base text-gray-500 mb-4">There are no regular academic classes available.</p>
          <button 
            @click="fetchClasses"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-sm md:text-base"
          >
            Refresh Data
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8 md:py-12">
          <div class="animate-spin rounded-full h-8 w-8 md:h-12 md:w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-sm md:text-base text-gray-600 mt-3 md:mt-4">Loading classes...</p>
        </div>
      </div>
    </div>

    <!-- Mobile Overlay -->
    <div 
      v-if="isMobileMenuOpen"
      @click="closeMobileMenu"
      class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Mobile menu state
const isMobileMenuOpen = ref(false)

// Mobile menu functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

// Course management state
const classes = ref([])
const loading = ref(true)
const error = ref('')

// Computed properties
const regularClasses = computed(() => {
  return classes.value.filter(course => course.type === 'regular')
})

// Handle search from navbar
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

// Fetch classes from API
const fetchClasses = async () => {
  try {
    loading.value = true
    error.value = ''
    
    const response = await apiClient.get('/courses/classes')
    
    if (response.data.success) {
      classes.value = response.data.data
    } else {
      error.value = response.data.message || 'Failed to fetch classes'
    }
  } catch (err) {
    console.error('Error fetching classes:', err)
    error.value = 'Failed to load classes. Please check your connection.'
    
    // Fallback to mock data
    classes.value = getMockClasses()
  } finally {
    loading.value = false
  }
}

// Mock data function for fallback
const getMockClasses = () => {
  return Array.from({ length: 12 }, (_, i) => ({
    id: i + 1,
    grade: i + 1,
    name: `Class ${i + 1}`,
    subjectCount: Math.floor(Math.random() * 8) + 5,
    studentCount: Math.floor(Math.random() * 40) + 20,
    teachers: Math.random() > 0.5 ? [
      {
        id: i * 10 + 1,
        name: `Teacher ${['A', 'B', 'C'][i % 3]} ${i + 1}`,
        email: `teacher${i + 1}@school.com`
      }
    ] : [],
    status: 'active',
    type: 'regular'
  }))
}

// Navigation functions
const viewClassDetails = (classItem) => {
  // Use Inertia's visit method instead of router.push
  router.visit(`/admin/courses/class/${classItem.grade}/subjects`)
}

// Utility functions
const getInitials = (name) => {
  if (!name) return 'T'
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
}

// Initialize when component mounts
onMounted(() => {
  fetchClasses()
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