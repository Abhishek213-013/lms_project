<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Content Management" @search="handleSearch" />
      
      <!-- Page Content -->
      <div class="p-6">
        <!-- Welcome Message -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Content Management</h1>
          <p class="text-gray-600">Manage all frontend content and text across your application.</p>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="flashSuccess" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
          {{ flashSuccess }}
        </div>
        
        <div v-if="flashError" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
          {{ flashError }}
        </div>

        <div v-if="successMessage" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
          {{ successMessage }}
        </div>
        
        <div v-if="errorMessage" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
          {{ errorMessage }}
        </div>

        <!-- Tabs for Different Sections -->
        <div class="mb-6">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
              <button
                v-for="(section, sectionKey) in sections"
                :key="sectionKey"
                @click="activeSection = sectionKey"
                :class="[
                  'py-2 px-1 border-b-2 font-medium text-sm',
                  activeSection === sectionKey
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ section.title }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Content Editor -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div v-if="activeSectionData">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ activeSectionData.title }}</h3>
            <p class="text-gray-600 mb-6">{{ activeSectionData.description }}</p>

            <!-- Content Fields -->
            <div class="space-y-6">
              <div v-for="(fieldLabel, fieldKey) in activeSectionData.fields" :key="fieldKey">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ fieldLabel }}
                </label>
                
                <!-- Image Upload Field -->
                <div v-if="fieldKey.includes('image')" class="space-y-4">
                  <!-- Current Image Preview -->
                  <div v-if="content[fieldKey] && content[fieldKey] !== getDefaultImage(fieldKey)" class="border rounded-lg p-4 bg-gray-50">
                    <p class="text-sm text-gray-600 mb-2">Current Image Preview:</p>
                    <div class="flex items-start space-x-4">
                      <img 
                        :src="content[fieldKey]" 
                        :alt="fieldLabel"
                        class="w-32 h-32 object-contain rounded-lg shadow-sm border bg-white"
                        @error="handleImagePreviewError"
                      />
                      <div class="flex-1">
                        <p class="text-xs text-gray-500 mb-2 break-all">{{ content[fieldKey] }}</p>
                        <button
                          @click="removeImage(fieldKey)"
                          class="px-3 py-1 bg-red-100 text-red-700 rounded-md text-sm hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500"
                        >
                          Remove Image
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Image Upload Controls -->
                  <div class="space-y-3">
                    <div class="flex items-center space-x-4">
                      <input
                        type="text"
                        v-model="imageUrls[fieldKey]"
                        @blur="saveImageUrl(fieldKey, imageUrls[fieldKey])"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Enter image URL or upload a file..."
                      />
                      <button
                        @click="openImageUpload(fieldKey)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center space-x-2"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span>Upload</span>
                      </button>
                    </div>
                    
                    <div class="flex items-center space-x-4 text-sm">
                      <button
                        @click="resetToDefault(fieldKey)"
                        class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
                      >
                        Reset to Default
                      </button>
                      <span class="text-xs text-gray-500">
                        Supported formats: JPEG, PNG, GIF, SVG, WebP (Max 5MB)
                      </span>
                    </div>
                  </div>
                </div>
                
                <!-- Text Field -->
                <div v-else>
                  <textarea
                    v-model="content[fieldKey]"
                    @blur="saveContent(fieldKey, content[fieldKey])"
                    :rows="getTextareaRows(fieldKey)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :placeholder="`Enter ${fieldLabel.toLowerCase()}...`"
                  ></textarea>
                  <p class="mt-1 text-xs text-gray-500">
                    Character count: {{ content[fieldKey] ? content[fieldKey].length : 0 }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Save Button -->
            <div class="mt-8 flex justify-end space-x-3">
              <button
                @click="resetSectionToDefaults"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Reset Section
              </button>
              <button
                @click="saveAllContent"
                :disabled="saving"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="saving">Saving...</span>
                <span v-else>Save All Changes</span>
              </button>
            </div>
          </div>

          <!-- Loading State -->
          <div v-else class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-2 text-gray-600">Loading content...</p>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Last Updated</p>
                <p class="text-lg font-semibold text-gray-800">{{ lastUpdated }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Total Fields</p>
                <p class="text-lg font-semibold text-gray-800">{{ totalFields }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Modified</p>
                <p class="text-lg font-semibold text-gray-800">{{ modifiedFields }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Upload Modal -->
    <div v-if="showImageUpload" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Upload Image</h3>
          <button @click="closeImageUpload" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="space-y-4">
          <!-- File Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Image File</label>
            <input
              type="file"
              ref="fileInput"
              @change="handleFileSelect"
              accept="image/*"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>
          
          <!-- File Preview -->
          <div v-if="selectedFile && filePreviewUrl" class="border rounded-lg p-3">
            <p class="text-sm text-gray-600 mb-2">Preview:</p>
            <img :src="filePreviewUrl" :alt="selectedFile.name" class="max-w-full h-32 object-contain mx-auto rounded" />
            <p class="text-xs text-gray-500 mt-2 text-center">{{ selectedFile.name }} ({{ formatFileSize(selectedFile.size) }})</p>
          </div>
          
          <!-- Upload Progress -->
          <div v-if="uploading" class="space-y-2">
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
            </div>
            <p class="text-xs text-gray-600 text-center">Uploading... {{ uploadProgress }}%</p>
          </div>
          
          <!-- Actions -->
          <div class="flex justify-end space-x-3 pt-4">
            <button
              @click="closeImageUpload"
              class="px-4 py-2 text-gray-600 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Cancel
            </button>
            <button
              @click="uploadImage"
              :disabled="!selectedFile || uploading"
              class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="uploading">Uploading...</span>
              <span v-else>Upload Image</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import Navbar from '../../Layout/Navbar.vue'
import Sidebar from '../../Layout/Sidebar.vue'

// Props from Laravel backend
const props = defineProps({
  content: {
    type: Object,
    default: () => ({})
  },
  sections: {
    type: Object,
    default: () => ({})
  },
  flash: {
    type: Object,
    default: () => ({})
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

// Reactive data
const activeSection = ref('about')
const saving = ref(false)
const localContent = ref({})
const imageUrls = ref({})
const successMessage = ref('')
const errorMessage = ref('')
const showImageUpload = ref(false)
const uploading = ref(false)
const uploadProgress = ref(0)
const currentImageField = ref('')
const selectedFile = ref(null)
const filePreviewUrl = ref(null)
const fileInput = ref(null)

// Use Inertia form for content saving
const saveForm = useForm({
  key: '',
  value: ''
})

// Computed properties for safe flash message access
const flashSuccess = computed(() => {
  return props.flash?.success || ''
})

const flashError = computed(() => {
  return props.flash?.error || ''
})

const activeSectionData = computed(() => props.sections[activeSection.value])
const totalFields = computed(() => {
  return Object.keys(props.sections).reduce((total, sectionKey) => {
    return total + Object.keys(props.sections[sectionKey].fields).length
  }, 0)
})
const modifiedFields = computed(() => {
  return Object.keys(localContent.value).filter(key => 
    localContent.value[key] !== props.content[key]
  ).length
})
const lastUpdated = computed(() => {
  return new Date().toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
})

// Initialize content
const content = computed({
  get: () => {
    const sectionFields = props.sections[activeSection.value]?.fields || {}
    const sectionContent = {}
    
    Object.keys(sectionFields).forEach(fieldKey => {
      sectionContent[fieldKey] = localContent.value[fieldKey] || props.content[fieldKey] || ''
    })
    
    return sectionContent
  },
  set: (value) => {
    Object.keys(value).forEach(key => {
      localContent.value[key] = value[key]
    })
  }
})

// Default images for different fields
const defaultImages = {
  'home_hero_image': '/assets/img/h2_banner_img.png'
}

// Methods
const getDefaultImage = (fieldKey) => {
  return defaultImages[fieldKey] || '/assets/img/placeholder.jpg'
}

const getTextareaRows = (fieldKey) => {
  if (fieldKey.includes('content')) return 6
  if (fieldKey.includes('title') || fieldKey.includes('subtitle')) return 3
  return 2
}

const showMessage = (message, type = 'success') => {
  if (type === 'success') {
    successMessage.value = message
    errorMessage.value = ''
  } else {
    errorMessage.value = message
    successMessage.value = ''
  }
  
  setTimeout(() => {
    successMessage.value = ''
    errorMessage.value = ''
  }, 5000)
}

const handleImagePreviewError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y3ZmFmYyIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5Y2EwYTYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjM1ZW0iPuKKojwvdGV4dD48L3N2Zz4='
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const openImageUpload = (fieldKey) => {
  currentImageField.value = fieldKey
  showImageUpload.value = true
  selectedFile.value = null
  filePreviewUrl.value = null
  uploadProgress.value = 0
  
  nextTick(() => {
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  })
}

const closeImageUpload = () => {
  showImageUpload.value = false
  currentImageField.value = ''
  selectedFile.value = null
  filePreviewUrl.value = null
  uploadProgress.value = 0
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file type and size
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp']
    const maxSize = 5 * 1024 * 1024 // 5MB
    
    if (!validTypes.includes(file.type)) {
      showMessage('Please select a valid image file (JPEG, PNG, GIF, SVG, WebP)', 'error')
      return
    }
    
    if (file.size > maxSize) {
      showMessage('Image size should be less than 5MB', 'error')
      return
    }
    
    selectedFile.value = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      filePreviewUrl.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const uploadImage = async () => {
  if (!selectedFile.value) {
    showMessage('Please select an image file', 'error')
    return
  }

  try {
    uploading.value = true
    uploadProgress.value = 0
    
    const formData = new FormData()
    formData.append('image', selectedFile.value)
    formData.append('key', currentImageField.value)

    const xhr = new XMLHttpRequest()
    
    xhr.upload.addEventListener('progress', (event) => {
      if (event.lengthComputable) {
        const percentComplete = (event.loaded / event.total) * 100
        uploadProgress.value = Math.round(percentComplete)
      }
    })
    
    xhr.addEventListener('load', () => {
      if (xhr.status === 200) {
        const result = JSON.parse(xhr.responseText)
        
        if (result.success) {
          // Update the content with the new image URL
          localContent.value[currentImageField.value] = result.url
          imageUrls.value[currentImageField.value] = result.url
          showMessage('Image uploaded successfully!')
          closeImageUpload()
        } else {
          showMessage(result.error || 'Failed to upload image', 'error')
        }
      } else {
        showMessage('Upload failed. Please try again.', 'error')
      }
      uploading.value = false
    })
    
    xhr.addEventListener('error', () => {
      showMessage('Upload failed. Please try again.', 'error')
      uploading.value = false
    })
    
    xhr.open('POST', '/admin/content-management/upload-image')
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))
    xhr.send(formData)
    
  } catch (error) {
    console.error('Image upload error:', error)
    showMessage('Error uploading image', 'error')
    uploading.value = false
  }
}

const removeImage = async (fieldKey) => {
  if (!confirm('Are you sure you want to remove this image? It will be reset to the default image.')) {
    return
  }

  try {
    const response = await fetch('/admin/content-management/remove-image', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({ key: fieldKey })
    })

    const result = await response.json()

    if (result.success) {
      // Update the content with the default image
      localContent.value[fieldKey] = result.default_value
      imageUrls.value[fieldKey] = result.default_value
      showMessage('Image removed successfully!')
    } else {
      showMessage(result.error || 'Failed to remove image', 'error')
    }
  } catch (error) {
    console.error('Image removal error:', error)
    showMessage('Error removing image', 'error')
  }
}

const resetToDefault = (fieldKey) => {
  localContent.value[fieldKey] = getDefaultImage(fieldKey)
  imageUrls.value[fieldKey] = getDefaultImage(fieldKey)
  saveContent(fieldKey, getDefaultImage(fieldKey))
}

const saveImageUrl = (fieldKey, url) => {
  if (url && url !== props.content[fieldKey]) {
    localContent.value[fieldKey] = url
    saveContent(fieldKey, url)
  }
}

const saveContent = async (key, value) => {
  if (!key || value === undefined) {
    showMessage('Invalid content data', 'error')
    return
  }

  // Don't save if value hasn't changed
  if (props.content[key] === value) {
    return
  }

  try {
    saving.value = true
    console.log('Saving content:', { key, value: value.substring(0, 50) + '...' })
    
    // Use Inertia form to post to the web route
    saveForm.key = key
    saveForm.value = value
    
    saveForm.post('/admin/content-management/save', {
      preserveScroll: true,
      preserveState: true,
      onSuccess: (page) => {
        console.log('✅ Content saved successfully')
        // Use server flash message or show local success
        if (page.props.flash?.success) {
          showMessage(page.props.flash.success)
        } else {
          showMessage('Content saved successfully!')
        }
        
        // Update the local content to match the saved state
        localContent.value[key] = value
      },
      onError: (errors) => {
        console.error('Form errors:', errors)
        const errorMsg = errors.message || Object.values(errors).join(', ') || 'Failed to save content'
        showMessage(errorMsg, 'error')
      },
      onFinish: () => {
        saving.value = false
        saveForm.reset()
      }
    })
    
  } catch (error) {
    console.error('❌ Error saving content:', error)
    showMessage('Error saving content: ' + error.message, 'error')
    saving.value = false
  }
}

const saveAllContent = async () => {
  const changes = Object.keys(localContent.value).filter(key => 
    localContent.value[key] !== props.content[key]
  )

  if (changes.length === 0) {
    showMessage('No changes to save', 'info')
    return
  }

  try {
    saving.value = true
    
    // Save each modified field sequentially
    for (const key of changes) {
      await new Promise((resolve) => {
        saveForm.key = key
        saveForm.value = localContent.value[key]
        
        saveForm.post('/admin/content-management/save', {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            console.log(`✅ Saved: ${key}`)
            resolve()
          },
          onError: (errors) => {
            console.error(`❌ Failed to save: ${key}`, errors)
            resolve() // Continue with next even if one fails
          }
        })
      })
      
      // Small delay between saves to avoid overwhelming the server
      await new Promise(resolve => setTimeout(resolve, 100))
    }

    console.log('✅ All content saved successfully')
    showMessage('All changes saved successfully!')
    
  } catch (error) {
    console.error('❌ Failed to save content:', error)
    showMessage('Failed to save content. Please try again.', 'error')
  } finally {
    saving.value = false
  }
}

const resetSectionToDefaults = () => {
  if (!confirm('Are you sure you want to reset all fields in this section to their default values?')) {
    return
  }

  const sectionFields = props.sections[activeSection.value]?.fields || {}
  Object.keys(sectionFields).forEach(fieldKey => {
    if (fieldKey.includes('image')) {
      localContent.value[fieldKey] = getDefaultImage(fieldKey)
      imageUrls.value[fieldKey] = getDefaultImage(fieldKey)
    } else {
      // For text fields, we don't have easy access to defaults, so clear them
      localContent.value[fieldKey] = ''
    }
  })
  
  showMessage('Section reset to defaults. Click "Save All Changes" to apply.')
}

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Initialize when component mounts
onMounted(() => {
  console.log('📝 Content Management loaded')
  console.log('Initial content:', props.content)
  console.log('Sections:', props.sections)
  console.log('Flash messages:', props.flash)
  console.log('Errors:', props.errors)
  
  // Initialize image URLs
  const imageFields = ['home_hero_image']
  imageFields.forEach(field => {
    imageUrls.value[field] = props.content[field] || getDefaultImage(field)
  })
})
</script>

