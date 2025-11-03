<template>
  <aside class="w-64 bg-white border-r border-gray-200 fixed h-screen overflow-y-auto flex flex-col justify-between px-4 py-4 sidebar-font">
    <!-- Top: Logo + Navigation -->
    <div class="logo">
      <a href="/super-admin">
        <!-- New Rectangular Logo -->
        <div class="logo-container">
          <img src="/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo-image">
        </div>
      </a>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="space-y-2">
      <!-- Dashboard -->
      <div>
        <button 
          @click="toggleMenu('dashboard')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="sidebar-text">Dashboard</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'dashboard' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'dashboard'" class="ml-8 mt-1 space-y-0.5">
          <a href="/super-admin" class="submenu-link sidebar-text">Overview</a>
          <a href="/super-admin/analytics" class="submenu-link sidebar-text">Analytics</a>
        </div>
      </div>

      <p class="sidebar-label px-2 mt-4 mb-1">
        Management
      </p>

      <!-- User Management -->
      <div>
        <button 
          @click="toggleMenu('users')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <span class="sidebar-text">Manage Users</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'users' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'users'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/users/super-admins" class="submenu-link sidebar-text">Super Admins</a>
          <a href="/admin/users/admins" class="submenu-link sidebar-text">Admins</a>
          <a href="/admin/users/teachers" class="submenu-link sidebar-text">Teachers</a>
        </div>
      </div>

      <!-- Course Management -->
      <div>
        <button 
          @click="toggleMenu('courses')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span class="sidebar-text">Courses</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'courses' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'courses'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/courses/all-courses" class="submenu-link sidebar-text">All Courses</a>
          <a href="/admin/courses/categories" class="submenu-link sidebar-text">Course Categories</a>
          <a href="/admin/courses/enrollments" class="submenu-link sidebar-text">Enrollments</a>
          <a href="/admin/courses/reviews" class="submenu-link sidebar-text">Course Reviews</a>
        </div>
      </div>

      <!-- Admissions -->
      <div>
        <button 
          @click="toggleMenu('admissions')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span class="sidebar-text">Admissions</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'admissions' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'admissions'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/admissions/applications" class="submenu-link sidebar-text">Applications</a>
          <a href="/admin/admissions/approvals" class="submenu-link sidebar-text">Approvals</a>
          <a href="/admin/admissions/enrolled-students" class="submenu-link sidebar-text">Enrolled Students</a>
        </div>
      </div>

      <!-- Reports -->
      <div>
        <button 
          @click="toggleMenu('reports')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="sidebar-text">Reports</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'reports' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'reports'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/reports/users" class="submenu-link sidebar-text">User Reports</a>
          <a href="/admin/reports/courses" class="submenu-link sidebar-text">Course Reports</a>
          <a href="/admin/reports/financial" class="submenu-link sidebar-text">Financial Reports</a>
        </div>
      </div>

      <!-- Settings - Only for Super Admin -->
      <div v-if="isSuperAdmin">
        <button 
          @click="toggleMenu('settings')"
          class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors sidebar-font"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="sidebar-text">System Settings</span>
          </div>
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': activeMenu === 'settings' }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div v-show="activeMenu === 'settings'" class="ml-8 mt-1 space-y-0.5">
          <a href="/admin/settings/general" class="submenu-link sidebar-text">General</a>
          <a href="/admin/settings/academic" class="submenu-link sidebar-text">Academic</a>
          <a href="/admin/settings/notifications" class="submenu-link sidebar-text">Notifications</a>
        </div>
      </div>
    </nav>

    <!-- Bottom: Collapsed View -->
    <div class="sidebar-collapsed-text cursor-pointer hover:underline px-2" @click="toggleCollapsed">
      {{ isCollapsed ? 'Expanded View' : 'Collapsed View' }}
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()

// Reactive data
const activeMenu = ref('dashboard')
const isCollapsed = ref(false)

// Computed - Use Inertia's shared auth data
const user = computed(() => {
  return page.props.auth?.user || {}
})

const isSuperAdmin = computed(() => {
  return user.value?.role === 'super_admin'
})

// Methods
const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu
}

const toggleCollapsed = () => {
  isCollapsed.value = !isCollapsed.value
  // You can emit an event here to notify parent component about collapsed state
  // emit('toggle-collapsed', isCollapsed.value)
}

// Initialize on mount
onMounted(() => {
  console.log('✅ Sidebar mounted successfully')
  console.log('👤 User:', user.value)
  console.log('🔑 Is Super Admin:', isSuperAdmin.value)
})
</script>

<style scoped>
/* Font family for the entire sidebar */
.sidebar-font {
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Main navigation text */
.sidebar-text {
  font-size: 13px;
  font-weight: 600;
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Section labels */
.sidebar-label {
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Collapsed view text */
.sidebar-collapsed-text {
  font-size: 12px;
  font-weight: 600;
  color: #4f46e5;
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

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
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
  text-decoration: none;
}

.logo-image {
  max-width: 180px;
  height: auto;
}
</style>