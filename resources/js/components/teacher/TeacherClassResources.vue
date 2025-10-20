<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Resources - {{ classData.name }}</h1>
        <p class="text-gray-600">Upload and manage learning resources for your students</p>
      </div>
      <div class="flex space-x-3">
        <button 
          @click="$router.back()"
          class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          <span>Back to Class</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Two Main Tiles for Upload -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Upload PDF Tile -->
        <div 
          @click="showPdfUpload = true"
          class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-all duration-200 hover:border-blue-300"
        >
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Upload PDF</h3>
              <p class="text-sm text-gray-600">Upload documents, notes, and study materials</p>
            </div>
          </div>
        </div>

        <!-- Upload Video Tile -->
        <div 
          @click="showVideoUpload = true"
          class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-all duration-200 hover:border-blue-300"
        >
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Upload Video</h3>
              <p class="text-sm text-gray-600">Upload video lessons or share video links</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Resources List -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">All Resources</h3>
          <div class="text-sm text-gray-500">
            {{ resources.length }} resource{{ resources.length !== 1 ? 's' : '' }}
          </div>
        </div>
        
        <div class="divide-y divide-gray-200">
          <div 
            v-for="resource in resources" 
            :key="resource.id"
            class="p-6 hover:bg-gray-50 transition-colors duration-150"
          >
            <div class="flex justify-between items-start">
              <div class="flex items-start space-x-4 flex-1">
                <div :class="`p-3 rounded-lg ${getResourceTypeColor(resource.type).bg} ${getResourceTypeColor(resource.type).text}`">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="resource.type === 'pdf'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                  </svg>
                </div>
                <div class="flex-1">
                  <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ resource.title }}</h4>
                  <p class="text-gray-600 mb-2">{{ resource.description || 'No description' }}</p>
                  
                  <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span>Uploaded: {{ formatDate(resource.created_at) }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      <span>{{ resource.download_count || 0 }} downloads</span>
                    </div>
                    <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getResourceTypeColor(resource.type).bg} ${getResourceTypeColor(resource.type).text}`">
                      {{ resource.type.toUpperCase() }}
                    </span>
                  </div>
                  
                  <!-- File info -->
                  <div v-if="resource.file_info" class="mt-2 text-sm text-gray-500">
                    <span>{{ resource.file_info.size }}</span>
                    <span v-if="resource.file_info.pages"> • {{ resource.file_info.pages }} pages</span>
                    <span v-if="resource.file_info.duration"> • {{ resource.file_info.duration }}</span>
                  </div>

                  <!-- Video URL -->
                  <div v-if="resource.type === 'video' && resource.content && resource.content.startsWith('http')" class="mt-2">
                    <a :href="resource.content" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                      </svg>
                      <span>View Video Link</span>
                    </a>
                  </div>
                </div>
              </div>
              
              <div class="flex space-x-2">
                <button 
                  @click="downloadResource(resource)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-1 rounded hover:bg-blue-50 transition-colors"
                >
                  Download
                </button>
                <button 
                  @click="shareResource(resource)"
                  class="text-green-600 hover:text-green-800 text-sm font-medium px-3 py-1 rounded hover:bg-green-50 transition-colors"
                >
                  Share
                </button>
                <button 
                  @click="deleteResource(resource.id)"
                  class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1 rounded hover:bg-red-50 transition-colors"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
          
          <div v-if="resources.length === 0" class="p-12 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-lg font-medium mb-2">No resources yet</p>
            <p class="text-sm mb-4">Upload your first resource to get started</p>
            <button 
              @click="showPdfUpload = true"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Upload Your First Resource
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- PDF Upload Modal -->
    <div v-if="showPdfUpload" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">Upload PDF Document</h3>
          <button @click="showPdfUpload = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="uploadPdf" class="p-6 space-y-6">
          <!-- Resource Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Title <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="pdfForm.title"
              type="text" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="Enter resource title"
              :disabled="uploading"
            >
          </div>

          <!-- Resource Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea 
              v-model="pdfForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="Describe this resource..."
              :disabled="uploading"
            ></textarea>
          </div>

          <!-- File Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              PDF File <span class="text-red-500">*</span>
            </label>
            <div 
              :class="`border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer ${
                pdfForm.file 
                  ? 'border-green-300 bg-green-50' 
                  : 'border-gray-300 hover:border-gray-400 bg-gray-50'
              }`"
              @click="$refs.pdfInput?.click()"
            >
              <input 
                type="file"
                @change="handlePdfUpload"
                accept=".pdf"
                class="hidden"
                ref="pdfInput"
                :disabled="uploading"
              >
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              <p class="text-sm text-gray-600 mb-1">
                {{ pdfForm.file ? 'Click to change file' : 'Click to upload PDF file' }}
              </p>
              <p class="text-xs text-gray-500">Maximum file size: 100MB</p>
            </div>
            
            <!-- File preview -->
            <div v-if="pdfForm.file" class="mt-4 p-4 bg-gray-50 rounded-lg border">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <div>
                    <p class="text-sm font-medium text-gray-700">{{ pdfForm.file.name }}</p>
                    <p class="text-xs text-gray-500">{{ formatFileSize(pdfForm.file.size) }}</p>
                  </div>
                </div>
                <button 
                  @click="removePdfFile"
                  type="button"
                  class="text-red-500 hover:text-red-700 p-1 rounded transition-colors"
                  :disabled="uploading"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <button 
              type="button"
              @click="showPdfUpload = false"
              class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50"
              :disabled="uploading"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="!pdfForm.file || !pdfForm.title || uploading"
              :class="`px-6 py-2 bg-blue-600 text-white rounded-lg transition-colors flex items-center space-x-2 ${
                (!pdfForm.file || !pdfForm.title || uploading) 
                  ? 'opacity-50 cursor-not-allowed' 
                  : 'hover:bg-blue-700'
              }`"
            >
              <svg v-if="uploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ uploading ? 'Uploading...' : 'Upload PDF' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Video Upload Modal -->
    <div v-if="showVideoUpload" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">Upload Video</h3>
          <button @click="showVideoUpload = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="uploadVideoFinal" class="p-6 space-y-6">
          <!-- Video Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Title <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="videoForm.title"
              type="text" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="Enter video title"
              :disabled="uploading"
            >
          </div>

          <!-- Video Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea 
              v-model="videoForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="Describe this video..."
              :disabled="uploading"
            ></textarea>
          </div>

          <!-- Upload Type Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Upload Type</label>
            <div class="grid grid-cols-2 gap-4">
              <button 
                type="button"
                @click="videoForm.uploadType = 'file'"
                :class="`p-4 border-2 rounded-lg text-center transition-all duration-200 ${
                  videoForm.uploadType === 'file' 
                    ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-sm' 
                    : 'border-gray-300 hover:border-gray-400 bg-white'
                }`"
                :disabled="uploading"
              >
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span class="text-sm font-medium">Upload File</span>
              </button>
              <button 
                type="button"
                @click="videoForm.uploadType = 'link'"
                :class="`p-4 border-2 rounded-lg text-center transition-all duration-200 ${
                  videoForm.uploadType === 'link' 
                    ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-sm' 
                    : 'border-gray-300 hover:border-gray-400 bg-white'
                }`"
                :disabled="uploading"
              >
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
                <span class="text-sm font-medium">Video Link</span>
              </button>
            </div>
          </div>

          <!-- File Upload or Link Input -->
          <div v-if="videoForm.uploadType === 'file'">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Video File <span class="text-red-500">*</span>
            </label>
            <div 
              :class="`border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer ${
                videoForm.file 
                  ? 'border-green-300 bg-green-50' 
                  : 'border-gray-300 hover:border-gray-400 bg-gray-50'
              }`"
              @click="$refs.videoInput?.click()"
            >
              <input 
                type="file"
                @change="handleVideoUpload"
                accept="video/*"
                class="hidden"
                ref="videoInput"
                :disabled="uploading"
              >
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              <p class="text-sm text-gray-600 mb-1">
                {{ videoForm.file ? 'Click to change file' : 'Click to upload video file' }}
              </p>
              <p class="text-xs text-gray-500">MP4, MOV, AVI up to 100MB</p>
            </div>
            
            <!-- File preview -->
            <div v-if="videoForm.file" class="mt-4 p-4 bg-gray-50 rounded-lg border">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                  </svg>
                  <div>
                    <p class="text-sm font-medium text-gray-700">{{ videoForm.file.name }}</p>
                    <p class="text-xs text-gray-500">{{ formatFileSize(videoForm.file.size) }}</p>
                  </div>
                </div>
                <button 
                  @click="removeVideoFile"
                  type="button"
                  class="text-red-500 hover:text-red-700 p-1 rounded transition-colors"
                  :disabled="uploading"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <div v-else>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Video URL <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="videoForm.url"
              type="url" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="https://www.youtube.com/watch?v=..."
              :disabled="uploading"
            >
            <p class="text-xs text-gray-500 mt-2">Supports YouTube, Vimeo, and other video platforms</p>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <button 
              type="button"
              @click="showVideoUpload = false"
              class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50"
              :disabled="uploading"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="uploading || !isVideoFormValid"
              :class="`px-6 py-2 bg-blue-600 text-white rounded-lg transition-colors flex items-center space-x-2 ${
                (uploading || !isVideoFormValid) 
                  ? 'opacity-50 cursor-not-allowed' 
                  : 'hover:bg-blue-700'
              }`"
            >
              <svg v-if="uploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ uploading ? 'Uploading...' : 'Upload Video' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import apiClient from '../../api/client'

const route = useRoute()
const router = useRouter()

const classData = ref({
  id: route.params.classId,
  name: 'Loading...'
})

const resources = ref([])
const showPdfUpload = ref(false)
const showVideoUpload = ref(false)
const uploading = ref(false)
const loading = ref(true)

const pdfForm = ref({
  title: '',
  description: '',
  file: null
})

const videoForm = ref({
  title: '',
  description: '',
  uploadType: 'file',
  file: null,
  url: ''
})

const pdfInput = ref(null)
const videoInput = ref(null)

// Fetch resources from API
const fetchResources = async () => {
  try {
    loading.value = true
    console.log('📚 Fetching resources for class:', classData.value.id)
    
    const response = await apiClient.get(`/resources/class/${classData.value.id}`)
    
    if (response.data.success) {
      resources.value = response.data.data
      console.log('✅ Resources fetched successfully:', resources.value.length)
    } else {
      console.error('❌ Failed to fetch resources:', response.data.message)
      resources.value = []
    }
  } catch (error) {
    console.error('❌ Error fetching resources:', error)
    resources.value = []
  } finally {
    loading.value = false
  }
}

// Fetch class data
const fetchClassData = async () => {
  try {
    console.log('🏫 Fetching class data for ID:', route.params.classId)
    const response = await apiClient.get(`/courses/${route.params.classId}`)
    
    if (response.data.success) {
      classData.value = {
        id: response.data.data.id,
        name: response.data.data.name || `Class ${response.data.data.grade}` || 'Unknown Class'
      }
      console.log('✅ Class data fetched:', classData.value)
    } else {
      console.error('❌ Failed to fetch class data:', response.data.message)
      classData.value.name = 'Class Not Found'
    }
  } catch (error) {
    console.error('❌ Error fetching class data:', error)
    classData.value.name = 'Error Loading Class'
  }
}

// Upload PDF to API
const uploadPdf = async () => {
  if (!pdfForm.value.file || !pdfForm.value.title) {
    alert('Please provide both title and PDF file')
    return
  }

  uploading.value = true
  try {
    const formData = new FormData()
    formData.append('title', pdfForm.value.title)
    formData.append('description', pdfForm.value.description || '')
    formData.append('type', 'pdf')
    formData.append('file', pdfForm.value.file)
    formData.append('assigned_class', classData.value.id)

    console.log('📤 Uploading PDF:', {
      title: pdfForm.value.title,
      file: pdfForm.value.file.name,
      classId: classData.value.id
    })

    const response = await apiClient.post(`/resources/upload/${getCurrentTeacherId()}`, formData)

    if (response.data.success) {
      console.log('✅ PDF uploaded successfully:', response.data)
      await fetchResources()
      showPdfUpload.value = false
      resetPdfForm()
      
      showNotification('PDF uploaded successfully!', 'success')
    } else {
      console.error('❌ Failed to upload PDF:', response.data)
      alert('Failed to upload PDF: ' + (response.data.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('❌ Error uploading PDF:', error)
    
    let errorMessage = 'Error uploading PDF. Please try again.'
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      errorMessage = Object.values(errors).flat().join(', ')
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }
    
    alert(errorMessage)
  } finally {
    uploading.value = false
  }
}

// Create a mock file for video links
const createMockVideoFile = () => {
  // Create a simple text file that contains the video URL
  const blob = new Blob([`Video URL: ${videoForm.value.url}`], { type: 'text/plain' })
  const file = new File([blob], 'video-link.txt', { 
    type: 'text/plain',
    lastModified: new Date().getTime()
  })
  return file
}

// Upload Video to API - SOLUTION FOR LINK UPLOADS
// Upload Video to API - FIXED VERSION WITH MOCK FILE
const uploadVideo = async () => {
  if (!isVideoFormValid.value) {
    alert('Please fill in all required fields')
    return
  }

  uploading.value = true
  try {
    console.log('🛡️ Getting CSRF token for upload request...')
    await getCsrfToken()
    
    const formData = new FormData()
    formData.append('title', videoForm.value.title.trim())
    formData.append('description', videoForm.value.description?.trim() || '')
    formData.append('type', 'video')
    
    if (classData.value.id && classData.value.id !== 'Loading...') {
      formData.append('assigned_class', classData.value.id)
    }

    if (videoForm.value.uploadType === 'file') {
      // Handle actual file upload
      formData.append('file', videoForm.value.file)
      console.log('📤 Uploading video file:', {
        title: videoForm.value.title,
        file: videoForm.value.file.name,
        classId: classData.value.id
      })
    } else {
      // Handle link upload - create a mock file
      const mockFile = createVideoLinkFile(videoForm.value.url)
      formData.append('file', mockFile)
      
      // Add URL to description so it's visible
      const enhancedDescription = videoForm.value.description 
        ? `${videoForm.value.description}\n\n🔗 Video Link: ${videoForm.value.url}`
        : `🔗 Video Link: ${videoForm.value.url}`
      
      formData.set('description', enhancedDescription)
      
      console.log('🔗 Uploading video link with placeholder file:', {
        title: videoForm.value.title,
        url: videoForm.value.url,
        classId: classData.value.id
      })
    }

    const response = await apiClient.post(`/resources/upload/${getCurrentTeacherId()}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      timeout: 300000,
    })

    if (response.data.success) {
      console.log('✅ Video uploaded successfully:', response.data)
      await fetchResources()
      showVideoUpload.value = false
      resetVideoForm()
      showNotification('Video uploaded successfully!', 'success')
    } else {
      console.error('❌ Failed to upload video:', response.data)
      alert('Failed to upload video: ' + (response.data.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('❌ Error uploading video:', error)
    handleUploadError(error)
  } finally {
    uploading.value = false
  }
}

const createVideoLinkFile = (url) => {
  const content = `This resource contains a video link.
  
Video URL: ${url}

Note: This is a placeholder file. The actual video content is available at the link above.`

  const blob = new Blob([content], { type: 'text/plain' })
  const file = new File([blob], 'video-link.txt', { 
    type: 'text/plain',
    lastModified: new Date().getTime()
  })
  return file
}

// ALTERNATIVE SOLUTION: Try different endpoint for links
const uploadVideoLinkAlternative = async () => {
  if (!videoForm.value.title || !videoForm.value.url) {
    alert('Please provide both title and video URL')
    return
  }

  uploading.value = true
  try {
    await getCsrfToken()

    // Try different endpoints that might accept link uploads without files
    const endpoints = [
      `/resources/video/link`,
      `/resources/link`,
      `/resources/upload/link/${getCurrentTeacherId()}`,
      `/resources/create/link`
    ]

    const payload = {
      title: videoForm.value.title.trim(),
      description: videoForm.value.description?.trim() || '',
      type: 'video',
      url: videoForm.value.url.trim(),
      video_url: videoForm.value.url.trim(),
      content: videoForm.value.url.trim(),
      assigned_class: classData.value.id,
      teacher_id: getCurrentTeacherId()
    }

    let success = false
    let lastError = null

    // Try each endpoint until one works
    for (const endpoint of endpoints) {
      try {
        console.log(`🔄 Trying endpoint: ${endpoint}`)
        const response = await apiClient.post(endpoint, payload, {
          headers: {
            'Content-Type': 'application/json',
          }
        })

        if (response.data.success) {
          console.log(`✅ Video link uploaded successfully via ${endpoint}:`, response.data)
          await fetchResources()
          showVideoUpload.value = false
          resetVideoForm()
          showNotification('Video link uploaded successfully!', 'success')
          success = true
          break
        }
      } catch (error) {
        console.log(`❌ Endpoint ${endpoint} failed:`, error.response?.data || error.message)
        lastError = error
        continue
      }
    }

    if (!success) {
      throw lastError || new Error('All endpoints failed')
    }

  } catch (error) {
    console.error('❌ Error uploading video link:', error)
    
    // Fallback to mock file method if endpoints don't work
    if (error.response?.status === 422 && error.response.data?.errors?.file) {
      alert('The server requires a file upload. Please use the file upload option for video links or contact the administrator.')
    } else {
      handleUploadError(error)
    }
  } finally {
    uploading.value = false
  }
}

// FINAL SOLUTION: Use this as the main upload function
const uploadVideoFinal = async () => {
  if (!isVideoFormValid.value) {
    alert('Please fill in all required fields')
    return
  }

  // For file uploads, use the normal method
  if (videoForm.value.uploadType === 'file') {
    await uploadVideo()
    return
  }

  // For link uploads, first try the alternative endpoint method
  // If that fails, use the mock file method
  const useMockFile = confirm(
    'For video links, the system requires a file upload. ' +
    'We can create a placeholder file with your video link. ' +
    'Click OK to continue with this method, or Cancel to try a different approach.'
  )

  if (useMockFile) {
    await uploadVideo() // This will use the mock file method
  } else {
    await uploadVideoLinkAlternative() // Try alternative endpoints
  }
}

// Get CSRF Token
const getCsrfToken = async () => {
  try {
    console.log('🛡️ Getting CSRF token...')
    const response = await apiClient.get('/sanctum/csrf-cookie')
    console.log('✅ CSRF token obtained successfully')
    return response
  } catch (error) {
    console.error('❌ Failed to get CSRF token:', error)
    throw error
  }
}

// Show notification
const showNotification = (message, type = 'info') => {
  if (typeof ElNotification !== 'undefined') {
    ElNotification({
      title: type.charAt(0).toUpperCase() + type.slice(1),
      message: message,
      type: type,
      duration: 3000
    })
  } else {
    // Fallback to alert if ElNotification is not available
    alert(message)
  }
}

// Handle upload errors
const handleUploadError = (error) => {
  let errorMessage = 'Error uploading video. Please try again.'
  
  if (error.response?.status === 413) {
    errorMessage = 'File too large. Please select a file smaller than 100MB.'
  } else if (error.response?.status === 422) {
    if (error.response.data?.errors) {
      const errors = error.response.data.errors
      errorMessage = 'Validation error: ' + Object.values(errors).flat().join(', ')
    } else if (error.response.data?.message) {
      errorMessage = error.response.data.message
    }
  } else if (error.response?.data?.errors) {
    const errors = error.response.data.errors
    errorMessage = Object.values(errors).flat().join(', ')
  } else if (error.response?.data?.message) {
    errorMessage = error.response.data.message
  } else if (error.code === 'ECONNABORTED') {
    errorMessage = 'Upload timeout. Please try again.'
  } else if (error.message) {
    errorMessage = error.message
  }
  
  console.error('🔍 Detailed upload error:', {
    status: error.response?.status,
    data: error.response?.data,
    message: error.message
  })
  
  alert(errorMessage)
}

// Update the template to use the final solution
// In your template, change @submit.prevent="uploadVideo" to @submit.prevent="uploadVideoFinal"

// Delete resource from API
const deleteResource = async (resourceId) => {
  if (!confirm('Are you sure you want to delete this resource?')) {
    return
  }

  try {
    console.log('🗑️ Deleting resource:', resourceId)
    const response = await apiClient.delete(`/resources/${resourceId}`)
    
    if (response.data.success) {
      console.log('✅ Resource deleted successfully')
      await fetchResources()
      showNotification('Resource deleted successfully!', 'success')
    } else {
      console.error('❌ Failed to delete resource:', response.data)
      alert('Failed to delete resource: ' + response.data.message)
    }
  } catch (error) {
    console.error('❌ Error deleting resource:', error)
    alert('Error deleting resource. Please try again.')
  }
}

// Download resource
const downloadResource = async (resource) => {
  try {
    if (resource.type === 'video' && resource.content && resource.content.startsWith('http')) {
      // Open video links in new tab
      window.open(resource.content, '_blank')
    } else if (resource.file_path) {
      // Create download link for files
      const link = document.createElement('a')
      link.href = `/storage/${resource.file_path}`
      link.download = resource.title
      link.target = '_blank'
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    } else {
      alert('Download not available for this resource')
    }
  } catch (error) {
    console.error('❌ Error downloading resource:', error)
    alert('Error downloading resource')
  }
}

// Share resource
const shareResource = (resource) => {
  if (navigator.share) {
    navigator.share({
      title: resource.title,
      text: resource.description,
      url: window.location.href
    })
  } else {
    // Fallback: copy to clipboard
    const shareText = `${resource.title}\n${resource.description}\n\nShared from Class Resources`
    navigator.clipboard.writeText(shareText).then(() => {
      showNotification('Resource information copied to clipboard!', 'success')
    })
  }
}

// Get current teacher ID
const getCurrentTeacherId = () => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  const teacherId = user.id
  console.log('👨‍🏫 Current teacher ID:', teacherId)
  return teacherId
}

// Computed properties
const isVideoFormValid = computed(() => {
  if (videoForm.value.uploadType === 'file') {
    return videoForm.value.title && videoForm.value.file
  } else {
    return videoForm.value.title && videoForm.value.url
  }
})

// File handling methods
const handlePdfUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.type === 'application/pdf') {
      if (file.size > 100 * 1024 * 1024) { // 100MB
        alert('File size must be less than 100MB')
        return
      }
      pdfForm.value.file = file
      console.log('📄 PDF file selected:', file.name)
    } else {
      alert('Please select a valid PDF file.')
    }
  }
}

const handleVideoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Check file type
    const videoTypes = ['video/mp4', 'video/mkv', 'video/avi', 'video/mov', 'video/wmv', 'video/webm', 'video/quicktime']
    if (!videoTypes.includes(file.type) && !file.name.match(/\.(mp4|mkv|avi|mov|wmv|webm|qt|m4v|mpg|mpeg)$/i)) {
      alert('Please select a valid video file (MP4, MKV, AVI, MOV, WMV, WEBM, MPG, MPEG).')
      event.target.value = ''
      return
    }
    
    // Check file size (100MB limit)
    const maxSize = 100 * 1024 * 1024
    if (file.size > maxSize) {
      alert('File size must be less than 100MB. Your file is ' + formatFileSize(file.size))
      event.target.value = ''
      return
    }
    
    videoForm.value.file = file
    console.log('🎥 Video file selected:', {
      name: file.name,
      size: file.size,
      type: file.type,
      formattedSize: formatFileSize(file.size)
    })
  }
}

const removePdfFile = () => {
  pdfForm.value.file = null
  if (pdfInput.value) pdfInput.value.value = ''
}

const removeVideoFile = () => {
  videoForm.value.file = null
  if (videoInput.value) videoInput.value.value = ''
}

// Reset forms
const resetPdfForm = () => {
  pdfForm.value = { title: '', description: '', file: null }
}

const resetVideoForm = () => {
  videoForm.value = { title: '', description: '', uploadType: 'file', file: null, url: '' }
}

// Helper methods
const getResourceTypeColor = (type) => {
  const colors = {
    pdf: { bg: 'bg-red-100', text: 'text-red-800' },
    video: { bg: 'bg-blue-100', text: 'text-blue-800' },
    document: { bg: 'bg-gray-100', text: 'text-gray-800' }
  }
  return colors[type] || { bg: 'bg-gray-100', text: 'text-gray-800' }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatFileSize = (bytes) => {
  if (!bytes) return 'N/A'
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Initialize
onMounted(async () => {
  console.log('🚀 Initializing TeacherClassResources component')
  await fetchClassData()
  await fetchResources()
})
</script>



<style scoped>
.upload-area {
  transition: all 0.3s ease;
}

.upload-area:hover {
  border-color: #3b82f6;
  background-color: #f8fafc;
}

.file-preview {
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.loading-spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>