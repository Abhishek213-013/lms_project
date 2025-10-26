<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Skill Based Courses" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Skill Based Courses</h1>
            <p class="text-gray-600">Manage life skills, spoken English, and other skill courses</p>
          </div>
          <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            <Link href="/admin/courses/all-courses">← Back to Dashboard</Link>
          </button>
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
                    ? 'bg-red-100 text-red-800'
                    : course.status === 'upcoming'
                    ? 'bg-yellow-100 text-yellow-800'
                    : 'bg-gray-100 text-gray-800'
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
          <button 
            @click="fetchClasses"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            Refresh Data
          </button>
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
import { router, Link } from '@inertiajs/vue3'
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

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
    router.visit('/login')
    return
  }
}

// Course management functions
const skillCourses = computed(() => {
  return classes.value.filter(course => course.type === 'other')
})

const fetchClasses = async () => {
  try {
    loading.value = true
    error.value = ''
    
    const response = await apiClient.get('/courses/classes')
    
    if (response.data.success) {
      classes.value = response.data.data
    } else {
      error.value = response.data.message || 'Failed to fetch courses'
    }
  } catch (err) {
    console.error('Error fetching classes:', err)
    error.value = 'Failed to load courses. Please check your connection.'
    
    // Fallback to mock data
    classes.value = getMockSkillCourses()
  } finally {
    loading.value = false
  }
}

const getMockSkillCourses = () => {
  return [
    {
      id: 1,
      name: 'Life Skills Development',
      category: 'Life Skills',
      description: 'Comprehensive life skills training including communication, problem-solving, and emotional intelligence.',
      studentCount: 25,
      capacity: 30,
      teachers: [
        { id: 101, name: 'Dr. Sarah Johnson', email: 'sarah@school.com' },
        { id: 102, name: 'Mr. David Chen', email: 'david@school.com' }
      ],
      status: 'active',
      type: 'other'
    },
    {
      id: 2,
      name: 'Spoken English Mastery',
      category: 'Language',
      description: 'Improve English speaking fluency, pronunciation, and conversational skills.',
      studentCount: 35,
      capacity: 40,
      teachers: [
        { id: 103, name: 'Ms. Emily Wilson', email: 'emily@school.com' }
      ],
      status: 'active',
      type: 'other'
    },
    {
      id: 3,
      name: 'Computer Basics & Digital Literacy',
      category: 'Technology',
      description: 'Learn fundamental computer skills, internet usage, and essential software applications.',
      studentCount: 20,
      capacity: 25,
      teachers: [],
      status: 'active',
      type: 'other'
    },
    {
      id: 4,
      name: 'Art & Creative Expression',
      category: 'Arts',
      description: 'Explore various art forms and develop creative thinking skills.',
      studentCount: 15,
      capacity: 20,
      teachers: [
        { id: 104, name: 'Mrs. Maria Garcia', email: 'maria@school.com' }
      ],
      status: 'upcoming',
      type: 'other'
    },
    {
      id: 5,
      name: 'Yoga & Meditation',
      category: 'Wellness',
      description: 'Learn yoga techniques and meditation practices for mental and physical well-being.',
      studentCount: 18,
      capacity: 25,
      teachers: [
        { id: 105, name: 'Mr. Raj Patel', email: 'raj@school.com' }
      ],
      status: 'active',
      type: 'other'
    },
    {
      id: 6,
      name: 'Career Counseling',
      category: 'Career Development',
      description: 'Professional guidance for career planning and skill development.',
      studentCount: 12,
      capacity: 15,
      teachers: [
        { id: 106, name: 'Dr. Michael Brown', email: 'michael@school.com' }
      ],
      status: 'active',
      type: 'other'
    }
  ]
}

const viewCourseDetails = (course) => {
  // Use Inertia's visit method instead of router.push
  router.visit(`/admin/courses/course/${course.id}/details`)
}

const getInitials = (name) => {
  return name ? name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2) : 'T'
}

onMounted(() => {
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