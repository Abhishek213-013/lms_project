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
            <h1 class="text-2xl font-bold text-gray-900">Regular Academic Courses</h1>
            <p class="text-gray-600">Manage classes and subjects for grades 1-12</p>
          </div>
          <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            <router-link to="/admin/courses/all-courses">← Back to Dashboard</router-link>
          </button>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
          <div 
            v-for="classItem in regularClasses" 
            :key="classItem.id"
            class="bg-white rounded-lg border border-gray-200 p-6 text-center cursor-pointer hover:shadow-lg transition-shadow duration-200"
            @click="viewClassDetails(classItem)"
          >
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl font-bold text-blue-600">{{ classItem.grade }}</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">{{ classItem.name }}</h3>
            <p class="text-sm text-gray-600">{{ classItem.subjectCount }} subjects</p>
            <p class="text-xs text-gray-500 mt-1">{{ classItem.studentCount }} students</p>
            
            <!-- Teachers Preview -->
            <div v-if="classItem.teachers && classItem.teachers.length > 0" class="mt-3">
              <div class="flex justify-center -space-x-2">
                <div 
                  v-for="teacher in classItem.teachers.slice(0, 3)" 
                  :key="teacher.id"
                  class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-semibold border-2 border-white"
                  :title="teacher.name"
                >
                  {{ getInitials(teacher.name) }}
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">{{ classItem.teachers.length }} teachers</p>
            </div>

            <!-- Class Status -->
            <div class="mt-3">
              <span 
                :class="`px-2 py-1 text-xs rounded-full ${
                  classItem.status === 'active' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-gray-100 text-gray-800'
                }`"
              >
                {{ classItem.status || 'Active' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && regularClasses.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No regular classes found</h3>
          <p class="text-gray-500 mb-4">There are no regular academic classes available.</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading classes...</p>
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
const classes = ref([])
const loading = ref(true)

const regularClasses = computed(() => {
  return classes.value.filter(course => course.type === 'regular')
})

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

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

const viewClassDetails = (classItem) => {
  router.push(`/admin/courses/class/${classItem.grade}/subjects`)
}

const getInitials = (name) => {
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
}

onMounted(() => {
  fetchClasses()
})
</script>