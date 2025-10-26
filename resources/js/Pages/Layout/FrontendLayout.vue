<template>
  <div class="frontend-layout">
    <!-- Header -->
    <FrontendHeader />
    
    <!-- Main Content -->
    <main class="frontend-content">
      <slot />
    </main>
    
    <!-- Footer -->
    <FrontendFooter />
  </div>
</template>

<script setup>
import FrontendHeader from './FrontendHeader.vue'
import FrontendFooter from './FrontendFooter.vue'
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