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
        page-title="Pending Applications" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />
      
      <!-- Page Content -->
      <div class="p-4 lg:p-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
          <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Pending Applications</h1>
            <p class="text-gray-600 text-sm lg:text-base">Review and process new student applications</p>
          </div>
          <div class="flex space-x-2 lg:space-x-3">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded-lg text-sm font-medium transition-colors w-full lg:w-auto">
              Export Applications
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg mr-3 lg:mr-4 flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600">Total Applications</p>
                <h3 class="text-lg lg:text-xl xl:text-2xl font-bold text-gray-800">{{ applications.length }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
              <div class="p-2 bg-yellow-100 rounded-lg mr-3 lg:mr-4 flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600">Pending Review</p>
                <h3 class="text-lg lg:text-xl xl:text-2xl font-bold text-gray-800">{{ applications.length }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg mr-3 lg:mr-4 flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600">Approved Today</p>
                <h3 class="text-lg lg:text-xl xl:text-2xl font-bold text-gray-800">12</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
              <div class="p-2 bg-red-100 rounded-lg mr-3 lg:mr-4 flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600">Rejected Today</p>
                <h3 class="text-lg lg:text-xl xl:text-2xl font-bold text-gray-800">2</h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Applications Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-4 lg:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Application List</h3>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full min-w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Class/Course</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Parent Info</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Applied Date</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="application in applications" :key="application.id" class="hover:bg-gray-50">
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                    <div class="min-w-0">
                      <div class="text-sm font-medium text-gray-900 truncate">{{ application.name }}</div>
                      <div class="text-sm text-gray-500 truncate">{{ application.email }}</div>
                      <div class="text-xs text-gray-500 sm:hidden">{{ application.class_name }}</div>
                    </div>
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden sm:table-cell">
                    {{ application.class_name }}
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                    <div class="min-w-0">
                      <div class="text-sm text-gray-900 truncate">{{ application.parent_name }}</div>
                      <div class="text-sm text-gray-500 truncate">{{ application.parent_contact }}</div>
                    </div>
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                    {{ formatDate(application.applied_date) }}
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                      Pending
                    </span>
                  </td>
                  <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex flex-col lg:flex-row lg:space-x-2 space-y-1 lg:space-y-0">
                      <button 
                        @click="approveApplication(application.id)"
                        class="text-green-600 hover:text-green-900 text-left lg:text-center text-xs lg:text-sm"
                      >
                        Approve
                      </button>
                      <button 
                        @click="rejectApplication(application.id)"
                        class="text-red-600 hover:text-red-900 text-left lg:text-center text-xs lg:text-sm"
                      >
                        Reject
                      </button>
                      <button class="text-blue-600 hover:text-blue-900 text-left lg:text-center text-xs lg:text-sm">
                        View
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="applications.length === 0" class="text-center py-8 lg:py-12">
            <svg class="w-8 h-8 lg:w-12 lg:h-12 text-gray-400 mx-auto mb-3 lg:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600 text-sm lg:text-base">No pending applications found</p>
            <p class="text-sm text-gray-500 mt-1">All applications have been processed</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

const applications = ref([])
const loading = ref(true)
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

const fetchApplications = async () => {
  try {
    loading.value = true
    // Simulate API call - replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Mock data for demonstration
    applications.value = [
      {
        id: 1,
        name: 'John Smith',
        email: 'john.smith@email.com',
        class_name: 'Class 10 - Science',
        parent_name: 'Robert Smith',
        parent_contact: '+1 234-567-8900',
        applied_date: '2024-01-15'
      },
      {
        id: 2,
        name: 'Sarah Johnson',
        email: 'sarah.j@email.com',
        class_name: 'Class 8 - Mathematics',
        parent_name: 'Michael Johnson',
        parent_contact: '+1 234-567-8901',
        applied_date: '2024-01-14'
      },
      {
        id: 3,
        name: 'David Wilson',
        email: 'david.wilson@email.com',
        class_name: 'Class 11 - Physics',
        parent_name: 'James Wilson',
        parent_contact: '+1 234-567-8902',
        applied_date: '2024-01-13'
      },
      {
        id: 4,
        name: 'Emma Davis',
        email: 'emma.davis@email.com',
        class_name: 'Class 9 - English',
        parent_name: 'Thomas Davis',
        parent_contact: '+1 234-567-8903',
        applied_date: '2024-01-12'
      },
      {
        id: 5,
        name: 'Michael Brown',
        email: 'michael.b@email.com',
        class_name: 'Class 7 - General',
        parent_name: 'William Brown',
        parent_contact: '+1 234-567-8904',
        applied_date: '2024-01-11'
      }
    ]
  } catch (error) {
    console.error('Error fetching applications:', error)
  } finally {
    loading.value = false
  }
}

const approveApplication = async (applicationId) => {
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    
    // Remove from list
    applications.value = applications.value.filter(app => app.id !== applicationId)
    console.log(`Application ${applicationId} approved`)
  } catch (error) {
    console.error('Error approving application:', error)
  }
}

const rejectApplication = async (applicationId) => {
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    
    // Remove from list
    applications.value = applications.value.filter(app => app.id !== applicationId)
    console.log(`Application ${applicationId} rejected`)
  } catch (error) {
    console.error('Error rejecting application:', error)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

onMounted(() => {
  fetchApplications()
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