<template>
  <aside class="w-64 bg-white border-r border-gray-200 fixed h-screen overflow-y-auto flex flex-col justify-between px-4 py-6">
    <!-- Top: Logo + Navigation -->
    <div>
      <!-- Phoenix Logo -->
      <div class="flex items-center space-x-2 mb-6 px-2">
        <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-sm">LMS</span>
        </div>
        <span class="text-2xl font-semibold text-gray-600">Teacher Portal</span>
      </div>

      <!-- Sidebar Navigation -->
      <nav class="space-y-1">
        <!-- Dashboard -->
        <div>
          <Link 
            href="/teacher"
            class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline"
            :class="{ 'bg-blue-50 text-blue-700': $page.url === '/teacher' }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
          </Link>
        </div>

        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-2">
          Teaching
        </p>

        <!-- My Classes -->
        <div>
          <button 
            @click="toggleMenu('classes')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline"
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
          <div v-show="activeMenu === 'classes'" class="ml-8 mt-1 space-y-0.5">
            <Link href="/teacher/classes" class="submenu-link">All Classes</Link>
            <Link href="/teacher/classes/schedule" class="submenu-link">Class Schedule</Link>
            <Link href="/teacher/classes/students" class="submenu-link">Student Roster</Link>
          </div>
        </div>

        <!-- Resources -->
        <div>
          <button 
            @click="toggleMenu('resources')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline"
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
          <div v-show="activeMenu === 'resources'" class="ml-8 mt-1 space-y-0.5">
            <a href="#" class="submenu-link" @click.prevent="$emit('showUploadModal')">Upload Resources</a>
            <Link href="/teacher/resources" class="submenu-link">My Resources</Link>
            <Link href="/teacher/resources/shared" class="submenu-link">Shared Resources</Link>
          </div>
        </div>

        <!-- Assessments -->
        <div>
          <button 
            @click="toggleMenu('assessments')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline"
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
          <div v-show="activeMenu === 'assessments'" class="ml-8 mt-1 space-y-0.5">
            <a href="#" class="submenu-link" @click.prevent="$emit('createAssignment')">Create Assignment</a>
            <Link href="/teacher/assignments" class="submenu-link">Grade Assignments</Link>
            <Link href="/teacher/assignments/progress" class="submenu-link">Student Progress</Link>
          </div>
        </div>

        <!-- Analytics -->
        <div>
          <Link 
            href="/teacher/analytics"
            class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline"
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
            class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline"
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
    <div class="text-sm text-indigo-600 font-medium cursor-pointer px-2 transition-colors hover:text-indigo-700" @click="$emit('goBackToAdmin')">
      ← Back to Admin
    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

// Define emits
const emit = defineEmits(['showUploadModal', 'createAssignment', 'goBackToAdmin'])

// UI State
const activeMenu = ref('')

// Methods
const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu
}
</script>

<style scoped>
.rotate-180 {
  transform: rotate(180deg);
}

.submenu-link {
  display: block;
  padding: 0.35rem 0.75rem;
  color: #4b5563;
  border-radius: 0.375rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
  text-decoration: none;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
  text-decoration: none;
}

/* Remove underline from all links and buttons */
.no-underline {
  text-decoration: none !important;
}

/* Ensure no underline appears on hover for any element */
button:hover,
a:hover {
  text-decoration: none !important;
}
</style>