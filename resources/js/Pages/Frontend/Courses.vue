<template>
  <FrontendLayout>
    <div class="page-courses">
      <div class="courses-container">
        <!-- Show loading state -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>Loading courses...</p>
        </div>

        <!-- Show courses when available -->
        <div v-else-if="coursesData.length > 0" class="courses-content">
          <h1 class="title">Available Courses</h1>
          <p class="text">
            Explore our wide range of courses and classes
          </p>
          
          <!-- Success Banner -->
          <div class="info-banner success">
            <i class="fas fa-check-circle"></i>
            <span>
              Successfully loaded {{ coursesData.length }} courses
            </span>
          </div>
          
          <!-- Search and Filter Section -->
          <div class="search-section">
            <div class="search-container">
              <form @submit.prevent="searchCourses" class="search-form">
                <div class="search-input-group">
                  <input 
                    v-model="searchQuery" 
                    type="text" 
                    placeholder="Search courses..." 
                    class="search-input"
                  >
                  <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </form>
            </div>
            
            <div class="filter-options">
              <select v-model="courseType" class="filter-select" @change="filterCourses">
                <option value="">All Course Types</option>
                <option value="regular">Regular Classes</option>
                <option value="other">Skill Courses</option>
              </select>
              
              <select v-model="sortBy" class="filter-select" @change="sortCourses">
                <option value="name">Sort by Name</option>
                <option value="grade">Sort by Grade</option>
                <option value="studentCount">Sort by Popularity</option>
              </select>
            </div>
          </div>

          <!-- Courses Grid -->
          <div class="courses-grid">
            <div 
              class="course-card" 
              v-for="course in filteredCourses" 
              :key="course.id"
              @click="viewCourseDetails(course)"
            >
              <div class="course-thumb">
                <img 
                  :src="getCourseThumbnail(course)" 
                  :alt="getCourseTitle(course)" 
                  @error="handleImageError"
                />
                <div class="course-type-badge" :class="course.type || 'regular'">
                  {{ (course.type || 'regular') === 'regular' ? 'Class' : 'Course' }}
                </div>
              </div>
              <div class="course-content">
                <!-- Updated: Show Class Name - Subject Name format -->
                <h4 class="course-title">{{ getCourseTitle(course) }}</h4>
                <p class="course-description">
                  {{ getCourseDescription(course) }}
                </p>
                <div class="course-meta">
                  <span class="course-category" :class="getCourseCategory(course)">
                    {{ getCourseCategory(course) }}
                  </span>
                  <span class="course-students">
                    <i class="fas fa-users"></i> {{ course.student_count || course.enrollment_count || course.studentCount || 0 }}
                  </span>
                </div>
                <div class="course-details">
                  <div v-if="(course.type || 'regular') === 'regular'" class="course-grade">
                    <i class="fas fa-graduation-cap"></i>
                    Grade {{ course.grade || 'N/A' }}
                  </div>
                  <div v-else class="course-category-info">
                    <i class="fas fa-tag"></i>
                    {{ course.category || 'Skill Course' }}
                  </div>
                  <div class="course-status" :class="course.status || 'active'">
                    {{ course.status || 'active' }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="courses.links && courses.links.length > 3" class="pagination">
            <div class="pagination-links">
              <template v-for="link in courses.links">
                <button
                  v-if="link.url"
                  :key="link.label"
                  @click="handlePagination(link.url)"
                  class="pagination-link"
                  :class="{ 
                    'active': link.active,
                    'disabled': !link.url
                  }"
                  v-html="link.label"
                ></button>
              </template>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button @click="clearFilters" class="btn btn-secondary">
              <i class="fas fa-refresh"></i>
              Clear Filters
            </button>
            <button @click="refreshCourses" class="btn btn-primary">
              <i class="fas fa-sync"></i>
              Refresh Courses
            </button>
          </div>
        </div>

        <!-- Show empty state when no courses -->
        <div v-else class="empty-container">
          <div class="empty-icon">
            <i class="fas fa-book-open fa-4x"></i>
          </div>
          <h1 class="title">No Courses Available</h1>
          <p class="text">
            No courses found. Please check back later.
          </p>
          <div class="action-buttons">
            <button @click="refreshCourses" class="btn btn-primary">
              <i class="fas fa-refresh"></i>
              Reload Courses
            </button>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>


<script>
import { router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import FrontendLayout from '../Layout/FrontendLayout.vue';

export default {
  name: 'CoursesPage',
  components: {
    FrontendLayout
  },
  props: {
    courses: {
      type: [Array, Object],
      default: () => ({ data: [] })
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props) {
    // Reactive data
    const loading = ref(false);
    const searchQuery = ref(props.filters.search || '');
    const courseType = ref(props.filters.type || '');
    const sortBy = ref(props.filters.sort || 'name');

    // Extract courses data from paginated object or array
    const coursesData = computed(() => {
      if (Array.isArray(props.courses)) {
        return props.courses;
      } else if (props.courses && props.courses.data) {
        return props.courses.data;
      }
      return [];
    });

    // Refresh courses
    const refreshCourses = () => {
      router.reload({ only: ['courses'] });
    };

    // Handle pagination
    const handlePagination = (url) => {
      if (url) {
        router.visit(url);
      }
    };

    // NEW: Get course title in "Class Name - Subject Name" format
    const getCourseTitle = (course) => {
      if (course.type === 'regular') {
        // For regular classes: "Class 1 - Mathematics"
        const className = course.name || `Class ${course.grade || ''}`;
        const subjectName = course.subject || 'General';
        return `${className} - ${subjectName}`;
      } else {
        // For other courses: Use the course name directly
        return course.name || course.class_name || 'Untitled Course';
      }
    };

    // Computed filtered courses
    const filteredCourses = computed(() => {
      let filtered = coursesData.value;

      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(course => {
          const courseName = course.name || course.class_name || '';
          const courseDescription = course.description || '';
          const courseCategory = course.category || '';
          const courseSubject = course.subject || '';
          
          return courseName.toLowerCase().includes(query) ||
                 courseDescription.toLowerCase().includes(query) ||
                 courseCategory.toLowerCase().includes(query) ||
                 courseSubject.toLowerCase().includes(query);
        });
      }

      if (courseType.value) {
        filtered = filtered.filter(course => {
          const courseType = course.type || 'regular';
          return courseType === courseType.value;
        });
      }

      switch (sortBy.value) {
        case 'name':
          filtered.sort((a, b) => {
            const nameA = getCourseTitle(a);
            const nameB = getCourseTitle(b);
            return nameA.localeCompare(nameB);
          });
          break;
        case 'grade':
          filtered.sort((a, b) => (a.grade || 0) - (b.grade || 0));
          break;
        case 'studentCount':
          filtered.sort((a, b) => {
            const countA = a.student_count || a.enrollment_count || a.studentCount || 0;
            const countB = b.student_count || b.enrollment_count || b.studentCount || 0;
            return countB - countA;
          });
          break;
      }

      return filtered;
    });

    // Methods
    const getCourseThumbnail = (course) => {
      const courseType = course.type || 'regular';
      
      if (courseType === 'regular') {
        const grade = course.grade || 1;
        const thumbnails = [
          '/assets/img/courses/h5_course_thumb1.jpg',
          '/assets/img/courses/h5_course_thumb02.jpg',
          '/assets/img/courses/h5_course_thumb03.jpg',
          '/assets/img/courses/h5_course_thumb04.jpg'
        ];
        return thumbnails[(grade - 1) % thumbnails.length];
      } else {
        const thumbnails = {
          'Language': '/assets/img/courses/h5_course_thumb05.jpg',
          'Technology': '/assets/img/courses/h5_course_thumb06.jpg',
          'Personal Development': '/assets/img/courses/h5_course_thumb07.jpg',
          'default': '/assets/img/courses/h5_course_thumb08.jpg'
        };
        return thumbnails[course.category] || thumbnails.default;
      }
    };

    const handleImageError = (event) => {
      event.target.src = '/assets/img/courses/h5_course_thumb01.jpg';
    };

    const getCourseDescription = (course) => {
      if (course.description) {
        return course.description.length > 120 ? course.description.substring(0, 120) + '...' : course.description;
      }
      
      const courseType = course.type || 'regular';
      if (courseType === 'regular') {
        return `Comprehensive ${course.subject || 'subject'} curriculum for ${course.name || `Class ${course.grade}`} students`;
      } else {
        return `Explore ${course.name || course.class_name || 'this course'} - ${course.category || 'Specialized course'}`;
      }
    };

    const getCourseCategory = (course) => {
      const courseType = course.type || 'regular';
      
      if (courseType === 'regular') {
        const grade = course.grade || 1;
        if (grade <= 5) return 'Primary';
        if (grade <= 8) return 'Junior';
        if (grade <= 10) return 'Secondary';
        return 'Higher Secondary';
      } else {
        return course.category || 'Skill Course';
      }
    };

    const searchCourses = () => {
      console.log('🔍 Searching for:', searchQuery.value);
    };

    const filterCourses = () => {
      console.log('🎯 Filtering by type:', courseType.value);
    };

    const sortCourses = () => {
      console.log('📊 Sorting by:', sortBy.value);
    };

    const clearFilters = () => {
      searchQuery.value = '';
      courseType.value = '';
      sortBy.value = 'name';
    };

    const viewCourseDetails = (course) => {
      const courseId = course.id || course.course_id;
      if (!courseId) {
        console.error('No course ID found:', course);
        return;
      }
      
      router.visit(`/course/${courseId}`, {
        data: {
          type: course.type || 'regular',
          name: getCourseTitle(course)
        }
      });
    };

    return {
      loading,
      searchQuery,
      courseType,
      sortBy,
      coursesData,
      filteredCourses,
      refreshCourses,
      handlePagination,
      getCourseTitle, // NEW: Export the new method
      getCourseThumbnail,
      handleImageError,
      getCourseDescription,
      getCourseCategory,
      searchCourses,
      filterCourses,
      sortCourses,
      clearFilters,
      viewCourseDetails
    };
  }
}
</script>

<style scoped>
/* ==================== */
/* LAYOUT & CONTAINERS */
/* ==================== */
.page-courses {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 0;
}

.courses-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 24px;
  background: white;
  min-height: 100vh;
}

/* ==================== */
/* HEADER SECTION */
/* ==================== */
.header-section {
  text-align: center;
  padding: 40px 0 30px;
  margin-bottom: 40px;
  border-bottom: 1px solid #e9ecef;
}

.title {
  font-size: 3rem;
  font-weight: 800;
  color: #1a202c;
  margin-bottom: 16px;
  line-height: 1.2;
}

.subtitle {
  font-size: 1.25rem;
  color: #718096;
  margin-bottom: 30px;
  line-height: 1.6;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

/* ==================== */
/* INFO BANNER */
/* ==================== */
.info-banner {
  padding: 16px 24px;
  border-radius: 12px;
  margin: 0 auto 30px;
  display: inline-flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
  max-width: 500px;
  width: 100%;
  justify-content: center;
}

.info-banner.success {
  background: #f0fff4;
  border: 2px solid #9ae6b4;
  color: #2f855a;
}

.info-banner i {
  font-size: 1.2em;
}

/* ==================== */
/* SEARCH & FILTER SECTION */
/* ==================== */
.search-filter-section {
  background: #f8f9fa;
  padding: 32px;
  border-radius: 16px;
  margin-bottom: 50px;
  border: 1px solid #e9ecef;
}

.search-container {
  margin-bottom: 24px;
}

.search-form {
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
}

.search-input-group {
  display: flex;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.search-input-group:focus-within {
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
  border-color: #4a6cf7;
}

.search-input {
  flex: 1;
  border: none;
  padding: 18px 24px;
  font-size: 16px;
  outline: none;
  background: transparent;
  color: #1d1e20;
}

.search-input::placeholder {
  color: #a0a4a8;
}

.search-button {
  border: none;
  background: #4a6cf7;
  color: white;
  padding: 18px 24px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 70px;
}

.search-button:hover {
  background: #3a5bd9;
}

.filter-options {
  display: flex;
  gap: 30px;
  justify-content: center;
  flex-wrap: wrap;
}

.filter-select {
  padding: 14px 20px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  background: white;
  color: #1d1e20;
  font-size: 15px;
  cursor: pointer;
  outline: none;
  transition: all 0.3s ease;
  min-width: 200px;
  font-weight: 500;
}

.filter-select:focus {
  border-color: #4a6cf7;
  box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
}

/* ==================== */
/* COURSES GRID SECTION */
/* ==================== */
.courses-grid-section {
  padding: 20px 0 35px;
}

.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 30px;
  margin: 0 auto;
}

.course-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
  text-align: left;
  border: 1px solid #f1f3f4;
}

.course-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
  border-color: #4a6cf7;
}

.course-thumb {
  height: 220px;
  overflow: hidden;
  position: relative;
}

.course-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.course-card:hover .course-thumb img {
  transform: scale(1.08);
}

.course-type-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.course-type-badge.regular {
  background: linear-gradient(135deg, #4a6cf7, #3a5bd9);
}

.course-type-badge.other {
  background: linear-gradient(135deg, #10b981, #059669);
}

.course-content {
  padding: 28px;
}

.course-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 12px;
  line-height: 1.4;
  min-height: 56px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.course-description {
  color: #718096;
  font-size: 14px;
  line-height: 1.6;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 44px;
}

.course-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #f1f3f4;
}

.course-category {
  background: #e9ecef;
  color: #495057;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.course-category.Primary {
  background: linear-gradient(135deg, #fff3cd, #ffeaa7);
  color: #856404;
}

.course-category.Junior {
  background: linear-gradient(135deg, #d1ecf1, #b8e6f1);
  color: #0c5460;
}

.course-category.Secondary {
  background: linear-gradient(135deg, #d4edda, #c3e6cb);
  color: #155724;
}

.course-category.Higher {
  background: linear-gradient(135deg, #e2e3e5, #d6d8db);
  color: #383d41;
}

.course-students {
  color: #6c757d;
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.course-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  color: #6c757d;
}

.course-grade, .course-category-info {
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 500;
}

.course-status {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.course-status.active {
  background: linear-gradient(135deg, #d4edda, #c3e6cb);
  color: #155724;
}

.course-status.inactive {
  background: linear-gradient(135deg, #f8d7da, #f5c6cb);
  color: #721c24;
}

.course-status.upcoming {
  background: linear-gradient(135deg, #fff3cd, #ffeaa7);
  color: #856404;
}

/* ==================== */
/* PAGINATION SECTION */
/* ==================== */
.pagination-section {
  padding: 40px 0;
  border-top: 1px solid #e9ecef;
  margin-top: 20px;
}

.pagination-links {
  display: flex;
  gap: 8px;
  justify-content: center;
  flex-wrap: wrap;
}

.pagination-link {
  padding: 12px 18px;
  border: 2px solid #e9ecef;
  background: white;
  color: #4a5568;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
  min-width: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-link:hover:not(.disabled) {
  background: #4a6cf7;
  color: white;
  border-color: #4a6cf7;
  transform: translateY(-2px);
}

.pagination-link.active {
  background: #4a6cf7;
  color: white;
  border-color: #4a6cf7;
}

.pagination-link.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* ==================== */
/* ACTION BUTTONS SECTION */
/* ==================== */
.action-buttons-section {
  padding: 50px 0 20px;
  border-top: 1px solid #e9ecef;
}

.action-buttons {
  display: flex;
  gap: 26px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 20px;
  padding: 26px 20px;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
  min-height: 56px;
}

.btn-primary {
  background: linear-gradient(135deg, #4a6cf7, #3a5bd9);
  color: white;
  box-shadow: 0 4px 15px rgba(74, 108, 247, 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #3a5bd9, #2a4bc7);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(74, 108, 247, 0.4);
}

.btn-secondary {
  background: linear-gradient(135deg, #6c757d, #5a6268);
  color: white;
  box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-secondary:hover {
  background: linear-gradient(135deg, #5a6268, #495057);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
}

/* ==================== */
/* EMPTY STATE */
/* ==================== */
.empty-container {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon {
  color: #cbd5e0;
  margin-bottom: 30px;
  opacity: 0.7;
}

.empty-icon i {
  font-size: 4rem;
}

/* ==================== */
/* LOADING STATES */
/* ==================== */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
  padding: 100px 0;
}

.spinner {
  width: 60px;
  height: 60px;
  border: 4px solid #e9ecef;
  border-top: 4px solid #4a6cf7;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* ==================== */
/* RESPONSIVE DESIGN */
/* ==================== */
@media (max-width: 1200px) {
  .courses-grid {
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 28px;
  }
}

@media (max-width: 768px) {
  .courses-container {
    padding: 24px 16px;
  }
  
  .header-section {
    padding: 30px 0 20px;
    margin-bottom: 30px;
  }
  
  .title {
    font-size: 2.25rem;
  }
  
  .subtitle {
    font-size: 1.125rem;
  }
  
  .search-filter-section {
    padding: 24px 20px;
    margin-bottom: 40px;
  }
  
  .search-input {
    padding: 16px 20px;
  }
  
  .search-button {
    padding: 16px 20px;
    min-width: 60px;
  }
  
  .filter-options {
    flex-direction: column;
    align-items: center;
    gap: 16px;
  }
  
  .filter-select {
    width: 100%;
    max-width: 300px;
  }
  
  .courses-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }
  
  .course-content {
    padding: 24px;
  }
  
  .action-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .btn {
    width: 100%;
    max-width: 300px;
    justify-content: center;
  }
  
  .pagination-links {
    gap: 6px;
  }
  
  .pagination-link {
    padding: 10px 14px;
    min-width: 42px;
  }
}

@media (max-width: 480px) {
  .courses-container {
    padding: 20px 12px;
  }
  
  .header-section {
    padding: 20px 0 16px;
  }
  
  .title {
    font-size: 1.875rem;
  }
  
  .subtitle {
    font-size: 1rem;
  }
  
  .search-filter-section {
    padding: 20px 16px;
    margin-bottom: 30px;
  }
  
  .search-input {
    padding: 14px 16px;
    font-size: 14px;
  }
  
  .search-button {
    padding: 14px 16px;
    min-width: 50px;
  }
  
  .course-content {
    padding: 20px;
  }
  
  .course-title {
    font-size: 1.125rem;
  }
  
  .btn {
    padding: 14px 20px;
    min-height: 50px;
  }
}

/* ==================== */
/* ANIMATIONS */
/* ==================== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.courses-content {
  animation: fadeInUp 0.6s ease-out;
}

.course-card {
  animation: fadeInUp 0.6s ease-out;
}

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.search-input:focus,
.filter-select:focus,
.btn:focus,
.pagination-link:focus {
  outline: 3px solid rgba(74, 108, 247, 0.3);
  outline-offset: 2px;
}

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .courses-content,
  .course-card,
  .search-input-group,
  .btn,
  .course-thumb img,
  .pagination-link {
    animation: none;
    transition: none;
  }
  
  .course-card:hover,
  .btn:hover,
  .pagination-link:hover {
    transform: none;
  }
  
  .course-card:hover .course-thumb img {
    transform: none;
  }
}
</style>