<template>
  <FrontendLayout>
    <div class="home-page">
      <!-- Hero Section -->
      <section 
        class="hero-section"
        :style="heroSectionStyle"
      >
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <div class="hero-content">
                <h1 class="hero-title">{{ displayContent.home_hero_title }}</h1>
                <p class="hero-subtitle">{{ displayContent.home_hero_subtitle }}</p>
                
                <div class="hero-actions">
                  <Link href="/courses" class="btn btn-hero-primary">
                    {{ displayContent.home_hero_primary_button }}
                  </Link>
                  <Link href="/instructors" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-play-circle"></i>
                    {{ displayContent.home_instructors_button }}
                  </Link>
                </div>
                
                <div class="hero-stats">
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_students.toLocaleString() }}+</span>
                    <span class="stat-label">{{ t('Students') }}</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_courses }}+</span>
                    <span class="stat-label">{{ t('Courses') }}</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_instructors }}+</span>
                    <span class="stat-label">{{ t('Instructors') }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="hero-image">
                <img src="/assets/img/h2_banner_img.png" :alt="t('Online Learning')" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Popular Courses -->
      <section class="courses-section py-5">
        <div class="container">
          <div class="row mb-5">
            <div class="col-12 text-center">
              <h2 class="section-title">{{ displayContent.home_courses_title }}</h2>
              <p class="section-subtitle">{{ displayContent.home_courses_subtitle }}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-4" v-for="course in featuredCourses" :key="course.id">
              <div class="card course-card h-100">
                <img :src="course.thumbnail" class="card-img-top" :alt="course.name" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                  <h5 class="card-title">{{ course.name }}</h5>
                  <p class="card-text">{{ course.description }}</p>
                  <div class="course-meta d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary">{{ course.category }}</span>
                    <span class="course-type">{{ course.type }}</span>
                  </div>
                  <div class="course-info d-flex justify-content-between align-items-center">
                    <small class="students-count">
                      <i class="fas fa-users"></i> {{ course.student_count }} {{ t('students') }}
                    </small>
                    <strong class="course-price">${{ course.fee }}</strong>
                  </div>
                </div>
                <div class="card-footer">
                  <Link :href="`/course/${course.id}`" class="btn btn-primary w-100">
                    {{ t('View Course') }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-12 text-center">
              <Link href="/courses" class="btn btn-outline-primary btn-lg">
                {{ displayContent.home_courses_button }}
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- FIXED: Instructors Section with Full-Size Avatar Images (No Head Cropping) -->
      <section class="instructors-section section-py-120" v-if="instructors.length > 0">
        <div class="container">
          <div class="row mb-5">
            <div class="col-12 text-center">
              <h2 class="section-title">{{ displayContent.home_instructors_title }}</h2>
              <p class="section-subtitle">{{ displayContent.home_instructors_subtitle }}</p>
            </div>
          </div>
          
          <!-- FIXED: Instructor Cards with Full-Size Avatar Images (No Head Cropping) -->
          <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6" v-for="instructor in instructors.slice(0, 8)" :key="instructor.id">
              <div class="instructor-card-new">
                <!-- Profile Header with Full-Size Image - NO HEAD CROPPING -->
                <div class="profile-header-full">
                  <div class="profile-image-full-container">
                    <img 
                      :src="getInstructorAvatar(instructor)" 
                      :alt="instructor.name"
                      class="profile-image-full"
                      @error="handleImageError"
                    >
                    <div class="profile-overlay-full">
                      <div class="profile-info-overlay">
                        <h3 class="instructor-name-overlay">{{ instructor.name }}</h3>
                        <p class="instructor-title-overlay">{{ getExpertise(instructor) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Education Section -->
                <div class="education-section-new">
                  <div class="section-header">
                    <i class="fas fa-graduation-cap icon-fixed"></i>
                    <span class="section-title-small">{{ t('Education') }}</span>
                  </div>
                  <p class="education-text-new line-clamp-2">{{ getEducation(instructor) }}</p>
                </div>

                <!-- Stats Section -->
                <div class="stats-section-new">
                  <div class="section-header">
                    <i class="fas fa-chart-bar icon-fixed"></i>
                    <span class="section-title-small">{{ t('Stats') }}</span>
                  </div>
                  <div class="stats-grid-new">
                    <div class="stat-item-new">
                      <div class="stat-icon-new">
                        <i class="fas fa-book-open icon-fixed"></i>
                      </div>
                      <div class="stat-info-new">
                        <div class="stat-number-new">{{ instructor.courses_count || 0 }}</div>
                        <div class="stat-label-new">{{ t('Courses') }}</div>
                      </div>
                    </div>
                    <div class="stat-item-new">
                      <div class="stat-icon-new">
                        <i class="fas fa-users icon-fixed"></i>
                      </div>
                      <div class="stat-info-new">
                        <div class="stat-number-new">{{ instructor.students_count || 0 }}</div>
                        <div class="stat-label-new">{{ t('Students') }}</div>
                      </div>
                    </div>
                    <div class="stat-item-new">
                      <div class="stat-icon-new">
                        <i class="fas fa-star icon-fixed"></i>
                      </div>
                      <div class="stat-info-new">
                        <div class="stat-number-new">{{ instructor.rating || '4.8' }}</div>
                        <div class="stat-label-new">{{ t('Rating') }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="profile-actions-footer-new">
                  <Link :href="`/instructor/${instructor.id}`" class="btn-view-profile-new">
                    <i class="fas fa-user-circle icon-fixed"></i>
                    {{ t('View Profile') }}
                  </Link>
                  <button class="btn-contact-new" :title="t('Contact Instructor')">
                    <i class="fas fa-envelope icon-fixed"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- View All Instructors Button -->
          <div class="row mt-4">
            <div class="col-12 text-center">
              <Link href="/instructors" class="btn btn-outline-primary btn-lg">
                {{ displayContent.home_instructors_button }}
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Section -->
      <section class="stats-section py-5">
        <div class="container">
          <div class="row text-center">
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_students.toLocaleString() }}+</h3>
              <p class="stats-label">{{ t('Students Enrolled') }}</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_instructors }}+</h3>
              <p class="stats-label">{{ t('Expert Instructors') }}</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_courses }}+</h3>
              <p class="stats-label">{{ t('Courses Available') }}</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_enrollments.toLocaleString() }}+</h3>
              <p class="stats-label">{{ t('Total Enrollments') }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="cta-section py-5">
        <div class="container">
          <div class="row text-center">
            <div class="col-12">
              <h2 class="cta-title">{{ displayContent.home_cta_title }}</h2>
              <p class="cta-subtitle">{{ displayContent.home_cta_subtitle }}</p>
              <Link href="/registration" class="btn btn-primary btn-lg">
                {{ displayContent.home_cta_button }}
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { ref, onMounted, computed, onUnmounted, watch } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

// Use translation composable
const { t, currentLanguage } = useTranslation()

// Define props
const props = defineProps({
  content: {
    type: Object,
    default: () => ({})
  },
  featuredCourses: Array,
  instructors: Array,
  stats: Object,
  testimonials: Array
})

// Theme state
const currentTheme = ref('light')

// Computed property for dynamic content with fallbacks
const displayContent = computed(() => {
  return Object.keys(props.content).length > 0 ? props.content : getDefaultContent();
})

// Computed property for hero section background
const heroSectionStyle = computed(() => {
  const isDark = currentTheme.value === 'dark'
  const lightBg = 'linear-gradient(135deg, rgba(245, 247, 250, 0.9) 0%, rgba(228, 232, 240, 0.9) 100%), url(\'/assets/img/banner/banner_bg02.png\') center/cover no-repeat'
  const darkBg = 'linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%), url(\'/assets/img/banner/banner_bg02.png\') center/cover no-repeat'
  
  return {
    background: isDark ? darkBg : lightBg
  }
})

// Default content fallback
const getDefaultContent = () => {
  return {
    home_hero_title: t('Learning is What You Make of it. Make it Yours at SkillGro.'),
    home_hero_subtitle: t('Unlock your potential with our expert-led courses and transform your career.'),
    home_hero_primary_button: t('Start Free Trial'),
    home_hero_secondary_button: t('Watch Our Class Demo'),
    home_courses_title: t('Popular Courses'),
    home_courses_subtitle: t('Discover our most enrolled courses'),
    home_courses_button: t('View All Courses'),
    home_instructors_title: t('Meet Our Expert Instructors'),
    home_instructors_subtitle: t('Learn from experienced professionals'),
    home_instructors_button: t('View All Instructors'),
    home_stats_title: t('Our Impact'),
    home_cta_title: t('Ready to Start Learning?'),
    home_cta_subtitle: t('Join thousands of students already learning with SkillGro'),
    home_cta_button: t('Get Started Today'),
  }
}

// Handle theme changes from global system
const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme
}

// Watch for language changes to trigger re-render
watch(currentLanguage, () => {
  // This will trigger reactive updates in the template
  console.log('Language changed, refreshing icons...')
  refreshIcons()
})

// Icon refresh function
const refreshIcons = () => {
  // Force Font Awesome to re-check for icons
  if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
    setTimeout(() => {
      window.FontAwesome.dom.i2svg()
    }, 100)
  }
}

onMounted(() => {
  // Get initial theme from localStorage or system preference
  const savedTheme = localStorage.getItem('preferredTheme')
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  
  currentTheme.value = savedTheme || (systemPrefersDark ? 'dark' : 'light')
  
  // Listen for theme changes from header
  window.addEventListener('themeChanged', handleThemeChange)
  
  // Initialize icons
  refreshIcons()
  
  console.log('Home page content received:', props.content)
})

onUnmounted(() => {
  window.removeEventListener('themeChanged', handleThemeChange)
})

// Methods for instructor data
const getInstructorAvatar = (instructor) => {
  if (instructor.avatar && instructor.avatar !== '/assets/img/instructors/default.jpg') {
    return instructor.avatar;
  }
  
  const avatars = [
    '/assets/img/instructor/instructor01.png',
    '/assets/img/instructor/instructor02.png',
    '/assets/img/instructor/instructor03.png',
    '/assets/img/instructor/instructor04.png'
  ];
  
  const avatarIndex = ((instructor.id || 1) - 1) % avatars.length;
  return avatars[avatarIndex];
}

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y3ZmFmYyIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5Y2EwYTYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjM1ZW0iPuKKojwvdGV4dD48L3N2Zz4=';
}

const getExpertise = (instructor) => {
  // Extract expertise from qualification or use default
  const qual = instructor.education_qualification || '';
  if (qual.includes('Science')) return t('Science Expert');
  if (qual.includes('English')) return t('English Specialist');
  if (qual.includes('Mathematics')) return t('Mathematics Teacher');
  if (qual.includes('Bangla')) return t('Bangla Instructor');
  if (qual.includes('Sports')) return t('Sports Coach');
  return t('Teaching Specialist');
}

const getEducation = (instructor) => {
  if (instructor.education_qualification && instructor.education_qualification !== 'Not specified') {
    return instructor.education_qualification;
  }
  return t('Teaching Degree');
}
</script>

<style scoped>
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

/* ==================== */
/* UPDATED: INSTRUCTOR SECTION STYLES */
/* ==================== */
.instructors-section {
  position: relative;
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

.section-py-120 {
  padding: 120px 0;
}

/* ==================== */
/* FIXED: INSTRUCTOR CARD WITH FULL-SIZE AVATAR IMAGES - NO HEAD CROPPING */
/* ==================== */
.instructor-card-new {
  background: var(--card-bg);
  border-radius: 20px;
  margin-bottom: 30px;
  box-shadow: var(--shadow);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  border: 1px solid var(--border-color);
  overflow: hidden;
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 0;
}

.instructor-card-new:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
}

/* Profile Header with Full-Size Image - NO HEAD CROPPING */
.profile-header-full {
  position: relative;
  height: 200px;
  overflow: hidden;
  flex-shrink: 0;
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
  padding: 10px; /* Padding around the image */
}

.profile-image-full-container {
  width: 100%;
  height: 100%;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  border-radius: 12px;
  background: var(--bg-secondary); /* Background color for empty spaces */
}

/* FIXED: Changed object-fit from 'cover' to 'contain' to prevent head cropping */
.profile-image-full {
  width: auto;
  height: auto;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain; /* CHANGED: This prevents head cropping */
  transition: all 0.3s ease;
  border-radius: 8px;
}

.instructor-card-new:hover .profile-image-full {
  transform: scale(1.05);
}

.profile-overlay-full {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, transparent 40%, rgba(0, 0, 0, 0.8) 100%);
  display: flex;
  align-items: flex-end;
  padding: 20px;
  transition: all 0.3s ease;
  border-radius: 12px;
}

.profile-info-overlay {
  width: 100%;
  text-align: center;
}

.instructor-name-overlay {
  font-size: 1.4rem;
  font-weight: 700;
  color: white;
  margin: 0 0 5px 0;
  line-height: 1.3;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.instructor-title-overlay {
  font-size: 1rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
  line-height: 1.4;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Section Headers */
.section-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid var(--border-light);
}

.section-header i {
  color: var(--primary-color);
  font-size: 14px;
  width: 16px;
  text-align: center;
}

.section-title-small {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-primary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Education Section */
.education-section-new {
  padding: 25px;
  background: var(--bg-secondary);
  margin: 0;
  border-bottom: 1px solid var(--border-light);
  flex-shrink: 0;
}

.education-text-new {
  font-size: 0.9rem;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.5;
  font-weight: 500;
  transition: color 0.3s ease;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Stats Section */
.stats-section-new {
  padding: 25px;
  background: var(--bg-secondary);
  margin: 0;
  flex-shrink: 0;
}

.stats-grid-new {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.stat-item-new {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  flex: 1;
  padding: 12px 8px;
  background: var(--card-bg);
  border-radius: 12px;
  transition: background-color 0.3s ease;
  min-width: 0;
  text-align: center;
  border: 1px solid var(--border-light);
}

.stat-item-new:hover {
  background: var(--bg-tertiary);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-icon-new {
  width: 32px;
  height: 32px;
  background: var(--primary-color);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 12px;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.stat-info-new {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.stat-number-new {
  font-size: 1rem !important;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1.2;
  transition: color 0.3s ease;
}

.stat-label-new {
  font-size: 0.7rem !important;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  transition: color 0.3s ease;
  line-height: 1.2;
  word-wrap: break-word;
  overflow-wrap: break-word;
  display: block;
}

/* Action Buttons */
.profile-actions-footer-new {
  display: flex;
  gap: 12px;
  align-items: center;
  padding: 25px;
  background: var(--card-bg);
  margin-top: auto;
  flex-shrink: 0;
}

.btn-view-profile-new {
  flex: 1;
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 14px 20px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 48px;
}

.btn-view-profile-new:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px color-mix(in srgb, var(--primary-color) 30%, transparent);
}

.btn-contact-new {
  width: 48px;
  height: 48px;
  background: var(--bg-secondary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 16px;
  flex-shrink: 0;
}

.btn-contact-new:hover {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
  transform: translateY(-2px);
}

/* ==================== */
/* LINE-CLAMP UTILITIES */
/* ==================== */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  display: -webkit-box;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ==================== */
/* RESPONSIVE DESIGN FOR FULL-SIZE AVATARS - NO HEAD CROPPING */
/* ==================== */
@media (max-width: 1199px) {
  .profile-header-full {
    height: 180px;
  }
  
  .stats-grid-new {
    gap: 8px;
  }
  
  .stat-item-new {
    padding: 10px 6px;
  }
}

@media (max-width: 991px) {
  .profile-header-full {
    height: 160px;
  }
  
  .instructor-name-overlay {
    font-size: 1.2rem;
  }
  
  .instructor-title-overlay {
    font-size: 0.9rem;
  }
  
  .education-section-new,
  .stats-section-new {
    padding: 20px;
  }
  
  .profile-actions-footer-new {
    padding: 20px;
  }
  
  .stats-grid-new {
    gap: 5px;
  }
  
  .stat-item-new {
    padding: 8px 4px;
  }
}

@media (max-width: 767px) {
  .profile-header-full {
    height: 140px;
  }
  
  .instructor-name-overlay {
    font-size: 1.1rem;
  }
  
  .instructor-title-overlay {
    font-size: 0.85rem;
  }
  
  .profile-overlay-full {
    padding: 15px;
  }
  
  .education-section-new,
  .stats-section-new {
    padding: 15px;
  }
  
  .stats-grid-new {
    gap: 6px;
  }
  
  .stat-item-new {
    padding: 8px 4px;
  }
  
  .stat-number-new {
    font-size: 0.9rem !important;
  }
  
  .stat-label-new {
    font-size: 0.65rem !important;
  }
  
  .profile-actions-footer-new {
    padding: 15px;
    flex-direction: column;
  }
  
  .btn-contact-new {
    width: 100%;
    margin-top: 10px;
  }
}

@media (max-width: 575px) {
  .instructor-card-new {
    margin-bottom: 20px;
  }
  
  .profile-header-full {
    height: 120px;
  }
  
  .instructor-name-overlay {
    font-size: 1rem;
  }
  
  .instructor-title-overlay {
    font-size: 0.8rem;
  }
  
  .profile-overlay-full {
    padding: 10px;
  }
  
  .stats-grid-new {
    flex-direction: column;
    gap: 8px;
  }
  
  .stat-item-new {
    flex-direction: row;
    justify-content: flex-start;
    text-align: left;
    padding: 12px 15px;
  }
  
  .stat-info-new {
    align-items: flex-start;
  }
}

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.btn:focus,
.btn-view-profile-new:focus,
.btn-contact-new:focus {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .instructor-card-new,
  .btn,
  .btn-view-profile-new,
  .profile-image-full {
    transition: none;
  }
  
  .instructor-card-new:hover,
  .btn:hover:not(:disabled),
  .btn-view-profile-new:hover,
  .btn-contact-new:hover {
    transform: none;
  }
}

/* Your existing CSS styles for other sections remain below */
.hero-section {
  padding: 120px 0 80px;
  position: relative;
  overflow: hidden;
  min-height: 80vh;
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, rgba(245, 247, 250, 0.9) 0%, rgba(228, 232, 240, 0.9) 100%), url('/assets/img/banner/banner_bg02.png') center/cover no-repeat;
}

.hero-content {
  max-width: 600px;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
}

.hero-subtitle {
  font-size: 1.25rem;
  color: var(--text-secondary);
  margin-bottom: 2.5rem;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 3rem;
}

.btn-hero-primary {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  color: white;
  padding: 0.75rem 2rem;
  font-weight: 600;
  border-radius: 50px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-hero-primary:hover {
  background-color: var(--primary-hover);
  border-color: var(--primary-hover);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
}

.btn-hero-secondary {
  background-color: transparent;
  color: var(--primary-color);
  border: 2px solid var(--primary-color);
  padding: 0.75rem 2rem;
  font-weight: 600;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-hero-secondary:hover {
  background-color: var(--primary-color);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
}

.hero-stats {
  display: flex;
  gap: 2rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
}

.stat-label {
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.hero-image {
  position: relative;
  text-align: center;
  animation: float 3s ease-in-out infinite;
}

.hero-image img {
  max-width: 100%;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

/* Section Titles */
.section-title {
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.section-subtitle {
  color: var(--text-secondary);
  font-size: 1.1rem;
}

/* Course Cards */
.courses-section {
  background: var(--bg-secondary);
}

.course-card {
  transition: transform 0.3s ease;
  border: none;
  box-shadow: var(--shadow);
  background: var(--card-bg);
  border: 1px solid var(--border-color);
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.card-title {
  color: var(--text-primary);
  font-weight: 600;
}

.card-text {
  color: var(--text-secondary);
}

.course-meta .badge {
  background: var(--primary-color);
}

.course-type {
  color: var(--text-muted);
}

.students-count {
  color: var(--text-muted);
}

.course-price {
  color: var(--primary-color);
}

.card-footer {
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-color);
}

/* Stats Section */
.stats-section {
  background: var(--primary-color);
  color: white;
}

.stats-label {
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
}

/* CTA Section */
.cta-section {
  background: var(--bg-primary);
}

.cta-title {
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.cta-subtitle {
  color: var(--text-secondary);
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

/* Buttons */
.btn-outline-primary {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.btn-outline-primary:hover {
  background: var(--primary-color);
  color: white;
}

/* Dark theme overrides */
.dark-theme .hero-section {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%), url('/assets/img/banner/banner_bg02.png') center/cover no-repeat !important;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-section {
    padding: 80px 0 60px;
    min-height: auto;
    text-align: center;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .hero-stats {
    justify-content: center;
  }
  
  .stat-number {
    font-size: 1.75rem;
  }
  
  .hero-image {
    margin-top: 3rem;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  .btn-hero-primary,
  .btn-hero-secondary {
    padding: 0.6rem 1.5rem;
    font-size: 0.9rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>