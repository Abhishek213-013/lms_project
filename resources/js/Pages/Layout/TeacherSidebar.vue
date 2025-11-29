<template>
  <aside 
    class="w-64 bg-white border-r border-gray-200 fixed h-screen overflow-y-auto flex flex-col justify-between px-4 py-6 sidebar-font transform transition-transform duration-300 ease-in-out z-50" 
    :class="sidebarClasses"
  >
    <!-- Mobile Header with Close Button -->
    <div class="lg:hidden flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
      <div class="logo">
        <a href="/teacher/portal" @click="handleMenuClick">
          <div class="logo-container">
            <img src="/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo-image">
          </div>
        </a>
      </div>
      <button 
        @click="closeMobileMenu"
        class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
      >
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Desktop Logo (hidden on mobile) -->
    <div class="logo mb-6 hidden lg:block">
      <a href="/teacher/portal">
        <div class="logo-container">
          <img src="/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo-image">
        </div>
      </a>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="space-y-2 flex-1">
      <p class="sidebar-label px-2 mt-4 mb-1">
        Teaching
      </p>

      <!-- My Classes -->
      <div>
        <button 
          @click="toggleMenu('classes')"
          class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline sidebar-item"
          :class="{ 'bg-blue-50 text-blue-700': activeMenu === 'classes' }"
        >
          <!-- Chevron - Show down arrow when open, right arrow when closed -->
          <svg 
            v-if="activeMenu === 'classes'" 
            class="w-4 h-4 mr-2" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
          <svg 
            v-else 
            class="w-4 h-4 mr-2" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
          
          <div class="flex items-center flex-1">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span class="sidebar-text">My Classes</span>
          </div>
        </button>
        <div v-show="activeMenu === 'classes'" class="ml-8 mt-1 space-y-0.5">
          <Link href="/teacher/classes" class="submenu-link sidebar-text" @click="handleMenuClick">All Classes</Link>
          <Link href="/teacher/classes/schedule" class="submenu-link sidebar-text" @click="handleMenuClick">Class Schedule</Link>
        </div>
      </div>

      <!-- Resources -->
      <div>
        <button 
          @click="toggleMenu('resources')"
          class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline sidebar-item"
          :class="{ 'bg-blue-50 text-blue-700': activeMenu === 'resources' }"
        >
          <!-- Chevron - Show down arrow when open, right arrow when closed -->
          <svg 
            v-if="activeMenu === 'resources'" 
            class="w-4 h-4 mr-2" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
          <svg 
            v-else 
            class="w-4 h-4 mr-2" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
          
          <div class="flex items-center flex-1">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="sidebar-text">Resources</span>
          </div>
        </button>
        <div v-show="activeMenu === 'resources'" class="ml-8 mt-1 space-y-0.5">
          <a href="#" class="submenu-link sidebar-text" @click.prevent="handleUploadClick">Upload Resources</a>
          <Link href="/teacher/resources" class="submenu-link sidebar-text" @click="handleMenuClick">My Resources</Link>
          <Link href="/teacher/resources/shared" class="submenu-link sidebar-text" @click="handleMenuClick">Shared Resources</Link>
        </div>
      </div>

      <!-- Assessments -->
      <div>
        <button 
          @click="toggleMenu('assessments')"
          class="w-full flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline sidebar-item"
          :class="{ 'bg-blue-50 text-blue-700': activeMenu === 'assessments' }"
        >
          <!-- Chevron - Show down arrow when open, right arrow when closed -->
          <svg 
            v-if="activeMenu === 'assessments'" 
            class="w-4 h-4 mr-2" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
          <svg 
            v-else 
            class="w-4 h-4 mr-2" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
          
          <div class="flex items-center flex-1">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span class="sidebar-text">Assessments</span>
          </div>
        </button>
        <div v-show="activeMenu === 'assessments'" class="ml-8 mt-1 space-y-0.5">
          <a href="#" class="submenu-link sidebar-text" @click.prevent="handleCreateAssignment">Create Assignment</a>
          <Link href="/teacher/assignments" class="submenu-link sidebar-text" @click="handleMenuClick">Grade Assignments</Link>
          <Link href="/teacher/assignments/progress" class="submenu-link sidebar-text" @click="handleMenuClick">Student Progress</Link>
        </div>
      </div>
    </nav>

    <!-- Back to Admin Button -->
    <div class="mt-6 pt-4 border-t border-gray-200">
      <button 
        @click="handleGoBackToAdmin"
        class="w-full flex items-center justify-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors no-underline sidebar-item text-sm"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Admin
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

// Define emits
const emit = defineEmits(['showUploadModal', 'createAssignment', 'goBackToAdmin', 'close-mobile', 'menu-click'])

// Props for mobile state
const props = defineProps({
  isMobileMenuOpen: {
    type: Boolean,
    default: false
  }
})

// UI State
const activeMenu = ref('')

// Computed classes for responsive behavior
const sidebarClasses = computed(() => {
  return props.isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
})

// Methods
const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu
}

const closeMobileMenu = () => {
  emit('close-mobile')
}

const handleMenuClick = () => {
  // Close mobile menu when a menu item is clicked
  emit('menu-click')
  closeMobileMenu()
}

const handleUploadClick = () => {
  emit('showUploadModal')
  closeMobileMenu()
}

const handleCreateAssignment = () => {
  emit('createAssignment')
  closeMobileMenu()
}

const handleGoBackToAdmin = () => {
  emit('goBackToAdmin')
  closeMobileMenu()
}
</script>

<style scoped>
/* Font family for the entire sidebar */
.sidebar-font {
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

/* Main navigation text */
.sidebar-text {
  font-size: 12.8px !important;
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

/* Section labels */
.sidebar-label {
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

.submenu-link {
  display: block;
  padding: 0.35rem 0.75rem;
  color: #4b5563;
  border-radius: 0.375rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
  text-decoration: none;
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
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

/* Mobile-specific styles */
@media (max-width: 1023px) {
  .sidebar-font {
    background: white;
    width: 100%;
    max-width: 16rem;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 50;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }
}

/* Desktop styles */
@media (min-width: 1024px) {
  .sidebar-font {
    transform: none !important;
    position: fixed;
    left: 0;
    top: 0;
  }
}

/* Smooth transitions */
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Ensure sidebar is above other content */
.z-50 {
  z-index: 50;
}
</style>