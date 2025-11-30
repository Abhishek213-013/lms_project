<template>
  <FrontendLayout>
    <div class="my-courses-page">
      <Head :title="t('My Courses')" />
      
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
                  <img v-if="courses.user?.avatar" 
                       :src="courses.user.avatar" 
                       :alt="(courses.user?.name || 'User') + ' ' + t('Avatar')" 
                       class="avatar-image">
                  <i v-else class="fas fa-user-circle"></i>
                </div>
                <div class="student-details">
                  <div class="student-name">{{ courses.user?.name || 'Student' }}</div>
                  <div class="student-email">{{ courses.user?.email || 'No email' }}</div>
                  <div class="student-roll" v-if="courses.user?.student_info?.roll_number">
                    {{ t('Roll') }}: {{ courses.user.student_info.roll_number }}
                  </div>
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
            <!-- Debug Section -->
            <!-- <div class="debug-section" v-if="showDebug">
              <h4>{{ t('Debug Information') }}</h4>
              <div class="debug-info">
                <p><strong>{{ t('Active Tab') }}:</strong> {{ activeTab }}</p>
                <p><strong>{{ t('Loading') }}:</strong> {{ loading }}</p>
                <p><strong>{{ t('Paid Enrolled Courses') }}:</strong> {{ getTabCount('enrolled') }}</p>
                <p><strong>{{ t('Paid Completed Courses') }}:</strong> {{ getTabCount('completed') }}</p>
                <p><strong>{{ t('Wishlist Courses') }}:</strong> {{ getTabCount('wishlist') }}</p>
                <p><strong>{{ t('User Data') }}:</strong> {{ courses.user ? 'Available' : 'Missing' }}</p>
              </div>
              
              <div v-if="courses.enrolled?.length > 0" class="debug-courses">
                <h5>{{ t('Paid Enrolled Courses Data') }}:</h5>
                <div v-for="(course, index) in courses.enrolled.slice(0, 3)" :key="index" class="debug-course">
                  <p><strong>ID:</strong> {{ course.id || course.class_id || 'N/A' }}</p>
                  <p><strong>Title:</strong> {{ course.title || course.name || 'N/A' }}</p>
                  <p><strong>Progress:</strong> {{ course.progress || 0 }}%</p>
                  <p><strong>Payment Method:</strong> {{ course.payment_method || 'N/A' }}</p>
                  <p><strong>Payment Verified:</strong> {{ course.payment_verified_at || 'N/A' }}</p>
                </div>
              </div>
              
              <button @click="showDebug = !showDebug" class="btn-debug-toggle">
                {{ showDebug ? t('Hide Debug') : t('Show Debug') }}
              </button>
            </div> -->

            <!-- Tabs -->
            <div class="courses-tabs">
              <button 
                v-for="tab in tabs" 
                :key="tab.id"
                :class="['tab-btn', { 'active': activeTab === tab.id }]"
                @click="activeTab = tab.id"
              >
                <i :class="tab.icon"></i>
                {{ t(tab.label) }}
                <span class="tab-count">{{ getTabCount(tab.id) }}</span>
              </button>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
              <div class="loading-spinner"></div>
              <p>{{ t('Loading courses...') }}</p>
            </div>

            <!-- Paid Enrolled Courses -->
            <div v-else-if="activeTab === 'enrolled' && getTabCount('enrolled') > 0" class="tab-content">
              <div class="payment-status-banner">
                <i class="fas fa-check-circle"></i>
                <span>{{ t('All courses shown are paid and verified') }}</span>
              </div>
              
              <div class="courses-grid">
                <div v-for="course in courses.enrolled" :key="getCourseData(course).id" class="course-card">
                  <div class="course-image">
                    <img v-if="getCourseData(course).thumbnail" 
                         :src="getCourseData(course).thumbnail" 
                         :alt="getCourseData(course).title" 
                         class="course-thumbnail">
                    <div v-else class="image-placeholder">
                      <i class="fas fa-book"></i>
                    </div>
                    <div class="course-progress">
                      <div class="progress-text">{{ getCourseData(course).progress }}% {{ t('Complete') }}</div>
                      <div class="progress-bar">
                        <div class="progress-fill" :style="{ width: getCourseData(course).progress + '%' }"></div>
                      </div>
                    </div>
                    <!-- Payment Badge -->
                    <div class="payment-badge" v-if="getCourseData(course).payment_verified_at">
                      <i class="fas fa-check-circle"></i>
                      {{ t('Paid') }}
                    </div>
                  </div>
                  
                  <div class="course-content">
                    <div class="course-category">{{ getCourseData(course).category }}</div>
                    <h3 class="course-title">{{ getCourseData(course).title }}</h3>
                    <p class="course-instructor">{{ t('By') }} {{ getCourseData(course).instructor }}</p>
                    
                    <!-- Payment Info -->
                    <div class="payment-info" v-if="getCourseData(course).payment_method">
                      <div class="payment-method">
                        <i class="fas fa-credit-card"></i>
                        {{ t('Paid via') }}: {{ formatPaymentMethod(getCourseData(course).payment_method) }}
                      </div>
                      <div class="payment-date" v-if="getCourseData(course).payment_verified_at">
                        <i class="fas fa-calendar-check"></i>
                        {{ t('Verified') }}: {{ getCourseData(course).payment_verified_at }}
                      </div>
                    </div>
                    
                    <div class="course-meta">
                      <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        {{ getCourseData(course).duration }}
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-play-circle"></i>
                        {{ getCourseData(course).lessons_completed }}/{{ getCourseData(course).total_lessons }} {{ t('Lessons') }}
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-star"></i>
                        {{ getCourseData(course).rating }}
                      </div>
                    </div>
                    
                    <div class="course-actions">
                      <Link 
                        :href="route('student.learning', { courseId: getCourseData(course).id })"
                        class="btn-continue" 
                        :disabled="continuingCourse === getCourseData(course).id"
                      >
                        <i class="fas fa-play"></i>
                        {{ getCourseData(course).progress === 0 ? t('Start Learning') : t('Continue') }}
                        <span v-if="continuingCourse === getCourseData(course).id" class="loading-dots"></span>
                      </Link>
                      <button class="btn-outline" @click="viewCourseDetails(course)">
                        <i class="fas fa-info-circle"></i>
                        {{ t('Details') }}
                      </button>
                    </div>
                    
                    <div class="last-accessed">
                      <i class="fas fa-history"></i>
                      {{ t('Last accessed') }} {{ getCourseData(course).last_accessed }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Paid Completed Courses -->
            <div v-else-if="activeTab === 'completed' && getTabCount('completed') > 0" class="tab-content">
              <div class="payment-status-banner">
                <i class="fas fa-check-circle"></i>
                <span>{{ t('All completed courses shown are paid and verified') }}</span>
              </div>
              
              <div class="courses-grid">
                <div v-for="course in courses.completed" :key="getCourseData(course).id" class="course-card completed">
                  <div class="course-image">
                    <img v-if="getCourseData(course).thumbnail" 
                         :src="getCourseData(course).thumbnail" 
                         :alt="getCourseData(course).title" 
                         class="course-thumbnail">
                    <div v-else class="image-placeholder">
                      <i class="fas fa-book"></i>
                    </div>
                    <div class="completion-badge">
                      <i class="fas fa-check-circle"></i>
                      {{ t('Completed') }}
                    </div>
                    <!-- Payment Badge -->
                    <div class="payment-badge" v-if="getCourseData(course).payment_verified_at">
                      <i class="fas fa-check-circle"></i>
                      {{ t('Paid') }}
                    </div>
                  </div>
                  
                  <div class="course-content">
                    <div class="course-category">{{ getCourseData(course).category }}</div>
                    <h3 class="course-title">{{ getCourseData(course).title }}</h3>
                    <p class="course-instructor">{{ t('By') }} {{ getCourseData(course).instructor }}</p>
                    
                    <!-- Payment Info -->
                    <div class="payment-info" v-if="getCourseData(course).payment_method">
                      <div class="payment-method">
                        <i class="fas fa-credit-card"></i>
                        {{ t('Paid via') }}: {{ formatPaymentMethod(getCourseData(course).payment_method) }}
                      </div>
                      <div class="payment-date" v-if="getCourseData(course).payment_verified_at">
                        <i class="fas fa-calendar-check"></i>
                        {{ t('Verified') }}: {{ getCourseData(course).payment_verified_at }}
                      </div>
                    </div>
                    
                    <div class="course-meta">
                      <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        {{ getCourseData(course).duration }}
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-calendar-check"></i>
                        {{ t('Completed') }} {{ getCourseData(course).completed_date }}
                      </div>
                    </div>
                    
                    <div class="course-actions">
                      <button v-if="getCourseData(course).certificate_available" 
                              class="btn-certificate" 
                              @click="downloadCertificate(course)">
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
            <div v-else-if="activeTab === 'wishlist' && getTabCount('wishlist') > 0" class="tab-content">
              <div class="courses-grid">
                <div v-for="course in courses.wishlist" :key="getCourseData(course).id" class="course-card wishlist">
                  <div class="course-image">
                    <img v-if="getCourseData(course).thumbnail" 
                         :src="getCourseData(course).thumbnail" 
                         :alt="getCourseData(course).title" 
                         class="course-thumbnail">
                    <div v-else class="image-placeholder">
                      <i class="fas fa-book"></i>
                    </div>
                    <div class="wishlist-actions">
                      <button class="btn-remove-wishlist" 
                              @click="removeFromWishlist(course)" 
                              :disabled="removingWishlist === getCourseData(course).id">
                        <i class="fas fa-heart"></i>
                        <span v-if="removingWishlist === getCourseData(course).id" class="loading-dots"></span>
                      </button>
                    </div>
                  </div>
                  
                  <div class="course-content">
                    <div class="course-category">{{ getCourseData(course).category }}</div>
                    <h3 class="course-title">{{ getCourseData(course).title }}</h3>
                    <p class="course-instructor">{{ t('By') }} {{ getCourseData(course).instructor }}</p>
                    
                    <div class="course-meta">
                      <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        {{ getCourseData(course).duration }}
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-star"></i>
                        {{ getCourseData(course).rating }}
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-users"></i>
                        {{ getCourseData(course).students.toLocaleString() }}
                      </div>
                    </div>
                    
                    <div class="course-price-section">
                      <div class="course-price">${{ getCourseData(course).price }}</div>
                      <button class="btn-enroll" 
                              @click="enrollCourse(course)" 
                              :disabled="enrollingCourse === getCourseData(course).id">
                        {{ t('Enroll Now') }}
                        <span v-if="enrollingCourse === getCourseData(course).id" class="loading-dots"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- No Paid Courses State -->
            <div v-else-if="activeTab === 'enrolled' && getTabCount('enrolled') === 0" class="tab-content">
              <div class="no-paid-courses-state">
                <div class="empty-icon">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>{{ t('No Paid Courses Yet') }}</h3>
                <p>{{ t('You haven\'t purchased any courses yet. Browse our course catalog and enroll in your desired courses.') }}</p>
                <div class="action-buttons">
                  <Link href="/courses" class="btn-primary">
                    <i class="fas fa-search"></i>
                    {{ t('Browse Courses') }}
                  </Link>
                  <Link href="/payment-history" class="btn-outline">
                    <i class="fas fa-history"></i>
                    {{ t('View Payment History') }}
                  </Link>
                </div>
              </div>
            </div>

            <!-- Empty State for other tabs -->
            <div v-else-if="!loading && getTabCount(activeTab) === 0" class="empty-state">
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
  </FrontendLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { useTranslation } from '@/composables/useTranslation'

const props = defineProps({
  courses: {
    type: Object,
    required: true,
    default: () => ({
      enrolled: [],
      completed: [],
      wishlist: [],
      user: {
        name: '',
        email: '',
        avatar: null,
        student_info: null
      }
    })
  }
})

// Initialize translation
const { t, currentLanguage, switchLanguage } = useTranslation()

const activeTab = ref('enrolled')
const loading = ref(false)
const continuingCourse = ref(null)
const enrollingCourse = ref(null)
const removingWishlist = ref(null)
const showDebug = ref(true)

const tabs = computed(() => [
  { id: 'enrolled', label: 'Enrolled', icon: 'fas fa-play-circle' },
  { id: 'completed', label: 'Completed', icon: 'fas fa-check-circle' },
  { id: 'wishlist', label: 'Wishlist', icon: 'fas fa-heart' }
])

// More robust data access
const getTabCount = (tabId) => {
  const courses = props.courses[tabId]
  return Array.isArray(courses) ? courses.length : 0
}

// Safe course data access
const getCourseData = (course) => {
  if (!course) {
    return {
      id: 'unknown',
      title: 'Unknown Course',
      category: 'General',
      instructor: 'Unknown Instructor',
      thumbnail: null,
      progress: 0,
      duration: '12 weeks',
      lessons_completed: 0,
      total_lessons: 10,
      rating: '4.5',
      last_accessed: 'Never',
      price: 0,
      students: 0,
      certificate_available: false,
      completed_date: 'Not completed',
      payment_method: null,
      payment_verified_at: null
    }
  }

  return {
    id: course?.id || course?.class_id || Math.random().toString(36).substr(2, 9),
    title: course?.title || course?.name || 'Untitled Course',
    category: course?.category || course?.type || 'General',
    instructor: course?.instructor || course?.teacher?.name || 'Unknown Instructor',
    thumbnail: course?.thumbnail || course?.image_url || course?.image || null,
    progress: course?.progress || 0,
    duration: course?.duration || '12 weeks',
    lessons_completed: course?.lessons_completed || 0,
    total_lessons: course?.total_lessons || 10,
    rating: course?.rating || '4.5',
    last_accessed: course?.last_accessed || 'Recently',
    price: course?.price || 0,
    students: course?.students || 0,
    certificate_available: course?.certificate_available || false,
    completed_date: course?.completed_date || 'Not completed',
    payment_method: course?.payment_method || null,
    payment_verified_at: course?.payment_verified_at || null
  }
}

// Format payment method for display
const formatPaymentMethod = (method) => {
  const methods = {
    'bkash': 'bKash',
    'nagad': 'Nagad',
    'rocket': 'Rocket',
    'upay': 'Upay',
    'bank_transfer': 'Bank Transfer',
    'card': 'Credit/Debit Card'
  }
  return methods[method] || method
}

const viewCourseDetails = (course) => {
  const courseData = getCourseData(course)
  router.get(`/course/${courseData.id}`)
}

const downloadCertificate = (course) => {
  const courseData = getCourseData(course)
  alert(t('Downloading certificate for: {course}', { course: courseData.title }))
  // Implement certificate download logic
}

const reviewCourse = (course) => {
  const courseData = getCourseData(course)
  router.get(`/course/${courseData.id}/review`)
}

const removeFromWishlist = async (course) => {
  const courseData = getCourseData(course)
  
  if (!confirm(t('Are you sure you want to remove this course from your wishlist?'))) {
    return
  }
  
  removingWishlist.value = courseData.id
  try {
    const response = await fetch(`/api/my-courses/${courseData.id}/remove-wishlist`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    
    const result = await response.json()
    
    if (response.ok) {
      // Refresh the page to update the wishlist
      router.reload()
    } else {
      alert(t('Failed to remove from wishlist: {error}', { error: result.message || 'Unknown error' }))
    }
  } catch (error) {
    alert(t('Error removing from wishlist: {error}', { error: error.message }))
  } finally {
    removingWishlist.value = null
  }
}

const enrollCourse = async (course) => {
  const courseData = getCourseData(course)
  enrollingCourse.value = courseData.id
  
  try {
    const response = await fetch(`/api/my-courses/${courseData.id}/enroll`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })
    
    const result = await response.json()
    
    if (response.ok) {
      alert(t('Successfully enrolled in the course!'))
      // Refresh the page to show the course in enrolled tab
      router.reload()
    } else {
      alert(t('Failed to enroll: {error}', { error: result.message || 'Unknown error' }))
    }
  } catch (error) {
    alert(t('Error enrolling in course: {error}', { error: error.message }))
  } finally {
    enrollingCourse.value = null
  }
}

const getEmptyStateTitle = () => {
  const titles = {
    enrolled: t('No paid courses enrolled'),
    completed: t('No courses completed'),
    wishlist: t('Wishlist is empty')
  }
  return titles[activeTab.value] || t('No courses found')
}

const getEmptyStateMessage = () => {
  const messages = {
    enrolled: t('Purchase courses to start your learning journey'),
    completed: t('Complete your enrolled courses to see them here'),
    wishlist: t('Add courses to your wishlist to save them for later')
  }
  return messages[activeTab.value] || t('Explore our course catalog to find interesting courses')
}

const logout = () => {
  if (confirm(t('Are you sure you want to logout?'))) {
    router.post('/logout')
  }
}

// Log the received data for debugging
onMounted(() => {
  console.log('MyCourses component mounted with data:', props.courses)
  console.log('Paid enrolled courses:', props.courses.enrolled)
  console.log('User data:', props.courses.user)
})
</script>

<style scoped>
/* Payment Status Banner */
.payment-status-banner {
  background: #e7f6ec;
  border: 1px solid #a3e9c4;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #0d6832;
}

.payment-status-banner i {
  color: #10b981;
}

/* Payment Badge */
.payment-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: #10b981;
  color: white;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Payment Info */
.payment-info {
  background: #f8f9fa;
  border-radius: 6px;
  padding: 10px;
  margin-bottom: 15px;
  border-left: 3px solid #10b981;
}

.payment-method, .payment-date {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.8rem;
  color: #495057;
}

.payment-method i, .payment-date i {
  color: #10b981;
  width: 12px;
}

/* No Paid Courses State */
.no-paid-courses-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.no-paid-courses-state .empty-icon {
  font-size: 4rem;
  color: #6c757d;
  margin-bottom: 20px;
}

.no-paid-courses-state h3 {
  font-size: 1.5rem;
  color: var(--text-primary);
  margin-bottom: 10px;
}

.no-paid-courses-state p {
  color: var(--text-secondary);
  margin-bottom: 25px;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

.action-buttons {
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
}

.action-buttons .btn-primary,
.action-buttons .btn-outline {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
}

/* Updated Debug Section */
.debug-section {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 25px;
  border-left: 4px solid #10b981;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.debug-section h4 {
  margin: 0 0 15px 0;
  color: #10b981;
  font-size: 1.1rem;
}

/* Rest of your existing CSS remains the same */
.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.course-thumbnail {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid var(--border-color);
  border-left: 4px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
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

/* Button styles */
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
  text-decoration: none;
  text-align: center;
}

.btn-continue:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.btn-continue:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.btn-continue:disabled:hover {
  background: var(--primary-color);
  transform: none;
}

.btn-enroll:disabled,
.btn-remove-wishlist:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
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

/* Rest of your existing CSS remains the same */
.my-courses-page {
  min-height: 100vh;
  background: var(--bg-primary);
}

.courses-header {
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
  
  .action-buttons {
    flex-direction: column;
    align-items: center;
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
  
  .payment-info {
    flex-direction: column;
    gap: 8px;
  }
}
</style>