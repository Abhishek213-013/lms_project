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
                  <div class="section-label">{{ t('Expertise') }}</div>
                  <p class="expertise-text">{{ getExpertise(instructor) }}</p>
                </div>

                <!-- Education -->
                <div class="tile-section education-section">
                  <div class="section-label">{{ t('Education') }}</div>
                  <p class="education-text">{{ getEducation(instructor) }}</p>
                </div>

                <!-- Stats -->
                <div class="tile-section stats-section">
                  <div class="stats-grid">
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.courses_count }}</div>
                      <div class="stat-label">{{ t('Courses') }}</div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.students_count }}</div>
                      <div class="stat-label">{{ t('Students') }}</div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.rating }}</div>
                      <div class="stat-label">{{ t('Rating') }}</div>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="tile-actions">
                  <Link :href="`/instructor/${instructor.id}`" class="btn-view-profile">
                    {{ t('View Full Profile') }}
                  </Link>
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

<script>
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, getCurrentInstance } from 'vue';
import { router } from '@inertiajs/vue3';
import FrontendLayout from '../Layout/FrontendLayout.vue';

export default {
  name: 'InstructorsPage',
  components: {
    FrontendLayout,
    Link
  },
  props: {
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
  },
  setup(props) {
    // Get the Vue instance for accessing global properties
    const { proxy } = getCurrentInstance()

    const loading = ref(false);
    const loadingMore = ref(false);
    const visibleCount = ref(8);
    const itemsPerPage = 8;
    const showInstructorModal = ref(false);
    const currentLanguage = ref('bn'); // Default to Bengali
    const currentTheme = ref('light');

    const localFilters = ref({ ...props.filters });

    // Load language and theme preferences from localStorage
    onMounted(() => {
      const savedLang = localStorage.getItem('preferredLanguage')
      if (savedLang && (savedLang === 'en' || savedLang === 'bn')) {
        currentLanguage.value = savedLang
      }
      
      const savedTheme = localStorage.getItem('preferredTheme')
      if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
        currentTheme.value = savedTheme
      }
      
      // Listen for language changes from other components
      window.addEventListener('languageChanged', (event) => {
        currentLanguage.value = event.detail.language
      })

      // Listen for theme changes
      window.addEventListener('themeChanged', (event) => {
        currentTheme.value = event.detail.theme
      })
    })

    onUnmounted(() => {
      window.removeEventListener('languageChanged', () => {})
      window.removeEventListener('themeChanged', () => {})
    })

    // Define translations object
    const translations = {
      en: {
        'All Instructors': 'All Instructors',
        'Home': 'Home',
        'Instructors': 'Instructors',
        'Search instructors by name, qualification, or institute...': 'Search instructors by name, qualification, or institute...',
        'All Specializations': 'All Specializations',
        'Showing': 'Showing',
        'of': 'of',
        'instructors': 'instructors',
        'Loading instructors...': 'Loading instructors...',
        'Meet Our Expert Instructors': 'Meet Our Expert Instructors',
        'Expertise': 'Expertise',
        'Education': 'Education',
        'Courses': 'Courses',
        'Students': 'Students',
        'Rating': 'Rating',
        'View Full Profile': 'View Full Profile',
        'No Instructors Found': 'No Instructors Found',
        'We couldn\'t find any instructors matching your criteria.': 'We couldn\'t find any instructors matching your criteria.',
        'Clear Filters': 'Clear Filters',
        'Load More Instructors': 'Load More Instructors',
        'Loading...': 'Loading...',
        'Become an Instructor Today': 'Become an Instructor Today',
        'Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.': 'Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.',
        'Join as Instructor': 'Join as Instructor',
        'Join Our Teaching Community': 'Join Our Teaching Community',
        'Science': 'Science',
        'English': 'English',
        'Mathematics': 'Mathematics',
        'Bangla': 'Bangla',
        'Sports': 'Sports',
        'Teaching Specialist': 'Teaching Specialist',
        'Teaching Degree': 'Teaching Degree'
      },
      bn: {
        'All Instructors': 'সমস্ত ইন্সট্রাক্টর',
        'Home': 'হোম',
        'Instructors': 'ইন্সট্রাক্টর',
        'Search instructors by name, qualification, or institute...': 'নাম, যোগ্যতা বা প্রতিষ্ঠান দ্বারা ইন্সট্রাক্টর খুঁজুন...',
        'All Specializations': 'সমস্ত বিশেষীকরণ',
        'Showing': 'দেখানো হচ্ছে',
        'of': 'এর',
        'instructors': 'ইন্সট্রাক্টর',
        'Loading instructors...': 'ইন্সট্রাক্টর লোড হচ্ছে...',
        'Meet Our Expert Instructors': 'আমাদের বিশেষজ্ঞ ইন্সট্রাক্টরদের সাথে পরিচিত হোন',
        'Expertise': 'দক্ষতা',
        'Education': 'শিক্ষা',
        'Courses': 'কোর্স',
        'Students': 'শিক্ষার্থী',
        'Rating': 'রেটিং',
        'View Full Profile': 'সম্পূর্ণ প্রোফাইল দেখুন',
        'No Instructors Found': 'কোন ইন্সট্রাক্টর পাওয়া যায়নি',
        'We couldn\'t find any instructors matching your criteria.': 'আপনার শর্তের সাথে মিলে এমন কোন ইন্সট্রাক্টর আমরা খুঁজে পাইনি।',
        'Clear Filters': 'ফিল্টার সরান',
        'Load More Instructors': 'আরও ইন্সট্রাক্টর লোড করুন',
        'Loading...': 'লোড হচ্ছে...',
        'Become an Instructor Today': 'আজই একজন ইন্সট্রাক্টর হোন',
        'Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.': 'আমাদের বিশেষজ্ঞ শিক্ষকদের দলে যোগ দিন এবং বিশ্বব্যাপী হাজার হাজার আগ্রহী শিক্ষার্থীর সাথে আপনার জ্ঞান শেয়ার করুন।',
        'Join as Instructor': 'ইন্সট্রাক্টর হিসেবে যোগ দিন',
        'Join Our Teaching Community': 'আমাদের শিক্ষণ সম্প্রদায়ে যোগ দিন',
        'Science': 'বিজ্ঞান',
        'English': 'ইংরেজি',
        'Mathematics': 'গণিত',
        'Bangla': 'বাংলা',
        'Sports': 'ক্রীড়া',
        'Teaching Specialist': 'শিক্ষণ বিশেষজ্ঞ',
        'Teaching Degree': 'শিক্ষণ ডিগ্রী'
      }
    }

    // Translation function - use local translations only
    const t = (key, replacements = {}) => {
      let translated = translations[currentLanguage.value]?.[key] || key
      
      Object.keys(replacements).forEach(replacementKey => {
        translated = translated.replace(`{${replacementKey}}`, replacements[replacementKey])
      })
      
      return translated
    }

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
      if (qual.includes('Science')) return t('Science');
      if (qual.includes('English')) return t('English');
      if (qual.includes('Mathematics')) return t('Mathematics');
      if (qual.includes('Bangla')) return t('Bangla');
      if (qual.includes('Sports')) return t('Sports');
      return t('Teaching Specialist');
    };

    const getEducation = (instructor) => {
      if (instructor.education_qualification && instructor.education_qualification !== 'Not specified') {
        return instructor.education_qualification;
      }
      return t('Teaching Degree');
    };

    return {
      loading,
      loadingMore,
      localFilters,
      visibleCount,
      displayedInstructors,
      showLoadMore,
      showInstructorModal,
      t,
      currentLanguage,
      currentTheme,
      searchInstructors,
      filterInstructors,
      clearFilters,
      loadMore,
      getInstructorAvatar,
      handleImageError,
      getExpertise,
      getEducation,
    };
  }
}
</script>

<style scoped>
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
/* INSTRUCTOR TILES */
/* ==================== */
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
}

.instructor-tile:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-color);
}

/* Profile Image */
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
}

.instructor-tile:hover .tile-image img {
  border-color: var(--primary-color);
  transform: scale(1.05);
}

/* Tile Sections */
.tile-section {
  padding: 15px 25px;
  border-top: 1px solid var(--border-light);
  transition: border-color 0.3s ease;
}

/* Name Section */
.name-section {
  border-top: none;
  padding-bottom: 10px;
}

.instructor-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.3;
  transition: color 0.3s ease;
}

/* Section Labels */
.section-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 5px;
  transition: color 0.3s ease;
}

/* Expertise Section */
.expertise-section {
  padding-top: 10px;
  padding-bottom: 10px;
}

.expertise-text {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary-color);
  margin: 0;
  line-height: 1.4;
  transition: color 0.3s ease;
}

/* Education Section */
.education-section {
  padding-top: 10px;
  padding-bottom: 10px;
}

.education-text {
  font-size: 0.85rem;
  color: var(--text-secondary);
  margin: 0;
  line-height: 1.4;
  font-weight: 500;
  transition: color 0.3s ease;
}

/* Stats Section */
.stats-section {
  padding: 20px 25px;
  background: var(--bg-secondary);
  transition: background-color 0.3s ease;
}

.stats-grid {
  display: flex;
  justify-content: space-between;
  align-items: center;
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
  transition: color 0.3s ease;
}

.stat-label {
  font-size: 0.75rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  transition: color 0.3s ease;
}

/* Action Buttons */
.tile-actions {
  padding: 20px 25px;
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
}

.btn-view-profile:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px color-mix(in srgb, var(--primary-color) 30%, transparent);
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
.dark-theme .instructor-tile:hover {
  border-color: var(--primary-color);
}

.dark-theme .search-input,
.dark-theme .filter-select {
  background: var(--bg-secondary);
}

.dark-theme .stats-section {
  background: var(--bg-tertiary);
}

.dark-theme .no-instructors {
  background: var(--bg-secondary);
}

/* ==================== */
/* LANGUAGE SUPPORT */
/* ==================== */
.bn-lang .instructor-tile,
.bn-lang .instructors-header,
.bn-lang .search-input,
.bn-lang .filter-select {
  font-family: 'Kalpurush', 'SolaimanLipi', 'Siyam Rupali', Arial, sans-serif;
}

.bn-lang .instructor-name {
  line-height: 1.4;
}

.bn-lang .expertise-text,
.bn-lang .education-text {
  line-height: 1.6;
}

.bn-lang .section-label {
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
}

@media (max-width: 991px) {
  .section-py-120 {
    padding: 80px 0;
  }
  
  .tile-image img {
    width: 100px;
    height: 100px;
  }
  
  .cta__area {
    padding: 80px 0;
  }
  
  .stats-grid {
    gap: 10px;
  }
}

@media (max-width: 767px) {
  .instructors-count {
    font-size: 1.3rem;
    text-align: center;
  }
  
  .tile-section {
    padding: 12px 20px;
  }
  
  .results-count {
    justify-content: flex-start;
    margin-top: 10px;
  }
  
  .search-input-group,
  .filter-select {
    margin-bottom: 15px;
  }
}

@media (max-width: 575px) {
  .instructor-tile {
    margin-bottom: 20px;
  }
  
  .instructor-name {
    font-size: 1.1rem;
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
  
  .breadcrumb__content .title {
    font-size: 2rem;
  }
  
  .breadcrumb {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
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

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .instructor-tile,
  .btn,
  .btn-view-profile,
  .tile-image img {
    transition: none;
  }
  
  .instructor-tile:hover,
  .btn:hover:not(:disabled),
  .btn-view-profile:hover {
    transform: none;
  }
  
  .alltuchtopdown {
    animation: none;
  }
}
</style>