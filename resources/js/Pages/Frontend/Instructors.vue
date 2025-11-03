<template>
  <FrontendLayout>
    <main class="main-area fix">
      <!-- breadcrumb-area -->
      <section class="breadcrumb__area breadcrumb__bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="breadcrumb__content">
                <h3 class="title">{{ t('All Instructors') }}</h3>
                <nav class="breadcrumb">
                  <span property="itemListElement" typeof="ListItem">
                    <Link href="/">{{ t('Home') }}</Link>
                  </span>
                  <span class="breadcrumb-separator"><i class="fas fa-angle-right icon-fixed"></i></span>
                  <span property="itemListElement" typeof="ListItem">{{ t('Instructors') }}</span>
                </nav>
              </div>
            </div>
          </div>
        </div>
        <div class="breadcrumb__shape-wrap">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape01.svg" alt="img" class="alltuchtopdown">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape02.svg" alt="img" data-aos="fade-right" data-aos-delay="300" class="aos-init aos-animate">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape03.png" alt="img" data-aos="fade-up" data-aos-delay="400" class="aos-init aos-animate">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape04.png" alt="img" data-aos="fade-down-left" data-aos-delay="400" class="aos-init aos-animate">
          <img src="../../../../public/assets/img/banner/breadcrumb_shape05.svg" alt="img" data-aos="fade-left" data-aos-delay="400" class="aos-init aos-animate">
        </div>
      </section>
      <!-- breadcrumb-area-end -->

      <!-- instructor-area -->
      <section class="instructor__area section-py-120">
        <div class="container">
          <!-- Search and Filter Section -->
          <div class="row mb-4">
            <div class="col-lg-6">
              <div class="search-box">
                <form @submit.prevent="searchInstructors" class="search-form">
                  <div class="search-input-group">
                    <input 
                      v-model="filters.search" 
                      type="text" 
                      :placeholder="t('Search instructors by name, qualification, or institute...')" 
                      class="search-input"
                    >
                    <button type="submit" class="search-button">
                      <i class="fas fa-search icon-fixed"></i>
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="col-lg-3">
              <div class="filter-box">
                <select v-model="filters.specialization" @change="filterInstructors" class="filter-select">
                  <option value="">{{ t('All Specializations') }}</option>
                  <option v-for="specialization in specializations" :key="specialization" :value="specialization">
                    {{ specialization }}
                  </option>
                </select>
              </div>
            </div>
            <div class="col-lg-3">
              <div class="results-count">
                {{ t('Showing') }} {{ displayedInstructors.length }} {{ t('of') }} {{ instructors.length }} {{ t('instructors') }}
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="row">
            <div class="col-12 text-center">
              <div class="simple-loading">
                <div class="loading-spinner"></div>
                <p>{{ t('Loading instructors...') }}</p>
              </div>
            </div>
          </div>

          <!-- Instructors Grid - UPDATED TO MATCH HOME PAGE DESIGN -->
          <div v-else class="row">
            <div class="col-12">
              <div class="instructors-header mb-4">
                <h4 class="instructors-count">
                  {{ t('Meet Our Expert Instructors') }}
                  <span class="count-badge">({{ instructors.length }} {{ t('instructors') }})</span>
                </h4>
              </div>
            </div>

            <!-- UPDATED: Instructor Cards with Rectangular Profile Image Layout (Same as Home Page) -->
            <div class="col-xl-3 col-lg-4 col-md-6" v-for="instructor in displayedInstructors" :key="instructor.id">
              <div class="instructor-card-new">
                <!-- Profile Picture - Rectangular Shape -->
                <div class="profile-image-container">
                  <img 
                    :src="getInstructorAvatar(instructor)" 
                    :alt="instructor.name"
                    class="profile-image-rectangular"
                    @error="handleImageError"
                  >
                </div>

                <!-- Teacher Name Section -->
                <div class="teacher-name-section">
                  <h3 class="teacher-name">{{ instructor.name }}</h3>
                </div>

                <!-- Education Section -->
                <div class="education-section">
                  <div class="section-header">
                    <i class="fas fa-graduation-cap icon-fixed"></i>
                    <span class="section-title-small">{{ t('Education') }}</span>
                  </div>
                  <p class="education-text line-clamp-2">{{ getEducation(instructor) }}</p>
                </div>

                <!-- Stats Section -->
                <div class="stats-section-new">
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

                <!-- View Profile Button -->
                <div class="view-profile-section">
                  <Link :href="`/instructor/${instructor.id}`" class="btn-view-profile">
                    <i class="fas fa-user-circle icon-fixed"></i>
                    {{ t('View Profile') }}
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- No Instructors Found -->
          <div v-if="instructors.length === 0 && !loading" class="row">
            <div class="col-12">
              <div class="no-instructors text-center">
                <i class="fas fa-users fa-4x mb-3 icon-fixed"></i>
                <h4>{{ t('No Instructors Found') }}</h4>
                <p>{{ t('We couldn\'t find any instructors matching your criteria.') }}</p>
                <button class="btn btn-primary" @click="clearFilters">
                  {{ t('Clear Filters') }}
                </button>
              </div>
            </div>
          </div>

          <!-- Load More Button -->
          <div class="row" v-if="showLoadMore">
            <div class="col-12">
              <div class="instructor__load-more text-center mt-50">
                <button class="btn btn-two" @click="loadMore" :disabled="loadingMore">
                  <span class="text" v-if="!loadingMore">{{ t('Load More Instructors') }}</span>
                  <span class="text" v-else>{{ t('Loading...') }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- instructor-area-end -->

      <!-- cta-area -->
      <section class="cta__area">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <div class="cta__content">
                <div class="cta__icon">
                  <i class="fas fa-chalkboard-teacher fa-3x icon-fixed"></i>
                </div>
                <h2 class="cta__title">{{ t('Become an Instructor Today') }}</h2>
                <p>{{ t('Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.') }}</p>
                <button @click="showInstructorModal = true" class="btn btn-two">
                  <span class="text">{{ t('Join as Instructor') }}</span>
                </button>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="cta__images">
                <div class="cta__placeholder">
                  <i class="fas fa-users fa-5x icon-fixed"></i>
                  <p>{{ t('Join Our Teaching Community') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- cta-area-end -->

      <!-- Instructor Application Modal -->
      <div v-if="showInstructorModal" class="modal-overlay" @click="showInstructorModal = false">
        <div class="modal-content" @click.stop>
          <div class="modal-header">
            <h3>{{ t('Become an Instructor') }}</h3>
            <button class="modal-close" @click="showInstructorModal = false">
              <i class="fas fa-times icon-fixed"></i>
            </button>
          </div>
          
          <div class="modal-body">
            <form @submit.prevent="submitInstructorApplication" class="instructor-form">
              <!-- Personal Information -->
              <div class="form-section">
                <h4>{{ t('Personal Information') }}</h4>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="name">{{ t('Full Name') }} *</label>
                    <input 
                      type="text" 
                      id="name"
                      v-model="instructorForm.name"
                      :placeholder="t('Enter your full name')"
                      required
                    >
                  </div>
                  
                  <div class="form-group">
                    <label for="username">{{ t('Username') }} *</label>
                    <input 
                      type="text" 
                      id="username"
                      v-model="instructorForm.username"
                      :placeholder="t('Choose a username')"
                      required
                    >
                  </div>
                  
                  <div class="form-group">
                    <label for="email">{{ t('Email Address') }} *</label>
                    <input 
                      type="email" 
                      id="email"
                      v-model="instructorForm.email"
                      :placeholder="t('Enter your email')"
                      required
                    >
                  </div>
                  
                  <div class="form-group">
                    <label for="dob">{{ t('Date of Birth') }} *</label>
                    <input 
                      type="date" 
                      id="dob"
                      v-model="instructorForm.dob"
                      required
                      :max="maxDate"
                    >
                    <small class="form-help">{{ t('You must be at least 18 years old') }}</small>
                  </div>
                </div>
              </div>

              <!-- Educational Background -->
              <div class="form-section">
                <h4>{{ t('Educational Background') }}</h4>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="education_qualification">{{ t('Highest Qualification') }} *</label>
                    <select 
                      id="education_qualification"
                      v-model="instructorForm.education_qualification"
                      required
                    >
                      <option value="">{{ t('Select Qualification') }}</option>
                      <option value="HSC">{{ t('Higher Secondary Certificate') }}</option>
                      <option value="BSC">{{ t('Bachelor of Science') }}</option>
                      <option value="BA">{{ t('Bachelor of Arts') }}</option>
                      <option value="MA">{{ t('Master of Arts') }}</option>
                      <option value="MSC">{{ t('Master of Science') }}</option>
                      <option value="PhD">{{ t('Doctor of Philosophy') }}</option>
                      <option value="Other">{{ t('Other Qualification') }}</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="institute">{{ t('Institute') }} *</label>
                    <input 
                      type="text" 
                      id="institute"
                      v-model="instructorForm.institute"
                      :placeholder="t('Your university or institution')"
                      required
                    >
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="experience">{{ t('Teaching Experience') }}</label>
                  <textarea 
                    id="experience"
                    v-model="instructorForm.experience"
                    :placeholder="t('Describe your teaching experience, if any...')"
                    rows="3"
                  ></textarea>
                </div>
              </div>

              <!-- Account Security -->
              <div class="form-section">
                <h4>{{ t('Account Security') }}</h4>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="password">{{ t('Password') }} *</label>
                    <input 
                      type="password" 
                      id="password"
                      v-model="instructorForm.password"
                      :placeholder="t('Create a strong password')"
                      required
                      minlength="8"
                    >
                  </div>
                  
                  <div class="form-group">
                    <label for="password_confirmation">{{ t('Confirm Password') }} *</label>
                    <input 
                      type="password" 
                      id="password_confirmation"
                      v-model="instructorForm.password_confirmation"
                      :placeholder="t('Confirm your password')"
                      required
                    >
                  </div>
                </div>
              </div>

              <!-- Terms and Conditions -->
              <div class="form-section">
                <div class="form-group checkbox-group">
                  <input 
                    type="checkbox" 
                    id="terms"
                    v-model="instructorForm.agree_terms"
                    required
                  >
                  <label for="terms">
                    {{ t('I agree to the') }} 
                    <a href="/terms" target="_blank" class="link">{{ t('Terms and Conditions') }}</a>
                    {{ t('and') }}
                    <a href="/privacy" target="_blank" class="link">{{ t('Privacy Policy') }}</a>
                  </label>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="form-actions">
                <button 
                  type="button" 
                  class="btn btn-cancel" 
                  @click="showInstructorModal = false"
                >
                  {{ t('Cancel') }}
                </button>
                <button 
                  type="submit" 
                  class="btn btn-submit"
                  :disabled="submitting"
                >
                  <span v-if="submitting" class="loading-spinner"></span>
                  {{ submitting ? t('Submitting...') : t('Submit Application') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Success Modal -->
      <div v-if="showSuccessModal" class="modal-overlay" @click="showSuccessModal = false">
        <div class="modal-content success-modal" @click.stop>
          <div class="success-icon">
            <i class="fas fa-check-circle icon-fixed"></i>
          </div>
          <h3>{{ t('Application Submitted!') }}</h3>
          <p>{{ t('Your instructor application has been received and is under review. We will notify you via email once a decision is made.') }}</p>
          <button class="btn btn-success" @click="showSuccessModal = false">
            {{ t('Close') }}
          </button>
        </div>
      </div>
    </main>
  </FrontendLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import FrontendLayout from '../Layout/FrontendLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

// Use the global translation composable
const { t, currentLanguage, switchLanguage } = useTranslation()

// Reactive data
const loading = ref(false);
const loadingMore = ref(false);
const visibleCount = ref(8);
const itemsPerPage = 8;
const showInstructorModal = ref(false);
const showSuccessModal = ref(false);
const submitting = ref(false);

// Instructor application form
const instructorForm = ref({
  name: '',
  username: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  experience: '',
  password: '',
  password_confirmation: '',
  agree_terms: false
});

// Props
const props = defineProps({
  instructors: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({
      search: '',
      specialization: '',
    })
  },
  specializations: {
    type: Array,
    default: () => []
  }
});

// Local filters state
const localFilters = ref({ ...props.filters });

// Add icon render key to force re-render icons when language changes
const iconRenderKey = ref(0);

// Watch for language changes and force icon re-render
watch(currentLanguage, () => {
  iconRenderKey.value++;
});

// Compute max date for date of birth (18 years ago)
const maxDate = computed(() => {
  const date = new Date();
  date.setFullYear(date.getFullYear() - 18);
  return date.toISOString().split('T')[0];
});

// Computed properties
const displayedInstructors = computed(() => {
  let filtered = props.instructors || [];

  if (localFilters.value.search) {
    const query = localFilters.value.search.toLowerCase();
    filtered = filtered.filter(instructor => {
      const name = instructor.name?.toLowerCase() || '';
      const qualification = instructor.education_qualification?.toLowerCase() || '';
      const institute = instructor.institute?.toLowerCase() || '';
      
      return name.includes(query) || qualification.includes(query) || institute.includes(query);
    });
  }

  if (localFilters.value.specialization) {
    filtered = filtered.filter(instructor => 
      instructor.education_qualification === localFilters.value.specialization
    );
  }

  return filtered.slice(0, visibleCount.value);
});

const showLoadMore = computed(() => {
  return visibleCount.value < (props.instructors || []).length;
});

// Methods
const searchInstructors = () => {
  router.get('/instructors', localFilters.value, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false
  });
};

const filterInstructors = () => {
  router.get('/instructors', localFilters.value, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false
  });
};

const clearFilters = () => {
  localFilters.value = {
    search: '',
    specialization: '',
  };
  router.get('/instructors', localFilters.value, {
    preserveState: true,
    replace: true,
    preserveScroll: true
  });
};

const loadMore = () => {
  loadingMore.value = true;
  setTimeout(() => {
    visibleCount.value += itemsPerPage;
    loadingMore.value = false;
  }, 500);
};

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
};

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y3ZmFmYyIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5Y2EwYTYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjM1ZW0iPuKKojwvdGV4dD48L3N2Zz4=';
};

const getExpertise = (instructor) => {
  const qual = instructor.education_qualification || '';
  if (qual.includes('Science')) return t('Science Expert');
  if (qual.includes('English')) return t('English Specialist');
  if (qual.includes('Mathematics')) return t('Mathematics Teacher');
  if (qual.includes('Bangla')) return t('Bangla Instructor');
  if (qual.includes('Sports')) return t('Sports Coach');
  return t('Teaching Specialist');
};

const getEducation = (instructor) => {
  if (instructor.education_qualification && instructor.education_qualification !== 'Not specified') {
    return instructor.education_qualification;
  }
  return t('Teaching Degree');
};

// Method to submit instructor application
const submitInstructorApplication = async () => {
  submitting.value = true;
  
  try {
    const response = await fetch('/api/public/instructor-requests', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(instructorForm.value)
    });

    const data = await response.json();

    if (data.success) {
      // Reset form and show success message
      resetInstructorForm();
      showInstructorModal.value = false;
      showSuccessModal.value = true;
    } else {
      // Handle validation errors
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat().join('\n');
        alert('Please fix the following errors:\n' + errorMessages);
      } else {
        alert(data.message || 'Failed to submit application. Please try again.');
      }
    }
  } catch (error) {
    console.error('Error submitting application:', error);
    alert('Network error. Please check your connection and try again.');
  } finally {
    submitting.value = false;
  }
};

// Method to reset the form
const resetInstructorForm = () => {
  instructorForm.value = {
    name: '',
    username: '',
    email: '',
    dob: '',
    education_qualification: '',
    institute: '',
    experience: '',
    password: '',
    password_confirmation: '',
    agree_terms: false
  };
};
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
/* NEW INSTRUCTOR CARD DESIGN (SAME AS HOME PAGE) */
/* ==================== */
.instructor-card-new {
  background: var(--card-bg);
  border-radius: 12px;
  margin-bottom: 30px;
  box-shadow: var(--shadow);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  overflow: hidden;
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 0;
}

.instructor-card-new:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

/* Profile Picture - Rectangular Shape */
.profile-image-container {
  width: 100%;
  height: 220px;
  overflow: hidden;
  background: var(--bg-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  margin: 0;
}

.profile-image-rectangular {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.instructor-card-new:hover .profile-image-rectangular {
  transform: scale(1.05);
}

/* Teacher Name Section */
.teacher-name-section {
  padding: 20px;
  border-bottom: 2px solid var(--border-color);
  background: var(--card-bg);
}

.teacher-name {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  text-align: center;
  line-height: 1.3;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Education Section */
.education-section {
  padding: 20px;
  border-bottom: 1px solid var(--border-light);
  background: var(--card-bg);
}

.section-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
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

.education-text {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin: 0;
  line-height: 1.5;
  font-weight: 500;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Stats Section */
.stats-section-new {
  padding: 20px;
  border-bottom: 1px solid var(--border-light);
  background: var(--card-bg);
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
  padding: 10px 5px;
  background: var(--bg-secondary);
  border-radius: 8px;
  transition: background-color 0.3s ease;
  min-width: 0;
  text-align: center;
}

.stat-item-new:hover {
  background: var(--bg-tertiary);
}

.stat-icon-new {
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
  transition: all 0.3s ease;
}

.stat-info-new {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
}

.stat-number-new {
  font-size: 0.9rem !important;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1.2;
}

.stat-label-new {
  font-size: 0.65rem !important;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  line-height: 1.2;
  word-wrap: break-word;
  overflow-wrap: break-word;
  display: block;
}

/* View Profile Section */
.view-profile-section {
  padding: 20px;
  background: var(--card-bg);
  margin-top: auto;
}

.btn-view-profile {
  width: 100%;
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 14px 20px;
  border-radius: 8px;
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

.btn-view-profile:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px color-mix(in srgb, var(--primary-color) 30%, transparent);
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
/* RESPONSIVE DESIGN FOR INSTRUCTOR CARDS */
/* ==================== */
@media (max-width: 1199px) {
  .profile-image-container {
    height: 200px;
  }
  
  .stats-grid-new {
    gap: 8px;
  }
  
  .stat-item-new {
    padding: 8px 4px;
  }
}

@media (max-width: 991px) {
  .profile-image-container {
    height: 180px;
  }
  
  .teacher-name {
    font-size: 1.2rem;
  }
  
  .education-section,
  .stats-section-new,
  .view-profile-section {
    padding: 15px;
  }
  
  .teacher-name-section {
    padding: 15px;
  }
  
  .stats-grid-new {
    gap: 5px;
  }
}

@media (max-width: 767px) {
  .profile-image-container {
    height: 160px;
  }
  
  .teacher-name {
    font-size: 1.1rem;
  }
  
  .education-section,
  .stats-section-new,
  .view-profile-section {
    padding: 12px;
  }
  
  .teacher-name-section {
    padding: 12px;
  }
  
  .stats-grid-new {
    gap: 6px;
  }
  
  .stat-item-new {
    padding: 6px 3px;
  }
  
  .stat-number-new {
    font-size: 0.85rem !important;
  }
  
  .stat-label-new {
    font-size: 0.6rem !important;
  }
}

@media (max-width: 575px) {
  .instructor-card-new {
    margin-bottom: 20px;
  }
  
  .profile-image-container {
    height: 140px;
  }
  
  .teacher-name {
    font-size: 1rem;
  }
  
  .stats-grid-new {
    flex-direction: column;
    gap: 8px;
  }
  
  .stat-item-new {
    flex-direction: row;
    justify-content: flex-start;
    text-align: left;
    padding: 10px 12px;
  }
  
  .stat-info-new {
    align-items: flex-start;
  }
}

/* ==================== */
/* EXISTING STYLES (KEEPING YOUR ORIGINAL STYLES FOR OTHER SECTIONS) */
/* ==================== */

/* Main Layout */
.main-area {
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

/* Breadcrumb Styles */
.breadcrumb__area {
  position: relative;
  padding: 4px 0 4px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
  color: var(--text-primary);
  background-color: var(--bg-secondary);
  transition: all 0.3s ease;
}

.breadcrumb__content {
  text-align: center;
  position: relative;
  z-index: 3;
  color: var(--text-primary);
}

.breadcrumb__content .title {
  font-size: 48px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 15px;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
  transition: color 0.3s ease;
}

.breadcrumb {
  display: flex;
  justify-content: center;
  align-items: center;
  list-style: none;
  padding: 0;
  margin: 0;
  color: var(--text-primary);
  font-size: 16px;
  font-weight: 500;
  transition: color 0.3s ease;
}

.breadcrumb a {
  color: var(--text-primary);
  text-decoration: none;
  opacity: 0.8;
  transition: opacity 0.3s ease, color 0.3s ease;
}

.breadcrumb a:hover {
  opacity: 1;
  color: var(--primary-color);
}

.breadcrumb-separator {
  color: var(--text-muted);
  opacity: 0.8;
  margin: 0 10px;
  font-size: 14px;
  transition: color 0.3s ease;
}

.breadcrumb span:not(.breadcrumb-separator) {
  color: var(--text-primary);
  opacity: 1;
  font-weight: 600;
  transition: color 0.3s ease;
}

/* Instructor Area */
.instructor__area {
  position: relative;
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

.section-py-120 {
  padding: 120px 0;
}

/* Loading Styles */
.simple-loading {
  padding: 80px 0;
  text-align: center;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--border-color);
  border-top: 3px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
  transition: border-color 0.3s ease;
}

.simple-loading p {
  color: var(--text-secondary);
  transition: color 0.3s ease;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Header and Count */
.instructors-header {
  background: var(--card-bg);
  padding: 25px;
  border-radius: 15px;
  box-shadow: var(--shadow);
  margin-bottom: 30px;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.instructors-count {
  margin: 0;
  color: var(--text-primary);
  font-size: 1.5rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 10px;
  transition: color 0.3s ease;
}

.count-badge {
  background: var(--primary-color);
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.9rem;
}

/* Search and Filter */
.search-box {
  margin-bottom: 20px;
}

.search-form {
  width: 100%;
}

.search-input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.search-input {
  width: 100%;
  padding: 12px 20px;
  border: 2px solid var(--border-color);
  border-radius: 10px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  background: var(--card-bg);
  color: var(--text-primary);
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent);
}

.search-input::placeholder {
  color: var(--text-muted);
}

.search-button {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  padding: 8px;
  transition: color 0.3s ease;
}

.search-button:hover {
  color: var(--primary-color);
}

.filter-box {
  margin-bottom: 20px;
}

.filter-select {
  width: 100%;
  padding: 12px 20px;
  border: 2px solid var(--border-color);
  border-radius: 10px;
  font-size: 0.9rem;
  background: var(--card-bg);
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent);
}

.results-count {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  color: var(--text-muted);
  font-size: 0.9rem;
  font-weight: 500;
  transition: color 0.3s ease;
}

/* No Instructors */
.no-instructors {
  padding: 80px 20px;
  color: var(--text-muted);
  text-align: center;
  background: var(--card-bg);
  border-radius: 15px;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.no-instructors i {
  color: var(--text-muted);
  margin-bottom: 20px;
  opacity: 0.7;
  transition: color 0.3s ease;
}

.no-instructors h4 {
  color: var(--text-primary);
  margin-bottom: 10px;
  transition: color 0.3s ease;
}

.no-instructors p {
  color: var(--text-secondary);
  margin-bottom: 20px;
  transition: color 0.3s ease;
}

/* Load More Button */
.instructor__load-more {
  margin-top: 50px;
}

.btn-two {
  background: var(--primary-color);
  color: #ffffff;
  padding: 15px 35px;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  transition: all 0.3s ease;
  cursor: pointer;
  font-size: 16px;
}

.btn-two:hover:not(:disabled) {
  background: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 10px 25px color-mix(in srgb, var(--primary-color) 30%, transparent);
}

/* CTA Section */
.cta__area {
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
  padding: 100px 0;
  transition: background 0.3s ease;
}

.cta__icon {
  margin-bottom: 20px;
  color: var(--primary-color);
  transition: color 0.3s ease;
}

.cta__title {
  font-size: 36px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 15px;
  transition: color 0.3s ease;
}

.cta__content p {
  font-size: 18px;
  color: var(--text-secondary);
  margin-bottom: 30px;
  transition: color 0.3s ease;
}

.cta__placeholder {
  text-align: center;
  color: var(--text-muted);
  transition: color 0.3s ease;
}

.cta__placeholder i {
  margin-bottom: 20px;
}

.cta__placeholder p {
  color: var(--text-secondary);
  transition: color 0.3s ease;
}

/* Breadcrumb Shapes */
.breadcrumb__shape-wrap {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  overflow: hidden;
  z-index: 1;
}

.breadcrumb__shape-wrap img {
  position: absolute;
  max-width: none;
  opacity: 0.3;
}

.breadcrumb__shape-wrap img:nth-child(1) {
  top: 20%;
  left: 8%;
  width: 120px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(2) {
  top: 35%;
  right: 20%;
  width: 80px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(3) {
  bottom: 1%;
  left: 32%;
  width: 100px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(4) {
  bottom: 2%;
  right: 40%;
  width: 90px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(5) {
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 150px;
  z-index: 1;
}

/* Animation for specific elements */
.alltuchtopdown {
  animation: alltuchtopdown 5s infinite linear;
}

@keyframes alltuchtopdown {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-30px);
  }
  100% {
    transform: translateY(0px);
  }
}

/* ==================== */
/* MODAL STYLES (KEEPING YOUR EXISTING MODAL STYLES) */
/* ==================== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  padding: 20px;
  backdrop-filter: blur(5px);
}

.modal-content {
  background: var(--card-bg);
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-width: 700px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  border: 1px solid var(--border-color);
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px 30px;
  border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
  margin: 0;
  color: var(--text-primary);
  font-size: 1.5rem;
  font-weight: 600;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-muted);
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.modal-close:hover {
  color: var(--text-primary);
  background: var(--bg-secondary);
}

.modal-body {
  padding: 30px;
}

/* Form Styles */
.instructor-form {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-section h4 {
  margin: 0;
  color: var(--text-primary);
  font-size: 1.2rem;
  font-weight: 600;
  padding-bottom: 10px;
  border-bottom: 2px solid var(--primary-color);
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  color: var(--text-primary);
  font-size: 0.9rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px 15px;
  border: 2px solid var(--border-color);
  border-radius: 10px;
  background: var(--bg-primary);
  color: var(--text-primary);
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent);
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.form-help {
  color: var(--text-muted);
  font-size: 0.8rem;
  margin-top: 4px;
}

/* Checkbox Group */
.checkbox-group {
  flex-direction: row;
  align-items: flex-start;
  gap: 12px;
}

.checkbox-group input[type="checkbox"] {
  margin-top: 2px;
  transform: scale(1.1);
}

.checkbox-group label {
  font-weight: normal;
  line-height: 1.4;
}

.link {
  color: var(--primary-color);
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  padding-top: 20px;
  border-top: 1px solid var(--border-color);
}

.btn {
  padding: 12px 25px;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.btn-cancel {
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.btn-cancel:hover {
  background: var(--border-color);
}

.btn-submit {
  background: var(--primary-color);
  color: white;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-submit:hover:not(:disabled) {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Success Modal */
.success-modal {
  text-align: center;
  max-width: 500px;
}

.success-icon {
  font-size: 4rem;
  color: #10b981;
  margin-bottom: 20px;
}

.success-modal h3 {
  margin: 0 0 15px 0;
  color: var(--text-primary);
  font-size: 1.5rem;
}

.success-modal p {
  color: var(--text-secondary);
  margin-bottom: 25px;
  line-height: 1.5;
}

.btn-success {
  background: #10b981;
  color: white;
  padding: 12px 30px;
}

.btn-success:hover {
  background: #059669;
}

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.btn:focus,
.search-input:focus,
.filter-select:focus,
.btn-view-profile:focus {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

.form-group input:focus-visible,
.form-group select:focus-visible,
.form-group textarea:focus-visible {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

.btn:focus-visible {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .instructor-card-new,
  .btn,
  .btn-view-profile,
  .profile-image-rectangular {
    transition: none;
  }
  
  .instructor-card-new:hover,
  .btn:hover:not(:disabled),
  .btn-view-profile:hover {
    transform: none;
  }
  
  .alltuchtopdown {
    animation: none;
  }
  
  .modal-content {
    animation: none;
  }
}

/* ==================== */
/* RESPONSIVE DESIGN */
/* ==================== */
@media (max-width: 767px) {
  .instructors-count {
    font-size: 1.3rem;
    text-align: center;
  }
  
  .results-count {
    justify-content: flex-start;
    margin-top: 10px;
  }
  
  .search-input-group,
  .filter-select {
    margin-bottom: 15px;
  }
  
  /* Modal responsive */
  .modal-content {
    margin: 10px;
    max-height: 95vh;
  }
  
  .modal-header {
    padding: 20px;
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
  }
}

@media (max-width: 575px) {
  .breadcrumb__content .title {
    font-size: 2rem;
  }
  
  .breadcrumb {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  
  /* Modal responsive */
  .modal-overlay {
    padding: 10px;
  }
  
  .modal-header h3 {
    font-size: 1.3rem;
  }
  
  .form-section h4 {
    font-size: 1.1rem;
  }
}
</style>