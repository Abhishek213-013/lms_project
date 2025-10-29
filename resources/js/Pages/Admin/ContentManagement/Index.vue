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
                <textarea
                  v-model="content[fieldKey]"
                  @blur="saveContent(fieldKey, content[fieldKey])"
                  :rows="fieldKey.includes('content') ? 6 : 3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  :placeholder="`Enter ${fieldLabel.toLowerCase()}...`"
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">
                  Character count: {{ content[fieldKey] ? content[fieldKey].length : 0 }}
                </p>
              </div>
            </div>

            <!-- Save Button -->
            <div class="mt-8 flex justify-end">
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
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
const successMessage = ref('')
const errorMessage = ref('')

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

// Methods
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
  if (Object.keys(localContent.value).length === 0) {
    showMessage('No changes to save', 'info')
    return
  }

  try {
    saving.value = true
    
    // Save each modified field sequentially
    for (const [key, value] of Object.entries(localContent.value)) {
      await new Promise((resolve) => {
        saveForm.key = key
        saveForm.value = value
        
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
    
    // Clear local changes after successful save
    localContent.value = {}
    
  } catch (error) {
    console.error('❌ Failed to save content:', error)
    showMessage('Failed to save content. Please try again.', 'error')
  } finally {
    saving.value = false
  }
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
})
</script>