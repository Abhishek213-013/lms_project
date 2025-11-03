<template>
  <div class="frontend-layout">
    <FrontendHeader />
    <main>
      <slot />
    </main>
    <FrontendFooter />
  </div>
</template>

<script setup>
import FrontendHeader from './FrontendHeader.vue'
import FrontendFooter from './FrontendFooter.vue'

// No lifecycle hooks needed in layout component
// Layout should be a simple wrapper without business logic
</script>

<style scoped>
.frontend-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}
</style>