<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <TeacherSidebar 
      @showUploadModal="showVideoUpload = true"
      @createAssignment="createAssignment"
      @goBackToAdmin="goBackToAdmin"
    />

    <!-- Main Content -->
    <div class="flex-1 ml-64 min-w-0">
      <!-- Top Navbar -->
      <nav class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center min-w-0">
              <h1 class="custom-heading truncate">Resources - {{ classData.name || 'Loading...' }}</h1>
            </div>
            
            <div class="flex items-center space-x-4 flex-shrink-0">
              <!-- Search -->
              <div class="relative hidden md:block">
                <input 
                  type="text" 
                  placeholder="Search..." 
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64"
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
                  <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                  <a href="#" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center" @click="editProfile">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                  </a>
                  <a href="#" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center" @click="navigateToSettings">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                  </a>
                  <div class="border-t border-gray-200 my-1"></div>
                  <button 
                    @click="logout"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center"
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

      <!-- Page Content -->
      <div class="p-6 max-w-full overflow-x-hidden">
        <!-- Resources Content -->
        <div class="space-y-6">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Resources - {{ classData.name }}</h1>
              <p class="text-gray-600">Share video links with your students</p>
            </div>
            <div class="flex space-x-3">
              <button 
                @click="goBack"
                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2 no-underline"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Class</span>
              </button>
              <button 
                @click="showVideoUpload = true"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2 no-underline"
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
                        
                        <!-- Debug Info (only in development) -->
                        <div v-if="isDevelopment" class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs">
                          <p class="font-semibold">Debug Info:</p>
                          <p>Content: {{ resource.content }}</p>
                          <p>File Path: {{ resource.file_path }}</p>
                          <p>Type: {{ resource.type }}</p>
                          <p>Video Found: {{ !!getVideoContent(resource) }}</p>
                        </div>
                        
                        <!-- Video Link -->
                        <div class="mt-2">
                          <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                              <path d="M8 5v14l11-7z"/>
                            </svg>
                            <a 
                              href="#" 
                              @click.prevent="playVideo(resource)"
                              class="text-blue-600 hover:text-blue-800 text-sm no-underline"
                            >
                              Watch Video
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="flex space-x-2">
                      <!-- Play Button -->
                      <button 
                        @click="playVideo(resource)"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-1 rounded hover:bg-blue-50 transition-colors flex items-center space-x-1 no-underline"
                      >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M8 5v14l11-7z"/>
                        </svg>
                        <span>Play</span>
                      </button>
                      
                      <button 
                        @click="shareResource(resource)"
                        class="text-green-600 hover:text-green-800 text-sm font-medium px-3 py-1 rounded hover:bg-green-50 transition-colors flex items-center space-x-1 no-underline"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                        <span>Share</span>
                      </button>
                      <button 
                        @click="deleteResource(resource.id)"
                        class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1 rounded hover:bg-red-50 transition-colors flex items-center space-x-1 no-underline"
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
                  <p class="text-sm mb-4">Share your first video link to get started</p>
                  <button 
                    @click="showVideoUpload = true"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors no-underline"
                  >
                    Add Your First Video
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Fixed Video Player Modal with Visible Header and Footer -->
    <div v-if="showVideoPlayer && currentVideo" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-4xl flex flex-col shadow-2xl border border-gray-300 max-h-[90vh]">
        <!-- Header - White background, black text -->
        <div class="px-3 py-2 bg-white flex justify-between items-center border-b border-gray-300 flex-shrink-0 z-10">
          <h3 class="text-lg font-semibold text-black truncate flex-1 mr-4">{{ currentVideo.title }}</h3>
          <button 
            @click="closeVideoPlayer" 
            class="text-gray-500 hover:text-gray-700 transition-colors flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 flex-shrink-0 no-underline"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        
        <!-- Video Content Area - Properly contained -->
        <div class="flex-1 min-h-0 bg-gray-50 flex items-center justify-center p-4">
          <div class="w-full max-w-3xl border-2 border-gray-300 rounded-lg overflow-hidden bg-black aspect-video relative">
            <!-- Pure Video Player for direct streams -->
            <div v-if="currentVideo.directVideoUrl" class="w-full h-full">
              <video 
                ref="videoPlayer"
                controls
                autoplay
                class="w-full h-full"
                :poster="currentVideo.thumbnail"
                @play="isPlaying = true"
                @pause="isPlaying = false"
              >
                <source :src="currentVideo.directVideoUrl" type="video/mp4">
                Your browser does not support the video tag.
              </video>
              
              <!-- Play Button Overlay -->
              <div 
                v-if="!isPlaying" 
                class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 cursor-pointer"
                @click="playPauseVideo"
              >
                <div class="w-20 h-20 bg-white bg-opacity-95 rounded-full flex items-center justify-center shadow-2xl hover:bg-white transition-all hover:scale-110 border-2 border-gray-200">
                  <svg class="w-10 h-10 text-gray-800 ml-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                  </svg>
                </div>
              </div>
            </div>
            
            <!-- YouTube embed with proper scaling -->
            <div v-else-if="currentVideo.ultraCleanUrl" class="w-full h-full relative">
              <!-- Scale down the iframe to ensure it doesn't overflow -->
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-[95%] h-[95%]">
                  <iframe 
                    :src="currentVideo.ultraCleanUrl"
                    class="w-full h-full"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                  ></iframe>
                </div>
              </div>
              
              <!-- Ensure borders are always visible -->
              <div class="absolute inset-0 border-2 border-gray-300 pointer-events-none rounded-lg"></div>
            </div>
            
            <!-- Error state -->
            <div v-else class="w-full h-full flex items-center justify-center text-white bg-gray-800">
              <div class="text-center p-6">
                <svg class="w-16 h-16 mx-auto mb-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-lg font-medium mb-2">Unable to load video</p>
                <p class="text-sm text-gray-300 mb-6">The video could not be loaded.</p>
                <div class="flex space-x-3 justify-center">
                  <button 
                    @click="closeVideoPlayer"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium border border-blue-700 no-underline"
                  >
                    Close
                  </button>
                  <button 
                    v-if="currentVideo.originalUrl"
                    @click="openInNewTab(currentVideo.originalUrl)"
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium border border-gray-700 no-underline"
                  >
                    Open Original
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer Area - Always visible -->
        <div class="bg-white px-3 py-2 border-t border-gray-300 flex-shrink-0 z-10">
          <div class="flex justify-between items-center text-sm text-gray-600">
            <span class="font-medium">Video Player</span>
            <div class="flex space-x-4">
              <button 
                v-if="currentVideo.directVideoUrl"
                @click="playPauseVideo"
                class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 font-medium no-underline"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path v-if="isPlaying" d="M6 4h4v16H6zM14 4h4v16h-4z"/>
                  <path v-else d="M8 5v14l11-7z"/>
                </svg>
                <span>{{ isPlaying ? 'Pause' : 'Play' }}</span>
              </button>
              <button 
                @click="closeVideoPlayer"
                class="text-gray-600 hover:text-gray-800 font-medium border border-gray-300 px-3 py-1 rounded hover:bg-gray-50 no-underline"
              >
                Close Player
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Video Upload Modal -->
    <div v-if="showVideoUpload" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">Add Video</h3>
          <button @click="showVideoUpload = false" class="text-gray-400 hover:text-gray-600 no-underline">
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
            <p class="text-xs text-gray-500 mt-2">Paste any video URL</p>
            
            <!-- Video Thumbnail Preview -->
            <div v-if="videoForm.url && isYouTubeUrl(videoForm.url)" class="mt-4 p-4 bg-gray-50 rounded-lg border">
              <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
              <img 
                :src="getYouTubeThumbnail(videoForm.url)" 
                alt="Video Thumbnail"
                class="w-48 h-36 object-cover rounded-lg shadow-sm"
                @error="handlePreviewImageError"
              >
              <p class="text-xs text-gray-500 mt-2">Auto-generated thumbnail</p>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <button 
              type="button"
              @click="showVideoUpload = false"
              class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 no-underline"
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
              } no-underline`"
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
import { ref, onMounted, computed, onUnmounted } from 'vue'
import { usePage, router, useForm } from '@inertiajs/vue3'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'

// Use Inertia.js page props
const page = usePage()
const props = defineProps({
  classId: String,
  classData: Object,
  resources: Array,
  teacher: { // ADD THIS PROP
    type: Object,
    default: () => ({
      name: 'Teacher',
      id: null,
      email: '',
      profile_picture_url: null,
      profile_picture: null
    })
  }
})

// ==================== TEACHER DATA ====================
// Use the teacher prop directly
const teacher = computed(() => {
  // Ensure profile_picture_url is set if profile_picture exists
  if (props.teacher?.profile_picture && !props.teacher.profile_picture_url) {
    props.teacher.profile_picture_url = `/storage/${props.teacher.profile_picture}`
  }
  return props.teacher || {}
})

// User initials for profile picture fallback
const userInitials = computed(() => {
  if (!props.teacher?.name) return 'T'
  return props.teacher.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

// ==================== RESOURCES STATE ====================
const showVideoUpload = ref(false)
const showVideoPlayer = ref(false)
const currentVideo = ref(null)
const uploading = ref(false)
const videoPlayer = ref(null)
const loading = ref(!props.resources)
const userMenuOpen = ref(false)
const isPlaying = ref(false)

// Check if we're in development mode
const isDevelopment = computed(() => {
  return window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
})

// Use reactive refs for local state
const classData = ref(props.classData || {
  id: props.classId,
  name: 'Loading...'
})

const resources = ref(props.resources || [])

// Use Inertia form for proper CSRF handling
const videoForm = useForm({
  title: '',
  description: '',
  url: '',
  type: 'video',
  assigned_class: props.classId || null
})

// ==================== NAVIGATION METHODS ====================
const goBack = () => {
  router.get(`/teacher/class/${props.classId}`)
}

const goBackToAdmin = () => {
  router.visit('/admin/users/other-users')
}

const createAssignment = () => {
  if (props.classId) {
    router.visit(`/teacher/class/${props.classId}/assignments/create`)
  } else {
    router.visit('/teacher/assignments/create')
  }
}

const navigateToSettings = () => {
  router.visit('/teacher/settings')
}

const editProfile = () => {
  router.visit('/teacher/profile/edit')
}

const logout = async () => {
  try {
    router.post('/logout')
  } catch (error) {
    console.error('Logout error:', error)
  }
}

// ==================== UI METHODS ====================
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// ==================== VIDEO RESOURCE METHODS ====================
// (Keep all your existing video resource methods exactly as they were)
// getVideoContent, playVideo, uploadVideoFinal, getResourceThumbnail, etc.

// Debug function to check resource structure
const debugResources = () => {
  console.log('🔍 [DEBUG] Resources Analysis:');
  
  if (resources.value.length === 0) {
    console.log('❌ No resources found');
    return;
  }
  
  resources.value.forEach((resource, index) => {
    console.log(`\n📹 Resource ${index + 1}: "${resource.title}"`);
    console.log('📋 Full resource data:', JSON.parse(JSON.stringify(resource)));
    
    // Check all possible URL fields
    const urlFields = ['content', 'file_path', 'url', 'video_url', 'link', 'source'];
    urlFields.forEach(field => {
      if (resource[field]) {
        console.log(`   📍 ${field}:`, resource[field]);
      }
    });
    
    // Try to extract video content
    const videoContent = getVideoContent(resource);
    console.log('   🎬 Extracted video content:', videoContent);
    
    if (videoContent) {
      console.log('   🔍 Is YouTube URL:', isYouTubeUrl(videoContent));
      console.log('   🆔 YouTube Video ID:', getYouTubeVideoId(videoContent));
    } else {
      console.log('   ❌ NO VIDEO CONTENT FOUND');
    }
  });
};

// Enhanced getVideoContent function
const getVideoContent = (resource) => {
  if (!resource) return null;
  
  console.log('🔍 [getVideoContent] Processing:', resource.title);
  
  // Check all possible fields that might contain the URL
  const possibleFields = [
    'content',           // Primary field
    'file_path',         // Alternative field
    'url',               // Direct URL field
    'video_url',         // Video-specific field
    'link',              // Link field
    'source',            // Source field
    'video_content',     // Video content field
    'youtube_url',       // YouTube-specific field
    'resource_url'       // Resource URL field
  ];
  
  for (const field of possibleFields) {
    if (resource[field] && typeof resource[field] === 'string') {
      const value = resource[field].trim();
      console.log(`   📍 Checking field "${field}":`, value);
      
      if (value.startsWith('http')) {
        console.log(`   ✅ Found HTTP URL in "${field}":`, value);
        return value;
      }
      
      // Check if it contains YouTube pattern but missing protocol
      if ((value.includes('youtube.com') || value.includes('youtu.be')) && !value.startsWith('http')) {
        const fullUrl = 'https://' + value.replace(/^\/\//, '');
        console.log(`   ✅ Reconstructed YouTube URL from "${field}":`, fullUrl);
        return fullUrl;
      }
    }
  }
  
  // Check if description contains YouTube URL
  if (resource.description && typeof resource.description === 'string') {
    console.log('   📝 Checking description for URLs...');
    const youtubeUrl = extractYouTubeUrlFromText(resource.description);
    if (youtubeUrl) {
      console.log('   ✅ Found YouTube URL in description:', youtubeUrl);
      return youtubeUrl;
    }
  }
  
  console.log('   ❌ No video URL found in any field');
  return null;
};

// Enhanced YouTube URL extraction
const extractYouTubeUrlFromText = (text) => {
  if (!text) return null;
  
  console.log('🔍 [extractYouTubeUrlFromText] Searching in text:', text);
  
  // More comprehensive YouTube URL patterns
  const youtubePatterns = [
    /(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/,
    /(https?:\/\/)?(www\.)?youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
    /(https?:\/\/)?(www\.)?youtube\.com\/v\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/,
    /youtu\.be\/([a-zA-Z0-9_-]{11})/,
    /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/
  ];
  
  for (const pattern of youtubePatterns) {
    const match = text.match(pattern);
    if (match) {
      let url = match[0];
      // Ensure it has http protocol
      if (!url.startsWith('http')) {
        url = 'https://' + url;
      }
      console.log('✅ [extractYouTubeUrlFromText] Found URL:', url);
      return url;
    }
  }
  
  // Also try simple URL extraction
  const urlMatch = text.match(/(https?:\/\/[^\s]+)/);
  if (urlMatch && (urlMatch[0].includes('youtube.com') || urlMatch[0].includes('youtu.be'))) {
    console.log('✅ [extractYouTubeUrlFromText] Found via simple URL match:', urlMatch[0]);
    return urlMatch[0];
  }
  
  console.log('❌ [extractYouTubeUrlFromText] No YouTube URL found');
  return null;
};

// Get resource thumbnail
const getResourceThumbnail = (resource) => {
  console.log('🖼️ Getting thumbnail for resource:', resource);
  
  // If resource has a stored thumbnail_path
  if (resource.thumbnail_path) {
    console.log('📁 Found thumbnail_path:', resource.thumbnail_path);
    
    // Check if it's a YouTube thumbnail reference
    if (resource.thumbnail_path.startsWith('youtube_')) {
      const videoId = resource.thumbnail_path.replace('youtube_', '');
      const youtubeThumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
      console.log('🎬 YouTube thumbnail URL:', youtubeThumbnail);
      return youtubeThumbnail;
    }
    
    // Check if it's a custom uploaded thumbnail
    if (resource.thumbnail_path.startsWith('thumbnails/')) {
      const customThumbnail = `/storage/${resource.thumbnail_path}`;
      console.log('🖼️ Custom thumbnail URL:', customThumbnail);
      return customThumbnail;
    }
  }
  
  // Fallback: Try to get thumbnail from YouTube URL
  const videoContent = getVideoContent(resource);
  if (videoContent && isYouTubeUrl(videoContent)) {
    const youtubeThumbnail = getYouTubeThumbnail(videoContent);
    console.log('🔍 Fallback YouTube thumbnail:', youtubeThumbnail);
    return youtubeThumbnail;
  }
  
  // Final fallback: Default thumbnail
  console.log('⚡ Using default thumbnail');
  return '/images/default-video-thumbnail.jpg';
};

// Handle image loading errors
const handleImageError = (event) => {
  console.log('❌ Image failed to load, using default');
  event.target.src = '/images/default-video-thumbnail.jpg';
};

const handlePreviewImageError = (event) => {
  console.log('❌ Preview image failed to load');
  event.target.src = '/images/default-video-thumbnail.jpg';
};

// YouTube URL detection and helper functions
const isYouTubeUrl = (url) => {
  if (!url || typeof url !== 'string') {
    console.log('❌ Invalid URL for YouTube detection:', url);
    return false;
  }
  
  const cleanUrl = url.trim();
  console.log('🔍 Checking if URL is YouTube:', cleanUrl);
  
  const youtubePatterns = [
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/,
    /youtu\.be\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/embed\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/v\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]+)/,
    /youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/live\/([a-zA-Z0-9_-]+)/
  ];
  
  const isYouTube = youtubePatterns.some(pattern => pattern.test(cleanUrl));
  console.log('🔍 YouTube detection result:', isYouTube);
  return isYouTube;
};

const getYouTubeVideoId = (url) => {
  if (!url || typeof url !== 'string') return null;
  
  const cleanUrl = url.trim();
  console.log('🔍 Extracting video ID from:', cleanUrl);
  
  const patterns = [
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/,
    /youtu\.be\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/v\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/live\/([a-zA-Z0-9_-]{11})/
  ];
  
  for (const pattern of patterns) {
    const match = cleanUrl.match(pattern);
    if (match && match[1]) {
      console.log('✅ Found YouTube video ID:', match[1]);
      return match[1];
    }
  }
  
  console.log('❌ No YouTube video ID found in URL:', cleanUrl);
  return null;
};

const getYouTubeThumbnail = (url) => {
  const videoId = getYouTubeVideoId(url);
  if (!videoId) {
    console.log('❌ Cannot get thumbnail - no video ID found');
    return '/images/default-video-thumbnail.jpg';
  }
  
  console.log('✅ Generating thumbnail for video ID:', videoId);
  return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
};

// Enhanced playVideo function with better error handling
const playVideo = async (resource) => {
  console.log('🎬 [playVideo] Attempting to play video:', resource.title);
  
  const videoContent = getVideoContent(resource);
  
  if (!videoContent) {
    alert(`No video content found for: ${resource.title}`);
    return;
  }
  
  if (isYouTubeUrl(videoContent)) {
    const videoId = getYouTubeVideoId(videoContent);
    
    if (videoId) {
      console.log('✅ Playing YouTube video with ID:', videoId);
      
      // Generate ultra-clean embed URL
      currentVideo.value = {
        ...resource,
        ultraCleanUrl: generateUltraCleanEmbedUrl(videoId),
        videoId: videoId,
        originalUrl: videoContent,
        isEmbed: true
      };
      
      showVideoPlayer.value = true;
      
    } else {
      alert('Could not extract YouTube video ID from the URL.');
    }
  } else {
    // Handle non-YouTube URLs (direct video files)
    console.log('🎯 Using direct video file');
    currentVideo.value = {
      ...resource,
      directVideoUrl: videoContent,
      isDirectVideo: true
    };
    showVideoPlayer.value = true;
  }
};

// Generate ultra-clean YouTube embed as fallback
const generateUltraCleanEmbedUrl = (videoId) => {
  // Maximum parameters to hide everything
  const params = new URLSearchParams({
    'autoplay': '1',
    'rel': '0',
    'modestbranding': '1',
    'controls': '0', // No controls
    'showinfo': '0',
    'iv_load_policy': '3',
    'disablekb': '1',
    'playsinline': '1',
    'enablejsapi': '1',
    'origin': window.location.origin,
    'widget_referrer': window.location.origin,
    'cc_lang_pref': 'en',
    'version': '3',
    'loop': '0',
    'playlist': videoId,
    'mute': '0',
    'start': '0',
    'end': '0'
  });
  
  return `https://www.youtube-nocookie.com/embed/${videoId}?${params.toString()}`;
};

// Play/pause video
const playPauseVideo = () => {
  if (videoPlayer.value) {
    if (videoPlayer.value.paused) {
      videoPlayer.value.play();
      isPlaying.value = true;
    } else {
      videoPlayer.value.pause();
      isPlaying.value = false;
    }
  }
};

// Helper function to open URLs in new tab
const openInNewTab = (url) => {
  window.open(url, '_blank', 'noopener,noreferrer');
};

const closeVideoPlayer = () => {
  // Stop video if playing (only if videoPlayer ref exists)
  if (videoPlayer.value && videoPlayer.value.pause) {
    videoPlayer.value.pause();
    videoPlayer.value.currentTime = 0;
  }
  
  showVideoPlayer.value = false;
  currentVideo.value = null;
  isPlaying.value = false;
};

// Enhanced upload function with URL normalization
const uploadVideoFinal = async () => {
  if (!videoForm.title || !videoForm.url) {
    alert('Please provide both title and video URL');
    return;
  }

  // Validate and normalize YouTube URL
  let youtubeUrl = videoForm.url.trim();
  
  // Ensure URL has proper protocol
  if (!youtubeUrl.startsWith('http')) {
    youtubeUrl = 'https://' + youtubeUrl;
  }
  
  // Validate it's a YouTube URL
  if (!isYouTubeUrl(youtubeUrl)) {
    alert('Please provide a valid YouTube URL (youtube.com or youtu.be)');
    return;
  }

  uploading.value = true;

  try {
    const formData = new FormData()
    formData.append('title', videoForm.title)
    formData.append('description', videoForm.description || '')
    formData.append('type', 'video')
    formData.append('content', youtubeUrl) // Make sure this is the normalized YouTube URL
    
    // Also store in multiple fields for redundancy
    formData.append('url', youtubeUrl)
    formData.append('video_url', youtubeUrl)
    
    if (props.classId) {
      formData.append('assigned_class', props.classId)
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    if (!csrfToken) {
      throw new Error('CSRF token not found')
    }

    console.log('📡 [UPLOAD] Sending YouTube URL:', youtubeUrl)

    const response = await fetch(`/api/resources/upload/${getCurrentTeacherId()}`, {
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
    console.log('📥 [UPLOAD] Response:', data)

    if (data.success) {
      showVideoUpload.value = false
      resetVideoForm()
      showNotification('Video added successfully!', 'success')
      
      // Refresh the page to get updated resources
      router.reload()
    } else {
      let errorMessage = 'Failed to add video: '
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat()
        errorMessage += errorMessages.join(', ')
      } else if (data.message) {
        errorMessage += data.message
      }
      alert(errorMessage)
    }
  } catch (error) {
    console.error('❌ Upload error:', error)
    alert('Network error. Please try again.')
  } finally {
    uploading.value = false
  }
}

// Fetch resources
const fetchResources = async () => {
  if (props.resources) {
    resources.value = props.resources
    loading.value = false
    
    // Debug: Log all resources to see their structure
    console.log('📦 All resources data:', resources.value)
    resources.value.forEach((resource, index) => {
      console.log(`📹 Resource ${index + 1}:`, resource)
      const videoContent = getVideoContent(resource)
      console.log(`🎬 Extracted video content for resource ${index + 1}:`, videoContent)
    })
    
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
        console.log('📦 Fetched resources:', resources.value)
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

// Share resource
const shareResource = async (resource) => {
  const videoContent = getVideoContent(resource)
  const shareData = {
    title: resource.title,
    text: resource.description,
    url: videoContent || window.location.href
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
    const shareText = `${resource.title}\n${resource.description}\n\nWatch: ${videoContent}`
    try {
      await navigator.clipboard.writeText(shareText)
      showNotification('Video link copied to clipboard!', 'success')
    } catch (error) {
      alert('Share feature not supported. Please copy the URL manually.')
    }
  }
}

// Delete resource
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
  videoForm.title = ''
  videoForm.description = ''
  videoForm.url = ''
  videoForm.assigned_class = props.classId || null
}

// Helper methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// ==================== LIFECYCLE ====================
onMounted(async () => {
  document.addEventListener('click', handleClickOutside)
  
  // Ensure teacher object has profile_picture_url
  if (props.teacher?.profile_picture && !props.teacher.profile_picture_url) {
    props.teacher.profile_picture_url = `/storage/${props.teacher.profile_picture}`
  }
  
  await fetchResources()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Custom styles for clean video player */
/* Enhanced Video Player Styles */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}
.html5-video-player {
  display: none !important;
}
.ytp-show-cards-title{
                display: none !important;
      }
.video-player-container {
  border: 1px solid #d1d5db; /* gray-300 */
  border-radius: 0.5rem; /* rounded-lg */
  overflow: hidden;
}

.video-content-area {
  position: relative;
  background: #000;
  aspect-ratio: 16 / 9;
}

.video-content-area iframe,
.video-content-area video {
  display: block;
  width: 100%;
  height: 100%;
  border: none;
}

/* Ensure proper border visibility */
.border-fix {
  box-shadow: 
    0 0 0 1px #d1d5db, /* Outer border */
    inset 0 0 0 1px #000; /* Inner border for video area */
}

/* Play button overlay */
.play-button-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.play-button-overlay:hover {
  background: rgba(0, 0, 0, 0.3);
}

.play-button-circle {
  width: 5rem;
  height: 5rem;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.play-button-circle:hover {
  background: rgba(255, 255, 255, 1);
  transform: scale(1.05);
}

.rotate-180 {
  transform: rotate(180deg);
}

.submenu-link {
  display: block;
  padding: 0.5rem 0.75rem;
  color: #000000;
  border-radius: 0.5rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
  text-decoration: none;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
  text-decoration: none;
}

iframe {
  border-radius: 0;
  background: #000;
}

/* Remove underline from all elements */
.no-underline {
  text-decoration: none !important;
}

/* Ensure no underline appears on hover for any element */
button:hover,
a:hover {
  text-decoration: none !important;
}
</style>