<template>
  <FrontendLayout>
    <main class="main-area fix">
      <!-- breadcrumb-area -->
      <section class="breadcrumb__area breadcrumb__bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
          <div class="container">
              <div class="row">
                  <div class="col-12">
                      <div class="breadcrumb__content">
                          <h3 class="title">{{ getCourseTitle(course) || t('Course Details') }}</h3>
                          <nav class="breadcrumb">
                              <span property="itemListElement" typeof="ListItem">
                                  <Link href="/">{{ t('Home') }}</Link>
                              </span>
                              <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                              <span property="itemListElement" typeof="ListItem">
                                  <Link href="/courses">{{ t('Courses') }}</Link>
                              </span>
                              <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                              <span property="itemListElement" typeof="ListItem">{{ getCourseTitle(course) || t('Course Details') }}</span>
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

      <!-- Loading State -->
      <div v-if="loading" class="loading-container text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">{{ t('Loading...') }}</span>
        </div>
        <p class="mt-3">{{ t('Loading course details...') }}</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-container text-center py-5">
        <div class="alert alert-danger mx-3">
          <h4>{{ t('Unable to Load Course') }}</h4>
          <p>{{ error }}</p>
          <button @click="fetchCourse" class="btn btn-primary mt-3">
            <i class="fas fa-refresh"></i> {{ t('Try Again') }}
          </button>
          <Link href="/courses" class="btn btn-secondary mt-3 ms-2">
            <i class="fas fa-arrow-left"></i> {{ t('Back to Courses') }}
          </Link>
        </div>
      </div>

      <!-- Course Details -->
      <section v-else-if="course" class="courses__details-area section-py-120">
        <div class="container">
          <div class="row g-5">
            <!-- Main Content -->
            <div class="col-xl-8 col-lg-7">
              <div class="courses__details-content">
                <!-- Course Header -->
                <div class="course-header mb-4">
                  <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <span class="badge bg-primary">{{ getCourseCategory(course) }}</span>
                    <div class="rating-badge d-flex align-items-center">
                      <i class="fas fa-star text-warning me-1"></i>
                      <span>4.5</span>
                      <span class="text-muted ms-1">(12 {{ t('reviews') }})</span>
                    </div>
                  </div>
                  <h1 class="course-title mb-3">{{ getCourseTitle(course) }}</h1>
                  <p class="course-description text-muted mb-4">{{ getCourseDescription(course) }}</p>
                  
                  <!-- Course Meta -->
                  <div class="course-meta d-flex flex-wrap gap-4 align-items-center">
                    <div class="instructor-info d-flex align-items-center">
                      <img :src="getInstructorAvatar(course.teacher)" :alt="getInstructorName(course.teacher)" class="instructor-avatar me-2">
                      <span>{{ t('By') }} <strong>{{ getInstructorName(course.teacher) }}</strong></span>
                    </div>
                    <div class="student-count">
                      <i class="fas fa-users text-muted me-1"></i>
                      {{ course.student_count || 0 }} {{ t('students') }}
                    </div>
                    <div class="last-updated">
                      <i class="fas fa-calendar text-muted me-1"></i>
                      {{ formatDate(course.updated_at || course.created_at) }}
                    </div>
                  </div>
                </div>

                <!-- Course Thumbnail -->
                <div class="courses__details-thumb mb-5">
                  <img :src="getCourseImage(course)" :alt="getCourseTitle(course)" class="w-100 rounded-3 shadow-sm">
                </div>

                <!-- Tabs Navigation -->
                <div class="course-tabs mb-4">
                  <ul class="nav nav-pills nav-fill" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" @click="activeTab = 'overview'" :class="{ active: activeTab === 'overview' }">
                        <i class="fas fa-info-circle me-2"></i>{{ t('Overview') }}
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" @click="activeTab = 'curriculum'" :class="{ active: activeTab === 'curriculum' }">
                        <i class="fas fa-book me-2"></i>{{ t('Curriculum') }}
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" @click="activeTab = 'subjects'" :class="{ active: activeTab === 'subjects' }">
                        <i class="fas fa-book-open me-2"></i>{{ t('Subjects') }}
                        <span v-if="otherSubjects.length > 0" class="badge bg-primary ms-2">{{ otherSubjects.length }}</span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" @click="activeTab = 'instructor'" :class="{ active: activeTab === 'instructor' }">
                        <i class="fas fa-user-tie me-2"></i>{{ t('Instructor') }}
                      </button>
                    </li>
                  </ul>
                </div>

                <!-- Tabs Content -->
                <div class="tab-content">
                  <!-- Overview Tab -->
                  <div v-if="activeTab === 'overview'" class="tab-pane fade show active">
                    <div class="card border-0 shadow-sm">
                      <div class="card-body p-4">
                        <h4 class="card-title mb-4">{{ t("What you'll learn") }}</h4>
                        <div class="learning-objectives">
                          <div class="row g-3">
                            <div class="col-md-6" v-for="(point, index) in getLearningPoints(course)" :key="index">
                              <div class="d-flex align-items-start">
                                <i class="fas fa-check text-success mt-1 me-3"></i>
                                <span class="learning-point">{{ point }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="mt-4">
                          <p class="text-muted">{{ getAdditionalInfo(course) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Curriculum Tab -->
                  <div v-if="activeTab === 'curriculum'" class="tab-pane fade show active">
                    <div class="card border-0 shadow-sm">
                      <div class="card-body p-4">
                        <h4 class="card-title mb-4">{{ t('Course Content') }}</h4>
                        <div class="accordion" id="curriculumAccordion">
                          <div class="accordion-item border-0 mb-3" v-for="(module, index) in getCourseModules(course)" :key="index">
                            <h3 class="accordion-header">
                              <button class="accordion-button collapsed bg-light fw-semibold" type="button" @click="toggleModule(index)">
                                <i class="fas fa-play-circle me-3 text-primary"></i>
                                {{ module.title }}
                                <span class="badge bg-secondary ms-auto me-3">{{ module.lessons.length }} {{ t('lessons') }}</span>
                              </button>
                            </h3>
                            <div v-show="openModule === index" class="accordion-collapse">
                              <div class="accordion-body p-0">
                                <div class="list-group list-group-flush">
                                  <a v-for="lesson in module.lessons" :key="lesson.id" 
                                     href="#" 
                                     class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center py-3"
                                     @click.prevent="handleLessonClick(lesson)">
                                    <div class="d-flex align-items-center">
                                      <i class="fas fa-play-circle text-muted me-3"></i>
                                      <span>{{ lesson.title }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                      <span class="text-muted small">{{ lesson.duration }}</span>
                                      <i v-if="!lesson.preview && !isEnrolled" class="fas fa-lock text-muted"></i>
                                      <i v-else-if="lesson.preview" class="fas fa-eye text-primary"></i>
                                    </div>
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Subjects Tab -->
                  <div v-if="activeTab === 'subjects'" class="tab-pane fade show active">
                    <div class="card border-0 shadow-sm">
                      <div class="card-body p-4">
                        <h4 class="card-title mb-4">
                          {{ t('Other Subjects in') }} {{ getClassName(course) }}
                          <small class="text-muted d-block mt-1">{{ t('Click any subject to view its details') }}</small>
                        </h4>
                        
                        <!-- Loading State for Subjects -->
                        <div v-if="loadingSubjects" class="text-center py-4">
                          <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ t('Loading...') }}</span>
                          </div>
                          <p class="mt-2">{{ t('Loading other subjects...') }}</p>
                        </div>

                        <!-- Subjects List -->
                        <div v-else class="subjects-list">
                          <div class="row g-4">
                            <div class="col-md-6 col-lg-4" v-for="subject in otherSubjects" :key="subject.id">
                              <div class="subject-card card border-0 shadow-sm h-100 cursor-pointer" 
                                   @click="navigateToSubject(subject)">
                                <div class="card-body text-center">
                                  <div class="subject-icon mb-3">
                                    <i :class="getSubjectIcon(subject.subject || subject.name)" class="text-primary fa-2x"></i>
                                  </div>
                                  <h5 class="subject-name mb-2">{{ subject.subject || subject.name }}</h5>
                                  <p class="subject-description text-muted small mb-3">
                                    {{ getSubjectDescription(subject.subject || subject.name) }}
                                  </p>
                                  <div class="subject-meta">
                                    <span class="badge bg-light text-dark me-2">
                                      <i class="fas fa-users me-1"></i>
                                      {{ subject.student_count || subject.studentCount || 0 }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                      <i class="fas fa-book me-1"></i>
                                      {{ subject.lesson_count || 12 }}
                                    </span>
                                  </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 text-center">
                                  <small class="text-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    {{ t('View Details') }}
                                  </small>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- No Subjects State -->
                        <div v-if="!loadingSubjects && otherSubjects.length === 0" class="text-center py-5">
                          <i class="fas fa-book fa-3x text-muted mb-3"></i>
                          <h5 class="text-muted">{{ t('No other subjects available') }}</h5>
                          <p class="text-muted">{{ t('This class currently has only this subject.') }}</p>
                          <Link href="/courses" class="btn btn-primary mt-3">
                            <i class="fas fa-search me-2"></i>
                            {{ t('Browse All Courses') }}
                          </Link>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Instructor Tab -->
                  <div v-if="activeTab === 'instructor'" class="tab-pane fade show active">
                    <div class="card border-0 shadow-sm">
                      <div class="card-body p-4">
                        <div class="row g-4">
                          <div class="col-md-3 text-center">
                            <img :src="getInstructorImage(course.teacher)" :alt="getInstructorName(course.teacher)" class="instructor-main-img rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                          </div>
                          <div class="col-md-9">
                            <h4 class="card-title">{{ getInstructorName(course.teacher) }}</h4>
                            <p class="text-muted mb-3">{{ getInstructorQualification(course.teacher) }}</p>
                            <div class="rating mb-3">
                              <i class="fas fa-star text-warning"></i>
                              <span class="ms-1 fw-semibold">{{ getInstructorRating(course.teacher) }}</span>
                              <span class="text-muted ms-1">{{ t('Instructor Rating') }}</span>
                            </div>
                            <p class="mb-4">{{ getInstructorBio(course.teacher) }}</p>
                            <div class="instructor-stats d-flex gap-4 mb-4">
                              <div class="stat-item text-center">
                                <div class="stat-number text-primary fw-bold">{{ getInstructorCoursesCount(course.teacher) }}</div>
                                <div class="stat-label text-muted small">{{ t('Courses') }}</div>
                              </div>
                              <div class="stat-item text-center">
                                <div class="stat-number text-primary fw-bold">{{ getInstructorStudentsCount(course.teacher) }}</div>
                                <div class="stat-label text-muted small">{{ t('Students') }}</div>
                              </div>
                              <div class="stat-item text-center">
                                <div class="stat-number text-primary fw-bold">{{ getInstructorExperience(course.teacher) }}</div>
                                <div class="stat-label text-muted small">{{ t('Years Experience') }}</div>
                              </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div v-if="getInstructorEmail(course.teacher)" class="instructor-contact mt-4">
                              <h6 class="mb-3">{{ t('Contact Information') }}</h6>
                              <div class="contact-item d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-muted me-3"></i>
                                <span>{{ getInstructorEmail(course.teacher) }}</span>
                              </div>
                              <div v-if="getInstructorInstitute(course.teacher)" class="contact-item d-flex align-items-center mb-2">
                                <i class="fas fa-university text-muted me-3"></i>
                                <span>{{ getInstructorInstitute(course.teacher) }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="col-xl-4 col-lg-5">
              <div class="courses__details-sidebar">
                <div class="sticky-sidebar">
                  <!-- Course Info Card -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                      <div class="preview-video position-relative">
                        <img :src="getPreviewImage(course)" :alt="getCourseTitle(course)" class="w-100 rounded-top">
                      </div>
                      <div class="p-4">
                        <div class="price-section text-center mb-3">
                          <span class="text-muted d-block mb-1">{{ t('Course Fee') }}</span>
                          <h3 class="text-primary mb-0">{{ t('Free') }}</h3>
                        </div>
                        <button class="btn btn-primary w-100 btn-lg mb-3" @click="enrollCourse" :disabled="isEnrolled || loadingEnroll">
                          <span v-if="loadingEnroll" class="spinner-border spinner-border-sm me-2"></span>
                          {{ isEnrolled ? t('Already Enrolled') : t('Enroll Now') }}
                        </button>
                        <div class="text-center">
                          <small class="text-muted">{{ t('Start learning today') }}</small>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Course Features -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                      <h6 class="card-title mb-3">{{ t('This course includes:') }}</h6>
                      <div class="course-features">
                        <div class="feature-item d-flex align-items-center mb-3" v-for="feature in getCourseFeatures(course)" :key="feature.text">
                          <i :class="feature.icon" class="text-primary me-3"></i>
                          <span>{{ feature.text }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Course Details -->
                  <div class="card border-0 shadow-sm">
                    <div class="card-body">
                      <h6 class="card-title mb-3">{{ t('Course Details') }}</h6>
                      <div class="course-details-list">
                        <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">{{ t('Level:') }}</span>
                          <span class="fw-semibold">{{ getCourseLevel(course) }}</span>
                        </div>
                        <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">{{ t('Duration:') }}</span>
                          <span class="fw-semibold">{{ getTotalDuration(course) }}</span>
                        </div>
                        <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">{{ t('Lessons:') }}</span>
                          <span class="fw-semibold">{{ getTotalLessons(course) }}</span>
                        </div>
                        <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">{{ t('Students:') }}</span>
                          <span class="fw-semibold">{{ course.student_count || 0 }}</span>
                        </div>
                        <div class="detail-item d-flex justify-content-between py-2">
                          <span class="text-muted">{{ t('Certificate:') }}</span>
                          <span class="fw-semibold text-success">{{ t('Yes') }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Instructor Quick Info -->
                  <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                      <h6 class="card-title mb-3">{{ t('About the Instructor') }}</h6>
                      <div class="instructor-quick-info">
                        <div class="d-flex align-items-center mb-3">
                          <img :src="getInstructorAvatar(course.teacher)" :alt="getInstructorName(course.teacher)" class="instructor-quick-avatar rounded-circle me-3">
                          <div>
                            <h6 class="mb-1">{{ getInstructorName(course.teacher) }}</h6>
                            <small class="text-muted">{{ getInstructorQualification(course.teacher) }}</small>
                          </div>
                        </div>
                        <div class="instructor-quick-stats">
                          <div class="row text-center">
                            <div class="col-4">
                              <div class="stat-number text-primary fw-bold">{{ getInstructorCoursesCount(course.teacher) }}</div>
                              <div class="stat-label text-muted small">{{ t('Courses') }}</div>
                            </div>
                            <div class="col-4">
                              <div class="stat-number text-primary fw-bold">{{ getInstructorStudentsCount(course.teacher) }}</div>
                              <div class="stat-label text-muted small">{{ t('Students') }}</div>
                            </div>
                            <div class="col-4">
                              <div class="stat-number text-primary fw-bold">{{ getInstructorRating(course.teacher) }}</div>
                              <div class="stat-label text-muted small">{{ t('Rating') }}</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Other Subjects Quick View -->
                  <div v-if="otherSubjects.length > 0" class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                      <h6 class="card-title mb-3">{{ t('Other Subjects') }}</h6>
                      <div class="quick-subjects">
                        <div v-for="subject in otherSubjects.slice(0, 3)" :key="subject.id" 
                             class="quick-subject-item d-flex align-items-center mb-2 cursor-pointer"
                             @click="navigateToSubject(subject)">
                          <i :class="getSubjectIcon(subject.subject || subject.name)" class="text-primary me-2 small"></i>
                          <span class="flex-grow-1">{{ subject.subject || subject.name }}</span>
                          <i class="fas fa-chevron-right text-muted small"></i>
                        </div>
                        <div v-if="otherSubjects.length > 3" class="text-center mt-2">
                          <small class="text-primary cursor-pointer" @click="activeTab = 'subjects'">
                            +{{ otherSubjects.length - 3 }} {{ t('more subjects') }}
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </FrontendLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useTranslation } from '@/composables/useTranslation';

// Define props FIRST before using them
const props = defineProps({
  course: {
    type: Object,
    default: null
  },
  relatedCourses: {
    type: Array,
    default: () => []
  },
  isEnrolled: {
    type: Boolean,
    default: false
  }
})

// Use the global translation composable
const { currentLanguage, t, switchLanguage } = useTranslation()

// Add icon render key to force re-render when language changes
const iconRenderKey = ref(0)

// Watch for language changes and force icon re-render
watch(currentLanguage, () => {
  iconRenderKey.value++
  nextTick(() => {
    // Force Font Awesome to re-render icons
    if (window.FontAwesome && window.FontAwesome.dom) {
      window.FontAwesome.dom.i2svg()
    }
  })
})

const page = usePage();
const loading = ref(false);
const loadingSubjects = ref(false);
const loadingEnroll = ref(false);
const error = ref(null);
const activeTab = ref('overview');
const openModule = ref(null);
const subjects = ref([]);
const isEnrolled = ref(props.isEnrolled);
const currentCourse = ref(props.course);
const currentTheme = ref('light');

// Get class name from course data
const getClassName = (course) => {
  if (!course) return t('This Class');
  
  if (course.type === 'regular') {
    return course.name || `Class ${course.grade || ''}`;
  } else {
    return course.name || t('This Course');
  }
};

// Get course title in "Class Name - Subject Name" format
const getCourseTitle = (course) => {
  if (!course) return t('Course Details');
  
  if (course.type === 'regular') {
    const className = course.name || `Class ${course.grade || ''}`;
    const subjectName = course.subject || 'General';
    return `${className} - ${subjectName}`;
  } else {
    return course.name || course.class_name || t('Untitled Course');
  }
};

// Get course description
const getCourseDescription = (course) => {
  if (course.description) {
    return course.description;
  }
  
  if (course.type === 'regular') {
    return t('Comprehensive curriculum for students. This course covers all essential subjects and prepares students for academic success.');
  } else {
    return t('Explore this course - learn essential skills and knowledge from expert instructors.');
  }
};

// ============ INSTRUCTOR METHODS ============

// Get instructor name with proper fallback
const getInstructorName = (teacher) => {
  if (teacher?.name) {
    return teacher.name;
  }
  return t('Expert Instructor');
};

// Get instructor qualification
const getInstructorQualification = (teacher) => {
  if (teacher?.education_qualification) {
    return teacher.education_qualification;
  }
  return t('Subject Expert');
};

// Get instructor rating
const getInstructorRating = (teacher) => {
  if (teacher?.rating) {
    return teacher.rating;
  }
  return '4.8';
};

// Get instructor bio
const getInstructorBio = (teacher) => {
  if (teacher?.bio) {
    return teacher.bio;
  }
  if (teacher?.experience) {
    return t('Experienced instructor with teaching experience. Committed to providing quality education and helping learners achieve their goals through engaging and effective teaching methods.');
  }
  return t('Experienced instructor with years of expertise in teaching and mentoring students. Committed to providing quality education and helping learners achieve their goals through engaging and effective teaching methods.');
};

// Get instructor courses count
const getInstructorCoursesCount = (teacher) => {
  if (teacher?.courses_count !== undefined) {
    return teacher.courses_count;
  }
  return 5;
};

// Get instructor students count
const getInstructorStudentsCount = (teacher) => {
  if (teacher?.students_count !== undefined) {
    return teacher.students_count;
  }
  return 250;
};

// Get instructor experience
const getInstructorExperience = (teacher) => {
  if (teacher?.experience) {
    // Extract years from experience string (e.g., "5+ years" -> "5")
    const match = teacher.experience.match(/(\d+)/);
    return match ? match[1] : '5';
  }
  return '5';
};

// Get instructor email
const getInstructorEmail = (teacher) => {
  return teacher?.email || null;
};

// Get instructor institute
const getInstructorInstitute = (teacher) => {
  return teacher?.institute || null;
};

// Get instructor avatar
const getInstructorAvatar = (teacher) => {
  if (teacher?.avatar) {
    return teacher.avatar;
  }
  return '/assets/img/instructors/default.jpg';
};

// Get instructor image
const getInstructorImage = (teacher) => {
  if (teacher?.avatar) {
    return teacher.avatar;
  }
  return '/assets/img/instructors/default.jpg';
};

// ============ COURSE METHODS ============

// Fetch other subjects for the same class
const fetchOtherSubjects = async () => {
  loadingSubjects.value = true;
  try {
    const courseId = currentCourse.value?.id;
    if (!courseId) {
      subjects.value = [];
      return;
    }

    console.log('🔍 Fetching other subjects for course:', courseId);
    
    const currentSubject = currentCourse.value.subject;
    const grade = currentCourse.value.grade;

    console.log('📚 Current course:', currentCourse.value);
    console.log('🎯 Current subject:', currentSubject);
    console.log('📊 Grade:', grade);

    // Try to fetch subjects by grade
    let otherSubjectsData = [];

    if (grade) {
      console.log('🔍 Trying to fetch subjects by grade:', grade);
      try {
        const gradeResponse = await fetch(`/api/courses/class/${grade}/subjects`);
        if (gradeResponse.ok) {
          const gradeData = await gradeResponse.json();
          if (gradeData.success && Array.isArray(gradeData.data)) {
            otherSubjectsData = gradeData.data.filter(subject => 
              subject.name !== currentSubject && subject.id !== parseInt(courseId)
            );
            console.log('✅ Subjects by grade found:', otherSubjectsData);
          }
        }
      } catch (gradeError) {
        console.log('❌ Grade-based fetch failed:', gradeError.message);
      }
    }

    // If no subjects found by grade, try general courses endpoint
    if (otherSubjectsData.length === 0) {
      console.log('🔍 Trying general courses endpoint');
      try {
        const coursesResponse = await fetch('/api/courses/classes');
        if (coursesResponse.ok) {
          const coursesData = await coursesResponse.json();
          if (coursesData.success && Array.isArray(coursesData.data)) {
            // Filter courses by same grade and type, excluding current subject
            otherSubjectsData = coursesData.data.filter(course => 
              course.grade === grade && 
              course.type === 'regular' && 
              course.subject !== currentSubject &&
              course.id !== parseInt(courseId)
            );
            console.log('✅ Subjects from general endpoint:', otherSubjectsData);
          }
        }
      } catch (generalError) {
        console.log('❌ General courses fetch failed:', generalError.message);
      }
    }

    subjects.value = otherSubjectsData;
    console.log('🎯 Final other subjects:', subjects.value);

  } catch (err) {
    console.error('❌ Error fetching other subjects:', err);
    subjects.value = [];
  } finally {
    loadingSubjects.value = false;
  }
};

// Computed property for other subjects
const otherSubjects = computed(() => {
  return subjects.value;
});

// Navigate to another subject
const navigateToSubject = (subject) => {
  console.log('🎯 Navigating to subject:', subject);
  router.visit(`/course/${subject.id}`);
};

const fetchCourse = async () => {
  if (props.course) {
    console.log('✅ Using course from props:', props.course);
    currentCourse.value = props.course;
    return;
  }

  loading.value = true;
  error.value = null;
  
  try {
    const path = window.location.pathname;
    const match = path.match(/\/course\/(\d+)/);
    const courseId = match ? match[1] : null;
    
    if (!courseId) {
      throw new Error('Course ID not found');
    }

    console.log('🔍 Fetching course:', courseId);

    const response = await fetch(`/api/courses/${courseId}`);
    if (!response.ok) {
      throw new Error('Failed to fetch course');
    }
    
    const data = await response.json();
    if (data.success) {
      currentCourse.value = data.data;
      console.log('✅ Course data received:', currentCourse.value);
    } else {
      throw new Error(data.message || 'Course not found');
    }
  } catch (err) {
    console.error('❌ Error fetching course:', err);
    error.value = err.message || 'Unable to load course details';
  } finally {
    loading.value = false;
  }
};

const toggleModule = (index) => {
  openModule.value = openModule.value === index ? null : index;
};

const enrollCourse = async () => {
  loadingEnroll.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 1000));
    isEnrolled.value = true;
    alert(t('Successfully enrolled in the course!'));
    
    const courseId = currentCourse.value?.id;
    if (courseId) {
      router.visit(`/learning/${courseId}`);
    }
  } catch (err) {
    console.error('Error enrolling:', err);
    alert(t('Failed to enroll. Please try again.'));
  } finally {
    loadingEnroll.value = false;
  }
};

// Helper methods
const getCourseImage = (course) => {
  return course?.thumbnail || '/assets/img/courses/h5_course_thumb01.jpg';
};

const getPreviewImage = (course) => {
  return course?.thumbnail || '/assets/img/courses/h5_course_thumb02.jpg';
};

const getCourseCategory = (course) => {
  if (!course) return t('Course');
  if (course.type === 'regular') {
    if (course.grade <= 5) return t('Primary');
    if (course.grade <= 8) return t('Junior');
    if (course.grade <= 10) return t('Secondary');
    return t('Higher Secondary');
  }
  return course.category || t('Skill Course');
};

const formatDate = (dateString) => {
  if (!dateString) return t('Recently');
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  } catch (error) {
    return t('Recently');
  }
};

const getLearningPoints = (course) => {
  return [
    t('Comprehensive understanding of core concepts'),
    t('Practical application of learned skills'),
    t('Interactive learning materials and resources'),
    t('Expert guidance and support'),
    t('Real-world projects and exercises'),
    t('Lifetime access to course materials')
  ];
};

const getAdditionalInfo = (course) => {
  return t('This comprehensive course is designed to provide you with practical skills and knowledge that you can apply immediately. Whether you\'re a beginner or looking to advance your skills, this course will help you achieve your learning goals.');
};

const getCourseModules = (course) => {
  return [
    {
      id: 1,
      title: 'Introduction & Basics',
      lessons: [
        { id: 1, title: 'Course Overview & Objectives', duration: '10:15', preview: true },
        { id: 2, title: 'Getting Started Guide', duration: '15:30', preview: true },
        { id: 3, title: 'Setting Up Your Environment', duration: '20:45', preview: false }
      ]
    },
    {
      id: 2,
      title: 'Core Concepts',
      lessons: [
        { id: 4, title: 'Fundamental Principles', duration: '25:20', preview: false },
        { id: 5, title: 'Advanced Techniques', duration: '30:15', preview: false },
        { id: 6, title: 'Practical Applications', duration: '35:40', preview: false }
      ]
    },
    {
      id: 3,
      title: 'Advanced Topics',
      lessons: [
        { id: 7, title: 'Expert Level Concepts', duration: '40:25', preview: false },
        { id: 8, title: 'Real-world Projects', duration: '50:10', preview: false },
        { id: 9, title: 'Final Assessment', duration: '15:00', preview: false }
      ]
    }
  ];
};

const getSubjectIcon = (subjectName) => {
  const icons = {
    'Mathematics': 'fas fa-calculator',
    'Science': 'fas fa-flask',
    'English': 'fas fa-language',
    'Bangla': 'fas fa-book',
    'Social Studies': 'fas fa-globe-asia',
    'Physics': 'fas fa-atom',
    'Chemistry': 'fas fa-flask',
    'Biology': 'fas fa-dna',
    'History': 'fas fa-monument',
    'Geography': 'fas fa-mountain',
    'Computer Science': 'fas fa-laptop-code',
    'default': 'fas fa-book'
  };
  return icons[subjectName] || icons.default;
};

const getSubjectDescription = (subjectName) => {
  const descriptions = {
    'Mathematics': t('Develop problem-solving skills and mathematical thinking'),
    'Science': t('Explore scientific concepts and experimental methods'),
    'English': t('Improve language proficiency and communication skills'),
    'Bangla': t('Master Bengali language and literature'),
    'Social Studies': t('Understand society, culture, and human interactions'),
    'default': t('Comprehensive curriculum designed to build strong foundational knowledge and practical skills.')
  };
  return descriptions[subjectName] || descriptions.default;
};

const handleLessonClick = (lesson) => {
  if (lesson.preview) {
    alert(`${t('Playing preview:')} ${lesson.title}`);
  } else if (!isEnrolled.value) {
    enrollCourse();
  } else {
    alert(`${t('Starting lesson:')} ${lesson.title}`);
  }
};

const getCourseFeatures = (course) => {
  return [
    { icon: 'fas fa-play-circle', text: t('45 on-demand videos') },
    { icon: 'fas fa-file-alt', text: t('Downloadable resources') },
    { icon: 'fas fa-infinity', text: t('Full lifetime access') },
    { icon: 'fas fa-mobile-alt', text: t('Access on mobile and desktop') },
    { icon: 'fas fa-trophy', text: t('Certificate of completion') }
  ];
};

const getCourseLevel = (course) => {
  if (!course) return t('All Levels');
  if (course.type === 'regular') {
    if (course.grade <= 5) return t('Beginner');
    if (course.grade <= 8) return t('Intermediate');
    return t('Advanced');
  }
  return t('All Levels');
};

const getTotalDuration = (course) => {
  return '11h 20m';
};

const getTotalLessons = (course) => {
  return '45';
};

onMounted(() => {
  const savedTheme = localStorage.getItem('preferredTheme')
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    currentTheme.value = savedTheme
  }
  
  // Listen for theme changes
  window.addEventListener('themeChanged', handleThemeChange);

  if (!props.course) {
    fetchCourse();
  }
  fetchOtherSubjects();
});

onUnmounted(() => {
  window.removeEventListener('themeChanged', handleThemeChange);
});

// Handle theme changes
const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme;
};
</script>

<!-- Add language-specific CSS -->
<style scoped>
/* ==================== */
/* LANGUAGE SUPPORT */
/* ==================== */
.bn-lang .course-header,
.bn-lang .card,
.bn-lang .btn,
.bn-lang .nav-link,
.bn-lang .list-group-item {
  font-family: 'Kalpurush', 'SolaimanLipi', 'Siyam Rupali', Arial, sans-serif;
}

.bn-lang .course-title {
  line-height: 1.5;
}

.bn-lang .course-description {
  line-height: 1.7;
}

.bn-lang .learning-point {
  line-height: 1.6;
}

/* Ensure Font Awesome icons are properly loaded and don't disappear */
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

/* Keep the rest of your existing CSS styles the same */
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
  padding: 50px 0 50px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
  color: var(--text-primary);
  background-color: var(--bg-secondary);
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
/* COURSE DETAILS AREA */
/* ==================== */
.courses__details-area {
  background: var(--bg-primary);
  padding: 80px 0;
  transition: background-color 0.3s ease;
}

/* ==================== */
/* COURSE HEADER */
/* ==================== */
.course-header {
  background: var(--bg-secondary);
  padding: 2rem;
  border-radius: 15px;
  margin-bottom: 2rem;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.course-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1.2;
  transition: color 0.3s ease;
}

.course-description {
  font-size: 1.1rem;
  line-height: 1.6;
  color: var(--text-secondary);
  transition: color 0.3s ease;
}

/* ==================== */
/* TABS */
/* ==================== */
.course-tabs .nav-pills .nav-link {
  padding: 1rem 1.5rem;
  font-weight: 600;
  color: var(--text-muted);
  border-radius: 10px;
  transition: all 0.3s ease;
  background: var(--bg-secondary);
  border: 1px solid var(--border-color);
}

.course-tabs .nav-pills .nav-link.active {
  background: var(--primary-color);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px color-mix(in srgb, var(--primary-color) 30%, transparent);
  border-color: var(--primary-color);
}

.course-tabs .nav-pills .nav-link:hover:not(.active) {
  background: var(--bg-tertiary);
  color: var(--text-primary);
}

/* ==================== */
/* CARDS */
/* ==================== */
.card {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg) !important;
}

.card-title {
  color: var(--text-primary);
  transition: color 0.3s ease;
}

/* ==================== */
/* ACCORDION */
/* ==================== */
.accordion-button {
  padding: 1.25rem;
  font-weight: 600;
  border: none;
  box-shadow: none;
  background: var(--bg-secondary);
  color: var(--text-primary);
  transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.accordion-button:hover {
  background: var(--bg-tertiary);
}

/* ==================== */
/* SUBJECT CARDS */
/* ==================== */
.subject-card {
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  cursor: pointer;
}

.subject-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-color);
}

.subject-name {
  font-weight: 600;
  color: var(--text-primary);
  transition: color 0.3s ease;
}

.subject-card:hover .subject-name {
  color: var(--primary-color);
}

.subject-description {
  color: var(--text-secondary);
  transition: color 0.3s ease;
}

/* ==================== */
/* BADGES */
/* ==================== */
.badge {
  font-size: 0.8rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
}

.badge.bg-primary {
  background: var(--primary-color) !important;
}

.badge.bg-secondary {
  background: var(--text-muted) !important;
}

.badge.bg-light {
  background: var(--bg-tertiary) !important;
  color: var(--text-primary) !important;
}

/* ==================== */
/* BUTTONS */
/* ==================== */
.btn-primary {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
  border: none;
  padding: 1rem 2rem;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px color-mix(in srgb, var(--primary-color) 40%, transparent);
  background: linear-gradient(135deg, var(--primary-hover), color-mix(in srgb, var(--primary-hover) 80%, black));
}

.btn-secondary {
  background: linear-gradient(135deg, var(--text-muted), color-mix(in srgb, var(--text-muted) 80%, black));
  border: none;
  color: white;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  background: linear-gradient(135deg, color-mix(in srgb, var(--text-muted) 80%, black), color-mix(in srgb, var(--text-muted) 60%, black));
  transform: translateY(-2px);
}

/* ==================== */
/* TEXT COLORS */
/* ==================== */
.text-primary {
  color: var(--primary-color) !important;
}

.text-muted {
  color: var(--text-muted) !important;
  transition: color 0.3s ease;
}

.text-success {
  color: var(--success-color) !important;
}

.text-warning {
  color: var(--warning-color) !important;
}

/* ==================== */
/* LOADING & ERROR STATES */
/* ==================== */
.loading-container, .error-container {
  min-height: 50vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

.error-container .alert {
  max-width: 600px;
  margin: 0 auto;
  background: var(--card-bg);
  border-color: var(--border-color);
  color: var(--text-primary);
}

/* ==================== */
/* SIDEBAR */
/* ==================== */
.sticky-sidebar {
  position: sticky;
  top: 100px;
}

.courses__details-sidebar .card {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
}

/* ==================== */
/* COURSE FEATURES */
/* ==================== */
.feature-item {
  padding: 0.5rem 0;
  color: var(--text-primary);
  transition: color 0.3s ease;
}

/* ==================== */
/* COURSE DETAILS LIST */
/* ==================== */
.course-details-list .detail-item {
  border-bottom-color: var(--border-color);
  transition: border-color 0.3s ease;
}

.course-details-list .detail-item:last-child {
  border-bottom: none;
}

/* ==================== */
/* INSTRUCTOR STYLES */
/* ==================== */
.instructor-quick-avatar {
  width: 40px;
  height: 40px;
  object-fit: cover;
}

.instructor-main-img {
  border: 4px solid var(--border-color);
  padding: 4px;
  transition: border-color 0.3s ease;
}

/* ==================== */
/* QUICK SUBJECTS */
/* ==================== */
.quick-subject-item {
  padding: 0.5rem;
  border-radius: 8px;
  transition: background-color 0.3s ease;
  color: var(--text-primary);
}

.quick-subject-item:hover {
  background: var(--bg-secondary);
}

/* ==================== */
/* LIST GROUP */
/* ==================== */
.list-group-item {
  border: none;
  padding: 1rem 1.25rem;
  transition: all 0.3s ease;
  background: var(--card-bg);
  color: var(--text-primary);
}

.list-group-item:hover {
  background: var(--bg-secondary);
  transform: translateX(5px);
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
.dark-theme .breadcrumb__area {
  background-color: var(--bg-secondary);
}

.dark-theme .course-header {
  background: var(--bg-secondary);
  border-color: var(--border-color);
}

.dark-theme .accordion-button {
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.dark-theme .subject-card .badge.bg-light {
  background: var(--bg-tertiary) !important;
  color: var(--text-primary) !important;
}

.dark-theme .quick-subject-item:hover {
  background: var(--bg-tertiary);
}

/* ==================== */
/* RESPONSIVE DESIGN */
/* ==================== */
@media (max-width: 768px) {
  .course-title {
    font-size: 2rem;
  }
  
  .course-header {
    padding: 1.5rem;
  }
  
  .course-tabs .nav-pills .nav-link {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
  }
  
  .sticky-sidebar {
    position: static;
  }
  
  .breadcrumb__content .title {
    font-size: 2rem;
  }
  
  .breadcrumb {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
}

@media (max-width: 480px) {
  .courses__details-area {
    padding: 40px 0;
  }
  
  .course-title {
    font-size: 1.75rem;
  }
  
  .course-tabs .nav-pills {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .course-tabs .nav-pills .nav-link {
    width: 100%;
  }
}

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.btn:focus,
.nav-link:focus,
.accordion-button:focus {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .card,
  .subject-card,
  .btn,
  .nav-link,
  .list-group-item {
    transition: none;
  }
  
  .card:hover,
  .subject-card:hover,
  .btn:hover:not(:disabled) {
    transform: none;
  }
  
  .alltuchtopdown {
    animation: none;
  }
}
</style>