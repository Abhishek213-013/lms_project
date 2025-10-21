<template>
  <div class="page-not-found">
    <div class="not-found-container">
      <!-- Show loading state -->
      <div v-if="loading" class="loading-container">
        <div class="spinner"></div>
        <p>Loading courses...</p>
      </div>

      <!-- Show error state -->
      <div v-else-if="error" class="error-container">
        <img class="image" alt="Error loading courses" src="/assets/img/empty/courses_empty.svg" />
        <h1 class="title">Unable to Load Courses</h1>
        <p class="text">
          {{ error }}
        </p>
        <button @click="fetchCourses" class="btn btn-primary">
          <i class="fas fa-refresh"></i>
          Try Again
        </button>
      </div>

      <!-- Show courses when available -->
      <div v-else-if="courses.length > 0" class="courses-container">
        <h1 class="title">Available Courses</h1>
        <p class="text">
          Explore our wide range of courses and classes
        </p>
        
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
                :alt="course.name" 
                @error="handleImageError"
              />
              <div class="course-type-badge" :class="course.type">
                {{ course.type === 'regular' ? 'Class' : 'Course' }}
              </div>
            </div>
            <div class="course-content">
              <h4 class="course-title">{{ course.name }}</h4>
              <p class="course-description">
                {{ getCourseDescription(course) }}
              </p>
              <div class="course-meta">
                <span class="course-category" :class="course.type">
                  {{ getCourseCategory(course) }}
                </span>
                <span class="course-students">
                  <i class="fas fa-users"></i> {{ course.studentCount || 0 }}
                </span>
              </div>
              <div class="course-details">
                <div v-if="course.type === 'regular'" class="course-grade">
                  <i class="fas fa-graduation-cap"></i>
                  Grade {{ course.grade }}
                </div>
                <div v-else class="course-category-info">
                  <i class="fas fa-tag"></i>
                  {{ course.category }}
                </div>
                <div class="course-status" :class="course.status">
                  {{ course.status }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <button @click="clearFilters" class="btn btn-secondary">
            <i class="fas fa-refresh"></i>
            Clear Filters
          </button>
          <router-link to="/" class="btn btn-primary">
            <i class="fas fa-home"></i>
            Back to Home
          </router-link>
        </div>
      </div>

      <!-- Show empty state when no courses -->
      <div v-else class="empty-container">
        <img class="image" alt="No Courses Found" src="/assets/img/empty/courses_empty.svg" />
        <h1 class="title">No Courses Available</h1>
        <p class="text">
          Sorry, we couldn't find any courses matching your criteria. 
          It's just an accident that was not intentional.
        </p>
        
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
            <select v-model="courseType" class="filter-select">
              <option value="">All Course Types</option>
              <option value="regular">Regular Classes</option>
              <option value="other">Skill Courses</option>
            </select>
            
            <select v-model="sortBy" class="filter-select">
              <option value="name">Sort by Name</option>
              <option value="grade">Sort by Grade</option>
              <option value="studentCount">Sort by Popularity</option>
            </select>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <button @click="clearFilters" class="btn btn-secondary">
            <i class="fas fa-refresh"></i>
            Clear Filters
          </button>
          <router-link to="/" class="btn btn-primary">
            <i class="fas fa-home"></i>
            Back to Home
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { authService } from '../src/services/authService.js';

export default {
  name: 'CoursesPage',
  data() {
    return {
      loading: true,
      error: null,
      courses: [],
      searchQuery: '',
      courseType: '',
      sortBy: 'name',
      isTokenInvalid: false
    }
  },
  computed: {
    filteredCourses() {
      let filtered = this.courses;

      // Filter by search query
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(course => 
          course.name.toLowerCase().includes(query) ||
          (course.description && course.description.toLowerCase().includes(query)) ||
          (course.category && course.category.toLowerCase().includes(query)) ||
          (course.subject && course.subject.toLowerCase().includes(query))
        );
      }

      // Filter by course type
      if (this.courseType) {
        filtered = filtered.filter(course => course.type === this.courseType);
      }

      // Sort courses
      switch (this.sortBy) {
        case 'name':
          filtered.sort((a, b) => a.name.localeCompare(b.name));
          break;
        case 'grade':
          filtered.sort((a, b) => (a.grade || 0) - (b.grade || 0));
          break;
        case 'studentCount':
          filtered.sort((a, b) => (b.studentCount || 0) - (a.studentCount || 0));
          break;
      }

      return filtered;
    }
  },
  async mounted() {
    await this.fetchCourses();
  },
  methods: {
    async fetchCourses() {
      this.loading = true;
      this.error = null;
      this.isTokenInvalid = false;
      
      try {
        console.log('📚 Fetching courses...');

        // Use the auth service for API calls
        const data = await authService.apiCall('/api/courses/classes');
        
        console.log('✅ Courses data received:', data);

        if (data.success) {
          this.courses = data.data || [];
          console.log(`📊 Loaded ${this.courses.length} courses`);
        } else {
          throw new Error(data.message || 'Failed to load courses');
        }
      } catch (error) {
        console.error('❌ Error fetching courses:', error);
        this.error = error.message || 'Unable to load courses. Please try again later.';
        this.isTokenInvalid = error.message.includes('session') || error.message.includes('Authentication');
        
        if (this.isTokenInvalid) {
          // Redirect to login after a short delay
          setTimeout(() => {
            this.$router.push('/student-login');
          }, 2000);
        }
      } finally {
        this.loading = false;
      }
    },

    goToLogin() {
      this.$router.push('/student-login');
    },

    getCourseThumbnail(course) {
      // Your existing method remains the same
      if (course.type === 'regular') {
        const grade = course.grade || 1;
        const thumbnails = [
          '/assets/img/courses/course_thumb01.png',
          '/assets/img/courses/course_thumb02.png',
          '/assets/img/courses/course_thumb03.png',
          '/assets/img/courses/course_thumb04.png'
        ];
        return thumbnails[(grade - 1) % thumbnails.length];
      } else {
        const thumbnails = {
          'Life Skills': '/assets/img/courses/course_thumb03.png',
          'Spoken English': '/assets/img/courses/course_thumb04.png',
          'default': '/assets/img/courses/course_thumb01.png'
        };
        return thumbnails[course.category] || thumbnails.default;
      }
    },

    handleImageError(event) {
      event.target.src = '/assets/img/courses/course_thumb01.png';
    },

    getCourseDescription(course) {
      if (course.description) {
        return course.description.length > 120 ? course.description.substring(0, 120) + '...' : course.description;
      }
      
      if (course.type === 'regular') {
        return `Comprehensive ${course.name} curriculum for grade ${course.grade} students`;
      } else {
        return `Explore ${course.name} - ${course.category || 'Specialized course'}`;
      }
    },

    getCourseCategory(course) {
      if (course.type === 'regular') {
        if (course.grade <= 5) return 'Primary';
        if (course.grade <= 8) return 'Junior';
        if (course.grade <= 10) return 'Secondary';
        return 'Higher Secondary';
      } else {
        return course.category || 'Skill Course';
      }
    },

    searchCourses() {
      console.log('🔍 Searching for:', this.searchQuery);
    },

    filterCourses() {
      console.log('🎯 Filtering by type:', this.courseType);
    },

    sortCourses() {
      console.log('📊 Sorting by:', this.sortBy);
    },

    clearFilters() {
      this.searchQuery = '';
      this.courseType = '';
      this.sortBy = 'name';
    },

    viewCourseDetails(course) {
      if (!authService.isAuthenticated()) {
        this.$router.push('/student-login');
        return;
      }
      
      this.$router.push({
        path: `/course/${course.id}`,
        query: { 
          type: course.type,
          name: course.name
        }
      });
    }
  }
}
</script>

<style scoped>
.page-not-found {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 40px 16px;
  background: #f4f5ff;
  font-family: "DM Sans", "Roboto", sans-serif;
}

.not-found-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  max-width: 1200px;
  width: 100%;
}

/* Loading Styles */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
  padding: 60px 0;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e9ecef;
  border-top: 4px solid #4a6cf7;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error Styles */
.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
  padding: 40px 0;
}

/* Courses Container */
.courses-container {
  width: 100%;
  text-align: center;
}

.image {
  max-width: 400px;
  margin-bottom: 40px;
  height: auto;
  object-fit: contain;
}

.title {
  text-align: center;
  margin-top: 0;
  margin-bottom: 16px;
  font-size: 32px;
  line-height: 40px;
  font-weight: 700;
  color: #1d1e20;
}

.text {
  text-align: center;
  max-width: 650px;
  margin-bottom: 40px;
  font-size: 18px;
  line-height: 28px;
  font-weight: 400;
  color: #6D7081;
}

/* Search Section */
.search-section {
  width: 100%;
  max-width: 600px;
  margin-bottom: 40px;
}

.search-container {
  margin-bottom: 20px;
}

.search-form {
  width: 100%;
}

.search-input-group {
  display: flex;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: box-shadow 0.3s ease;
}

.search-input-group:focus-within {
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
}

.search-input {
  flex: 1;
  border: none;
  padding: 16px 20px;
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
  padding: 16px 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 60px;
}

.search-button:hover {
  background: #3a5bd9;
}

/* Filter Options */
.filter-options {
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
}

.filter-select {
  padding: 12px 16px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  background: white;
  color: #1d1e20;
  font-size: 14px;
  cursor: pointer;
  outline: none;
  transition: border-color 0.3s ease;
  min-width: 180px;
}

.filter-select:focus {
  border-color: #4a6cf7;
}

/* Courses Grid */
.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 25px;
  margin: 40px 0;
  width: 100%;
}

.course-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
  text-align: left;
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.course-thumb {
  height: 180px;
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
  transform: scale(1.05);
}

.course-type-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 12px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: 600;
  color: white;
}

.course-type-badge.regular {
  background: #4a6cf7;
}

.course-type-badge.other {
  background: #10b981;
}

.course-content {
  padding: 20px;
}

.course-title {
  font-size: 18px;
  font-weight: 700;
  color: #1d1e20;
  margin-bottom: 8px;
  line-height: 1.4;
}

.course-description {
  color: #6D7081;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 15px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
  box-orient: vertical;
}

.course-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.course-category {
  background: #e9ecef;
  color: #495057;
  padding: 4px 12px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: 600;
}

.course-category.Primary {
  background: #fff3cd;
  color: #856404;
}

.course-category.Junior {
  background: #d1ecf1;
  color: #0c5460;
}

.course-category.Secondary {
  background: #d4edda;
  color: #155724;
}

.course-category.Higher {
  background: #e2e3e5;
  color: #383d41;
}

.course-students {
  color: #6c757d;
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.course-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  color: #6c757d;
}

.course-grade, .course-category-info {
  display: flex;
  align-items: center;
  gap: 4px;
}

.course-status {
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
}

.course-status.active {
  background: #d4edda;
  color: #155724;
}

.course-status.inactive {
  background: #f8d7da;
  color: #721c24;
}

.course-status.upcoming {
  background: #fff3cd;
  color: #856404;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 15px;
  margin-bottom: 40px;
  flex-wrap: wrap;
  justify-content: center;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
  min-height: 48px;
}

.btn-primary {
  background: #4a6cf7;
  color: white;
}

.btn-primary:hover {
  background: #3a5bd9;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(74, 108, 247, 0.3);
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
}

/* Empty Container */
.empty-container {
  width: 100%;
  text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
  .title {
    font-size: 28px;
    line-height: 36px;
  }
  
  .text {
    font-size: 16px;
    line-height: 24px;
  }
  
  .image {
    max-width: 300px;
    margin-bottom: 30px;
  }
  
  .filter-options {
    flex-direction: column;
    align-items: center;
  }
  
  .filter-select {
    width: 100%;
    max-width: 300px;
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
  
  .courses-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
}

@media (max-width: 480px) {
  .page-not-found {
    padding: 20px 12px;
  }
  
  .title {
    font-size: 24px;
    line-height: 32px;
  }
  
  .text {
    font-size: 14px;
    line-height: 22px;
  }
  
  .image {
    max-width: 250px;
    margin-bottom: 20px;
  }
  
  .search-input {
    padding: 14px 16px;
    font-size: 14px;
  }
  
  .search-button {
    padding: 14px 16px;
    min-width: 50px;
  }
}

/* Loading Animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.not-found-container {
  animation: fadeIn 0.6s ease-out;
}

.course-card {
  animation: fadeIn 0.6s ease-out;
}

/* Focus styles for accessibility */
.search-input:focus,
.filter-select:focus,
.btn:focus {
  outline: 2px solid #4a6cf7;
  outline-offset: 2px;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .not-found-container,
  .course-card,
  .search-input-group,
  .btn,
  .course-thumb img {
    animation: none;
    transition: none;
  }
  
  .course-card:hover,
  .btn:hover {
    transform: none;
  }
  
  .course-card:hover .course-thumb img {
    transform: none;
  }
}
</style>