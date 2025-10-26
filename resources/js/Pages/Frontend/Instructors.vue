<template>
  <FrontendLayout>
    <main class="main-area fix">
      <!-- breadcrumb-area -->
      <section class="breadcrumb__area breadcrumb__bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="breadcrumb__content">
                <h3 class="title">All Instructors</h3>
                <nav class="breadcrumb">
                  <span property="itemListElement" typeof="ListItem">
                    <Link href="/">Home</Link>
                  </span>
                  <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                  <span property="itemListElement" typeof="ListItem">Instructors</span>
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
                      placeholder="Search instructors by name, qualification, or institute..." 
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
                  <option value="">All Specializations</option>
                  <option v-for="specialization in specializations" :key="specialization" :value="specialization">
                    {{ specialization }}
                  </option>
                </select>
              </div>
            </div>
            <div class="col-lg-3">
              <div class="results-count">
                Showing {{ displayedInstructors.length }} of {{ instructors.length }} instructors
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="row">
            <div class="col-12 text-center">
              <div class="simple-loading">
                <div class="loading-spinner"></div>
                <p>Loading instructors...</p>
              </div>
            </div>
          </div>

          <!-- Instructors Grid - Updated Tile Layout -->
          <div v-else class="row">
            <div class="col-12">
              <div class="instructors-header mb-4">
                <h4 class="instructors-count">
                  Meet Our Expert Instructors 
                  <span class="count-badge">({{ instructors.length }} instructors)</span>
                </h4>
              </div>
            </div>

            <!-- Updated Tile Layout -->
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
                  <div class="section-label">Expertise</div>
                  <p class="expertise-text">{{ getExpertise(instructor) }}</p>
                </div>

                <!-- Education -->
                <div class="tile-section education-section">
                  <div class="section-label">Education</div>
                  <p class="education-text">{{ getEducation(instructor) }}</p>
                </div>

                <!-- Stats -->
                <div class="tile-section stats-section">
                  <div class="stats-grid">
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.courses_count || 0 }}</div>
                      <div class="stat-label">Courses</div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.students_count || 0 }}</div>
                      <div class="stat-label">Students</div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-number">{{ instructor.rating || '4.8' }}</div>
                      <div class="stat-label">Rating</div>
                    </div>
                  </div>
                </div>

                <!-- Social Links -->
                <div class="tile-section social-section">
                  <div class="social-links">
                    <a href="#" class="social-link">
                      <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link">
                      <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link">
                      <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link">
                      <i class="fab fa-instagram"></i>
                    </a>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="tile-actions">
                  <Link :href="`/instructor/${instructor.id}`" class="btn-view-profile">
                    View Full Profile
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
                <h4>No Instructors Found</h4>
                <p>We couldn't find any instructors matching your criteria.</p>
                <button class="btn btn-primary" @click="clearFilters">
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          <!-- Load More Button -->
          <div class="row" v-if="showLoadMore">
            <div class="col-12">
              <div class="instructor__load-more text-center mt-50">
                <button class="btn btn-two" @click="loadMore" :disabled="loadingMore">
                  <span class="text" v-if="!loadingMore">Load More Instructors</span>
                  <span class="text" v-else>Loading...</span>
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
                <h2 class="cta__title">Become an Instructor Today</h2>
                <p>Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.</p>
                <Link href="/become-instructor" class="btn btn-two">
                  <span class="text">Join as Instructor</span>
                </Link>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="cta__images">
                <div class="cta__placeholder">
                  <i class="fas fa-users fa-5x"></i>
                  <p>Join Our Teaching Community</p>
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
import { ref, computed, onMounted } from 'vue';
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
    const loading = ref(false);
    const loadingMore = ref(false);
    const visibleCount = ref(8);
    const itemsPerPage = 8;

    const localFilters = ref({ ...props.filters });

    // Computed properties
    const displayedInstructors = computed(() => {
      let filtered = props.instructors || [];

      // Apply search filter
      if (localFilters.value.search) {
        const query = localFilters.value.search.toLowerCase();
        filtered = filtered.filter(instructor => {
          const name = instructor.name?.toLowerCase() || '';
          const qualification = instructor.education_qualification?.toLowerCase() || '';
          const institute = instructor.institute?.toLowerCase() || '';
          
          return name.includes(query) || qualification.includes(query) || institute.includes(query);
        });
      }

      // Apply specialization filter
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
      // Extract expertise from qualification or use default
      const qual = instructor.education_qualification || '';
      if (qual.includes('Science')) return 'Science';
      if (qual.includes('English')) return 'English';
      if (qual.includes('Mathematics')) return 'Mathematics';
      if (qual.includes('Bangla')) return 'Bangla';
      if (qual.includes('Sports ')) return 'Sports ';
      return 'Teaching Specialist';
    };

    const getEducation = (instructor) => {
      if (instructor.education_qualification && instructor.education_qualification !== 'Not specified') {
        return instructor.education_qualification;
      }
      return 'Teaching Degree';
    };

    return {
      loading,
      loadingMore,
      localFilters,
      visibleCount,
      displayedInstructors,
      showLoadMore,
      searchInstructors,
      filterInstructors,
      clearFilters,
      loadMore,
      getInstructorAvatar,
      handleImageError,
      getExpertise,
      getEducation
    };
  }
}
</script>

<style scoped>
/* Instructor Area */
.instructor__area {
  position: relative;
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
  border: 3px solid #f3f3f3;
  border-top: 3px solid var(--primary-color, #4a6cf7);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Updated Tile Layout Styles */
.instructor-tile {
  background: #ffffff;
  border-radius: 15px;
  padding: 0;
  margin-bottom: 30px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid #f0f0f0;
  text-align: center;
  overflow: hidden;
  height: 100%;
}

.instructor-tile:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

/* Profile Image */
.tile-image {
  padding: 30px 30px 20px;
}

.tile-image img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid #f8f9fa;
  transition: all 0.3s ease;
}

.instructor-tile:hover .tile-image img {
  border-color: var(--primary-color, #4a6cf7);
  transform: scale(1.05);
}

/* Tile Sections */
.tile-section {
  padding: 15px 25px;
  border-top: 1px solid #f0f0f0;
}

/* Name Section */
.name-section {
  border-top: none;
  padding-bottom: 10px;
}

.instructor-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
  line-height: 1.3;
}

/* Section Labels */
.section-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: #7f8c8d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 5px;
}

/* Expertise Section */
.expertise-section {
  padding-top: 10px;
  padding-bottom: 10px;
}

.expertise-text {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary-color, #4a6cf7);
  margin: 0;
  line-height: 1.4;
}

/* Education Section */
.education-section {
  padding-top: 10px;
  padding-bottom: 10px;
}

.education-text {
  font-size: 0.85rem;
  color: #5d6d7e;
  margin: 0;
  line-height: 1.4;
  font-weight: 500;
}

/* Stats Section */
.stats-section {
  padding: 20px 25px;
  background: #f8f9fa;
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
  color: #2c3e50;
  margin-bottom: 2px;
}

.stat-label {
  font-size: 0.75rem;
  color: #7f8c8d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

/* Social Section */
.social-section {
  padding: 15px 25px;
  background: #f8f9fa;
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.social-link {
  width: 36px;
  height: 36px;
  background: #ffffff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #7f8c8d;
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
  font-size: 14px;
}

.social-link:hover {
  background: var(--primary-color, #4a6cf7);
  color: #ffffff;
  transform: translateY(-2px);
  border-color: var(--primary-color, #4a6cf7);
}

/* Action Buttons */
.tile-actions {
  padding: 20px 25px;
}

.btn-view-profile {
  display: block;
  width: 100%;
  background: var(--primary-color, #4a6cf7);
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
  background: #3a5bd9;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
}

/* Header and Count */
.instructors-header {
  background: white;
  padding: 25px;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  margin-bottom: 30px;
}

.instructors-count {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 10px;
}

.count-badge {
  background: var(--primary-color, #4a6cf7);
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
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color, #4a6cf7);
  box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
}

.search-button {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  color: #7f8c8d;
  cursor: pointer;
  padding: 8px;
}

.filter-box {
  margin-bottom: 20px;
}

.filter-select {
  width: 100%;
  padding: 12px 20px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 0.9rem;
  background: white;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary-color, #4a6cf7);
  box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
}

.results-count {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  color: #7f8c8d;
  font-size: 0.9rem;
  font-weight: 500;
}

/* No Instructors */
.no-instructors {
  padding: 80px 20px;
  color: #7f8c8d;
  text-align: center;
}

.no-instructors i {
  color: #bdc3c7;
  margin-bottom: 20px;
}

.no-instructors h4 {
  color: #2c3e50;
  margin-bottom: 10px;
}

/* Load More Button */
.instructor__load-more {
  margin-top: 50px;
}

.btn-two {
  background: var(--primary-color, #4a6cf7);
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
  background: #3a5bd9;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(74, 108, 247, 0.3);
}

/* CTA Section */
.cta__area {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 100px 0;
}

.cta__icon {
  margin-bottom: 20px;
}

.cta__title {
  font-size: 36px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 15px;
}

.cta__content p {
  font-size: 18px;
  color: #7f8c8d;
  margin-bottom: 30px;
}

.cta__placeholder {
  text-align: center;
  color: #bdc3c7;
}

.cta__placeholder i {
  margin-bottom: 20px;
}

/* ==================== */
/* BREADCRUMB STYLES */
/* ==================== */

.breadcrumb__area {
  position: relative;
  padding: 50px 0 50px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
  color: #000000;
}

.breadcrumb__content {
  text-align: center;
  position: relative;
  z-index: 3;
  color: #000000;
}

.breadcrumb__content .title {
  font-size: 48px;
  font-weight: 700;
  color: #000000;
  margin-bottom: 15px;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.breadcrumb {
  display: flex;
  justify-content: center;
  align-items: center;
  list-style: none;
  padding: 0;
  margin: 0;
  color: #000000;
  font-size: 16px;
  font-weight: 500;
}

.breadcrumb a {
  color: #000000;
  text-decoration: none;
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.breadcrumb a:hover {
  opacity: 1;
  color: #000000;
}

.breadcrumb-separator {
  color: #000000;
  opacity: 0.8;
  margin: 0 10px;
  font-size: 14px;
}

.breadcrumb span:not(.breadcrumb-separator) {
  color: #000000;
  opacity: 1;
  font-weight: 600;
}

/* ==================== */
/* BREADCRUMB SHAPE POSITIONING */
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
}

/* Position each shape individually with pixel values */
.breadcrumb__shape-wrap img:nth-child(1) {
  /* breadcrumb_shape01.svg */
  top: 20%;
  left: 8%;
  width: 120px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(2) {
  /* breadcrumb_shape02.svg */
  top: 35%;
  right: 20%;
  width: 80px;
  z-index: 1;
}
.breadcrumb__shape-wrap img:nth-child(3) {
  /* breadcrumb_shape03.png */
  bottom: 1%;
  left: 32%;
  width: 100px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(4) {
  /* breadcrumb_shape04.png */
  bottom: 2%;
  right: 40%;
  width: 90px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(5) {
  /* breadcrumb_shape05.svg */
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

/* Animation keyframes */
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

/* AOS animations for other shapes */
.breadcrumb__shape-wrap img[data-aos] {
  opacity: 0.7;
}

/* Responsive Design */
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
  
  .social-links {
    gap: 8px;
  }
  
  .social-link {
    width: 32px;
    height: 32px;
    font-size: 12px;
  }
  
  .results-count {
    justify-content: flex-start;
    margin-top: 10px;
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
}
</style>