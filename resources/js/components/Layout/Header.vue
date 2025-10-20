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
                  <router-link to="/">
                    <img src="/assets/img/logo/logo.svg" alt="Logo">
                  </router-link>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-xl-flex">
                  <ul class="navigation">
                    <li class="menu-item-has-children">
                      <router-link to="/">Home</router-link>
                    </li>
                    <li class="menu-item-has-children">
                      <router-link to="/courses">Courses</router-link>
                      <ul class="sub-menu">
                        <li><router-link to="/courses">All Courses</router-link></li>
                        <li><router-link to="/course-details">Course Details</router-link></li>
                      </ul>
                    </li>
                    <li class="menu-item-has-children">
                      <router-link to="/about">About</router-link>
                    </li>
                    <li class="menu-item-has-children">
                      <router-link to="/instructors">Instructors</router-link>
                    </li>
                    <li v-if="isAuthenticated" class="menu-item-has-children">
                      <router-link to="/dashboard">Dashboard</router-link>
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
                      <router-link to="/dashboard" class="btn">Dashboard</router-link>
                    </li>
                    <li v-else class="header-btn login-btn">
                      <router-link to="/login" class="btn">Get Started</router-link>
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
                  <router-link to="/">
                    <img src="/assets/img/logo/logo.svg" alt="Logo">
                  </router-link>
                </div>
                <div class="tgmobile__search">
                  <form @submit.prevent="searchCourses">
                    <input v-model="searchQuery" type="text" placeholder="Search here...">
                    <button><i class="fas fa-search"></i></button>
                  </form>
                </div>
                <div class="tgmobile__menu-outer">
                  <ul class="navigation">
                    <li><router-link to="/" @click="toggleMobileMenu">Home</router-link></li>
                    <li><router-link to="/courses" @click="toggleMobileMenu">Courses</router-link></li>
                    <li><router-link to="/about" @click="toggleMobileMenu">About</router-link></li>
                    <li><router-link to="/instructors" @click="toggleMobileMenu">Instructors</router-link></li>
                    <li v-if="isAuthenticated">
                      <router-link to="/dashboard" @click="toggleMobileMenu">Dashboard</router-link>
                    </li>
                    <li v-else>
                      <router-link to="/login" @click="toggleMobileMenu">Login</router-link>
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

<script>
export default {
  name: 'AppHeader',
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
    }
  },
  methods: {
    toggleMobileMenu() {
      this.mobileMenuOpen = !this.mobileMenuOpen;
    },
    searchCourses() {
      this.$router.push({
        path: '/courses',
        query: {
          search: this.searchQuery,
          category: this.searchCategory
        }
      });
      this.toggleMobileMenu();
    }
  }
}
</script>