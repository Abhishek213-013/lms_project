<template>
  <FrontendLayout>
    <div class="settings-page">
      <Head title="Settings" />
      
      <!-- Header -->
      <div class="settings-header">
        <div class="container">
          <div class="header-content">
            <h1 class="page-title">{{ t('Settings') }}</h1>
            <p class="page-subtitle">{{ t('Manage your account preferences and settings') }}</p>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="settings-layout">
          <!-- Main Sidebar -->
          <div class="main-sidebar">
            <div class="sidebar-header">
              <div class="student-info">
                <div class="student-avatar">
                  <img v-if="settings.user.avatar" 
                       :src="settings.user.avatar" 
                       :alt="settings.user.name + ' Avatar'" 
                       class="avatar-image">
                  <i v-else class="fas fa-user-circle"></i>
                </div>
                <div class="student-details">
                  <div class="student-name">{{ settings.user.name }}</div>
                  <div class="student-email">{{ settings.user.email }}</div>
                  <div class="student-roll" v-if="settings.user.student_info">
                    Roll: {{ settings.user.student_info.roll_number }}
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
              
              <Link href="/learning-progress" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span class="nav-text">{{ t('Learning Progress') }}</span>
              </Link>
              
              <Link href="/certificates" class="nav-item">
                <i class="fas fa-certificate"></i>
                <span class="nav-text">{{ t('Certificates') }}</span>
              </Link>
              
              <Link href="/settings" class="nav-item active">
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

          <!-- Settings Content -->
          <div class="settings-content">
            <div class="settings-container">
              <!-- Settings Sidebar Navigation -->
              <div class="settings-sidebar">
                <nav class="sidebar-nav">
                  <button 
                    v-for="section in sections" 
                    :key="section.id"
                    :class="['nav-item', { 'active': activeSection === section.id }]"
                    @click="activeSection = section.id"
                  >
                    <i :class="section.icon"></i>
                    <span>{{ section.label }}</span>
                  </button>
                </nav>
              </div>

              <!-- Main Settings Content -->
              <div class="settings-main">
                <!-- Profile Settings -->
                <div v-if="activeSection === 'profile'" class="settings-section">
                  <div class="section-header">
                    <h2>{{ t('Profile Settings') }}</h2>
                    <p>{{ t('Manage your personal information and profile details') }}</p>
                  </div>

                  <form @submit.prevent="updateProfile" class="settings-form">
                    <div class="form-grid">
                      <div class="form-group">
                        <label for="name">{{ t('Full Name') }} *</label>
                        <input 
                          id="name"
                          v-model="profileForm.name"
                          type="text" 
                          :placeholder="t('Enter your full name')"
                          required
                        >
                      </div>

                      <div class="form-group">
                        <label for="email">{{ t('Email Address') }} *</label>
                        <input 
                          id="email"
                          v-model="profileForm.email"
                          type="email" 
                          :placeholder="t('Enter your email address')"
                          required
                        >
                      </div>

                      <div class="form-group">
                        <label for="phone">{{ t('Phone Number') }}</label>
                        <input 
                          id="phone"
                          v-model="profileForm.phone"
                          type="tel" 
                          :placeholder="t('Enter your phone number')"
                        >
                      </div>

                      <div class="form-group">
                        <label for="location">{{ t('Location') }}</label>
                        <input 
                          id="location"
                          v-model="profileForm.location"
                          type="text" 
                          :placeholder="t('Enter your location')"
                        >
                      </div>

                      <div class="form-group full-width">
                        <label for="bio">{{ t('Bio') }}</label>
                        <textarea 
                          id="bio"
                          v-model="profileForm.bio"
                          :placeholder="t('Tell us about yourself')"
                          rows="4"
                          maxlength="500"
                        ></textarea>
                        <div class="char-count">{{ profileForm.bio.length }}/500</div>
                      </div>

                      <div class="form-group">
                        <label for="website">{{ t('Website') }}</label>
                        <input 
                          id="website"
                          v-model="profileForm.website"
                          type="url" 
                          :placeholder="t('https://example.com')"
                        >
                      </div>

                      <div class="form-group">
                        <label for="language">{{ t('Preferred Language') }}</label>
                        <select id="language" v-model="profileForm.language">
                          <option value="en">English</option>
                          <option value="bn">Bengali</option>
                        </select>
                      </div>

                      <div class="form-group">
                        <label for="timezone">{{ t('Timezone') }}</label>
                        <select id="timezone" v-model="profileForm.timezone">
                          <option value="Asia/Dhaka">Asia/Dhaka (UTC+6)</option>
                          <option value="UTC">UTC</option>
                          <option value="America/New_York">America/New_York (UTC-5)</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-actions">
                      <button type="button" class="btn-cancel" @click="resetProfileForm" :disabled="updatingProfile">
                        {{ t('Cancel') }}
                      </button>
                      <button type="submit" class="btn-save" :disabled="!isProfileChanged || updatingProfile">
                        <span v-if="updatingProfile" class="loading-spinner-small"></span>
                        {{ updatingProfile ? t('Updating...') : t('Save Changes') }}
                      </button>
                    </div>
                  </form>
                </div>

                <!-- Preferences -->
                <div v-if="activeSection === 'preferences'" class="settings-section">
                  <div class="section-header">
                    <h2>{{ t('Preferences') }}</h2>
                    <p>{{ t('Customize your learning experience and notifications') }}</p>
                  </div>

                  <div class="preferences-list">
                    <div class="preference-item">
                      <div class="preference-info">
                        <h4>{{ t('Email Notifications') }}</h4>
                        <p>{{ t('Receive updates and announcements via email') }}</p>
                      </div>
                      <div class="preference-toggle">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="preferencesForm.email_notifications"
                            @change="updatePreferences"
                            :disabled="updatingPreferences"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="preference-item">
                      <div class="preference-info">
                        <h4>{{ t('Push Notifications') }}</h4>
                        <p>{{ t('Get instant notifications in your browser') }}</p>
                      </div>
                      <div class="preference-toggle">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="preferencesForm.push_notifications"
                            @change="updatePreferences"
                            :disabled="updatingPreferences"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="preference-item">
                      <div class="preference-info">
                        <h4>{{ t('SMS Notifications') }}</h4>
                        <p>{{ t('Receive important updates via SMS') }}</p>
                      </div>
                      <div class="preference-toggle">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="preferencesForm.sms_notifications"
                            @change="updatePreferences"
                            :disabled="updatingPreferences"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="preference-item">
                      <div class="preference-info">
                        <h4>{{ t('Course Updates') }}</h4>
                        <p>{{ t('Get notified about new content in your enrolled courses') }}</p>
                      </div>
                      <div class="preference-toggle">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="preferencesForm.course_updates"
                            @change="updatePreferences"
                            :disabled="updatingPreferences"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="preference-item">
                      <div class="preference-info">
                        <h4>{{ t('Newsletter') }}</h4>
                        <p>{{ t('Receive weekly learning tips and course recommendations') }}</p>
                      </div>
                      <div class="preference-toggle">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="preferencesForm.newsletter"
                            @change="updatePreferences"
                            :disabled="updatingPreferences"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="preference-item">
                      <div class="preference-info">
                        <h4>{{ t('Learning Reminders') }}</h4>
                        <p>{{ t('Get reminders to continue your learning journey') }}</p>
                      </div>
                      <div class="preference-toggle">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="preferencesForm.learning_reminders"
                            @change="updatePreferences"
                            :disabled="updatingPreferences"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="preference-item">
                      <div class="preference-info">
                        <h4>{{ t('Dark Mode') }}</h4>
                        <p>{{ t('Switch between light and dark theme') }}</p>
                      </div>
                      <div class="preference-toggle">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="preferencesForm.dark_mode"
                            @change="toggleDarkMode"
                            :disabled="updatingPreferences"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div v-if="preferencesMessage" class="message" :class="preferencesMessageType">
                    {{ preferencesMessage }}
                  </div>
                </div>

                <!-- Security -->
                <div v-if="activeSection === 'security'" class="settings-section">
                  <div class="section-header">
                    <h2>{{ t('Security') }}</h2>
                    <p>{{ t('Manage your account security and privacy settings') }}</p>
                  </div>

                  <div class="security-settings">
                    <div class="security-item">
                      <div class="security-info">
                        <h4>{{ t('Two-Factor Authentication') }}</h4>
                        <p>{{ t('Add an extra layer of security to your account') }}</p>
                      </div>
                      <div class="security-action">
                        <button 
                          class="btn-security" 
                          :class="{ 'enabled': securityForm.two_factor_auth }"
                          @click="toggleTwoFactorAuth"
                          :disabled="updatingSecurity"
                        >
                          <span v-if="updatingSecurity" class="loading-spinner-small"></span>
                          {{ securityForm.two_factor_auth ? t('Disable') : t('Enable') }}
                        </button>
                      </div>
                    </div>

                    <div class="security-item">
                      <div class="security-info">
                        <h4>{{ t('Login Alerts') }}</h4>
                        <p>{{ t('Get notified when someone logs into your account') }}</p>
                      </div>
                      <div class="security-action">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="securityForm.login_alerts"
                            @change="updateSecurity"
                            :disabled="updatingSecurity"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="security-item">
                      <div class="security-info">
                        <h4>{{ t('Device Management') }}</h4>
                        <p>{{ t('View and manage devices that have access to your account') }}</p>
                      </div>
                      <div class="security-action">
                        <label class="toggle-switch">
                          <input 
                            type="checkbox" 
                            v-model="securityForm.device_management"
                            @change="updateSecurity"
                            :disabled="updatingSecurity"
                          >
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="security-item">
                      <div class="security-info">
                        <h4>{{ t('Change Password') }}</h4>
                        <p>{{ t('Update your password regularly to keep your account secure') }}</p>
                      </div>
                      <div class="security-action">
                        <button class="btn-security" @click="changePassword">
                          {{ t('Change Password') }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <div v-if="securityMessage" class="message" :class="securityMessageType">
                    {{ securityMessage }}
                  </div>
                </div>

                <!-- Billing -->
                <div v-if="activeSection === 'billing'" class="settings-section">
                  <div class="section-header">
                    <h2>{{ t('Billing & Plan') }}</h2>
                    <p>{{ t('Manage your subscription and billing information') }}</p>
                  </div>

                  <div class="billing-content">
                    <div class="current-plan">
                      <div class="plan-card">
                        <div class="plan-header">
                          <h3>{{ billingForm.plan }} Plan</h3>
                          <span class="plan-badge" :class="billingForm.status">
                            {{ billingForm.status }}
                          </span>
                        </div>
                        
                        <div class="plan-features">
                          <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Access to all free courses</span>
                          </div>
                          <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Community support</span>
                          </div>
                          <div class="feature-item">
                            <i class="fas fa-times"></i>
                            <span>Premium courses</span>
                          </div>
                          <div class="feature-item">
                            <i class="fas fa-times"></i>
                            <span>Certificate download</span>
                          </div>
                        </div>

                        <div class="plan-actions">
                          <button class="btn-upgrade" @click="upgradePlan">
                            {{ t('Upgrade to Premium') }}
                          </button>
                        </div>
                      </div>
                    </div>

                    <div class="billing-history" v-if="billingForm.next_billing_date">
                      <h4>{{ t('Billing Information') }}</h4>
                      <div class="billing-details">
                        <div class="billing-item">
                          <span class="label">{{ t('Next Billing Date') }}:</span>
                          <span class="value">{{ billingForm.next_billing_date }}</span>
                        </div>
                        <div class="billing-item">
                          <span class="label">{{ t('Payment Method') }}:</span>
                          <span class="value">{{ billingForm.payment_method || t('Not set') }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, reactive, computed, watch, getCurrentInstance } from 'vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'

// Get the current Vue instance to access global properties
const { proxy } = getCurrentInstance()

// Define props first
const props = defineProps({
  settings: {
    type: Object,
    required: true,
    default: () => ({
      profile: {
        name: '',
        email: '',
        phone: '',
        bio: '',
        location: '',
        website: '',
        language: 'en',
        timezone: 'Asia/Dhaka'
      },
      preferences: {
        email_notifications: true,
        push_notifications: true,
        sms_notifications: false,
        course_updates: true,
        newsletter: true,
        learning_reminders: true,
        dark_mode: false
      },
      security: {
        two_factor_auth: false,
        login_alerts: true,
        device_management: true
      },
      billing: {
        plan: 'Free',
        status: 'active',
        next_billing_date: null,
        payment_method: null
      },
      user: {
        name: '',
        email: '',
        avatar: null,
        student_info: null
      }
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

const activeSection = ref('profile')

// Loading states
const updatingProfile = ref(false)
const updatingPreferences = ref(false)
const updatingSecurity = ref(false)

// Messages
const preferencesMessage = ref('')
const preferencesMessageType = ref('')
const securityMessage = ref('')
const securityMessageType = ref('')

const sections = [
  { id: 'profile', label: t('Profile'), icon: 'fas fa-user' },
  { id: 'preferences', label: t('Preferences'), icon: 'fas fa-cog' },
  { id: 'security', label: t('Security'), icon: 'fas fa-shield-alt' },
  { id: 'billing', label: t('Billing'), icon: 'fas fa-credit-card' }
]

// Profile Form
const profileForm = reactive({
  name: props.settings.profile.name,
  email: props.settings.profile.email,
  phone: props.settings.profile.phone,
  bio: props.settings.profile.bio,
  location: props.settings.profile.location,
  website: props.settings.profile.website,
  language: props.settings.profile.language,
  timezone: props.settings.profile.timezone
})

const initialProfile = { ...profileForm }

const isProfileChanged = computed(() => {
  return JSON.stringify(profileForm) !== JSON.stringify(initialProfile)
})

const updateProfile = async () => {
  updatingProfile.value = true
  
  try {
    const response = await fetch('/api/settings/profile', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(profileForm)
    })

    const result = await response.json()
    
    if (response.ok) {
      alert('Profile updated successfully!')
      Object.assign(initialProfile, { ...profileForm })
    } else {
      alert('Failed to update profile: ' + (result.message || 'Unknown error'))
    }
  } catch (error) {
    alert('Error updating profile: ' + error.message)
  } finally {
    updatingProfile.value = false
  }
}

const resetProfileForm = () => {
  Object.assign(profileForm, { ...initialProfile })
}

// Preferences Form
const preferencesForm = reactive({ ...props.settings.preferences })

const updatePreferences = async () => {
  updatingPreferences.value = true
  preferencesMessage.value = ''
  
  try {
    const response = await fetch('/api/settings/preferences', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(preferencesForm)
    })

    const result = await response.json()
    
    if (response.ok) {
      preferencesMessage.value = 'Preferences updated successfully!'
      preferencesMessageType.value = 'success'
    } else {
      preferencesMessage.value = 'Failed to update preferences: ' + (result.message || 'Unknown error')
      preferencesMessageType.value = 'error'
    }
  } catch (error) {
    preferencesMessage.value = 'Error updating preferences: ' + error.message
    preferencesMessageType.value = 'error'
  } finally {
    updatingPreferences.value = false
    
    // Clear message after 3 seconds
    setTimeout(() => {
      preferencesMessage.value = ''
    }, 3000)
  }
}

const toggleDarkMode = () => {
  window.dispatchEvent(new CustomEvent('themeChanged', { 
    detail: { theme: preferencesForm.dark_mode ? 'dark' : 'light' } 
  }))
}

// Security Form
const securityForm = reactive({ ...props.settings.security })

const toggleTwoFactorAuth = async () => {
  updatingSecurity.value = true
  securityMessage.value = ''
  
  try {
    const response = await fetch('/api/settings/security', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        two_factor_auth: !securityForm.two_factor_auth
      })
    })

    const result = await response.json()
    
    if (response.ok) {
      securityForm.two_factor_auth = !securityForm.two_factor_auth
      securityMessage.value = `Two-factor authentication ${securityForm.two_factor_auth ? 'enabled' : 'disabled'}`
      securityMessageType.value = 'success'
    } else {
      securityMessage.value = 'Failed to update security settings: ' + (result.message || 'Unknown error')
      securityMessageType.value = 'error'
    }
  } catch (error) {
    securityMessage.value = 'Error updating security settings: ' + error.message
    securityMessageType.value = 'error'
  } finally {
    updatingSecurity.value = false
    
    // Clear message after 3 seconds
    setTimeout(() => {
      securityMessage.value = ''
    }, 3000)
  }
}

const updateSecurity = async () => {
  updatingSecurity.value = true
  securityMessage.value = ''
  
  try {
    const response = await fetch('/api/settings/security', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(securityForm)
    })

    const result = await response.json()
    
    if (response.ok) {
      securityMessage.value = 'Security settings updated successfully!'
      securityMessageType.value = 'success'
    } else {
      securityMessage.value = 'Failed to update security settings: ' + (result.message || 'Unknown error')
      securityMessageType.value = 'error'
    }
  } catch (error) {
    securityMessage.value = 'Error updating security settings: ' + error.message
    securityMessageType.value = 'error'
  } finally {
    updatingSecurity.value = false
    
    // Clear message after 3 seconds
    setTimeout(() => {
      securityMessage.value = ''
    }, 3000)
  }
}

const changePassword = () => {
  const newPassword = prompt('Enter new password:')
  if (newPassword) {
    alert('Password change functionality would be implemented here')
  }
}

// Billing Form
const billingForm = reactive({ ...props.settings.billing })

const upgradePlan = () => {
  alert('Redirecting to premium plan selection...')
}

const logout = () => {
  router.post('/logout')
}

// Watch for language changes
watch(() => profileForm.language, (newLang) => {
  window.dispatchEvent(new CustomEvent('languageChanged', { 
    detail: { language: newLang } 
  }))
})
</script>

<style scoped>
/* Add these new styles to your existing CSS */

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.student-roll {
  font-size: 0.8rem;
  opacity: 0.8;
  background: rgba(255,255,255,0.2);
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
  margin-top: 5px;
}

.loading-spinner-small {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  display: inline-block;
  margin-right: 8px;
}

.char-count {
  font-size: 0.8rem;
  color: var(--text-muted);
  text-align: right;
  margin-top: 5px;
}

.message {
  padding: 12px 16px;
  border-radius: 8px;
  margin-top: 20px;
  font-weight: 500;
}

.message.success {
  background: #e8f5e8;
  color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.message.error {
  background: #ffebee;
  color: #d32f2f;
  border: 1px solid #ffcdd2;
}

.btn-save:disabled,
.btn-cancel:disabled,
.btn-security:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.toggle-switch input:disabled + .slider {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Rest of your existing CSS remains the same */
.settings-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.settings-header {
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
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Settings Layout */
.settings-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
  padding: 40px 0;
  align-items: start;
}

/* Main Sidebar */
.main-sidebar {
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
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: white;
  border: 3px solid rgba(255,255,255,0.3);
  position: relative;
  overflow: hidden;
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
  opacity: 0.9;
  margin-bottom: 5px;
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

/* Settings Content */
.settings-content {
  background: var(--bg-secondary);
  border-radius: 16px;
  box-shadow: var(--shadow);
  overflow: hidden;
}

.settings-container {
  display: grid;
  grid-template-columns: 250px 1fr;
  min-height: 600px;
}

/* Settings Sidebar */
.settings-sidebar {
  background: var(--bg-primary);
  border-right: 1px solid var(--border-color);
  padding: 20px 0;
}

.settings-sidebar .sidebar-nav {
  padding: 0;
}

.settings-sidebar .nav-item {
  border-left: none;
  border-right: 3px solid transparent;
  padding: 12px 20px;
  font-size: 14px;
  color: var(--text-secondary);
}

.settings-sidebar .nav-item:hover {
  background: var(--bg-tertiary);
  color: var(--primary-color);
  border-right-color: var(--primary-light);
}

.settings-sidebar .nav-item.active {
  background: var(--primary-light);
  color: var(--primary-color);
  border-right-color: var(--primary-color);
}

/* Settings Main Content */
.settings-main {
  padding: 30px;
  background: var(--bg-secondary);
}

.settings-section {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.section-header {
  margin-bottom: 30px;
}

.section-header h2 {
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 8px;
}

.section-header p {
  color: var(--text-secondary);
  font-size: 1rem;
}

/* Form Styles */
.settings-form {
  max-width: 800px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 30px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px 15px;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  background: var(--bg-primary);
  color: var(--text-primary);
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
}

.btn-cancel {
  padding: 12px 24px;
  border: 2px solid var(--border-color);
  background: transparent;
  color: var(--text-primary);
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-cancel:hover {
  border-color: var(--text-muted);
  background: var(--bg-primary);
}

.btn-save {
  padding: 12px 24px;
  border: none;
  background: var(--primary-color);
  color: white;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-save:hover:not(:disabled) {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Preferences List */
.preferences-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 800px;
}

.preference-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background: var(--bg-primary);
  border-radius: 12px;
  transition: all 0.3s ease;
}

.preference-item:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow);
}

.preference-info h4 {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 5px;
}

.preference-info p {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

/* Toggle Switch */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: var(--border-color);
  transition: .4s;
  border-radius: 34px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: var(--primary-color);
}

input:checked + .slider:before {
  transform: translateX(26px);
}

/* Security Settings */
.security-settings {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 800px;
}

.security-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background: var(--bg-primary);
  border-radius: 12px;
  transition: all 0.3s ease;
}

.security-item:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow);
}

.security-info h4 {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 5px;
}

.security-info p {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.btn-security {
  padding: 10px 20px;
  border: 2px solid var(--primary-color);
  background: transparent;
  color: var(--primary-color);
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-security.enabled {
  background: var(--primary-color);
  color: white;
}

.btn-security:hover {
  background: var(--primary-color);
  color: white;
  transform: translateY(-2px);
}

/* Billing */
.billing-content {
  max-width: 800px;
}

.current-plan {
  margin-bottom: 30px;
}

.plan-card {
  background: var(--bg-primary);
  border-radius: 12px;
  padding: 25px;
  box-shadow: var(--shadow);
}

.plan-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.plan-header h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.plan-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.plan-badge.active {
  background: #e8f5e8;
  color: #2e7d32;
}

.plan-features {
  margin-bottom: 25px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
  color: var(--text-primary);
}

.feature-item i {
  width: 16px;
}

.feature-item i.fa-check {
  color: #10b981;
}

.feature-item i.fa-times {
  color: #ef4444;
}

.btn-upgrade {
  width: 100%;
  padding: 15px;
  border: none;
  background: var(--primary-color);
  color: white;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-upgrade:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.billing-history {
  background: var(--bg-primary);
  border-radius: 12px;
  padding: 25px;
  box-shadow: var(--shadow);
}

.billing-history h4 {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 15px;
}

.billing-details {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.billing-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid var(--border-light);
}

.billing-item:last-child {
  border-bottom: none;
}

.billing-item .label {
  color: var(--text-secondary);
  font-weight: 500;
}

.billing-item .value {
  color: var(--text-primary);
  font-weight: 600;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .settings-layout {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .main-sidebar {
    position: static;
    order: 2;
  }
  
  .settings-content {
    order: 1;
  }
}

@media (max-width: 768px) {
  .settings-container {
    grid-template-columns: 1fr;
  }
  
  .settings-sidebar {
    border-right: none;
    border-bottom: 1px solid var(--border-color);
  }
  
  .settings-sidebar .sidebar-nav {
    display: flex;
    overflow-x: auto;
    padding: 10px;
  }
  
  .settings-sidebar .nav-item {
    white-space: nowrap;
    border-right: none;
    border-bottom: 3px solid transparent;
    flex-shrink: 0;
  }
  
  .settings-sidebar .nav-item.active {
    border-right-color: transparent;
    border-bottom-color: var(--primary-color);
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .preference-item,
  .security-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .main-sidebar .sidebar-nav {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    padding: 20px;
  }
  
  .main-sidebar .nav-item {
    border-left: none;
    border-bottom: 3px solid transparent;
    justify-content: center;
    text-align: center;
    flex-direction: column;
    border-radius: 8px;
  }
  
  .main-sidebar .nav-item.active,
  .main-sidebar .nav-item:hover {
    border-left: none;
    border-bottom-color: var(--primary-color);
  }
  
  .main-sidebar .nav-divider {
    display: none;
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 2rem;
  }
  
  .settings-main {
    padding: 20px;
  }
  
  .section-header h2 {
    font-size: 1.5rem;
  }
  
  .main-sidebar .sidebar-nav {
    grid-template-columns: 1fr;
  }
}
</style>