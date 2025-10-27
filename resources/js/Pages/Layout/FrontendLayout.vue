<template>
  <div class="frontend-layout" :class="{ 'bn-lang': currentLanguage === 'bn', 'dark-theme': currentTheme === 'dark' }">
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
import { ref, onMounted } from 'vue'

const currentLanguage = ref('bn')
const currentTheme = ref('light')

onMounted(() => {
  // Load current theme
  const savedTheme = localStorage.getItem('preferredTheme')
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    currentTheme.value = savedTheme
  }
  
  // Load current language
  const savedLang = localStorage.getItem('preferredLanguage')
  if (savedLang && (savedLang === 'en' || savedLang === 'bn')) {
    currentLanguage.value = savedLang
  }
  
  // Listen for theme changes
  window.addEventListener('themeChanged', (event) => {
    currentTheme.value = event.detail.theme
  })
  
  // Listen for language changes
  window.addEventListener('languageChanged', (event) => {
    currentLanguage.value = event.detail.language
  })
})
</script>

<style scoped>
.frontend-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
}

.frontend-content {
  flex: 1;
}
</style>