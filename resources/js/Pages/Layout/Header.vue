<template>
  <header>
    <div id="header-fixed-height"></div>
    <div id="sticky-header" class="tg-header__area tg-header__style-two">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="tgmenu__wrap">
              <nav class="tgmenu__nav">
                <div class="logo">
                  <a href="/">
                    <img src="/assets/img/logo/logo.svg" alt="Logo">
                  </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-xl-flex">
                  <ul class="navigation">
                    <li class="menu-item-has-children">
                      <a href="/">Home</a>
                    </li>
                    <li class="menu-item-has-children">
                      <a href="/courses">Courses</a>
                      <ul class="sub-menu">
                        <li><a href="/courses">All Courses</a></li>
                        <li><a href="/course-details">Course Details</a></li>
                      </ul>
                    </li>
                    <li class="menu-item-has-children">
                      <a href="/about">About</a>
                    </li>
                    <li class="menu-item-has-children">
                      <a href="/instructors">Instructors</a>
                    </li>
                    <li v-if="isAuthenticated" class="menu-item-has-children">
                      <a :href="dashboardLink">Dashboard</a>
                    </li>
                  </ul>
                </div>

                <!-- Search Bar -->
                <div class="tgmenu__search d-none d-md-block">
                  <form @submit.prevent="searchCourses" class="tgmenu__search-form">
                    <div class="select-grp">
                      <select v-model="searchCategory" class="form-select">
                        <option value="">All Categories</option>
                        <option value="business">Business</option>
                        <option value="data-science">Data Science</option>
                        <option value="design">Art & Design</option>
                        <option value="marketing">Marketing</option>
                        <option value="finance">Finance</option>
                      </select>
                    </div>
                    <div class="input-grp">
                      <input v-model="searchQuery" type="text" placeholder="Search For Course...">
                      <button type="submit"><i class="flaticon-search"></i></button>
                    </div>
                  </form>
                </div>

                <!-- Action Buttons -->
                <div class="tgmenu__action tgmenu__action-two">
                  <ul class="list-wrap">
                    <li v-if="isAuthenticated" class="header-btn">
                      <a :href="dashboardLink" class="btn">Dashboard</a>
                    </li>
                    <li v-else class="header-btn login-btn">
                      <a href="/login" class="btn">Get Started</a>
                    </li>
                  </ul>
                </div>

                <!-- Mobile Menu Toggler -->
                <div class="mobile-nav-toggler" @click="toggleMobileMenu">
                  <i class="tg-flaticon-menu-1"></i>
                </div>
              </nav>
            </div>

            <!-- Mobile Menu -->
            <div class="tgmobile__menu" :class="{ 'menu-open': mobileMenuOpen }">
              <nav class="tgmobile__menu-box">
                <div class="close-btn" @click="toggleMobileMenu">
                  <i class="tg-flaticon-close-1"></i>
                </div>
                <div class="nav-logo">
                  <a href="/">
                    <img src="/assets/img/logo/logo.svg" alt="Logo">
                  </a>
                </div>
                <div class="tgmobile__search">
                  <form @submit.prevent="searchCourses">
                    <input v-model="searchQuery" type="text" placeholder="Search here...">
                    <button><i class="fas fa-search"></i></button>
                  </form>
                </div>
                <div class="tgmobile__menu-outer">
                  <ul class="navigation">
                    <li><a href="/" @click="toggleMobileMenu">Home</a></li>
                    <li><a href="/courses" @click="toggleMobileMenu">Courses</a></li>
                    <li><a href="/about" @click="toggleMobileMenu">About</a></li>
                    <li><a href="/instructors" @click="toggleMobileMenu">Instructors</a></li>
                    <li v-if="isAuthenticated">
                      <a :href="dashboardLink" @click="toggleMobileMenu">Dashboard</a>
                    </li>
                    <li v-else>
                      <a href="/login" @click="toggleMobileMenu">Login</a>
                    </li>
                  </ul>
                </div>
              </nav>
            </div>
            <div class="tgmobile__menu-backdrop" 
                 :class="{ 'backdrop-open': mobileMenuOpen }" 
                 @click="toggleMobileMenu"></div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const mobileMenuOpen = ref(false)
const searchQuery = ref('')
const searchCategory = ref('')

// Use Inertia's shared auth data
const isAuthenticated = computed(() => {
  return !!page.props.auth.user
})

// Get dashboard link based on user role
const dashboardLink = computed(() => {
  const user = page.props.auth.user
  if (!user) return '/login'
  
  switch (user.role) {
    case 'super_admin':
      return '/super-admin'
    case 'admin':
      return '/admin'
    case 'teacher':
      return '/teacher'
    default:
      return '/'
  }
})

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const searchCourses = () => {
  // Use Inertia's visit method for navigation
  window.location.href = `/courses?search=${encodeURIComponent(searchQuery.value)}&category=${encodeURIComponent(searchCategory.value)}`
  toggleMobileMenu()
}
</script>