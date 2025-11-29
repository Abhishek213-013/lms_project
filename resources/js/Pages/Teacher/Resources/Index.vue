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
      @showUploadModal="showUploadModal = true"
      @close-mobile="closeMobileMenu"
    />

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64 min-w-0 w-full transition-all duration-300">
      <!-- Page Content -->
      <div class="p-4 sm:p-6 max-w-full overflow-x-hidden">
        <!-- Header -->
        <div class="mb-4 sm:mb-6">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <div>
              <h1 class="text-xl sm:text-2xl font-bold text-gray-900">My Resources</h1>
              <p class="text-gray-600 text-sm sm:text-base">Manage your teaching materials and resources</p>
            </div>
            <button 
              @click="showUploadModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 justify-center sm:justify-start"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Upload Resource</span>
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Total Resources</p>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-600">{{ resources.length }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-blue-100 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Videos</p>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-green-600">{{ videoCount }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-green-100 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Documents</p>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-purple-600">{{ documentCount }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-purple-100 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Total Downloads</p>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-orange-600">{{ totalDownloads }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-orange-100 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Resources List -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
              <h3 class="text-lg font-semibold text-gray-800">All Resources</h3>
              <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <select v-model="filterType" class="px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-base">
                  <option value="all">All Types</option>
                  <option value="video">Videos</option>
                  <option value="pdf">PDFs</option>
                  <option value="document">Documents</option>
                  <option value="link">Links</option>
                </select>
                <select v-model="filterClass" class="px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-base">
                  <option value="all">All Classes</option>
                  <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                    {{ classItem.name }}
                  </option>
                </select>
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search resources..." 
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
              <p class="text-gray-600">Loading resources...</p>
            </div>

            <!-- Resources List -->
            <div v-else class="space-y-4">
              <div 
                v-for="resource in filteredResources" 
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
                      
                      <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 text-xs sm:text-sm text-gray-500">
                        <div class="flex items-center space-x-1">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          <span>Uploaded: {{ formatDate(resource.created_at) }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                          </svg>
                          <span>{{ resource.download_count || 0 }} downloads</span>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                          {{ resource.type }}
                        </span>
                        <span v-if="resource.class" class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                          {{ resource.class }}
                        </span>
                      </div>
                    </div>
                  </div>
                  
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
                      @click="shareResource(resource)"
                      class="text-green-600 hover:text-green-800 text-sm font-medium p-2 sm:p-0"
                      title="Share Resource"
                    >
                      <span class="hidden sm:inline">Share</span>
                      <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                      </svg>
                    </button>
                    <button 
                      @click="deleteResource(resource.id)"
                      class="text-red-600 hover:text-red-800 text-sm font-medium p-2 sm:p-0"
                      title="Delete Resource"
                    >
                      <span class="hidden sm:inline">Delete</span>
                      <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && filteredResources.length === 0" class="text-center py-8 sm:py-12 text-gray-500">
              <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-base sm:text-lg font-medium mb-2">No resources found</p>
              <p class="text-sm mb-4">Upload your first resource to get started</p>
              <button 
                @click="showUploadModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
              >
                Upload Resource
              </button>
              <div v-if="searchQuery || filterType !== 'all' || filterClass !== 'all'" class="mt-4">
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

    <!-- Upload Modal -->
    <UploadResourceModal 
      v-if="showUploadModal"
      @close="showUploadModal = false"
      @uploaded="handleResourceUploaded"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'

// State
const resources = ref([])
const classes = ref([])
const showUploadModal = ref(false)
const searchQuery = ref('')
const filterType = ref('all')
const filterClass = ref('all')
const loading = ref(true)
const isMobileMenuOpen = ref(false)

// Mobile menu functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

// Computed
const videoCount = computed(() => {
  return resources.value.filter(r => r.type === 'video').length
})

const documentCount = computed(() => {
  return resources.value.filter(r => ['pdf', 'document'].includes(r.type)).length
})

const totalDownloads = computed(() => {
  return resources.value.reduce((sum, resource) => sum + (resource.download_count || 0), 0)
})

const filteredResources = computed(() => {
  let filtered = resources.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(resource => 
      resource.title.toLowerCase().includes(query) ||
      resource.description?.toLowerCase().includes(query)
    )
  }

  if (filterType.value !== 'all') {
    filtered = filtered.filter(resource => resource.type === filterType.value)
  }

  if (filterClass.value !== 'all') {
    filtered = filtered.filter(resource => resource.class_id == filterClass.value)
  }

  return filtered
})

// Methods
const fetchResources = async () => {
  try {
    loading.value = true
    // Mock data for demonstration
    resources.value = [
      {
        id: 1,
        title: 'Introduction to Algebra',
        description: 'Basic algebra concepts and examples for beginners',
        type: 'video',
        class_id: 1,
        class: 'Advanced Mathematics',
        created_at: new Date().toISOString(),
        download_count: 45,
        content: 'https://www.youtube.com/watch?v=example1'
      },
      {
        id: 2,
        title: 'Physics Lab Manual',
        description: 'Complete lab manual for Physics 101 experiments',
        type: 'pdf',
        class_id: 2,
        class: 'Physics 101',
        created_at: new Date(Date.now() - 86400000).toISOString(),
        download_count: 32,
        file_path: 'documents/physics-lab-manual.pdf'
      },
      {
        id: 3,
        title: 'Chemistry Formulas Sheet',
        description: 'Important chemical formulas and equations',
        type: 'document',
        class_id: 3,
        class: 'Chemistry Basics',
        created_at: new Date(Date.now() - 172800000).toISOString(),
        download_count: 28,
        file_path: 'documents/chemistry-formulas.docx'
      },
      {
        id: 4,
        title: 'Interactive Math Exercises',
        description: 'Online interactive math exercises and quizzes',
        type: 'link',
        class_id: 1,
        class: 'Advanced Mathematics',
        created_at: new Date(Date.now() - 259200000).toISOString(),
        download_count: 15,
        content: 'https://www.khanacademy.org/math'
      }
    ]
  } catch (error) {
    console.error('Error fetching resources:', error)
  } finally {
    loading.value = false
  }
}

const fetchClasses = async () => {
  try {
    // Mock classes data
    classes.value = [
      { id: 1, name: 'Advanced Mathematics' },
      { id: 2, name: 'Physics 101' },
      { id: 3, name: 'Chemistry Basics' },
      { id: 4, name: 'Biology Fundamentals' }
    ]
  } catch (error) {
    console.error('Error fetching classes:', error)
  }
}

const viewResource = (resource) => {
  if (resource.type === 'video') {
    window.open(resource.content, '_blank')
  } else if (resource.file_path) {
    window.open(`/storage/${resource.file_path}`, '_blank')
  } else if (resource.type === 'link') {
    window.open(resource.content, '_blank')
  }
}

const shareResource = async (resource) => {
  const shareData = {
    title: resource.title,
    text: resource.description,
    url: resource.content || resource.file_path
  }

  if (navigator.share) {
    try {
      await navigator.share(shareData)
    } catch (error) {
      if (error.name !== 'AbortError') {
        console.error('Error sharing:', error)
      }
    }
  } else {
    const shareText = `${resource.title}\n${resource.description}\n\nAccess: ${resource.content || resource.file_path}`
    try {
      await navigator.clipboard.writeText(shareText)
      alert('Resource link copied to clipboard!')
    } catch (error) {
      alert('Share feature not supported. Please copy the URL manually.')
    }
  }
}

const deleteResource = async (resourceId) => {
  if (confirm('Are you sure you want to delete this resource?')) {
    try {
      resources.value = resources.value.filter(r => r.id !== resourceId)
    } catch (error) {
      console.error('Error deleting resource:', error)
      alert('Error deleting resource. Please try again.')
    }
  }
}

const handleResourceUploaded = () => {
  showUploadModal.value = false
  fetchResources()
}

const clearFilters = () => {
  searchQuery.value = ''
  filterType.value = 'all'
  filterClass.value = 'all'
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
  if (event.key === 'Escape' && isMobileMenuOpen.value) {
    closeMobileMenu()
  }
}

// Lifecycle
onMounted(() => {
  fetchResources()
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