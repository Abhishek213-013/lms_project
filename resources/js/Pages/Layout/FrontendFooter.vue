<template>
  <footer class="frontend-footer bg-dark text-white py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 mb-4">
          <h5 class="mb-3">
            <Link href="/" class="text-white text-decoration-none">
              <i class="fas fa-graduation-cap me-2"></i>
              SkillGrow
            </Link>
          </h5>
          <p class="text-light">{{ t('Transforming education through innovative online learning solutions.') }}</p>
          <div class="social-links">
            <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
        
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="mb-3">{{ t('Quick Links') }}</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><Link href="/" class="text-light text-decoration-none">{{ t('Home') }}</Link></li>
            <li class="mb-2"><Link href="/courses" class="text-light text-decoration-none">{{ t('Courses') }}</Link></li>
            <li class="mb-2"><Link href="/instructors" class="text-light text-decoration-none">{{ t('Instructors') }}</Link></li>
            <li class="mb-2"><Link href="/about" class="text-light text-decoration-none">{{ t('About Us') }}</Link></li>
          </ul>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <h6 class="mb-3">{{ t('Categories') }}</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><Link href="/courses?category=primary" class="text-light text-decoration-none">{{ t('Primary (1-5)') }}</Link></li>
            <li class="mb-2"><Link href="/courses?category=junior" class="text-light text-decoration-none">{{ t('Junior (6-8)') }}</Link></li>
            <li class="mb-2"><Link href="/courses?category=secondary" class="text-light text-decoration-none">{{ t('Secondary (9-10)') }}</Link></li>
            <li class="mb-2"><Link href="/courses?category=higher-secondary" class="text-light text-decoration-none">{{ t('Higher Secondary (11-12)') }}</Link></li>
          </ul>
        </div>
        
        <div class="col-lg-3 mb-4">
          <h6 class="mb-3">{{ t('Contact Info') }}</h6>
          <ul class="list-unstyled text-light">
            <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>123 Education Street</li>
            <li class="mb-2"><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</li>
            <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@skillgrow.com</li>
          </ul>
        </div>
      </div>
      
      <hr class="bg-light">
      
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="mb-0 text-light">&copy; 2024 SkillGrow. {{ t('All rights reserved.') }}</p>
        </div>
        <div class="col-md-6 text-md-end">
          <Link href="/privacy" class="text-light text-decoration-none me-3">{{ t('Privacy Policy') }}</Link>
          <Link href="/terms" class="text-light text-decoration-none">{{ t('Terms of Service') }}</Link>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { translations } from '../../utils/translations'

const currentLanguage = ref('bn') // Default to Bengali

// Load language preference from localStorage
onMounted(() => {
  const savedLang = localStorage.getItem('preferredLanguage')
  if (savedLang && (savedLang === 'en' || savedLang === 'bn')) {
    currentLanguage.value = savedLang
  }
})

// Translation function
const t = (key, replacements = {}) => {
  let translated = translations[currentLanguage.value]?.[key] || key
  
  Object.keys(replacements).forEach(replacementKey => {
    translated = translated.replace(`{${replacementKey}}`, replacements[replacementKey])
  })
  
  return translated
}
</script>

<style scoped>
.frontend-footer {
  margin-top: auto;
}

.social-links a {
  transition: color 0.3s ease;
}

.social-links a:hover {
  color: #e74c3c !important;
}

.text-light {
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.text-light:hover {
  opacity: 1;
}
</style>