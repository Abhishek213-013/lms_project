<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 fixed h-screen overflow-y-auto flex flex-col justify-between px-4 py-6">
      <!-- Top: Logo + Navigation -->
      <div>
        <!-- Phoenix Logo -->
        <div class="flex items-center space-x-2 mb-8 px-2">
          <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">LMS</span>
          </div>
          <span class="text-2xl font-semibold text-gray-600">Teacher Portal</span>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="space-y-5">
          <!-- Dashboard -->
          <div>
            <router-link 
              to="/admin/teacher-portal"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $route.path === '/admin/teacher-portal' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              <span>Dashboard</span>
            </router-link>
          </div>

          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">
            Teaching
          </p>

          <!-- My Classes -->
          <div>
            <button 
              @click="toggleMenu('classes')"
              class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>My Classes</span>
              </div>
              <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': activeMenu === 'classes' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div v-show="activeMenu === 'classes'" class="ml-8 mt-2 space-y-1">
              <a href="#" class="submenu-link" @click="navigateToAllClasses">All Classes</a>
              <a href="#" class="submenu-link" @click="navigateToClassSchedule">Class Schedule</a>
              <a href="#" class="submenu-link" @click="navigateToStudentRoster">Student Roster</a>
            </div>
          </div>

          <!-- Resources -->
          <div>
            <button 
              @click="toggleMenu('resources')"
              class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Resources</span>
              </div>
              <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': activeMenu === 'resources' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div v-show="activeMenu === 'resources'" class="ml-8 mt-2 space-y-1">
              <a href="#" class="submenu-link" @click="showUploadModal = true">Upload Resources</a>
              <a href="#" class="submenu-link" @click="navigateToMyResources">My Resources</a>
              <a href="#" class="submenu-link" @click="navigateToSharedResources">Shared Resources</a>
            </div>
          </div>

          <!-- Assessments -->
          <div>
            <button 
              @click="toggleMenu('assessments')"
              class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span>Assessments</span>
              </div>
              <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': activeMenu === 'assessments' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div v-show="activeMenu === 'assessments'" class="ml-8 mt-2 space-y-1">
              <a href="#" class="submenu-link" @click="createAssignment">Create Assignment</a>
              <a href="#" class="submenu-link" @click="navigateToGradeAssignments">Grade Assignments</a>
              <a href="#" class="submenu-link" @click="navigateToStudentProgress">Student Progress</a>
            </div>
          </div>

          <!-- Analytics -->
          <div>
            <router-link 
              to="/admin/teacher-portal/analytics"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $route.path === '/admin/teacher-portal/analytics' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              <span>Analytics</span>
            </router-link>
          </div>

          <!-- Settings -->
          <div>
            <router-link 
              to="/admin/teacher-portal/settings"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $route.path === '/admin/teacher-portal/settings' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              <span>Settings</span>
            </router-link>
          </div>
        </nav>
      </div>

      <!-- Bottom: Back to Admin -->
      <div class="text-sm text-indigo-600 font-medium cursor-pointer hover:underline px-2" @click="goBackToAdmin">
        ← Back to Admin
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 ml-64 min-w-0">
      <!-- Top Navbar -->
      <nav class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center min-w-0">
              <h1 class="text-2xl font-semibold text-gray-800 truncate">Assignments - {{ classData.name }}</h1>
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
              
              <!-- Theme Toggle -->
              <button
                @click="toggleTheme"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-orange-50 hover:bg-orange-100 transition flex-shrink-0"
              >
                <svg
                  v-if="!isDark"
                  class="w-5 h-5 text-orange-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 3v1m0 16v1m8.66-9h-1M4.34 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.02 0l-.7.7M6.34 17.66l-.7.7M12 8a4 4 0 100 8 4 4 0 000-8z"
                  />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5 text-indigo-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"
                  />
                </svg>
              </button>

              <!-- Notifications -->
              <button class="relative p-2 text-gray-600 hover:text-blue-600 rounded-lg hover:bg-gray-100 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 010 11.25"></path>
                </svg>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
              </button>

              <!-- User Menu -->
              <div class="relative flex-shrink-0">
                <button 
                  @click="toggleUserMenu"
                  class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 min-w-0"
                >
                  <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-semibold">{{ userInitials }}</span>
                  </div>
                  <div class="text-left min-w-0 hidden md:block">
                    <p class="text-sm font-medium text-gray-700 truncate">{{ currentUser?.name || 'Teacher' }}</p>
                    <p class="text-xs text-gray-500 capitalize truncate">Teacher</p>
                  </div>
                  <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>

                <!-- User Dropdown -->
                <div v-show="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
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
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Assignments - {{ classData.name }}</h1>
            <p class="text-gray-600">Create and manage assignments for your students</p>
          </div>
          <div class="flex space-x-3">
            <button 
              @click="$router.back()"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium"
            >
              ← Back to Class
            </button>
            <button 
              @click="showCreateModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              Create Assignment
            </button>
          </div>
        </div>

        <!-- Assignments List -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">All Assignments</h3>
          </div>
          
          <div class="divide-y divide-gray-200">
            <div 
              v-for="assignment in assignments" 
              :key="assignment.id"
              class="p-6 hover:bg-gray-50 transition-colors"
            >
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-2">
                    <h4 class="text-lg font-semibold text-gray-900">{{ assignment.title }}</h4>
                    <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(assignment.status)}`">
                      {{ assignment.status }}
                    </span>
                  </div>
                  <p class="text-gray-600 mb-3">{{ assignment.description }}</p>
                  
                  <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      <span>Due: {{ formatDate(assignment.due_date) }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      <span>{{ assignment.submitted_count }}/{{ classData.studentCount }} submitted</span>
                    </div>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span>Points: {{ assignment.points }}</span>
                    </div>
                  </div>
                </div>
                
                <div class="flex space-x-2">
                  <button 
                    @click="viewSubmissions(assignment.id)"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                  >
                    View Submissions
                  </button>
                  <button 
                    @click="editAssignment(assignment)"
                    class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                  >
                    Edit
                  </button>
                  <button 
                    @click="deleteAssignment(assignment.id)"
                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                  >
                    Delete
                  </button>
                </div>
              </div>
              
              <!-- Assignment Resources -->
              <div v-if="assignment.attachments && assignment.attachments.length > 0" class="mt-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Attachments:</p>
                <div class="flex space-x-2">
                  <div 
                    v-for="attachment in assignment.attachments" 
                    :key="attachment.id"
                    class="flex items-center space-x-1 px-3 py-1 bg-gray-100 rounded-lg text-sm text-gray-600"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <span>{{ attachment.name }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="assignments.length === 0" class="p-12 text-center text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              <p class="text-lg font-medium mb-2">No assignments yet</p>
              <p class="text-sm mb-4">Create your first assignment to get started</p>
              <button 
                @click="showCreateModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
              >
                Create Assignment
              </button>
            </div>
          </div>
        </div>

        <!-- Create/Edit Assignment Modal -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">
                {{ showEditModal ? 'Edit Assignment' : 'Create New Assignment' }}
              </h3>
            </div>

            <form @submit.prevent="saveAssignment" class="p-6 space-y-4">
              <!-- Assignment Title -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Title *</label>
                <input 
                  v-model="assignmentForm.title"
                  type="text" 
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter assignment title"
                >
              </div>

              <!-- Assignment Description -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                <textarea 
                  v-model="assignmentForm.description"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Provide instructions for the assignment..."
                ></textarea>
              </div>

              <!-- Due Date and Points -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Due Date *</label>
                  <input 
                    v-model="assignmentForm.due_date"
                    type="datetime-local" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                  <input 
                    v-model="assignmentForm.points"
                    type="number" 
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="100"
                  >
                </div>
              </div>

              <!-- Attachments -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                  <input 
                    type="file"
                    @change="handleFileUpload"
                    multiple
                    class="hidden"
                    ref="fileInput"
                    id="fileInput"
                  >
                  <label for="fileInput" class="cursor-pointer">
                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-sm text-gray-600">Click to upload files or drag and drop</p>
                    <p class="text-xs text-gray-500">PDF, DOC, PPT, images up to 10MB each</p>
                  </label>
                </div>
                
                <!-- Uploaded files preview -->
                <div v-if="assignmentForm.attachments.length > 0" class="mt-3 space-y-2">
                  <div 
                    v-for="(file, index) in assignmentForm.attachments" 
                    :key="index"
                    class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"
                  >
                    <div class="flex items-center space-x-2">
                      <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                      </svg>
                      <span class="text-sm text-gray-700">{{ file.name }}</span>
                    </div>
                    <button 
                      @click="removeAttachment(index)"
                      type="button"
                      class="text-red-500 hover:text-red-700"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>
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
                  :disabled="saving"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ saving ? 'Saving...' : (showEditModal ? 'Update Assignment' : 'Create Assignment') }}
                </button>
              </div>
            </form>
          </div>
        </div>
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

// Sidebar and navbar state
const activeMenu = ref('')
const userMenuOpen = ref(false)
const isDark = ref(false)

// Current user data
const currentUser = computed(() => {
  return JSON.parse(localStorage.getItem('user') || '{}')
})

// User initials for profile picture
const userInitials = computed(() => {
  if (!currentUser.value?.name) return 'T'
  return currentUser.value.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

// Original component state
const classData = ref({
  id: route.params.classId,
  name: 'Loading...',
  studentCount: 0
})

const assignments = ref([])
const showCreateModal = ref(false)
const showEditModal = ref(false)
const saving = ref(false)
const loading = ref(true)

const assignmentForm = ref({
  id: null,
  title: '',
  description: '',
  due_date: '',
  points: 100,
  attachments: []
})

const fileInput = ref(null)

// ==================== SIDEBAR NAVIGATION METHODS ====================

// My Classes submenu
const navigateToAllClasses = () => {
  router.push('/admin/teacher-portal')
}

const navigateToClassSchedule = () => {
  if (classData.value.id) {
    router.push(`/teacher/class/${classData.value.id}/schedule`)
  } else {
    alert('No class selected.')
  }
}

const navigateToStudentRoster = () => {
  if (classData.value.id) {
    alert(`Showing student roster for ${classData.value.name}`)
  } else {
    alert('No class selected to view student roster.')
  }
}

// Resources submenu
const navigateToMyResources = () => {
  if (classData.value.id) {
    router.push(`/teacher/class/${classData.value.id}/resources`)
  } else {
    alert('No class selected to view resources.')
  }
}

const navigateToSharedResources = () => {
  alert('Navigating to shared resources page...')
}

// Assessments submenu
const navigateToGradeAssignments = () => {
  if (classData.value.id) {
    alert(`Navigating to grade assignments for ${classData.value.name}`)
  } else {
    alert('No class selected to grade assignments.')
  }
}

const navigateToStudentProgress = () => {
  if (classData.value.id) {
    alert(`Showing student progress for ${classData.value.name}`)
  } else {
    alert('No class selected to view student progress.')
  }
}

// Settings navigation
const navigateToSettings = () => {
  router.push('/admin/teacher-portal/settings')
}

// ==================== NAVBAR METHODS ====================

const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
}

const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const goBackToAdmin = () => {
  router.push('/admin/users/other-users')
}

const editProfile = () => {
  alert('Edit profile functionality would open here')
}

const logout = async () => {
  try {
    const token = localStorage.getItem('token')
    await apiClient.post('/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// ==================== ORIGINAL ASSIGNMENT METHODS ====================

// Fetch assignments from API
const fetchAssignments = async () => {
  try {
    loading.value = true
    const response = await apiClient.get(`/assignments/class/${classData.value.id}`)
    
    if (response.data.success) {
      assignments.value = response.data.data
    } else {
      console.error('Failed to fetch assignments:', response.data.message)
    }
  } catch (error) {
    console.error('Error fetching assignments:', error)
  } finally {
    loading.value = false
  }
}

// Fetch class data
const fetchClassData = async () => {
  try {
    const response = await apiClient.get(`/courses/${route.params.classId}`)
    if (response.data.success) {
      classData.value = {
        id: response.data.data.id,
        name: response.data.data.name,
        studentCount: response.data.data.studentCount || 0
      }
    }
  } catch (error) {
    console.error('Error fetching class data:', error)
  }
}

// Save assignment to API
const saveAssignment = async () => {
  saving.value = true
  try {
    const formData = {
      title: assignmentForm.value.title,
      description: assignmentForm.value.description,
      points: assignmentForm.value.points,
      due_date: assignmentForm.value.due_date,
      attachments: assignmentForm.value.attachments
    }

    let response
    if (showEditModal.value) {
      response = await apiClient.put(`/assignments/${assignmentForm.value.id}`, formData)
    } else {
      response = await apiClient.post(`/assignments/class/${classData.value.id}`, formData)
    }

    if (response.data.success) {
      await fetchAssignments() // Refresh the list
      closeModal()
    } else {
      alert('Failed to save assignment: ' + response.data.message)
    }
  } catch (error) {
    console.error('Error saving assignment:', error)
    alert('Error saving assignment. Please try again.')
  } finally {
    saving.value = false
  }
}

// Delete assignment from API
const deleteAssignment = async (assignmentId) => {
  if (confirm('Are you sure you want to delete this assignment?')) {
    try {
      const response = await apiClient.delete(`/assignments/${assignmentId}`)
      if (response.data.success) {
        await fetchAssignments() // Refresh the list
      } else {
        alert('Failed to delete assignment: ' + response.data.message)
      }
    } catch (error) {
      console.error('Error deleting assignment:', error)
      alert('Error deleting assignment. Please try again.')
    }
  }
}

// Handle file upload
const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    assignmentForm.value.attachments.push({
      id: Date.now() + Math.random(),
      name: file.name,
      file: file
    })
  })
  event.target.value = '' // Reset file input
}

// Remove attachment
const removeAttachment = (index) => {
  assignmentForm.value.attachments.splice(index, 1)
}

// Edit assignment
const editAssignment = (assignment) => {
  assignmentForm.value = { 
    ...assignment,
    due_date: assignment.due_date.slice(0, 16) // Convert to datetime-local format
  }
  showEditModal.value = true
}

// Close modal and reset form
const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  resetForm()
}

// Reset form
const resetForm = () => {
  assignmentForm.value = {
    id: null,
    title: '',
    description: '',
    due_date: '',
    points: 100,
    attachments: []
  }
}

// View submissions (placeholder)
const viewSubmissions = (assignmentId) => {
  console.log('View submissions for assignment:', assignmentId)
  // You can implement this later
}

// Helper methods
const getStatusColor = (status) => {
  const colors = {
    active: 'bg-green-100 text-green-800',
    draft: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-blue-100 text-blue-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Initialize
onMounted(async () => {
  // Add click outside listener for user menu
  document.addEventListener('click', handleClickOutside)
  
  await fetchClassData()
  await fetchAssignments()
})
</script>

<style scoped>
.rotate-180 {
  transform: rotate(180deg);
}

.submenu-link {
  display: block;
  padding: 0.5rem 0.75rem;
  color: #4b5563;
  border-radius: 0.5rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
}
</style>