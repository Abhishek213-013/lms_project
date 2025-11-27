<template>
  <div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
    <!-- Sidebar - Hidden on mobile, shown on desktop -->
    <div class="hidden lg:block">
      <Sidebar />
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div v-if="mobileSidebarOpen" class="fixed inset-0 z-40 lg:hidden">
      <div class="fixed inset-0 bg-gray-600 opacity-75" @click="closeMobileMenu"></div>
      <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
        <Sidebar 
          :isMobileMenuOpen="mobileSidebarOpen"
          @close-mobile="closeMobileMenu"
          @menu-click="closeMobileMenu"
        />
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64">
      <!-- Top Navbar with Mobile Menu Button -->
      <Navbar 
        page-title="Enrolled Students" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />
      
      <!-- Page Content -->
      <div class="p-4 lg:p-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
          <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Enrolled Students</h1>
            <p class="text-gray-600 text-sm lg:text-base">Manage all enrolled students across classes and courses</p>
          </div>
          <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 lg:space-x-3">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded-lg text-sm font-medium transition-colors w-full sm:w-auto">
              Export Students
            </button>
            <button class="bg-green-600 hover:bg-green-700 text-white px-3 lg:px-4 py-2 rounded-lg text-sm font-medium transition-colors w-full sm:w-auto">
              Add Student
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg mr-3 lg:mr-4 flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600">Total Students</p>
                <h3 class="text-lg lg:text-xl xl:text-2xl font-bold text-gray-800">{{ students.length }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg mr-3 lg:mr-4 flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600">Regular Classes</p>
                <h3 class="text-lg lg:text-xl xl:text-2xl font-bold text-gray-800">{{ regularStudentsCount }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg mr-3 lg:mr-4 flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600">Skill Courses</p>
                <h3 class="text-lg lg:text-xl xl:text-2xl font-bold text-gray-800">{{ skillStudentsCount }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
              <div class="p-2 bg-orange-100 rounded-lg mr-3 lg:mr-4 flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600">New This Month</p>
                <h3 class="text-lg lg:text-xl xl:text-2xl font-bold text-gray-800">15</h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-4 lg:p-6 border-b border-gray-200 flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-3 lg:space-y-0">
            <h3 class="text-lg font-semibold text-gray-800">Student List</h3>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 lg:space-x-3 w-full lg:w-auto">
              <select v-model="selectedClass" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                <option value="">All Classes</option>
                <option value="regular">Regular Classes</option>
                <option value="other">Skill Courses</option>
                <option v-for="className in uniqueClasses" :key="className" :value="className">{{ className }}</option>
              </select>
              <input 
                type="text" 
                v-model="searchQuery"
                placeholder="Search students..." 
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto"
              >
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full min-w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Roll No</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class/Course</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Parent Info</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Enrolled Date</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-gray-50">
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                    <div class="min-w-0">
                      <div class="text-sm font-medium text-gray-900 truncate">{{ student.name }}</div>
                      <div class="text-sm text-gray-500 truncate">{{ student.email }}</div>
                      <div class="text-xs text-gray-500 sm:hidden">{{ student.roll_number }}</div>
                    </div>
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden sm:table-cell">
                    {{ student.roll_number }}
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                    <div class="min-w-0">
                      <div class="text-sm text-gray-900 truncate">{{ student.class_name }}</div>
                      <div class="text-xs text-gray-500 capitalize">{{ student.class_type }}</div>
                    </div>
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                    <div class="min-w-0">
                      <div class="text-sm text-gray-900 truncate">{{ student.parent_name }}</div>
                      <div class="text-sm text-gray-500 truncate">{{ student.parent_contact }}</div>
                    </div>
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                    {{ formatDate(student.enrolled_date) }}
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      Enrolled
                    </span>
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex flex-col lg:flex-row lg:space-x-2 space-y-1 lg:space-y-0">
                      <button class="text-blue-600 hover:text-blue-900 text-left lg:text-center text-xs lg:text-sm">
                        View
                      </button>
                      <button class="text-green-600 hover:text-green-900 text-left lg:text-center text-xs lg:text-sm">
                        Edit
                      </button>
                      <button class="text-red-600 hover:text-red-900 text-left lg:text-center text-xs lg:text-sm">
                        Remove
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="filteredStudents.length === 0" class="text-center py-8 lg:py-12">
            <svg class="w-8 h-8 lg:w-12 lg:h-12 text-gray-400 mx-auto mb-3 lg:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <p class="text-gray-600 text-sm lg:text-base">No students found</p>
            <p class="text-sm text-gray-500 mt-1">No students match your search criteria</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

const students = ref([])
const selectedClass = ref('')
const searchQuery = ref('')
const mobileSidebarOpen = ref(false)

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Mobile menu functions
const toggleMobileMenu = () => {
  mobileSidebarOpen.value = !mobileSidebarOpen.value
}

const closeMobileMenu = () => {
  mobileSidebarOpen.value = false
}

const fetchStudents = async () => {
  try {
    // Simulate API call - replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Mock data for demonstration
    students.value = [
      {
        id: 1,
        name: 'John Smith',
        email: 'john.smith@email.com',
        roll_number: '2024001',
        class_name: 'Class 10 - Science',
        class_type: 'regular',
        parent_name: 'Robert Smith',
        parent_contact: '+1 234-567-8900',
        enrolled_date: '2024-01-15'
      },
      {
        id: 2,
        name: 'Sarah Johnson',
        email: 'sarah.j@email.com',
        roll_number: '2024002',
        class_name: 'Class 8 - Mathematics',
        class_type: 'regular',
        parent_name: 'Michael Johnson',
        parent_contact: '+1 234-567-8901',
        enrolled_date: '2024-01-14'
      },
      {
        id: 3,
        name: 'David Wilson',
        email: 'david.wilson@email.com',
        roll_number: '2024003',
        class_name: 'Life Skills Development',
        class_type: 'other',
        parent_name: 'James Wilson',
        parent_contact: '+1 234-567-8902',
        enrolled_date: '2024-01-13'
      },
      {
        id: 4,
        name: 'Emma Davis',
        email: 'emma.davis@email.com',
        roll_number: '2024004',
        class_name: 'Class 9 - English',
        class_type: 'regular',
        parent_name: 'Thomas Davis',
        parent_contact: '+1 234-567-8903',
        enrolled_date: '2024-01-12'
      },
      {
        id: 5,
        name: 'Michael Brown',
        email: 'michael.b@email.com',
        roll_number: '2024005',
        class_name: 'Spoken English Mastery',
        class_type: 'other',
        parent_name: 'William Brown',
        parent_contact: '+1 234-567-8904',
        enrolled_date: '2024-01-11'
      }
    ]
  } catch (error) {
    console.error('Error fetching students:', error)
  }
}

const filteredStudents = computed(() => {
  let filtered = students.value

  if (selectedClass.value) {
    if (selectedClass.value === 'regular') {
      filtered = filtered.filter(student => student.class_type === 'regular')
    } else if (selectedClass.value === 'other') {
      filtered = filtered.filter(student => student.class_type === 'other')
    } else {
      filtered = filtered.filter(student => student.class_name === selectedClass.value)
    }
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(student => 
      student.name.toLowerCase().includes(query) ||
      student.email.toLowerCase().includes(query) ||
      student.roll_number.toLowerCase().includes(query) ||
      student.class_name.toLowerCase().includes(query)
    )
  }

  return filtered
})

const uniqueClasses = computed(() => {
  return [...new Set(students.value.map(student => student.class_name))]
})

const regularStudentsCount = computed(() => {
  return students.value.filter(student => student.class_type === 'regular').length
})

const skillStudentsCount = computed(() => {
  return students.value.filter(student => student.class_type === 'other').length
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

onMounted(() => {
  fetchStudents()
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