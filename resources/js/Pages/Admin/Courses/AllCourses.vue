<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Course Management" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-900">Course Management</h1>
          <p class="text-gray-600">Manage all courses and classes in the system</p>
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

        <!-- Three Main Tiles -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <!-- Regular Courses Tile -->
          <div 
            class="bg-white rounded-xl border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
            @click="viewRegularCourses"
          >
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l9 5m-9-5v10"></path>
                </svg>
              </div>
              <span class="text-blue-600 text-sm font-semibold">{{ regularClasses.length }} classes</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Regular Courses</h3>
            <p class="text-gray-600 text-sm mb-4">Manage academic classes from Grade 1 to 12 with standard curriculum</p>
            <div class="flex items-center text-blue-600 text-sm font-medium">
              <span>View Classes</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>

          <!-- Skill Based Courses Tile -->
          <div 
            class="bg-white rounded-xl border border-gray-200 p-6 cursor-pointer hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
            @click="viewSkillCourses"
          >
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <span class="text-green-600 text-sm font-semibold">{{ otherCourses.length }} courses</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Skill Based Courses</h3>
            <p class="text-gray-600 text-sm mb-4">Manage life skills, spoken English, computer basics, and other skill courses</p>
            <div class="flex items-center text-green-600 text-sm font-medium">
              <span>View Courses</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>

          <!-- Create New Course Tile -->
          <div 
            class="bg-white rounded-xl border-2 border-dashed border-gray-300 p-6 cursor-pointer hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:border-blue-400"
            @click="showCreateModal = true"
          >
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
              </div>
              <span class="text-purple-600 text-sm font-semibold">Create New</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Create New Course</h3>
            <p class="text-gray-600 text-sm mb-4">Add new regular class or skill-based course to the system</p>
            <div class="flex items-center text-purple-600 text-sm font-medium">
              <span>Get Started</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Create Course Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Create New Course</h3>
            </div>

            <form @submit.prevent="createNewCourse" class="p-6 space-y-6">
              <!-- Course Type Selection -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Course Type *</label>
                <div class="grid grid-cols-2 gap-4">
                  <label class="relative flex cursor-pointer">
                    <input 
                      type="radio" 
                      v-model="newCourse.type"
                      value="regular"
                      class="sr-only"
                      @change="onCourseTypeChange"
                    >
                    <div class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-colors w-full"
                      :class="{'border-blue-500 bg-blue-50': newCourse.type === 'regular'}">
                      <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center"
                        :class="{'border-blue-500': newCourse.type === 'regular'}">
                        <div v-if="newCourse.type === 'regular'" class="w-3 h-3 bg-blue-500 rounded-full"></div>
                      </div>
                      <div class="text-left">
                        <span class="font-medium text-gray-900">Regular Class</span>
                        <p class="text-sm text-gray-500">Grades 1-12 with standard curriculum</p>
                      </div>
                    </div>
                  </label>
                  
                  <label class="relative flex cursor-pointer">
                    <input 
                      type="radio" 
                      v-model="newCourse.type"
                      value="other"
                      class="sr-only"
                      @change="onCourseTypeChange"
                    >
                    <div class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 transition-colors w-full"
                      :class="{'border-green-500 bg-green-50': newCourse.type === 'other'}">
                      <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center"
                        :class="{'border-green-500': newCourse.type === 'other'}">
                        <div v-if="newCourse.type === 'other'" class="w-3 h-3 bg-green-500 rounded-full"></div>
                      </div>
                      <div class="text-left">
                        <span class="font-medium text-gray-900">Other Course</span>
                        <p class="text-sm text-gray-500">Life Skills, Spoken English, etc.</p>
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Course Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- For Regular Classes -->
                <div v-if="newCourse.type === 'regular'">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Class Grade *</label>
                  <select 
                    v-model="newCourse.grade"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Select Grade</option>
                    <option v-for="grade in availableGrades" :key="grade" :value="grade">
                      Class {{ grade }}
                    </option>
                  </select>
                </div>
                
                <!-- For Other Courses -->
                <div v-if="newCourse.type === 'other'">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Course Category *</label>
                  <select 
                    v-model="newCourse.category"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  >
                    <option value="">Select Category</option>
                    <option v-for="category in availableCategories" :key="category.value" :value="category.value">
                      {{ category.label }}
                    </option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ newCourse.type === 'regular' ? 'Class Name' : 'Course Name' }} *
                  </label>
                  <input 
                    v-model="newCourse.name"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :placeholder="newCourse.type === 'regular' ? 'e.g., Class 1A, Science Group' : 'e.g., Life Skills, Spoken English'"
                  >
                </div>
              </div>

              <!-- Subject Information -->
              <div v-if="newCourse.type === 'regular'">
                <label class="block text-sm font-medium text-gray-700 mb-2">Subjects *</label>
                <div class="space-y-4">
                  <div 
                    v-for="(subject, index) in newCourse.subjects" 
                    :key="index"
                    class="border border-gray-200 rounded-lg p-4 space-y-4"
                  >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject Name *</label>
                        <input 
                          v-model="subject.name"
                          type="text" 
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Mathematics, Science, etc."
                        >
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject Code *</label>
                        <input 
                          v-model="subject.code"
                          type="text" 
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="MATH, SCI, etc."
                        >
                      </div>
                    </div>
                    
                    <!-- Subject Image Upload -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Subject Image</label>
                      <div 
                        @click="triggerSubjectImageInput(index)"
                        @drop="handleSubjectImageDrop($event, index)"
                        @dragover="handleDragOver"
                        @dragleave="handleDragLeave"
                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-400 transition-colors"
                        :class="{ 'border-blue-500 bg-blue-50': subject.isDragOver }"
                      >
                        <input 
                          type="file" 
                          :ref="el => subjectImageInputs[index] = el"
                          @change="handleSubjectImageSelect($event, index)"
                          accept="image/*"
                          class="hidden"
                        >
                        
                        <div v-if="!subject.imageFile" class="space-y-2">
                          <svg class="w-8 h-8 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                              :src="getSubjectImagePreview(index)" 
                              :alt="`${subject.name} image preview`"
                              class="w-24 h-24 object-cover rounded-lg mx-auto"
                            >
                            <button 
                              type="button"
                              @click.stop="removeSubjectImage(index)"
                              class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                            >
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                              </svg>
                            </button>
                          </div>
                          <p class="text-sm text-gray-600">Image ready for upload</p>
                          <p class="text-xs text-gray-500">{{ subject.imageFile.name }} ({{ formatFileSize(subject.imageFile.size) }})</p>
                        </div>
                      </div>
                    </div>

                    <div class="flex justify-between items-center">
                      <div class="text-sm text-gray-500">
                        Subject {{ index + 1 }} of {{ newCourse.subjects.length }}
                      </div>
                      <button 
                        type="button"
                        @click="removeSubject(index)"
                        class="p-2 text-red-600 hover:text-red-800"
                        v-if="newCourse.subjects.length > 1"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
                <button 
                  type="button"
                  @click="addSubject"
                  class="mt-2 flex items-center space-x-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  <span>Add Another Subject</span>
                </button>
              </div>

              <!-- For Other Courses - Single Subject -->
              <div v-if="newCourse.type === 'other'">
                <label class="block text-sm font-medium text-gray-700 mb-2">Course Details *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Course Code</label>
                    <input 
                      v-model="newCourse.courseCode"
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      placeholder="Course code"
                    >
                  </div>
                </div>
                
                <!-- Course Image Upload for Other Courses -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Course Image</label>
                  <div 
                    @click="triggerImageInput"
                    @drop="handleImageDrop"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-green-400 transition-colors"
                    :class="{ 'border-green-500 bg-green-50': isDragOver }"
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
                        <p class="text-sm font-medium text-gray-700">Upload course image</p>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG up to 5MB</p>
                      </div>
                      <button type="button" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        Click to browse or drag & drop
                      </button>
                    </div>
                    
                    <div v-else class="space-y-3">
                      <div class="relative inline-block">
                        <img 
                          :src="getImagePreview()" 
                          :alt="`Course image preview`"
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
                      <p class="text-sm text-gray-600">Image ready for upload</p>
                      <p class="text-xs text-gray-500">{{ selectedImageFile.name }} ({{ formatFileSize(selectedImageFile.size) }})</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Course Details -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Course Capacity</label>
                  <input 
                    v-model="newCourse.capacity"
                    type="number" 
                    min="1"
                    max="60"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Maximum students"
                  >
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                  <select 
                    v-model="newCourse.status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="upcoming">Upcoming</option>
                  </select>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea 
                  v-model="newCourse.description"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :placeholder="newCourse.type === 'regular' ? 'Optional class description...' : 'Course description and objectives...'"
                ></textarea>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button 
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="creating"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ creating ? 'Creating...' : 'Create Course' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Total Classes</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ regularClasses.length }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Skill Courses</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ otherCourses.length }}</h3>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
              <div class="p-2 bg-orange-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Active Teachers</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ activeTeachersCount }}</h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Courses Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Recent Courses</h3>
            <button 
              @click="fetchClasses"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center space-x-1"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>Refresh</span>
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Regular Classes Preview -->
            <div 
              v-for="classItem in regularClasses.slice(0, 3)" 
              :key="classItem.id"
              class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
              @click="viewClassDetails(classItem)"
            >
              <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <span class="text-blue-600 font-bold text-sm">{{ classItem.grade }}</span>
                </div>
                <span 
                  :class="`px-2 py-1 text-xs rounded-full ${
                    classItem.status === 'active' 
                      ? 'bg-green-100 text-green-800' 
                      : classItem.status === 'inactive'
                      ? 'bg-red-100 text-red-800'
                      : 'bg-yellow-100 text-yellow-800'
                  }`"
                >
                  {{ classItem.status }}
                </span>
              </div>
              <h4 class="font-semibold text-gray-800 mb-1">{{ classItem.name }}</h4>
              <p class="text-sm text-gray-600 mb-2">{{ classItem.subjectCount }} subjects</p>
              <div class="flex justify-between text-xs text-gray-500">
                <span>{{ classItem.studentCount }} students</span>
                <span>{{ classItem.teachers?.length || 0 }} teachers</span>
              </div>
            </div>

            <!-- Skill Courses Preview -->
            <div 
              v-for="course in otherCourses.slice(0, 3)" 
              :key="course.id"
              class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
              @click="viewClassDetails(course)"
            >
              <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                </div>
                <span 
                  :class="`px-2 py-1 text-xs rounded-full ${
                    course.status === 'active' 
                      ? 'bg-green-100 text-green-800' 
                      : course.status === 'inactive'
                      ? 'bg-red-100 text-red-800'
                      : 'bg-yellow-100 text-yellow-800'
                  }`"
                >
                  {{ course.status }}
                </span>
              </div>
              <h4 class="font-semibold text-gray-800 mb-1">{{ course.name }}</h4>
              <p class="text-sm text-gray-600 mb-2">{{ course.category }}</p>
              <div class="flex justify-between text-xs text-gray-500">
                <span>{{ course.studentCount }} students</span>
                <span>{{ course.teachers?.length || 0 }} teachers</span>
              </div>
            </div>
          </div>

          <div v-if="classes.length === 0 && !loading" class="text-center py-8">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-gray-600">No courses found</p>
            <button 
              @click="showCreateModal = true"
              class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
            >
              Create your first course
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600">Loading courses...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Course management state
const classes = ref([])
const activeTeachersCount = ref(0)
const loading = ref(true)
const error = ref('')
const successMessage = ref('')
const showCreateModal = ref(false)
const creating = ref(false)
const isDragOver = ref(false)
const selectedImageFile = ref(null)
const imageInput = ref(null)
const subjectImageInputs = ref([])

// Available grades 1-12
const availableGrades = Array.from({ length: 12 }, (_, i) => i + 1)

// Available categories for other courses
const availableCategories = [
  { value: 'life_skills', label: 'Life Skills' },
  { value: 'spoken_english', label: 'Spoken English' },
  { value: 'computer_basics', label: 'Computer Basics' },
  { value: 'art_craft', label: 'Art & Craft' },
  { value: 'music', label: 'Music' },
  { value: 'sports', label: 'Sports' },
  { value: 'dance', label: 'Dance' },
  { value: 'yoga', label: 'Yoga & Meditation' },
  { value: 'career_counseling', label: 'Career Counseling' },
  { value: 'other', label: 'Other' }
]

// New course form data
const newCourse = ref({
  type: 'regular',
  grade: '',
  name: '',
  category: '',
  courseCode: '',
  subjects: [
    { 
      name: '', 
      code: '',
      imageFile: null,
      isDragOver: false
    }
  ],
  capacity: 30,
  status: 'active',
  description: ''
})

// Handle search from navbar
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Computed properties
const regularClasses = computed(() => {
  return classes.value.filter(course => course.type === 'regular')
})

const otherCourses = computed(() => {
  return classes.value.filter(course => course.type === 'other')
})

const totalStudents = computed(() => {
  return classes.value.reduce((total, course) => total + (course.studentCount || 0), 0)
})

// Navigation functions
const viewRegularCourses = () => {
  router.visit('/admin/courses/regular-courses')
}

const viewSkillCourses = () => {
  router.visit('/admin/courses/skill-courses')
}

const viewClassDetails = (course) => {
  if (course.type === 'regular') {
    router.visit(`/admin/courses/class/${course.grade}/subjects`)
  } else {
    router.visit(`/admin/courses/course/${course.id}/details`)
  }
}

// Fetch active teachers count
const fetchActiveTeachersCount = async () => {
  try {
    const response = await apiClient.get('/users/other-users')
    
    if (response.data.success) {
      const teachers = response.data.data.filter(user => user.role === 'teacher')
      activeTeachersCount.value = teachers.length
    } else {
      console.error('Failed to fetch teachers count:', response.data.message)
      activeTeachersCount.value = classes.value.reduce((total, course) => total + (course.teachers?.length || 0), 0)
    }
  } catch (err) {
    console.error('Error fetching active teachers count:', err)
    activeTeachersCount.value = classes.value.reduce((total, course) => total + (course.teachers?.length || 0), 0)
  }
}

// Course management functions
const fetchClasses = async () => {
  try {
    loading.value = true
    error.value = ''
    
    const response = await apiClient.get('/courses/classes')
    
    if (response.data.success) {
      classes.value = response.data.data
      await fetchActiveTeachersCount()
    } else {
      error.value = response.data.message || 'Failed to fetch classes'
      await fetchActiveTeachersCount()
    }
  } catch (err) {
    console.error('Error fetching classes:', err)
    error.value = 'Failed to load courses. Please check if the server is running.'
    
    // Fallback to mock data
    classes.value = getMockClasses()
    activeTeachersCount.value = classes.value.reduce((total, course) => total + (course.teachers?.length || 0), 0)
  } finally {
    loading.value = false
  }
}

// Mock data function
const getMockClasses = () => {
  return [
    ...Array.from({ length: 12 }, (_, i) => ({
      id: i + 1,
      grade: i + 1,
      name: `Class ${i + 1}`,
      subjectCount: Math.floor(Math.random() * 8) + 5,
      studentCount: Math.floor(Math.random() * 40) + 20,
      teachers: [],
      status: 'active',
      type: 'regular'
    })),
    {
      id: 13,
      name: 'Life Skills',
      category: 'Life Skills',
      studentCount: 25,
      teachers: [],
      status: 'active',
      type: 'other'
    },
    {
      id: 14,
      name: 'Spoken English',
      category: 'Spoken English',
      studentCount: 30,
      teachers: [],
      status: 'active',
      type: 'other'
    },
    {
      id: 15,
      name: 'Computer Basics',
      category: 'Computer Basics',
      studentCount: 20,
      teachers: [],
      status: 'active',
      type: 'other'
    }
  ]
}

const createNewCourse = async () => {
  // Validation
  if (newCourse.value.type === 'regular') {
    if (!newCourse.value.grade || !newCourse.value.name) {
      error.value = 'Please fill in all required fields for regular class'
      return
    }
    const invalidSubjects = newCourse.value.subjects.filter(subject => !subject.name || !subject.code)
    if (invalidSubjects.length > 0) {
      error.value = 'Please fill in all subject names and codes'
      return
    }
  } else {
    if (!newCourse.value.category || !newCourse.value.name) {
      error.value = 'Please fill in all required fields for the course'
      return
    }
  }

  creating.value = true
  error.value = ''

  try {
    const formData = new FormData()
    formData.append('type', newCourse.value.type)
    formData.append('name', newCourse.value.name)
    formData.append('capacity', newCourse.value.capacity)
    formData.append('status', newCourse.value.status)
    formData.append('description', newCourse.value.description || '')

    if (newCourse.value.type === 'regular') {
      formData.append('grade', newCourse.value.grade)
      
      // Add subjects as array
      newCourse.value.subjects.forEach((subject, index) => {
        formData.append(`subjects[${index}][name]`, subject.name)
        formData.append(`subjects[${index}][code]`, subject.code)
        
        // Append subject image with simple key format
        if (subject.imageFile) {
          console.log(`📸 Adding subject ${index} image:`, subject.imageFile.name)
          formData.append(`subject_image_${index}`, subject.imageFile)
        }
      })
    } else {
      formData.append('category', newCourse.value.category)
      formData.append('courseCode', newCourse.value.courseCode || '')
      
      if (selectedImageFile.value) {
        formData.append('image', selectedImageFile.value)
      }
    }

    console.log('📦 FormData contents:')
    for (let [key, value] of formData.entries()) {
      console.log(`  ${key}:`, value instanceof File ? `${value.name} (${value.size} bytes)` : value)
    }

    // FIRST: Test with debug endpoint
    console.log('🔍 Testing with debug endpoint...')
    const debugResponse = await apiClient.post('/courses/debug-upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (debugResponse.data.success) {
      console.log('✅ Debug test successful:', debugResponse.data.data)
      
      // NOW: Try the actual creation
      console.log('🚀 Now attempting actual course creation...')
      const response = await apiClient.post('/courses/classes', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      if (response.data.success) {
        successMessage.value = 'Course created successfully!'
        console.log('✅ Course created successfully:', response.data.data)
        closeModal()
        await fetchClasses()
      } else {
        error.value = response.data.message || 'Failed to create course'
      }
    } else {
      error.value = 'Debug test failed: ' + debugResponse.data.message
    }

  } catch (err) {
    console.error('❌ Error creating course:', err)
    if (err.response?.status === 422) {
      error.value = 'Validation error: ' + JSON.stringify(err.response.data.errors)
    } else if (err.response?.data?.message) {
      error.value = 'Failed to create course: ' + err.response.data.message
    } else {
      error.value = 'Failed to create course: ' + (err.message || 'Unknown error')
    }
  } finally {
    creating.value = false
  }
}

const onCourseTypeChange = () => {
  if (newCourse.value.type === 'regular') {
    newCourse.value.category = ''
    newCourse.value.courseName = ''
    newCourse.value.courseCode = ''
    newCourse.value.subjects.forEach(subject => {
      subject.imageFile = null
    })
  } else {
    newCourse.value.grade = ''
    newCourse.value.subjects = [{ name: '', code: '' }]
    selectedImageFile.value = null
  }
}

const addSubject = () => {
  newCourse.value.subjects.push({ 
    name: '', 
    code: '',
    imageFile: null,
    isDragOver: false
  })
}

const removeSubject = (index) => {
  if (newCourse.value.subjects.length > 1) {
    newCourse.value.subjects.splice(index, 1)
  }
}

// Image handling functions
const triggerImageInput = () => {
  imageInput.value?.click()
}

const triggerSubjectImageInput = (index) => {
  const inputElement = subjectImageInputs.value[index]
  if (inputElement) {
    inputElement.click()
  }
}

const handleImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      error.value = 'Image size should be less than 5MB'
      return
    }
    if (!file.type.startsWith('image/')) {
      error.value = 'Please select a valid image file'
      return
    }
    selectedImageFile.value = file
  }
}

const handleSubjectImageSelect = (event, index) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      error.value = 'Image size should be less than 5MB'
      return
    }
    if (!file.type.startsWith('image/')) {
      error.value = 'Please select a valid image file'
      return
    }
    newCourse.value.subjects[index].imageFile = file
  }
}

const handleImageDrop = (event) => {
  event.preventDefault()
  isDragOver.value = false
  const files = event.dataTransfer.files
  if (files.length > 0) {
    const file = files[0]
    if (file.type.startsWith('image/')) {
      selectedImageFile.value = file
    } else {
      error.value = 'Please drop a valid image file'
    }
  }
}

const handleSubjectImageDrop = (event, index) => {
  event.preventDefault()
  newCourse.value.subjects[index].isDragOver = false
  const files = event.dataTransfer.files
  if (files.length > 0) {
    const file = files[0]
    if (file.type.startsWith('image/')) {
      newCourse.value.subjects[index].imageFile = file
    } else {
      error.value = 'Please drop a valid image file'
    }
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

const getSubjectImagePreview = (index) => {
  const subject = newCourse.value.subjects[index]
  if (subject.imageFile) {
    return URL.createObjectURL(subject.imageFile)
  }
  return ''
}

const removeImage = () => {
  selectedImageFile.value = null
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}

const removeSubjectImage = (index) => {
  newCourse.value.subjects[index].imageFile = null
  const inputElement = subjectImageInputs.value[index]
  if (inputElement) {
    inputElement.value = ''
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const closeModal = () => {
  showCreateModal.value = false
  resetForm()
}

const resetForm = () => {
  newCourse.value = {
    type: 'regular',
    grade: '',
    name: '',
    category: '',
    courseName: '',
    courseCode: '',
    subjects: [
      { 
        name: '', 
        code: '',
        imageFile: null,
        isDragOver: false
      }
    ],
    capacity: 30,
    status: 'active',
    description: ''
  }
  selectedImageFile.value = null
  subjectImageInputs.value = []
  error.value = ''
}

// Initialize when component mounts
onMounted(() => {
  fetchClasses()
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