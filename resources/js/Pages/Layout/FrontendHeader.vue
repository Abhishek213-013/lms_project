<template>
  <header>
    <div id="header-fixed-height"></div>
    <div id="sticky-header" class="header-area">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="menu-wrap">
              <nav class="main-nav">
                <div class="logo">
                  <Link href="/">
                    <!-- New Rectangular Logo -->
                    <div class="logo-container">
                      <img src="../../../../public/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo-image">
                    </div>
                  </Link>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="desktop-menu">
                  <ul class="navigation">
                    <li><Link href="/">{{ t('Home') }}</Link></li>
                    <li><Link href="/courses">{{ t('Courses') }}</Link></li>
                    <li><Link href="/instructors">{{ t('Instructors') }}</Link></li>
                    <li><Link href="/about">{{ t('About') }}</Link></li>
                    
                    <!-- Search Bar with Dropdown -->
                    <li class="search-nav-item">
                      <div class="nav-search">
                        <form @submit.prevent="searchCourses">
                          <div class="search-input-group" :class="{ 'active': showSuggestions }">
                            <input 
                              v-model="searchQuery" 
                              type="text" 
                              :placeholder="t('Search Courses...')"
                              @input="handleSearchInput"
                              @focus="handleSearchFocus"
                              @blur="onSearchBlur"
                            >
                            <button type="submit"><i class="fas fa-search"></i></button>
                            
                            <!-- Search Suggestions Dropdown -->
                            <div class="search-suggestions" v-show="showSuggestions && (filteredSuggestions.length > 0 || isLoadingSuggestions)">
                              <div class="suggestions-header">
                                <span>Course Suggestions</span>
                                <span class="suggestion-count" v-if="!isLoadingSuggestions && filteredSuggestions.length > 0">
                                  {{ filteredSuggestions.length }} results
                                </span>
                              </div>
                              
                              <!-- Loading State -->
                              <div class="loading-suggestions" v-if="isLoadingSuggestions">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Searching courses...</span>
                              </div>
                              
                              <!-- Results -->
                              <div class="suggestions-list" v-else-if="filteredSuggestions.length > 0">
                                <button 
                                  v-for="course in filteredSuggestions" 
                                  :key="course.id"
                                  @click="selectSuggestion(course)"
                                  class="suggestion-item"
                                >
                                  <i class="fas fa-book"></i>
                                  <div class="course-info">
                                    <div class="course-title">{{ course.name }}</div>
                                    <div class="course-details">
                                      <span class="course-type" v-if="course.type === 'regular'">
                                        Class {{ course.grade }} • {{ course.subject }}
                                      </span>
                                      <span class="course-type" v-else-if="course.type === 'other'">
                                        {{ course.category }} • Skill Course
                                      </span>
                                      <span class="course-students">
                                        <i class="fas fa-users"></i> {{ course.student_count || 0 }} students
                                      </span>
                                    </div>
                                  </div>
                                </button>
                              </div>
                              
                              <!-- No Results -->
                              <div class="no-suggestions" v-else-if="searchQuery.trim()">
                                <i class="fas fa-search"></i>
                                <span>No courses found for "{{ searchQuery }}"</span>
                              </div>
                              
                              <!-- Empty State -->
                              <div class="no-suggestions" v-else>
                                <i class="fas fa-book"></i>
                                <span>Start typing to search courses</span>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                    </li>

                    <!-- Theme Toggle -->
                    <li class="theme-nav-item">
                      <div class="theme-switcher-nav">
                        <button 
                          @click="toggleTheme"
                          class="theme-btn-nav"
                          :title="currentTheme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
                        >
                          <i v-if="currentTheme === 'light'" class="fas fa-moon"></i>
                          <i v-else class="fas fa-sun"></i>
                        </button>
                      </div>
                    </li>

                    <!-- Language Switcher -->
                    <li class="language-nav-item">
                      <div class="language-switcher-nav">
                        <button 
                          @click="switchLanguageWithIcons('bn')"
                          :class="['lang-btn-nav', { 'active': currentLanguage === 'bn' }]"
                        >
                          Bn
                        </button>
                        <button 
                          @click="switchLanguageWithIcons('en')"
                          :class="['lang-btn-nav', { 'active': currentLanguage === 'en' }]"
                        >
                          En
                        </button>
                      </div>
                    </li>
                    
                    <!-- Profile Dropdown -->
                    <li class="profile-nav-item" v-if="$page.props.auth.user">
                      <div class="profile-wrapper">
                        <button class="profile-trigger" @click="toggleProfileDropdown">
                          <i class="fas fa-user-circle"></i>
                          {{ $page.props.auth.user.name }}
                          <i class="fas fa-chevron-down" :class="{ 'rotate': profileOpen }"></i>
                        </button>
                        
                        <div class="profile-dropdown" v-show="profileOpen" v-click-outside="closeProfileDropdown">
                          <div class="dropdown-header">
                            <div class="student-info">
                              <i class="fas fa-user-circle student-avatar"></i>
                              <div class="student-details">
                                <div class="student-name">{{ $page.props.auth.user.name }}</div>
                                <div class="student-email">{{ $page.props.auth.user.email }}</div>
                              </div>
                            </div>
                          </div>
                          
                          <div class="dropdown-divider"></div>
                          
                          <div class="dropdown-menu-items">
                            <button @click="navigateToProfile" class="dropdown-item">
                              <i class="fas fa-user"></i>
                              <span class="dropdown-text">{{ t('My Profile') }}</span>
                            </button>
                            <button @click="navigateToMyCourses" class="dropdown-item">
                              <i class="fas fa-book"></i>
                              <span class="dropdown-text">{{ t('My Courses') }}</span>
                            </button>
                            <button @click="navigateToLearningProgress" class="dropdown-item">
                              <i class="fas fa-chart-line"></i>
                              <span class="dropdown-text">{{ t('Learning Progress') }}</span>
                            </button>
                            <button @click="navigateToSettings" class="dropdown-item">
                              <i class="fas fa-cog"></i>
                              <span class="dropdown-text">{{ t('Settings') }}</span>
                            </button>
                            
                            <div class="dropdown-divider"></div>
                            
                            <button class="dropdown-item logout" @click="logout">
                              <i class="fas fa-sign-out-alt"></i>
                              <span class="dropdown-text">{{ t('Logout') }}</span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </li>
                    
                    <!-- Login/Register -->
                    <li v-else class="auth-nav-item">
                      <div class="auth-buttons">
                        <Link href="/student-login" class="btn-login">{{ t('Login') }}</Link>
                        <Link href="/phone-verification" class="btn-primary">{{ t('Get Started') }}</Link>
                      </div>
                    </li>
                  </ul>
                </div>

                <!-- Mobile Menu Toggler -->
                <div class="mobile-menu-toggler" @click="toggleMobileMenu">
                  <i class="fas fa-bars"></i>
                </div>
              </nav>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu" :class="{ 'open': mobileOpen }">
              <div class="mobile-menu-content">
                <div class="mobile-menu-header">
                  <div class="mobile-logo">
                    <Link href="/" @click="closeAll">
                      <!-- Mobile Logo -->
                      <div class="logo-container mobile-logo-container">
                        <img src="/images/pathshala-logo.png" alt="Pathshala LMS" class="logo-image">
                      </div>
                    </Link>
                  </div>
                  
                  <!-- Theme and Language Switchers in Mobile -->
                  <div class="mobile-controls">
                    <!-- Theme Toggle in Mobile -->
                    <div class="mobile-theme-switcher">
                      <button 
                        @click="toggleTheme"
                        class="theme-btn-mobile"
                        :title="currentTheme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
                      >
                        <i v-if="currentTheme === 'light'" class="fas fa-moon"></i>
                        <i v-else class="fas fa-sun"></i>
                      </button>
                    </div>
                    
                    <!-- Language Switcher in Mobile -->
                    <div class="mobile-language-switcher">
                      <button 
                        @click="switchLanguageWithIcons('bn')"
                        :class="['lang-btn-mobile', { 'active': currentLanguage === 'bn' }]"
                      >
                        Bn
                      </button>
                      <button 
                        @click="switchLanguageWithIcons('en')"
                        :class="['lang-btn-mobile', { 'active': currentLanguage === 'en' }]"
                      >
                        En
                      </button>
                    </div>
                  </div>
                  
                  <div class="mobile-menu-close" @click="closeAll">
                    <i class="fas fa-times"></i>
                  </div>
                </div>
                
                <!-- Mobile Search with Suggestions -->
                <div class="mobile-search">
                  <form @submit.prevent="searchCourses">
                    <div class="mobile-search-wrapper" :class="{ 'active': showMobileSuggestions }">
                      <input 
                        v-model="searchQuery" 
                        type="text" 
                        :placeholder="t('Search Courses...')"
                        @input="handleSearchInput"
                        @focus="handleSearchFocus"
                        @blur="onMobileSearchBlur"
                      >
                      <button type="submit"><i class="fas fa-search"></i></button>
                      
                      <!-- Mobile Search Suggestions -->
                      <div class="mobile-search-suggestions" v-show="showMobileSuggestions && (filteredSuggestions.length > 0 || isLoadingSuggestions)">
                        <div class="mobile-suggestions-header">
                          <span>Course Suggestions</span>
                        </div>
                        
                        <!-- Loading State -->
                        <div class="mobile-loading-suggestions" v-if="isLoadingSuggestions">
                          <i class="fas fa-spinner fa-spin"></i>
                          <span>Searching courses...</span>
                        </div>
                        
                        <!-- Results -->
                        <div class="mobile-suggestions-list" v-else-if="filteredSuggestions.length > 0">
                          <button 
                            v-for="course in filteredSuggestions" 
                            :key="course.id"
                            @click="selectSuggestion(course)"
                            class="mobile-suggestion-item"
                          >
                            <i class="fas fa-book"></i>
                            <div class="mobile-course-info">
                              <div class="mobile-course-title">{{ course.name }}</div>
                              <div class="mobile-course-details">
                                <span v-if="course.type === 'regular'">
                                  Class {{ course.grade }} • {{ course.subject }}
                                </span>
                                <span v-else-if="course.type === 'other'">
                                  {{ course.category }}
                                </span>
                                <span class="mobile-course-students">
                                  <i class="fas fa-users"></i> {{ course.student_count || 0 }}
                                </span>
                              </div>
                            </div>
                          </button>
                        </div>
                        
                        <!-- No Results -->
                        <div class="mobile-no-suggestions" v-else-if="searchQuery.trim()">
                          <i class="fas fa-search"></i>
                          <span>No courses found for "{{ searchQuery }}"</span>
                        </div>
                        
                        <!-- Empty State -->
                        <div class="mobile-no-suggestions" v-else>
                          <i class="fas fa-book"></i>
                          <span>Start typing to search courses</span>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                
                <div class="mobile-navigation">
                  <ul class="mobile-nav-list">
                    <li><Link href="/" @click="closeAll">{{ t('Home') }}</Link></li>
                    <li><Link href="/courses" @click="closeAll">{{ t('Courses') }}</Link></li>
                    <li><Link href="/instructors" @click="closeAll">{{ t('Instructors') }}</Link></li>
                    <li><Link href="/about" @click="closeAll">{{ t('About') }}</Link></li>
                    
                    <template v-if="$page.props.auth.user">
                      <li class="mobile-profile-section">
                        <div class="mobile-profile-header">
                          <div class="mobile-student-info">
                            <i class="fas fa-user-circle"></i>
                            <div class="mobile-student-details">
                              <div class="mobile-student-name">{{ $page.props.auth.user.name }}</div>
                              <div class="mobile-student-email">{{ $page.props.auth.user.email }}</div>
                            </div>
                          </div>
                        </div>
                      </li>
                      <li><button @click="navigateToProfileMobile" class="mobile-nav-btn"><i class="fas fa-user"></i>{{ t('My Profile') }}</button></li>
                      <li><button @click="navigateToMyCoursesMobile" class="mobile-nav-btn"><i class="fas fa-book"></i>{{ t('My Courses') }}</button></li>
                      <li><button @click="navigateToLearningProgressMobile" class="mobile-nav-btn"><i class="fas fa-chart-line"></i>{{ t('Learning Progress') }}</button></li>
                      <li><button @click="navigateToSettingsMobile" class="mobile-nav-btn"><i class="fas fa-cog"></i>{{ t('Settings') }}</button></li>
                      <li><button @click="logoutMobile" class="mobile-logout-btn"><i class="fas fa-sign-out-alt"></i>{{ t('Logout') }}</button></li>
                    </template>
                    <template v-else>
                      <li><Link href="/student-login" @click="closeAll">{{ t('Login') }}</Link></li>
                      <li><Link href="/phone-verification" @click="closeAll">{{ t('Get Started') }}</Link></li>
                    </template>
                  </ul>
                </div>
              </div>
            </div>
            
            <div class="mobile-overlay" v-if="mobileOpen" @click="closeAll"></div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, onMounted, watch, nextTick, computed } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

// Use the global translation composable
const { currentLanguage, t, switchLanguage } = useTranslation()

// Reactive data
const mobileOpen = ref(false)
const profileOpen = ref(false)
const searchQuery = ref('')
const currentTheme = ref('light')
const showSuggestions = ref(false)
const showMobileSuggestions = ref(false)
const courseSuggestions = ref([])
const isLoadingSuggestions = ref(false)

// Fetch real classes from database
const fetchCourseSuggestions = async (query = '') => {
  try {
    isLoadingSuggestions.value = true
    
    const response = await fetch(`/api/search-classes?query=${encodeURIComponent(query)}`)
    
    if (response.ok) {
      const data = await response.json()
      courseSuggestions.value = data.data || []
    } else {
      console.error('Failed to fetch course suggestions')
      courseSuggestions.value = []
    }
  } catch (error) {
    console.error('Error fetching course suggestions:', error)
    courseSuggestions.value = []
  } finally {
    isLoadingSuggestions.value = false
  }
}

// Filtered suggestions based on search query
const filteredSuggestions = computed(() => {
  if (!searchQuery.value.trim()) {
    return courseSuggestions.value.slice(0, 5) // Show top 5 when empty
  }
  
  const query = searchQuery.value.toLowerCase()
  return courseSuggestions.value.filter(course => 
    course.name.toLowerCase().includes(query) ||
    (course.subject && course.subject.toLowerCase().includes(query)) ||
    (course.category && course.category.toLowerCase().includes(query))
  ).slice(0, 8) // Limit to 8 results
})

// Handle search input with debouncing
let searchTimeout = null
const handleSearchInput = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  searchTimeout = setTimeout(() => {
    if (searchQuery.value.trim()) {
      fetchCourseSuggestions(searchQuery.value)
      showSuggestions.value = true
      showMobileSuggestions.value = true
    } else {
      // If search is empty, fetch popular classes
      fetchCourseSuggestions()
      showSuggestions.value = true
      showMobileSuggestions.value = true
    }
  }, 300)
}

// Handle search focus
const handleSearchFocus = () => {
  if (courseSuggestions.value.length === 0) {
    fetchCourseSuggestions() // Fetch popular classes when focusing
  }
  showSuggestions.value = true
  showMobileSuggestions.value = true
}

// Handle search blur with delay to allow clicking on suggestions
const onSearchBlur = () => {
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

const onMobileSearchBlur = () => {
  setTimeout(() => {
    showMobileSuggestions.value = false
  }, 200)
}

// Select a suggestion
const selectSuggestion = (course) => {
  searchQuery.value = course.name
  showSuggestions.value = false
  showMobileSuggestions.value = false
  searchCourses()
}

// Search courses function
const searchCourses = () => {
  if (searchQuery.value.trim()) {
    router.get('/courses', { search: searchQuery.value })
    searchQuery.value = ''
    showSuggestions.value = false
    showMobileSuggestions.value = false
  }
  closeAll()
}

// Load popular courses on component mount
onMounted(() => {
  fetchCourseSuggestions() // Load popular courses initially
  
  // Load theme preference and apply immediately
  const savedTheme = localStorage.getItem('preferredTheme')
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    currentTheme.value = savedTheme
    applyTheme(savedTheme)
  } else {
    // Check system preference
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    currentTheme.value = systemPrefersDark ? 'dark' : 'light'
    applyTheme(currentTheme.value)
  }
  
  // Listen for system theme changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('preferredTheme')) {
      const newTheme = e.matches ? 'dark' : 'light'
      currentTheme.value = newTheme
      applyTheme(newTheme)
      window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }))
    }
  })
  
  // Listen for theme changes from other components
  window.addEventListener('themeChanged', (event) => {
    currentTheme.value = event.detail.theme
    applyTheme(event.detail.theme)
  })
})

// Add a key to force re-render of icons
const iconRenderKey = ref(0)

// Enhanced theme management
const applyTheme = (theme) => {
  currentTheme.value = theme
  
  // Remove all theme classes first
  document.documentElement.classList.remove('light-theme', 'dark-theme')
  document.body.classList.remove('light-theme', 'dark-theme')
  
  // Add the current theme class
  if (theme === 'dark') {
    document.documentElement.classList.add('dark-theme')
    document.body.classList.add('dark-theme')
  } else {
    document.documentElement.classList.add('light-theme')
    document.body.classList.add('light-theme')
  }
  
  localStorage.setItem('preferredTheme', theme)
}

// Watch for theme changes and apply immediately
watch(currentTheme, (newTheme) => {
  applyTheme(newTheme)
})

// Enhanced language switching with icon preservation
const switchLanguageWithIcons = async (lang) => {
  // Force re-render of icons
  iconRenderKey.value++
  
  // Small delay to ensure DOM updates
  await nextTick()
  
  // Switch language
  switchLanguage(lang)
  
  closeAll()
}

// Enhanced toggle theme function
const toggleTheme = () => {
  const newTheme = currentTheme.value === 'light' ? 'dark' : 'light'
  currentTheme.value = newTheme
  applyTheme(newTheme)
  
  // Dispatch global event for other components
  window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }))
  
  closeAll()
}

// Profile Dropdown Navigation Methods
const navigateToProfile = () => {
  closeAll()
  router.get('/student-profile')
}

const navigateToMyCourses = () => {
  closeAll()
  router.get('/my-courses')
}

const navigateToLearningProgress = () => {
  closeAll()
  router.get('/learning-progress')
}

const navigateToSettings = () => {
  closeAll()
  router.get('/settings')
}

// Mobile Navigation Methods
const navigateToProfileMobile = () => {
  closeAll()
  router.get('/student-profile')
}

const navigateToMyCoursesMobile = () => {
  closeAll()
  router.get('/my-courses')
}

const navigateToLearningProgressMobile = () => {
  closeAll()
  router.get('/learning-progress')
}

const navigateToSettingsMobile = () => {
  closeAll()
  router.get('/settings')
}

// Methods
const toggleProfileDropdown = (event) => {
  event?.stopPropagation()
  profileOpen.value = !profileOpen.value
}

const closeProfileDropdown = () => {
  profileOpen.value = false
}

const toggleMobileMenu = () => {
  mobileOpen.value = !mobileOpen.value
  profileOpen.value = false
}

const closeAll = () => {
  mobileOpen.value = false
  profileOpen.value = false
  showSuggestions.value = false
  showMobileSuggestions.value = false
}

const logout = () => {
  router.post('/logout')
  closeAll()
}

const logoutMobile = () => {
  logout()
}

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = function(event) {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event)
      }
    }
    document.body.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.body.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>

<style scoped>
/* Base header styles using CSS variables */
.header-area {
  background: var(--header-bg);
  box-shadow: var(--shadow);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  width: 100%;
  transition: all 0.3s ease;
}

.menu-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0;
}

.main-nav {
  display: flex;
  align-items: center;
  width: 100%;
  justify-content: space-between;
  padding: 12px 0;
}

/* Logo Styles */
.logo {
  margin-right: 30px;
}

.logo-container {
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.logo-container:hover {
  opacity: 0.9;
}

.logo-image {
  height: 36px;
  width: auto;
  object-fit: contain;
}

/* Mobile Logo */
.mobile-logo-container .logo-image {
  height: 32px;
}

/* Desktop Navigation */
.desktop-menu {
  flex: 1;
}

.navigation {
  display: flex;
  align-items: center;
  list-style: none;
  gap: 16px;
  margin: 0;
  padding: 0;
}

.navigation li a {
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 15px;
  padding: 10px 0;
  transition: color 0.3s ease;
  white-space: nowrap;
}

.navigation li a:hover {
  color: var(--primary-color);
}

/* Search Bar - Completely Borderless */
.search-nav-item {
  margin-left: 8px;
  position: relative;
}

.nav-search {
  position: relative;
}

.search-input-group {
  display: flex;
  align-items: center;
  background: var(--bg-secondary);
  border-radius: 18px;
  padding: 5px 12px;
  min-width: 180px;
  transition: all 0.3s ease;
  position: relative;
  border: none;
  box-shadow: none;
}

.search-input-group:hover {
  background: var(--bg-tertiary);
}

.search-input-group.active {
  border-radius: 18px 18px 0 0;
  background: var(--bg-secondary);
}

.search-input-group input {
  border: none;
  background: transparent;
  padding: 5px 0;
  outline: none !important;
  width: 100%;
  font-size: 13px;
  flex: 1;
  color: var(--text-primary);
}

.search-input-group input::placeholder {
  color: var(--text-muted);
}

.search-input-group button {
  border: none;
  background: transparent;
  color: var(--primary-color);
  cursor: pointer;
  padding: 3px;
  margin-left: 4px;
  transition: color 0.3s ease;
  outline: none !important;
}

.search-input-group button:hover {
  color: var(--primary-hover);
}

/* Search Suggestions Dropdown */
.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: var(--bg-primary);
  border-radius: 0 0 18px 18px;
  box-shadow: var(--shadow-lg);
  z-index: 1001;
  max-height: 350px;
  overflow-y: auto;
}

.suggestions-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
  font-size: 11px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.suggestion-count {
  font-size: 10px;
  color: var(--text-muted);
  font-weight: 500;
}

.suggestions-list {
  padding: 6px 0;
}

/* Search Suggestions Loading State */
.loading-suggestions {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px;
  color: var(--text-secondary);
  font-size: 13px;
  justify-content: center;
}

.loading-suggestions i {
  color: var(--primary-color);
}

/* No Suggestions State */
.no-suggestions {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px;
  color: var(--text-secondary);
  font-size: 13px;
  justify-content: center;
  text-align: center;
  flex-direction: column;
}

.no-suggestions i {
  color: var(--text-muted);
  font-size: 20px;
  margin-bottom: 6px;
}

/* Enhanced suggestion items */
.suggestion-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 10px 14px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--text-primary);
}

.suggestion-item:hover {
  background: var(--bg-secondary);
}

.suggestion-item i {
  color: var(--primary-color);
  font-size: 13px;
  width: 14px;
  text-align: center;
  margin-top: 2px;
}

.course-info {
  flex: 1;
  min-width: 0;
}

.course-title {
  font-weight: 500;
  font-size: 13px;
  margin-bottom: 3px;
  line-height: 1.3;
}

.course-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.course-type {
  font-size: 11px;
  color: var(--text-secondary);
}

.course-students {
  font-size: 10px;
  color: var(--text-muted);
  display: flex;
  align-items: center;
  gap: 3px;
}

.course-students i {
  font-size: 9px;
  margin-top: 0;
}

/* Mobile Search - Completely Borderless */
.mobile-search {
  padding: 16px;
  border-bottom: 1px solid var(--border-color);
  position: relative;
}

.mobile-search-wrapper {
  position: relative;
}

.mobile-search-wrapper.active {
  z-index: 1003;
}

.mobile-search form {
  display: flex;
  background: var(--bg-secondary);
  border-radius: 18px;
  padding: 6px 12px;
  border: none;
  position: relative;
  box-shadow: none;
}

.mobile-search-wrapper.active form {
  border-radius: 18px 18px 0 0;
  background: var(--bg-secondary);
}

.mobile-search form:hover {
  background: var(--bg-tertiary);
}

.mobile-search input {
  border: none;
  background: transparent;
  flex: 1;
  outline: none !important;
  font-size: 13px;
  color: var(--text-primary);
}

.mobile-search input::placeholder {
  color: var(--text-muted);
}

.mobile-search button {
  border: none;
  background: transparent;
  color: var(--primary-color);
  cursor: pointer;
  margin-left: 6px;
  transition: color 0.3s ease;
  outline: none !important;
}

.mobile-search button:hover {
  color: var(--primary-hover);
}

.mobile-search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: var(--bg-primary);
  border-radius: 0 0 18px 18px;
  box-shadow: var(--shadow-lg);
  z-index: 1002;
  max-height: 280px;
  overflow-y: auto;
}

.mobile-suggestions-header {
  padding: 10px 14px;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
  font-size: 11px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.mobile-suggestions-list {
  padding: 6px 0;
}

.mobile-loading-suggestions {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px;
  color: var(--text-secondary);
  font-size: 13px;
  justify-content: center;
}

.mobile-loading-suggestions i {
  color: var(--primary-color);
}

.mobile-no-suggestions {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px;
  color: var(--text-secondary);
  font-size: 13px;
  justify-content: center;
  text-align: center;
  flex-direction: column;
}

.mobile-no-suggestions i {
  color: var(--text-muted);
  font-size: 18px;
  margin-bottom: 6px;
}

.mobile-suggestion-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 10px 14px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--text-primary);
}

.mobile-suggestion-item:hover {
  background: var(--bg-secondary);
}

.mobile-suggestion-item i {
  color: var(--primary-color);
  font-size: 13px;
  width: 14px;
  text-align: center;
  margin-top: 2px;
}

.mobile-course-info {
  flex: 1;
  min-width: 0;
}

.mobile-course-title {
  font-weight: 500;
  font-size: 13px;
  margin-bottom: 2px;
  line-height: 1.3;
}

.mobile-course-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 11px;
  color: var(--text-secondary);
}

.mobile-course-students {
  font-size: 10px;
  color: var(--text-muted);
  display: flex;
  align-items: center;
  gap: 3px;
}

.mobile-course-students i {
  font-size: 9px;
  margin-top: 0;
}

/* Theme Switcher */
.theme-nav-item {
  margin-left: 4px;
}

.theme-switcher-nav {
  display: flex;
}

.theme-btn-nav {
  padding: 6px 10px;
  border: none;
  border-radius: 14px;
  background: var(--bg-secondary);
  color: var(--primary-color);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 13px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  outline: none !important;
}

.theme-btn-nav:hover {
  background: var(--bg-tertiary);
  transform: scale(1.05);
}

/* Language Switcher */
.language-nav-item {
  margin-left: 4px;
}

.language-switcher-nav {
  display: flex;
  gap: 1px;
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 2px;
  transition: all 0.3s ease;
}

.lang-btn-nav {
  padding: 5px 10px;
  border: none;
  border-radius: 14px;
  background: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 11px;
  font-weight: 600;
  min-width: 36px;
  color: var(--text-primary);
  outline: none !important;
}

.lang-btn-nav.active {
  background: var(--primary-color);
  color: white;
}

.lang-btn-nav:hover {
  background: var(--bg-tertiary);
}

.lang-btn-nav.active:hover {
  background: var(--primary-hover);
}

/* Profile Dropdown */
.profile-wrapper {
  position: relative;
}

.profile-trigger {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--bg-secondary);
  padding: 6px 12px;
  border-radius: 16px;
  border: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
  max-width: 180px;
  overflow: hidden;
  text-overflow: ellipsis;
  outline: none !important;
}

.profile-trigger:hover {
  background: var(--bg-tertiary);
}

.profile-trigger i.fa-user-circle {
  font-size: 16px;
  color: var(--primary-color);
}

.profile-trigger .fa-chevron-down {
  font-size: 11px;
  transition: transform 0.3s ease;
}

.rotate {
  transform: rotate(180deg);
}

.profile-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: var(--bg-primary);
  min-width: 280px;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
  padding: 0;
  margin-top: 6px;
  z-index: 1001;
  border: 1px solid var(--border-color);
  overflow: hidden;
}

.dropdown-header {
  padding: 16px 20px;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
}

.student-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.student-avatar {
  font-size: 36px;
  color: var(--primary-color);
}

.student-details {
  flex: 1;
  min-width: 0;
}

.student-name {
  font-weight: 600;
  font-size: 14px;
  color: var(--text-primary);
  margin-bottom: 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.student-email {
  font-size: 11px;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dropdown-menu-items {
  padding: 8px 8px;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 13px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  margin: 0;
  outline: none !important;
}

.dropdown-item:hover {
  background: var(--bg-secondary);
  color: var(--primary-color);
}

.dropdown-item i {
  width: 14px;
  color: var(--text-muted);
  font-size: 13px;
  text-align: center;
}

.dropdown-item:hover i {
  color: var(--primary-color);
}

.dropdown-text {
  flex: 1;
  font-weight: 500;
}

.dropdown-divider {
  height: 1px;
  background: var(--border-color);
  margin: 6px 0;
  border: none;
}

.logout {
  color: #ef4444;
}

.logout:hover {
  background: #fef2f2;
  color: #dc2626;
}

.dark-theme .logout:hover {
  background: #7f1d1d;
  color: #fca5a5;
}

.logout:hover i {
  color: #dc2626;
}

/* Auth Buttons */
.auth-buttons {
  display: flex;
  gap: 10px;
  align-items: center;
}

.btn-login {
  padding: 6px 16px;
  border-radius: 16px;
  text-decoration: none;
  font-weight: 500;
  font-size: 13px;
  border: 2px solid var(--primary-color);
  background: transparent;
  color: var(--primary-color);
  transition: all 0.3s ease;
  outline: none !important;
}

.btn-login:hover {
  background: var(--primary-color);
  color: white;
}

.btn-primary {
  padding: 6px 16px;
  border-radius: 16px;
  text-decoration: none;
  font-weight: 500;
  font-size: 13px;
  border: 2px solid var(--primary-color);
  background: var(--primary-color);
  color: white;
  transition: all 0.3s ease;
  outline: none !important;
}

.btn-primary:hover {
  background: var(--primary-hover);
  border-color: var(--primary-hover);
}

/* Mobile Menu */
.mobile-menu-toggler {
  display: none;
  cursor: pointer;
  font-size: 22px;
  color: var(--text-primary);
  padding: 6px;
  transition: color 0.3s ease;
  outline: none !important;
}

.mobile-menu {
  position: fixed;
  top: 0;
  left: -100%;
  width: 300px;
  height: 100vh;
  background: var(--bg-primary);
  z-index: 1002;
  transition: left 0.3s ease;
  box-shadow: var(--shadow-lg);
}

.mobile-menu.open {
  left: 0;
}

.mobile-menu-content {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.mobile-menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid var(--border-color);
}

.mobile-menu-close {
  cursor: pointer;
  font-size: 18px;
  color: var(--text-primary);
  padding: 6px;
  border-radius: 4px;
  transition: all 0.3s ease;
  outline: none !important;
}

.mobile-menu-close:hover {
  background: var(--bg-secondary);
}

/* Mobile Controls */
.mobile-controls {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-right: 8px;
}

.mobile-theme-switcher {
  display: flex;
}

.theme-btn-mobile {
  padding: 6px 10px;
  border: none;
  border-radius: 14px;
  background: var(--bg-secondary);
  color: var(--primary-color);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 13px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  outline: none !important;
}

.theme-btn-mobile:hover {
  background: var(--bg-tertiary);
  transform: scale(1.05);
}

.mobile-language-switcher {
  display: flex;
  gap: 1px;
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 2px;
}

.lang-btn-mobile {
  padding: 5px 10px;
  border: none;
  border-radius: 14px;
  background: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 11px;
  font-weight: 600;
  min-width: 36px;
  color: var(--text-primary);
  outline: none !important;
}

.lang-btn-mobile.active {
  background: var(--primary-color);
  color: white;
}

.lang-btn-mobile:hover {
  background: var(--bg-tertiary);
}

.mobile-navigation {
  flex: 1;
  overflow-y: auto;
  padding: 16px 0;
}

.mobile-nav-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.mobile-nav-list li {
  border-bottom: 1px solid var(--border-light);
}

.mobile-nav-list li:last-child {
  border-bottom: none;
}

.mobile-nav-list a,
.mobile-nav-btn,
.mobile-logout-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-size: 14px;
  cursor: pointer;
  outline: none !important;
}

.mobile-nav-list a:hover,
.mobile-nav-btn:hover,
.mobile-logout-btn:hover {
  background: var(--bg-secondary);
  color: var(--primary-color);
}

.mobile-profile-section {
  background: var(--bg-secondary);
}

.mobile-profile-header {
  padding: 16px;
}

.mobile-student-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mobile-student-info i {
  font-size: 28px;
  color: var(--primary-color);
}

.mobile-student-details {
  flex: 1;
  min-width: 0;
}

.mobile-student-name {
  font-weight: 600;
  font-size: 14px;
  color: var(--text-primary);
  margin-bottom: 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mobile-student-email {
  font-size: 11px;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1001;
}

#header-fixed-height {
  height: 60px;
}

/* Responsive */
@media (max-width: 1199px) {
  .desktop-menu {
    display: none;
  }
  
  .mobile-menu-toggler {
    display: block;
  }
}

@media (max-width: 767px) {
  .mobile-menu {
    width: 260px;
  }
  
  .logo-image {
    height: 32px;
  }
  
  .profile-trigger {
    max-width: 140px;
    font-size: 13px;
    padding: 5px 10px;
  }
  
  .mobile-controls {
    gap: 4px;
  }
  
  .search-input-group {
    min-width: 160px;
  }
}

/* Truncate long names */
.profile-trigger {
  max-width: 160px;
}

@media (max-width: 480px) {
  .profile-trigger {
    max-width: 120px;
    font-size: 12px;
  }
  
  .logo-image {
    height: 30px;
  }
  
  .search-input-group {
    min-width: 140px;
  }
  
  .auth-buttons {
    gap: 6px;
  }
  
  .btn-login,
  .btn-primary {
    padding: 5px 12px;
    font-size: 12px;
  }
}

/* Ensure Font Awesome icons are properly loaded */
:deep(.fas),
:deep(.fab) {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  display: inline-block !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  line-height: 1 !important;
}

/* Force icon rendering */
:deep(i[class*="fa-"]) {
  display: inline-block !important;
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* Smooth transitions for all interactive elements */
.search-input-group,
.theme-btn-nav,
.lang-btn-nav,
.profile-trigger,
.dropdown-item,
.btn-login,
.btn-primary,
.mobile-nav-btn,
.mobile-logout-btn {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* REMOVED FOCUS STYLES - No blue outline will appear */
.search-input-group input:focus {
  box-shadow: none !important;
}

.theme-btn-nav:focus,
.lang-btn-nav:focus,
.profile-trigger:focus,
.dropdown-item:focus,
.btn-login:focus,
.btn-primary:focus {
  outline: none !important;
  box-shadow: none !important;
}

/* Scrollbar styling for dropdowns */
.search-suggestions::-webkit-scrollbar,
.mobile-search-suggestions::-webkit-scrollbar {
  width: 5px;
}

.search-suggestions::-webkit-scrollbar-track,
.mobile-search-suggestions::-webkit-scrollbar-track {
  background: var(--bg-secondary);
}

.search-suggestions::-webkit-scrollbar-thumb,
.mobile-search-suggestions::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 3px;
}

.search-suggestions::-webkit-scrollbar-thumb:hover,
.mobile-search-suggestions::-webkit-scrollbar-thumb:hover {
  background: var(--text-muted);
}
</style>