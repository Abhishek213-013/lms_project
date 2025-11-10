<template>
  <FrontendLayout>
    <div class="learning-progress-page">
      <Head title="Learning Progress" />
      
      <!-- Header -->
      <div class="progress-header">
        <div class="container">
          <div class="header-content">
            <h1 class="page-title">{{ t('Learning Progress') }}</h1>
            <p class="page-subtitle">{{ t('Track your learning journey and achievements') }}</p>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="progress-layout">
          <!-- Sidebar -->
          <div class="progress-sidebar">
            <div class="sidebar-header">
              <div class="student-info">
                <div class="student-avatar" @click="triggerAvatarUpload">
                  <img v-if="profile.user.avatar" 
                       :src="profile.user.avatar" 
                       :alt="profile.user.name + ' Avatar'" 
                       class="avatar-image">
                  <i v-else class="fas fa-user-circle"></i>
                  <input 
                    type="file" 
                    ref="avatarInput" 
                    @change="handleAvatarUpload" 
                    accept="image/*" 
                    style="display: none;"
                  >
                </div>
                <div class="student-details">
                  <div class="student-name">{{ $page.props.auth.user.name }}</div>
                  <div class="student-email">{{ $page.props.auth.user.email }}</div>
                  <div class="student-stats">
                    <div class="stat-mini">
                      <i class="fas fa-book"></i>
                      {{ progress.overview.total_courses }} {{ t('Courses') }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <nav class="sidebar-nav">
              <Link href="/student-profile" class="nav-item">
                <i class="fas fa-user"></i>
                <span class="nav-text">{{ t('My Profile') }}</span>
              </Link>
              
              <Link href="/my-courses" class="nav-item">
                <i class="fas fa-book"></i>
                <span class="nav-text">{{ t('My Courses') }}</span>
              </Link>
              
              <Link href="/learning-progress" class="nav-item active">
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
          <div class="progress-main-content">
            <!-- Overview Stats -->
            <div class="overview-section">
              <div class="stats-grid">
                <div class="stat-card main">
                  <div class="stat-content">
                    <div class="stat-icon">
                      <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                      <div class="stat-number">{{ progress.overview.average_progress }}%</div>
                      <div class="stat-label">{{ t('Average Progress') }}</div>
                      <div class="stat-subtext">
                        {{ progress.overview.completed_courses }}/{{ progress.overview.total_courses }} {{ t('courses completed') }}
                      </div>
                    </div>
                  </div>
                  <div class="stat-trend" :class="getProgressTrend()">
                    <i class="fas" :class="getProgressTrendIcon()"></i>
                    {{ getProgressTrendText() }}
                  </div>
                </div>
                
                <div class="stat-card">
                  <div class="stat-content">
                    <div class="stat-icon">
                      <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                      <div class="stat-number">{{ progress.overview.total_learning_hours }}</div>
                      <div class="stat-label">{{ t('Learning Hours') }}</div>
                      <div class="stat-subtext">
                        {{ progress.overview.in_progress_courses }} {{ t('in progress') }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="stat-card">
                  <div class="stat-content">
                    <div class="stat-icon">
                      <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                      <div class="stat-number">{{ progress.overview.completed_courses }}</div>
                      <div class="stat-label">{{ t('Courses Completed') }}</div>
                      <div class="stat-subtext">
                        {{ Math.round((progress.overview.completed_courses / progress.overview.total_courses) * 100) || 0 }}% {{ t('completion rate') }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="stat-card">
                  <div class="stat-content">
                    <div class="stat-icon">
                      <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-info">
                      <div class="stat-number">{{ progress.overview.current_streak }}</div>
                      <div class="stat-label">{{ t('Day Streak') }}</div>
                      <div class="stat-subtext">
                        {{ t('Longest') }}: {{ progress.overview.longest_streak }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Weekly Progress Chart -->
            <div class="chart-section">
              <div class="section-header">
                <h3>{{ t('Weekly Learning Activity') }}</h3>
                <div class="chart-legend">
                  <div class="legend-item">
                    <div class="legend-color hours"></div>
                    <span>{{ t('Learning Hours') }}</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-color courses"></div>
                    <span>{{ t('Courses Accessed') }}</span>
                  </div>
                </div>
              </div>
              
              <div class="chart-container">
                <div class="chart-bars">
                  <div v-for="(day, index) in progress.weekly_progress.labels" 
                      :key="index" 
                      class="chart-bar-group">
                    <div class="bar-label">{{ day }}</div>
                    <div class="bars">
                      <div class="bar hours" :style="{ height: Math.max(progress.weekly_progress.hours[index] * 15, 10) + 'px' }">
                        <span class="bar-value">{{ progress.weekly_progress.hours[index] }}h</span>
                      </div>
                      <div class="bar courses" :style="{ height: Math.max(progress.weekly_progress.courses[index] * 8, 10) + 'px' }">
                        <span class="bar-value">{{ progress.weekly_progress.courses[index] }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="chart-summary">
                <div class="summary-item">
                  <strong>{{ getWeeklyTotalHours() }}</strong> {{ t('total hours this week') }}
                </div>
                <div class="summary-item">
                  <strong>{{ getWeeklyTotalCourses() }}</strong> {{ t('courses accessed') }}
                </div>
              </div>
            </div>

            <!-- Course Progress -->
            <div class="courses-progress-section">
              <div class="section-header">
                <h3>{{ t('Course Progress') }}</h3>
                <div class="progress-filters">
                  <button class="filter-btn" :class="{ active: activeFilter === 'all' }" @click="activeFilter = 'all'">
                    {{ t('All') }} ({{ progress.course_progress.length }})
                  </button>
                  <button class="filter-btn" :class="{ active: activeFilter === 'in-progress' }" @click="activeFilter = 'in-progress'">
                    {{ t('In Progress') }} ({{ getInProgressCount() }})
                  </button>
                  <button class="filter-btn" :class="{ active: activeFilter === 'completed' }" @click="activeFilter = 'completed'">
                    {{ t('Completed') }} ({{ progress.overview.completed_courses }})
                  </button>
                </div>
              </div>
              
              <div class="progress-list">
                <div v-for="course in filteredCourses" :key="course.id" class="progress-item">
                  <div class="course-info">
                    <div class="course-category">{{ course.category }}</div>
                    <h4 class="course-title">{{ course.title }}</h4>
                    <div class="course-meta">
                      <span class="meta-item">
                        <i class="fas fa-clock"></i>
                        {{ course.time_spent }}
                      </span>
                      <span class="meta-item">
                        <i class="fas fa-history"></i>
                        {{ course.last_activity }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="progress-info">
                    <div class="progress-stats">
                      <div class="progress-percentage">{{ course.progress }}%</div>
                      <div class="progress-trend" :class="course.weekly_trend">
                        <i class="fas" :class="getTrendIcon(course.weekly_trend)"></i>
                      </div>
                    </div>
                    <div class="progress-bar">
                      <div class="progress-fill" :style="{ width: course.progress + '%' }"></div>
                    </div>
                    <div class="progress-status">
                      <span v-if="course.progress === 100" class="status-completed">
                        <i class="fas fa-check"></i> {{ t('Completed') }}
                      </span>
                      <span v-else class="status-in-progress">
                        {{ t('In Progress') }}
                      </span>
                    </div>
                  </div>
                  
                  <button class="btn-continue" @click="continueCourse(course)" :disabled="course.progress === 100">
                    <i class="fas" :class="course.progress === 100 ? 'fa-check' : 'fa-play'"></i>
                  </button>
                </div>
                
                <div v-if="filteredCourses.length === 0" class="no-courses">
                  <i class="fas fa-book-open"></i>
                  <h4>{{ t('No courses found') }}</h4>
                  <p>{{ t('Start learning to see your progress here!') }}</p>
                  <Link href="/my-courses" class="btn-primary">
                    <i class="fas fa-book"></i>
                    {{ t('Browse Courses') }}
                  </Link>
                </div>
              </div>
            </div>

            <!-- Achievements -->
            <div class="achievements-section">
              <div class="section-header">
                <h3>{{ t('Achievements') }}</h3>
                <div class="achievements-summary">
                  {{ getCompletedAchievements() }}/{{ progress.achievements.length }} {{ t('Completed') }}
                </div>
              </div>
              
              <div class="achievements-grid">
                <div v-for="(achievement, index) in progress.achievements" 
                    :key="index" 
                    :class="['achievement-card', { 'completed': achievement.completed }]">
                  <div class="achievement-icon">
                    <i :class="achievement.icon"></i>
                    <div v-if="achievement.completed" class="completion-badge">
                      <i class="fas fa-check"></i>
                    </div>
                  </div>
                  
                  <div class="achievement-content">
                    <h4>{{ achievement.title }}</h4>
                    <p>{{ achievement.description }}</p>
                    
                    <div v-if="!achievement.completed" class="progress-container">
                      <div class="progress-bar">
                        <div class="progress-fill" :style="{ width: achievement.progress + '%' }"></div>
                      </div>
                      <span class="progress-text">{{ achievement.progress }}%</span>
                    </div>
                    
                    <div v-else class="completion-date">
                      <i class="fas fa-trophy"></i>
                      <span v-if="achievement.date_earned">
                        {{ t('Earned on') }} {{ formatDate(achievement.date_earned) }}
                      </span>
                      <span v-else>
                        {{ t('Achievement earned!') }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading Overlay -->
      <div v-if="loading" class="loading-overlay">
        <div class="loading-spinner"></div>
        <p>{{ t('Updating profile...') }}</p>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, getCurrentInstance } from 'vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'

// Get the current Vue instance to access global properties
const { proxy } = getCurrentInstance()

// Define props first
const props = defineProps({
  progress: {
    type: Object,
    required: true,
    default: () => ({
      overview: {
        average_progress: 0,
        total_learning_hours: 0,
        completed_courses: 0,
        current_streak: 0,
        total_courses: 0,
        in_progress_courses: 0,
        longest_streak: 0
      },
      weekly_progress: {
        labels: [],
        hours: [],
        courses: []
      },
      course_progress: [],
      achievements: []
    })
  },
  profile: {
    type: Object,
    required: true,
    default: () => ({
      user: {
        name: '',
        email: '',
        avatar: null,
        phone: '',
        bio: '',
        location: '',
        joined_date: ''
      },
      stats: {
        courses_enrolled: 0,
        courses_completed: 0,
        learning_hours: 0,
        current_streak: 0
      },
      recent_activity: [],
      student_info: {
        roll_number: '',
        class: '',
        admission_date: '',
        father_name: '',
        mother_name: '',
        parent_contact: '',
        address: '',
        profile_picture: '',
        profile_picture_url: ''
      }
    })
  }
})

const activeFilter = ref('all')
const loading = ref(false)
const avatarInput = ref(null)

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

// Computed properties
const filteredCourses = computed(() => {
  if (activeFilter.value === 'all') {
    return props.progress.course_progress
  } else if (activeFilter.value === 'in-progress') {
    return props.progress.course_progress.filter(course => course.progress > 0 && course.progress < 100)
  } else if (activeFilter.value === 'completed') {
    return props.progress.course_progress.filter(course => course.progress === 100)
  }
  return props.progress.course_progress
})

// Methods
const getTrendIcon = (trend) => {
  const icons = {
    up: 'fa-arrow-up',
    down: 'fa-arrow-down',
    stable: 'fa-minus'
  }
  return icons[trend] || 'fa-minus'
}

const getProgressTrend = () => {
  // Simple trend calculation based on weekly progress
  const totalHours = props.progress.weekly_progress.hours.reduce((a, b) => a + b, 0)
  return totalHours > 10 ? 'up' : totalHours > 5 ? 'stable' : 'down'
}

const getProgressTrendIcon = () => {
  const trend = getProgressTrend()
  return getTrendIcon(trend)
}

const getProgressTrendText = () => {
  const trend = getProgressTrend()
  const totalHours = props.progress.weekly_progress.hours.reduce((a, b) => a + b, 0)
  
  if (trend === 'up') return t('Great week!')
  if (trend === 'stable') return t('Good progress')
  return t('Keep going!')
}

const getWeeklyTotalHours = () => {
  return props.progress.weekly_progress.hours.reduce((a, b) => a + b, 0).toFixed(1)
}

const getWeeklyTotalCourses = () => {
  return props.progress.weekly_progress.courses.reduce((a, b) => a + b, 0)
}

const getInProgressCount = () => {
  return props.progress.course_progress.filter(course => course.progress > 0 && course.progress < 100).length
}

const continueCourse = (course) => {
  if (course.progress === 100) {
    alert(t('Course already completed!'))
    return
  }
  // Navigate to course or show course content
  router.visit(`/course/${course.id}`)
}

const getCompletedAchievements = () => {
  return props.progress.achievements.filter(a => a.completed).length
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const triggerAvatarUpload = () => {
  avatarInput.value.click()
}

const handleAvatarUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file type and size
  if (!file.type.startsWith('image/')) {
    alert(t('Please select an image file'))
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    alert(t('Image size should be less than 2MB'))
    return
  }

  loading.value = true
  const formData = new FormData()
  formData.append('avatar', file)

  console.log('Starting avatar upload from Learning Progress page...')

  try {
    const response = await fetch(route('api.student-profile.upload-avatar'), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: formData
    })

    console.log('Upload response status:', response.status)

    if (!response.ok) {
      const text = await response.text()
      let errorMessage = t('Upload failed')
      
      try {
        const errorData = JSON.parse(text)
        errorMessage = errorData.error || errorData.message || t('Upload failed')
      } catch {
        errorMessage = text || t('Upload failed with status: ') + response.status
      }
      
      throw new Error(errorMessage)
    }

    const result = await response.json()
    
    console.log('Upload successful:', result)
    
    // Show success message
    alert(t('Avatar uploaded successfully! The page will now reload.'))
    
    // Force a complete page reload to get fresh data from server
    setTimeout(() => {
      window.location.href = route('learning-progress.new')
    }, 1000)
    
  } catch (error) {
    console.error('Avatar upload error:', error)
    alert(t('Error uploading avatar: ') + error.message)
  } finally {
    loading.value = false
    // Reset the input
    event.target.value = ''
  }
}

const logout = () => {
  if (confirm(t('Are you sure you want to logout?'))) {
    router.post('/logout')
  }
}
</script>

<style scoped>
/* Add these new styles to your existing CSS */

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.student-avatar {
  cursor: pointer;
  transition: opacity 0.3s ease;
  position: relative;
  overflow: hidden;
}

.student-avatar:hover {
  opacity: 0.8;
}

.student-stats {
  margin-top: 10px;
}

.stat-mini {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.8rem;
  opacity: 0.9;
}

.stat-subtext {
  font-size: 0.8rem;
  opacity: 0.7;
  margin-top: 5px;
}

.chart-summary {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid var(--border-color);
}

.summary-item {
  text-align: center;
  color: var(--text-secondary);
}

.summary-item strong {
  color: var(--text-primary);
  font-size: 1.1rem;
}

.progress-filters {
  display: flex;
  gap: 10px;
}

.filter-btn {
  padding: 8px 16px;
  border: 1px solid var(--border-color);
  background: var(--bg-primary);
  color: var(--text-secondary);
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.filter-btn.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.filter-btn:hover:not(.active) {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.progress-status {
  margin-top: 5px;
  font-size: 0.8rem;
}

.status-completed {
  color: #10b981;
  font-weight: 600;
}

.status-in-progress {
  color: var(--text-muted);
}

.no-courses {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-muted);
}

.no-courses i {
  font-size: 4rem;
  margin-bottom: 20px;
  opacity: 0.3;
}

.no-courses h4 {
  font-size: 1.2rem;
  margin-bottom: 10px;
  color: var(--text-primary);
}

.no-courses p {
  margin-bottom: 20px;
}

.btn-primary {
  background: var(--primary-color);
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.btn-continue:disabled {
  background: var(--text-muted);
  cursor: not-allowed;
  transform: none;
}

.btn-continue:disabled:hover {
  background: var(--text-muted);
  transform: none;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  color: white;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(255,255,255,0.3);
  border-left: 4px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-overlay p {
  font-size: 1.1rem;
  font-weight: 500;
}

/* Rest of your existing CSS remains the same */
.learning-progress-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.progress-header {
  color: var(--primary-color);
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

/* Progress Layout */
.progress-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
  padding: 40px 0;
  align-items: start;
}

/* Sidebar Styles */
.progress-sidebar {
  background: var(--bg-secondary);
  border-radius: 16px;
  box-shadow: var(--shadow);
  overflow: hidden;
  position: sticky;
  top: 100px;
}

.sidebar-header {
  padding: 30px 20px;
  background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
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
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: white;
  border: 3px solid rgba(255,255,255,0.3);
}

.student-details {
  text-align: center;
}

.student-name {
  color: black;
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 5px;
}

.student-email {
  color: black;
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
.progress-main-content {
  display: grid;
  gap: 30px;
}

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.student-stats {
  margin-top: 10px;
}

.stat-mini {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.8rem;
  opacity: 0.9;
}

.stat-subtext {
  font-size: 0.8rem;
  opacity: 0.7;
  margin-top: 5px;
}

.chart-summary {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid var(--border-color);
}

.summary-item {
  text-align: center;
  color: var(--text-secondary);
}

.summary-item strong {
  color: var(--text-primary);
  font-size: 1.1rem;
}

.progress-filters {
  display: flex;
  gap: 10px;
}

.filter-btn {
  padding: 8px 16px;
  border: 1px solid var(--border-color);
  background: var(--bg-primary);
  color: var(--text-secondary);
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.filter-btn.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.filter-btn:hover:not(.active) {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.progress-status {
  margin-top: 5px;
  font-size: 0.8rem;
}

.status-completed {
  color: #10b981;
  font-weight: 600;
}

.status-in-progress {
  color: var(--text-muted);
}

.no-courses {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-muted);
}

.no-courses i {
  font-size: 4rem;
  margin-bottom: 20px;
  opacity: 0.3;
}

.no-courses h4 {
  font-size: 1.2rem;
  margin-bottom: 10px;
  color: var(--text-primary);
}

.no-courses p {
  margin-bottom: 20px;
}

.btn-primary {
  background: var(--primary-color);
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.btn-continue:disabled {
  background: var(--text-muted);
  cursor: not-allowed;
  transform: none;
}

.btn-continue:disabled:hover {
  background: var(--text-muted);
  transform: none;
}

.learning-progress-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.progress-header {
  color: var(--primary-color);
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

/* Progress Layout */
.progress-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
  padding: 40px 0;
  align-items: start;
}

/* Sidebar Styles */
.progress-sidebar {
  background: var(--bg-secondary);
  border-radius: 16px;
  box-shadow: var(--shadow);
  overflow: hidden;
  position: sticky;
  top: 100px;
}

.sidebar-header {
  padding: 30px 20px;
  background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
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
.progress-main-content {
  display: grid;
  gap: 30px;
}

.progress-content {
  padding: 40px 0;
  display: grid;
  gap: 30px;
}

/* Overview Stats */
.overview-section {
  margin-bottom: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 20px;
}

.stat-card {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 25px;
  box-shadow: var(--shadow);
  transition: transform 0.3s ease;
}

.stat-card.main {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-content {
  display: flex;
  align-items: center;
  gap: 15px;
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-card.main .stat-icon {
  background: rgba(255, 255, 255, 0.2);
}

.stat-card:not(.main) .stat-icon {
  background: var(--primary-light);
  color: var(--primary-color);
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.8;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.8rem;
  font-weight: 600;
  margin-top: 10px;
}

.stat-trend.up {
  color: #10b981;
}

.stat-trend.down {
  color: #ef4444;
}

/* Chart Section */
.chart-section {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 30px;
  box-shadow: var(--shadow);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.section-header h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.chart-legend {
  display: flex;
  gap: 20px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 2px;
}

.legend-color.hours {
  background: var(--primary-color);
}

.legend-color.courses {
  background: #8b5cf6;
}

.chart-container {
  padding: 20px 0;
}

.chart-bars {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  height: 200px;
  gap: 10px;
}

.chart-bar-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  flex: 1;
}

.bar-label {
  font-size: 0.8rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.bars {
  display: flex;
  gap: 4px;
  align-items: flex-end;
  height: 150px;
}

.bar {
  border-radius: 4px 4px 0 0;
  position: relative;
  transition: height 0.3s ease;
  min-width: 20px;
}

.bar.hours {
  background: var(--primary-color);
}

.bar.courses {
  background: #8b5cf6;
}

.bar-value {
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
}

/* Course Progress */
.courses-progress-section {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 30px;
  box-shadow: var(--shadow);
}

.view-all-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: gap 0.3s ease;
}

.view-all-link:hover {
  gap: 10px;
}

.progress-list {
  display: grid;
  gap: 15px;
}

.progress-item {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 20px;
  background: var(--bg-primary);
  border-radius: 12px;
  transition: transform 0.3s ease;
}

.progress-item:hover {
  transform: translateX(5px);
}

.course-info {
  flex: 1;
}

.course-category {
  color: var(--primary-color);
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  margin-bottom: 5px;
}

.course-title {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 8px;
}

.course-meta {
  display: flex;
  gap: 15px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 5px;
  color: var(--text-muted);
  font-size: 0.8rem;
}

.progress-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
  min-width: 100px;
}

.progress-stats {
  display: flex;
  align-items: center;
  gap: 8px;
}

.progress-percentage {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--text-primary);
}

.progress-trend {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
}

.progress-trend.up {
  background: #e8f5e8;
  color: #2e7d32;
}

.progress-trend.down {
  background: #ffebee;
  color: #d32f2f;
}

.progress-trend.stable {
  background: #fff3e0;
  color: #f57c00;
}

.progress-bar {
  width: 100px;
  height: 6px;
  background: var(--border-color);
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: var(--primary-color);
  transition: width 0.3s ease;
}

.btn-continue {
  background: var(--primary-color);
  color: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-continue:hover {
  background: var(--primary-hover);
  transform: scale(1.1);
}

/* Achievements */
.achievements-section {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 30px;
  box-shadow: var(--shadow);
}

.achievements-summary {
  color: var(--text-secondary);
  font-weight: 500;
}

.achievements-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.achievement-card {
  background: var(--bg-primary);
  padding: 20px;
  border-radius: 12px;
  display: flex;
  gap: 15px;
  transition: all 0.3s ease;
}

.achievement-card.completed {
  border: 2px solid var(--primary-color);
}

.achievement-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow);
}

.achievement-icon {
  position: relative;
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: var(--primary-light);
  color: var(--primary-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.achievement-card.completed .achievement-icon {
  background: var(--primary-color);
  color: white;
}

.completion-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #10b981;
  color: white;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.6rem;
}

.achievement-content {
  flex: 1;
}

.achievement-content h4 {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 5px;
}

.achievement-content p {
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin-bottom: 10px;
}

.progress-container {
  display: flex;
  align-items: center;
  gap: 10px;
}

.progress-container .progress-bar {
  flex: 1;
  width: auto;
}

.progress-text {
  font-size: 0.8rem;
  color: var(--text-muted);
  font-weight: 600;
  min-width: 40px;
}

.completion-date {
  display: flex;
  align-items: center;
  gap: 5px;
  color: var(--primary-color);
  font-size: 0.8rem;
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .progress-layout {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .progress-sidebar {
    position: static;
    order: 2;
  }
  
  .progress-main-content {
    order: 1;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
  }
  
  .stat-card.main {
    grid-column: 1 / -1;
  }
  
  .section-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .progress-item {
    flex-direction: column;
    text-align: center;
    gap: 15px;
  }
  
  .progress-info {
    align-items: center;
  }
  
  .chart-bars {
    gap: 5px;
  }
  
  .bars {
    gap: 2px;
  }
  
  .bar {
    min-width: 15px;
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
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .achievements-grid {
    grid-template-columns: 1fr;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .chart-bars {
    flex-wrap: wrap;
    height: auto;
  }
  
  .chart-bar-group {
    flex: 0 0 calc(25% - 10px);
  }
  
  .sidebar-nav {
    grid-template-columns: 1fr;
  }
}
</style>