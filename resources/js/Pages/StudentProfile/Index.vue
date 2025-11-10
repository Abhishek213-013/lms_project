<template>
  <FrontendLayout>
    <div class="student-profile-page">
      <Head :title="t('My Profile')" />
      
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
                <div class="student-avatar" @click="triggerAvatarUpload">
                  <img v-if="profile.user.avatar" 
                       :src="profile.user.avatar" 
                       :alt="profile.user.name + ' ' + t('Avatar')" 
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
                  <div class="student-roll" v-if="profile.student_info">
                    {{ t('Roll') }}: {{ profile.student_info.roll_number }}
                  </div>
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
                  <div class="avatar-placeholder" @click="triggerAvatarUpload">
                    <img v-if="profile.user.avatar" 
                         :src="profile.user.avatar" 
                         :alt="profile.user.name + ' ' + t('Avatar')" 
                         class="avatar-image">
                    <i v-else class="fas fa-user-circle"></i>
                  </div>
                  <div class="profile-info">
                    <h2>{{ profile.user.name }}</h2>
                    <p>{{ profile.user.email }}</p>
                    <div class="member-since">
                      <i class="fas fa-calendar-alt"></i>
                      {{ t('Member since') }} {{ profile.user.joined_date }}
                    </div>
                    <div v-if="profile.student_info" class="student-additional-info">
                      <div class="info-item">
                        <strong>{{ t('Roll No') }}:</strong> {{ profile.student_info.roll_number }}
                      </div>
                      <div class="info-item">
                        <strong>{{ t('Class') }}:</strong> {{ profile.student_info.class }}
                      </div>
                      <div class="info-item" v-if="profile.student_info.admission_date">
                        <strong>{{ t('Admission') }}:</strong> {{ profile.student_info.admission_date }}
                      </div>
                    </div>
                  </div>
                </div>
                <button class="edit-profile-btn" @click="showEditModal = true">
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
                    <div class="stat-number">{{ Math.round(profile.stats.learning_hours) }}</div>
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

              <!-- Student Details Section -->
              <div class="student-details-section" v-if="profile.student_info">
                <h3>{{ t('Student Information') }}</h3>
                <div class="details-grid">
                  <div class="detail-item">
                    <label>{{ t("Father's Name") }}:</label>
                    <span>{{ profile.student_info.father_name || t('Not set') }}</span>
                  </div>
                  <div class="detail-item">
                    <label>{{ t("Mother's Name") }}:</label>
                    <span>{{ profile.student_info.mother_name || t('Not set') }}</span>
                  </div>
                  <div class="detail-item">
                    <label>{{ t('Parent Contact') }}:</label>
                    <span>{{ profile.student_info.parent_contact || t('Not set') }}</span>
                  </div>
                  <div class="detail-item">
                    <label>{{ t('Address') }}:</label>
                    <span>{{ profile.student_info.address || t('Not set') }}</span>
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
                <div v-if="profile.recent_activity.length > 0" 
                     v-for="(activity, index) in profile.recent_activity" 
                     :key="index" 
                     class="activity-item">
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
                <div v-else class="no-activity">
                  <i class="fas fa-inbox"></i>
                  <p>{{ t('No recent activity') }}</p>
                  <p class="no-activity-sub">{{ t('Start learning to see your activity here!') }}</p>
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

      <!-- Edit Profile Modal -->
      <div v-if="showEditModal" class="modal-overlay" @click="showEditModal = false">
        <div class="modal-content" @click.stop>
          <div class="modal-header">
            <h3>{{ t('Edit Profile') }}</h3>
            <button class="close-btn" @click="showEditModal = false">&times;</button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateProfile">
              <div class="form-row">
                <div class="form-group">
                  <label for="name">{{ t('Full Name') }} *</label>
                  <input type="text" id="name" v-model="editForm.name" required>
                </div>
                <div class="form-group">
                  <label for="email">{{ t('Email') }} *</label>
                  <input type="email" id="email" v-model="editForm.email" required>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="phone">{{ t('Phone') }}</label>
                  <input type="tel" id="phone" v-model="editForm.phone" :placeholder="t('Enter your phone number')">
                </div>
                <div class="form-group">
                  <label for="location">{{ t('Location') }}</label>
                  <input type="text" id="location" v-model="editForm.location" :placeholder="t('Your city')">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="father_name">{{ t("Father's Name") }}</label>
                  <input type="text" id="father_name" v-model="editForm.father_name">
                </div>
                <div class="form-group">
                  <label for="mother_name">{{ t("Mother's Name") }}</label>
                  <input type="text" id="mother_name" v-model="editForm.mother_name">
                </div>
              </div>

              <div class="form-group">
                <label for="bio">{{ t('Bio') }}</label>
                <textarea id="bio" v-model="editForm.bio" rows="3" :placeholder="t('Tell us about yourself...')"></textarea>
              </div>

              <div class="form-group">
                <label for="address">{{ t('Address') }}</label>
                <textarea id="address" v-model="editForm.address" rows="2" :placeholder="t('Your full address')"></textarea>
              </div>

              <div class="form-actions">
                <button type="button" @click="showEditModal = false" class="btn-cancel">{{ t('Cancel') }}</button>
                <button type="submit" class="btn-save" :disabled="updating">
                  {{ updating ? t('Updating...') : t('Save Changes') }}
                </button>
              </div>
            </form>
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
import { ref, reactive } from 'vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { useTranslation } from '@/composables/useTranslation'

const props = defineProps({
  profile: {
    type: Object,
    required: true
  }
})

// Initialize translation
const { t, currentLanguage, switchLanguage } = useTranslation()

const showEditModal = ref(false)
const updating = ref(false)
const loading = ref(false)
const avatarInput = ref(null)

const editForm = reactive({
  name: props.profile.user.name,
  email: props.profile.user.email,
  phone: props.profile.user.phone || '',
  bio: props.profile.user.bio || '',
  location: props.profile.user.location || '',
  father_name: props.profile.student_info?.father_name || '',
  mother_name: props.profile.student_info?.mother_name || '',
  address: props.profile.student_info?.address || ''
})

const getActivityIcon = (type) => {
  const icons = {
    video: 'fa-play-circle',
    quiz: 'fa-question-circle',
    assignment: 'fa-tasks',
    reading: 'fa-book',
    lesson: 'fa-play-circle'
  }
  return icons[type] || 'fa-circle'
}

const triggerAvatarUpload = () => {
  avatarInput.value.click()
}

const handleAvatarUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

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

  try {
    const response = await fetch(route('api.student-profile.upload-avatar'), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: formData
    })

    if (!response.ok) {
      const text = await response.text()
      let errorMessage = t('Upload failed')
      
      try {
        const errorData = JSON.parse(text)
        errorMessage = errorData.error || errorData.message || t('Upload failed')
      } catch {
        errorMessage = text || t('Upload failed with status: {status}', { status: response.status })
      }
      
      throw new Error(errorMessage)
    }

    const result = await response.json()
    
    alert(t('Avatar uploaded successfully! The page will now reload.'))
    
    setTimeout(() => {
      window.location.href = route('student.profile.new')
    }, 1000)
    
  } catch (error) {
    console.error('Avatar upload error:', error)
    alert(t('Error uploading avatar: {error}', { error: error.message }))
  } finally {
    loading.value = false
    event.target.value = ''
  }
}

const updateProfile = async () => {
  updating.value = true
  
  try {
    await router.put('/api/student-profile/update', editForm, {
      onSuccess: () => {
        showEditModal.value = false
        alert(t('Profile updated successfully!'))
        router.reload()
      },
      onError: (errors) => {
        let errorMessage = t('Failed to update profile. Please check the form.')
        if (errors && typeof errors === 'object') {
          errorMessage = Object.values(errors).join('\n')
        }
        alert(errorMessage)
      }
    })
  } catch (error) {
    alert(t('Error updating profile: {error}', { error: error.message }))
  } finally {
    updating.value = false
  }
}

const downloadCertificate = () => {
  alert(t('Certificate download feature coming soon!'))
}

const logout = () => {
  if (confirm(t('Are you sure you want to logout?'))) {
    router.post('/logout')
  }
}
</script>

<style scoped>
.student-profile-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.profile-header {
  color: var(--primary-color);
  padding: 40px 0 20px;
  margin-bottom: 30px;
}

.header-content {
  text-align: center;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 10px;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
  padding: 20px 0 40px;
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
  border: 1px solid var(--border-color);
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
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 3px solid rgba(255,255,255,0.3);
  position: relative;
  overflow: hidden;
}

.student-avatar:hover {
  transform: scale(1.05);
  border-color: rgba(255,255,255,0.5);
}

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
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
  opacity: 0.9;
  margin-bottom: 5px;
}

.student-roll {
  font-size: 0.8rem;
  opacity: 0.8;
  background: rgba(255,255,255,0.2);
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
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
  font-weight: 600;
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

/* Profile Card */
.profile-card {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 30px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border-color);
}

.profile-header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  gap: 20px;
}

.avatar-section {
  display: flex;
  align-items: center;
  gap: 20px;
  flex: 1;
}

.avatar-placeholder {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: var(--primary-light);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  color: var(--primary-color);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 3px solid var(--border-color);
  position: relative;
  overflow: hidden;
}

.avatar-placeholder:hover {
  transform: scale(1.05);
  border-color: var(--primary-color);
}

.profile-info {
  flex: 1;
}

.profile-info h2 {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 5px;
  color: var(--text-primary);
}

.profile-info p {
  color: var(--text-secondary);
  margin-bottom: 8px;
  font-size: 1rem;
}

.member-since {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--text-muted);
  font-size: 0.9rem;
  margin-bottom: 15px;
}

.student-additional-info {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid var(--border-color);
  display: grid;
  gap: 8px;
}

.info-item {
  font-size: 0.9rem;
  color: var(--text-secondary);
  display: flex;
  align-items: center;
  gap: 8px;
}

.info-item strong {
  color: var(--text-primary);
  min-width: 80px;
}

.edit-profile-btn {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.edit-profile-btn:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Student Details Section */
.student-details-section {
  margin-top: 30px;
  padding-top: 25px;
  border-top: 1px solid var(--border-color);
}

.student-details-section h3 {
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 20px;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
  padding: 15px;
  background: var(--bg-primary);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.detail-item label {
  font-size: 0.8rem;
  color: var(--text-muted);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.detail-item span {
  font-size: 0.95rem;
  color: var(--text-primary);
  font-weight: 500;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 10px;
}

.stat-card {
  background: var(--bg-primary);
  padding: 25px 20px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 15px;
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  flex-shrink: 0;
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

.stat-info {
  flex: 1;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
  margin-bottom: 5px;
}

.stat-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
}

/* Activity Section */
.activity-section {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 30px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border-color);
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
  transition: all 0.3s ease;
}

.view-all-link:hover {
  gap: 10px;
  color: var(--primary-hover);
}

.activity-list {
  display: grid;
  gap: 15px;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background: var(--bg-primary);
  border-radius: 12px;
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
}

.activity-item:hover {
  transform: translateX(5px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.activity-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  flex-shrink: 0;
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

.activity-icon.reading {
  background: #fff3e0;
  color: #f57c00;
}

.activity-details {
  flex: 1;
  min-width: 0;
}

.activity-details h4 {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 8px;
  font-size: 1rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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
  width: 100px;
  height: 8px;
  background: var(--border-color);
  border-radius: 4px;
  overflow: hidden;
  flex-shrink: 0;
}

.progress-fill {
  height: 100%;
  background: var(--primary-color);
  transition: width 0.3s ease;
  border-radius: 4px;
}

.no-activity {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-muted);
}

.no-activity i {
  font-size: 4rem;
  margin-bottom: 20px;
  opacity: 0.3;
}

.no-activity p {
  font-size: 1.1rem;
  margin-bottom: 10px;
}

.no-activity-sub {
  font-size: 0.9rem !important;
  opacity: 0.7;
}

/* Quick Actions */
.quick-actions {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 30px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border-color);
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
  padding: 25px 20px;
  border-radius: 12px;
  text-decoration: none;
  color: var(--text-primary);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  cursor: pointer;
}

.action-card:hover {
  background: var(--primary-color);
  color: white;
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  border-color: var(--primary-color);
}

.action-card i {
  font-size: 2.5rem;
}

.action-card span {
  font-weight: 600;
  font-size: 0.95rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 0;
  max-width: 600px;
  width: 95%;
  max-height: 90vh;
  overflow-y: auto;
  border: 1px solid var(--border-color);
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px 30px;
  border-bottom: 1px solid var(--border-color);
  background: var(--bg-primary);
  border-radius: 16px 16px 0 0;
}

.modal-header h3 {
  margin: 0;
  color: var(--text-primary);
  font-size: 1.4rem;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.8rem;
  cursor: pointer;
  color: var(--text-secondary);
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: var(--bg-tertiary);
  color: var(--text-primary);
}

.modal-body {
  padding: 30px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: var(--text-primary);
  font-size: 0.9rem;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: var(--bg-primary);
  color: var(--text-primary);
  font-size: 14px;
  transition: all 0.3s ease;
  font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid var(--border-color);
}

.btn-cancel,
.btn-save {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.btn-cancel {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

.btn-cancel:hover {
  background: var(--border-color);
}

.btn-save {
  background: var(--primary-color);
  color: white;
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-save:hover:not(:disabled) {
  background: var(--primary-hover);
  transform: translateY(-1px);
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
    gap: 15px;
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
    gap: 12px;
  }
  
  .progress-bar {
    width: 100%;
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
    padding: 12px 15px;
  }
  
  .nav-item.active,
  .nav-item:hover {
    border-left: none;
    border-bottom-color: var(--primary-color);
  }
  
  .nav-divider {
    display: none;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .details-grid {
    grid-template-columns: 1fr;
  }

  .modal-content {
    width: 98%;
    margin: 10px;
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

  .profile-header {
    padding: 30px 0 15px;
  }

  .profile-card,
  .activity-section,
  .quick-actions {
    padding: 20px;
  }

  .modal-body {
    padding: 20px;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-cancel,
  .btn-save {
    width: 100%;
  }
}

/* Dark theme adjustments */
@media (prefers-color-scheme: dark) {
  .stat-icon.courses {
    background: rgba(25, 118, 210, 0.2);
    color: #64b5f6;
  }

  .stat-icon.completed {
    background: rgba(46, 125, 50, 0.2);
    color: #81c784;
  }

  .stat-icon.hours {
    background: rgba(245, 124, 0, 0.2);
    color: #ffb74d;
  }

  .stat-icon.streak {
    background: rgba(211, 47, 47, 0.2);
    color: #e57373;
  }

  .activity-icon.video {
    background: rgba(25, 118, 210, 0.2);
    color: #64b5f6;
  }

  .activity-icon.quiz {
    background: rgba(123, 31, 162, 0.2);
    color: #ba68c8;
  }

  .activity-icon.assignment {
    background: rgba(46, 125, 50, 0.2);
    color: #81c784;
  }

  .activity-icon.reading {
    background: rgba(245, 124, 0, 0.2);
    color: #ffb74d;
  }
}
</style>