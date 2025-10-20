<template>
  <div class="frontend-layout">
    <!-- Header -->
    <FrontendHeader />
    
    <!-- Main Content -->
    <main class="frontend-content">
      <router-view />
    </main>
    
    <!-- Footer -->
    <FrontendFooter />
  </div>
</template>

<script>
import FrontendHeader from './FrontendHeader.vue';
import FrontendFooter from './FrontendFooter.vue';

export default {
  name: 'FrontendLayout',
  components: {
    FrontendHeader,
    FrontendFooter
  }
}
</script>

<style scoped>
.frontend-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.frontend-content {
  flex: 1;
}
</style>