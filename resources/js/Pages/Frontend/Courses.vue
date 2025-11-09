<template>
  <FrontendLayout>
    <div class="page-courses">
      <div class="courses-container">
        <!-- Show loading state -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('Loading courses...') }}</p>
        </div>

        <!-- Show courses when available -->
        <div v-else-if="coursesData.length > 0" class="courses-content">
          <!-- Header Section - Centered -->
          <div class="header-section">
            <h1 class="title">{{ t('Available Courses') }}</h1>
            <p class="subtitle">
              {{ t('Explore our wide range of courses and classes') }}
            </p>
          </div>
          
          <!-- Search and Filter Section -->
          <div class="search-filter-section">
            <div class="search-container">
              <form @submit.prevent="searchCourses" class="search-form">
                <div class="search-input-group">
                  <input 
                    v-model="searchQuery" 
                    type="text" 
                    :placeholder="t('Search courses...')" 
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
                <option value="">{{ t('All Course Types') }}</option>
                <option value="regular">{{ t('Regular Classes') }}</option>
                <option value="other">{{ t('Skill Courses') }}</option>
              </select>
              
              <select v-model="sortBy" class="filter-select" @change="sortCourses">
                <option value="name">{{ t('Sort by Name') }}</option>
                <option value="grade">{{ t('Sort by Grade') }}</option>
                <option value="studentCount">{{ t('Sort by Popularity') }}</option>
              </select>

              <select v-model="perPage" class="filter-select" @change="updatePageSize">
                <option value="12">{{ t('12 per page') }}</option>
                <option value="24">{{ t('24 per page') }}</option>
                <option value="48">{{ t('48 per page') }}</option>
                <option value="96">{{ t('96 per page') }}</option>
              </select>
            </div>
          </div>

          <!-- Courses Count and Pagination Info -->
          <div class="pagination-info-section">
            <div class="pagination-info">
              <span class="results-count">
                {{ t('Showing') }} 
                <strong>{{ pagination.from }}-{{ pagination.to }}</strong> 
                {{ t('of') }} 
                <strong>{{ pagination.total }}</strong> 
                {{ t('courses') }}
              </span>
            </div>
          </div>

          <!-- Courses Grid Section -->
          <div class="courses-grid-section">
            <div class="courses-grid">
              <div 
                class="course-card" 
                v-for="course in coursesData" 
                :key="course.id"
                @click="viewCourseDetails(course)"
              >
                <div class="course-thumb">
                  <img 
                    :src="getCourseThumbnail(course)" 
                    :alt="getCourseTitle(course)" 
                    :data-course-id="course.id"
                    :data-image-src="course.image || 'none'"
                    :data-thumbnail-src="course.thumbnail || 'none'"
                    @error="handleImageError"
                    @load="handleImageLoad(course.id)"
                    loading="lazy"
                  />
                  <div class="course-type-badge" :class="course.type || 'regular'">
                    {{ (course.type || 'regular') === 'regular' ? t('Class') : t('Course') }}
                  </div>
                  <!-- Image loading indicator -->
                  <div v-if="imageLoading[course.id]" class="image-loading">
                    <div class="loading-spinner"></div>
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
                      {{ getCourseCategoryText(course) }}
                    </span>
                    <span class="course-students">
                      <i class="fas fa-users"></i> {{ course.student_count || course.enrollment_count || course.studentCount || 0 }}
                    </span>
                  </div>
                  <div class="course-details">
                    <div v-if="(course.type || 'regular') === 'regular'" class="course-grade">
                      <i class="fas fa-graduation-cap"></i>
                      {{ t('Grade') }} {{ course.grade || 'N/A' }}
                    </div>
                    <div v-else class="course-category-info">
                      <i class="fas fa-tag"></i>
                      {{ course.category || t('Skill Course') }}
                    </div>
                    <div class="course-status" :class="course.status || 'active'">
                      {{ getStatusText(course.status || 'active') }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Pagination Section -->
          <div v-if="showPagination" class="pagination-section">
            <div class="pagination-container">
              <!-- Previous Button -->
              <button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="!pagination.prev_page_url"
                class="pagination-btn pagination-prev"
                :class="{ 'disabled': !pagination.prev_page_url }"
              >
                <i class="fas fa-chevron-left"></i>
                {{ t('Previous') }}
              </button>

              <!-- Page Numbers -->
              <div class="pagination-numbers">
                <!-- First Page -->
                <button
                  v-if="pagination.current_page > 2"
                  @click="goToPage(1)"
                  class="pagination-number"
                  :class="{ 'active': pagination.current_page === 1 }"
                >
                  1
                </button>

                <!-- Ellipsis before current page if needed -->
                <span 
                  v-if="pagination.current_page > 3" 
                  class="pagination-ellipsis"
                >
                  ...
                </span>

                <!-- Pages around current page -->
                <template v-for="page in visiblePages" :key="page">
                  <button
                    v-if="page > 0 && page <= pagination.last_page"
                    @click="goToPage(page)"
                    class="pagination-number"
                    :class="{ 'active': page === pagination.current_page }"
                  >
                    {{ page }}
                  </button>
                </template>

                <!-- Ellipsis after current page if needed -->
                <span 
                  v-if="pagination.current_page < pagination.last_page - 2" 
                  class="pagination-ellipsis"
                >
                  ...
                </span>

                <!-- Last Page -->
                <button
                  v-if="pagination.current_page < pagination.last_page - 1"
                  @click="goToPage(pagination.last_page)"
                  class="pagination-number"
                  :class="{ 'active': pagination.current_page === pagination.last_page }"
                >
                  {{ pagination.last_page }}
                </button>
              </div>

              <!-- Next Button -->
              <button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="!pagination.next_page_url"
                class="pagination-btn pagination-next"
                :class="{ 'disabled': !pagination.next_page_url }"
              >
                {{ t('Next') }}
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>

            <!-- Page Jump -->
            <div class="page-jump-section">
              <span>{{ t('Go to page') }}:</span>
              <input
                type="number"
                v-model="pageJump"
                :min="1"
                :max="pagination.last_page"
                class="page-jump-input"
                @keyup.enter="goToPage(parseInt(pageJump))"
              >
              <button 
                @click="goToPage(parseInt(pageJump))"
                class="page-jump-btn"
                :disabled="!pageJump || pageJump < 1 || pageJump > pagination.last_page"
              >
                {{ t('Go') }}
              </button>
            </div>
          </div>

          <!-- Action Buttons Section -->
          <div class="action-buttons-section">
            <div class="action-buttons">
              <button @click="clearFilters" class="btn btn-secondary">
                <i class="fas fa-refresh"></i>
                {{ t('Clear Filters') }}
              </button>
              <button @click="refreshCourses" class="btn btn-primary">
                <i class="fas fa-sync"></i>
                {{ t('Refresh Courses') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Show empty state when no courses -->
        <div v-else class="empty-container">
          <div class="empty-icon">
            <i class="fas fa-book-open fa-4x"></i>
          </div>
          <h1 class="title">{{ t('No Courses Available') }}</h1>
          <p class="text">
            {{ t('No courses found. Please check back later.') }}
          </p>
          <div class="action-buttons">
            <button @click="refreshCourses" class="btn btn-primary">
              <i class="fas fa-refresh"></i>
              {{ t('Reload Courses') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import FrontendLayout from '../Layout/FrontendLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

// Define props FIRST
const props = defineProps({
  courses: {
    type: [Array, Object],
    default: () => ({ data: [] })
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

// Use the global translation composable
const { currentLanguage, t, switchLanguage } = useTranslation()

// Reactive data - NOW we can use props
const loading = ref(false);
const searchQuery = ref(props.filters.search || '');
const courseType = ref(props.filters.type || '');
const sortBy = ref(props.filters.sort || 'name');
const currentTheme = ref('light');
const imageLoading = ref({}); // Track image loading states
const perPage = ref(props.filters.per_page || 12); // Items per page
const pageJump = ref(''); // For page jump input

// Add icon render key to prevent disappearing icons
const iconRenderKey = ref(0);

// Watch for language changes to update icons
watch(currentLanguage, () => {
  iconRenderKey.value++;
});

// Handle theme changes
const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme;
};

// Extract courses data from paginated object or array
const coursesData = computed(() => {
  const data = Array.isArray(props.courses) ? props.courses : (props.courses?.data || []);
  
  // Log the data structure to debug
  console.log('📦 Courses data received:', data);
  
  if (data.length > 0) {
    console.log('🖼️ First course image data:', {
      id: data[0].id,
      name: data[0].name,
      image: data[0].image,
      image_url: data[0].image_url,
      thumbnail: data[0].thumbnail,
      thumbnail_url: data[0].thumbnail_url
    });
  }
  
  return data;
});

// Enhanced Pagination Computed Properties
const pagination = computed(() => {
  if (Array.isArray(props.courses)) {
    // If courses is an array (no pagination), return default values
    return {
      current_page: 1,
      last_page: 1,
      per_page: perPage.value,
      from: 1,
      to: props.courses.length,
      total: props.courses.length,
      prev_page_url: null,
      next_page_url: null,
      path: window.location.pathname
    };
  }

  // Handle paginated response
  const paginationData = props.courses;
  return {
    current_page: paginationData.current_page || 1,
    last_page: paginationData.last_page || 1,
    per_page: paginationData.per_page || perPage.value,
    from: paginationData.from || 1,
    to: paginationData.to || paginationData.data?.length || 0,
    total: paginationData.total || 0,
    prev_page_url: paginationData.prev_page_url || null,
    next_page_url: paginationData.next_page_url || null,
    path: paginationData.path || window.location.pathname
  };
});

// Compute visible pages for pagination
const visiblePages = computed(() => {
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const delta = 2; // Number of pages to show around current page
  const range = [];
  const rangeWithDots = [];

  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i);
  }

  if (current - delta > 2) {
    rangeWithDots.push(1);
    if (current - delta > 3) {
      rangeWithDots.push('...');
    }
  } else {
    rangeWithDots.push(1);
  }

  rangeWithDots.push(...range);

  if (current + delta < last - 1) {
    if (current + delta < last - 2) {
      rangeWithDots.push('...');
    }
    rangeWithDots.push(last);
  } else if (last > 1) {
    rangeWithDots.push(last);
  }

  return rangeWithDots.filter(page => page !== '...');
});

const showPagination = computed(() => {
  return pagination.value.total > pagination.value.per_page;
});

// Pagination Methods
const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page || page === pagination.value.current_page) return;
  
  const currentParams = new URLSearchParams(window.location.search);
  currentParams.set('page', page);
  
  // Preserve existing filters
  if (searchQuery.value) currentParams.set('search', searchQuery.value);
  if (courseType.value) currentParams.set('type', courseType.value);
  if (sortBy.value !== 'name') currentParams.set('sort', sortBy.value);
  if (perPage.value !== 12) currentParams.set('per_page', perPage.value);
  
  router.visit(`${window.location.pathname}?${currentParams.toString()}`);
};

const updatePageSize = () => {
  const currentParams = new URLSearchParams(window.location.search);
  currentParams.set('per_page', perPage.value);
  currentParams.delete('page'); // Go to first page when changing page size
  
  // Preserve existing filters
  if (searchQuery.value) currentParams.set('search', searchQuery.value);
  if (courseType.value) currentParams.set('type', courseType.value);
  if (sortBy.value !== 'name') currentParams.set('sort', sortBy.value);
  
  router.visit(`${window.location.pathname}?${currentParams.toString()}`);
};

// Refresh courses
const refreshCourses = () => {
  router.reload({ only: ['courses'] });
};

// Get course title in "Class Name - Subject Name" format
const getCourseTitle = (course) => {
  if (course.type === 'regular') {
    // For regular classes: "Class 1 - Mathematics"
    const className = course.name || `${t('Class')} ${course.grade || ''}`;
    const subjectName = course.subject || t('General');
    return `${className} - ${subjectName}`;
  } else {
    // For other courses: Use the course name directly
    return course.name || course.class_name || t('Untitled Course');
  }
};

// Get translated category text
const getCourseCategoryText = (course) => {
  const category = getCourseCategory(course);
  return t(category);
};

// Get translated status text
const getStatusText = (status) => {
  return t(status.charAt(0).toUpperCase() + status.slice(1));
};

// Search and Filter Methods
const searchCourses = () => {
  const currentParams = new URLSearchParams(window.location.search);
  
  if (searchQuery.value) {
    currentParams.set('search', searchQuery.value);
  } else {
    currentParams.delete('search');
  }
  
  currentParams.delete('page'); // Reset to first page when searching
  
  router.visit(`${window.location.pathname}?${currentParams.toString()}`);
};

const filterCourses = () => {
  const currentParams = new URLSearchParams(window.location.search);
  
  if (courseType.value) {
    currentParams.set('type', courseType.value);
  } else {
    currentParams.delete('type');
  }
  
  currentParams.delete('page'); // Reset to first page when filtering
  
  router.visit(`${window.location.pathname}?${currentParams.toString()}`);
};

const sortCourses = () => {
  const currentParams = new URLSearchParams(window.location.search);
  
  if (sortBy.value !== 'name') {
    currentParams.set('sort', sortBy.value);
  } else {
    currentParams.delete('sort');
  }
  
  currentParams.delete('page'); // Reset to first page when sorting
  
  router.visit(`${window.location.pathname}?${currentParams.toString()}`);
};

const clearFilters = () => {
  searchQuery.value = '';
  courseType.value = '';
  sortBy.value = 'name';
  perPage.value = 12;
  
  // Clear all filters and go to first page
  router.visit(window.location.pathname);
};

// Image handling methods
const getCourseThumbnail = (course) => {
  console.log('🖼️ Course image data for frontend:', {
    id: course.id,
    name: course.name,
    image: course.image,
    image_url: course.image_url,
    thumbnail: course.thumbnail,
    thumbnail_url: course.thumbnail_url
  });

  // 🔥 FIX: Use the image data from backend
  if (course.image_url && course.image_url !== 'null' && course.image_url !== 'NULL') {
    console.log('✅ Using image_url from backend:', course.image_url);
    return course.image_url;
  }

  if (course.thumbnail_url && course.thumbnail_url !== 'null' && course.thumbnail_url !== 'NULL') {
    console.log('✅ Using thumbnail_url from backend:', course.thumbnail_url);
    return course.thumbnail_url;
  }

  if (course.image && course.image !== 'null' && course.image !== 'NULL') {
    const imageUrl = formatImageUrl(course.image);
    console.log('✅ Using raw image path from backend:', imageUrl);
    return imageUrl;
  }

  if (course.thumbnail && course.thumbnail !== 'null' && course.thumbnail !== 'NULL') {
    const thumbnailUrl = formatImageUrl(course.thumbnail);
    console.log('✅ Using raw thumbnail path from backend:', thumbnailUrl);
    return thumbnailUrl;
  }

  // Fallback to demo thumbnails only if no database image exists
  console.log('📸 No database image found, using fallback for course:', course.id);
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

const formatImageUrl = (imagePath) => {
  if (!imagePath) return null;
  
  console.log('🔄 Formatting image path:', imagePath);
  
  // If it's already a full URL, return as is
  if (imagePath.startsWith('http')) {
    return imagePath;
  }
  
  // If it starts with storage/, remove the storage/ prefix for public access
  if (imagePath.startsWith('storage/')) {
    const publicPath = imagePath.replace('storage/', '');
    return `/storage/${publicPath}`;
  }
  
  // If it's a relative path, assume it's in storage
  if (imagePath.startsWith('courses/')) {
    return `/storage/${imagePath}`;
  }
  
  // Default case - prepend /storage/
  return `/storage/${imagePath}`;
};

const handleImageError = (event) => {
  const img = event.target;
  const courseId = img.dataset.courseId;
  
  console.warn('❌ Image failed to load:', img.src, 'for course:', courseId);
  
  // Mark image as failed to load
  if (courseId) {
    imageLoading.value[courseId] = false;
  }
  
  // Try different fallback strategies
  const fallbacks = [
    '/assets/img/courses/h5_course_thumb01.jpg',
    '/assets/img/courses/default-course.jpg',
    '/assets/img/placeholder-course.jpg'
  ];
  
  let currentFallbackIndex = 0;
  
  const tryNextFallback = () => {
    if (currentFallbackIndex < fallbacks.length) {
      img.src = fallbacks[currentFallbackIndex];
      currentFallbackIndex++;
    } else {
      // All fallbacks failed, use a data URL placeholder
      img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIyMCIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjIwMCIgeT0iMTEwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkNvdXJzZSBJbWFnZTwvdGV4dD48L3N2Zz4=';
      img.onerror = null; // Prevent infinite loop
    }
  };
  
  img.onerror = tryNextFallback;
  tryNextFallback();
};

const handleImageLoad = (courseId) => {
  imageLoading.value[courseId] = false;
};

const preloadImage = (src, courseId) => {
  return new Promise((resolve, reject) => {
    imageLoading.value[courseId] = true;
    const img = new Image();
    img.onload = () => {
      imageLoading.value[courseId] = false;
      resolve(src);
    };
    img.onerror = () => {
      imageLoading.value[courseId] = false;
      reject(new Error(`Failed to load image: ${src}`));
    };
    img.src = src;
  });
};

const getCourseDescription = (course) => {
  if (course.description) {
    return course.description.length > 120 ? course.description.substring(0, 120) + '...' : course.description;
  }
  
  const courseType = course.type || 'regular';
  if (courseType === 'regular') {
    const className = course.name || `Class ${course.grade}`;
    const subjectName = course.subject || t('General');
    return t('Comprehensive curriculum for students. This course covers all essential subjects and prepares students for academic success.');
  } else {
    const courseName = course.name || course.class_name || t('this course');
    const category = course.category || t('Specialized course');
    return t('Explore this course - learn essential skills and knowledge from expert instructors.');
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

// Preload images when component mounts
onMounted(() => {
  // Load theme preference from localStorage
  const savedTheme = localStorage.getItem('preferredTheme')
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    currentTheme.value = savedTheme
  }
  
  // Listen for theme changes
  window.addEventListener('themeChanged', handleThemeChange)
  
  // Preload course images
  coursesData.value.forEach(course => {
    const imageUrl = getCourseThumbnail(course);
    if (imageUrl && !imageUrl.includes('/assets/img/courses/')) {
      preloadImage(imageUrl, course.id).catch(error => {
        console.warn(`Failed to preload image for course ${course.id}:`, error);
      });
    }
  });
})

onUnmounted(() => {
  window.removeEventListener('themeChanged', handleThemeChange)
})
</script>

<style scoped>
/* ==================== */
/* LAYOUT & CONTAINERS */
/* ==================== */
.page-courses {
  min-height: 100vh;
  background: var(--bg-primary);
  padding: 0;
  transition: background-color 0.3s ease;
}

.courses-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 24px;
  background: var(--bg-primary);
  min-height: 100vh;
  transition: background-color 0.3s ease;
}

/* ==================== */
/* HEADER SECTION - CENTERED */
/* ==================== */
.header-section {
  text-align: center;
  padding: 10px 0 20px;
  margin-bottom: 0;
  border-bottom: none;
}

.title {
  font-size: 2rem;
  font-weight: 400;
  color: var(--text-primary);
  margin-bottom: 16px;
  line-height: 1.2;
  transition: color 0.3s ease;
}

.subtitle {
  font-size: 1.25rem;
  color: var(--text-secondary);
  margin-bottom: 0;
  line-height: 1.6;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
  transition: color 0.3s ease;
}

/* ==================== */
/* SEARCH & FILTER SECTION */
/* ==================== */
.search-filter-section {
  background: var(--bg-secondary);
  padding: 32px;
  border-radius: 16px;
  margin-bottom: 40px;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
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
  background: var(--card-bg);
  border-radius: 12px;
  box-shadow: var(--shadow);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.search-input-group:focus-within {
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-color);
}

.search-input {
  flex: 1;
  border: none;
  padding: 18px 24px;
  font-size: 16px;
  outline: none;
  background: transparent;
  color: var(--text-primary);
  transition: color 0.3s ease;
}

.search-input::placeholder {
  color: var(--text-muted);
}

.search-button {
  border: none;
  background: var(--primary-color);
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
  background: var(--primary-hover);
}

.filter-options {
  display: flex;
  gap: 30px;
  justify-content: center;
  flex-wrap: wrap;
}

.filter-select {
  padding: 14px 20px;
  border: 2px solid var(--border-color);
  border-radius: 10px;
  background: var(--card-bg);
  color: var(--text-primary);
  font-size: 15px;
  cursor: pointer;
  outline: none;
  transition: all 0.3s ease;
  min-width: 200px;
  font-weight: 500;
}

.filter-select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 20%, transparent);
}

/* ==================== */
/* PAGINATION INFO SECTION */
/* ==================== */
.pagination-info-section {
  margin-bottom: 30px;
  text-align: center;
}

.pagination-info {
  background: var(--bg-secondary);
  padding: 16px 24px;
  border-radius: 12px;
  border: 1px solid var(--border-color);
  display: inline-block;
}

.results-count {
  color: var(--text-secondary);
  font-size: 14px;
  font-weight: 500;
}

.results-count strong {
  color: var(--text-primary);
}

/* ==================== */
/* COURSES GRID SECTION */
/* ==================== */
.courses-grid-section {
  padding: 20px 0 50px;
}

.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 30px;
  margin: 0 auto;
}

.course-card {
  background: var(--card-bg);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: all 0.3s ease;
  cursor: pointer;
  text-align: left;
  border: 1px solid var(--border-color);
}

.course-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-color);
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

/* Image loading indicator */
.image-loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--bg-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--border-color);
  border-top: 3px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
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
  z-index: 3;
}

.course-type-badge.regular {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
}

.course-type-badge.other {
  background: linear-gradient(135deg, var(--success-color), color-mix(in srgb, var(--success-color) 80%, black));
}

.course-content {
  padding: 28px;
}

.course-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 12px;
  line-height: 1.4;
  min-height: 56px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
}

.course-description {
  color: var(--text-secondary);
  font-size: 14px;
  line-height: 1.6;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 44px;
  line-clamp: 2;
}

.course-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--border-light);
}

.course-category {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
}

/* Category-specific styles with theme support */
.course-category.Primary {
  background: linear-gradient(135deg, color-mix(in srgb, var(--warning-color) 20%, var(--bg-primary)), color-mix(in srgb, var(--warning-color) 40%, var(--bg-primary)));
  color: color-mix(in srgb, var(--warning-color) 70%, black);
}

.course-category.Junior {
  background: linear-gradient(135deg, color-mix(in srgb, #0c5460 20%, var(--bg-primary)), color-mix(in srgb, #0c5460 40%, var(--bg-primary)));
  color: color-mix(in srgb, #0c5460 70%, white);
}

.course-category.Secondary {
  background: linear-gradient(135deg, color-mix(in srgb, var(--success-color) 20%, var(--bg-primary)), color-mix(in srgb, var(--success-color) 40%, var(--bg-primary)));
  color: color-mix(in srgb, var(--success-color) 70%, black);
}

.course-category.Higher {
  background: linear-gradient(135deg, color-mix(in srgb, var(--text-muted) 20%, var(--bg-primary)), color-mix(in srgb, var(--text-muted) 40%, var(--bg-primary)));
  color: var(--text-primary);
}

.course-students {
  color: var(--text-muted);
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: color 0.3s ease;
}

.course-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  color: var(--text-muted);
  transition: color 0.3s ease;
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
  background: linear-gradient(135deg, color-mix(in srgb, var(--success-color) 20%, var(--bg-primary)), color-mix(in srgb, var(--success-color) 40%, var(--bg-primary)));
  color: color-mix(in srgb, var(--success-color) 70%, black);
}

.course-status.inactive {
  background: linear-gradient(135deg, color-mix(in srgb, var(--error-color) 20%, var(--bg-primary)), color-mix(in srgb, var(--error-color) 40%, var(--bg-primary)));
  color: color-mix(in srgb, var(--error-color) 70%, black);
}

.course-status.upcoming {
  background: linear-gradient(135deg, color-mix(in srgb, var(--warning-color) 20%, var(--bg-primary)), color-mix(in srgb, var(--warning-color) 40%, var(--bg-primary)));
  color: color-mix(in srgb, var(--warning-color) 70%, black);
}

/* ==================== */
/* ENHANCED PAGINATION SECTION */
/* ==================== */
.pagination-section {
  padding: 40px 0;
  border-top: 1px solid var(--border-color);
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
  transition: border-color 0.3s ease;
}

.pagination-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border: 2px solid var(--border-color);
  background: var(--card-bg);
  color: var(--text-primary);
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
  min-width: 100px;
}

.pagination-btn:hover:not(.disabled) {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
  transform: translateY(-2px);
}

.pagination-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: var(--bg-tertiary);
}

.pagination-numbers {
  display: flex;
  gap: 4px;
  align-items: center;
}

.pagination-number {
  padding: 12px 16px;
  border: 2px solid var(--border-color);
  background: var(--card-bg);
  color: var(--text-primary);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
  min-width: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-number:hover:not(.active) {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
  transform: translateY(-2px);
}

.pagination-number.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.pagination-ellipsis {
  padding: 12px 8px;
  color: var(--text-muted);
  font-weight: 600;
}

/* Page Jump Section */
.page-jump-section {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--text-secondary);
  font-size: 14px;
}

.page-jump-input {
  padding: 8px 12px;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--card-bg);
  color: var(--text-primary);
  width: 60px;
  text-align: center;
  outline: none;
}

.page-jump-input:focus {
  border-color: var(--primary-color);
}

.page-jump-btn {
  padding: 8px 16px;
  border: 1px solid var(--primary-color);
  background: var(--primary-color);
  color: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
}

.page-jump-btn:hover:not(:disabled) {
  background: var(--primary-hover);
  border-color: var(--primary-hover);
}

.page-jump-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* ==================== */
/* ACTION BUTTONS SECTION */
/* ==================== */
.action-buttons-section {
  padding: 50px 0 20px;
  border-top: 1px solid var(--border-color);
  margin-top: 30px;
  transition: border-color 0.3s ease;
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
  gap: 12px;
  padding: 16px 32px;
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
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
  color: white;
  box-shadow: 0 4px 15px color-mix(in srgb, var(--primary-color) 30%, transparent);
}

.btn-primary:hover {
  background: linear-gradient(135deg, var(--primary-hover), color-mix(in srgb, var(--primary-hover) 80%, black));
  transform: translateY(-3px);
  box-shadow: 0 8px 25px color-mix(in srgb, var(--primary-color) 40%, transparent);
}

.btn-secondary {
  background: linear-gradient(135deg, var(--text-muted), color-mix(in srgb, var(--text-muted) 80%, black));
  color: white;
  box-shadow: 0 4px 15px color-mix(in srgb, var(--text-muted) 30%, transparent);
}

.btn-secondary:hover {
  background: linear-gradient(135deg, color-mix(in srgb, var(--text-muted) 80%, black), color-mix(in srgb, var(--text-muted) 60%, black));
  transform: translateY(-3px);
  box-shadow: 0 8px 25px color-mix(in srgb, var(--text-muted) 40%, transparent);
}

/* ==================== */
/* EMPTY STATE */
/* ==================== */
.empty-container {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon {
  color: var(--text-muted);
  margin-bottom: 30px;
  opacity: 0.7;
  transition: color 0.3s ease;
}

.empty-icon i {
  font-size: 4rem;
}

.empty-container .title {
  color: var(--text-primary);
  margin-bottom: 16px;
}

.empty-container .text {
  color: var(--text-secondary);
  margin-bottom: 30px;
  font-size: 1.125rem;
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

.loading-container p {
  color: var(--text-secondary);
  font-size: 1.125rem;
  transition: color 0.3s ease;
}

.spinner {
  width: 60px;
  height: 60px;
  border: 4px solid var(--border-color);
  border-top: 4px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  transition: border-color 0.3s ease;
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
    margin-bottom: 0;
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
  
  .pagination-section {
    flex-direction: column;
    gap: 20px;
  }
  
  .pagination-container {
    order: 2;
  }
  
  .page-jump-section {
    order: 1;
  }
  
  .pagination-numbers {
    display: none; /* Hide page numbers on mobile, show only prev/next */
  }
  
  .pagination-btn {
    min-width: 120px;
  }
  
  .action-buttons {
    flex-direction: column;
    align-items: center;
    gap: 16px;
  }
  
  .btn {
    width: 100%;
    max-width: 300px;
    justify-content: center;
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
  
  .pagination-btn {
    padding: 10px 16px;
    min-width: 100px;
    font-size: 14px;
  }
  
  .page-jump-section {
    font-size: 12px;
  }
  
  .page-jump-input {
    padding: 6px 10px;
    width: 50px;
  }
  
  .page-jump-btn {
    padding: 6px 12px;
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
.pagination-btn:focus,
.pagination-number:focus,
.page-jump-input:focus,
.page-jump-btn:focus {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
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
  .pagination-btn,
  .pagination-number {
    animation: none;
    transition: none;
  }
  
  .course-card:hover,
  .btn:hover,
  .pagination-btn:hover,
  .pagination-number:hover {
    transform: none;
  }
  
  .course-card:hover .course-thumb img {
    transform: none;
  }
}

/* ==================== */
/* DARK THEME SPECIFIC ENHANCEMENTS */
/* ==================== */
.dark-theme .course-card {
  border-color: var(--border-color);
}

.dark-theme .search-input-group {
  background: var(--bg-secondary);
}

.dark-theme .filter-select {
  background: var(--bg-secondary);
}

.dark-theme .course-meta {
  border-bottom-color: var(--border-color);
}

.dark-theme .course-category:not(.Primary):not(.Junior):not(.Secondary):not(.Higher) {
  background: var(--bg-tertiary);
  color: var(--text-primary);
}

/* Ensure text remains readable in dark mode */
.dark-theme .course-students,
.dark-theme .course-details,
.dark-theme .course-grade,
.dark-theme .course-category-info {
  color: var(--text-secondary);
}

/* Improve contrast for disabled states in dark mode */
.dark-theme .pagination-btn.disabled {
  background: var(--bg-tertiary);
  color: var(--text-muted);
}

/* Enhanced focus states for better accessibility */
.dark-theme .search-input:focus,
.dark-theme .filter-select:focus {
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 40%, transparent);
}

/* ==================== */
/* LINE-CLAMP STANDARD PROPERTY */
/* ==================== */
@supports (line-clamp: 2) {
  .course-title,
  .course-description {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-clamp: 2;
    -webkit-box-orient: vertical;
  }
}

/* ==================== */
/* LANGUAGE SUPPORT */
/* ==================== */
.bn-lang .course-card,
.bn-lang .search-input,
.bn-lang .filter-select,
.bn-lang .btn {
  font-family: 'Kalpurush', 'SolaimanLipi', 'Siyam Rupali', Arial, sans-serif;
}

.bn-lang .course-title {
  line-height: 1.5;
}

.bn-lang .course-description {
  line-height: 1.7;
}

/* Ensure Font Awesome icons don't disappear */
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

/* Force icon rendering */
:deep(i[class*="fa-"]) {
  display: inline-block !important;
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
}
</style>