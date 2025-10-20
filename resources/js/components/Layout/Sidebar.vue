<template>
  <aside class="w-64 bg-white border-r border-gray-200 fixed h-screen overflow-y-auto flex flex-col justify-between px-4 py-4">
    <!-- Top: Logo + Navigation -->
    <div>
      <!-- Phoenix Logo -->
      <div class="flex items-center space-x-2 mb-6 px-2">
        <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-sm">LMS</span>
        </div>
        <span class="text-xl font-semibold text-gray-600">{{ isSuperAdmin ? 'Super Admin' : 'Admin' }}</span>
      </div>

      <!-- Sidebar Navigation -->
      <nav class="space-y-4">
        <!-- Dashboard -->
        <div>
          <button 
            @click="toggleMenu('dashboard')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <div class="flex items-center">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              <span class="text-sm">Dashboard</span>
            </div>
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': activeMenu === 'dashboard' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="activeMenu === 'dashboard'" class="ml-8 mt-1 space-y-1">
            <a href="#" class="submenu-link text-sm">Overview</a>
            <a href="#" class="submenu-link text-sm">Analytics</a>
          </div>
        </div>

        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">
          Management
        </p>

        <!-- User Management -->
        <div>
          <button 
            @click="toggleMenu('users')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <div class="flex items-center">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <span class="text-sm">Manage Users</span>
            </div>
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': activeMenu === 'users' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="activeMenu === 'users'" class="ml-8 mt-1 space-y-1">
            <router-link to="/admin/users/super-admins" class="submenu-link text-sm">Super Admins</router-link>
            <router-link to="/admin/users/admins" class="submenu-link text-sm">Admins</router-link>
            <router-link to="/admin/users/other-users" class="submenu-link text-sm">Teachers</router-link>
          </div>
        </div>

        <!-- Course Management -->
        <div>
          <button 
            @click="toggleMenu('courses')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <div class="flex items-center">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <span class="text-sm">Courses</span>
            </div>
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': activeMenu === 'courses' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="activeMenu === 'courses'" class="ml-8 mt-1 space-y-1">
            <router-link to="/admin/courses/all-courses" class="submenu-link text-sm">All Courses</router-link>
            <router-link to="/admin/courses/categories" class="submenu-link text-sm">Course Categories</router-link>
            <router-link to="/admin/courses/enrollments" class="submenu-link text-sm">Enrollments</router-link>
            <router-link to="/admin/courses/reviews" class="submenu-link text-sm">Course Reviews</router-link>
          </div>
        </div>

        <!-- Admissions -->
        <div>
          <button 
            @click="toggleMenu('admissions')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <div class="flex items-center">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              <span class="text-sm">Admissions</span>
            </div>
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': activeMenu === 'admissions' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="activeMenu === 'admissions'" class="ml-8 mt-1 space-y-1">
            <router-link to="/admin/admissions/applications" class="submenu-link text-sm">Applications</router-link>
            <router-link to="/admin/admissions/approvals" class="submenu-link text-sm">Approvals</router-link>
            <router-link to="/admin/admissions/enrolled-students" class="submenu-link text-sm">Enrolled Students</router-link>
          </div>
        </div>

        <!-- Reports -->
        <div>
          <button 
            @click="toggleMenu('reports')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <div class="flex items-center">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              <span class="text-sm">Reports</span>
            </div>
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': activeMenu === 'reports' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="activeMenu === 'reports'" class="ml-8 mt-1 space-y-1">
            <a href="#" class="submenu-link text-sm">User Reports</a>
            <a href="#" class="submenu-link text-sm">Course Reports</a>
            <a href="#" class="submenu-link text-sm">Financial Reports</a>
          </div>
        </div>

        <!-- Settings - Only for Super Admin -->
        <div v-if="isSuperAdmin">
          <button 
            @click="toggleMenu('settings')"
            class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <div class="flex items-center">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              <span class="text-sm">System Settings</span>
            </div>
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': activeMenu === 'settings' }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="activeMenu === 'settings'" class="ml-8 mt-1 space-y-1">
            <a href="#" class="submenu-link text-sm">General</a>
            <a href="#" class="submenu-link text-sm">Academic</a>
            <a href="#" class="submenu-link text-sm">Notifications</a>
          </div>
        </div>
      </nav>
    </div>

    <!-- Bottom: Collapsed View -->
    <div class="text-xs text-indigo-600 font-medium cursor-pointer hover:underline px-2">
      Collapsed View
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'

// Reactive data
const activeMenu = ref('dashboard')

// Computed
const user = computed(() => {
  return JSON.parse(localStorage.getItem('user') || '{}')
})

const isSuperAdmin = computed(() => {
  return user.value?.role === 'super_admin'
})

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
  padding: 0.4rem 0.75rem;
  color: #4b5563;
  border-radius: 0.5rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
}
</style>