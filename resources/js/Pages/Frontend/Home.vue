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
                <h1 class="hero-title">Learning is What You Make of it. Make it Yours at SkillGro.</h1>
                <p class="hero-subtitle">Unlock your potential with our expert-led courses and transform your career.</p>
                
                <div class="hero-actions">
                  <Link href="/registration" class="btn btn-hero-primary">Start Free Trial</Link>
                  <a href="#" class="btn btn-hero-secondary">
                    <i class="fas fa-play-circle"></i>
                    Watch Our Class Demo
                  </a>
                </div>
                
                <div class="hero-stats">
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_students.toLocaleString() }}+</span>
                    <span class="stat-label">Students</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_courses }}+</span>
                    <span class="stat-label">Courses</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_instructors }}+</span>
                    <span class="stat-label">Instructors</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="hero-image">
                <img src="/assets/img/h2_banner_img.png" alt="Online Learning" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section class="features-section py-5">
        <div class="container">
          <div class="row text-center mb-5">
            <div class="col-12">
              <h2 class="section-title">Why Choose SkillGro?</h2>
              <p class="section-subtitle">Learn from industry experts and advance your career</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-4" v-for="feature in features" :key="feature.id">
              <div class="feature-card text-center p-4">
                <div class="feature-icon mb-3">
                  <i :class="feature.icon"></i>
                </div>
                <h5 class="feature-title">{{ feature.title }}</h5>
                <p class="feature-description">{{ feature.description }}</p>
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
              <h2 class="section-title">Popular Courses</h2>
              <p class="section-subtitle">Discover our most enrolled courses</p>
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
                      <i class="fas fa-users"></i> {{ course.student_count }} students
                    </small>
                    <strong class="course-price">${{ course.fee }}</strong>
                  </div>
                </div>
                <div class="card-footer">
                  <Link :href="`/course/${course.id}`" class="btn btn-primary w-100">
                    View Course
                  </Link>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-12 text-center">
              <Link href="/courses" class="btn btn-outline-primary btn-lg">
                View All Courses
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- Instructors Section -->
      <section class="instructors-section py-5" v-if="instructors.length > 0">
        <div class="container">
          <div class="row mb-5">
            <div class="col-12 text-center">
              <h2 class="section-title">Meet Our Expert Instructors</h2>
              <p class="section-subtitle">Learn from experienced professionals</p>
            </div>
          </div>
          
          <!-- Instructors Grid -->
          <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6" v-for="instructor in instructors.slice(0, 8)" :key="instructor.id">
              <div class="instructor-tile">
                <!-- Profile Image -->
                <div class="tile-image">
                  <img 
                    :src="getInstructorAvatar(instructor)" 
                    :alt="instructor.name"
                    @error="handleImageError"
                  >
                </div>

                <!-- Name -->
                <div class="tile-section name-section">
                  <h3 class="instructor-name">{{ instructor.name }}</h3>
                </div>

                <!-- Expertise -->
                <div class="tile-section expertise-section">
                  <div class="section-label">Expertise</div>
                  <p class="expertise-text">{{ getExpertise(instructor) }}</p>
                </div>

                <!-- Education -->
                <div class="tile-section education-section">
                  <div class="section-label">Education</div>
                  <p class="education-text">{{ getEducation(instructor) }}</p>
                </div>

                <!-- Stats -->
                <div class="tile-section stats-section">
                  <div class="stats-grid">
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.courses_count || 0 }}</div>
                      <div class="stat-label">Courses</div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.students_count || 0 }}</div>
                      <div class="stat-label">Students</div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.rating || '4.8' }}</div>
                      <div class="stat-label">Rating</div>
                    </div>
                  </div>
                </div>

                <!-- Social Links -->
                <div class="tile-section social-section">
                  <div class="social-links">
                    <a href="#" class="social-link">
                      <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link">
                      <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link">
                      <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link">
                      <i class="fab fa-instagram"></i>
                    </a>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="tile-actions">
                  <Link :href="`/instructor/${instructor.id}`" class="btn-view-profile">
                    View Full Profile
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- View All Instructors Button -->
          <div class="row mt-4">
            <div class="col-12 text-center">
              <Link href="/instructors" class="btn btn-outline-primary btn-lg">
                View All Instructors
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
              <p class="stats-label">Students Enrolled</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_instructors }}+</h3>
              <p class="stats-label">Expert Instructors</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_courses }}+</h3>
              <p class="stats-label">Courses Available</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_enrollments.toLocaleString() }}+</h3>
              <p class="stats-label">Total Enrollments</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Testimonials Section -->
      <section class="testimonials-section py-5" v-if="testimonials.length > 0">
        <div class="container">
          <div class="row mb-5">
            <div class="col-12 text-center">
              <h2 class="section-title">What Our Students Say</h2>
              <p class="section-subtitle">Hear from our successful students</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-4" v-for="testimonial in testimonials" :key="testimonial.id">
              <div class="testimonial-card p-4">
                <div class="testimonial-content mb-3">
                  <p class="testimonial-text">"{{ testimonial.content }}"</p>
                </div>
                <div class="testimonial-author d-flex align-items-center">
                  <img :src="testimonial.avatar" :alt="testimonial.name" class="testimonial-avatar">
                  <div>
                    <h6 class="author-name">{{ testimonial.name }}</h6>
                    <small class="author-role">{{ testimonial.role }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="cta-section py-5">
        <div class="container">
          <div class="row text-center">
            <div class="col-12">
              <h2 class="cta-title">Ready to Start Learning?</h2>
              <p class="cta-subtitle">Join thousands of students already learning with SkillGro</p>
              <Link href="/registration" class="btn btn-primary btn-lg">
                Get Started Today
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
import { ref, onMounted, computed, onUnmounted } from 'vue'

// Theme state
const currentTheme = ref('light')

// Computed property for hero section background
const heroSectionStyle = computed(() => {
  const isDark = currentTheme.value === 'dark'
  const lightBg = 'linear-gradient(135deg, rgba(245, 247, 250, 0.9) 0%, rgba(228, 232, 240, 0.9) 100%), url(\'/assets/img/banner/banner_bg02.png\') center/cover no-repeat'
  const darkBg = 'linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%), url(\'/assets/img/banner/banner_bg02.png\') center/cover no-repeat'
  
  return {
    background: isDark ? darkBg : lightBg
  }
})

// Handle theme changes from global system
const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme
}

onMounted(() => {
  // Get initial theme from localStorage or system preference
  const savedTheme = localStorage.getItem('preferredTheme')
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  
  currentTheme.value = savedTheme || (systemPrefersDark ? 'dark' : 'light')
  
  // Listen for theme changes from header
  window.addEventListener('themeChanged', handleThemeChange)
})

onUnmounted(() => {
  window.removeEventListener('themeChanged', handleThemeChange)
})

// Define props
defineProps({
  featuredCourses: Array,
  instructors: Array,
  stats: Object,
  testimonials: Array
})

// Features data
const features = [
  {
    id: 1,
    title: 'Expert Instructors',
    description: 'Learn from industry professionals with real-world experience',
    icon: 'fas fa-chalkboard-teacher fa-3x'
  },
  {
    id: 2,
    title: 'Flexible Learning',
    description: 'Study at your own pace with 24/7 access to course materials',
    icon: 'fas fa-laptop-house fa-3x'
  },
  {
    id: 3,
    title: 'Career Support',
    description: 'Get career guidance and certification for your achievements',
    icon: 'fas fa-graduation-cap fa-3x'
  }
]

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
  if (qual.includes('Science')) return 'Science';
  if (qual.includes('English')) return 'English';
  if (qual.includes('Mathematics')) return 'Mathematics';
  if (qual.includes('Bangla')) return 'Bangla';
  if (qual.includes('Sports ')) return 'Sports ';
  return 'Teaching Specialist';
}

const getEducation = (instructor) => {
  if (instructor.education_qualification && instructor.education_qualification !== 'Not specified') {
    return instructor.education_qualification;
  }
  return 'Teaching Degree';
}
</script>

<style scoped>
/* Base styles */
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

/* Feature Cards */
.feature-card {
  border-radius: 15px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid var(--border-color);
  background: var(--card-bg);
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.feature-icon {
  color: var(--primary-color);
}

.feature-title {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.feature-description {
  color: var(--text-secondary);
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

/* Instructor Tile Styles */
.instructors-section {
  background: var(--bg-primary);
}

.instructor-tile {
  background: var(--card-bg);
  border-radius: 15px;
  padding: 0;
  margin-bottom: 30px;
  box-shadow: var(--shadow);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  text-align: center;
  overflow: hidden;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.instructor-tile:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

/* Profile Image - Centered */
.tile-image {
  padding: 30px 30px 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

.tile-image img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid var(--bg-secondary);
  transition: all 0.3s ease;
  display: block;
  margin: 0 auto;
}

.instructor-tile:hover .tile-image img {
  border-color: var(--primary-color);
  transform: scale(1.05);
}

/* Tile Sections */
.tile-section {
  padding: 15px 25px;
  border-top: 1px solid var(--border-light);
  width: 100%;
  text-align: center;
}

/* Name Section */
.name-section {
  border-top: none;
  padding-bottom: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.instructor-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.3;
  text-align: center;
}

/* Section Labels */
.section-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 5px;
  text-align: center;
}

/* Expertise Section */
.expertise-section {
  padding-top: 10px;
  padding-bottom: 10px;
  text-align: center;
}

.expertise-text {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary-color);
  margin: 0;
  line-height: 1.4;
  text-align: center;
}

/* Education Section */
.education-section {
  padding-top: 10px;
  padding-bottom: 10px;
  text-align: center;
}

.education-text {
  font-size: 0.85rem;
  color: var(--text-secondary);
  margin: 0;
  line-height: 1.4;
  font-weight: 500;
  text-align: center;
}

/* Stats Section */
.stats-section {
  padding: 20px 25px;
  background: var(--bg-secondary);
  text-align: center;
}

.stats-grid {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 100%;
}

.stat-item {
  text-align: center;
  flex: 1;
}

.stat-number {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 2px;
  display: block;
}

.stat-label {
  font-size: 0.75rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  display: block;
}

/* Social Section */
.social-section {
  padding: 15px 25px;
  background: var(--bg-secondary);
  text-align: center;
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.social-link {
  width: 36px;
  height: 36px;
  background: var(--card-bg);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  font-size: 14px;
}

.social-link:hover {
  background: var(--primary-color);
  color: #ffffff;
  transform: translateY(-2px);
  border-color: var(--primary-color);
}

/* Action Buttons */
.tile-actions {
  padding: 20px 25px;
  width: 100%;
  text-align: center;
}

.btn-view-profile {
  display: block;
  width: 100%;
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  text-align: center;
  max-width: 200px;
  margin: 0 auto;
}

.btn-view-profile:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
}

/* Stats Section */
.stats-section {
  background: var(--primary-color);
  color: white;
}

.stats-number {
  font-weight: 700;
  font-size: 3rem;
  margin-bottom: 0.5rem;
  color: white;
}

.stats-label {
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
}

/* Testimonials */
.testimonials-section {
  background: var(--bg-secondary);
}

.testimonial-card {
  background: var(--card-bg);
  border-radius: 12px;
  box-shadow: var(--shadow);
  transition: transform 0.3s ease;
  border: 1px solid var(--border-color);
}

.testimonial-card:hover {
  transform: translateY(-5px);
}

.testimonial-text {
  font-style: italic;
  color: var(--text-primary);
  margin: 0;
}

.testimonial-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 1rem;
}

.author-name {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.author-role {
  color: var(--text-muted);
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

/* Dark theme overrides using CSS variables - no need for :global or :deep */
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

  .tile-image {
    padding: 25px 25px 15px;
  }
  
  .tile-image img {
    width: 90px;
    height: 90px;
  }
  
  .btn-view-profile {
    padding: 10px 15px;
    font-size: 0.85rem;
  }
  
  .stat-number {
    font-size: 1.1rem;
  }
  
  .stat-label {
    font-size: 0.7rem;
  }
  
  .stats-number {
    font-size: 2rem;
  }
}
</style>