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
          <p class="text-gray-600">Manage all frontend content and text across your application in both English and Bengali.</p>
        </div>

        <!-- Language Toggle -->
        <div class="mb-6">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-800">Language Management</h3>
                <p class="text-gray-600">Switch between English and Bengali to manage content for both languages</p>
              </div>
              <div class="flex space-x-2 bg-gray-100 rounded-lg p-1">
                <button
                  @click="activeLanguage = 'en'"
                  :class="[
                    'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                    activeLanguage === 'en'
                      ? 'bg-white text-gray-900 shadow-sm'
                      : 'text-gray-600 hover:text-gray-900'
                  ]"
                >
                  English
                </button>
                <button
                  @click="activeLanguage = 'bn'"
                  :class="[
                    'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                    activeLanguage === 'bn'
                      ? 'bg-white text-gray-900 shadow-sm'
                      : 'text-gray-600 hover:text-gray-900'
                  ]"
                >
                  বাংলা (Bengali)
                </button>
              </div>
            </div>
          </div>
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

            <!-- Language Indicator -->
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
              <div class="flex items-center">
                <span class="text-sm font-medium text-blue-800">
                  Currently editing in: 
                  <span class="font-bold">{{ activeLanguage === 'en' ? 'English' : 'বাংলা (Bengali)' }}</span>
                </span>
                <span class="ml-2 text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded">
                  {{ activeLanguage === 'en' ? 'EN' : 'BN' }}
                </span>
              </div>
            </div>

            <!-- Content Fields -->
            <div class="space-y-6">
              <div v-for="(fieldLabel, fieldKey) in activeSectionData.fields" :key="fieldKey">
                <!-- Special handling for image fields -->
                <div v-if="fieldKey.includes('_image')">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ fieldLabel }}
                    <span class="text-xs text-gray-500 ml-1">
                      ({{ activeLanguage === 'en' ? 'English' : 'Bengali' }})
                    </span>
                  </label>
                  
                  <!-- Image Upload Section -->
                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-2">
                    <!-- English Version Image -->
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                      <label class="block text-xs font-medium text-gray-600 mb-1">
                        English Version Image
                      </label>
                      
                      <!-- Current Image Preview -->
                      <div v-if="englishContent[fieldKey] && englishContent[fieldKey] !== getDefaultImage(fieldKey, 'en')" class="mb-3">
                        <p class="text-xs text-gray-500 mb-2">Current Image:</p>
                        <img 
                          :src="englishContent[fieldKey]" 
                          :alt="fieldLabel"
                          class="w-full h-32 object-cover rounded-lg border border-gray-300"
                        >
                        <p class="text-xs text-gray-500 mt-1 truncate">{{ englishContent[fieldKey] }}</p>
                      </div>
                      
                      <!-- Default Image Preview -->
                      <div v-else class="mb-3">
                        <p class="text-xs text-gray-500 mb-2">Default Image:</p>
                        <img 
                          :src="getDefaultImage(fieldKey, 'en')" 
                          :alt="fieldLabel"
                          class="w-full h-32 object-cover rounded-lg border border-gray-300 opacity-75"
                        >
                        <p class="text-xs text-gray-500 mt-1">Using default image</p>
                      </div>
                      
                      <!-- Image Upload Controls -->
                      <div class="space-y-2">
                        <input
                          type="file"
                          :ref="el => addFileInputRef(el, fieldKey, 'en')"
                          accept="image/*"
                          class="hidden"
                          @change="handleImageUpload($event, fieldKey, 'en')"
                        />
                        
                        <button
                          type="button"
                          @click="triggerFileInput(fieldKey, 'en')"
                          class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                          <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                          </svg>
                          Upload New Image
                        </button>
                        
                        <button
                          v-if="englishContent[fieldKey] && englishContent[fieldKey] !== getDefaultImage(fieldKey, 'en')"
                          type="button"
                          @click="removeImage(fieldKey, 'en')"
                          class="w-full px-3 py-2 bg-red-50 border border-red-200 rounded-md text-sm font-medium text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        >
                          <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                          Remove Image
                        </button>
                      </div>
                      
                      <!-- Upload Progress -->
                      <div v-if="uploadProgress[fieldKey]?.en > 0" class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div 
                            class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                            :style="{ width: uploadProgress[fieldKey].en + '%' }"
                          ></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Uploading: {{ uploadProgress[fieldKey].en }}%</p>
                      </div>
                    </div>

                    <!-- Bengali Version Image -->
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50" :class="{ 'border-blue-300 bg-blue-50': activeLanguage === 'bn' }">
                      <label class="block text-xs font-medium text-gray-600 mb-1">
                        Bengali Version Image
                      </label>
                      
                      <!-- Current Image Preview -->
                      <div v-if="bengaliContent[fieldKey] && bengaliContent[fieldKey] !== getDefaultImage(fieldKey, 'bn')" class="mb-3">
                        <p class="text-xs text-gray-500 mb-2">Current Image:</p>
                        <img 
                          :src="bengaliContent[fieldKey]" 
                          :alt="fieldLabel"
                          class="w-full h-32 object-cover rounded-lg border border-gray-300"
                        >
                        <p class="text-xs text-gray-500 mt-1 truncate">{{ bengaliContent[fieldKey] }}</p>
                      </div>
                      
                      <!-- Default Image Preview -->
                      <div v-else class="mb-3">
                        <p class="text-xs text-gray-500 mb-2">Default Image:</p>
                        <img 
                          :src="getDefaultImage(fieldKey, 'bn')" 
                          :alt="fieldLabel"
                          class="w-full h-32 object-cover rounded-lg border border-gray-300 opacity-75"
                        >
                        <p class="text-xs text-gray-500 mt-1">Using default image</p>
                      </div>
                      
                      <!-- Image Upload Controls -->
                      <div class="space-y-2">
                        <input
                          type="file"
                          :ref="el => addFileInputRef(el, fieldKey, 'bn')"
                          accept="image/*"
                          class="hidden"
                          @change="handleImageUpload($event, fieldKey, 'bn')"
                        />
                        
                        <button
                          type="button"
                          @click="triggerFileInput(fieldKey, 'bn')"
                          class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                          <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                          </svg>
                          Upload New Image
                        </button>
                        
                        <button
                          v-if="bengaliContent[fieldKey] && bengaliContent[fieldKey] !== getDefaultImage(fieldKey, 'bn')"
                          type="button"
                          @click="removeImage(fieldKey, 'bn')"
                          class="w-full px-3 py-2 bg-red-50 border border-red-200 rounded-md text-sm font-medium text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        >
                          <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                          Remove Image
                        </button>
                      </div>
                      
                      <!-- Upload Progress -->
                      <div v-if="uploadProgress[fieldKey]?.bn > 0" class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div 
                            class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                            :style="{ width: uploadProgress[fieldKey].bn + '%' }"
                          ></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Uploading: {{ uploadProgress[fieldKey].bn }}%</p>
                      </div>
                    </div>
                  </div>

                  <!-- Image Notes -->
                  <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700">
                    <p><strong>Note:</strong> Recommended image size: 1200x600px. Supported formats: JPG, PNG, WebP. Max file size: 2MB.</p>
                  </div>

                  <!-- Quick Actions for Images -->
                  <div class="flex justify-between items-center text-xs text-gray-500 mt-2">
                    <span>
                      Last saved: {{ getLastSaved(fieldKey) }}
                    </span>
                    <div class="flex space-x-2">
                      <button
                        @click="copyImageToBengali(fieldKey)"
                        class="text-blue-600 hover:text-blue-800"
                        title="Copy English image to Bengali"
                      >
                        Copy EN → BN
                      </button>
                      <button
                        @click="resetImageToDefault(fieldKey)"
                        class="text-gray-600 hover:text-gray-800"
                        title="Reset to default image"
                      >
                        Reset
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Regular text fields -->
                <div v-else>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ fieldLabel }}
                    <span class="text-xs text-gray-500 ml-1">
                      ({{ activeLanguage === 'en' ? 'English' : 'Bengali' }})
                    </span>
                  </label>
                  
                  <!-- Show both languages side by side for better management -->
                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-2">
                    <!-- English Version -->
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                      <label class="block text-xs font-medium text-gray-600 mb-1">
                        English Version
                      </label>
                      <textarea
                        v-model="englishContent[fieldKey]"
                        @blur="saveContent(fieldKey, englishContent[fieldKey], bengaliContent[fieldKey])"
                        :rows="fieldKey.includes('content') ? 4 : 2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        :placeholder="`Enter ${fieldLabel.toLowerCase()} in English...`"
                      ></textarea>
                      <p class="mt-1 text-xs text-gray-500">
                        Characters: {{ englishContent[fieldKey] ? englishContent[fieldKey].length : 0 }}
                      </p>
                    </div>

                    <!-- Bengali Version -->
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50" :class="{ 'border-blue-300 bg-blue-50': activeLanguage === 'bn' }">
                      <label class="block text-xs font-medium text-gray-600 mb-1">
                        Bengali Version
                      </label>
                      <textarea
                        v-model="bengaliContent[fieldKey]"
                        @blur="saveContent(fieldKey, englishContent[fieldKey], bengaliContent[fieldKey])"
                        :rows="fieldKey.includes('content') ? 4 : 2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bengali"
                        :placeholder="`Enter ${fieldLabel.toLowerCase()} in Bengali...`"
                        dir="auto"
                      ></textarea>
                      <p class="mt-1 text-xs text-gray-500">
                        Characters: {{ bengaliContent[fieldKey] ? bengaliContent[fieldKey].length : 0 }}
                      </p>
                    </div>
                  </div>

                  <!-- Quick Actions -->
                  <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>
                      Last saved: {{ getLastSaved(fieldKey) }}
                    </span>
                    <div class="flex space-x-2">
                      <button
                        @click="copyToBengali(fieldKey)"
                        class="text-blue-600 hover:text-blue-800"
                        title="Copy English to Bengali"
                      >
                        Copy EN → BN
                      </button>
                      <button
                        @click="resetToDefault(fieldKey)"
                        class="text-gray-600 hover:text-gray-800"
                        title="Reset to default"
                      >
                        Reset
                      </button>
                    </div>
                  </div>
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
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <p class="text-sm font-medium text-gray-600">English Modified</p>
                <p class="text-lg font-semibold text-gray-800">{{ modifiedEnglishFields }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
              <div class="p-2 bg-orange-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Bengali Modified</p>
                <p class="text-lg font-semibold text-gray-800">{{ modifiedBengaliFields }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
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
const activeLanguage = ref('en') // 'en' or 'bn'
const saving = ref(false)
const englishContent = reactive({})
const bengaliContent = reactive({})
const lastSaved = reactive({})
const successMessage = ref('')
const errorMessage = ref('')
const uploadProgress = reactive({})
const fileInputs = reactive({})

// Use Inertia form for content saving
const saveForm = useForm({
  key: '',
  value: '',
  value_bn: ''
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
const modifiedEnglishFields = computed(() => {
  return Object.keys(englishContent).filter(key => 
    englishContent[key] !== props.content[key]
  ).length
})
const modifiedBengaliFields = computed(() => {
  return Object.keys(bengaliContent).filter(key => 
    bengaliContent[key] && bengaliContent[key] !== getDefaultBengali(key)
  ).length
})
const lastUpdated = computed(() => {
  return new Date().toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
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

const getDefaultBengali = (fieldKey) => {
  // This would typically come from your default content
  const defaultBengali = {
    'about_banner_title': 'স্কিলগ্রো সম্পর্কে',
    'about_banner_description': 'মানসম্মত শিক্ষা এবং উদ্ভাবনী লার্নিং সমাধানের মাধ্যমে বিশ্বব্যাপী শিক্ষার্থীদের ক্ষমতায়ন',
    // Add other default Bengali translations here
  }
  return defaultBengali[fieldKey] || ''
}

const getDefaultImage = (fieldKey, language) => {
  const defaultImages = {
    'home_hero_image': {
      'en': '/assets/img/h2_banner_img.png',
      'bn': '/assets/img/h2_banner_img.png'
    }
    // Add more default images for other image fields if needed
  }
  
  return defaultImages[fieldKey]?.[language] || '/assets/img/placeholder-image.jpg'
}

const getLastSaved = (fieldKey) => {
  return lastSaved[fieldKey] || 'Never'
}

const copyToBengali = (fieldKey) => {
  if (englishContent[fieldKey]) {
    bengaliContent[fieldKey] = englishContent[fieldKey]
    showMessage('Copied English content to Bengali field', 'info')
  }
}

const copyImageToBengali = (fieldKey) => {
  if (englishContent[fieldKey] && englishContent[fieldKey] !== getDefaultImage(fieldKey, 'en')) {
    bengaliContent[fieldKey] = englishContent[fieldKey]
    saveContent(fieldKey, englishContent[fieldKey], bengaliContent[fieldKey])
    showMessage('Copied English image to Bengali', 'info')
  }
}

const resetToDefault = (fieldKey) => {
  englishContent[fieldKey] = props.content[fieldKey] || ''
  bengaliContent[fieldKey] = getDefaultBengali(fieldKey)
  showMessage('Field reset to default values', 'info')
}

const resetImageToDefault = (fieldKey) => {
  englishContent[fieldKey] = getDefaultImage(fieldKey, 'en')
  bengaliContent[fieldKey] = getDefaultImage(fieldKey, 'bn')
  saveContent(fieldKey, englishContent[fieldKey], bengaliContent[fieldKey])
  showMessage('Image reset to default', 'info')
}

// File input management
const addFileInputRef = (el, fieldKey, language) => {
  if (el) {
    const key = `${fieldKey}_${language}`
    fileInputs[key] = el
  }
}

const triggerFileInput = (fieldKey, language) => {
  const key = `${fieldKey}_${language}`
  if (fileInputs[key]) {
    fileInputs[key].click()
  }
}

const handleImageUpload = async (event, fieldKey, language) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file type
  const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
  if (!validTypes.includes(file.type)) {
    showMessage('Please select a valid image file (JPG, PNG, WebP)', 'error')
    return
  }

  // Validate file size (2MB)
  if (file.size > 2 * 1024 * 1024) {
    showMessage('Image size should be less than 2MB', 'error')
    return
  }

  try {
    // Initialize progress tracking
    if (!uploadProgress[fieldKey]) {
      uploadProgress[fieldKey] = { en: 0, bn: 0 }
    }
    uploadProgress[fieldKey][language] = 10 // Start at 10%

    const formData = new FormData()
    formData.append('image', file)
    formData.append('field_key', fieldKey)
    formData.append('language', language)

    const response = await fetch('/admin/content-management/upload-image', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: formData,
    })

    // Simulate progress for better UX
    uploadProgress[fieldKey][language] = 60

    const result = await response.json()

    if (response.ok && result.success) {
      uploadProgress[fieldKey][language] = 100
      
      // Update the content with the new image URL
      if (language === 'en') {
        englishContent[fieldKey] = result.image_url
      } else {
        bengaliContent[fieldKey] = result.image_url
      }
      
      // Save the image URL to content management
      await saveContent(fieldKey, 
        language === 'en' ? result.image_url : englishContent[fieldKey],
        language === 'bn' ? result.image_url : bengaliContent[fieldKey]
      )
      
      showMessage('Image uploaded successfully!', 'success')
    } else {
      throw new Error(result.message || 'Failed to upload image')
    }
  } catch (error) {
    console.error('Image upload error:', error)
    showMessage('Failed to upload image: ' + error.message, 'error')
  } finally {
    // Reset progress after a delay
    setTimeout(() => {
      if (uploadProgress[fieldKey]) {
        uploadProgress[fieldKey][language] = 0
      }
    }, 1000)
    // Reset file input
    event.target.value = ''
  }
}

const removeImage = async (fieldKey, language) => {
  if (!confirm('Are you sure you want to remove this image?')) {
    return
  }

  try {
    const response = await fetch('/admin/content-management/remove-image', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({
        field_key: fieldKey,
        language: language
      })
    })

    const result = await response.json()

    if (response.ok && result.success) {
      // Update the content with default image
      const defaultImage = getDefaultImage(fieldKey, language)
      if (language === 'en') {
        englishContent[fieldKey] = defaultImage
      } else {
        bengaliContent[fieldKey] = defaultImage
      }
      
      // Save the change
      await saveContent(fieldKey, 
        language === 'en' ? defaultImage : englishContent[fieldKey],
        language === 'bn' ? defaultImage : bengaliContent[fieldKey]
      )
      
      showMessage('Image removed successfully!', 'success')
    } else {
      throw new Error(result.message || 'Failed to remove image')
    }
  } catch (error) {
    console.error('Image removal error:', error)
    showMessage('Failed to remove image: ' + error.message, 'error')
  }
}

const saveContent = async (key, value, valueBn = null) => {
  if (!key || value === undefined) {
    showMessage('Invalid content data', 'error')
    return
  }

  try {
    saving.value = true
    console.log('Saving content:', { key, value, valueBn })
    
    // Use Inertia form to post to the web route
    saveForm.key = key
    saveForm.value = value
    saveForm.value_bn = valueBn
    
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
        englishContent[key] = value
        if (valueBn !== null) {
          bengaliContent[key] = valueBn
        }
        
        // Update last saved timestamp
        lastSaved[key] = new Date().toLocaleTimeString()
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
  const changes = []
  
  // Check for English changes
  Object.keys(englishContent).forEach(key => {
    if (englishContent[key] !== props.content[key]) {
      changes.push({ key, value: englishContent[key], value_bn: bengaliContent[key] || '' })
    }
  })
  
  // Check for Bengali changes (even if English hasn't changed)
  Object.keys(bengaliContent).forEach(key => {
    if (bengaliContent[key] && bengaliContent[key] !== getDefaultBengali(key)) {
      // If this key is not already in changes, add it
      if (!changes.find(change => change.key === key)) {
        changes.push({ 
          key, 
          value: englishContent[key] || props.content[key] || '', 
          value_bn: bengaliContent[key] 
        })
      }
    }
  })

  if (changes.length === 0) {
    showMessage('No changes to save', 'info')
    return
  }

  try {
    saving.value = true
    
    // Save each modified field sequentially
    for (const change of changes) {
      await new Promise((resolve) => {
        saveForm.key = change.key
        saveForm.value = change.value
        saveForm.value_bn = change.value_bn
        
        saveForm.post('/admin/content-management/save', {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            console.log(`✅ Saved: ${change.key}`)
            lastSaved[change.key] = new Date().toLocaleTimeString()
            resolve()
          },
          onError: (errors) => {
            console.error(`❌ Failed to save: ${change.key}`, errors)
            resolve() // Continue with next even if one fails
          }
        })
      })
      
      // Small delay between saves to avoid overwhelming the server
      await new Promise(resolve => setTimeout(resolve, 100))
    }

    console.log('✅ All content saved successfully')
    showMessage(`${changes.length} changes saved successfully!`)
    
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
    if (fieldKey.includes('_image')) {
      englishContent[fieldKey] = getDefaultImage(fieldKey, 'en')
      bengaliContent[fieldKey] = getDefaultImage(fieldKey, 'bn')
    } else {
      englishContent[fieldKey] = props.content[fieldKey] || ''
      bengaliContent[fieldKey] = getDefaultBengali(fieldKey)
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
  
  // Initialize content from props
  const sectionFields = props.sections[activeSection.value]?.fields || {}
  Object.keys(sectionFields).forEach(fieldKey => {
    englishContent[fieldKey] = props.content[fieldKey] || ''
    // For Bengali, we'll need to fetch from the server or use defaults
    // For now, we'll initialize with empty values
    bengaliContent[fieldKey] = ''
  })
})
</script>

<style scoped>
/* Bengali font support */


/* Ensure proper text direction for Bengali */
[dir="auto"] {
  text-align: left;
}

/* Image preview styles */
img {
  max-width: 100%;
  height: auto;
}
</style>