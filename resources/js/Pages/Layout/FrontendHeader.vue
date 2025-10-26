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
                    <li><Link href="/">Home</Link></li>
                    <li><Link href="/courses">Courses</Link></li>
                    <li><Link href="/instructors">Instructors</Link></li>
                    <li><Link href="/about">About</Link></li>
                    <li><Link href="/blog">Blog</Link></li>
                    <li><Link href="/contact">Contact</Link></li>
                    
                    <!-- Search Bar -->
                    <li class="search-nav-item">
                      <div class="nav-search">
                        <form @submit.prevent="searchCourses">
                          <div class="search-input-group">
                            <input v-model="searchQuery" type="text" placeholder="Search Courses...">
                            <button type="submit"><i class="fas fa-search"></i></button>
                          </div>
                        </form>
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
                          <Link href="/student-profile" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-user"></i>My Profile
                          </Link>
                          <Link href="/my-courses" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-book"></i>My Courses
                          </Link>
                          <Link href="/learning-progress" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-chart-line"></i>Learning Progress
                          </Link>
                          <Link href="/settings" class="dropdown-item" @click="closeAll">
                            <i class="fas fa-cog"></i>Settings
                          </Link>
                          <div class="dropdown-divider"></div>
                          <button class="dropdown-item logout" @click="logout">
                            <i class="fas fa-sign-out-alt"></i>Logout
                          </button>
                        </div>
                      </div>
                    </li>
                    
                    <!-- Login/Register -->
                    <li v-else class="auth-nav-item">
                      <div class="auth-buttons">
                        <Link href="/student-login" class="btn-login">Login</Link>
                        <Link href="/phone-verification" class="btn-primary">Get Started</Link>
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
                  <div class="mobile-menu-close" @click="closeAll">
                    <i class="fas fa-times"></i>
                  </div>
                </div>
                
                <div class="mobile-search">
                  <form @submit.prevent="searchCourses">
                    <input v-model="searchQuery" type="text" placeholder="Search courses...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                  </form>
                </div>
                
                <div class="mobile-navigation">
                  <ul class="mobile-nav-list">
                    <li><Link href="/" @click="closeAll">Home</Link></li>
                    <li><Link href="/courses" @click="closeAll">Courses</Link></li>
                    <li><Link href="/instructors" @click="closeAll">Instructors</Link></li>
                    <li><Link href="/about" @click="closeAll">About</Link></li>
                    <li><Link href="/blog" @click="closeAll">Blog</Link></li>
                    <li><Link href="/contact" @click="closeAll">Contact</Link></li>
                    
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
                      <li><Link href="/student-profile" @click="closeAll"><i class="fas fa-user"></i>My Profile</Link></li>
                      <li><Link href="/my-courses" @click="closeAll"><i class="fas fa-book"></i>My Courses</Link></li>
                      <li><Link href="/learning-progress" @click="closeAll"><i class="fas fa-chart-line"></i>Learning Progress</Link></li>
                      <li><Link href="/settings" @click="closeAll"><i class="fas fa-cog"></i>Settings</Link></li>
                      <li><button @click="logoutMobile" class="mobile-logout-btn"><i class="fas fa-sign-out-alt"></i>Logout</button></li>
                    </template>
                    <template v-else>
                      <li><Link href="/student-login" @click="closeAll">Login</Link></li>
                      <li><Link href="/phone-verification" @click="closeAll">Get Started</Link></li>
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
import { ref } from 'vue'

// Reactive data
const mobileOpen = ref(false)
const profileOpen = ref(false)
const searchQuery = ref('')

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
/* Base Styles */
.header-area {
  background: #ffffff;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  width: 100%;
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
  color: #4a6cf7;
  text-decoration: none;
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
  gap: 30px;
  margin: 0;
  padding: 0;
}

.navigation li a {
  text-decoration: none;
  color: #2c3e50;
  font-weight: 500;
  font-size: 16px;
  padding: 12px 0;
  transition: color 0.3s ease;
  white-space: nowrap;
}

.navigation li a:hover {
  color: #4a6cf7;
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
  background: #f8f9fa;
  border-radius: 20px;
  padding: 6px 15px;
  border: 1px solid #e9ecef;
  min-width: 200px;
}

.search-input-group input {
  border: none;
  background: transparent;
  padding: 6px 0;
  outline: none;
  width: 100%;
  font-size: 14px;
  flex: 1;
}

.search-input-group button {
  border: none;
  background: transparent;
  color: #4a6cf7;
  cursor: pointer;
  padding: 4px;
  margin-left: 6px;
}

/* Profile Dropdown */
.profile-wrapper {
  position: relative;
}

.profile-trigger {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f8f9fa;
  padding: 8px 15px;
  border-radius: 20px;
  border: 1px solid #e9ecef;
  color: #2c3e50;
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
  background: #e9ecef;
}

.profile-trigger i.fa-user-circle {
  font-size: 18px;
  color: #4a6cf7;
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
  background: white;
  min-width: 280px;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  padding: 0;
  margin-top: 5px;
  z-index: 1001;
}

.dropdown-header {
  padding: 16px 20px;
  background: #f8f9fa;
  border-radius: 8px 8px 0 0;
}

.student-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.student-avatar {
  font-size: 40px;
  color: #4a6cf7;
}

.student-details {
  flex: 1;
  min-width: 0;
}

.student-name {
  font-weight: 600;
  font-size: 16px;
  color: #2c3e50;
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.student-email {
  font-size: 12px;
  color: #6c757d;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  color: #2c3e50;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 14px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.dropdown-item:hover {
  background: #f8f9fa;
  color: #4a6cf7;
}

.dropdown-item i {
  width: 16px;
  color: #6c757d;
}

.dropdown-item:hover i {
  color: #4a6cf7;
}

.dropdown-divider {
  height: 1px;
  background: #e9ecef;
  margin: 0;
}

.logout {
  color: #ef4444;
}

.logout:hover {
  background: #fef2f2;
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
  border: 2px solid #4a6cf7;
  background: transparent;
  color: #4a6cf7;
  transition: all 0.3s ease;
}

.btn-login:hover {
  background: #4a6cf7;
  color: white;
}

.btn-primary {
  padding: 8px 20px;
  border-radius: 20px;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  border: 2px solid #4a6cf7;
  background: #4a6cf7;
  color: white;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: #3a5bd9;
  border-color: #3a5bd9;
}

/* Mobile Menu */
.mobile-menu-toggler {
  display: none;
  cursor: pointer;
  font-size: 24px;
  color: #2c3e50;
  padding: 8px;
}

.mobile-menu {
  position: fixed;
  top: 0;
  left: -100%;
  width: 320px;
  height: 100vh;
  background: white;
  z-index: 1002;
  transition: left 0.3s ease;
  box-shadow: 5px 0 25px rgba(0, 0, 0, 0.1);
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
  border-bottom: 1px solid #e9ecef;
}

.mobile-menu-close {
  cursor: pointer;
  font-size: 20px;
  color: #2c3e50;
  padding: 8px;
}

.mobile-search {
  padding: 20px;
  border-bottom: 1px solid #e9ecef;
}

.mobile-search form {
  display: flex;
  background: #f8f9fa;
  border-radius: 20px;
  padding: 8px 15px;
  border: 1px solid #e9ecef;
}

.mobile-search input {
  border: none;
  background: transparent;
  flex: 1;
  outline: none;
  font-size: 14px;
}

.mobile-search button {
  border: none;
  background: transparent;
  color: #4a6cf7;
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
  border-bottom: 1px solid #f1f3f4;
}

.mobile-nav-list li:last-child {
  border-bottom: none;
}

.mobile-nav-list a,
.mobile-logout-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  color: #2c3e50;
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
.mobile-logout-btn:hover {
  background: #f8f9fa;
  color: #4a6cf7;
}

.mobile-profile-section {
  background: #f8f9fa;
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
  color: #4a6cf7;
}

.mobile-student-details {
  flex: 1;
  min-width: 0;
}

.mobile-student-name {
  font-weight: 600;
  font-size: 16px;
  color: #2c3e50;
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mobile-student-email {
  font-size: 12px;
  color: #6c757d;
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