<template>
  <div class="student-profile-page">
    <Head title="My Profile" />
    
    <!-- Header -->
    <div class="profile-header">
      <div class="container">
        <div class="header-content">
          <h1 class="page-title">{{ t('My Profile') }}</h1>
          <p class="page-subtitle">{{ t('Manage your profile and track your learning journey') }}</p>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="profile-layout">
        <!-- Sidebar -->
        <div class="profile-sidebar">
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
            <Link href="/student-profile" class="nav-item active">
              <i class="fas fa-user"></i>
              <span class="nav-text">{{ t('My Profile') }}</span>
            </Link>
            
            <Link href="/my-courses" class="nav-item">
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
        <div class="profile-main-content">
          <!-- Profile Card -->
          <div class="profile-card">
            <div class="profile-header-section">
              <div class="avatar-section">
                <div class="avatar-placeholder">
                  <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-info">
                  <h2>{{ profile.user.name }}</h2>
                  <p>{{ profile.user.email }}</p>
                  <div class="member-since">
                    <i class="fas fa-calendar-alt"></i>
                    {{ t('Member since') }} {{ profile.user.joined_date }}
                  </div>
                </div>
              </div>
              <button class="edit-profile-btn">
                <i class="fas fa-edit"></i>
                {{ t('Edit Profile') }}
              </button>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-icon courses">
                  <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                  <div class="stat-number">{{ profile.stats.courses_enrolled }}</div>
                  <div class="stat-label">{{ t('Courses Enrolled') }}</div>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon completed">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                  <div class="stat-number">{{ profile.stats.courses_completed }}</div>
                  <div class="stat-label">{{ t('Courses Completed') }}</div>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon hours">
                  <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                  <div class="stat-number">{{ profile.stats.learning_hours }}</div>
                  <div class="stat-label">{{ t('Learning Hours') }}</div>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon streak">
                  <i class="fas fa-fire"></i>
                </div>
                <div class="stat-info">
                  <div class="stat-number">{{ profile.stats.current_streak }}</div>
                  <div class="stat-label">{{ t('Day Streak') }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="activity-section">
            <div class="section-header">
              <h3>{{ t('Recent Activity') }}</h3>
              <Link href="/learning-progress" class="view-all-link">
                {{ t('View All') }} <i class="fas fa-arrow-right"></i>
              </Link>
            </div>
            
            <div class="activity-list">
              <div v-for="(activity, index) in profile.recent_activity" :key="index" class="activity-item">
                <div class="activity-icon" :class="activity.type">
                  <i class="fas" :class="getActivityIcon(activity.type)"></i>
                </div>
                <div class="activity-details">
                  <h4>{{ activity.course }}</h4>
                  <p>{{ t('Progress') }}: {{ activity.progress }}%</p>
                  <span class="activity-time">{{ activity.last_accessed }}</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill" :style="{ width: activity.progress + '%' }"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="quick-actions">
            <h3>{{ t('Quick Actions') }}</h3>
            <div class="action-grid">
              <Link href="/my-courses" class="action-card">
                <i class="fas fa-book-open"></i>
                <span>{{ t('My Courses') }}</span>
              </Link>
              <Link href="/learning-progress" class="action-card">
                <i class="fas fa-chart-line"></i>
                <span>{{ t('Learning Progress') }}</span>
              </Link>
              <Link href="/settings" class="action-card">
                <i class="fas fa-cog"></i>
                <span>{{ t('Settings') }}</span>
              </Link>
              <button class="action-card" @click="downloadCertificate">
                <i class="fas fa-certificate"></i>
                <span>{{ t('Certificates') }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { getCurrentInstance } from 'vue'

// Get the current Vue instance to access global properties
const { proxy } = getCurrentInstance()

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

defineProps({
  profile: {
    type: Object,
    required: true
  }
})

const getActivityIcon = (type) => {
  const icons = {
    video: 'fa-play-circle',
    quiz: 'fa-question-circle',
    assignment: 'fa-tasks'
  }
  return icons[type] || 'fa-circle'
}

const downloadCertificate = () => {
  alert('Certificate download feature coming soon!')
}

const logout = () => {
  router.post('/logout')
}
</script>

<style scoped>
.student-profile-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.profile-header {
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

/* Profile Layout */
.profile-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
  padding: 40px 0;
  align-items: start;
}

/* Sidebar Styles */
.profile-sidebar {
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
.profile-main-content {
  display: grid;
  gap: 30px;
}

/* Existing styles for profile card, stats, activity, etc. */
.profile-card {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 30px;
  box-shadow: var(--shadow);
}

.profile-header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
}

.avatar-section {
  display: flex;
  align-items: center;
  gap: 20px;
}

.avatar-placeholder {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: var(--primary-light);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: var(--primary-color);
}

.profile-info h2 {
  font-size: 1.8rem;
  font-weight: 600;
  margin-bottom: 5px;
  color: var(--text-primary);
}

.profile-info p {
  color: var(--text-secondary);
  margin-bottom: 8px;
}

.member-since {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--text-muted);
  font-size: 0.9rem;
}

.edit-profile-btn {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.edit-profile-btn:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.stat-card {
  background: var(--bg-primary);
  padding: 20px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 15px;
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
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

.stat-icon.courses {
  background: #e3f2fd;
  color: #1976d2;
}

.stat-icon.completed {
  background: #e8f5e8;
  color: #2e7d32;
}

.stat-icon.hours {
  background: #fff3e0;
  color: #f57c00;
}

.stat-icon.streak {
  background: #ffebee;
  color: #d32f2f;
}

.stat-number {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--text-primary);
}

.stat-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.activity-section {
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

.activity-list {
  display: grid;
  gap: 15px;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  background: var(--bg-primary);
  border-radius: 12px;
  transition: transform 0.3s ease;
}

.activity-item:hover {
  transform: translateX(5px);
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.activity-icon.video {
  background: #e3f2fd;
  color: #1976d2;
}

.activity-icon.quiz {
  background: #f3e5f5;
  color: #7b1fa2;
}

.activity-icon.assignment {
  background: #e8f5e8;
  color: #2e7d32;
}

.activity-details {
  flex: 1;
}

.activity-details h4 {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 5px;
}

.activity-details p {
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin-bottom: 5px;
}

.activity-time {
  font-size: 0.8rem;
  color: var(--text-muted);
}

.progress-bar {
  width: 80px;
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

.quick-actions {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 30px;
  box-shadow: var(--shadow);
}

.quick-actions h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 20px;
}

.action-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.action-card {
  background: var(--bg-primary);
  padding: 20px;
  border-radius: 12px;
  text-decoration: none;
  color: var(--text-primary);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
}

.action-card:hover {
  background: var(--primary-color);
  color: white;
  transform: translateY(-5px);
}

.action-card i {
  font-size: 2rem;
}

.action-card span {
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .profile-layout {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .profile-sidebar {
    position: static;
    order: 2;
  }
  
  .profile-main-content {
    order: 1;
  }
}

@media (max-width: 768px) {
  .profile-header-section {
    flex-direction: column;
    gap: 20px;
  }
  
  .avatar-section {
    flex-direction: column;
    text-align: center;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .action-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .activity-item {
    flex-direction: column;
    text-align: center;
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
  
  .action-grid {
    grid-template-columns: 1fr;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .sidebar-nav {
    grid-template-columns: 1fr;
  }
}
</style>