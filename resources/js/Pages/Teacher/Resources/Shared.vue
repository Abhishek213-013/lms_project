<template>
  <div class="min-h-screen bg-gray-50">
    <TeacherSidebar />
    
    <div class="ml-64 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Shared Resources</h1>
          <p class="text-gray-600">Discover resources shared by other teachers</p>
        </div>

        <!-- Shared Resources List -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-semibold text-gray-800">Resources from Other Teachers</h3>
              <div class="flex space-x-3">
                <select v-model="filterType" class="px-3 py-2 border border-gray-300 rounded-lg">
                  <option value="all">All Types</option>
                  <option value="video">Videos</option>
                  <option value="pdf">PDFs</option>
                  <option value="document">Documents</option>
                </select>
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search shared resources..." 
                  class="px-3 py-2 border border-gray-300 rounded-lg w-64"
                >
              </div>
            </div>
          </div>

          <div class="p-6">
            <div class="space-y-4">
              <div 
                v-for="resource in filteredSharedResources" 
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
                      
                      <div class="flex items-center space-x-4 text-sm text-gray-500 mb-2">
                        <div class="flex items-center space-x-1">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                          </svg>
                          <span>By: {{ resource.teacher.name }}</span>
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

                      <div class="flex items-center space-x-4 text-sm text-gray-500">
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
                          <span>{{ resource.class.name }}</span>
                        </div>
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
                      @click="saveToMyResources(resource)"
                      class="text-green-600 hover:text-green-800 text-sm font-medium"
                    >
                      Save
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="filteredSharedResources.length === 0" class="text-center py-12 text-gray-500">
              <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-lg font-medium mb-2">No shared resources found</p>
              <p class="text-sm">Shared resources from other teachers will appear here</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'

// State
const sharedResources = ref([])
const searchQuery = ref('')
const filterType = ref('all')

// Computed
const filteredSharedResources = computed(() => {
  let filtered = sharedResources.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(resource => 
      resource.title.toLowerCase().includes(query) ||
      resource.description?.toLowerCase().includes(query) ||
      resource.teacher.name.toLowerCase().includes(query)
    )
  }

  if (filterType.value !== 'all') {
    filtered = filtered.filter(resource => resource.type === filterType.value)
  }

  return filtered
})

// Methods
const fetchSharedResources = async () => {
  try {
    const response = await fetch('/api/resources/shared')
    const result = await response.json()
    if (result.success) {
      sharedResources.value = result.data
    }
  } catch (error) {
    console.error('Error fetching shared resources:', error)
  }
}

const viewResource = (resource) => {
  if (resource.type === 'video') {
    window.open(resource.content, '_blank')
  } else if (resource.file_path) {
    window.open(`/storage/${resource.file_path}`, '_blank')
  }
}

const saveToMyResources = async (resource) => {
  try {
    const response = await fetch(`/api/resources/${resource.id}/save`, {
      method: 'POST'
    })
    const result = await response.json()
    if (result.success) {
      alert('Resource saved to your collection!')
    } else {
      alert('Failed to save resource: ' + result.message)
    }
  } catch (error) {
    console.error('Error saving resource:', error)
    alert('Error saving resource. Please try again.')
  }
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
  fetchSharedResources()
})
</script>