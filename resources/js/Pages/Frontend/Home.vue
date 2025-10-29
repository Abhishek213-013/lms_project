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
                  <Link href="/registration" class="btn btn-hero-primary">
                    {{ displayContent.home_hero_primary_button }}
                  </Link>
                  <a href="#" class="btn btn-hero-secondary">
                    <i class="fas fa-play-circle"></i>
                    {{ displayContent.home_hero_secondary_button }}
                  </a>
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

      <!-- UPDATED: Instructors Section with Fixed Stats -->
      <section class="instructors-section section-py-120" v-if="instructors.length > 0">
        <div class="container">
          <div class="row mb-5">
            <div class="col-12 text-center">
              <h2 class="section-title">{{ displayContent.home_instructors_title }}</h2>
              <p class="section-subtitle">{{ displayContent.home_instructors_subtitle }}</p>
            </div>
          </div>
          
          <!-- UPDATED: Professional Instructor Cards Grid -->
          <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6" v-for="instructor in instructors.slice(0, 8)" :key="instructor.id">
              <div class="instructor-card">
                <!-- Profile Image Container -->
                <div class="profile-image-container">
                  <div class="profile-image-wrapper">
                    <img 
                      :src="getInstructorAvatar(instructor)" 
                      :alt="instructor.name"
                      class="profile-image"
                      @error="handleImageError"
                    >
                    <div class="profile-overlay">
                      <div class="profile-actions">
                        <button class="btn-quick-view" :title="t('Quick View')">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-favorite" :title="t('Add to Favorite')">
                          <i class="fas fa-heart"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Profile Content -->
                <div class="profile-content">
                  <!-- Name and Title -->
                  <div class="profile-header">
                    <h3 class="instructor-name">{{ instructor.name }}</h3>
                    <p class="instructor-title">{{ getExpertise(instructor) }}</p>
                  </div>

                  <!-- Education -->
                  <div class="profile-education">
                    <div class="education-icon">
                      <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="education-info">
                      <span class="education-label">{{ t('Education') }}</span>
                      <p class="education-text line-clamp-2">{{ getEducation(instructor) }}</p>
                    </div>
                  </div>

                  <!-- FIXED: Stats Section -->
                  <div class="profile-stats">
                    <div class="stats-grid">
                      <div class="stat-item">
                        <div class="stat-icon">
                          <i class="fas fa-book-open"></i>
                        </div>
                        <div class="stat-info">
                          <div class="stat-number">{{ instructor.courses_count || 0 }}</div>
                          <div class="stat-label">{{ t('Courses') }}</div>
                        </div>
                      </div>
                      <div class="stat-item">
                        <div class="stat-icon">
                          <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                          <div class="stat-number">{{ instructor.students_count || 0 }}</div>
                          <div class="stat-label">{{ t('Students') }}</div>
                        </div>
                      </div>
                      <div class="stat-item">
                        <div class="stat-icon">
                          <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                          <div class="stat-number">{{ instructor.rating || '4.8' }}</div>
                          <div class="stat-label">{{ t('Rating') }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="profile-actions-footer">
                    <Link :href="`/instructor/${instructor.id}`" class="btn-view-profile">
                      <i class="fas fa-user-circle"></i>
                      {{ t('View Profile') }}
                    </Link>
                    <button class="btn-contact" :title="t('Contact Instructor')">
                      <i class="fas fa-envelope"></i>
                    </button>
                  </div>
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
})

onMounted(() => {
  // Get initial theme from localStorage or system preference
  const savedTheme = localStorage.getItem('preferredTheme')
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  
  currentTheme.value = savedTheme || (systemPrefersDark ? 'dark' : 'light')
  
  // Listen for theme changes from header
  window.addEventListener('themeChanged', handleThemeChange)
  
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
/* Your existing CSS styles for other sections remain the same */

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
/* UPDATED: PROFESSIONAL INSTRUCTOR CARD */
/* ==================== */
.instructor-card {
  background: var(--card-bg);
  border-radius: 20px;
  margin-bottom: 30px;
  box-shadow: var(--shadow);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  border: 1px solid var(--border-color);
  overflow: hidden;
  height: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
}

.instructor-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
  border-color: var(--primary-color);
}

/* Profile Image Container */
.profile-image-container {
  position: relative;
  padding: 40px 30px 30px;
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
  text-align: center;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.profile-image-wrapper {
  position: relative;
  display: inline-block;
  width: 140px;
  height: 140px;
  border-radius: 20px;
  background: var(--card-bg);
  padding: 8px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
}

.instructor-card:hover .profile-image-wrapper {
  transform: scale(1.05);
  box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}

.profile-image {
  width: 100%;
  height: 100%;
  border-radius: 15px;
  object-fit: cover;
  border: 3px solid var(--bg-primary);
  transition: all 0.3s ease;
}

.instructor-card:hover .profile-image {
  border-color: var(--primary-color);
}

.profile-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.9) 0%, rgba(var(--secondary-rgb), 0.9) 100%);
  border-radius: 15px;
  opacity: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.profile-image-wrapper:hover .profile-overlay {
  opacity: 1;
}

.profile-actions {
  display: flex;
  gap: 10px;
}

.btn-quick-view,
.btn-favorite {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: white;
  color: var(--primary-color);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.btn-quick-view:hover,
.btn-favorite:hover {
  background: var(--primary-color);
  color: white;
  transform: scale(1.1);
}

/* Profile Content */
.profile-content {
  padding: 0 25px 25px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* Profile Header */
.profile-header {
  text-align: center;
  padding: 20px 0;
  border-bottom: 1px solid var(--border-light);
  margin-bottom: 20px;
  flex-shrink: 0;
}

.instructor-name {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 8px 0;
  line-height: 1.3;
  transition: color 0.3s ease;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.instructor-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary-color);
  margin: 0;
  line-height: 1.4;
  transition: color 0.3s ease;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Education Section */
.profile-education {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  padding: 15px;
  background: var(--bg-secondary);
  border-radius: 12px;
  margin-bottom: 20px;
  transition: background-color 0.3s ease;
  flex-shrink: 0;
}

.education-icon {
  width: 40px;
  height: 40px;
  background: var(--primary-color);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 16px;
  flex-shrink: 0;
  transition: background-color 0.3s ease;
}

.education-info {
  flex: 1;
  min-width: 0;
}

.education-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
  display: block;
  transition: color 0.3s ease;
}

.education-text {
  font-size: 0.85rem;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.4;
  font-weight: 500;
  transition: color 0.3s ease;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* ==================== */
/* FIXED: STATS SECTION - PROPER LABEL DISPLAY */
/* ==================== */
.profile-stats {
  margin-bottom: 20px;
  flex-shrink: 0;
}

.stats-grid {
  display: flex;
  justify-content: space-between;
  gap: 8px;
  padding: 0 2px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  flex: 1;
  padding: 10px 6px;
  background: var(--bg-secondary);
  border-radius: 10px;
  transition: background-color 0.3s ease;
  min-width: 0;
  text-align: center;
}

.stat-icon {
  width: 28px;
  height: 28px;
  background: var(--primary-color);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 11px;
  flex-shrink: 0;
  transition: background-color 0.3s ease;
}

.stat-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

/* Smaller stat numbers */
.stat-number {
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1.2;
  transition: color 0.3s ease;
}

/* Fixed: Proper label display without content replacement */
.stat-label {
  font-size: 0.65rem;
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
.profile-actions-footer {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-top: auto;
  flex-shrink: 0;
}

.btn-view-profile {
  flex: 1;
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 12px 16px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 44px;
}

.btn-view-profile:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px color-mix(in srgb, var(--primary-color) 30%, transparent);
}

.btn-contact {
  width: 44px;
  height: 44px;
  background: var(--bg-secondary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
  flex-shrink: 0;
}

.btn-contact:hover {
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
/* RESPONSIVE DESIGN */
/* ==================== */
@media (max-width: 1199px) {
  .profile-image-wrapper {
    width: 130px;
    height: 130px;
  }
  
  .stats-grid {
    gap: 6px;
  }
  
  .stat-item {
    padding: 8px 5px;
  }
  
  .stat-number {
    font-size: 0.85rem;
  }
  
  .stat-label {
    font-size: 0.6rem;
  }
}

@media (max-width: 991px) {
  .section-py-120 {
    padding: 80px 0;
  }
  
  .profile-image-wrapper {
    width: 120px;
    height: 120px;
  }
  
  .stats-grid {
    gap: 5px;
  }
  
  .stat-item {
    padding: 8px 4px;
  }
  
  .stat-icon {
    width: 26px;
    height: 26px;
    font-size: 10px;
  }
  
  .stat-number {
    font-size: 0.8rem;
  }
  
  .stat-label {
    font-size: 0.55rem;
  }
}

@media (max-width: 767px) {
  .profile-content {
    padding: 0 20px 20px;
  }
  
  .profile-image-container {
    padding: 30px 25px 25px;
  }
  
  .profile-actions-footer {
    flex-direction: column;
  }
  
  .btn-contact {
    width: 100%;
    margin-top: 10px;
  }
  
  .stats-grid {
    gap: 8px;
  }
  
  .stat-item {
    padding: 10px 6px;
  }
  
  .stat-number {
    font-size: 0.85rem;
  }
  
  .stat-label {
    font-size: 0.6rem;
  }
}

@media (max-width: 575px) {
  .instructor-card {
    margin-bottom: 20px;
  }
  
  .instructor-name {
    font-size: 1.2rem;
  }
  
  .profile-image-wrapper {
    width: 110px;
    height: 110px;
  }
  
  .profile-image-container {
    padding: 25px 20px 20px;
  }
  
  .btn-view-profile {
    padding: 10px 12px;
    font-size: 0.85rem;
  }
  
  .stats-grid {
    flex-direction: column;
    gap: 8px;
  }
  
  .stat-item {
    justify-content: center;
    padding: 8px 10px;
    flex-direction: row;
    text-align: left;
    gap: 10px;
  }
  
  .stat-item .stat-info {
    align-items: flex-start;
  }
  
  .stat-number {
    font-size: 0.9rem;
  }
  
  .stat-label {
    font-size: 0.65rem;
  }
}

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.btn:focus,
.btn-view-profile:focus,
.btn-contact:focus,
.btn-quick-view:focus,
.btn-favorite:focus {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .instructor-card,
  .btn,
  .btn-view-profile,
  .profile-image-wrapper,
  .profile-image {
    transition: none;
  }
  
  .instructor-card:hover,
  .btn:hover:not(:disabled),
  .btn-view-profile:hover,
  .btn-contact:hover,
  .btn-quick-view:hover,
  .btn-favorite:hover {
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

/* .stats-number {
  font-weight: 100;
  font-size: 1rem;
  margin-bottom: 0.5rem;
  color: white;
} */

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