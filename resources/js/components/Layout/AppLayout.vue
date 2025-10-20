<template>
  <div id="app">
    <!-- Preloader -->
    <div v-if="loading" id="preloader">
      <div class="loader">
        <div class="loader-container">
          <div class="loader-icon">
            <img src="/assets/img/logo/preloader.svg" alt="Preloader">
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll Top Button -->
    <button class="scroll__top scroll-to-target" @click="scrollToTop">
      <i class="tg-flaticon-arrowhead-up"></i>
    </button>

    <!-- Header -->
    <AppHeader />

    <!-- Main Content -->
    <main>
      <router-view />
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script>
import AppHeader from './Header.vue';
import AppFooter from './Footer.vue';

export default {
  name: 'AppLayout',
  components: {
    AppHeader,
    AppFooter
  },
  data() {
    return {
      loading: false
    }
  },
  methods: {
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }
}
</script>