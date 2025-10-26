<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Resources - {{ classData.name }}</h1>
        <p class="text-gray-600">Share YouTube video links with your students</p>
      </div>
      <div class="flex space-x-3">
        <button 
          @click="goBack"
          class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          <span>Back to Class</span>
        </button>
        <button 
          @click="showVideoUpload = true"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          <span>Add Video Link</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Resources List -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">Video Resources</h3>
          <div class="text-sm text-gray-500">
            {{ resources.length }} video{{ resources.length !== 1 ? 's' : '' }}
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
                <!-- Video Thumbnail -->
                <div 
                  class="relative group cursor-pointer"
                  @click="playVideo(resource)"
                >
                  <div class="relative">
                    <img 
                      :src="getResourceThumbnail(resource)" 
                      :alt="resource.title"
                      class="w-24 h-16 object-cover rounded-lg shadow-sm"
                      @error="handleImageError"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                      <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                      </svg>
                    </div>
                  </div>
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
                      <span>{{ resource.download_count || 0 }} views</span>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                      VIDEO
                    </span>
                  </div>
                  
                  <!-- YouTube Link -->
                  <div class="mt-2">
                    <div class="flex items-center space-x-2">
                      <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                      </svg>
                      <a 
                        :href="getVideoContent(resource)" 
                        target="_blank" 
                        class="text-blue-600 hover:text-blue-800 text-sm"
                        @click.stop
                      >
                        Watch on YouTube
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="flex space-x-2">
                <!-- Play Button -->
                <button 
                  @click="playVideo(resource)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-1 rounded hover:bg-blue-50 transition-colors flex items-center space-x-1"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                  </svg>
                  <span>Play</span>
                </button>
                
                <button 
                  @click="shareResource(resource)"
                  class="text-green-600 hover:text-green-800 text-sm font-medium px-3 py-1 rounded hover:bg-green-50 transition-colors flex items-center space-x-1"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                  </svg>
                  <span>Share</span>
                </button>
                <button 
                  @click="deleteResource(resource.id)"
                  class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1 rounded hover:bg-red-50 transition-colors flex items-center space-x-1"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  <span>Delete</span>
                </button>
              </div>
            </div>
          </div>
          
          <div v-if="resources.length === 0" class="p-12 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
            </svg>
            <p class="text-lg font-medium mb-2">No video resources yet</p>
            <p class="text-sm mb-4">Share your first YouTube video link to get started</p>
            <button 
              @click="showVideoUpload = true"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Add Your First Video
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Video Player Modal -->
    <div v-if="showVideoPlayer && currentVideo" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
          <h3 class="text-lg font-semibold text-gray-800">{{ currentVideo.title }}</h3>
          <button 
            @click="closeVideoPlayer" 
            class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="bg-black flex items-center justify-center min-h-[400px]">
          <!-- YouTube Video -->
          <div v-if="currentVideo.embedUrl" class="w-full aspect-video">
            <iframe 
              :src="currentVideo.embedUrl"
              class="w-full h-full"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
          </div>
          
          <!-- Direct Video File -->
          <div v-else-if="currentVideo.isDirectVideo" class="w-full aspect-video">
            <video 
              controls
              autoplay
              class="w-full h-full"
            >
              <source :src="currentVideo.videoContent" type="video/mp4">
              <source :src="currentVideo.videoContent" type="video/webm">
              <source :src="currentVideo.videoContent" type="video/ogg">
              Your browser does not support the video tag.
            </video>
          </div>
          
          <!-- Fallback - Show debug info -->
          <div v-else class="w-full aspect-video flex flex-col items-center justify-center text-white p-8 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <p class="text-lg mb-2">Unable to Play Video</p>
            <p class="text-sm text-gray-300 mb-4">The video URL could not be processed.</p>
            
            <!-- Debug Information -->
            <div class="text-xs text-gray-400 mt-4 p-4 bg-gray-800 rounded text-left max-w-md">
              <p><strong>Debug Information:</strong></p>
              <p>Video Content: {{ currentVideo.videoContent }}</p>
              <p>Embed URL: {{ currentVideo.embedUrl }}</p>
              <p>Is Direct Video: {{ currentVideo.isDirectVideo }}</p>
              <p>YouTube Video ID: {{ currentVideo.videoId }}</p>
            </div>
            
            <button 
              v-if="currentVideo.videoContent"
              @click="openInNewTab(currentVideo.videoContent)"
              class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Open Video Link
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Video Upload Modal -->
    <div v-if="showVideoUpload" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">Add YouTube Video</h3>
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

          <!-- YouTube URL Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              YouTube URL <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="videoForm.url"
              type="url" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="https://www.youtube.com/watch?v=..."
              :disabled="uploading"
            >
            <p class="text-xs text-gray-500 mt-2">Paste any YouTube video URL</p>
            
            <!-- YouTube Thumbnail Preview -->
            <div v-if="videoForm.url && isYouTubeUrl(videoForm.url)" class="mt-4 p-4 bg-gray-50 rounded-lg border">
              <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
              <img 
                :src="getYouTubeThumbnail(videoForm.url)" 
                alt="YouTube Thumbnail"
                class="w-48 h-36 object-cover rounded-lg shadow-sm"
                @error="handlePreviewImageError"
              >
              <p class="text-xs text-gray-500 mt-2">YouTube auto-generated thumbnail</p>
            </div>
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
              :disabled="uploading || !videoForm.title || !videoForm.url"
              :class="`px-6 py-2 bg-blue-600 text-white rounded-lg transition-colors flex items-center space-x-2 ${
                (uploading || !videoForm.title || !videoForm.url) 
                  ? 'opacity-50 cursor-not-allowed' 
                  : 'hover:bg-blue-700'
              }`"
            >
              <svg v-if="uploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ uploading ? 'Adding...' : 'Add Video' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage, router, useForm } from '@inertiajs/vue3'

// Use Inertia.js page props
const page = usePage()
const props = defineProps({
  classId: String,
  classData: Object,
  resources: Array,
})

// Use reactive refs for local state
const classData = ref(props.classData || {
  id: props.classId,
  name: 'Loading...'
})

const resources = ref(props.resources || [])
const showVideoUpload = ref(false)
const showVideoPlayer = ref(false)
const currentVideo = ref(null)
const uploading = ref(false)
const loading = ref(!props.resources)

// Use Inertia form for proper CSRF handling
const videoForm = useForm({
  title: '',
  description: '',
  url: '',
  type: 'video',
  assigned_class: props.classId || null
})

// Navigation
const goBack = () => {
  router.get(`/teacher/class/${props.classId}`)
}

const debugResourceData = () => {
  console.log('🔍 [DEBUG] Checking all resources data:')
  
  if (resources.value.length === 0) {
    console.log('❌ No resources found')
    return
  }
  
  resources.value.forEach((resource, index) => {
    console.log(`\n📹 Resource ${index + 1}: "${resource.title}"`)
    console.log('   📋 Raw resource data:', {
      id: resource.id,
      type: resource.type,
      content: resource.content,
      description: resource.description,
      file_path: resource.file_path,
      thumbnail_path: resource.thumbnail_path
    })
    
    const videoContent = getVideoContent(resource)
    console.log('   🎬 Extracted video content:', videoContent)
    console.log('   🔍 Is YouTube URL:', isYouTubeUrl(videoContent))
    console.log('   🆔 YouTube Video ID:', getYouTubeVideoId(videoContent))
    console.log('   🔗 YouTube Embed URL:', getYouTubeEmbedUrl(videoContent))
  })
}

// Get video content from resource - FIXED VERSION
const getVideoContent = (resource) => {
  if (!resource) {
    console.log('❌ Resource is null or undefined')
    return null
  }
  
  console.log('🔍 [getVideoContent] Processing resource:', resource.title)
  console.log('📋 [getVideoContent] Full resource data:', JSON.parse(JSON.stringify(resource)))
  
  // Priority 1: Check content field (this should be the main field for YouTube URLs)
  if (resource.content && typeof resource.content === 'string' && resource.content.trim()) {
    const content = resource.content.trim()
    console.log('✅ [getVideoContent] Found content field:', content)
    
    // Check if it's a YouTube URL
    if (isYouTubeUrl(content)) {
      console.log('✅ [getVideoContent] Content is YouTube URL')
      return content
    }
    
    // Check if it's any HTTP URL
    if (content.startsWith('http')) {
      console.log('✅ [getVideoContent] Content is HTTP URL (not YouTube)')
      return content
    }
    
    // Check if it's a storage path that might contain a YouTube URL
    if (content.includes('youtube') || content.includes('youtu.be')) {
      console.log('✅ [getVideoContent] Content contains YouTube reference')
      const extractedUrl = extractYouTubeUrlFromText(content)
      if (extractedUrl) return extractedUrl
    }
  }
  
  // Priority 2: Check if file_path contains a YouTube URL (sometimes URLs are stored here)
  if (resource.file_path && typeof resource.file_path === 'string' && resource.file_path.trim()) {
    const filePath = resource.file_path.trim()
    console.log('📁 [getVideoContent] Checking file_path:', filePath)
    
    // Check if file_path actually contains a URL
    if (filePath.startsWith('http')) {
      console.log('✅ [getVideoContent] file_path contains HTTP URL')
      if (isYouTubeUrl(filePath)) {
        return filePath
      }
      return filePath
    }
    
    // Check if file_path contains YouTube reference
    if (filePath.includes('youtube') || filePath.includes('youtu.be')) {
      console.log('✅ [getVideoContent] file_path contains YouTube reference')
      const extractedUrl = extractYouTubeUrlFromText(filePath)
      if (extractedUrl) return extractedUrl
    }
  }
  
  // Priority 3: Check description for embedded URLs
  if (resource.description && typeof resource.description === 'string') {
    console.log('📝 [getVideoContent] Checking description for URLs')
    const youtubeUrl = extractYouTubeUrlFromText(resource.description)
    if (youtubeUrl) {
      console.log('✅ [getVideoContent] Found YouTube URL in description:', youtubeUrl)
      return youtubeUrl
    }
  }
  
  // Priority 4: Check other possible URL fields
  const possibleUrlFields = ['url', 'video_url', 'video_link', 'link', 'source']
  for (const field of possibleUrlFields) {
    if (resource[field] && typeof resource[field] === 'string' && resource[field].trim()) {
      const url = resource[field].trim()
      console.log(`🔍 [getVideoContent] Checking field "${field}":`, url)
      
      if (url.startsWith('http')) {
        console.log(`✅ [getVideoContent] Found HTTP URL in "${field}"`)
        return url
      }
    }
  }
  
  console.log('❌ [getVideoContent] No video URL found in resource')
  console.log('📊 [getVideoContent] Resource summary:', {
    hasContent: !!resource.content,
    contentValue: resource.content,
    hasFilePath: !!resource.file_path,
    filePathValue: resource.file_path,
    hasDescription: !!resource.description,
    type: resource.type,
    allFields: Object.keys(resource)
  })
  
  return null
}

// Get resource thumbnail - FIXED VERSION
const getResourceThumbnail = (resource) => {
  console.log('🖼️ Getting thumbnail for resource:', resource)
  
  // If resource has a stored thumbnail_path
  if (resource.thumbnail_path) {
    console.log('📁 Found thumbnail_path:', resource.thumbnail_path)
    
    // Check if it's a YouTube thumbnail reference (starts with youtube_)
    if (resource.thumbnail_path.startsWith('youtube_')) {
      const videoId = resource.thumbnail_path.replace('youtube_', '')
      const youtubeThumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`
      console.log('🎬 YouTube thumbnail URL:', youtubeThumbnail)
      return youtubeThumbnail
    }
    
    // Check if it's a custom uploaded thumbnail
    if (resource.thumbnail_path.startsWith('thumbnails/')) {
      const customThumbnail = `/storage/${resource.thumbnail_path}`
      console.log('🖼️ Custom thumbnail URL:', customThumbnail)
      return customThumbnail
    }
  }
  
  // Fallback: Try to get thumbnail from YouTube URL
  const videoContent = getVideoContent(resource)
  if (videoContent && isYouTubeUrl(videoContent)) {
    const youtubeThumbnail = getYouTubeThumbnail(videoContent)
    console.log('🔍 Fallback YouTube thumbnail:', youtubeThumbnail)
    return youtubeThumbnail
  }
  
  // Final fallback: Default thumbnail
  console.log('⚡ Using default thumbnail')
  return '/images/default-video-thumbnail.jpg'
}

// Handle image loading errors
const handleImageError = (event) => {
  console.log('❌ Image failed to load, using default')
  event.target.src = '/images/default-video-thumbnail.jpg'
}

const handlePreviewImageError = (event) => {
  console.log('❌ Preview image failed to load')
  event.target.src = '/images/default-video-thumbnail.jpg'
}

// YouTube URL detection and helper functions
const isYouTubeUrl = (url) => {
  if (!url || typeof url !== 'string') {
    console.log('❌ Invalid URL for YouTube detection:', url)
    return false
  }
  
  const cleanUrl = url.trim()
  console.log('🔍 Checking if URL is YouTube:', cleanUrl)
  
  const youtubePatterns = [
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/,
    /youtu\.be\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/embed\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/v\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]+)/,
    /youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/live\/([a-zA-Z0-9_-]+)/
  ]
  
  const isYouTube = youtubePatterns.some(pattern => pattern.test(cleanUrl))
  console.log('🔍 YouTube detection result:', isYouTube)
  return isYouTube
}

const getYouTubeVideoId = (url) => {
  if (!url || typeof url !== 'string') return null
  
  const cleanUrl = url.trim()
  console.log('🔍 Extracting video ID from:', cleanUrl)
  
  // Different YouTube URL patterns with more specific matching
  const patterns = [
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/,
    /youtu\.be\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/v\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/live\/([a-zA-Z0-9_-]{11})/
  ]
  
  for (const pattern of patterns) {
    const match = cleanUrl.match(pattern)
    if (match && match[1]) {
      console.log('✅ Found YouTube video ID:', match[1])
      return match[1]
    }
  }
  
  console.log('❌ No YouTube video ID found in URL:', cleanUrl)
  return null
}

const getYouTubeThumbnail = (url) => {
  const videoId = getYouTubeVideoId(url)
  if (!videoId) {
    console.log('❌ Cannot get thumbnail - no video ID found')
    return '/images/default-video-thumbnail.jpg'
  }
  
  console.log('✅ Generating thumbnail for video ID:', videoId)
  // Return high quality thumbnail
  return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`
}

const getYouTubeEmbedUrl = (url) => {
  const videoId = getYouTubeVideoId(url)
  if (!videoId) return null
  
  return `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1`
}

// EMERGENCY FIX: More aggressive YouTube URL extraction
const extractYouTubeUrlFromText = (text) => {
  if (!text) return null
  
  console.log('🔍 [extractYouTubeUrlFromText] Searching in text:', text)
  
  // More comprehensive patterns
  const youtubePatterns = [
    /(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/,
    /(https?:\/\/)?(www\.)?youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
    /(https?:\/\/)?(www\.)?youtube\.com\/v\/([a-zA-Z0-9_-]{11})/,
    /(https?:\/\/)?(www\.)?youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/,
    /youtu\.be\/([a-zA-Z0-9_-]{11})/,
    // More flexible patterns
    /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/,
    /(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/
  ]
  
  for (const pattern of youtubePatterns) {
    const match = text.match(pattern)
    if (match) {
      let url = match[0]
      // Ensure it has http protocol
      if (!url.startsWith('http')) {
        url = 'https://' + url
      }
      console.log('✅ [extractYouTubeUrlFromText] Found URL:', url)
      return url
    }
  }
  
  // Also try a simple string search
  if (text.includes('youtube.com/watch?v=') || text.includes('youtu.be/')) {
    const urlMatch = text.match(/(https?:\/\/[^\s]+)/)
    if (urlMatch) {
      console.log('✅ [extractYouTubeUrlFromText] Found via string search:', urlMatch[0])
      return urlMatch[0]
    }
  }
  
  console.log('❌ [extractYouTubeUrlFromText] No YouTube URL found')
  return null
}

// Video player functions - IMPROVED VERSION
const playVideo = (resource) => {
  console.log('🎬 [playVideo] Attempting to play video:', resource.title)
  console.log('🎬 [playVideo] Full resource data:', JSON.parse(JSON.stringify(resource)))
  
  if (resource.type === 'video') {
    // Get the actual video content
    const videoContent = getVideoContent(resource)
    console.log('🎬 [playVideo] Video content found:', videoContent)
    
    if (videoContent) {
      console.log('🎬 [playVideo] Checking if YouTube URL...')
      const isYT = isYouTubeUrl(videoContent)
      console.log('🎬 [playVideo] Is YouTube URL:', isYT)
      
      if (isYT) {
        const videoId = getYouTubeVideoId(videoContent)
        console.log('🎬 [playVideo] YouTube Video ID:', videoId)
        
        if (videoId) {
          console.log('✅ [playVideo] Valid YouTube video detected')
          
          // Create enhanced resource object with the video content
          currentVideo.value = {
            ...resource,
            videoContent: videoContent,
            actualContent: videoContent,
            videoId: videoId,
            embedUrl: getYouTubeEmbedUrl(videoContent)
          }
          
          showVideoPlayer.value = true
          console.log('✅ [playVideo] Video player opened successfully')
          return
        } else {
          console.error('❌ [playVideo] Could not extract YouTube video ID')
          alert('Unable to play video: Could not extract YouTube video ID from URL')
          return
        }
      } else {
        console.log('⚠️ [playVideo] Not a YouTube URL, but has video content:', videoContent)
        
        // For non-YouTube URLs, still try to play if it's a direct video URL
        if (videoContent.match(/\.(mp4|webm|ogg|mov|avi|wmv)$/i)) {
          console.log('✅ [playVideo] Direct video file detected')
          currentVideo.value = {
            ...resource,
            videoContent: videoContent,
            actualContent: videoContent,
            isDirectVideo: true
          }
          showVideoPlayer.value = true
          return
        } else {
          console.error('❌ [playVideo] Not a valid video URL format')
          alert('This is not a valid video URL. Please use YouTube URLs or direct video file links.')
          return
        }
      }
    } else {
      console.error('❌ [playVideo] No video content found')
      
      // Show detailed debug info
      const debugInfo = `
Unable to find video content in this resource.

Resource Details:
- Title: ${resource.title}
- Type: ${resource.type}
- Content Field: ${resource.content || 'Empty'}
- File Path: ${resource.file_path || 'Empty'}
- Description: ${resource.description ? resource.description.substring(0, 100) + '...' : 'Empty'}

Please check if the resource was properly uploaded with a YouTube URL.
      `
      alert(debugInfo)
    }
  } else {
    console.error('❌ [playVideo] Resource is not a video type')
    alert('This resource is not a video')
  }
}

const closeVideoPlayer = () => {
  showVideoPlayer.value = false
  currentVideo.value = null
}

// Fetch resources
const fetchResources = async () => {
  if (props.resources) {
    resources.value = props.resources
    loading.value = false
    return
  }

  try {
    loading.value = true
    const response = await fetch(`/api/resources/class/${classData.value.id}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'include'
    })
    
    if (response.ok) {
      const data = await response.json()
      if (data.success) {
        resources.value = data.data
      } else {
        resources.value = []
      }
    } else {
      resources.value = []
    }
  } catch (error) {
    resources.value = []
  } finally {
    loading.value = false
  }
}

// UPDATED: Upload video link using Inertia form (proper CSRF handling)
const uploadVideoFinal = async () => {
  if (!videoForm.title || !videoForm.url) {
    alert('Please provide both title and YouTube URL')
    return
  }

  uploading.value = true

  try {
    const formData = new FormData()
    formData.append('title', videoForm.title)
    formData.append('description', videoForm.description || '')
    formData.append('type', 'video')
    formData.append('content', videoForm.url)
    
    if (props.classId) {
      formData.append('assigned_class', props.classId)
    }

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

    const response = await fetch(`/api/teachers/${getCurrentTeacherId()}/resources`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: formData,
      credentials: 'include'
    })

    const data = await response.json()

    if (data.success) {
      showVideoUpload.value = false
      resetVideoForm()
      showNotification('Video added successfully!', 'success')
      
      // Refresh the resources list
      if (!props.resources) {
        await fetchResources()
      } else {
        // Reload the page to get updated resources from server
        router.reload()
      }
    } else {
      // Handle validation errors
      let errorMessage = 'Failed to add video: '
      if (data.errors) {
        // Extract all error messages
        const errorMessages = Object.values(data.errors).flat()
        errorMessage += errorMessages.join(', ')
      } else if (data.message) {
        errorMessage += data.message
      } else {
        errorMessage += 'Unknown error occurred'
      }
      alert(errorMessage)
    }
  } catch (error) {
    console.error('Upload error:', error)
    alert('Network error. Please check your connection and try again.')
  } finally {
    uploading.value = false
  }
}

// Share resource
const shareResource = async (resource) => {
  const shareData = {
    title: resource.title,
    text: resource.description,
    url: getVideoContent(resource) || window.location.href
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
    const shareText = `${resource.title}\n${resource.description}\n\nWatch: ${getVideoContent(resource)}`
    try {
      await navigator.clipboard.writeText(shareText)
      showNotification('Video link copied to clipboard!', 'success')
    } catch (error) {
      alert('Share feature not supported. Please copy the URL manually.')
    }
  }
}

// Delete resource with debug logging
const deleteResource = async (resourceId) => {
  console.log('🗑️ [FRONTEND] Attempting to delete resource ID:', resourceId)
  
  if (!confirm('Are you sure you want to delete this video?')) {
    return
  }

  try {
    console.log('📡 [FRONTEND] Sending DELETE request to API...')
    
    const response = await fetch(`/api/resources/${resourceId}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      credentials: 'include'
    })

    const data = await response.json()
    console.log('📥 [FRONTEND] Delete response:', data)

    if (data.success) {
      console.log('✅ [FRONTEND] Delete successful, refreshing resources...')
      await fetchResources()
      showNotification('Video deleted successfully!', 'success')
    } else {
      console.error('❌ [FRONTEND] Delete failed:', data.message)
      alert('Failed to delete video: ' + (data.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('❌ [FRONTEND] Delete error:', error)
    alert('Error deleting video. Please try again.')
  }
}

// Get current teacher ID
const getCurrentTeacherId = () => {
  const user = page.props.auth.user
  return user?.id
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
    alert(message)
  }
}

// Reset form
const resetVideoForm = () => {
  videoForm.reset()
}

// Helper methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Initialize
onMounted(async () => {
  if (!props.resources) {
    await fetchResources()
  }
  
  // Debug the resources data after loading
  setTimeout(() => {
    debugResourceData()
  }, 1000)
})
</script>