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
                    <div class="logo-text">
                      <i class="fas fa-graduation-cap"></i>
                      SkillGrow
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
                    
                    <!-- Search Bar -->
                    <li class="search-nav-item">
                      <div class="nav-search">
                        <form @submit.prevent="searchCourses">
                          <div class="search-input-group">
                            <input v-model="searchQuery" type="text" :placeholder="t('Search Courses...')">
                            <button type="submit"><i class="fas fa-search"></i></button>
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
                          <i class="fas" :class="themeIcon"></i>
                        </button>
                      </div>
                    </li>

                    <!-- Language Switcher -->
                    <li class="language-nav-item">
                      <div class="language-switcher-nav">
                        <button 
                          @click="switchLanguage('bn')"
                          :class="['lang-btn-nav', { 'active': currentLanguage === 'bn' }]"
                        >
                          Bn
                        </button>
                        <button 
                          @click="switchLanguage('en')"
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
                      <div class="logo-text">
                        <i class="fas fa-graduation-cap"></i>
                        SkillGrow
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
                        <i class="fas" :class="themeIcon"></i>
                      </button>
                    </div>
                    
                    <!-- Language Switcher in Mobile -->
                    <div class="mobile-language-switcher">
                      <button 
                        @click="switchLanguage('bn')"
                        :class="['lang-btn-mobile', { 'active': currentLanguage === 'bn' }]"
                      >
                        Bn
                      </button>
                      <button 
                        @click="switchLanguage('en')"
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
                
                <div class="mobile-search">
                  <form @submit.prevent="searchCourses">
                    <input v-model="searchQuery" type="text" :placeholder="t('Search Courses...')">
                    <button type="submit"><i class="fas fa-search"></i></button>
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
import { ref, onMounted, getCurrentInstance, computed, watch } from 'vue'

// Get the Vue instance for accessing global properties
const { proxy } = getCurrentInstance()

// Reactive data
const mobileOpen = ref(false)
const profileOpen = ref(false)
const searchQuery = ref('')
const currentLanguage = ref('bn')
const currentTheme = ref('light')

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

// Computed property for theme icon
const themeIcon = computed(() => {
  return currentTheme.value === 'light' ? 'fa-moon' : 'fa-sun'
})

onMounted(() => {
  // Load language preference
  const savedLang = localStorage.getItem('preferredLanguage')
  if (savedLang && (savedLang === 'en' || savedLang === 'bn')) {
    currentLanguage.value = savedLang
  }
  
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

// Enhanced toggle theme function
const toggleTheme = () => {
  const newTheme = currentTheme.value === 'light' ? 'dark' : 'light'
  currentTheme.value = newTheme
  applyTheme(newTheme)
  
  // Dispatch global event for other components
  window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }))
  
  closeAll()
}

// Translation function
const t = (key, replacements = {}) => {
  // Try to use global t function first
  if (proxy && typeof proxy.t === 'function') {
    return proxy.t(key, replacements)
  }
  
  // Fallback translation
  const translations = {
    en: {
      'Home': 'Home',
      'Courses': 'Courses',
      'Instructors': 'Instructors',
      'About': 'About',
      'Search Courses...': 'Search Courses...',
      'My Profile': 'My Profile',
      'My Courses': 'My Courses',
      'Learning Progress': 'Learning Progress',
      'Settings': 'Settings',
      'Logout': 'Logout',
      'Login': 'Login',
      'Get Started': 'Get Started',
    },
    bn: {
      'Home': 'হোম',
      'Courses': 'কোর্সসমূহ',
      'Instructors': 'ইন্সট্রাক্টর',
      'About': 'আমাদের সম্পর্কে',
      'Search Courses...': 'কোর্স খুঁজুন...',
      'My Profile': 'আমার প্রোফাইল',
      'My Courses': 'আমার কোর্সসমূহ',
      'Learning Progress': 'শেখার অগ্রগতি',
      'Settings': 'সেটিংস',
      'Logout': 'লগআউট',
      'Login': 'লগইন',
      'Get Started': 'শুরু করুন',
    }
  }
  
  let translated = translations[currentLanguage.value]?.[key] || key
  
  Object.keys(replacements).forEach(replacementKey => {
    translated = translated.replace(`{${replacementKey}}`, replacements[replacementKey])
  })
  
  return translated
}

// Switch language
const switchLanguage = (lang) => {
  currentLanguage.value = lang
  localStorage.setItem('preferredLanguage', lang)
  
  // Update body class
  if (lang === 'bn') {
    document.body.classList.add('bn-lang')
  } else {
    document.body.classList.remove('bn-lang')
  }
  
  // Update page title
  document.title = lang === 'bn' 
    ? 'স্কিলগ্রো - অনলাইন লার্নিং প্ল্যাটফর্ম'
    : 'SkillGro - Online Learning Platform'
  
  // Dispatch event for other components
  window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }))
  
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
}

const searchCourses = () => {
  if (searchQuery.value.trim()) {
    router.get('/courses', { search: searchQuery.value })
    searchQuery.value = ''
  }
  closeAll()
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
  padding: 15px 0;
}

/* Logo */
.logo {
  margin-right: 40px;
}

.logo-text {
  display: flex;
  align-items: center;
  font-size: 24px;
  font-weight: 700;
  color: var(--primary-color);
  text-decoration: none;
  transition: color 0.3s ease;
}

.logo-text i {
  margin-right: 8px;
  font-size: 28px;
}

/* Desktop Navigation */
.desktop-menu {
  flex: 1;
}

.navigation {
  display: flex;
  align-items: center;
  list-style: none;
  gap: 20px;
  margin: 0;
  padding: 0;
}

.navigation li a {
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 16px;
  padding: 12px 0;
  transition: color 0.3s ease;
  white-space: nowrap;
}

.navigation li a:hover {
  color: var(--primary-color);
}

/* Search Bar */
.search-nav-item {
  margin-left: 10px;
}

.nav-search {
  position: relative;
}

.search-input-group {
  display: flex;
  align-items: center;
  background: var(--bg-secondary);
  border-radius: 20px;
  padding: 6px 15px;
  border: 1px solid var(--border-color);
  min-width: 200px;
  transition: all 0.3s ease;
}

.search-input-group input {
  border: none;
  background: transparent;
  padding: 6px 0;
  outline: none;
  width: 100%;
  font-size: 14px;
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
  padding: 4px;
  margin-left: 6px;
  transition: color 0.3s ease;
}

/* Theme Switcher */
.theme-nav-item {
  margin-left: 5px;
}

.theme-switcher-nav {
  display: flex;
}

.theme-btn-nav {
  padding: 8px 12px;
  border: none;
  border-radius: 16px;
  background: var(--bg-secondary);
  color: var(--primary-color);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 44px;
}

.theme-btn-nav:hover {
  background: var(--bg-tertiary);
  transform: scale(1.05);
}

/* Language Switcher */
.language-nav-item {
  margin-left: 5px;
}

.language-switcher-nav {
  display: flex;
  gap: 2px;
  background: var(--bg-secondary);
  border-radius: 20px;
  padding: 2px;
  transition: all 0.3s ease;
}

.lang-btn-nav {
  padding: 6px 12px;
  border: none;
  border-radius: 16px;
  background: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 12px;
  font-weight: 600;
  min-width: 40px;
  color: var(--text-primary);
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
  gap: 8px;
  background: var(--bg-secondary);
  padding: 8px 16px;
  border-radius: 20px;
  border: 1px solid var(--border-color);
  color: var(--text-primary);
  font-weight: 500;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.profile-trigger:hover {
  background: var(--bg-tertiary);
}

.profile-trigger i.fa-user-circle {
  font-size: 18px;
  color: var(--primary-color);
}

.profile-trigger .fa-chevron-down {
  font-size: 12px;
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
  min-width: 300px;
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
  padding: 0;
  margin-top: 8px;
  z-index: 1001;
  border: 1px solid var(--border-color);
  overflow: hidden;
}

.dropdown-header {
  padding: 20px 24px;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
}

.student-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.student-avatar {
  font-size: 40px;
  color: var(--primary-color);
}

.student-details {
  flex: 1;
  min-width: 0;
}

.student-name {
  font-weight: 600;
  font-size: 16px;
  color: var(--text-primary);
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.student-email {
  font-size: 12px;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dropdown-menu-items {
  padding: 10px 10px;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 24px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 14px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  margin: 0;
}

.dropdown-item:hover {
  background: var(--bg-secondary);
  color: var(--primary-color);
}

.dropdown-item i {
  width: 16px;
  color: var(--text-muted);
  font-size: 14px;
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
  margin: 8px 0;
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
  gap: 12px;
  align-items: center;
}

.btn-login {
  padding: 8px 20px;
  border-radius: 20px;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  border: 2px solid var(--primary-color);
  background: transparent;
  color: var(--primary-color);
  transition: all 0.3s ease;
}

.btn-login:hover {
  background: var(--primary-color);
  color: white;
}

.btn-primary {
  padding: 8px 20px;
  border-radius: 20px;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  border: 2px solid var(--primary-color);
  background: var(--primary-color);
  color: white;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: var(--primary-hover);
  border-color: var(--primary-hover);
}

/* Mobile Menu */
.mobile-menu-toggler {
  display: none;
  cursor: pointer;
  font-size: 24px;
  color: var(--text-primary);
  padding: 8px;
  transition: color 0.3s ease;
}

.mobile-menu {
  position: fixed;
  top: 0;
  left: -100%;
  width: 320px;
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
  padding: 20px;
  border-bottom: 1px solid var(--border-color);
}

.mobile-menu-close {
  cursor: pointer;
  font-size: 20px;
  color: var(--text-primary);
  padding: 8px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.mobile-menu-close:hover {
  background: var(--bg-secondary);
}

/* Mobile Controls */
.mobile-controls {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-right: 10px;
}

.mobile-theme-switcher {
  display: flex;
}

.theme-btn-mobile {
  padding: 8px 12px;
  border: none;
  border-radius: 16px;
  background: var(--bg-secondary);
  color: var(--primary-color);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 44px;
}

.theme-btn-mobile:hover {
  background: var(--bg-tertiary);
  transform: scale(1.05);
}

.mobile-language-switcher {
  display: flex;
  gap: 2px;
  background: var(--bg-secondary);
  border-radius: 20px;
  padding: 2px;
}

.lang-btn-mobile {
  padding: 6px 12px;
  border: none;
  border-radius: 16px;
  background: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 12px;
  font-weight: 600;
  min-width: 40px;
  color: var(--text-primary);
}

.lang-btn-mobile.active {
  background: var(--primary-color);
  color: white;
}

.lang-btn-mobile:hover {
  background: var(--bg-tertiary);
}

.mobile-search {
  padding: 20px;
  border-bottom: 1px solid var(--border-color);
}

.mobile-search form {
  display: flex;
  background: var(--bg-secondary);
  border-radius: 20px;
  padding: 8px 15px;
  border: 1px solid var(--border-color);
}

.mobile-search input {
  border: none;
  background: transparent;
  flex: 1;
  outline: none;
  font-size: 14px;
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
  margin-left: 8px;
}

.mobile-navigation {
  flex: 1;
  overflow-y: auto;
  padding: 20px 0;
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
  gap: 12px;
  padding: 14px 20px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-size: 16px;
  cursor: pointer;
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
  padding: 20px;
}

.mobile-student-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mobile-student-info i {
  font-size: 32px;
  color: var(--primary-color);
}

.mobile-student-details {
  flex: 1;
  min-width: 0;
}

.mobile-student-name {
  font-weight: 600;
  font-size: 16px;
  color: var(--text-primary);
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mobile-student-email {
  font-size: 12px;
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
  height: 70px;
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
    width: 280px;
  }
  
  .logo-text {
    font-size: 20px;
  }
  
  .profile-trigger {
    max-width: 150px;
    font-size: 14px;
    padding: 6px 12px;
  }
  
  .mobile-controls {
    gap: 6px;
  }
}

/* Truncate long names */
.profile-trigger {
  max-width: 180px;
}

@media (max-width: 480px) {
  .profile-trigger {
    max-width: 140px;
    font-size: 13px;
  }
}
</style>