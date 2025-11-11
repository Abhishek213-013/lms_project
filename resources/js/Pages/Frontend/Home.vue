<template>
  <FrontendLayout>
    <div class="home-page">
      <!-- Hero Section -->
      <section 
        class="hero-section p-0"
        :style="heroSectionStyle"
      >
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <div class="hero-content">
                <h1 class="hero-title md:w-[400px]">{{ displayContent.home_hero_title }}</h1>
                <p class="hero-subtitle">{{ displayContent.home_hero_subtitle }}</p>
                
                <div class="hero-actions">
                  <Link href="/courses" class="btn btn-hero-primary">
                    {{ displayContent.home_hero_primary_button }}
                  </Link>
                  <Link href="/instructors" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-play-circle icon-fixed"></i>
                    {{ displayContent.home_instructors_button }}
                  </Link>
                </div>
                
                <div class="hero-stats">
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_students.toLocaleString() }}+</span>
                    <span class="stat-label">{{ t('Students') }}</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_courses }}+</span>
                    <span class="stat-label">{{ t('Courses') }}</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-number">{{ stats.total_instructors }}+</span>
                    <span class="stat-label">{{ t('Instructors') }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="hero-image">
                <img 
                  :src="heroImageUrl" 
                  :alt="t('Online Learning')" 
                  class="img-fluid hero-main-image"
                  @error="handleHeroImageError"
                >
                <!-- Fallback image that's hidden by default -->
                <img 
                  src="/assets/img/h2_banner_img.png" 
                  :alt="t('Online Learning')" 
                  class="img-fluid hero-fallback-image"
                  ref="fallbackImage"
                >
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Popular Courses Section -->
      <section class="courses-section py-5">
        <div class="container">
          <div class="row mb-5">
            <div class="col-12 text-center">
              <h2 class="section-title">{{ displayContent.home_courses_title }}</h2>
              <p class="section-subtitle">{{ displayContent.home_courses_subtitle }}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-4" v-for="course in featuredCourses" :key="course.id">
              <div class="card course-card h-100">
                <img 
                  :src="getCourseThumbnail(course)" 
                  :alt="getCourseTitle(course)" 
                  class="card-img-top" 
                  style="height: 200px; object-fit: cover;"
                  @error="handleCourseImageError"
                  loading="lazy"
                />
                <div class="card-body">
                  <h5 class="card-title">{{ getCourseTitle(course) }}</h5>
                  <p class="card-text">{{ getCourseDescription(course) }}</p>
                  <div class="course-meta d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary">{{ getCourseCategoryText(course) }}</span>
                    <span class="course-type">{{ getCourseTypeText(course.type) }}</span>
                  </div>
                  <div class="course-info d-flex justify-content-between align-items-center">
                    <small class="students-count">
                      <i class="fas fa-users icon-fixed"></i> {{ course.student_count }} {{ t('students') }}
                    </small>
                    <strong class="course-price">৳{{ course.fee }}</strong>
                  </div>
                </div>
                <div class="card-footer">
                  <Link :href="`/course/${course.id}`" class="btn btn-primary w-100" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                    {{ displayContent.home_courses_button || t('View Course') }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-12 text-center">
              <Link href="/courses" class="btn btn-outline-primary btn-lg">
                {{ displayContent.home_courses_button || t('View All Courses') }}
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- Instructors Section -->
      <section class="instructors-section section-py-120" v-if="instructors.length > 0">
        <div class="container">
          <div class="row mb-5">
            <div class="col-12 text-center">
              <h2 class="section-title">{{ displayContent.home_instructors_title }}</h2>
              <p class="section-subtitle">{{ displayContent.home_instructors_subtitle }}</p>
            </div>
          </div>
          
          <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6" v-for="instructor in instructors.slice(0, 8)" :key="instructor.id">
              <div class="instructor-card-new">
                <div class="profile-image-container">
                  <img 
                    :src="getInstructorAvatar(instructor)" 
                    :alt="instructor.name"
                    :data-instructor-id="instructor.id"
                    class="profile-image-rectangular"
                    @error="handleImageError"
                    loading="lazy"
                  >
                </div>

                <div class="teacher-name-section">
                  <h3 class="teacher-name">{{ instructor.name }}</h3>
                </div>

                <div class="education-section">
                  <div class="section-header">
                    <i class="fas fa-graduation-cap icon-fixed"></i>
                    <span class="section-title-small">{{ t('Education') }}</span>
                  </div>
                  <p class="education-text line-clamp-2">{{ getEducation(instructor) }}</p>
                </div>

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

                <div class="view-profile-section">
                  <Link :href="`/instructor/${instructor.id}`" class="btn-view-profile">
                    <i class="fas fa-user-circle icon-fixed"></i>
                    {{ t('View Profile') }}
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-12 text-center">
              <Link href="/instructors" class="btn btn-outline-primary btn-lg">
                {{ displayContent.home_instructors_button || t('View All Instructors') }}
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Section -->
      <section class="stats-section py-5">
        <div class="container">
          <div class="row text-center">
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_students.toLocaleString() }}+</h3>
              <p class="stats-label">{{ t('Students Enrolled') }}</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_instructors }}+</h3>
              <p class="stats-label">{{ t('Expert Instructors') }}</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_courses }}+</h3>
              <p class="stats-label">{{ t('Courses Available') }}</p>
            </div>
            <div class="col-md-3 mb-4">
              <h3 class="stats-number">{{ stats.total_enrollments.toLocaleString() }}+</h3>
              <p class="stats-label">{{ t('Total Enrollments') }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="cta-section py-5">
        <div class="container">
          <div class="row text-center">
            <div class="col-12">
              <h2 class="cta-title">{{ displayContent.home_cta_title || t('Ready to Start Learning?') }}</h2>
              <p class="cta-subtitle">{{ displayContent.home_cta_subtitle || t('Join thousands of students already learning with Pathshala') }}</p>
              <Link href="/registration" class="btn btn-primary btn-lg">
                {{ displayContent.home_cta_button || t('Get Started Today') }}
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- Debug Section - Enhanced -->
      <!-- <div v-if="isDevelopment" class="debug-section p-3 bg-gray-100 mt-5">
        <div class="container">
          <h3>Content Debug Info:</h3>
          <p><strong>Current Language:</strong> {{ currentLanguage }}</p>
          <p><strong>Content Refresh Key:</strong> {{ contentRefreshKey }}</p>
          <p><strong>Translation Version:</strong> {{ translationVersion }}</p>
          
          <h4>Language Detection:</h4>
          <p><strong>URL Parameter:</strong> {{ $page.url }}</p>
          <p><strong>Has Bengali Content:</strong> {{ hasBengaliContent }}</p>
          
          <h4>Raw Content from Props:</h4>
          <pre>{{ JSON.stringify(props.content, null, 2) }}</pre>
          
          <h4>Content Keys Analysis:</h4>
          <div class="row">
            <div class="col-md-6">
              <h5>English Content Found:</h5>
              <ul>
                <li v-for="key in Object.keys(displayContent)" :key="key">
                  <strong>{{ key }}:</strong> "{{ displayContent[key] }}"
                  <span v-if="isEnglishText(displayContent[key])" class="badge bg-warning">EN</span>
                  <span v-else class="badge bg-success">BN</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div> -->
    </div>
  </FrontendLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { ref, onMounted, computed, onUnmounted, watch, nextTick } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

const { t, currentLanguage, translationVersion, switchLanguage } = useTranslation()

// Define props
const props = defineProps({
  content: {
    type: Object,
    default: () => ({})
  },
  featuredCourses: Array,
  instructors: Array,
  stats: Object,
  testimonials: Array
})

// Reactive data
const currentTheme = ref('light')
const heroImageLoaded = ref(false)
const heroImageError = ref(false)
const fallbackImage = ref(null)
const contentRefreshKey = ref(0)

// Check if we're in development mode safely
const isDevelopment = ref(false)
// Add these computed properties
const hasBengaliContent = computed(() => {
  const content = displayContent.value;
  const bengaliRegex = /[অ-ঔক-হ০-৯]/;
  return Object.values(content).some(value => 
    typeof value === 'string' && bengaliRegex.test(value)
  );
});

const isEnglishText = (text) => {
  if (typeof text !== 'string') return true;
  const bengaliRegex = /[অ-ঔক-হ০-৯]/;
  return !bengaliRegex.test(text);
};
// Computed properties
const displayContent = computed(() => {
  contentRefreshKey.value; // This makes the computed property reactive to language changes
  
  // Use props.content if available, otherwise use default content
  const content = Object.keys(props.content).length > 0 ? props.content : getDefaultContent()
  
  console.log('🔄 Display Content:', {
    hasPropsContent: Object.keys(props.content).length > 0,
    propsContentKeys: Object.keys(props.content),
    finalContentKeys: Object.keys(content)
  })
  
  return content
})

const heroImageUrl = computed(() => {
  // Get hero image from content management, fallback to default
  const customImage = props.content?.home_hero_image;
  
  if (customImage && customImage !== '/assets/img/h2_banner_img.png') {
    console.log('Using custom hero image:', customImage);
    return customImage;
  }
  
  console.log('Using default hero image');
  return '/assets/img/h2_banner_img.png';
})

const heroSectionStyle = computed(() => {
  const isDark = currentTheme.value === 'dark'
  const lightBg = 'linear-gradient(135deg, rgba(245, 247, 250, 0.9) 0%, rgba(228, 232, 240, 0.9) 100%), url(\'/assets/img/banner/banner_bg02.png\') center/cover no-repeat'
  const darkBg = 'linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%), url(\'/assets/img/banner/banner_bg02.png\') center/cover no-repeat'
  
  return {
    background: isDark ? darkBg : lightBg
  }
})

// Methods
const getDefaultContent = () => {
  return {
    home_hero_title: 'Learning is What You Make of it. Make it Yours at Pathshala LMS.',
    home_hero_subtitle: 'Unlock your potential with our expert-led courses and transform your career.',
    home_hero_primary_button: 'Browse Courses',
    home_hero_secondary_button: 'Watch Demo',
    home_courses_title: 'Popular Courses',
    home_courses_subtitle: 'Discover our most enrolled courses',
    home_courses_button: 'View All Courses',
    home_instructors_title: 'Featured Instructors',
    home_instructors_subtitle: 'Learn from experienced professionals',
    home_instructors_button: 'View All Instructors',
    home_stats_title: 'Our Impact',
    home_cta_title: 'Ready to Start Learning?',
    home_cta_subtitle: 'Join thousands of students already learning with Pathshala',
    home_cta_button: 'Get Started Today',
  }
}

const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme
}

const handleLanguageChange = (event) => {
  console.log('Language change received in home page:', event.detail.language)
  refreshIcons()
  // Force content refresh
  contentRefreshKey.value++
  
  // Force a re-render of all dynamic content
  nextTick(() => {
    if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
      window.FontAwesome.dom.i2svg()
    }
  })
}

const refreshIcons = () => {
  if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
    setTimeout(() => {
      window.FontAwesome.dom.i2svg()
    }, 100)
  }
}

const getInstructorAvatar = (instructor) => {
  // First priority: Use profile_picture from database if available
  if (instructor.profile_picture && instructor.profile_picture !== 'null' && instructor.profile_picture !== 'NULL') {
    const profilePicUrl = formatInstructorImageUrl(instructor.profile_picture);
    return profilePicUrl;
  }

  // Second priority: Use avatar field if available
  if (instructor.avatar && instructor.avatar !== '/assets/img/instructors/default.jpg') {
    return instructor.avatar;
  }

  // Fallback to default avatars only if no database image exists
  const avatars = [
    '/assets/img/instructor/instructor01.png',
    '/assets/img/instructor/instructor02.png',
    '/assets/img/instructor/instructor03.png',
    '/assets/img/instructor/instructor04.png'
  ];
  
  const avatarIndex = ((instructor.id || 1) - 1) % avatars.length;
  return avatars[avatarIndex];
};

const formatInstructorImageUrl = (imagePath) => {
  if (!imagePath) return null;
  
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
  if (imagePath.startsWith('instructors/') || imagePath.startsWith('profiles/')) {
    return `/storage/${imagePath}`;
  }
  
  // Default case - prepend /storage/
  return `/storage/${imagePath}`;
};

const handleImageError = (event) => {
  const instructorId = event.target.getAttribute('data-instructor-id');
  console.warn(`❌ Failed to load profile picture for instructor ${instructorId}`);
  
  // Try different fallback strategies
  const fallbacks = [
    '/assets/img/instructor/instructor01.png',
    '/assets/img/instructor/instructor02.png',
    '/assets/img/instructor/instructor03.png',
    '/assets/img/instructor/instructor04.png'
  ];
  
  let currentFallbackIndex = 0;
  
  const tryNextFallback = () => {
    if (currentFallbackIndex < fallbacks.length) {
      event.target.src = fallbacks[currentFallbackIndex];
      currentFallbackIndex++;
    } else {
      // All fallbacks failed, use a data URL placeholder
      event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y3ZmFmYyIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5Y2EwYTYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjM1ZW0iPuKKojwvdGV4dD48L3N2Zz4=';
      event.target.onerror = null; // Prevent infinite loop
    }
  };
  
  event.target.onerror = tryNextFallback;
  tryNextFallback();
};

const handleHeroImageError = (event) => {
  console.error('Hero image failed to load:', event.target.src);
  heroImageError.value = true;
  
  // Show fallback image
  if (fallbackImage.value) {
    const mainImage = event.target;
    const fallback = fallbackImage.value;
    
    // Hide main image and show fallback
    mainImage.style.display = 'none';
    fallback.style.display = 'block';
  }
}

const preloadHeroImage = () => {
  const img = new Image();
  img.src = heroImageUrl.value;
  
  img.onload = () => {
    console.log('Hero image loaded successfully:', heroImageUrl.value);
    heroImageLoaded.value = true;
    heroImageError.value = false;
  };
  
  img.onerror = () => {
    console.error('Failed to preload hero image:', heroImageUrl.value);
    heroImageError.value = true;
    heroImageLoaded.value = false;
  };
}

const getCourseThumbnail = (course) => {
  // Use the image data from backend
  if (course.image_url && course.image_url !== 'null' && course.image_url !== 'NULL') {
    return course.image_url;
  }

  if (course.thumbnail_url && course.thumbnail_url !== 'null' && course.thumbnail_url !== 'NULL') {
    return course.thumbnail_url;
  }

  if (course.image && course.image !== 'null' && course.image !== 'NULL') {
    const imageUrl = formatImageUrl(course.image);
    return imageUrl;
  }

  if (course.thumbnail && course.thumbnail !== 'null' && course.thumbnail !== 'NULL') {
    const thumbnailUrl = formatImageUrl(course.thumbnail);
    return thumbnailUrl;
  }

  // Fallback to demo thumbnails only if no database image exists
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

const handleCourseImageError = (event) => {
  const img = event.target;
  
  console.warn('❌ Course image failed to load:', img.src);
  
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

// Methods for course data
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

const getCourseCategoryText = (course) => {
  const category = getCourseCategory(course);
  return t(category);
};

const getCourseTypeText = (courseType) => {
  return courseType === 'regular' ? t('Class') : t('Course');
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

const getEducation = (instructor) => {
  if (instructor.education_qualification && instructor.education_qualification !== 'Not specified') {
    return instructor.education_qualification;
  }
  return t('Teaching Degree');
}

// Watch for content changes to reload hero image
watch(() => props.content?.home_hero_image, (newImageUrl, oldImageUrl) => {
  if (newImageUrl !== oldImageUrl) {
    console.log('Hero image URL changed:', newImageUrl);
    heroImageLoaded.value = false;
    heroImageError.value = false;
    preloadHeroImage();
  }
})

// Watch for language changes
watch(currentLanguage, (newLang, oldLang) => {
  console.log('Language changed from', oldLang, 'to', newLang);
  console.log('Current content props:', props.content);
  refreshIcons();
  contentRefreshKey.value++; // Force content refresh
  
  setTimeout(() => {
    if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
      window.FontAwesome.dom.i2svg();
    }
  }, 200);
});

// Watch translation version for reactivity
watch(translationVersion, () => {
  console.log('Translation version changed, refreshing content');
  contentRefreshKey.value++; // Force content refresh
});

// Watch for props.content changes
watch(() => props.content, (newContent, oldContent) => {
  if (newContent !== oldContent) {
    console.log('Content props updated:', newContent);
    contentRefreshKey.value++;
  }
}, { deep: true });

// Lifecycle hooks
onMounted(() => {
  // Check if we're in development mode
  isDevelopment.value = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
  
  // Get initial theme
  const savedTheme = localStorage.getItem('preferredTheme')
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  
  currentTheme.value = savedTheme || (systemPrefersDark ? 'dark' : 'light')
  
  // Preload hero image
  preloadHeroImage();
  
  // Log the received content for debugging
  console.log('🏠 Home Page Mounted - Content Received:', props.content);
  console.log('🏠 Home Page Mounted - Language:', currentLanguage.value);
  console.log('🏠 Home Page Mounted - Display Content:', displayContent.value);
  
  // Listen for theme changes
  window.addEventListener('themeChanged', handleThemeChange)
  
  // Listen for language changes from header
  window.addEventListener('languageChanged', handleLanguageChange)
  
  // Listen for content refresh events
  window.addEventListener('contentRefresh', refreshIcons)
  
  // Initialize icons
  refreshIcons()
})

onUnmounted(() => {
  window.removeEventListener('themeChanged', handleThemeChange)
  window.removeEventListener('languageChanged', handleLanguageChange)
  window.removeEventListener('contentRefresh', refreshIcons)
})
</script>

<style scoped>
/* Your existing CSS styles remain exactly the same */
/* ... all the CSS from your original file ... */

/* Ensure View Course button uses primary color */
.course-card .btn-primary {
  background-color: var(--primary-color) !important;
  border-color: var(--primary-color) !important;
  color: white !important;
}

.course-card .btn-primary:hover {
  background-color: var(--primary-hover) !important;
  border-color: var(--primary-hover) !important;
  color: white !important;
}

/* ==================== */
/* HERO IMAGE STYLES */
/* ==================== */
.hero-image {
  position: relative;
  text-align: center;
  animation: float 3s ease-in-out infinite;
}

.hero-main-image {
  max-width: 100%;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
  transition: opacity 0.3s ease;
}

.hero-fallback-image {
  max-width: 100%;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
  display: none; /* Hidden by default */
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.hero-image img {
  max-width: 100%;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

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
/* NEW INSTRUCTOR CARD DESIGN */
/* ==================== */
.instructors-section {
  position: relative;
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

.section-py-120 {
  padding: 120px 0;
}

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
/* ACCESSIBILITY */
/* ==================== */
.btn-view-profile:focus {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .instructor-card-new,
  .btn-view-profile,
  .profile-image-rectangular {
    transition: none;
  }
  
  .instructor-card-new:hover,
  .btn-view-profile:hover {
    transform: none;
  }
}

/* ==================== */
/* EXISTING STYLES FOR OTHER SECTIONS */
/* ==================== */
.hero-section {
  padding: 120px 0 80px;
  position: relative;
  overflow: hidden;
  min-height: 80vh;
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, rgba(245, 247, 250, 0.9) 0%, rgba(228, 232, 240, 0.9) 100%), url('/assets/img/banner/banner_bg02.png') center/cover no-repeat;
}

.hero-content {
  max-width: 600px;
}

.hero-title {
  font-size: 2rem;
  font-weight: 400;
  line-height: 1.2;
  margin-bottom: 1rem;
  color: var(--text-primary);
}

.hero-subtitle {
  font-size: 1.25rem;
  color: var(--text-secondary);
  margin-bottom: 2.5rem;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 3rem;
}

.btn-hero-primary {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  color: white;
  padding: 0.75rem 2rem;
  font-weight: 600;
  border-radius: 50px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-hero-primary:hover {
  background-color: var(--primary-hover);
  border-color: var(--primary-hover);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
}

.btn-hero-secondary {
  background-color: transparent;
  color: var(--primary-color);
  border: 2px solid var(--primary-color);
  padding: 0.75rem 2rem;
  font-weight: 600;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-hero-secondary:hover {
  background-color: var(--primary-color);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
}

.hero-stats {
  display: flex;
  gap: 2rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
}

.stat-label {
  font-size: 0.9rem;
  color: var(--text-secondary);
}

/* Section Titles */
.section-title {
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.section-subtitle {
  color: var(--text-secondary);
  font-size: 1.1rem;
}

/* Course Cards */
.courses-section {
  background: var(--bg-secondary);
}

.course-card {
  transition: transform 0.3s ease;
  border: none;
  box-shadow: var(--shadow);
  background: var(--card-bg);
  border: 1px solid var(--border-color);
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.card-title {
  color: var(--text-primary);
  font-weight: 600;
}

.card-text {
  color: var(--text-secondary);
}

.course-meta .badge {
  background: var(--primary-color);
}

.course-type {
  color: var(--text-muted);
}

.students-count {
  color: var(--text-muted);
}

.course-price {
  font-family: Arial, sans-serif; /* Ensures Taka symbol displays properly */
  color: var(--primary-color);
}

.card-footer {
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-color);
}

/* Stats Section */
.stats-section {
  background: var(--primary-color);
  color: white;
}

.stats-label {
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
}

/* CTA Section */
.cta-section {
  background: var(--bg-primary);
}

.cta-title {
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.cta-subtitle {
  color: var(--text-secondary);
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

/* Buttons */
.btn-outline-primary {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.btn-outline-primary:hover {
  background: var(--primary-color);
  color: white;
}

/* Dark theme overrides */
.dark-theme .hero-section {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%), url('/assets/img/banner/banner_bg02.png') center/cover no-repeat !important;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-section {
    padding: 80px 0 60px;
    min-height: auto;
    text-align: center;
  }
  
  .hero-title {
    font-size: 1.5rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .hero-stats {
    justify-content: center;
  }
  
  .stat-number {
    font-size: 1.75rem;
  }
  
  .hero-image {
    margin-top: 3rem;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  .btn-hero-primary,
  .btn-hero-secondary {
    padding: 0.6rem 1.5rem;
    font-size: 0.9rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 1rem;
  }
}

/* Debug Section Styles */
.debug-section {
  border: 2px solid #e53e3e;
  border-radius: 8px;
  font-family: monospace;
}

.debug-section pre {
  background: #f7fafc;
  padding: 10px;
  border-radius: 4px;
  overflow-x: auto;
  font-size: 12px;
}
</style>