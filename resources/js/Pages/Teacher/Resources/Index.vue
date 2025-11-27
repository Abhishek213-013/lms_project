<template>
  <div class="min-h-screen bg-gray-50">
    <TeacherSidebar @showUploadModal="showUploadModal = true" />
    
    <div class="ml-64 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">My Resources</h1>
              <p class="text-gray-600">Manage your teaching materials and resources</p>
            </div>
            <button 
              @click="showUploadModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Upload Resource</span>
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Total Resources</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ resources.length }}</h3>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Videos</p>
                <h3 class="text-3xl font-bold text-green-600">{{ videoCount }}</h3>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Documents</p>
                <h3 class="text-3xl font-bold text-purple-600">{{ documentCount }}</h3>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Total Downloads</p>
                <h3 class="text-3xl font-bold text-orange-600">{{ totalDownloads }}</h3>
              </div>
              <div class="p-3 bg-orange-100 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Resources List -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-semibold text-gray-800">All Resources</h3>
              <div class="flex space-x-3">
                <select v-model="filterType" class="px-3 py-2 border border-gray-300 rounded-lg">
                  <option value="all">All Types</option>
                  <option value="video">Videos</option>
                  <option value="pdf">PDFs</option>
                  <option value="document">Documents</option>
                  <option value="link">Links</option>
                </select>
                <select v-model="filterClass" class="px-3 py-2 border border-gray-300 rounded-lg">
                  <option value="all">All Classes</option>
                  <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                    {{ classItem.name }}
                  </option>
                </select>
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search resources..." 
                  class="px-3 py-2 border border-gray-300 rounded-lg w-64"
                >
              </div>
            </div>
          </div>

          <div class="p-6">
            <div class="space-y-4">
              <div 
                v-for="resource in filteredResources" 
                :key="resource.id"
                class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow"
              >
                <div class="flex justify-between items-start">
                  <div class="flex items-start space-x-4 flex-1">
                    <!-- Resource Icon -->
                    <div :class="`p-3 rounded-lg ${getResourceTypeColor(resource.type)}`">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="resource.type === 'video'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    
                    <div class="flex-1">
                      <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ resource.title }}</h4>
                      <p class="text-gray-600 mb-3">{{ resource.description || 'No description' }}</p>
                      
                      <div class="flex items-center space-x-4 text-sm text-gray-500">
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
                  
                  <div class="flex space-x-2">
                    <button 
                      @click="viewResource(resource)"
                      class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                      View
                    </button>
                    <button 
                      @click="shareResource(resource)"
                      class="text-green-600 hover:text-green-800 text-sm font-medium"
                    >
                      Share
                    </button>
                    <button 
                      @click="deleteResource(resource.id)"
                      class="text-red-600 hover:text-red-800 text-sm font-medium"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="filteredResources.length === 0" class="text-center py-12 text-gray-500">
              <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-lg font-medium mb-2">No resources found</p>
              <p class="text-sm mb-4">Upload your first resource to get started</p>
              <button 
                @click="showUploadModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
              >
                Upload Resource
              </button>
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
    const response = await fetch('/api/teacher/resources')
    const result = await response.json()
    if (result.success) {
      resources.value = result.data
    }
  } catch (error) {
    console.error('Error fetching resources:', error)
  }
}

const fetchClasses = async () => {
  try {
    const response = await fetch('/api/teacher/classes')
    const result = await response.json()
    if (result.success) {
      classes.value = result.data
    }
  } catch (error) {
    console.error('Error fetching classes:', error)
  }
}

const viewResource = (resource) => {
  if (resource.type === 'video') {
    // Open video player
    window.open(resource.content, '_blank')
  } else if (resource.file_path) {
    // Download file
    window.open(`/storage/${resource.file_path}`, '_blank')
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
    // Fallback: Copy to clipboard
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
      const response = await fetch(`/api/resources/${resourceId}`, {
        method: 'DELETE'
      })
      const result = await response.json()
      if (result.success) {
        await fetchResources()
      } else {
        alert('Failed to delete resource: ' + result.message)
      }
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
  return new Date(dateString).toLocaleDateString()
}

// Lifecycle
onMounted(() => {
  fetchResources()
  fetchClasses()
})
</script>