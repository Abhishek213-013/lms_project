<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <TeacherSidebar 
      :classData="classData"
      @showUploadModal="showUploadModal = true"
      @goBackToTeacherPortal="goBackToTeacherPortal"
    />

    <!-- Main Content -->
    <div class="flex-1 ml-64 min-w-0">
      <!-- Top Navbar -->
      <nav class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center min-w-0">
              <h1 class="custom-heading truncate">{{ `${classData?.name || 'Class'} - Class Dashboard` }}</h1>
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
        <!-- Main Content -->
        <div class="min-h-screen bg-gray-50">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ classData?.name || 'Class' }}</h1>
              <p class="text-gray-600">{{ classData?.subject || 'Subject' }} • {{ classData?.studentCount || 0 }} students</p>
              <p class="text-sm text-gray-500">Teacher: {{ classData?.teacher_name || 'Teacher' }}</p>
            </div>
            <button 
              @click="goBackToTeacherPortal"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center space-x-1 no-underline"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              <span>Back to Teacher Portal</span>
            </button>
          </div>

          <!-- Three Main Tiles -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Give Assignment Tile -->
            <div 
              @click="navigateToAssignments"
              class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Give Assignment</h3>
                  <p class="text-sm text-gray-600">Create and manage assignments</p>
                </div>
              </div>
            </div>

            <!-- Provide Resources Tile -->
            <div 
              @click="navigateToResources"
              class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Provide Resources</h3>
                  <p class="text-sm text-gray-600">Upload and share learning materials</p>
                </div>
              </div>
            </div>

            <!-- Schedule Class Tile -->
            <div 
              @click="navigateToSchedule"
              class="bg-white rounded-lg border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">Schedule Class</h3>
                  <p class="text-sm text-gray-600">Manage class schedule and timings</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Assignments</p>
                  <h3 class="text-xl font-bold text-gray-800">{{ stats?.totalAssignments || 0 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Resources</p>
                  <h3 class="text-xl font-bold text-gray-800">{{ stats?.totalResources || 0 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Scheduled</p>
                  <h3 class="text-xl font-bold text-gray-800">{{ stats?.totalSchedules || 0 }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Students</p>
                  <h3 class="text-xl font-bold text-gray-800">{{ classData?.studentCount || 0 }}</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
            <div class="space-y-4">
              <div 
                v-for="activity in recentActivities" 
                :key="activity.id"
                class="flex items-center justify-between p-4 border border-gray-100 rounded-lg"
              >
                <div class="flex items-center space-x-4">
                  <div :class="`p-3 rounded-lg ${getActivityColor(activity.type)}`">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="activity.type === 'assignment'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                      <path v-else-if="activity.type === 'resource'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-medium text-gray-900">{{ activity.title }}</h4>
                    <p class="text-sm text-gray-600">{{ activity.description }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(activity.created_at) }}</p>
                  </div>
                </div>
                <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(activity.status)}`">
                  {{ activity.status }}
                </span>
              </div>
              <div v-if="!recentActivities || recentActivities.length === 0" class="text-center py-8 text-gray-500">
                No recent activity. Create your first assignment or resource!
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Upload Resource Modal -->
  <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Upload Resource</h3>
      </div>

      <form @submit.prevent="uploadResource" class="p-6 space-y-4">
        <!-- Resource Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Resource Type *</label>
          <select 
            v-model="newResource.type"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Select type</option>
            <option value="video">Video Link</option>
            <option value="pdf">PDF Document</option>
            <option value="document">Other Document</option>
            <option value="link">External Link</option>
          </select>
        </div>

        <!-- Resource Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
          <input 
            v-model="newResource.title"
            type="text" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter resource title"
          >
        </div>

        <!-- Resource Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
          <textarea 
            v-model="newResource.description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter resource description"
          ></textarea>
        </div>

        <!-- Resource Content based on type -->
        <div v-if="newResource.type === 'video'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Video URL *</label>
          <input 
            v-model="newResource.content"
            type="url" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="https://www.youtube.com/watch?v=..."
          >
        </div>

        <div v-else-if="newResource.type === 'pdf' || newResource.type === 'document'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Upload File *</label>
          <input 
            type="file"
            @change="handleFileUpload"
            accept=".pdf,.doc,.docx,.txt"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
        </div>

        <div v-else-if="newResource.type === 'link'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Link URL *</label>
          <input 
            v-model="newResource.content"
            type="url" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="https://example.com"
          >
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button 
            type="button"
            @click="showUploadModal = false"
            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors no-underline"
          >
            Cancel
          </button>
          <button 
            type="submit"
            :disabled="uploading"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed no-underline"
          >
            {{ uploading ? 'Uploading...' : 'Upload Resource' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Profile Picture Upload Modal -->
  <div v-if="showProfilePictureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg w-full max-w-md mx-4">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Upload Profile Picture</h3>
      </div>

      <div class="p-6">
        <!-- Image Preview -->
        <div v-if="profilePicturePreview" class="mb-4 flex justify-center">
          <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200">
            <img :src="profilePicturePreview" alt="Preview" class="w-full h-full object-cover">
          </div>
        </div>

        <!-- Upload Area -->
        <div 
          @drop="handleDrop"
          @dragover="handleDragOver"
          @dragleave="handleDragLeave"
          :class="`border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors ${
            isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'
          }`"
          @click="triggerFileInput"
        >
          <input 
            type="file" 
            ref="fileInput"
            @change="handleFileSelect"
            accept="image/jpeg,image/png,image/jpg,image/gif"
            class="hidden"
          >
          
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          
          <p class="text-gray-600 mb-2">
            <span class="text-blue-600 font-medium">Click to upload</span> or drag and drop
          </p>
          <p class="text-xs text-gray-500">
            PNG, JPG, GIF up to 2MB
          </p>
        </div>

        <!-- Error Message -->
        <div v-if="uploadError" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-sm text-red-600">{{ uploadError }}</p>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 mt-4">
          <button 
            type="button"
            @click="cancelProfilePictureUpload"
            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button 
            type="button"
            @click="uploadProfilePicture"
            :disabled="!selectedFile || uploadingPicture"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center"
          >
            <svg v-if="uploadingPicture" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ uploadingPicture ? 'Uploading...' : 'Upload Picture' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'

// Props from Laravel with default values
const props = defineProps({
  classData: {
    type: Object,
    default: () => ({
      id: null,
      name: 'Class',
      subject: 'Subject',
      studentCount: 0,
      teacher_name: 'Teacher',
      teacher_id: null
    })
  },
  stats: {
    type: Object,
    default: () => ({
      totalAssignments: 0,
      totalResources: 0,
      totalSchedules: 0
    })
  },
  recentActivities: {
    type: Array,
    default: () => []
  },
  teacher: {
    type: Object,
    default: () => ({
      name: 'Teacher',
      id: null,
      profile_picture_url: null,
      profile_picture: null
    })
  }
})

// UI State
const showUploadModal = ref(false)
const showProfilePictureModal = ref(false)
const uploading = ref(false)
const userMenuOpen = ref(false)
const uploadingPicture = ref(false)

// Profile Picture State
const selectedFile = ref(null)
const profilePicturePreview = ref(null)
const isDragging = ref(false)
const uploadError = ref(null)
const fileInput = ref(null)

// Forms
const newResource = ref({
  type: '',
  title: '',
  description: '',
  content: '',
  file: null
})

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

// Navigation methods
const goBackToTeacherPortal = () => {
  const teacherId = props.classData?.teacher_id || props.teacher?.id
  if (teacherId) {
    router.visit(`/teacher/portal`)
  } else {
    router.visit('/teacher/portal')
  }
}

const navigateToAssignments = () => {
  if (props.classData?.id) {
    router.visit(`/teacher/class/${props.classData.id}/assignments`)
  }
}

const navigateToResources = () => {
  if (props.classData?.id) {
    router.visit(`/teacher/class/${props.classData.id}/resources`)
  }
}

const navigateToSchedule = () => {
  if (props.classData?.id) {
    router.visit(`/teacher/class/${props.classData.id}/schedule`)
  }
}

const navigateToSettings = () => {
  router.visit('/teacher/settings')
}

const editProfile = () => {
  router.visit('/teacher/profile')
}

const logout = async () => {
  try {
    router.post('/logout')
  } catch (err) {
    console.error('Logout error:', err)
  }
}

// UI Methods
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Profile Picture Methods
const triggerProfilePictureUpload = () => {
  showProfilePictureModal.value = true
  uploadError.value = null
  selectedFile.value = null
  profilePicturePreview.value = null
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    validateAndSetFile(file)
  }
}

const handleDrop = (event) => {
  event.preventDefault()
  isDragging.value = false
  
  const files = event.dataTransfer.files
  if (files.length > 0) {
    validateAndSetFile(files[0])
  }
}

const handleDragOver = (event) => {
  event.preventDefault()
  isDragging.value = true
}

const handleDragLeave = (event) => {
  event.preventDefault()
  isDragging.value = false
}

const validateAndSetFile = (file) => {
  uploadError.value = null
  
  // Validate file type
  const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']
  if (!validTypes.includes(file.type)) {
    uploadError.value = 'Please select a valid image file (JPEG, PNG, JPG, GIF)'
    return
  }
  
  // Validate file size (2MB)
  if (file.size > 2 * 1024 * 1024) {
    uploadError.value = 'File size must be less than 2MB'
    return
  }
  
  selectedFile.value = file
  
  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    profilePicturePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const uploadProfilePicture = async () => {
  if (!selectedFile.value) return
  
  uploadingPicture.value = true
  uploadError.value = null
  
  try {
    const formData = new FormData()
    formData.append('profile_picture', selectedFile.value)
    
    const response = await fetch(`/api/profile-picture/teacher/${props.teacher.id}/upload`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: formData
    })
    
    // Check if response is JSON
    const contentType = response.headers.get('content-type');
    let result;
    
    if (contentType && contentType.includes('application/json')) {
      result = await response.json();
    } else {
      // Handle non-JSON response (likely an error page)
      const text = await response.text();
      console.error('Non-JSON response:', text);
      throw new Error('Server returned an error. Please try again.');
    }
    
    if (result.success) {
      // Update the teacher object with new profile picture URL
      props.teacher.profile_picture_url = result.profile_picture_url
      props.teacher.profile_picture = result.profile_picture_path
      
      showProfilePictureModal.value = false
      selectedFile.value = null
      profilePicturePreview.value = null
      
      // Show success message
      alert('Profile picture updated successfully!')
    } else {
      uploadError.value = result.message || 'Failed to upload profile picture'
    }
  } catch (error) {
    console.error('Error uploading profile picture:', error)
    uploadError.value = error.message || 'An error occurred while uploading the picture'
    
    // If it's a server error, show more details
    if (error.message.includes('Server returned an error')) {
      uploadError.value = 'Server error occurred. Please check if the upload endpoint is working.';
    }
  } finally {
    uploadingPicture.value = false
  }
}

const removeProfilePicture = async () => {
  if (!confirm('Are you sure you want to remove your profile picture?')) {
    return
  }
  
  uploadingPicture.value = true
  
  try {
    const response = await fetch(`/api/profile-picture/teacher/${props.teacher.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      },
    })
    
    const result = await response.json()
    
    if (result.success) {
      // Remove profile picture from teacher object
      props.teacher.profile_picture_url = null
      props.teacher.profile_picture = null
      
      alert('Profile picture removed successfully!')
    } else {
      alert(result.message || 'Failed to remove profile picture')
    }
  } catch (error) {
    console.error('Error removing profile picture:', error)
    alert('An error occurred while removing the picture')
  } finally {
    uploadingPicture.value = false
  }
}

const cancelProfilePictureUpload = () => {
  showProfilePictureModal.value = false
  selectedFile.value = null
  profilePicturePreview.value = null
  uploadError.value = null
}

// Helper methods
const getActivityColor = (type) => {
  const colors = {
    assignment: 'bg-blue-500',
    resource: 'bg-green-500',
    schedule: 'bg-purple-500'
  }
  return colors[type] || 'bg-gray-500'
}

const getStatusColor = (status) => {
  const colors = {
    active: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-blue-100 text-blue-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Modal Methods
const uploadResource = async () => {
  if (!newResource.value.type || !newResource.value.title) {
    alert('Please fill in all required fields')
    return
  }
  uploading.value = true
  // Simulate upload
  setTimeout(() => {
    uploading.value = false
    showUploadModal.value = false
    alert('Resource uploaded successfully!')
    resetResourceForm()
  }, 1500)
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    newResource.value.file = file
    newResource.value.content = file.name
  }
}

const resetResourceForm = () => {
  newResource.value = {
    type: '',
    title: '',
    description: '',
    content: '',
    file: null
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  
  // Ensure teacher object has profile_picture_url
  if (props.teacher?.profile_picture && !props.teacher.profile_picture_url) {
    props.teacher.profile_picture_url = `/storage/${props.teacher.profile_picture}`
  }
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