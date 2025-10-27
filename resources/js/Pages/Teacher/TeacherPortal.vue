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
            <Link 
              href="/teacher"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $page.url === '/teacher' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              <span>Dashboard</span>
            </Link>
          </div>

          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">
            Teaching
          </p>

          <!-- My Classes -->
          <div>
            <button 
              @click="toggleMenu('classes')"
              class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': activeMenu === 'classes' }"
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
              <Link href="/teacher/classes" class="submenu-link">All Classes</Link>
              <Link href="/teacher/classes/schedule" class="submenu-link">Class Schedule</Link>
              <Link href="/teacher/classes/students" class="submenu-link">Student Roster</Link>
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
              <a href="#" class="submenu-link" @click.prevent="showUploadModal = true">Upload Resources</a>
              <Link href="/teacher/resources" class="submenu-link">My Resources</Link>
              <Link href="/teacher/resources/shared" class="submenu-link">Shared Resources</Link>
            </div>
          </div>

          <!-- Assessments -->
          <div>
            <button 
              @click="toggleMenu('assessments')"
              class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': activeMenu === 'assessments' }"
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
              <a href="#" class="submenu-link" @click.prevent="createAssignment">Create Assignment</a>
              <Link href="/teacher/assignments" class="submenu-link">Grade Assignments</Link>
              <Link href="/teacher/assignments/progress" class="submenu-link">Student Progress</Link>
            </div>
          </div>

          <!-- Analytics -->
          <div>
            <Link 
              href="/teacher/analytics"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $page.url === '/teacher/analytics' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              <span>Analytics</span>
            </Link>
          </div>

          <!-- Settings -->
          <div>
            <Link 
              href="/teacher/settings"
              class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
              :class="{ 'bg-blue-50 text-blue-700': $page.url === '/teacher/settings' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              <span>Settings</span>
            </Link>
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
              <h1 class="text-2xl font-semibold text-gray-800 truncate">Teacher Portal - {{ teacher.name || 'Loading...' }}</h1>
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
                    <p class="text-sm font-medium text-gray-700 truncate">{{ teacher.name || 'Teacher' }}</p>
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
        <!-- Main Content -->
        <div class="space-y-6">
          <!-- Teacher Profile Header -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div class="w-20 h-20 bg-purple-600 rounded-full flex items-center justify-center">
                  <span class="text-white text-2xl font-semibold">
                    {{ getUserInitials(teacher.name) }}
                  </span>
                </div>
                <div>
                  <h2 class="text-2xl font-bold text-gray-900">{{ teacher.name }}</h2>
                  <p class="text-gray-600">{{ teacher.email }}</p>
                  <p class="text-sm text-gray-500">@{{ teacher.username }}</p>
                </div>
              </div>
              <div class="flex space-x-3">
                <button 
                  @click="editProfile"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Edit Profile
                </button>
                <button 
                  @click="sendMessage"
                  class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Message
                </button>
              </div>
            </div>
          </div>

          <!-- Stats Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-sm font-medium text-gray-600 mb-2">Total Classes</p>
                  <h3 class="text-3xl font-bold text-blue-600">{{ teacherClasses.length }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-sm font-medium text-gray-600 mb-2">Total Students</p>
                  <h3 class="text-3xl font-bold text-green-600">{{ totalStudents }}</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-sm font-medium text-gray-600 mb-2">Resources</p>
                  <h3 class="text-3xl font-bold text-purple-600">{{ recentResources.length }}</h3>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Experience Stats Card -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-sm font-medium text-gray-600 mb-2">Experience</p>
                  <h3 class="text-3xl font-bold text-orange-600">
                    {{ formatExperience(teacher.experience) }}
                  </h3>
                  <p class="text-xs text-gray-500 mt-1">Teaching experience</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-lg">
                  <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Teacher Info -->
            <div class="lg:col-span-1 space-y-6">
              <!-- Teacher Information -->
              <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Teacher Information</h3>
                <div class="space-y-4">
                  <div>
                    <label class="text-sm font-medium text-gray-600">Experience</label>
                    <p class="text-gray-900">{{ formatExperience(teacher.experience) }} of teaching experience</p>
                    <p class="text-xs text-gray-500 mt-1">
                      <span v-if="getExperienceLevel(teacher.experience)" 
                            :class="`px-2 py-1 rounded-full text-xs ${getExperienceLevel(teacher.experience).color}`">
                        {{ getExperienceLevel(teacher.experience).level }}
                      </span>
                    </p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-600">Education</label>
                    <p class="text-gray-900">{{ teacher.education_qualification }} from {{ teacher.institute }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-600">Date of Birth</label>
                    <p class="text-gray-900">{{ formatDate(teacher.dob) }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-600">Join Date</label>
                    <p class="text-gray-900">{{ formatDate(teacher.created_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Quick Actions -->
              <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                  <button 
                    @click="showUploadModal = true"
                    class="w-full flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-left"
                  >
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span>Upload Resources</span>
                  </button>
                  <button 
                    @click="createAssignment"
                    class="w-full flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-left"
                  >
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Create Assignment</span>
                  </button>
                  <button 
                    @click="scheduleClass"
                    class="w-full flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-left"
                  >
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Schedule Class</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Right Column - Classes and Resources -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Classes Section -->
              <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-semibold text-gray-800">My Classes</h3>
                  <button class="text-blue-600 hover:text-blue-700 text-sm font-medium" @click="navigateToAllClasses">
                    View All
                  </button>
                </div>
                <div class="space-y-4">
                  <div 
                    v-for="classItem in teacherClasses" 
                    :key="classItem.id"
                    class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
                    @click="viewClass(classItem.id)"
                  >
                    <div class="flex justify-between items-start">
                      <div>
                        <h4 class="font-semibold text-gray-900">{{ classItem.name }}</h4>
                        <p class="text-sm text-gray-600">{{ classItem.subject }} • {{ classItem.studentCount }} students</p>
                        <p class="text-xs text-gray-500 mt-1">{{ classItem.schedule }}</p>
                      </div>
                      <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getClassStatusColor(classItem.status)}`">
                        {{ classItem.status }}
                      </span>
                    </div>
                  </div>
                  <div v-if="teacherClasses.length === 0" class="text-center py-8 text-gray-500">
                    No classes assigned yet.
                  </div>
                </div>
              </div>

              <!-- Recent Resources -->
              <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-semibold text-gray-800">Recent Resources</h3>
                  <button class="text-blue-600 hover:text-blue-700 text-sm font-medium" @click="navigateToMyResources">
                    View All
                  </button>
                </div>
                <div class="space-y-3">
                  <div 
                    v-for="resource in recentResources" 
                    :key="resource.id"
                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg"
                  >
                    <div class="flex items-center space-x-3">
                      <div :class="`p-2 rounded-lg ${getResourceTypeColor(resource.type)}`">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="resource.type === 'video'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                      </div>
                      <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ resource.title }}</h4>
                        <p class="text-xs text-gray-500">{{ resource.class }} • {{ formatDate(resource.uploaded_at) }}</p>
                      </div>
                    </div>
                    <span class="text-xs text-gray-500 capitalize">{{ resource.type }}</span>
                  </div>
                  <div v-if="recentResources.length === 0" class="text-center py-8 text-gray-500">
                    No resources uploaded yet.
                  </div>
                </div>
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

        <!-- Assigned Class -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Class</label>
          <select 
            v-model="newResource.assigned_class"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Classes</option>
            <option v-for="classItem in teacherClasses" :key="classItem.id" :value="classItem.id">
              {{ classItem.name }}
            </option>
          </select>
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

  <!-- Edit Profile Modal -->
  <div v-if="showEditProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Edit Profile</h3>
      </div>

      <form @submit.prevent="saveProfile" class="p-6 space-y-4">
        <!-- Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
          <input 
            v-model="profileForm.name"
            type="text" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter your full name"
            :disabled="savingProfile"
          >
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
          <input 
            v-model="profileForm.email"
            type="email" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter your email"
            :disabled="savingProfile"
          >
        </div>

        <!-- Username -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
          <input 
            v-model="profileForm.username"
            type="text" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter your username"
            :disabled="savingProfile"
          >
        </div>

        <!-- Date of Birth -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
          <input 
            v-model="profileForm.dob"
            type="date" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            :disabled="savingProfile"
          >
        </div>

        <!-- Education Qualification -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Education Qualification *</label>
          <select 
            v-model="profileForm.education_qualification"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            :disabled="savingProfile"
          >
            <option value="">Select qualification</option>
            <option value="HSC">HSC</option>
            <option value="BSC">BSC</option>
            <option value="BA">BA</option>
            <option value="MA">MA</option>
            <option value="MSC">MSC</option>
            <option value="PhD">PhD</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <!-- Institute -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Institute *</label>
          <input 
            v-model="profileForm.institute"
            type="text" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="e.g., University of Technology"
            :disabled="savingProfile"
          >
        </div>

        <!-- Experience -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Experience (years)</label>
          <input 
            v-model="profileForm.experience"
            type="number" 
            min="0"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter years of experience"
            :disabled="savingProfile"
          >
        </div>

        <!-- Bio -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
          <textarea 
            v-model="profileForm.bio"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Tell us about yourself..."
            :disabled="savingProfile"
          ></textarea>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button 
            type="button"
            @click="showEditProfileModal = false"
            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            :disabled="savingProfile"
          >
            Cancel
          </button>
          <button 
            type="submit"
            :disabled="savingProfile"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center"
          >
            <svg v-if="savingProfile" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ savingProfile ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Message Modal -->
  <div v-if="showMessageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Send Message</h3>
      </div>

      <form @submit.prevent="sendMessageToAdmin" class="p-6 space-y-4">
        <!-- Recipient -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
          <select 
            v-model="messageForm.recipient"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Select recipient</option>
            <option value="admin">School Administrator</option>
            <option value="principal">Principal</option>
            <option value="support">Technical Support</option>
          </select>
        </div>

        <!-- Subject -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
          <input 
            v-model="messageForm.subject"
            type="text" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter message subject"
          >
        </div>

        <!-- Message -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
          <textarea 
            v-model="messageForm.message"
            rows="6"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Type your message here..."
          ></textarea>
        </div>

        <!-- Priority -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
          <select 
            v-model="messageForm.priority"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="normal">Normal</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button 
            type="button"
            @click="showMessageModal = false"
            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button 
            type="submit"
            :disabled="sendingMessage"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            {{ sendingMessage ? 'Sending...' : 'Send Message' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

// Props from Laravel
const props = defineProps({
  teacher: Object,
  teacherClasses: Array,
  recentResources: Array,
})

// UI State
const activeMenu = ref('')
const userMenuOpen = ref(false)
const isDark = ref(false)
const showUploadModal = ref(false)
const showEditProfileModal = ref(false)
const showMessageModal = ref(false)
const uploading = ref(false)
const savingProfile = ref(false)
const sendingMessage = ref(false)

// Forms
const newResource = ref({
  type: '',
  title: '',
  description: '',
  content: '',
  assigned_class: '',
  file: null
})

const profileForm = ref({
  name: '',
  email: '',
  username: '',
  dob: '',
  education_qualification: '',
  institute: '',
  experience: '',
  bio: ''
})

const messageForm = ref({
  recipient: '',
  subject: '',
  message: '',
  priority: 'normal'
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

const totalStudents = computed(() => {
  return props.teacherClasses.reduce((sum, classItem) => sum + (classItem.studentCount || 0), 0)
})

// Helper Functions
const getUserInitials = (name) => {
  if (!name) return 'T'
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const getClassStatusColor = (status) => {
  const colors = {
    'Active': 'bg-green-100 text-green-800',
    'Inactive': 'bg-gray-100 text-gray-800',
    'Upcoming': 'bg-blue-100 text-blue-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getResourceTypeColor = (type) => {
  const colors = {
    'video': 'bg-red-500',
    'pdf': 'bg-red-600',
    'document': 'bg-blue-500',
    'link': 'bg-green-500'
  }
  return colors[type] || 'bg-gray-500'
}

const formatExperience = (experience) => {
  if (!experience) return '0 years'
  if (typeof experience === 'string') {
    if (experience.includes('years') || experience.includes('year')) {
      return experience
    }
    if (!isNaN(experience)) {
      return `${experience} ${experience === '1' ? 'year' : 'years'}`
    }
  }
  if (typeof experience === 'number') {
    return `${experience} ${experience === 1 ? 'year' : 'years'}`
  }
  return experience || 'Not specified'
}

const getExperienceLevel = (experience) => {
  if (!experience) return null
  let years = 0
  if (typeof experience === 'string') {
    const match = experience.match(/(\d+)/)
    years = match ? parseInt(match[1]) : 0
  } else if (typeof experience === 'number') {
    years = experience
  }
  if (years >= 15) {
    return { level: 'Expert', color: 'bg-purple-100 text-purple-800' }
  } else if (years >= 10) {
    return { level: 'Senior', color: 'bg-blue-100 text-blue-800' }
  } else if (years >= 5) {
    return { level: 'Experienced', color: 'bg-green-100 text-green-800' }
  } else if (years >= 2) {
    return { level: 'Intermediate', color: 'bg-yellow-100 text-yellow-800' }
  } else {
    return { level: 'Beginner', color: 'bg-gray-100 text-gray-800' }
  }
}

// Navigation Methods
const navigateToAllClasses = () => {
  router.visit('/teacher/classes')
}

const navigateToClassSchedule = () => {
  router.visit('/teacher/classes/schedule')
}

const navigateToStudentRoster = () => {
  router.visit('/teacher/classes/students')
}

const navigateToMyResources = () => {
  router.visit('/teacher/resources')
}

const navigateToSharedResources = () => {
  router.visit('/teacher/resources/shared')
}

const navigateToGradeAssignments = () => {
  router.visit('/teacher/assignments')
}

const navigateToStudentProgress = () => {
  router.visit('/teacher/assignments/progress')
}

const navigateToSettings = () => {
  router.visit('/teacher/settings')
}

const viewClass = (classId) => {
  router.visit(`/teacher/class/${classId}`)
}

const createAssignment = () => {
  if (props.teacherClasses.length > 0) {
    router.visit(`/teacher/class/${props.teacherClasses[0].id}/assignments/create`)
  } else {
    router.visit('/teacher/assignments/create')
  }
}

const editProfile = () => {
  profileForm.value = {
    name: props.teacher?.name || '',
    email: props.teacher?.email || '',
    username: props.teacher?.username || '',
    dob: props.teacher?.dob || '',
    education_qualification: props.teacher?.education_qualification || '',
    institute: props.teacher?.institute || '',
    experience: props.teacher?.experience || '',
    bio: props.teacher?.bio || ''
  }
  showEditProfileModal.value = true
}

const sendMessage = () => {
  showMessageModal.value = true
}

const scheduleClass = () => {
  if (props.teacherClasses.length > 0) {
    router.visit(`/teacher/class/${props.teacherClasses[0].id}/schedule`)
  } else {
    alert('No classes available to schedule.')
  }
}

const goBackToAdmin = () => {
  router.visit('/admin/users/other-users')
}

const logout = async () => {
  try {
    router.post('/logout')
  } catch (err) {
    console.error('Logout error:', err)
  }
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
    assigned_class: '',
    file: null
  }
}

const saveProfile = async () => {
  savingProfile.value = true
  // Simulate save
  setTimeout(() => {
    savingProfile.value = false
    showEditProfileModal.value = false
    alert('Profile updated successfully!')
  }, 1500)
}

const sendMessageToAdmin = async () => {
  sendingMessage.value = true
  // Simulate send
  setTimeout(() => {
    sendingMessage.value = false
    showMessageModal.value = false
    resetMessageForm()
    alert('Message sent successfully!')
  }, 1500)
}

const resetMessageForm = () => {
  messageForm.value = {
    recipient: '',
    subject: '',
    message: '',
    priority: 'normal'
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