<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Class Subjects" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Class {{ grade }} - Subjects</h1>
            <p class="text-gray-600">Manage subjects and teacher assignments</p>
            <div v-if="subjects.length > 0" class="mt-2">
              <span class="text-sm text-gray-500">
                Data Source: <span class="font-medium">{{ dataSource }}</span>
              </span>
            </div>
          </div>
          <div class="flex space-x-3">
            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
              <Link href="/admin/courses/all-courses">← Back to Classes</Link>
            </button>
            <button 
              @click="refreshData"
              class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Refresh</span>
            </button>
          </div>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700">{{ error }}</span>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-green-700">{{ successMessage }}</span>
          </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Total Subjects</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ filteredSubjects.length }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Total Students</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ totalStudents }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Assigned Teachers</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ totalTeachers }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-orange-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Completion</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ completionRate }}%</h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Subjects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="subject in filteredSubjects" 
            :key="subject.id"
            class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1"
          >
            <!-- Subject Image -->
            <div class="mb-4">
              <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                <img 
                  v-if="subject.image"
                  :src="getImageUrl(subject.image)"
                  :alt="subject.name"
                  class="w-full h-full object-cover"
                >
                <div v-else class="text-gray-400 flex flex-col items-center">
                  <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span class="text-xs">No Image</span>
                </div>
              </div>
            </div>

            <div class="flex justify-between items-start mb-4">
              <h3 class="font-semibold text-gray-800 text-lg">{{ subject.name }}</h3>
              <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                {{ subject.code }}
              </span>
            </div>
            
            <div class="space-y-3">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Students:</span>
                <span class="font-medium text-gray-800">{{ subject.studentCount }}</span>
              </div>
              
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Assigned Teachers:</span>
                <span class="font-medium" :class="subject.assignedTeachers.length > 0 ? 'text-green-600' : 'text-yellow-600'">
                  {{ subject.assignedTeachers.length }}
                </span>
              </div>

              <!-- Assigned Teachers Preview -->
              <div v-if="subject.assignedTeachers.length > 0" class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2 font-medium">ASSIGNED TEACHERS</p>
                <div class="flex -space-x-2">
                  <div 
                    v-for="teacher in subject.assignedTeachers.slice(0, 3)" 
                    :key="teacher.id"
                    class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-semibold border-2 border-white shadow-sm"
                    :title="teacher.name"
                  >
                    {{ getInitials(teacher.name) }}
                  </div>
                  <div 
                    v-if="subject.assignedTeachers.length > 3"
                    class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-xs font-semibold border-2 border-white shadow-sm"
                    title="More teachers"
                  >
                    +{{ subject.assignedTeachers.length - 3 }}
                  </div>
                </div>
              </div>

              <div v-else class="mt-4 pt-4 border-t border-gray-100">
                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  No teachers assigned
                </span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
              <!-- Manage Teachers Button -->
              <button 
                @click="viewSubjectTeachers(subject.id)"
                class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                </svg>
                <span>Manage Teachers</span>
              </button>

              <!-- Edit/Delete Buttons -->
              <div class="flex space-x-2">
                <button 
                  @click="editSubject(subject)"
                  class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  <span>Edit</span>
                </button>
                <button 
                  @click="deleteSubject(subject)"
                  class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  <span>Delete</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && filteredSubjects.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No subjects found</h3>
          <p class="text-gray-500 mb-4">There are no subjects available for this class.</p>
          <button 
            @click="refreshData"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            Refresh Data
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600">Loading subjects...</p>
        </div>

        <!-- Edit Subject Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Edit Subject</h3>
            </div>
            
            <form @submit.prevent="updateSubject" class="p-6 space-y-6">
              <!-- Image Upload Section -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject Image</label>
                
                <!-- Current Image Display -->
                <div v-if="editingSubject.image && !selectedImageFile" class="mb-4">
                  <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                  <div class="relative inline-block">
                    <img 
                      :src="getImageUrl(editingSubject.image)" 
                      alt="Current subject image"
                      class="w-32 h-32 object-cover rounded-lg border"
                    >
                    <button 
                      type="button"
                      @click="removeCurrentImage"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- No Image State -->
                <div v-if="!editingSubject.image && !selectedImageFile" class="mb-4">
                  <p class="text-sm text-gray-600 mb-2">No image currently set</p>
                </div>

                <!-- Image Upload Area -->
                <div 
                  @click="triggerImageInput"
                  @drop="handleImageDrop"
                  @dragover="handleDragOver"
                  @dragleave="handleDragLeave"
                  class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition-colors"
                  :class="{ 'border-blue-500 bg-blue-50': isDragOver }"
                >
                  <input 
                    type="file" 
                    ref="imageInput"
                    @change="handleImageSelect"
                    accept="image/*"
                    class="hidden"
                  >
                  
                  <div v-if="!selectedImageFile" class="space-y-3">
                    <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                      <p class="text-sm font-medium text-gray-700">Upload subject image</p>
                      <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG up to 5MB</p>
                    </div>
                    <button type="button" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                      Click to browse or drag & drop
                    </button>
                  </div>
                  
                  <div v-else class="space-y-3">
                    <div class="relative inline-block">
                      <img 
                        :src="getImagePreview()" 
                        alt="Subject image preview"
                        class="w-32 h-32 object-cover rounded-lg mx-auto"
                      >
                      <button 
                        type="button"
                        @click.stop="removeImage"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                      </button>
                    </div>
                    <p class="text-sm text-gray-600">New image ready for upload</p>
                    <p class="text-xs text-gray-500">{{ selectedImageFile.name }} ({{ formatFileSize(selectedImageFile.size) }})</p>
                  </div>
                </div>
              </div>

              <!-- Subject Details -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Subject Name *</label>
                  <input 
                    v-model="editingSubject.name"
                    type="text" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Subject Code *</label>
                  <input 
                    v-model="editingSubject.code"
                    type="text" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Student Count</label>
                <input 
                  v-model="editingSubject.studentCount"
                  type="number" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  min="0"
                  required
                >
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea 
                  v-model="editingSubject.description"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Optional subject description..."
                ></textarea>
              </div>

              <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button 
                  type="button"
                  @click="closeEditModal"
                  class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm font-medium"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="updating"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ updating ? 'Updating...' : 'Update Subject' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Delete Subject</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to delete <strong>{{ deletingSubject?.name }}</strong>? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
              <button 
                @click="showDeleteModal = false"
                class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm font-medium"
              >
                Cancel
              </button>
              <button 
                @click="confirmDelete"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors"
              >
                Delete Subject
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Get grade from Inertia props
const props = defineProps({
  grade: {
    type: [String, Number],
    required: true
  }
})

const subjects = ref([])
const loading = ref(true)
const error = ref('')
const successMessage = ref('')
const searchQuery = ref('')
const dataSource = ref('')
const isDragOver = ref(false)
const selectedImageFile = ref(null)
const imageInput = ref(null)
const updating = ref(false)
// Modal states
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const editingSubject = ref(null)
const deletingSubject = ref(null)

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Computed properties for statistics
const filteredSubjects = computed(() => {
  if (!searchQuery.value) return subjects.value
  
  const query = searchQuery.value.toLowerCase()
  return subjects.value.filter(subject => 
    subject.name?.toLowerCase().includes(query) ||
    subject.code?.toLowerCase().includes(query)
  )
})

const totalStudents = computed(() => {
  return subjects.value.reduce((total, subject) => total + subject.studentCount, 0)
})

const totalTeachers = computed(() => {
  return subjects.value.reduce((total, subject) => total + subject.assignedTeachers.length, 0)
})

const completionRate = computed(() => {
  const totalSubjects = subjects.value.length
  const subjectsWithTeachers = subjects.value.filter(subject => subject.assignedTeachers.length > 0).length
  return totalSubjects > 0 ? Math.round((subjectsWithTeachers / totalSubjects) * 100) : 0
})

// Subject management functions
const editSubject = (subject) => {
  editingSubject.value = { 
    ...subject,
    // Ensure we have the raw image path for editing
    image: subject.raw_image || subject.image
  }
  selectedImageFile.value = null
  showEditModal.value = true
}

const deleteSubject = (subject) => {
  deletingSubject.value = subject
  showDeleteModal.value = true
}

const updateSubject = async () => {
  try {
    updating.value = true
    error.value = ''
    successMessage.value = ''
    
    console.log('🔄 Starting subject update for ID:', editingSubject.value.id)
    
    // Create FormData for file upload
    const formData = new FormData()
    formData.append('_method', 'PUT')
    formData.append('subject', editingSubject.value.name)
    formData.append('code', editingSubject.value.code)
    formData.append('student_count', editingSubject.value.studentCount)
    formData.append('description', editingSubject.value.description || '')
    
    // 🔥 FIX: Properly handle image upload
    console.log('📸 Image state:', {
      selectedImageFile: selectedImageFile.value,
      currentImage: editingSubject.value.image,
      removeImage: editingSubject.value.image === null
    })
    
    // Append image file if selected
    if (selectedImageFile.value) {
      console.log('📸 Adding image to FormData:', selectedImageFile.value.name)
      formData.append('image', selectedImageFile.value)
    }
    
    // 🔥 FIX: Send remove_image as proper boolean value
    if (editingSubject.value.image === null) {
      console.log('🗑️ Removing current image')
      // Send as proper boolean value
      formData.append('remove_image', '1') // This will be converted to boolean true
    }

    console.log('🚀 Sending POST request to:', `/courses/${editingSubject.value.id}`)
    console.log('📦 FormData entries:')
    for (let [key, value] of formData.entries()) {
      console.log(`  ${key}:`, value instanceof File ? `File: ${value.name}` : value)
    }
    
    const response = await apiClient.post(`/courses/${editingSubject.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      timeout: 30000
    })
    
    console.log('✅ Update response:', response.data)
    
    if (response.data.success) {
      successMessage.value = 'Subject updated successfully'
      
      // Update the subject in the local state
      const index = subjects.value.findIndex(s => s.id === editingSubject.value.id)
      if (index !== -1) {
        subjects.value[index] = { 
          ...subjects.value[index],
          name: response.data.data.subject || response.data.data.name,
          code: response.data.data.code,
          studentCount: response.data.data.studentCount,
          description: response.data.data.description,
          image: response.data.data.image,
          thumbnail: response.data.data.thumbnail,
          raw_image: response.data.data.raw_image
        }
      }
      
      closeEditModal()
      refreshData()
    } else {
      error.value = response.data.message || 'Failed to update subject'
    }
  } catch (err) {
    console.error('💥 Error updating subject:', err)
    console.error('💥 Full error details:', {
      message: err.message,
      status: err.response?.status,
      data: err.response?.data,
      url: err.config?.url
    })
    
    if (err.response?.status === 422) {
      // Show validation errors
      const errors = err.response.data.errors
      error.value = 'Validation errors: ' + Object.values(errors).flat().join(', ')
    } else if (err.response?.status === 413) {
      error.value = 'File too large. Please select a smaller image.'
    } else if (err.response?.status === 404) {
      error.value = 'Endpoint not found.'
    } else {
      error.value = 'Failed to update subject: ' + (err.response?.data?.message || err.message)
    }
  } finally {
    updating.value = false
  }
}

// Image handling functions
const triggerImageInput = () => {
  imageInput.value?.click()
}

const handleImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    console.log('📸 File selected:', file.name, file.size, file.type)
    
    if (file.size > 5 * 1024 * 1024) { // 5MB limit
      error.value = 'Image size should be less than 5MB'
      return
    }
    if (!file.type.startsWith('image/')) {
      error.value = 'Please select a valid image file'
      return
    }
    selectedImageFile.value = file
    console.log('✅ Image file ready for upload')
  } else {
    console.log('📸 No file selected')
  }
}

const handleImageDrop = (event) => {
  event.preventDefault()
  isDragOver.value = false
  
  const files = event.dataTransfer.files
  if (files.length > 0) {
    handleImageSelect({ target: { files } })
  }
}

const handleDragOver = (event) => {
  event.preventDefault()
  isDragOver.value = true
}

const handleDragLeave = (event) => {
  event.preventDefault()
  isDragOver.value = false
}

const getImagePreview = () => {
  if (selectedImageFile.value) {
    return URL.createObjectURL(selectedImageFile.value)
  }
  return ''
}

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/assets/img/courses/default-course.jpg'
  
  // Handle both full URLs and storage paths
  if (imagePath.startsWith('http')) {
    return imagePath
  } else if (imagePath.startsWith('storage/')) {
    return `/${imagePath}`
  } else {
    return `/storage/${imagePath}`
  }
}

const removeImage = () => {
  console.log('🗑️ Removing selected image')
  selectedImageFile.value = null
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}

const removeCurrentImage = () => {
  console.log('🗑️ Removing current image')
  editingSubject.value.image = null
  selectedImageFile.value = null
  // Also clear the file input
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}


const closeEditModal = () => {
  showEditModal.value = false
  editingSubject.value = null
  selectedImageFile.value = null
  isDragOver.value = false
}

const confirmDelete = async () => {
  try {
    error.value = ''
    successMessage.value = ''
    
    const response = await apiClient.delete(`/courses/${deletingSubject.value.id}`)
    
    if (response.data.success) {
      successMessage.value = 'Subject deleted successfully'
      
      // Remove the subject from the local state
      subjects.value = subjects.value.filter(s => s.id !== deletingSubject.value.id)
      
      showDeleteModal.value = false
      deletingSubject.value = null
    } else {
      error.value = response.data.message || 'Failed to delete subject'
    }
  } catch (err) {
    console.error('Error deleting subject:', err)
    if (err.response?.status === 404) {
      error.value = 'Subject not found. It may have already been deleted.'
    } else {
      error.value = 'Failed to delete subject: ' + (err.response?.data?.message || err.message)
    }
  }
}

// Helper function to format file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Fetch subjects for the current class
const fetchSubjects = async (grade) => {
  try {
    loading.value = true
    error.value = ''
    
    console.log(`📡 [DEBUG] Starting fetch for grade ${grade}`)
    
    // FIXED: Remove the /api/ prefix since apiClient already handles it
    const response = await apiClient.get(`/courses/class/${grade}/subjects`)
    
    console.log('✅ [DEBUG] Subjects API Response received')
    console.log('📊 [DEBUG] Response data:', response.data)
    
    if (response.data.success) {
      subjects.value = response.data.data
      dataSource.value = response.data.source === 'database' ? 'Database' : 'Mock Data'
      console.log(`📊 Loaded ${subjects.value.length} subjects from ${response.data.source}`)
    } else {
      error.value = response.data.message || 'Failed to fetch subjects'
      console.error('❌ [DEBUG] API returned error:', response.data)
    }
  } catch (err) {
    console.error('💥 [DEBUG] Error fetching subjects:', err)
    
    if (err.response) {
      console.error('📡 [DEBUG] Response error details:', {
        status: err.response.status,
        data: err.response.data
      })
      error.value = err.response.data?.message || `Server error: ${err.response.status}`
    } else if (err.request) {
      console.error('🌐 [DEBUG] No response received')
      error.value = 'No response from server. Please check if the server is running.'
    } else {
      console.error('⚡ [DEBUG] Request setup error:', err.message)
      error.value = 'Request failed: ' + err.message
    }
    
    // Enhanced fallback to mock data
    console.log('🔄 [DEBUG] Using enhanced fallback mock data')
    const mockSubjects = getEnhancedMockSubjects(grade)
    subjects.value = mockSubjects
    dataSource.value = 'Mock Data (Fallback - API Unavailable)'
  } finally {
    loading.value = false
  }
}

const getEnhancedMockSubjects = (grade) => {
  const baseSubjects = [
    { id: 1, name: 'Mathematics', code: 'MATH' },
    { id: 2, name: 'Science', code: 'SCI' },
    { id: 3, name: 'English', code: 'ENG' },
    { id: 4, name: 'Social Studies', code: 'SST' },
    { id: 5, name: 'Computer Science', code: 'CS' },
    { id: 6, name: 'Physical Education', code: 'PE' },
    { id: 7, name: 'Bangla', code: 'BAN' },
    { id: 8, name: 'Religion', code: 'REL' }
  ]
  
  return baseSubjects.map((subject, index) => ({
    ...subject,
    id: subject.id + (grade * 100),
    studentCount: Math.floor(Math.random() * 30) + 20,
    image: Math.random() > 0.5 ? null : `/assets/img/subjects/${subject.code.toLowerCase()}.jpg`,
    assignedTeachers: Math.random() > 0.3 ? [
      {
        id: grade * 10 + index,
        name: `Teacher ${['A', 'B', 'C'][index % 3]} ${grade}`,
        email: `teacher${grade}${index}@school.com`,
        experience: Math.floor(Math.random() * 15) + 5
      }
    ] : []
  }))
}

const refreshData = () => {
  console.log('🔄 Manually refreshing subjects data')
  fetchSubjects(props.grade)
}

const viewSubjectTeachers = (subjectId) => {
  console.log(`🎯 Navigating to subject ${subjectId} teachers for grade ${props.grade}`)
  router.visit(`/admin/courses/class/${props.grade}/subject/${subjectId}/teachers`)
}

const getInitials = (name) => {
  return name.split(' ').map(word => word[0]).join('').toUpperCase()
}

onMounted(() => {
  console.log('🚀 ClassSubjects component mounted for grade:', props.grade)
  fetchSubjects(props.grade)
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
</style>