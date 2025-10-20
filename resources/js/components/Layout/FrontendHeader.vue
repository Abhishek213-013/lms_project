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
                  <router-link to="/">
                    <div class="logo-text">
                      <i class="fas fa-graduation-cap"></i>
                      SkillGrow
                    </div>
                  </router-link>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="desktop-menu d-none d-xl-flex">
                  <ul class="navigation">
                    <li>
                      <router-link to="/" :class="{ active: $route.path === '/' }">Home</router-link>
                    </li>
                    <li class="has-dropdown">
                      <router-link to="/courses" :class="{ active: $route.path === '/courses' }">Courses</router-link>
                      <ul class="dropdown-menu">
                        <li><router-link to="/courses">All Courses</router-link></li>
                        <li><router-link to="/course-details">Course Details</router-link></li>
                      </ul>
                    </li>
                    <li>
                      <router-link to="/instructors" :class="{ active: $route.path === '/instructors' }">Instructors</router-link>
                    </li>
                    <li>
                      <router-link to="/about" :class="{ active: $route.path === '/about' }">About</router-link>
                    </li>
                    <li>
                      <router-link to="/blog" :class="{ active: $route.path === '/blog' }">Blog</router-link>
                    </li>
                    <li>
                      <router-link to="/contact" :class="{ active: $route.path === '/contact' }">Contact</router-link>
                    </li>
                  </ul>
                </div>

                <!-- Search Bar -->
                <div class="header-search d-none d-md-block">
                  <form @submit.prevent="searchCourses" class="search-form">
                    <div class="search-input-group">
                      <input v-model="searchQuery" type="text" placeholder="Search courses...">
                      <button type="submit">
                        <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </form>
                </div>


                <!-- Mobile Menu Toggler -->
                <div class="mobile-menu-toggler" @click="toggleMobileMenu">
                  <i class="fas fa-bars"></i>
                </div>
              </nav>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu" :class="{ 'mobile-menu-open': mobileMenuOpen }">
              <div class="mobile-menu-content">
                <div class="mobile-menu-header">
                  <div class="mobile-logo">
                    <router-link to="/" @click="toggleMobileMenu">
                      <div class="logo-text">
                        <i class="fas fa-graduation-cap"></i>
                        SkillGrow
                      </div>
                    </router-link>
                  </div>
                  <div class="mobile-menu-close" @click="toggleMobileMenu">
                    <i class="fas fa-times"></i>
                  </div>
                </div>
                
                <div class="mobile-search">
                  <form @submit.prevent="searchCourses">
                    <input v-model="searchQuery" type="text" placeholder="Search courses...">
                    <button type="submit">
                      <i class="fas fa-search"></i>
                    </button>
                  </form>
                </div>
                
                <div class="mobile-navigation">
                  <ul class="mobile-nav-list">
                    <li>
                      <router-link to="/" @click="toggleMobileMenu" :class="{ active: $route.path === '/' }">Home</router-link>
                    </li>
                    <li>
                      <router-link to="/courses" @click="toggleMobileMenu" :class="{ active: $route.path === '/courses' }">Courses</router-link>
                    </li>
                    <li>
                      <router-link to="/instructors" @click="toggleMobileMenu" :class="{ active: $route.path === '/instructors' }">Instructors</router-link>
                    </li>
                    <li>
                      <router-link to="/about" @click="toggleMobileMenu" :class="{ active: $route.path === '/about' }">About</router-link>
                    </li>
                    <li>
                      <router-link to="/blog" @click="toggleMobileMenu" :class="{ active: $route.path === '/blog' }">Blog</router-link>
                    </li>
                    <li>
                      <router-link to="/contact" @click="toggleMobileMenu" :class="{ active: $route.path === '/contact' }">Contact</router-link>
                    </li>
                    
                    <template v-if="isAuthenticated">
                      <li>
                        <router-link :to="dashboardRoute" @click="toggleMobileMenu" :class="{ active: $route.path.includes('/dashboard') }">Dashboard</router-link>
                      </li>
                      <li><button @click="logoutMobile" class="mobile-logout-btn">Logout</button></li>
                    </template>
                    <template v-else>
                      <li>
                        <router-link to="/login" @click="toggleMobileMenu" :class="{ active: $route.path === '/login' }">Login</router-link>
                      </li>
                      <li>
                        <router-link to="/registration" @click="toggleMobileMenu" :class="{ active: $route.path === '/registration' }">Get Started</router-link>
                      </li>
                    </template>
                  </ul>
                </div>
              </div>
            </div>
            
            <div class="mobile-menu-overlay" 
                 :class="{ 'overlay-open': mobileMenuOpen }" 
                 @click="toggleMobileMenu"></div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'FrontendHeader',
  data() {
    return {
      mobileMenuOpen: false,
      searchQuery: '',
      searchCategory: ''
    }
  },
  computed: {
    isAuthenticated() {
      return this.$store.getters.isAuthenticated;
    },
    user() {
      return this.$store.getters.user;
    },
    dashboardRoute() {
      const roleRoutes = {
        super_admin: '/super-admin',
        admin: '/admin',
        teacher: '/teacher'
      };
      return roleRoutes[this.user?.role] || '/';
    }
  },
  methods: {
    toggleMobileMenu() {
      this.mobileMenuOpen = !this.mobileMenuOpen;
      // Prevent body scroll when mobile menu is open
      if (this.mobileMenuOpen) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    },
    searchCourses() {
      if (this.searchQuery.trim()) {
        this.$router.push({
          path: '/courses',
          query: {
            search: this.searchQuery,
            category: this.searchCategory
          }
        });
        this.searchQuery = '';
        this.searchCategory = '';
      }
      this.toggleMobileMenu();
    },
    logout() {
      this.$store.dispatch('logout');
      this.$router.push('/');
      this.$toast.success('Logged out successfully');
    },
    logoutMobile() {
      this.logout();
      this.toggleMobileMenu();
    }
  }
}
</script>

<style scoped>
/* Reset and base styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

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

/* Logo Styles - More spacing from navigation */
.logo {
  margin-right: 60px; /* Increased spacing between logo and first nav item */
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

/* Desktop Navigation - Increased spacing */
.desktop-menu {
  flex: 1;
  display: flex;
  justify-content: flex-start; /* Align navigation to start */
  margin-left: 20px; /* Additional spacing from logo */
}

.navigation {
  display: flex;
  list-style: none;
  gap: 45px; /* Increased gap between navigation items */
  margin: 0;
  padding: 0;
}

.navigation li {
  position: relative;
}

.navigation li a {
  text-decoration: none;
  color: #2c3e50;
  font-weight: 500;
  font-size: 16px;
  padding: 12px 0; /* Increased padding for better click area */
  transition: all 0.3s ease;
  position: relative;
  white-space: nowrap; /* Prevent text wrapping */
}

.navigation li a:hover,
.navigation li a.active {
  color: #4a6cf7;
}

.navigation li a.active::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 100%;
  height: 2px;
  background: #4a6cf7;
  border-radius: 2px;
}

/* Dropdown Styles */
.has-dropdown .dropdown-menu {
  position: absolute;
  top: 100%;
  left: -15px; /* Adjusted positioning */
  background: #ffffff;
  min-width: 200px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  padding: 10px 0;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.3s ease;
  list-style: none;
  z-index: 1000;
}

.has-dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-menu li a {
  display: block;
  padding: 10px 20px; /* Increased padding */
  color: #2c3e50;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 14px;
}

.dropdown-menu li a:hover {
  background: #f8f9fa;
  color: #4a6cf7;
}

/* Search Bar - Better positioning */
.header-search {
  margin: 0 40px; /* Increased margin for better spacing */
  flex-shrink: 0; /* Prevent search bar from shrinking */
}

.search-form {
  display: flex;
}

.search-input-group {
  display: flex;
  align-items: center;
  background: #f8f9fa;
  border-radius: 25px;
  padding: 8px 18px; /* Increased padding */
  border: 1px solid #e9ecef;
  min-width: 280px; /* Ensure consistent width */
}

.search-input-group input {
  border: none;
  background: transparent;
  padding: 8px 0;
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
  padding: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 8px; /* Space between input and button */
}

/* Action Buttons - Better spacing */
.header-actions {
  margin-left: 30px; /* Space from search bar */
  flex-shrink: 0; /* Prevent buttons from shrinking */
}

.action-buttons {
  display: flex;
  gap: 16px; /* Increased gap between buttons */
  align-items: center;
}

.btn {
  padding: 12px 24px; /* Increased padding for better proportions */
  border-radius: 25px;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  cursor: pointer;
  display: inline-block;
  text-align: center;
  white-space: nowrap; /* Prevent button text wrapping */
}

.btn-login {
  background: transparent;
  color: #4a6cf7;
  border-color: #4a6cf7;
}

.btn-login:hover {
  background: #4a6cf7;
  color: white;
  transform: translateY(-2px);
}

.btn-primary {
  background: #4a6cf7;
  color: white;
  border-color: #4a6cf7;
}

.btn-primary:hover {
  background: #3a5bd9;
  border-color: #3a5bd9;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
}

.btn-dashboard {
  background: #10b981;
  color: white;
  border-color: #10b981;
}

.btn-dashboard:hover {
  background: #059669;
  border-color: #059669;
  transform: translateY(-2px);
}

.btn-logout {
  background: #ef4444;
  color: white;
  border-color: #ef4444;
}

.btn-logout:hover {
  background: #dc2626;
  border-color: #dc2626;
  transform: translateY(-2px);
}

/* Mobile Menu Toggler */
.mobile-menu-toggler {
  display: none;
  cursor: pointer;
  font-size: 24px;
  color: #2c3e50;
  padding: 8px 12px; /* Increased padding */
  margin-left: 15px; /* Space from other elements */
}

/* Mobile Menu */
.mobile-menu {
  position: fixed;
  top: 0;
  left: -100%;
  width: 320px;
  height: 100vh;
  background: white;
  z-index: 1001;
  transition: all 0.3s ease;
  box-shadow: 5px 0 25px rgba(0, 0, 0, 0.1);
}

.mobile-menu.mobile-menu-open {
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
  padding: 25px 20px; /* Increased padding */
  border-bottom: 1px solid #e9ecef;
}

.mobile-menu-close {
  cursor: pointer;
  font-size: 22px; /* Slightly larger */
  color: #2c3e50;
  padding: 8px; /* Increased padding */
}

.mobile-search {
  padding: 25px 20px; /* Increased padding */
  border-bottom: 1px solid #e9ecef;
}

.mobile-search form {
  display: flex;
  background: #f8f9fa;
  border-radius: 25px;
  padding: 10px 18px; /* Increased padding */
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
  margin-left: 8px; /* Space between input and button */
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
.mobile-nav-list .mobile-logout-btn {
  display: block;
  padding: 16px 25px; /* Increased padding */
  color: #2c3e50;
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
}

.mobile-nav-list a:hover,
.mobile-nav-list .mobile-logout-btn:hover,
.mobile-nav-list a.active {
  background: #f8f9fa;
  color: #4a6cf7;
}

.mobile-menu-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.mobile-menu-overlay.overlay-open {
  opacity: 1;
  visibility: visible;
}

#header-fixed-height {
  height: 80px;
}

/* Responsive Design */
@media (max-width: 1199px) {
  .desktop-menu {
    display: none !important;
  }
  
  .mobile-menu-toggler {
    display: block;
  }
}

@media (max-width: 991px) {
  .header-search {
    display: none !important;
  }
  
  .action-buttons {
    gap: 12px; /* Slightly reduced but still good spacing */
  }
  
  .btn {
    padding: 10px 20px; /* Slightly reduced but still good */
    font-size: 13px;
  }
  
  .logo {
    margin-right: 30px; /* Reduced on smaller screens */
  }
}

@media (max-width: 767px) {
  .mobile-menu {
    width: 280px;
  }
  
  .logo-text {
    font-size: 20px;
  }
  
  .logo-text i {
    font-size: 24px;
  }
  
  .action-buttons .btn {
    padding: 8px 16px;
    font-size: 12px;
  }
}

@media (max-width: 575px) {
  .action-buttons .btn:not(.mobile-menu-toggler) {
    display: none;
  }
  
  .logo {
    margin-right: 15px; /* Further reduced on mobile */
  }
  
  .mobile-menu-toggler {
    margin-left: 10px; /* Reduced on mobile */
  }
}

/* Additional spacing utilities for better layout */
.main-nav > *:not(:first-child) {
  margin-left: 10px; /* Consistent spacing between nav sections */
}

.navigation li:not(:last-child) {
  margin-right: 5px; /* Additional spacing between nav items */
}
</style>