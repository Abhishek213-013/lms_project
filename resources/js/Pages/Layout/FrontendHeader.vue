<template>
  <header>
    <!-- Remove the debug div for production -->
    <!-- <div style="position: fixed; top: 0; left: 0; background: red; color: white; padding: 10px; z-index: 9999;">
      Debug: {{ JSON.stringify($page.props.auth) }}
    </div> -->
    
    <div id="header-fixed-height"></div>
    <div id="sticky-header" class="header-area">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="menu-wrap">
              <nav class="main-nav">
                <!-- SECTION 1: Logo -->
                <div class="logo-section">
                  <div class="logo">
                    <a href="#" @click.prevent="navigateWithLanguage('/')">
                      <div class="logo-container">
                        <img src="../../../../public/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo-image">
                      </div>
                    </a>
                  </div>
                </div>

                <!-- SECTION 2: Navigation Components -->
                <div class="nav-components-section">
                  <ul class="navigation">
                    <li><a href="#" @click.prevent="navigateWithLanguage('/')">{{ t('Home') }}</a></li>
                    <li><a href="#" @click.prevent="navigateWithLanguage('/courses')">{{ t('Courses') }}</a></li>
                    <li><a href="#" @click.prevent="navigateWithLanguage('/instructors')">{{ t('Instructors') }}</a></li>
                    <li><a href="#" @click.prevent="navigateWithLanguage('/about')">{{ t('About') }}</a></li>
                  </ul>
                </div>

                <!-- SECTION 3: Utilities & Profile -->
                <div class="utilities-section">
                  <!-- Search Bar -->
                  <div class="search-utility">
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
                            
                            <div class="loading-suggestions" v-if="isLoadingSuggestions">
                              <i class="fas fa-spinner fa-spin"></i>
                              <span>Searching courses...</span>
                            </div>
                            
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
                            
                            <div class="no-suggestions" v-else-if="searchQuery.trim()">
                              <i class="fas fa-search"></i>
                              <span>No courses found for "{{ searchQuery }}"</span>
                            </div>
                            
                            <div class="no-suggestions" v-else>
                              <i class="fas fa-book"></i>
                              <span>Start typing to search courses</span>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>

                  <!-- Theme Toggle -->
                  <div class="theme-utility">
                    <button 
                      @click="toggleTheme"
                      class="theme-btn-nav"
                      :title="currentTheme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
                    >
                      <i v-if="currentTheme === 'light'" class="fas fa-moon"></i>
                      <i v-else class="fas fa-sun"></i>
                    </button>
                  </div>

                  <!-- Language Toggle -->
                  <div class="language-utility">
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
                  </div>

                  <!-- Profile Section -->
                  <div class="profile-utility" v-if="$page.props.auth?.user">
                    <div class="profile-wrapper">
                      <button class="profile-trigger" @click="toggleProfileDropdown">
                        <!-- Dynamic Avatar Image -->
                        <img 
                          v-if="studentAvatar && !avatarError" 
                          :src="studentAvatar" 
                          alt="Profile" 
                          class="avatar-image"
                          @error="handleAvatarError"
                          @load="handleAvatarLoad"
                        >
                        <i v-else class="fas fa-user-circle fallback-avatar"></i>
                        <i class="fas fa-chevron-down" :class="{ 'rotate': profileOpen }"></i>
                      </button>
                      
                      <div class="profile-dropdown" v-show="profileOpen" v-click-outside="closeProfileDropdown">
                        <div class="dropdown-header">
                          <div class="student-info">
                            <!-- Dynamic Avatar Image -->
                            <img 
                              v-if="studentAvatar && !avatarError" 
                              :src="studentAvatar" 
                              alt="Profile" 
                              class="student-avatar-image"
                              @error="handleAvatarError"
                              @load="handleAvatarLoad"
                            >
                            <i v-else class="fas fa-user-circle student-avatar fallback-avatar"></i>
                            <div class="student-details">
                              <div class="student-name">{{ $page.props.auth?.user?.name }}</div>
                              <div class="student-email">{{ $page.props.auth?.user?.email }}</div>
                              <div class="student-roll" v-if="$page.props.auth?.user?.student?.roll_number">
                                Roll: {{ $page.props.auth?.user?.student?.roll_number }}
                              </div>
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
                          
                          <button class="dropdown-item logout" @click="logout" :disabled="isLoggingOut">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="dropdown-text">
                              {{ isLoggingOut ? t('Logging out...') : t('Logout') }}
                            </span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Login/Register -->
                  <div class="auth-utility" v-else>
                    <div class="auth-buttons-simple">
                      <a href="#" @click.prevent="navigateWithLanguage('/student-login')" class="text-link">{{ t('Login') }}</a>
                      <span class="separator">|</span>
                      <a href="#" @click.prevent="navigateWithLanguage('/phone-verification')" class="text-link primary">{{ t('Get Started') }}</a>
                    </div>
                  </div>
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
                    <a href="#" @click.prevent="navigateWithLanguage('/'); closeAll();">
                      <div class="logo-container mobile-logo-container">
                        <img src="/images/pathshala-logo.png" alt="Pathshala LMS" class="logo-image">
                      </div>
                    </a>
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
                    <li><a href="#" @click.prevent="navigateWithLanguage('/'); closeAll();">{{ t('Home') }}</a></li>
                    <li><a href="#" @click.prevent="navigateWithLanguage('/courses'); closeAll();">{{ t('Courses') }}</a></li>
                    <li><a href="#" @click.prevent="navigateWithLanguage('/instructors'); closeAll();">{{ t('Instructors') }}</a></li>
                    <li><a href="#" @click.prevent="navigateWithLanguage('/about'); closeAll();">{{ t('About') }}</a></li>
                    
                    <template v-if="$page.props.auth?.user">
                      <li class="mobile-profile-section">
                        <div class="mobile-profile-header">
                          <div class="mobile-student-info">
                            <!-- Dynamic Avatar Image for Mobile -->
                            <img 
                              v-if="studentAvatar && !avatarError" 
                              :src="studentAvatar" 
                              alt="Profile" 
                              class="mobile-avatar-image"
                              @error="handleAvatarError"
                              @load="handleAvatarLoad"
                            >
                            <i v-else class="fas fa-user-circle fallback-avatar"></i>
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
                      <li><button @click="logoutMobile" class="mobile-logout-btn" :disabled="isLoggingOut">
                        <i class="fas fa-sign-out-alt"></i>
                        {{ isLoggingOut ? t('Logging out...') : t('Logout') }}
                      </button></li>
                    </template>
                    <template v-else>
                      <li class="mobile-auth-section">
                        <div class="mobile-auth-simple">
                          <a href="#" @click.prevent="navigateWithLanguage('/student-login'); closeAll();" class="mobile-text-link">{{ t('Login') }}</a>
                          <a href="#" @click.prevent="navigateWithLanguage('/phone-verification'); closeAll();" class="mobile-text-link primary">{{ t('Get Started') }}</a>
                        </div>
                      </li>
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
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, watch, nextTick, computed, onUnmounted } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

// Use Inertia.js page props
const $page = usePage()

// Use the global translation composable
const { currentLanguage, t, switchLanguage: switchLang } = useTranslation()

// Reactive data
const mobileOpen = ref(false)
const profileOpen = ref(false)
const searchQuery = ref('')
const currentTheme = ref('light')
const showSuggestions = ref(false)
const showMobileSuggestions = ref(false)
const courseSuggestions = ref([])
const isLoadingSuggestions = ref(false)
const iconErrors = ref(0)
const avatarError = ref(false)
const isLoggingOut = ref(false)
const avatarLoaded = ref(false)
const isLanguageSwitching = ref(false)

// Computed property for student avatar
const studentData = computed(() => {
  return $page.props.auth?.user?.student || null
})

const studentAvatar = computed(() => {
  const url = studentData.value?.profile_picture_url
  return url
})

// Theme functions (keep your existing theme logic)
const getCurrentTheme = () => {
    const savedTheme = localStorage.getItem('preferredTheme')
    if (savedTheme === 'dark') {
        return 'dark'
    }
    return 'light'
}

const applyTheme = (theme) => {
    currentTheme.value = theme
    document.documentElement.classList.remove('light-theme', 'dark-theme')
    document.body.classList.remove('light-theme', 'dark-theme')
    
    if (theme === 'dark') {
        document.documentElement.classList.add('dark-theme')
        document.body.classList.add('dark-theme')
    } else {
        document.documentElement.classList.add('light-theme')
        document.body.classList.add('light-theme')
    }
    
    localStorage.setItem('preferredTheme', theme)
    document.documentElement.setAttribute('data-theme', theme)
}

const toggleTheme = () => {
    const newTheme = currentTheme.value === 'light' ? 'dark' : 'light'
    currentTheme.value = newTheme
    applyTheme(newTheme)
    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }))
    closeAll()
}

const syncTheme = () => {
    const theme = getCurrentTheme()
    currentTheme.value = theme
    applyTheme(theme)
}

const initializeTheme = () => {
    const theme = getCurrentTheme()
    currentTheme.value = theme
    applyTheme(theme)
}

// NEW: Synchronized Language Switch using the same pattern as About page
const switchLanguage = async (lang) => {
  if (lang === currentLanguage.value || isLanguageSwitching.value) return;
  
  console.log('🌐 Header: Switching language to:', lang);
  isLanguageSwitching.value = true;
  
  try {
    closeAll();
    
    // Show loading state immediately
    document.body.classList.add('language-switching');
    
    // Call the API to switch language - same pattern as About page
    const response = await fetch('/switch-language', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        language: lang
      })
    });

    const result = await response.json();
    
    if (result.success) {
      console.log('✅ Header: Language switch successful');
      
      // Update the translation composable state
      currentLanguage.value = lang;
      localStorage.setItem('preferredLanguage', lang);
      
      // Apply language settings to DOM
      document.body.classList.remove('en-lang', 'bn-lang');
      document.body.classList.add(`${lang}-lang`);
      document.documentElement.lang = lang;
      
      // Clean URL parameters
      cleanUrlLanguageParameter();
      
      // Dispatch global event for other components
      window.dispatchEvent(new CustomEvent('languageChanged', {
        detail: { 
          language: lang,
          timestamp: Date.now()
        }
      }));
      
      // Refresh icons after language change
      setTimeout(() => {
        refreshIcons();
      }, 100);
      
    } else {
      console.error('❌ Header: Language switch failed:', result);
    }
    
  } catch (error) {
    console.error('❌ Error switching language in header:', error);
  } finally {
    // Remove loading state
    document.body.classList.remove('language-switching');
    isLanguageSwitching.value = false;
  }
};

// Clean URL parameters function
const cleanUrlLanguageParameter = () => {
  if (typeof window !== 'undefined') {
    const url = new URL(window.location.href);
    if (url.searchParams.has('lang')) {
      url.searchParams.delete('lang');
      window.history.replaceState({}, '', url.toString());
      console.log('🧹 Header: Cleaned lang parameter from URL');
    }
  }
}

// Keep your existing functions (they remain the same)
const handleAvatarError = (event) => {
  avatarError.value = true
  avatarLoaded.value = false
  if (event.target) {
    event.target.style.display = 'none'
  }
}

const handleAvatarLoad = (event) => {
  avatarError.value = false
  avatarLoaded.value = true
  if (event.target) {
    event.target.style.display = 'block'
  }
}

const refreshIcons = () => {
  if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
    try {
      window.FontAwesome.dom.i2svg()
    } catch (error) {
      console.error('❌ Error refreshing Font Awesome icons:', error)
    }
  }
  
  setTimeout(() => {
    const icons = document.querySelectorAll('i[class*="fa-"]')
    icons.forEach(icon => {
      icon.style.display = 'none'
      icon.offsetHeight
      icon.style.display = 'inline-block'
    })
  }, 50)
}

const loadFontAwesome = () => {
  const existingLink = document.querySelector('link[href*="fontawesome"]')
  if (!existingLink) {
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
    link.integrity = 'sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=='
    link.crossOrigin = 'anonymous'
    document.head.appendChild(link)
  }
}

// Navigation functions (keep your existing ones)
const navigateWithLanguage = (url, options = {}) => {
  return router.visit(url, {
    preserveState: true,
    preserveScroll: true,
    ...options
  });
}

// Search functions (keep your existing ones)
const fetchCourseSuggestions = async (query = '') => {
  try {
    isLoadingSuggestions.value = true
    const response = await fetch(`/api/search-classes?query=${encodeURIComponent(query)}`)
    
    if (response.ok) {
      const data = await response.json()
      courseSuggestions.value = data.data || []
    } else {
      courseSuggestions.value = []
    }
  } catch (error) {
    courseSuggestions.value = []
  } finally {
    isLoadingSuggestions.value = false
  }
}

const filteredSuggestions = computed(() => {
  if (!searchQuery.value.trim()) {
    return courseSuggestions.value.slice(0, 5)
  }
  
  const query = searchQuery.value.toLowerCase()
  return courseSuggestions.value.filter(course => 
    course.name.toLowerCase().includes(query) ||
    (course.subject && course.subject.toLowerCase().includes(query)) ||
    (course.category && course.category.toLowerCase().includes(query))
  ).slice(0, 8)
})

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
      fetchCourseSuggestions()
      showSuggestions.value = true
      showMobileSuggestions.value = true
    }
  }, 300)
}

const handleSearchFocus = () => {
  if (courseSuggestions.value.length === 0) {
    fetchCourseSuggestions()
  }
  showSuggestions.value = true
  showMobileSuggestions.value = true
}

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

const selectSuggestion = (course) => {
  searchQuery.value = course.name
  showSuggestions.value = false
  showMobileSuggestions.value = false
  searchCourses()
}

const searchCourses = () => {
  if (searchQuery.value.trim()) {
    navigateWithLanguage('/courses', { data: { search: searchQuery.value } })
    searchQuery.value = ''
    showSuggestions.value = false
    showMobileSuggestions.value = false
  }
  closeAll()
}

// Navigation methods (keep your existing ones)
const navigateToProfile = () => {
  closeAll()
  navigateWithLanguage('/student-profile')
}

const navigateToMyCourses = () => {
  closeAll()
  navigateWithLanguage('/my-courses')
}

const navigateToLearningProgress = () => {
  closeAll()
  navigateWithLanguage('/learning-progress')
}

const navigateToSettings = () => {
  closeAll()
  navigateWithLanguage('/settings')
}

const navigateToProfileMobile = () => {
  closeAll()
  navigateWithLanguage('/student-profile')
}

const navigateToMyCoursesMobile = () => {
  closeAll()
  navigateWithLanguage('/my-courses')
}

const navigateToLearningProgressMobile = () => {
  closeAll()
  navigateWithLanguage('/learning-progress')
}

const navigateToSettingsMobile = () => {
  closeAll()
  navigateWithLanguage('/settings')
}

// UI control methods (keep your existing ones)
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

const logout = async () => {
  if (isLoggingOut.value) return;
  
  isLoggingOut.value = true;
  try {
    closeAll();
    await router.post('/logout');
    router.visit('/student-login', {
      replace: true,
      preserveState: false,
      preserveScroll: false
    });
  } catch (error) {
    console.error('❌ Logout error:', error);
    window.location.href = '/student-login';
  } finally {
    isLoggingOut.value = false;
  }
}

const logoutMobile = () => {
  logout();
}

// Lifecycle hooks
onMounted(() => {
    console.log('🚀 FrontendHeader mounted');
    
    // Ensure Font Awesome is loaded
    loadFontAwesome()
    
    // Initialize theme
    initializeTheme()
    
    // Initial icon refresh
    setTimeout(() => {
        refreshIcons()
    }, 500)
    
    // Load initial suggestions
    fetchCourseSuggestions()
    
    // Listen for theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('preferredTheme')) {
            console.log('🖥️ System theme changed, but we default to light theme')
        }
    })
    
    // Listen for theme changes from other components
    window.addEventListener('themeChanged', (event) => {
        currentTheme.value = event.detail.theme
        applyTheme(event.detail.theme)
    })
    
    // Listen for page navigation events
    router.on('navigate', (event) => {
        setTimeout(() => {
            syncTheme()
            refreshIcons()
        }, 100)
    })
})

// Watch for auth changes
watch(() => $page.props.auth?.user, (newUser, oldUser) => {
  if (newUser && newUser.id !== (oldUser?.id)) {
    avatarError.value = false
    avatarLoaded.value = false
  } else if (!newUser) {
    avatarError.value = false
    avatarLoaded.value = false
  }
})

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
/* Add language switching styles */
.language-switching {
  pointer-events: none;
}

.language-switching * {
  transition: opacity 0.3s ease-in-out !important;
}

/* Your existing CSS styles remain exactly the same */
/* ... all your existing CSS ... */

/* Enhanced language buttons with loading state */
.lang-btn-nav:disabled,
.lang-btn-mobile:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  position: relative;
}

.lang-btn-nav:disabled::after,
.lang-btn-mobile:disabled::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 16px;
  height: 16px;
  margin: -8px 0 0 -8px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Smooth transitions for text during language switch */
.navigation li a,
.text-link,
.dropdown-text,
.mobile-nav-btn {
  transition: opacity 0.2s ease-in-out;
}

body.language-switching .navigation li a,
body.language-switching .text-link,
body.language-switching .dropdown-text,
body.language-switching .mobile-nav-btn {
  opacity: 0.7;
}
/* Enhanced theme toggle icon styles */
.theme-btn-nav i,
.theme-btn-mobile i {
  display: inline-block !important;
  visibility: visible !important;
  opacity: 1 !important;
  font-size: 16px;
  width: 16px;
  height: 16px;
  transition: all 0.3s ease;
}

/* Force icon visibility and proper rendering */
:deep(i[class*="fa-"]) {
  display: inline-block !important;
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  -webkit-font-smoothing: antialiased !important;
  -moz-osx-font-smoothing: grayscale !important;
  line-height: 1 !important;
  transition: opacity 0.3s ease;
}

/* Specific styles for different icon types */
:deep(.fas) {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 300 !important;
}

:deep(.far) {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 400 !important;
}

:deep(.fab) {
  font-family: 'Font Awesome 6 Brands' !important;
  font-weight: 400 !important;
}

/* Avatar image styles - FIXED FOR VISIBILITY */
.avatar-image {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--border-color);
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
}

.student-avatar-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--primary-color);
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
}

.mobile-avatar-image {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--border-color);
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
}

.fallback-avatar {
  color: var(--primary-color);
  font-size: 24px;
}

/* Force all images to be visible */
img {
  display: inline-block !important;
  visibility: visible !important;
  opacity: 1 !important;
}

/* Base header styles using CSS variables - FIXED HEIGHT */
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
  /* FIXED HEIGHT */
  height: 70px;
}

.menu-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0;
  /* FIXED HEIGHT */
  height: 70px;
}

/* Main navigation layout with three sections */
.main-nav {
  display: flex;
  align-items: center;
  width: 100%;
  justify-content: space-between;
  /* REMOVED VERTICAL PADDING - using fixed height instead */
  padding: 0;
  gap: 20px;
  /* FIXED HEIGHT */
  height: 70px;
}

/* SECTION 1: Logo - Fixed height container */
.logo-section {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  /* FIXED HEIGHT */
  height: 70px;
}

.logo-container {
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: opacity 0.3s ease;
  /* FIXED HEIGHT */
  height: 70px;
  padding: 10px 0;
}

.logo-container:hover {
  opacity: 0.9;
}

.logo-image {
  /* Logo can have variable height but max constraint */
  height: 60px;
  width: auto;
  object-fit: contain;
  transition: height 0.3s ease;
}

/* SECTION 2: Navigation Components - Fixed height */
.nav-components-section {
  flex: 1;
  display: flex;
  justify-content: center;
  /* FIXED HEIGHT */
  height: 70px;
  align-items: center;
}

.navigation {
  display: flex;
  align-items: center;
  list-style: none;
  gap: 24px;
  margin: 0;
  padding: 0;
  /* FIXED HEIGHT */
  height: 70px;
}

.navigation li {
  display: flex;
  align-items: center;
  /* FIXED HEIGHT */
  height: 70px;
}

.navigation li a {
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  font-size: 15px;
  padding: 10px 0;
  transition: color 0.3s ease;
  white-space: nowrap;
  display: flex;
  align-items: center;
  /* FIXED HEIGHT */
  height: 70px;
}

.navigation li a:hover {
  color: var(--primary-color);
}

/* SECTION 3: Utilities & Profile - Fixed height */
.utilities-section {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 12px;
  /* FIXED HEIGHT */
  height: 70px;
}

/* All utility items should align properly */
.search-utility,
.theme-utility,
.language-utility,
.profile-utility,
.auth-utility {
  display: flex;
  align-items: center;
  /* FIXED HEIGHT */
  height: 70px;
}

/* Search Utility */
.search-utility {
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
  min-width: 200px;
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

/* Theme Utility */
.theme-utility {
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

/* Language Utility */
.language-utility {
  display: flex;
}

.language-switcher-nav {
  display: flex;
  gap: 1px;
  background: var(--bg-primary);
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

/* Profile Utility */
.profile-utility {
  position: relative;
}

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
  outline: none !important;
  /* CONSISTENT HEIGHT */
  height: 40px;
}

.profile-trigger:hover {
  background: var(--bg-tertiary);
}

.profile-trigger .fa-chevron-down {
  font-size: 11px;
  transition: transform 0.3s ease;
  margin-left: 2px;
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

.student-roll {
  font-size: 10px;
  color: var(--text-muted);
  font-weight: 500;
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

.logout:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.logout:disabled:hover {
  background: transparent;
  color: #ef4444;
}

/* Auth Utility */
.auth-utility {
  display: flex;
}

.auth-buttons-simple {
  display: flex;
  gap: 12px;
  align-items: center;
}

.text-link {
  text-decoration: none;
  font-weight: 500;
  font-size: 15px;
  color: var(--text-primary);
  transition: color 0.3s ease;
  white-space: nowrap;
  padding: 8px 0;
}

.text-link.primary {
  font-weight: 600;
  color: var(--primary-color);
}

.text-link:hover {
  color: var(--primary-color);
}

.text-link.primary:hover {
  color: var(--primary-hover);
}

.separator {
  color: var(--border-color);
  font-size: 14px;
}

/* Mobile Menu Styles */
.mobile-menu-toggler {
  display: none;
  cursor: pointer;
  font-size: 22px;
  color: var(--text-primary);
  padding: 6px;
  transition: color 0.3s ease;
  outline: none !important;
  /* FIXED HEIGHT */
  height: 70px;
  align-items: center;
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
  /* FIXED HEIGHT for mobile header */
  height: 70px;
}

.mobile-logo-container .logo-image {
  height: 32px;
  max-height: 32px;
}

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

.mobile-logout-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.mobile-logout-btn:disabled:hover {
  background: transparent;
  color: var(--text-primary);
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

.mobile-student-roll {
  font-size: 10px;
  color: var(--text-muted);
  font-weight: 500;
}

/* Mobile Simple Links */
.mobile-auth-section {
  border-top: 1px solid var(--border-light);
  padding: 20px 0;
}

.mobile-auth-simple {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 0 16px;
}

.mobile-text-link {
  text-decoration: none;
  font-weight: 500;
  font-size: 16px;
  color: var(--text-primary);
  padding: 12px 0;
  text-align: center;
  border-bottom: 1px solid var(--border-light);
  transition: color 0.3s ease;
}

.mobile-text-link.primary {
  font-weight: 600;
  color: var(--primary-color);
  border-bottom: none;
}

.mobile-text-link:hover {
  color: var(--primary-color);
}

.mobile-text-link.primary:hover {
  color: var(--primary-hover);
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
  height: 70px; /* Match the fixed navbar height */
}

/* Responsive */
@media (max-width: 1199px) {
  .nav-components-section,
  .utilities-section {
    display: none;
  }
  
  .mobile-menu-toggler {
    display: flex; /* Changed from block to flex for better alignment */
  }
  
  /* Ensure mobile logo section maintains height */
  .logo-section {
    height: 70px;
  }
}

@media (max-width: 767px) {
  .mobile-menu {
    width: 260px;
  }
  
  .logo-image {
    height: 35px; /* Slightly smaller for mobile but consistent */
    max-height: 35px;
  }
  
  .profile-trigger {
    padding: 6px 10px;
  }
  
  .mobile-controls {
    gap: 4px;
  }
  
  .search-input-group {
    min-width: 160px;
  }
  
  /* Responsive simple buttons */
  .auth-buttons-simple {
    gap: 10px;
  }
  
  .text-link {
    font-size: 14px;
    padding: 6px 0;
  }
  
  .mobile-text-link {
    font-size: 15px;
    padding: 10px 0;
  }
}

@media (max-width: 480px) {
  .profile-trigger {
    padding: 5px 8px;
  }
  
  .logo-image {
    height: 32px; /* Even smaller for very small screens */
    max-height: 32px;
  }
  
  .search-input-group {
    min-width: 140px;
  }
  
  .auth-buttons-simple {
    gap: 8px;
  }
  
  .text-link {
    font-size: 13px;
  }
}

/* Smooth transitions for all interactive elements */
.search-input-group,
.theme-btn-nav,
.lang-btn-nav,
.profile-trigger,
.dropdown-item,
.text-link,
.mobile-text-link,
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
.text-link:focus,
.mobile-text-link:focus {
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

/* Responsive adjustments for larger screens */
@media (min-width: 1200px) {
  .main-nav {
    gap: 30px;
  }
  
  .navigation {
    gap: 28px;
  }
  
  .utilities-section {
    gap: 16px;
  }
}
</style>