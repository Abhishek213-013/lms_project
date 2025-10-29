<template>
  <div class="my-courses-page">
    <Head title="My Courses" />
    
    <!-- Header -->
    <div class="courses-header">
      <div class="container">
        <div class="header-content">
          <h1 class="page-title">{{ t('My Courses') }}</h1>
          <p class="page-subtitle">{{ t('Continue your learning journey') }}</p>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="courses-layout">
        <!-- Sidebar -->
        <div class="courses-sidebar">
          <div class="sidebar-header">
            <div class="student-info">
              <div class="student-avatar">
                <i class="fas fa-user-circle"></i>
              </div>
              <div class="student-details">
                <div class="student-name">{{ $page.props.auth.user.name }}</div>
                <div class="student-email">{{ $page.props.auth.user.email }}</div>
              </div>
            </div>
          </div>

          <nav class="sidebar-nav">
            <Link href="/student-profile" class="nav-item">
              <i class="fas fa-user"></i>
              <span class="nav-text">{{ t('My Profile') }}</span>
            </Link>
            
            <Link href="/my-courses" class="nav-item active">
              <i class="fas fa-book"></i>
              <span class="nav-text">{{ t('My Courses') }}</span>
            </Link>
            
            <Link href="/learning-progress" class="nav-item">
              <i class="fas fa-chart-line"></i>
              <span class="nav-text">{{ t('Learning Progress') }}</span>
            </Link>
            
            <Link href="/certificates" class="nav-item">
              <i class="fas fa-certificate"></i>
              <span class="nav-text">{{ t('Certificates') }}</span>
            </Link>
            
            <Link href="/settings" class="nav-item">
              <i class="fas fa-cog"></i>
              <span class="nav-text">{{ t('Settings') }}</span>
            </Link>
            
            <div class="nav-divider"></div>
            
            <button class="nav-item logout" @click="logout">
              <i class="fas fa-sign-out-alt"></i>
              <span class="nav-text">{{ t('Logout') }}</span>
            </button>
          </nav>
        </div>

        <!-- Main Content -->
        <div class="courses-main-content">
          <!-- Tabs -->
          <div class="courses-tabs">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              :class="['tab-btn', { 'active': activeTab === tab.id }]"
              @click="activeTab = tab.id"
            >
              <i :class="tab.icon"></i>
              {{ tab.label }}
              <span class="tab-count">{{ getTabCount(tab.id) }}</span>
            </button>
          </div>

          <!-- Enrolled Courses -->
          <div v-if="activeTab === 'enrolled'" class="tab-content">
            <div class="courses-grid">
              <div v-for="course in courses.enrolled" :key="course.id" class="course-card">
                <div class="course-image">
                  <div class="image-placeholder">
                    <i class="fas fa-book"></i>
                  </div>
                  <div class="course-progress">
                    <div class="progress-text">{{ course.progress }}% {{ t('Complete') }}</div>
                    <div class="progress-bar">
                      <div class="progress-fill" :style="{ width: course.progress + '%' }"></div>
                    </div>
                  </div>
                </div>
                
                <div class="course-content">
                  <div class="course-category">{{ course.category }}</div>
                  <h3 class="course-title">{{ course.title }}</h3>
                  <p class="course-instructor">{{ t('By') }} {{ course.instructor }}</p>
                  
                  <div class="course-meta">
                    <div class="meta-item">
                      <i class="fas fa-clock"></i>
                      {{ course.duration }}
                    </div>
                    <div class="meta-item">
                      <i class="fas fa-play-circle"></i>
                      {{ course.lessons_completed }}/{{ course.total_lessons }} {{ t('Lessons') }}
                    </div>
                    <div class="meta-item">
                      <i class="fas fa-star"></i>
                      {{ course.rating }}
                    </div>
                  </div>
                  
                  <div class="course-actions">
                    <button class="btn-continue" @click="continueCourse(course)">
                      <i class="fas fa-play"></i>
                      {{ course.progress === 0 ? t('Start Learning') : t('Continue') }}
                    </button>
                    <button class="btn-outline" @click="viewCourseDetails(course)">
                      <i class="fas fa-info-circle"></i>
                    </button>
                  </div>
                  
                  <div class="last-accessed">
                    <i class="fas fa-history"></i>
                    {{ t('Last accessed') }} {{ course.last_accessed }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Completed Courses -->
          <div v-if="activeTab === 'completed'" class="tab-content">
            <div class="courses-grid">
              <div v-for="course in courses.completed" :key="course.id" class="course-card completed">
                <div class="course-image">
                  <div class="image-placeholder">
                    <i class="fas fa-book"></i>
                  </div>
                  <div class="completion-badge">
                    <i class="fas fa-check-circle"></i>
                    {{ t('Completed') }}
                  </div>
                </div>
                
                <div class="course-content">
                  <div class="course-category">{{ course.category }}</div>
                  <h3 class="course-title">{{ course.title }}</h3>
                  <p class="course-instructor">{{ t('By') }} {{ course.instructor }}</p>
                  
                  <div class="course-meta">
                    <div class="meta-item">
                      <i class="fas fa-clock"></i>
                      {{ course.duration }}
                    </div>
                    <div class="meta-item">
                      <i class="fas fa-calendar-check"></i>
                      {{ t('Completed') }} {{ course.completed_date }}
                    </div>
                  </div>
                  
                  <div class="course-actions">
                    <button v-if="course.certificate_available" class="btn-certificate" @click="downloadCertificate(course)">
                      <i class="fas fa-certificate"></i>
                      {{ t('Get Certificate') }}
                    </button>
                    <button class="btn-outline" @click="reviewCourse(course)">
                      <i class="fas fa-star"></i>
                      {{ t('Write Review') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Wishlist -->
          <div v-if="activeTab === 'wishlist'" class="tab-content">
            <div class="courses-grid">
              <div v-for="course in courses.wishlist" :key="course.id" class="course-card wishlist">
                <div class="course-image">
                  <div class="image-placeholder">
                    <i class="fas fa-book"></i>
                  </div>
                  <div class="wishlist-actions">
                    <button class="btn-remove-wishlist" @click="removeFromWishlist(course)">
                      <i class="fas fa-heart"></i>
                    </button>
                  </div>
                </div>
                
                <div class="course-content">
                  <div class="course-category">{{ course.category }}</div>
                  <h3 class="course-title">{{ course.title }}</h3>
                  <p class="course-instructor">{{ t('By') }} {{ course.instructor }}</p>
                  
                  <div class="course-meta">
                    <div class="meta-item">
                      <i class="fas fa-clock"></i>
                      {{ course.duration }}
                    </div>
                    <div class="meta-item">
                      <i class="fas fa-star"></i>
                      {{ course.rating }}
                    </div>
                    <div class="meta-item">
                      <i class="fas fa-users"></i>
                      {{ course.students.toLocaleString() }}
                    </div>
                  </div>
                  
                  <div class="course-price-section">
                    <div class="course-price">${{ course.price }}</div>
                    <button class="btn-enroll" @click="enrollCourse(course)">
                      {{ t('Enroll Now') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="getTabCount(activeTab) === 0" class="empty-state">
            <div class="empty-icon">
              <i class="fas fa-book-open"></i>
            </div>
            <h3>{{ getEmptyStateTitle() }}</h3>
            <p>{{ getEmptyStateMessage() }}</p>
            <Link href="/courses" class="btn-primary">
              {{ t('Browse Courses') }}
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, getCurrentInstance } from 'vue'

// Get the current Vue instance to access global properties
const { proxy } = getCurrentInstance()

// Define props first
const props = defineProps({
  courses: {
    type: Object,
    required: true,
    default: () => ({
      enrolled: [],
      completed: [],
      wishlist: []
    })
  }
})

// Use the global t function
const t = (key, replacements = {}) => {
  // Try to use the global t function from the Vue instance
  if (proxy && typeof proxy.t === 'function') {
    return proxy.t(key, replacements)
  }
  
  // Fallback: Try to use window.SkillGro.t
  if (window.SkillGro && typeof window.SkillGro.t === 'function') {
    return window.SkillGro.t(key, replacements)
  }
  
  // Final fallback: return the key
  return key
}

const activeTab = ref('enrolled')

const tabs = computed(() => [
  { id: 'enrolled', label: t('Enrolled'), icon: 'fas fa-play-circle' },
  { id: 'completed', label: t('Completed'), icon: 'fas fa-check-circle' },
  { id: 'wishlist', label: t('Wishlist'), icon: 'fas fa-heart' }
])

const getTabCount = (tabId) => {
  return props.courses[tabId]?.length || 0
}

const continueCourse = (course) => {
  alert(`Continuing course: ${course.title}`)
  // In real app: router.get(`/course/${course.id}/learn`)
}

const viewCourseDetails = (course) => {
  alert(`Viewing details for: ${course.title}`)
  // In real app: router.get(`/course/${course.id}`)
}

const downloadCertificate = (course) => {
  alert(`Downloading certificate for: ${course.title}`)
}

const reviewCourse = (course) => {
  alert(`Writing review for: ${course.title}`)
}

const removeFromWishlist = (course) => {
  alert(`Removing from wishlist: ${course.title}`)
}

const enrollCourse = (course) => {
  alert(`Enrolling in: ${course.title}`)
}

const getEmptyStateTitle = () => {
  const titles = {
    enrolled: t('No courses enrolled'),
    completed: t('No courses completed'),
    wishlist: t('Wishlist is empty')
  }
  return titles[activeTab.value] || t('No courses found')
}

const getEmptyStateMessage = () => {
  const messages = {
    enrolled: t('Start your learning journey by enrolling in courses'),
    completed: t('Complete your enrolled courses to see them here'),
    wishlist: t('Add courses to your wishlist to save them for later')
  }
  return messages[activeTab.value] || t('Explore our course catalog to find interesting courses')
}

const logout = () => {
  router.post('/logout')
}
</script>

<style scoped>
.my-courses-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.courses-header {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
  padding: 60px 0 40px;
}

.header-content {
  text-align: center;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 10px;
}

.page-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Courses Layout */
.courses-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
  padding: 40px 0;
  align-items: start;
}

/* Sidebar Styles */
.courses-sidebar {
  background: var(--bg-secondary);
  border-radius: 16px;
  box-shadow: var(--shadow);
  overflow: hidden;
  position: sticky;
  top: 100px;
}

.sidebar-header {
  padding: 30px 20px;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
  text-align: center;
}

.student-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.student-avatar {
  font-size: 60px;
  color: rgba(255, 255, 255, 0.9);
}

.student-details {
  text-align: center;
}

.student-name {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 5px;
}

.student-email {
  font-size: 0.9rem;
  opacity: 0.8;
}

.sidebar-nav {
  padding: 20px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 15px 25px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  font-size: 14px;
  border-left: 3px solid transparent;
}

.nav-item:hover {
  background: var(--bg-tertiary);
  color: var(--primary-color);
  border-left-color: var(--primary-light);
}

.nav-item.active {
  background: var(--primary-light);
  color: var(--primary-color);
  border-left-color: var(--primary-color);
}

.nav-item i {
  width: 20px;
  text-align: center;
  font-size: 16px;
}

.nav-text {
  font-weight: 500;
}

.nav-divider {
  height: 1px;
  background: var(--border-color);
  margin: 15px 25px;
}

.nav-item.logout {
  color: #ef4444;
}

.nav-item.logout:hover {
  background: #fef2f2;
  color: #dc2626;
  border-left-color: #ef4444;
}

.dark-theme .nav-item.logout:hover {
  background: #7f1d1d;
  color: #fca5a5;
}

/* Main Content */
.courses-main-content {
  display: grid;
  gap: 30px;
}

.courses-content {
  padding: 40px 0;
}

.courses-tabs {
  display: flex;
  background: var(--bg-secondary);
  border-radius: 12px;
  padding: 8px;
  margin-bottom: 30px;
  gap: 5px;
}

.tab-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  border: none;
  background: transparent;
  color: var(--text-secondary);
  font-weight: 500;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tab-btn.active {
  background: var(--primary-color);
  color: white;
}

.tab-count {
  background: var(--bg-primary);
  color: var(--text-primary);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.tab-btn.active .tab-count {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 25px;
}

.course-card {
  background: var(--bg-secondary);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.course-image {
  position: relative;
  height: 180px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-placeholder {
  font-size: 3rem;
  color: rgba(255, 255, 255, 0.9);
}

.course-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 10px 15px;
}

.progress-text {
  font-size: 0.8rem;
  margin-bottom: 5px;
}

.progress-bar {
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: var(--primary-color);
  transition: width 0.3s ease;
}

.completion-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  background: #10b981;
  color: white;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 5px;
}

.wishlist-actions {
  position: absolute;
  top: 15px;
  right: 15px;
}

.btn-remove-wishlist {
  background: rgba(255, 255, 255, 0.9);
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #ef4444;
  transition: all 0.3s ease;
}

.btn-remove-wishlist:hover {
  background: white;
  transform: scale(1.1);
}

.course-content {
  padding: 20px;
}

.course-category {
  color: var(--primary-color);
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.course-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 8px;
  line-height: 1.4;
}

.course-instructor {
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin-bottom: 15px;
}

.course-meta {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 5px;
  color: var(--text-muted);
  font-size: 0.8rem;
}

.course-actions {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}

.btn-continue {
  flex: 1;
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-continue:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.btn-outline {
  background: transparent;
  border: 2px solid var(--border-color);
  color: var(--text-primary);
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-outline:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.btn-certificate {
  background: #10b981;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-certificate:hover {
  background: #059669;
  transform: translateY(-2px);
}

.course-price-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
}

.course-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
}

.btn-enroll {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-enroll:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.last-accessed {
  display: flex;
  align-items: center;
  gap: 5px;
  color: var(--text-muted);
  font-size: 0.8rem;
  margin-top: 10px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  font-size: 4rem;
  color: var(--text-muted);
  margin-bottom: 20px;
}

.empty-state h3 {
  font-size: 1.5rem;
  color: var(--text-primary);
  margin-bottom: 10px;
}

.empty-state p {
  color: var(--text-secondary);
  margin-bottom: 20px;
}

.btn-primary {
  background: var(--primary-color);
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  display: inline-block;
}

.btn-primary:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .courses-layout {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .courses-sidebar {
    position: static;
    order: 2;
  }
  
  .courses-main-content {
    order: 1;
  }
}

@media (max-width: 768px) {
  .courses-tabs {
    flex-direction: column;
  }
  
  .courses-grid {
    grid-template-columns: 1fr;
  }
  
  .course-actions {
    flex-direction: column;
  }
  
  .course-price-section {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }
  
  .sidebar-nav {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    padding: 20px;
  }
  
  .nav-item {
    border-left: none;
    border-bottom: 3px solid transparent;
    justify-content: center;
    text-align: center;
    flex-direction: column;
    border-radius: 8px;
  }
  
  .nav-item.active,
  .nav-item:hover {
    border-left: none;
    border-bottom-color: var(--primary-color);
  }
  
  .nav-divider {
    display: none;
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 2rem;
  }
  
  .course-meta {
    flex-direction: column;
    gap: 8px;
  }
  
  .sidebar-nav {
    grid-template-columns: 1fr;
  }
}
</style>