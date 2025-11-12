<template>
  <FrontendLayout>
    <div class="main-area fix">
      <!-- Breadcrumb Area - Reduced Height -->
      <section class="breadcrumb__area breadcrumb__bg" :data-background="themeImagePath">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="breadcrumb__content">
                <h3 class="title">{{ displayContent.about_banner_title || t('Who We Are') }}</h3>
                <nav class="breadcrumb">
                  <span property="itemListElement" typeof="ListItem">
                    <a href="/">{{ t('Home') }}</a>
                  </span>
                  <span class="breadcrumb-separator"><i class="fas fa-angle-right icon-fixed"></i></span>
                  <span property="itemListElement" typeof="ListItem">{{ t('About Us') }}</span>
                </nav>
              </div>
            </div>
          </div>
        </div>
        <div class="breadcrumb__shape-wrap">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape01.svg" alt="img" class="alltuchtopdown">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape02.svg" alt="img" data-aos="fade-right" data-aos-delay="300" class="aos-init aos-animate">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape03.png" alt="img" data-aos="fade-up" data-aos-delay="400" class="aos-init aos-animate">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape04.png" alt="img" data-aos="fade-down-left" data-aos-delay="400" class="aos-init aos-animate">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape05.svg" alt="img" data-aos="fade-left" data-aos-delay="400" class="aos-init aos-animate">
        </div>
      </section>

      <!-- About Content Section - Centered -->
      <section class="about-area-three section-py-120">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
              <div class="about__content-three text-center">
                <div class="section__title mb-10">
                  <h2 class="title">
                    {{ displayContent.about_our_story_title }}
                  </h2>
                </div>
                <p class="desc">{{ displayContent.about_our_story_content }}</p>
                <ul class="about__info-list list-wrap justify-content-center">
                  <li class="about__info-list-item">
                    <i class="flaticon-angle-right icon-fixed"></i>
                    <p class="content">{{ t('World Class Instructors') }}</p>
                  </li>
                  <li class="about__info-list-item">
                    <i class="flaticon-angle-right icon-fixed"></i>
                    <p class="content">{{ t('Access Anywhere') }}</p>
                  </li>
                  <li class="about__info-list-item">
                    <i class="flaticon-angle-right icon-fixed"></i>
                    <p class="content">{{ t('Flexible Course Plan') }}</p>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Mission & Vision with proper spacing -->
      <section class="mission-vision-area section-pt-60 section-pb-120">
        <div class="container">
          <div class="row">
            <div class="col-lg-6">
              <div class="mission-card">
                <div class="card-icon">
                  <i class="flaticon-target icon-fixed"></i>
                </div>
                <h3>{{ displayContent.about_mission_title }}</h3>
                <p>
                  {{ displayContent.about_mission_content }}
                </p>
                <ul class="mission-list">
                  <li><i class="fas fa-check icon-fixed"></i> {{ t('Provide accessible education') }}</li>
                  <li><i class="fas fa-check icon-fixed"></i> {{ t('Foster lifelong learning') }}</li>
                  <li><i class="fas fa-check icon-fixed"></i> {{ t('Bridge skills gap') }}</li>
                  <li><i class="fas fa-check icon-fixed"></i> {{ t('Promote innovation') }}</li>
                </ul>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="vision-card">
                <div class="card-icon">
                  <i class="flaticon-vision icon-fixed"></i>
                </div>
                <h3>{{ displayContent.about_vision_title }}</h3>
                <p>
                  {{ displayContent.about_vision_content }}
                </p>
                <ul class="vision-list">
                  <li><i class="fas fa-check icon-fixed"></i> {{ t('Global learning community') }}</li>
                  <li><i class="fas fa-check icon-fixed"></i> {{ t('Personalized learning paths') }}</li>
                  <li><i class="fas fa-check icon-fixed"></i> {{ t('Industry-relevant curriculum') }}</li>
                  <li><i class="fas fa-check icon-fixed"></i> {{ t('Continuous innovation') }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </FrontendLayout>
</template>

<script setup>
import FrontendLayout from '../Layout/FrontendLayout.vue';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useTranslation } from '@/composables/useTranslation';

// Define props
const props = defineProps({
  content: {
    type: Object,
    default: () => ({})
  }
});

// Use translation composable
const { t, currentLanguage, translationVersion } = useTranslation()

// Reactive data
const loading = ref(false);
const currentTheme = ref('light');
const contentRefreshKey = ref(0);
const isDevelopment = ref(false);

// Local content state for smooth updates
const localContent = ref({})

// Initialize local content with props
onMounted(() => {
  localContent.value = { ...props.content }
})

// Computed properties
const displayContent = computed(() => {
  // Use local content if available, otherwise fallback to props
  return Object.keys(localContent.value).length > 0 ? localContent.value : props.content;
});

const themeImagePath = computed(() => {
  const theme = currentTheme.value === 'dark' ? 'dark' : 'light';
  return `/assets/img/bg/${theme}/breadcrumb_bg.jpg`;
});

// UPDATED: Language change handler that fetches fresh content from database
const handleLanguageChange = async (event) => {
  const newLanguage = event.detail.language;
  console.log('🌐 About page: Language change received:', newLanguage);
  
  try {
    // Call the API to switch language AND get fresh about content
    const response = await fetch('/switch-language-about', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        language: newLanguage
      })
    });

    const result = await response.json();
    
    if (result.success && result.content) {
      console.log('✅ About page: Language switch successful with fresh content');
      
      // Update local content with fresh database content
      localContent.value = result.content;
      
      // Force content refresh
      contentRefreshKey.value++;
      
      refreshIcons();
      
    } else {
      console.error('❌ About page: Language switch failed:', result);
    }
  } catch (error) {
    console.error('❌ Error switching language in about page:', error);
  }
};

// Methods - KEEP EXISTING LOGIC
const loadThemePreference = () => {
  const savedTheme = localStorage.getItem('preferredTheme');
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    currentTheme.value = savedTheme;
  } else {
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    currentTheme.value = systemPrefersDark ? 'dark' : 'light';
  }
};

const setupThemeListener = () => {
  window.addEventListener('themeChanged', (event) => {
    currentTheme.value = event.detail.theme;
  });
};

const refreshIcons = () => {
  // Force Font Awesome to re-render icons
  if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
    setTimeout(() => {
      window.FontAwesome.dom.i2svg();
    }, 100);
  }
};

// Lifecycle hooks - KEEP EXISTING LOGIC
onMounted(() => {
  // Check if we're in development mode
  isDevelopment.value = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
  
  loadThemePreference();
  setupThemeListener();
  console.log('About page content received:', props.content);
  
  // Listen for language changes
  window.addEventListener('languageChanged', handleLanguageChange);
  
  // Initialize icons
  refreshIcons();
});

onUnmounted(() => {
  window.removeEventListener('themeChanged', () => {});
  window.removeEventListener('languageChanged', handleLanguageChange);
});

// Watch for language changes - KEEP EXISTING LOGIC
watch(currentLanguage, (newLang, oldLang) => {
  console.log('About page: Language changed from', oldLang, 'to', newLang);
  contentRefreshKey.value++; // Force content refresh
});

// Watch translation version for reactivity - KEEP EXISTING LOGIC
watch(translationVersion, () => {
  console.log('Translation version changed, refreshing about page content');
  contentRefreshKey.value++; // Force content refresh
});

// Watch for props.content changes - KEEP EXISTING LOGIC
watch(() => props.content, (newContent, oldContent) => {
  if (newContent !== oldContent) {
    console.log('About page content props updated');
    localContent.value = { ...newContent };
    contentRefreshKey.value++;
  }
}, { deep: true });
</script>

<style scoped>
/* Your existing CSS styles remain exactly the same */
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
.fas, .fa, .far, .fab, .flaticon {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* Specific fixes for Bengali language */
:global(.bn-lang) .fas,
:global(.bn-lang) .fa,
:global(.bn-lang) .far,
:global(.bn-lang) .fab,
:global(.bn-lang) .flaticon,
:global(.bn-lang) .icon-fixed {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* Breadcrumb Area - Reduced Height */
.breadcrumb__area {
  position: relative;
  padding: 20px 0 10px;
  background-size: cover;
  background-position: center;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 150px;
  background-color: var(--bg-secondary);
}

.breadcrumb__area::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: var(--bg-secondary);
  opacity: 1;
  z-index: -1;
}

.breadcrumb__content {
  text-align: center;
  width: 100%;
}

.breadcrumb__content .title {
  font-size: 24px;
  font-weight: 400;
  color: var(--text-primary);
  margin-bottom: 10px;
  text-align: center;
  width: 100%;
}

.breadcrumb {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  color: var(--text-primary);
}

.breadcrumb a {
  color: var(--text-primary);
  text-decoration: none;
  opacity: 0.7;
}

.breadcrumb a:hover {
  color: var(--primary-color);
  opacity: 1;
}

.breadcrumb-separator {
  margin: 0 10px;
  color: var(--text-primary);
  opacity: 0.7;
}

.breadcrumb span:last-child {
  color: var(--text-primary);
  opacity: 1;
  font-weight: 600;
}

.breadcrumb__shape-wrap {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  overflow: hidden;
  z-index: 1;
}

.breadcrumb__shape-wrap img {
  position: absolute;
  max-width: none;
  opacity: 0.3;
}

/* Adjusted shape positions for smaller height */
.breadcrumb__shape-wrap img:nth-child(1) {
  top: 10%;
  left: 8%;
  width: 80px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(2) {
  top: 25%;
  right: 20%;
  width: 60px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(3) {
  bottom: 5%;
  left: 32%;
  width: 70px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(4) {
  bottom: 8%;
  right: 40%;
  width: 60px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(5) {
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100px;
  z-index: 1;
}

/* Animation for specific elements */
.alltuchtopdown {
  animation: alltuchtopdown 5s infinite linear;
}

@keyframes alltuchtopdown {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
  100% {
    transform: translateY(0px);
  }
}

/* About Area - Centered Layout */
.about-area-three {
  position: relative;
  background: var(--bg-primary);
}

.about__content-three {
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
}

.about__content-three .section__title {
  text-align: center;
}

.about__content-three .section__title .sub-title {
  color: var(--primary-color);
  font-weight: 600;
  display: block;
  margin-bottom: 10px;
  font-size: 16px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.about__content-three .section__title .title {
  font-size: 36px;
  line-height: 1.3;
  margin-bottom: 20px;
  color: var(--text-primary);
  font-weight: 700;
  min-height: 90px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.desc {
  font-size: 16px;
  line-height: 1.8;
  color: var(--text-secondary);
  margin-bottom: 30px;
  text-align: center;
  /* FIX: Set min-height to prevent layout shift */
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  word-wrap: break-word;
  overflow-wrap: break-word;
  padding: 0 10px;
}

.about__info-list {
  margin-bottom: 40px;
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 20px;
}

.about__info-list-item {
  display: flex;
  align-items: center;
  margin-bottom: 0;
}

.about__info-list-item i {
  color: var(--primary-color);
  margin-right: 10px;
  font-size: 14px;
}

.about__info-list-item .content {
  margin: 0;
  font-weight: 500;
  color: var(--text-primary);
  font-size: 16px;
}

/* Mission & Vision with proper spacing */
.mission-vision-area {
  background: var(--bg-primary);
  padding-top: 60px;
  padding-bottom: 120px;
}

.mission-card,
.vision-card {
  background: var(--card-bg);
  padding: 40px;
  border-radius: 10px;
  box-shadow: var(--shadow);
  height: 100%;
  position: relative;
  overflow: hidden;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  margin-bottom: 30px;
}

.mission-card:hover,
.vision-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.mission-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
}

.vision-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(135deg, #3498db, #2980b9);
}

.card-icon {
  width: 80px;
  height: 80px;
  background: var(--bg-secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 25px;
  border: 2px solid var(--border-color);
  margin-left: auto;
  margin-right: auto;
}

.card-icon i {
  font-size: 2rem;
  color: var(--primary-color);
}

.vision-card .card-icon i {
  color: #3498db;
}

.mission-card h3,
.vision-card h3 {
  font-size: 1.8rem;
  color: var(--text-primary);
  margin-bottom: 20px;
  font-weight: 700;
  text-align: center;
  /* FIX: Set min-height to prevent layout shift */
  min-height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.mission-card p,
.vision-card p {
  color: var(--text-secondary);
  line-height: 1.7;
  margin-bottom: 25px;
  font-size: 16px;
  text-align: center;
  /* FIX: Set min-height to prevent layout shift */
  min-height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  word-wrap: break-word;
  overflow-wrap: break-word;
  padding: 0 10px;
}

.mission-list,
.vision-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.mission-list li,
.vision-list li {
  padding: 8px 0;
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--text-primary);
  font-size: 15px;
}

.mission-list li i,
.vision-list li i {
  color: var(--success-color);
  font-size: 14px;
}

/* Debug Section Styles */
.debug-section {
  border: 2px solid #e53e3e;
  border-radius: 8px;
  font-family: monospace;
}

.debug-section pre {
  background: #f7fafc;
  padding: 10px;
  border-radius: 4px;
  overflow-x: auto;
  font-size: 12px;
}

/* Responsive Styles */
@media (max-width: 1199px) {
  .breadcrumb__content .title {
    font-size: 32px;
  }
  
  .about__content-three .section__title .title {
    font-size: 32px;
    min-height: 80px;
  }

  .desc {
    min-height: 110px;
  }
  
  .mission-card p,
  .vision-card p {
    min-height: 130px;
  }
  
  .mission-card h3,
  .vision-card h3 {
    min-height: 55px;
  }
}

@media (max-width: 991px) {
  .breadcrumb__content .title {
    font-size: 28px;
  }
  
  .about__content-three .section__title .title {
    font-size: 28px;
    min-height: 70px;
  }
  
  .desc {
    min-height: 100px;
    font-size: 15px;
  }
  
  .mission-card p,
  .vision-card p {
    min-height: 120px;
    font-size: 15px;
  }
  
  .mission-card h3,
  .vision-card h3 {
    font-size: 1.6rem;
    min-height: 50px;
  }
  
  .mission-card,
  .vision-card {
    margin-bottom: 30px;
  }
  
  .about__info-list {
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }
}

@media (max-width: 767px) {
  .breadcrumb__area {
    padding: 25px 0 15px;
    min-height: 120px;
  }
  
  .breadcrumb__content .title {
    font-size: 24px;
  }
  
  .about__content-three .section__title .title {
    font-size: 24px;
    min-height: 60px;
  }
  
  .desc {
    min-height: 90px;
    font-size: 14px;
  }
  
  .mission-card p,
  .vision-card p {
    min-height: 110px;
    font-size: 14px;
  }
  
  .mission-card h3,
  .vision-card h3 {
    font-size: 1.4rem;
    min-height: 45px;
  }
  
  .mission-vision-area {
    padding-top: 40px;
    padding-bottom: 80px;
  }
  
  .mission-card,
  .vision-card {
    padding: 30px 20px;
  }
  
  .mission-card h3,
  .vision-card h3 {
    font-size: 1.5rem;
  }
  
  .card-icon {
    width: 60px;
    height: 60px;
    margin-bottom: 20px;
  }
  
  .card-icon i {
    font-size: 1.5rem;
  }
  
  /* Further reduce shapes on mobile */
  .breadcrumb__shape-wrap img:nth-child(1) {
    width: 60px;
  }
  
  .breadcrumb__shape-wrap img:nth-child(2) {
    width: 40px;
  }
  
  .breadcrumb__shape-wrap img:nth-child(3) {
    width: 50px;
  }
  
  .breadcrumb__shape-wrap img:nth-child(4) {
    width: 40px;
  }
  
  .breadcrumb__shape-wrap img:nth-child(5) {
    width: 70px;
  }
}

@media (max-width: 575px) {
  .breadcrumb__content .title {
    font-size: 22px;
  }
  
  .about__content-three .section__title .title {
    font-size: 22px;
    min-height: 55px;
  }
  
  .desc {
    min-height: 80px;
    font-size: 14px;
  }
  
  .mission-card p,
  .vision-card p {
    min-height: 100px;
    font-size: 14px;
  }
  
  .mission-card h3,
  .vision-card h3 {
    font-size: 1.3rem;
    min-height: 40px;
  }
  
  .about__info-list-item .content {
    font-size: 14px;
  }
  
  .desc {
    font-size: 14px;
  }
  
  .mission-card p,
  .vision-card p {
    font-size: 14px;
  }
  
  .mission-list li,
  .vision-list li {
    font-size: 14px;
  }
  
  .breadcrumb__area {
    min-height: 100px;
    padding: 20px 0 15px;
  }
}

/* Dark theme specific adjustments */
.dark-theme .breadcrumb__area::before {
  background: var(--bg-secondary);
}

.dark-theme .about-image {
  filter: brightness(0.9);
}

.dark-theme .mission-card,
.dark-theme .vision-card {
  background: var(--card-bg);
}

/* RTL support for Bengali */
.bn-lang .about__content-three .section__title .title {
  line-height: 1.4; /* Slightly tighter line height for Bengali */
  font-size: 34px; /* Slightly smaller font for longer Bengali text */
}
.bn-lang .desc {
  line-height: 1.7; /* Adjusted line height for Bengali */
  font-size: 15px; /* Slightly smaller font for longer text */
}

.bn-lang .mission-card p,
.bn-lang .vision-card p {
  line-height: 1.6; /* Adjusted line height for Bengali */
  font-size: 15px; /* Slightly smaller font for longer text */
}
.bn-lang .about__content-three .section__title .title,
.bn-lang .desc,
.bn-lang .mission-card p,
.bn-lang .vision-card p,
.bn-lang .mission-card h3,
.bn-lang .vision-card h3 {
  word-break: break-word;
  hyphens: auto;
}
.bn-lang .about__content-three .section__title,
.bn-lang .about__info-list-item,
.bn-lang .mission-list li,
.bn-lang .vision-list li {
  text-align: right;
}

.bn-lang .about__info-list-item i {
  margin-right: 0;
  margin-left: 10px;
}

/* Print styles */
@media print {
  .breadcrumb__shape-wrap,
  .btn.arrow-btn {
    display: none;
  }
  
  .mission-card,
  .vision-card {
    box-shadow: none;
    border: 1px solid #ccc;
  }
}
</style>