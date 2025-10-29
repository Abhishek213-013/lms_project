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
                  <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
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
                      <i class="fas fa-search"></i>
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

          <!-- Instructors Grid -->
          <div v-else class="row">
            <div class="col-12">
              <div class="instructors-header mb-4">
                <h4 class="instructors-count">
                  {{ t('Meet Our Expert Instructors') }}
                  <span class="count-badge">({{ instructors.length }} {{ t('instructors') }})</span>
                </h4>
              </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6" v-for="instructor in displayedInstructors" :key="instructor.id">
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
                      <p class="education-text">{{ getEducation(instructor) }}</p>
                    </div>
                  </div>

                  <!-- Stats -->
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

          <!-- No Instructors Found -->
          <div v-if="instructors.length === 0 && !loading" class="row">
            <div class="col-12">
              <div class="no-instructors text-center">
                <i class="fas fa-users fa-4x mb-3"></i>
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
                  <i class="fas fa-chalkboard-teacher fa-3x"></i>
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
                  <i class="fas fa-users fa-5x"></i>
                  <p>{{ t('Join Our Teaching Community') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- cta-area-end -->
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
</script>

<style scoped>
/* ==================== */
/* CSS VARIABLES */
/* ==================== */
:root {
  --line-clamp-1: 1;
  --line-clamp-2: 2;
  --line-clamp-3: 3;
}

/* ==================== */
/* MAIN LAYOUT */
/* ==================== */
.main-area {
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

/* ==================== */
/* BREADCRUMB STYLES */
/* ==================== */
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

/* ==================== */
/* INSTRUCTOR AREA */
/* ==================== */
.instructor__area {
  position: relative;
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

.section-py-120 {
  padding: 120px 0;
}

/* ==================== */
/* LOADING STYLES */
/* ==================== */
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

/* ==================== */
/* PROFESSIONAL INSTRUCTOR CARD */
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
  
  /* Enhanced line-clamp with better browser support */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
  box-orient: vertical;
}

/* ==================== */
/* UPDATED STATS SECTION */
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
  padding: 12px 8px;
  background: var(--bg-secondary);
  border-radius: 10px;
  transition: background-color 0.3s ease;
  min-width: 0;
  text-align: center;
}

.stat-icon {
  width: 32px;
  height: 32px;
  background: var(--primary-color);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 12px;
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

.stat-number {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1.2;
  transition: color 0.3s ease;
}

.stat-label {
  font-size: 0.7rem;
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

/* Specific stat item alignment */
.stat-item:nth-child(1) .stat-label::before {
  content: "Courses";
}

.stat-item:nth-child(2) .stat-label::before {
  content: "Students";
}

.stat-item:nth-child(3) .stat-label::before {
  content: "Rating";
}

.stat-label {
  font-size: 0;
}

.stat-label::before {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
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
/* HEADER AND COUNT */
/* ==================== */
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

/* ==================== */
/* SEARCH AND FILTER */
/* ==================== */
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

/* ==================== */
/* NO INSTRUCTORS */
/* ==================== */
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

/* ==================== */
/* LOAD MORE BUTTON */
/* ==================== */
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

/* ==================== */
/* CTA SECTION */
/* ==================== */
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

/* ==================== */
/* BREADCRUMB SHAPES */
/* ==================== */
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
/* DARK THEME ENHANCEMENTS */
/* ==================== */
.dark-theme .instructor-card:hover {
  border-color: var(--primary-color);
}

.dark-theme .search-input,
.dark-theme .filter-select {
  background: var(--bg-secondary);
}

.dark-theme .profile-education,
.dark-theme .stat-item {
  background: var(--bg-tertiary);
}

.dark-theme .no-instructors {
  background: var(--bg-secondary);
}

/* ==================== */
/* LANGUAGE SUPPORT */
/* ==================== */
.bn-lang .instructor-card,
.bn-lang .instructors-header,
.bn-lang .search-input,
.bn-lang .filter-select {
  font-family: 'Kalpurush', 'SolaimanLipi', 'Siyam Rupali', Arial, sans-serif;
}

.bn-lang .instructor-name {
  line-height: 1.4;
}

.bn-lang .instructor-title,
.bn-lang .education-text {
  line-height: 1.6;
}

.bn-lang .education-label,
.bn-lang .stat-label {
  letter-spacing: 0;
}

/* ==================== */
/* RESPONSIVE DESIGN */
/* ==================== */
@media (max-width: 1199px) {
  .cta__title {
    font-size: 32px;
  }
  
  .breadcrumb__content .title {
    font-size: 42px;
  }
  
  .profile-image-wrapper {
    width: 130px;
    height: 130px;
  }
  
  .stats-grid {
    gap: 6px;
  }
  
  .stat-item {
    padding: 10px 6px;
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
  
  .cta__area {
    padding: 80px 0;
  }
  
  .stats-grid {
    gap: 5px;
  }
  
  .stat-item {
    padding: 10px 5px;
  }
  
  .stat-icon {
    width: 28px;
    height: 28px;
    font-size: 11px;
  }
  
  .stat-number {
    font-size: 1rem;
  }
  
  .stat-label {
    font-size: 0.65rem;
  }
}

@media (max-width: 767px) {
  .instructors-count {
    font-size: 1.3rem;
    text-align: center;
  }
  
  .profile-content {
    padding: 0 20px 20px;
  }
  
  .profile-image-container {
    padding: 30px 25px 25px;
  }
  
  .results-count {
    justify-content: flex-start;
    margin-top: 10px;
  }
  
  .search-input-group,
  .filter-select {
    margin-bottom: 15px;
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
    padding: 12px 8px;
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
  
  .stat-number {
    font-size: 0.95rem;
  }
  
  .stat-label {
    font-size: 0.6rem;
  }
  
  .breadcrumb__content .title {
    font-size: 2rem;
  }
  
  .breadcrumb {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  
  .stats-grid {
    flex-direction: column;
    gap: 8px;
  }
  
  .stat-item {
    justify-content: center;
    padding: 10px 12px;
    flex-direction: row;
    text-align: left;
    gap: 12px;
  }
  
  .stat-item .stat-info {
    align-items: flex-start;
  }
}

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.btn:focus,
.search-input:focus,
.filter-select:focus,
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
  
  .alltuchtopdown {
    animation: none;
  }
}

/* ==================== */
/* ICON PRESERVATION */
/* ==================== */
:deep(.fas),
:deep(.fab) {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  display: inline-block !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  line-height: 1 !important;
}

:deep(i[class*="fa-"]) {
  display: inline-block !important;
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* Ensure Font Awesome icons are properly loaded */
.fa-search,
.fa-angle-right,
.fa-users,
.fa-chalkboard-teacher,
.fa-eye,
.fa-heart,
.fa-graduation-cap,
.fa-book-open,
.fa-star,
.fa-user-circle,
.fa-envelope {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}

/* ==================== */
/* ENHANCED LINE-CLAMP COMPATIBILITY */
/* ==================== */
/* Modern line-clamp implementation with full browser support */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 1;
  box-orient: vertical;
  max-height: calc(1em * 1.4 * 1); /* line-height * number of lines */
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
  box-orient: vertical;
  max-height: calc(1em * 1.4 * 2); /* line-height * number of lines */
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 3;
  box-orient: vertical;
  max-height: calc(1em * 1.4 * 3); /* line-height * number of lines */
}

/* Enhanced education text with better line-clamp */
.education-text {
  font-size: 0.85rem;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.4;
  font-weight: 500;
  transition: color 0.3s ease;
  word-wrap: break-word;
  overflow-wrap: break-word;
  
  /* Enhanced line-clamp implementation */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
  box-orient: vertical;
  max-height: calc(0.85rem * 1.4 * 2); /* font-size * line-height * lines */
}

/* Fallback for browsers that don't support line-clamp */
@supports not ((line-clamp: 2) or (-webkit-line-clamp: 2)) {
  .education-text {
    max-height: 2.8em;
    overflow: hidden;
    position: relative;
    display: block;
  }
  
  .education-text::after {
    content: '...';
    position: absolute;
    bottom: 0;
    right: 0;
    background: linear-gradient(90deg, transparent 0%, var(--bg-secondary) 20%);
    padding-left: 20px;
  }
  
  .line-clamp-1 {
    max-height: 1.4em;
    overflow: hidden;
    display: block;
  }
  
  .line-clamp-2 {
    max-height: 2.8em;
    overflow: hidden;
    display: block;
  }
  
  .line-clamp-3 {
    max-height: 4.2em;
    overflow: hidden;
    display: block;
  }
}

/* Utility classes for text truncation */
.text-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.text-truncate-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 1;
  box-orient: vertical;
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
  box-orient: vertical;
}

.text-truncate-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 3;
  box-orient: vertical;
}

/* ==================== */
/* PERFORMANCE OPTIMIZATIONS */
/* ==================== */
.instructor-card {
  will-change: transform;
  backface-visibility: hidden;
  transform: translateZ(0);
}

.profile-image {
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
}

/* ==================== */
/* PRINT STYLES */
/* ==================== */
@media print {
  .instructor-card {
    break-inside: avoid;
    box-shadow: none;
    border: 1px solid #ccc;
  }
  
  .profile-actions-footer,
  .profile-overlay {
    display: none;
  }
}
</style>