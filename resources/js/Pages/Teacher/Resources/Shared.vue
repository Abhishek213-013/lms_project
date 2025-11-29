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
      <!-- Top Navbar -->
      <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="px-4 sm:px-6 py-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center min-w-0 flex-1 lg:flex-none">
              <!-- Mobile menu button space -->
              <div class="w-10 lg:hidden"></div>
              <h1 class="custom-heading truncate text-lg sm:text-xl ml-2 lg:ml-0">Teacher Portal - {{ teacher?.name || 'Loading...' }}</h1>
            </div>
            
            <div class="flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
              <!-- Search - Hidden on mobile, visible on medium screens and up -->
              <div class="relative hidden sm:block">
                <input 
                  type="text" 
                  placeholder="Search..." 
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-40 md:w-64 text-sm"
                >
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>

              <!-- User Menu -->
              <div class="relative flex-shrink-0">
                <button 
                  @click="toggleUserMenu"
                  class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 min-w-0"
                >
                  <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                    <img 
                      v-if="teacher?.profile_picture_url" 
                      :src="teacher.profile_picture_url" 
                      :alt="teacher?.name"
                      class="w-full h-full object-cover"
                    >
                    <span v-else class="text-white text-sm font-semibold">{{ userInitials }}</span>
                  </div>
                  <svg class="w-4 h-4 text-gray-400 flex-shrink-0 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>

                <!-- User Dropdown -->
                <div v-show="userMenuOpen" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-20">
                  <!-- User Info in Dropdown Header -->
                  <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center overflow-hidden">
                        <img 
                          v-if="teacher?.profile_picture_url" 
                          :src="teacher.profile_picture_url" 
                          :alt="teacher?.name"
                          class="w-full h-full object-cover"
                        >
                        <span v-else class="text-white text-sm font-semibold">{{ userInitials }}</span>
                      </div>
                      <div class="text-left min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">{{ teacher?.name || 'Teacher' }}</p>
                        <p class="text-xs text-gray-500 capitalize truncate">Teacher</p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Dropdown Menu Items -->
                  <button 
                    @click="editProfile"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center no-underline"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                  </button>
                  <button 
                    @click="navigateToSettings"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center no-underline"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                  </button>
                  <button 
                    @click="logout"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center no-underline"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Sign out
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <!-- Rest of your content remains exactly the same -->
      <!-- Page Content -->
      <div class="p-4 sm:p-6 max-w-full overflow-x-hidden">
        <!-- Header -->
        <div class="mb-4 sm:mb-6">
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Shared Resources</h1>
          <p class="text-gray-600 text-sm sm:text-base">Discover resources shared by other teachers</p>
        </div>

        <!-- Shared Resources List -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
              <h3 class="text-lg font-semibold text-gray-800">Resources from Other Teachers</h3>
              <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <select v-model="filterType" class="px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-base">
                  <option value="all">All Types</option>
                  <option value="video">Videos</option>
                  <option value="pdf">PDFs</option>
                  <option value="document">Documents</option>
                  <option value="link">Links</option>
                </select>
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search shared resources..." 
                  class="px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-base w-full sm:w-64"
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
              <p class="text-gray-600">Loading shared resources...</p>
            </div>

            <!-- Resources List -->
            <div v-else class="space-y-4">
              <div 
                v-for="resource in filteredSharedResources" 
                :key="resource.id"
                class="border border-gray-200 rounded-lg p-4 sm:p-6 hover:shadow-lg transition-shadow"
              >
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start space-y-4 sm:space-y-0">
                  <div class="flex items-start space-x-3 sm:space-x-4 flex-1">
                    <!-- Resource Icon -->
                    <div :class="`p-2 sm:p-3 rounded-lg ${getResourceTypeColor(resource.type)} flex-shrink-0`">
                      <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="resource.type === 'video'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                      <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 truncate">{{ resource.title }}</h4>
                      <p class="text-gray-600 text-sm sm:text-base mb-3 line-clamp-2">{{ resource.description || 'No description' }}</p>
                      
                      <!-- Teacher and Date Info -->
                      <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 text-xs sm:text-sm text-gray-500 mb-2">
                        <div class="flex items-center space-x-1">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                          </svg>
                          <span class="truncate">By: {{ resource.teacher.name }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          <span>Shared: {{ formatDate(resource.created_at) }}</span>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                          {{ resource.type }}
                        </span>
                      </div>

                      <!-- Stats and Class Info -->
                      <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 text-xs sm:text-sm text-gray-500">
                        <div class="flex items-center space-x-1">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                          </svg>
                          <span>{{ resource.download_count || 0 }} downloads</span>
                        </div>
                        <div v-if="resource.class" class="flex items-center space-x-1">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                          </svg>
                          <span class="truncate">{{ resource.class.name }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Action Buttons -->
                  <div class="flex justify-end space-x-2 sm:space-x-3">
                    <button 
                      @click="viewResource(resource)"
                      class="text-blue-600 hover:text-blue-800 text-sm font-medium p-2 sm:p-0"
                      title="View Resource"
                    >
                      <span class="hidden sm:inline">View</span>
                      <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button 
                      @click="saveToMyResources(resource)"
                      class="text-green-600 hover:text-green-800 text-sm font-medium p-2 sm:p-0"
                      title="Save to My Resources"
                    >
                      <span class="hidden sm:inline">Save</span>
                      <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && filteredSharedResources.length === 0" class="text-center py-8 sm:py-12 text-gray-500">
              <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-base sm:text-lg font-medium mb-2">No shared resources found</p>
              <p class="text-sm mb-4">Shared resources from other teachers will appear here</p>
              <div v-if="searchQuery || filterType !== 'all'" class="mt-4">
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

// Props with default value
const props = defineProps({
  teacher: {
    type: Object,
    default: () => ({
      name: 'Teacher',
      profile_picture_url: null
    })
  }
})

// State
const sharedResources = ref([])
const searchQuery = ref('')
const filterType = ref('all')
const loading = ref(true)
const isMobileMenuOpen = ref(false)
const userMenuOpen = ref(false)

// Computed
const userInitials = computed(() => {
  if (!props.teacher?.name) return 'T'
  return props.teacher.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const filteredSharedResources = computed(() => {
  let filtered = sharedResources.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(resource => 
      resource.title.toLowerCase().includes(query) ||
      resource.description?.toLowerCase().includes(query) ||
      resource.teacher.name.toLowerCase().includes(query) ||
      resource.class?.name.toLowerCase().includes(query)
    )
  }

  if (filterType.value !== 'all') {
    filtered = filtered.filter(resource => resource.type === filterType.value)
  }

  return filtered
})

// Mobile menu functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

// User menu functions
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

// Navigation methods
const navigateToSettings = () => {
  router.visit('/teacher/settings')
}

const editProfile = () => {
  // Add edit profile functionality here
  console.log('Edit profile clicked')
}

const logout = async () => {
  try {
    router.post('/logout')
  } catch (err) {
    console.error('Logout error:', err)
  }
}

// Methods
const fetchSharedResources = async () => {
  try {
    loading.value = true
    // Mock data for demonstration
    sharedResources.value = [
      {
        id: 1,
        title: 'Advanced Calculus Lecture Series',
        description: 'Complete video lecture series covering advanced calculus topics including multivariable calculus and differential equations',
        type: 'video',
        teacher: { name: 'Dr. Sarah Johnson' },
        class: { name: 'Advanced Mathematics' },
        created_at: new Date().toISOString(),
        download_count: 127,
        content: 'https://www.youtube.com/watch?v=advanced-calculus'
      },
      {
        id: 2,
        title: 'Physics Experiment Guide',
        description: 'Comprehensive guide for conducting physics experiments with detailed procedures and safety guidelines',
        type: 'pdf',
        teacher: { name: 'Prof. Michael Chen' },
        class: { name: 'Physics 101' },
        created_at: new Date(Date.now() - 86400000).toISOString(),
        download_count: 89,
        file_path: 'documents/physics-experiment-guide.pdf'
      },
      {
        id: 3,
        title: 'Chemistry Lab Templates',
        description: 'Collection of lab report templates and experiment worksheets for chemistry classes',
        type: 'document',
        teacher: { name: 'Dr. Emily Rodriguez' },
        class: { name: 'Chemistry Basics' },
        created_at: new Date(Date.now() - 172800000).toISOString(),
        download_count: 156,
        file_path: 'documents/chemistry-lab-templates.docx'
      },
      {
        id: 4,
        title: 'Interactive Biology Resources',
        description: 'Links to interactive biology simulations and virtual lab experiences',
        type: 'link',
        teacher: { name: 'Mr. David Wilson' },
        class: { name: 'Biology Fundamentals' },
        created_at: new Date(Date.now() - 259200000).toISOString(),
        download_count: 203,
        content: 'https://www.biointeractive.org'
      },
      {
        id: 5,
        title: 'Mathematics Problem Sets',
        description: 'Challenging problem sets for advanced mathematics students with solutions',
        type: 'pdf',
        teacher: { name: 'Dr. Lisa Thompson' },
        class: { name: 'Advanced Mathematics' },
        created_at: new Date(Date.now() - 345600000).toISOString(),
        download_count: 94,
        file_path: 'documents/math-problem-sets.pdf'
      }
    ]
  } catch (error) {
    console.error('Error fetching shared resources:', error)
  } finally {
    loading.value = false
  }
}

const viewResource = (resource) => {
  if (resource.type === 'video' || resource.type === 'link') {
    window.open(resource.content, '_blank')
  } else if (resource.file_path) {
    window.open(`/storage/${resource.file_path}`, '_blank')
  }
}

const saveToMyResources = async (resource) => {
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    alert('Resource saved to your collection!')
  } catch (error) {
    console.error('Error saving resource:', error)
    alert('Error saving resource. Please try again.')
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  filterType.value = 'all'
}

const getResourceTypeColor = (type) => {
  const colors = {
    video: 'bg-red-500',
    pdf: 'bg-red-600',
    document: 'bg-blue-500',
    link: 'bg-green-500'
  }
  return colors[type] || 'bg-gray-500'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Handle escape key to close mobile menu
const handleEscape = (event) => {
  if (event.key === 'Escape') {
    if (isMobileMenuOpen.value) {
      closeMobileMenu()
    }
    if (userMenuOpen.value) {
      userMenuOpen.value = false
    }
  }
}

// Handle click outside to close user menu
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchSharedResources()
  document.addEventListener('keydown', handleEscape)
  document.addEventListener('click', handleClickOutside)
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

/* Ensure smooth transitions */
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Z-index for proper layering */
.z-30 {
  z-index: 30;
}

.z-40 {
  z-index: 40;
}

.z-50 {
  z-index: 50;
}

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Mobile-specific adjustments */
@media (max-width: 640px) {
  .custom-heading {
    font-size: 1.125rem;
  }
}
</style>