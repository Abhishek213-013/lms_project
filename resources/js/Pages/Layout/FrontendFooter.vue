<template>
  <footer class="frontend-footer" :class="themeClass">
    <div class="footer-padding-layer">
      <div class="container">
        <div class="row compact-row">
          <div class="col-lg-4 compact-col">
            <div class="footer-logo compact-logo">
              <Link href="/" class="text-decoration-none">
                <div class="footer-logo-container">
                  <img src="../../../../public/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="footer-logo-image">
                </div>
              </Link>
            </div>
            <p class="footer-description compact-description">{{ t('Transforming education through innovative online learning solutions.') }}</p>
            <div class="social-links compact-social">
              <a href="#" class="social-link" :title="t('Follow us on Facebook')">
                <i class="fab fa-facebook-f icon-fixed"></i>
              </a>
              <a href="#" class="social-link" :title="t('Follow us on Twitter')">
                <i class="fab fa-twitter icon-fixed"></i>
              </a>
              <a href="#" class="social-link" :title="t('Follow us on LinkedIn')">
                <i class="fab fa-linkedin-in icon-fixed"></i>
              </a>
              <a href="#" class="social-link" :title="t('Follow us on Instagram')">
                <i class="fab fa-instagram icon-fixed"></i>
              </a>
            </div>
          </div>
          
          <div class="col-lg-2 col-md-6 compact-col">
            <h6 class="footer-heading compact-heading">{{ t('Quick Links') }}</h6>
            <ul class="footer-links compact-links">
              <li class="footer-link-item">
                <Link href="/" class="footer-link">
                  {{ t('Home') }}
                </Link>
              </li>
              <li class="footer-link-item">
                <Link href="/courses" class="footer-link">
                  {{ t('Courses') }}
                </Link>
              </li>
              <li class="footer-link-item">
                <Link href="/instructors" class="footer-link">
                  {{ t('Instructors') }}
                </Link>
              </li>
              <li class="footer-link-item">
                <Link href="/about" class="footer-link">
                  {{ t('About Us') }}
                </Link>
              </li>
            </ul>
          </div>
          
          <div class="col-lg-3 col-md-6 compact-col">
            <h6 class="footer-heading compact-heading">{{ t('Categories') }}</h6>
            <ul class="footer-links compact-links">
              <li class="footer-link-item">
                <Link href="/courses?category=primary" class="footer-link">
                  {{ t('Primary (1-5)') }}
                </Link>
              </li>
              <li class="footer-link-item">
                <Link href="/courses?category=junior" class="footer-link">
                  {{ t('Junior (6-8)') }}
                </Link>
              </li>
              <li class="footer-link-item">
                <Link href="/courses?category=secondary" class="footer-link">
                  {{ t('Secondary (9-10)') }}
                </Link>
              </li>
              <li class="footer-link-item">
                <Link href="/courses?category=higher-secondary" class="footer-link">
                  {{ t('Higher Secondary (11-12)') }}
                </Link>
              </li>
            </ul>
          </div>
          
          <div class="col-lg-3 compact-col">
            <h6 class="footer-heading compact-heading">{{ t('Contact Info') }}</h6>
            <ul class="contact-info compact-contact">
              <li class="contact-item">
                <i class="fas fa-map-marker-alt contact-icon icon-fixed"></i>
                <span class="contact-text">159 Anabil, Dhopadighir Par (North), Jail Road, Sylhet, Sylhet 3100</span>
              </li>
              <li class="contact-item">
                <i class="fas fa-phone contact-icon icon-fixed"></i>
                <span class="contact-text">+88 01842-485222</span>
              </li>
              <li class="contact-item">
                <i class="fas fa-envelope contact-icon icon-fixed"></i>
                <span class="contact-text">itlslhelpdesk@gmail.com</span>
              </li>
              <li class="contact-item">
                <i class="fas fa-clock contact-icon icon-fixed"></i>
                <span class="contact-text">{{ t('24/7 Support') }}</span>
              </li>
            </ul>
          </div>
        </div>
        
        <hr class="footer-divider compact-divider">
        
        <div class="footer-bottom compact-bottom">
          <div class="row align-items-center">
            <div class="col-md-6">
              <p class="copyright">
                &copy; {{ new Date().getFullYear() }} IT Lab Solutions Ltd. {{ t('All rights reserved.') }}
              </p>
            </div>
            <div class="col-md-6 text-md-end">
              <Link href="/privacy" class="footer-bottom-link me-3">
                {{ t('Privacy Policy') }}
              </Link>
              <Link href="/terms" class="footer-bottom-link me-3">
                {{ t('Terms of Service') }}
              </Link>
              <Link href="/contact" class="footer-bottom-link">
                {{ t('Contact') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

// Use the global translation composable
const { currentLanguage, t, switchLanguage } = useTranslation()

// Theme state
const currentTheme = ref('light')

// Compute theme class for the footer
const themeClass = ref('light-theme')

// Update theme class based on current theme
const updateThemeClass = () => {
  themeClass.value = currentTheme.value === 'dark' ? 'dark-theme' : 'light-theme'
}

// Handle theme change events from header
const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme
  updateThemeClass()
}

// Load initial theme
const loadInitialTheme = () => {
  const savedTheme = localStorage.getItem('preferredTheme')
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    currentTheme.value = savedTheme
  } else {
    // Check system preference
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    currentTheme.value = systemPrefersDark ? 'dark' : 'light'
  }
  updateThemeClass()
}

// Refresh icons when language changes
const refreshIcons = () => {
  if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
    setTimeout(() => {
      window.FontAwesome.dom.i2svg();
    }, 100);
  }
}

// Watch for language changes
watch(currentLanguage, (newLang, oldLang) => {
  console.log('Footer: Language changed from', oldLang, 'to', newLang);
  refreshIcons();
  
  setTimeout(() => {
    refreshIcons();
  }, 200);
});

onMounted(() => {
  // Load initial theme
  loadInitialTheme()
  
  // Listen for theme changes from header
  window.addEventListener('themeChanged', handleThemeChange)
  
  // Also listen for direct storage changes (as a backup)
  window.addEventListener('storage', (event) => {
    if (event.key === 'preferredTheme') {
      currentTheme.value = event.newValue
      updateThemeClass()
    }
  })
  
  // Watch for system theme changes
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  mediaQuery.addEventListener('change', (e) => {
    if (!localStorage.getItem('preferredTheme')) {
      currentTheme.value = e.matches ? 'dark' : 'light'
      updateThemeClass()
    }
  })
  
  // Initialize icons
  refreshIcons()
})

onUnmounted(() => {
  // Clean up event listeners
  window.removeEventListener('themeChanged', handleThemeChange)
})

// Watch for theme changes and update class
watch(currentTheme, () => {
  updateThemeClass()
})
</script>

<style scoped>
/* Base footer styles - Ultra Compact Height */
.frontend-footer {
  margin-top: auto;
  transition: all 0.3s ease;
  padding: 0;
  min-height: auto;
}

/* NEW: Additional padding layer for separation */
.footer-padding-layer {
  padding: 2rem 0 1rem 0;
  position: relative;
}

/* Add subtle top border for better separation */
.frontend-footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, var(--border-color) 50%, transparent 100%);
  opacity: 0.6;
}

/* Compact layout classes */
.compact-row {
  margin-bottom: 0.5rem;
}

.compact-col {
  margin-bottom: 1rem !important;
}

.compact-logo {
  margin-bottom: 0.75rem !important;
}

.compact-description {
  margin-bottom: 0.75rem !important;
  line-height: 1.3;
}

.compact-social {
  margin-top: 0.75rem !important;
}

.compact-heading {
  margin-bottom: 0.5rem !important;
  padding-bottom: 0.5rem !important;
}

.compact-links {
  margin-top: 0.25rem !important;
}

.compact-contact {
  margin-top: 0.25rem !important;
}

.compact-divider {
  margin: 1rem 0 0.5rem 0 !important;
}

.compact-bottom {
  padding-top: 0.25rem !important;
}

/* ==================== */
/* ICON FIXES FOR LANGUAGE SWITCH */
/* ==================== */
.icon-fixed {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  -webkit-font-smoothing: antialiased !important;
  speak: none;
}

/* Ensure all Font Awesome icons maintain their font family */
.fas, .fa, .far, .fab {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* Specific fixes for Bengali language */
:global(.bn-lang) .fas,
:global(.bn-lang) .fa,
:global(.bn-lang) .far,
:global(.bn-lang) .fab,
:global(.bn-lang) .icon-fixed {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* Light theme */
.frontend-footer.light-theme {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-top: 1px solid #dee2e6;
  color: #333;
}

.frontend-footer.light-theme .footer-description {
  color: #666;
  font-size: 0.9rem;
}

.frontend-footer.light-theme .footer-heading {
  color: #2c3e50;
}

.frontend-footer.light-theme .footer-link {
  color: #555;
}

.frontend-footer.light-theme .footer-link:hover {
  color: #e74c3c;
}

.frontend-footer.light-theme .contact-item {
  color: #555;
}

.frontend-footer.light-theme .contact-icon {
  color: #e74c3c;
}

.frontend-footer.light-theme .footer-divider {
  border-color: #dee2e6;
  opacity: 0.7;
}

.frontend-footer.light-theme .copyright {
  color: #666;
}

.frontend-footer.light-theme .footer-bottom-link {
  color: #666;
}

.frontend-footer.light-theme .footer-bottom-link:hover {
  color: #e74c3c;
}

/* Dark theme */
.frontend-footer.dark-theme {
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  border-top: 1px solid #333;
  color: #fff;
}

.dark-theme .footer-description {
  color: #ccc;
  font-size: 0.9rem;
}

.dark-theme .footer-heading {
  color: #fff;
}

.dark-theme .footer-heading::after {
  background: linear-gradient(90deg, #e74c3c, #3498db);
}

.dark-theme .footer-link {
  color: #ccc;
}

.dark-theme .footer-link:hover {
  color: #fff;
}

.dark-theme .contact-item {
  color: #ccc;
}

.dark-theme .contact-icon {
  color: #e74c3c;
}

.dark-theme .footer-divider {
  border-color: #444;
  opacity: 0.5;
}

.dark-theme .copyright {
  color: #999;
}

.dark-theme .footer-bottom-link {
  color: #999;
}

.dark-theme .footer-bottom-link:hover {
  color: #fff;
}

/* Footer Logo Styles */
.footer-logo-container {
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.footer-logo-container:hover {
  opacity: 0.9;
}

.footer-logo-image {
  height: 35px;
  width: auto;
  object-fit: contain;
}

.light-theme .footer-logo-image {
  filter: brightness(0.8);
}

.dark-theme .footer-logo-image {
  filter: brightness(0) invert(1);
}

/* Footer Headings */
.footer-heading {
  font-weight: 600;
  font-size: 15px;
  position: relative;
  line-height: 1.2;
}

.footer-heading::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 25px;
  height: 2px;
  border-radius: 2px;
}

.light-theme .footer-heading::after {
  background: linear-gradient(90deg, #e74c3c, #3498db);
}

.dark-theme .footer-heading::after {
  background: linear-gradient(90deg, #e74c3c, #3498db);
}

/* Footer Links */
.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-link-item {
  margin-bottom: 0.25rem;
}

.footer-link {
  opacity: 0.8;
  transition: all 0.3s ease;
  padding: 0.125rem 0;
  display: inline-block;
  position: relative;
  text-decoration: none;
  font-size: 14px;
  line-height: 1.2;
}

.footer-link::before {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 1px;
  transition: width 0.3s ease;
}

.light-theme .footer-link::before {
  background: linear-gradient(90deg, #e74c3c, #3498db);
}

.dark-theme .footer-link::before {
  background: linear-gradient(90deg, #e74c3c, #3498db);
}

.footer-link:hover {
  opacity: 1;
  transform: translateX(4px);
}

.footer-link:hover::before {
  width: 100%;
}

/* Social Links */
.social-links {
  display: flex;
  gap: 8px;
}

.social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  transition: all 0.3s ease;
  text-decoration: none;
  font-size: 13px;
}

.light-theme .social-link {
  background: rgba(0, 0, 0, 0.1);
  color: #555;
}

.dark-theme .social-link {
  background: rgba(255, 255, 255, 0.1);
  color: #ccc;
}

.social-link:hover {
  background: linear-gradient(135deg, #e74c3c, #3498db);
  color: #ffffff !important;
  transform: translateY(-1px);
  box-shadow: 0 3px 8px rgba(231, 76, 60, 0.4);
}

/* Contact Items */
.contact-info {
  list-style: none;
  padding: 0;
  margin: 0;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  padding: 0.25rem 0;
  transition: all 0.3s ease;
  margin-bottom: 0.25rem;
}

.contact-item:hover {
  transform: translateX(3px);
}

.contact-icon {
  width: 12px;
  margin-right: 8px;
  margin-top: 2px;
  flex-shrink: 0;
  font-size: 12px;
}

.contact-text {
  line-height: 1.3;
  font-size: 13px;
}

/* Footer Bottom */
.footer-divider {
  opacity: 0.5;
  border: none;
  border-top: 1px solid;
}

.copyright {
  margin: 0;
  font-size: 13px;
  line-height: 1.3;
}

.footer-bottom-link {
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 13px;
  line-height: 1.3;
}

.footer-bottom-link:hover {
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
  .frontend-footer {
    text-align: center;
  }
  
  .footer-padding-layer {
    padding: 1.5rem 0 0.75rem 0;
  }
  
  .compact-col {
    margin-bottom: 0.75rem !important;
  }
  
  .footer-logo-container {
    justify-content: center;
  }
  
  .footer-logo-image {
    height: 30px;
  }
  
  .social-links {
    justify-content: center;
    gap: 6px;
  }
  
  .footer-heading::after {
    left: 50%;
    transform: translateX(-50%);
  }
  
  .footer-bottom {
    text-align: center;
  }
  
  .footer-bottom-link {
    margin: 0 0.5rem;
    display: inline-block;
  }
  
  .footer-link-item {
    margin-bottom: 0.125rem;
  }
  
  .contact-item {
    justify-content: center;
    text-align: center;
    padding: 0.125rem 0;
  }
}

@media (max-width: 576px) {
  .footer-padding-layer {
    padding: 1rem 0 0.5rem 0;
  }
  
  .footer-logo-image {
    height: 28px;
  }
  
  .footer-heading {
    font-size: 14px;
  }
  
  .footer-link {
    font-size: 13px;
  }
  
  .contact-text {
    font-size: 12px;
  }
  
  .social-link {
    width: 30px;
    height: 30px;
    font-size: 12px;
  }
}

/* Extra padding for very small screens */
@media (max-width: 375px) {
  .footer-padding-layer {
    padding: 0.75rem 0 0.25rem 0;
  }
  
  .container {
    padding-left: 10px;
    padding-right: 10px;
  }
}

/* Ensure Font Awesome icons are properly loaded */
:deep(.fas),
:deep(.fab) {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  display: inline-block !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  line-height: 1 !important;
}
</style>