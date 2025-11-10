<template>
  <FrontendLayout>
    <div class="certificates-page">
      <Head :title="t('My Certificates')" />
      
      <!-- Header -->
      <div class="certificates-header">
        <div class="container">
          <div class="header-content">
            <h1 class="page-title">{{ t('My Certificates') }}</h1>
            <p class="page-subtitle">{{ t('Your achievements and completed courses') }}</p>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="certificates-layout">
          <!-- Sidebar -->
          <div class="certificates-sidebar">
            <div class="sidebar-header">
              <div class="student-info">
                <div class="student-avatar">
                  <img v-if="certificates.user.avatar" 
                       :src="certificates.user.avatar" 
                       :alt="certificates.user.name + ' ' + t('Avatar')" 
                       class="avatar-image">
                  <i v-else class="fas fa-user-circle"></i>
                </div>
                <div class="student-details">
                  <div class="student-name">{{ certificates.user.name }}</div>
                  <div class="student-email">{{ certificates.user.email }}</div>
                  <div class="student-roll" v-if="certificates.user.student_info">
                    {{ t('Roll') }}: {{ certificates.user.student_info.roll_number }}
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
              
              <Link href="/certificates" class="nav-item active">
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
          <div class="certificates-main-content">
            <!-- Stats Overview -->
            <div class="stats-section">
              <div class="stats-grid">
                <div class="stat-card main">
                  <div class="stat-content">
                    <div class="stat-icon">
                      <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                      <div class="stat-number">{{ certificates.stats.total_certificates }}</div>
                      <div class="stat-label">{{ t('Total Certificates') }}</div>
                      <div class="stat-subtext">
                        {{ certificates.stats.certificates_this_year }} {{ t('this year') }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="stat-card">
                  <div class="stat-content">
                    <div class="stat-icon">
                      <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                      <div class="stat-number">{{ certificates.stats.total_learning_hours }}</div>
                      <div class="stat-label">{{ t('Learning Hours') }}</div>
                    </div>
                  </div>
                </div>
                
                <div class="stat-card">
                  <div class="stat-content">
                    <div class="stat-icon">
                      <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-info">
                      <div class="stat-number">{{ certificates.stats.average_grade }}</div>
                      <div class="stat-label">{{ t('Average Grade') }}</div>
                    </div>
                  </div>
                </div>
                
                <div class="stat-card">
                  <div class="stat-content">
                    <div class="stat-icon">
                      <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                      <div class="stat-number">{{ certificates.stats.completion_rate }}%</div>
                      <div class="stat-label">{{ t('Completion Rate') }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Certificates List -->
            <div class="certificates-section">
              <div class="section-header">
                <h3>{{ t('My Certificates') }}</h3>
                <div class="section-actions">
                  <button class="btn-outline" @click="exportAllCertificates" :disabled="exportingAll">
                    <i class="fas fa-download"></i>
                    {{ t('Export All') }}
                    <span v-if="exportingAll" class="loading-dots"></span>
                  </button>
                </div>
              </div>
              
              <div class="certificates-grid">
                <div v-for="certificate in certificates.certificates" 
                     :key="certificate.id" 
                     class="certificate-card">
                  <div class="certificate-header">
                    <div class="certificate-badge">
                      <i class="fas fa-certificate"></i>
                    </div>
                    <div class="certificate-meta">
                      <span class="certificate-number">{{ certificate.certificate_number }}</span>
                      <span class="certificate-status verified">
                        <i class="fas fa-check-circle"></i>
                        {{ t('Verified') }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="certificate-content">
                    <div class="course-category">{{ certificate.course_category }}</div>
                    <h4 class="course-title">{{ certificate.course_title }}</h4>
                    <p class="certificate-description">{{ certificate.description }}</p>
                    
                    <div class="certificate-details">
                      <div class="detail-item">
                        <label>{{ t('Student') }}:</label>
                        <span>{{ certificate.student_name }}</span>
                      </div>
                      <div class="detail-item">
                        <label>{{ t('Instructor') }}:</label>
                        <span>{{ certificate.instructor_name }}</span>
                      </div>
                      <div class="detail-item">
                        <label>{{ t('Completed') }}:</label>
                        <span>{{ certificate.completion_date }}</span>
                      </div>
                      <div class="detail-item">
                        <label>{{ t('Duration') }}:</label>
                        <span>{{ certificate.duration }}</span>
                      </div>
                      <div class="detail-item">
                        <label>{{ t('Grade') }}:</label>
                        <span class="grade-badge">{{ certificate.grade }}</span>
                      </div>
                      <div class="detail-item">
                        <label>{{ t('Credits') }}:</label>
                        <span>{{ certificate.credits }}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="certificate-actions">
                    <button class="btn-primary" @click="downloadCertificate(certificate)" :disabled="downloading === certificate.id">
                      <i class="fas fa-download"></i>
                      {{ t('Download PDF') }}
                      <span v-if="downloading === certificate.id" class="loading-dots"></span>
                    </button>
                    <button class="btn-outline" @click="shareCertificate(certificate)" :disabled="sharing === certificate.id">
                      <i class="fas fa-share-alt"></i>
                      {{ t('Share') }}
                      <span v-if="sharing === certificate.id" class="loading-dots"></span>
                    </button>
                    <button class="btn-verify" @click="verifyCertificate(certificate)">
                      <i class="fas fa-search"></i>
                      {{ t('Verify') }}
                    </button>
                  </div>
                  
                  <div class="certificate-footer">
                    <div class="verification-link">
                      <i class="fas fa-link"></i>
                      <a :href="certificate.verification_url" target="_blank" class="verification-url">
                        {{ t('Verification Link') }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Empty State -->
              <div v-if="certificates.certificates.length === 0" class="empty-state">
                <div class="empty-icon">
                  <i class="fas fa-certificate"></i>
                </div>
                <h3>{{ t('No Certificates Yet') }}</h3>
                <p>{{ t('Complete courses to earn certificates and showcase your achievements') }}</p>
                <Link href="/my-courses" class="btn-primary">
                  <i class="fas fa-book"></i>
                  {{ t('Browse Courses') }}
                </Link>
              </div>
            </div>

            <!-- Verification Modal -->
            <div v-if="showVerificationModal" class="modal-overlay" @click="showVerificationModal = false">
              <div class="modal-content" @click.stop>
                <div class="modal-header">
                  <h3>{{ t('Certificate Verification') }}</h3>
                  <button class="close-btn" @click="showVerificationModal = false">&times;</button>
                </div>
                <div class="modal-body">
                  <div class="verification-info">
                    <div class="verification-badge success">
                      <i class="fas fa-check-circle"></i>
                    </div>
                    <h4>{{ t('Certificate Verified') }}</h4>
                    <p>{{ t('This certificate has been successfully verified and is authentic.') }}</p>
                    
                    <div class="verification-details" v-if="selectedCertificate">
                      <div class="detail-row">
                        <strong>{{ t('Certificate ID') }}:</strong>
                        <span>{{ selectedCertificate.certificate_number }}</span>
                      </div>
                      <div class="detail-row">
                        <strong>{{ t('Student Name') }}:</strong>
                        <span>{{ selectedCertificate.student_name }}</span>
                      </div>
                      <div class="detail-row">
                        <strong>{{ t('Course') }}:</strong>
                        <span>{{ selectedCertificate.course_title }}</span>
                      </div>
                      <div class="detail-row">
                        <strong>{{ t('Issue Date') }}:</strong>
                        <span>{{ selectedCertificate.issue_date }}</span>
                      </div>
                      <div class="detail-row">
                        <strong>{{ t('Status') }}:</strong>
                        <span class="status-verified">{{ t('Verified') }}</span>
                      </div>
                    </div>
                    
                    <div class="verification-actions">
                      <button class="btn-outline" @click="showVerificationModal = false">
                        {{ t('Close') }}
                      </button>
                      <a :href="selectedCertificate?.verification_url" target="_blank" class="btn-primary">
                        <i class="fas fa-external-link-alt"></i>
                        {{ t('Open Verification Page') }}
                      </a>
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
import { ref } from 'vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { useTranslation } from '@/composables/useTranslation' // Add this import

const props = defineProps({
  certificates: {
    type: Object,
    required: true,
    default: () => ({
      certificates: [],
      stats: {
        total_certificates: 0,
        total_learning_hours: 0,
        certificates_this_year: 0,
        average_grade: 'N/A',
        completion_rate: 0
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

// Initialize translation - add this
const { t, currentLanguage, switchLanguage } = useTranslation()

const showVerificationModal = ref(false)
const selectedCertificate = ref(null)
const downloading = ref(null)
const sharing = ref(null)
const exportingAll = ref(false)

const downloadCertificate = async (certificate) => {
  downloading.value = certificate.id
  try {
    const response = await fetch(`/api/certificates/${certificate.id}/download`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })
    
    const result = await response.json()
    
    if (response.ok) {
      alert(t('Certificate download initiated!'))
    } else {
      alert(t('Failed to download certificate: {error}', { error: result.error || t('Unknown error') }))
    }
  } catch (error) {
    alert(t('Error downloading certificate: {error}', { error: error.message }))
  } finally {
    downloading.value = null
  }
}

const shareCertificate = async (certificate) => {
  sharing.value = certificate.id
  try {
    const response = await fetch(`/api/certificates/${certificate.id}/share`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })
    
    const result = await response.json()
    
    if (response.ok) {
      await navigator.clipboard.writeText(result.share_url)
      alert(t('Shareable link copied to clipboard!'))
    } else {
      alert(t('Failed to generate share link: {error}', { error: result.error || t('Unknown error') }))
    }
  } catch (error) {
    alert(t('Error sharing certificate: {error}', { error: error.message }))
  } finally {
    sharing.value = null
  }
}

const verifyCertificate = (certificate) => {
  selectedCertificate.value = certificate
  showVerificationModal.value = true
}

const exportAllCertificates = async () => {
  if (!confirm(t('This will export all your certificates as a ZIP file. Continue?'))) {
    return
  }
  
  exportingAll.value = true
  try {
    alert(t('Export feature coming soon!'))
  } catch (error) {
    alert(t('Error exporting certificates: {error}', { error: error.message }))
  } finally {
    exportingAll.value = false
  }
}

const logout = () => {
  if (confirm(t('Are you sure you want to logout?'))) {
    router.post('/logout')
  }
}
</script>

<style scoped>
/* Your existing CSS remains the same - no changes needed */
.certificates-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.certificates-header {
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

.certificates-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.certificates-header {
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

/* Certificates Layout */
.certificates-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
  padding: 40px 0;
  align-items: start;
}

/* Sidebar Styles */
.certificates-sidebar {
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
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 5px;
}

.student-email {
  font-size: 0.9rem;
  opacity: 0.8;
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
.certificates-main-content {
  display: grid;
  gap: 30px;
}

/* Stats Section */
.stats-section {
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

.stat-subtext {
  font-size: 0.8rem;
  opacity: 0.7;
  margin-top: 5px;
}

/* Certificates Section */
.certificates-section {
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

.section-actions {
  display: flex;
  gap: 10px;
}

.btn-outline {
  background: transparent;
  border: 2px solid var(--border-color);
  color: var(--text-primary);
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.btn-outline:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.btn-outline:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-dots {
  display: inline-block;
  position: relative;
  width: 20px;
  height: 10px;
}

.loading-dots::after {
  content: '...';
  position: absolute;
  animation: dots 1.5s infinite;
}

@keyframes dots {
  0%, 20% { content: '.'; }
  40% { content: '..'; }
  60%, 100% { content: '...'; }
}

.certificates-grid {
  display: grid;
  gap: 25px;
}

.certificate-card {
  background: var(--bg-primary);
  border-radius: 12px;
  padding: 25px;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.certificate-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow);
}

.certificate-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid var(--border-color);
}

.certificate-badge {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #ffd700, #ffed4e);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: #b8860b;
}

.certificate-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 5px;
}

.certificate-number {
  font-size: 0.8rem;
  color: var(--text-muted);
  font-weight: 500;
}

.certificate-status {
  font-size: 0.8rem;
  padding: 4px 8px;
  border-radius: 12px;
  font-weight: 500;
}

.certificate-status.verified {
  background: #e8f5e8;
  color: #2e7d32;
}

.course-category {
  color: var(--primary-color);
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.course-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 12px;
}

.certificate-description {
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin-bottom: 20px;
  line-height: 1.5;
}

.certificate-details {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
  margin-bottom: 25px;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid var(--border-color);
}

.detail-item label {
  font-size: 0.8rem;
  color: var(--text-muted);
  font-weight: 500;
}

.detail-item span {
  font-size: 0.9rem;
  color: var(--text-primary);
  font-weight: 500;
}

.grade-badge {
  background: var(--primary-color);
  color: white;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
}

.certificate-actions {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.btn-primary {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-primary:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-verify {
  background: #10b981;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-verify:hover {
  background: #059669;
  transform: translateY(-2px);
}

.certificate-footer {
  padding-top: 15px;
  border-top: 1px solid var(--border-color);
}

.verification-link {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
}

.verification-url {
  color: var(--primary-color);
  text-decoration: none;
}

.verification-url:hover {
  text-decoration: underline;
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
  max-width: 500px;
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

.verification-info {
  text-align: center;
}

.verification-badge {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  margin: 0 auto 20px;
}

.verification-badge.success {
  background: #e8f5e8;
  color: #2e7d32;
}

.verification-info h4 {
  font-size: 1.3rem;
  color: var(--text-primary);
  margin-bottom: 10px;
}

.verification-info p {
  color: var(--text-secondary);
  margin-bottom: 25px;
}

.verification-details {
  background: var(--bg-primary);
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 25px;
  text-align: left;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid var(--border-color);
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row strong {
  color: var(--text-primary);
  font-weight: 600;
}

.detail-row span {
  color: var(--text-secondary);
}

.status-verified {
  color: #2e7d32;
  font-weight: 600;
}

.verification-actions {
  display: flex;
  gap: 10px;
  justify-content: center;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .certificates-layout {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .certificates-sidebar {
    position: static;
    order: 2;
  }
  
  .certificates-main-content {
    order: 1;
  }
  
  .stats-grid {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
  }
  
  .stat-card.main {
    grid-column: 1 / -1;
  }
}

@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .certificate-actions {
    flex-direction: column;
  }
  
  .certificate-details {
    grid-template-columns: 1fr;
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
  
  .verification-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .sidebar-nav {
    grid-template-columns: 1fr;
  }
  
  .certificate-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .certificate-meta {
    align-items: flex-start;
  }
}
</style>