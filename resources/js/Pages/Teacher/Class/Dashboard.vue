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
          <span class="text-2xl font-semibold text-gray-600">Class Portal</span>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="space-y-5">
          <!-- Class Dashboard -->
          <div>
            <Link 
              :href="`/teacher/class/${classData?.id}`"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $page.url === `/teacher/class/${classData?.id}` }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              <span>Class Dashboard</span>
            </Link>
          </div>

          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">
            Class Management
          </p>

          <!-- Assignments -->
          <div>
            <button 
              @click="toggleMenu('assignments')"
              class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': activeMenu === 'assignments' }"
            >
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span>Assignments</span>
              </div>
              <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': activeMenu === 'assignments' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div v-show="activeMenu === 'assignments'" class="ml-8 mt-2 space-y-1">
              <Link :href="`/teacher/class/${classData?.id}/assignments`" class="submenu-link">All Assignments</Link>
              <Link :href="`/teacher/class/${classData?.id}/assignments/create`" class="submenu-link">Create Assignment</Link>
              <Link :href="`/teacher/class/${classData?.id}/assignments/grading`" class="submenu-link">Grade Assignments</Link>
            </div>
          </div>

          <!-- Resources -->
          <div>
            <button 
              @click="toggleMenu('resources')"
              class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': activeMenu === 'resources' }"
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
              <Link :href="`/teacher/class/${classData?.id}/resources`" class="submenu-link">All Resources</Link>
              <a href="#" class="submenu-link" @click.prevent="showUploadModal = true">Upload Resources</a>
              <Link :href="`/teacher/class/${classData?.id}/resources/shared`" class="submenu-link">Shared Resources</Link>
            </div>
          </div>

          <!-- Schedule -->
          <div>
            <button 
              @click="toggleMenu('schedule')"
              class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': activeMenu === 'schedule' }"
            >
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Schedule</span>
              </div>
              <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': activeMenu === 'schedule' }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div v-show="activeMenu === 'schedule'" class="ml-8 mt-2 space-y-1">
              <Link :href="`/teacher/class/${classData?.id}/schedule`" class="submenu-link">Class Schedule</Link>
              <Link :href="`/teacher/class/${classData?.id}/schedule/create`" class="submenu-link">Add Schedule</Link>
              <Link :href="`/teacher/class/${classData?.id}/calendar`" class="submenu-link">Calendar View</Link>
            </div>
          </div>

          <!-- Students -->
          <div>
            <Link 
              :href="`/teacher/class/${classData?.id}/students`"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $page.url === `/teacher/class/${classData?.id}/students` }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span>Students</span>
            </Link>
          </div>

          <!-- Analytics -->
          <div>
            <Link 
              :href="`/teacher/class/${classData?.id}/analytics`"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $page.url === `/teacher/class/${classData?.id}/analytics` }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              <span>Analytics</span>
            </Link>
          </div>
        </nav>
      </div>

      <!-- Bottom: Back to Teacher Portal -->
      <div class="text-sm text-indigo-600 font-medium cursor-pointer hover:underline px-2" @click="goBackToTeacherPortal">
        ← Back to Teacher Portal
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 ml-64 min-w-0">
      <!-- Top Navbar -->
      <nav class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center min-w-0">
              <h1 class="text-2xl font-semibold text-gray-800 truncate">{{ classData?.name || 'Class' }} - Class Dashboard</h1>
            </div>
            
            <div class="flex items-center space-x-4 flex-shrink-0">
              <!-- Search -->
              <div class="relative hidden md:block">
                <input 
                  type="text" 
                  placeholder="Search class..." 
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
                    <p class="text-sm font-medium text-gray-700 truncate">{{ teacher?.name || 'Teacher' }}</p>
                    <p class="text-xs text-gray-500 capitalize truncate">Class Teacher</p>
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
              class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center space-x-1"
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
            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button 
            type="submit"
            :disabled="uploading"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            {{ uploading ? 'Uploading...' : 'Upload Resource' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

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
      id: null
    })
  }
})

// UI State
const activeMenu = ref('')
const userMenuOpen = ref(false)
const isDark = ref(false)
const showUploadModal = ref(false)
const uploading = ref(false)

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
    router.visit(`/admin/teacher-portal/${teacherId}`)
  } else {
    router.visit('/teacher')
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

// UI Methods
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

const editProfile = () => {
  router.visit('/teacher/settings')
}

const logout = async () => {
  try {
    await router.post('/logout')
  } catch (err) {
    console.error('Logout error:', err)
  }
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
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