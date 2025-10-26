<template>
  <FrontendLayout>
    <main class="main-area fix">
      <!-- breadcrumb-area -->
      <section class="breadcrumb__area breadcrumb__bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
          <div class="container">
              <div class="row">
                  <div class="col-12">
                      <div class="breadcrumb__content">
                          <h3 class="title">{{ getCourseTitle(course) || 'Course Details' }}</h3>
                          <nav class="breadcrumb">
                              <span property="itemListElement" typeof="ListItem">
                                  <Link href="/">Home</Link>
                              </span>
                              <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                              <span property="itemListElement" typeof="ListItem">
                                  <Link href="/courses">Courses</Link>
                              </span>
                              <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                              <span property="itemListElement" typeof="ListItem">{{ getCourseTitle(course) || 'Course Details' }}</span>
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
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Loading course details...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-container text-center py-5">
        <div class="alert alert-danger mx-3">
          <h4>Unable to Load Course</h4>
          <p>{{ error }}</p>
          <button @click="fetchCourse" class="btn btn-primary mt-3">
            <i class="fas fa-refresh"></i> Try Again
          </button>
          <Link href="/courses" class="btn btn-secondary mt-3 ms-2">
            <i class="fas fa-arrow-left"></i> Back to Courses
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
                      <span class="text-muted ms-1">(12 reviews)</span>
                    </div>
                  </div>
                  <h1 class="course-title mb-3">{{ getCourseTitle(course) }}</h1>
                  <p class="course-description text-muted mb-4">{{ getCourseDescription(course) }}</p>
                  
                  <!-- Course Meta -->
                  <div class="course-meta d-flex flex-wrap gap-4 align-items-center">
                    <div class="instructor-info d-flex align-items-center">
                      <img :src="getInstructorAvatar(course.teacher)" :alt="getInstructorName(course.teacher)" class="instructor-avatar me-2">
                      <span>By <strong>{{ getInstructorName(course.teacher) }}</strong></span>
                    </div>
                    <div class="student-count">
                      <i class="fas fa-users text-muted me-1"></i>
                      {{ course.student_count || 0 }} students
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
                        <i class="fas fa-info-circle me-2"></i>Overview
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" @click="activeTab = 'curriculum'" :class="{ active: activeTab === 'curriculum' }">
                        <i class="fas fa-book me-2"></i>Curriculum
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" @click="activeTab = 'subjects'" :class="{ active: activeTab === 'subjects' }">
                        <i class="fas fa-book-open me-2"></i>Subjects
                        <span v-if="otherSubjects.length > 0" class="badge bg-primary ms-2">{{ otherSubjects.length }}</span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" @click="activeTab = 'instructor'" :class="{ active: activeTab === 'instructor' }">
                        <i class="fas fa-user-tie me-2"></i>Instructor
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
                        <h4 class="card-title mb-4">What you'll learn</h4>
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
                        <h4 class="card-title mb-4">Course Content</h4>
                        <div class="accordion" id="curriculumAccordion">
                          <div class="accordion-item border-0 mb-3" v-for="(module, index) in getCourseModules(course)" :key="index">
                            <h3 class="accordion-header">
                              <button class="accordion-button collapsed bg-light fw-semibold" type="button" @click="toggleModule(index)">
                                <i class="fas fa-play-circle me-3 text-primary"></i>
                                {{ module.title }}
                                <span class="badge bg-secondary ms-auto me-3">{{ module.lessons.length }} lessons</span>
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
                          Other Subjects in {{ getClassName(course) }}
                          <small class="text-muted d-block mt-1">Click any subject to view its details</small>
                        </h4>
                        
                        <!-- Loading State for Subjects -->
                        <div v-if="loadingSubjects" class="text-center py-4">
                          <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading subjects...</span>
                          </div>
                          <p class="mt-2">Loading other subjects...</p>
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
                                    View Details
                                  </small>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- No Subjects State -->
                        <div v-if="!loadingSubjects && otherSubjects.length === 0" class="text-center py-5">
                          <i class="fas fa-book fa-3x text-muted mb-3"></i>
                          <h5 class="text-muted">No other subjects available</h5>
                          <p class="text-muted">This class currently has only this subject.</p>
                          <Link href="/courses" class="btn btn-primary mt-3">
                            <i class="fas fa-search me-2"></i>
                            Browse All Courses
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
                              <span class="text-muted ms-1">Instructor Rating</span>
                            </div>
                            <p class="mb-4">{{ getInstructorBio(course.teacher) }}</p>
                            <div class="instructor-stats d-flex gap-4 mb-4">
                              <div class="stat-item text-center">
                                <div class="stat-number text-primary fw-bold">{{ getInstructorCoursesCount(course.teacher) }}</div>
                                <div class="stat-label text-muted small">Courses</div>
                              </div>
                              <div class="stat-item text-center">
                                <div class="stat-number text-primary fw-bold">{{ getInstructorStudentsCount(course.teacher) }}</div>
                                <div class="stat-label text-muted small">Students</div>
                              </div>
                              <div class="stat-item text-center">
                                <div class="stat-number text-primary fw-bold">{{ getInstructorExperience(course.teacher) }}</div>
                                <div class="stat-label text-muted small">Years Experience</div>
                              </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div v-if="getInstructorEmail(course.teacher)" class="instructor-contact mt-4">
                              <h6 class="mb-3">Contact Information</h6>
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
                          <span class="text-muted d-block mb-1">Course Fee</span>
                          <h3 class="text-primary mb-0">Free</h3>
                        </div>
                        <button class="btn btn-primary w-100 btn-lg mb-3" @click="enrollCourse" :disabled="isEnrolled || loadingEnroll">
                          <span v-if="loadingEnroll" class="spinner-border spinner-border-sm me-2"></span>
                          {{ isEnrolled ? 'Already Enrolled' : 'Enroll Now' }}
                        </button>
                        <div class="text-center">
                          <small class="text-muted">Start learning today</small>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Course Features -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                      <h6 class="card-title mb-3">This course includes:</h6>
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
                      <h6 class="card-title mb-3">Course Details</h6>
                      <div class="course-details-list">
                        <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">Level:</span>
                          <span class="fw-semibold">{{ getCourseLevel(course) }}</span>
                        </div>
                        <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">Duration:</span>
                          <span class="fw-semibold">{{ getTotalDuration(course) }}</span>
                        </div>
                        <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">Lessons:</span>
                          <span class="fw-semibold">{{ getTotalLessons(course) }}</span>
                        </div>
                        <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                          <span class="text-muted">Students:</span>
                          <span class="fw-semibold">{{ course.student_count || 0 }}</span>
                        </div>
                        <div class="detail-item d-flex justify-content-between py-2">
                          <span class="text-muted">Certificate:</span>
                          <span class="fw-semibold text-success">Yes</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Instructor Quick Info -->
                  <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                      <h6 class="card-title mb-3">About the Instructor</h6>
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
                              <div class="stat-label text-muted small">Courses</div>
                            </div>
                            <div class="col-4">
                              <div class="stat-number text-primary fw-bold">{{ getInstructorStudentsCount(course.teacher) }}</div>
                              <div class="stat-label text-muted small">Students</div>
                            </div>
                            <div class="col-4">
                              <div class="stat-number text-primary fw-bold">{{ getInstructorRating(course.teacher) }}</div>
                              <div class="stat-label text-muted small">Rating</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Other Subjects Quick View -->
                  <div v-if="otherSubjects.length > 0" class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                      <h6 class="card-title mb-3">Other Subjects</h6>
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
                            +{{ otherSubjects.length - 3 }} more subjects
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


<script>

import { Link } from '@inertiajs/vue3'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

export default {
  name: 'CourseSingle',
  components: {
    FrontendLayout,
    Link
  },
  props: {
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
  },
  setup(props) {
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

    // Get class name from course data
    const getClassName = (course) => {
      if (!course) return 'This Class';
      
      if (course.type === 'regular') {
        return course.name || `Class ${course.grade || ''}`;
      } else {
        return course.name || 'This Course';
      }
    };

    // Get course title in "Class Name - Subject Name" format
    const getCourseTitle = (course) => {
      if (!course) return 'Course Details';
      
      if (course.type === 'regular') {
        const className = course.name || `Class ${course.grade || ''}`;
        const subjectName = course.subject || 'General';
        return `${className} - ${subjectName}`;
      } else {
        return course.name || course.class_name || 'Untitled Course';
      }
    };

    // Get course description
    const getCourseDescription = (course) => {
      if (course.description) {
        return course.description;
      }
      
      if (course.type === 'regular') {
        return `Comprehensive ${course.subject || 'subject'} curriculum for ${course.name || `Class ${course.grade}`} students. This course covers all essential subjects and prepares students for academic success.`;
      } else {
        return `Explore ${course.name || course.class_name || 'this course'} - ${course.category || 'Specialized course'}. Learn essential skills and knowledge from expert instructors.`;
      }
    };

    // ============ INSTRUCTOR METHODS ============

    // Get instructor name with proper fallback
    const getInstructorName = (teacher) => {
      if (teacher?.name) {
        return teacher.name;
      }
      return 'Expert Instructor';
    };

    // Get instructor qualification
    const getInstructorQualification = (teacher) => {
      if (teacher?.education_qualification) {
        return teacher.education_qualification;
      }
      return 'Subject Expert';
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
        return `Experienced instructor with ${teacher.experience} of teaching experience. Committed to providing quality education and helping learners achieve their goals through engaging and effective teaching methods.`;
      }
      return 'Experienced instructor with years of expertise in teaching and mentoring students. Committed to providing quality education and helping learners achieve their goals through engaging and effective teaching methods.';
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
        alert('Successfully enrolled in the course!');
        
        const courseId = currentCourse.value?.id;
        if (courseId) {
          router.visit(`/learning/${courseId}`);
        }
      } catch (err) {
        console.error('Error enrolling:', err);
        alert('Failed to enroll. Please try again.');
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
      if (!course) return 'Course';
      if (course.type === 'regular') {
        if (course.grade <= 5) return 'Primary';
        if (course.grade <= 8) return 'Junior';
        if (course.grade <= 10) return 'Secondary';
        return 'Higher Secondary';
      }
      return course.category || 'Skill Course';
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'Recently';
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        });
      } catch (error) {
        return 'Recently';
      }
    };

    const getLearningPoints = (course) => {
      return [
        'Comprehensive understanding of core concepts',
        'Practical application of learned skills',
        'Interactive learning materials and resources',
        'Expert guidance and support',
        'Real-world projects and exercises',
        'Lifetime access to course materials'
      ];
    };

    const getAdditionalInfo = (course) => {
      return `This comprehensive course is designed to provide you with practical skills and knowledge that you can apply immediately. Whether you're a beginner or looking to advance your skills, this course will help you achieve your learning goals.`;
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
        'Mathematics': 'Develop problem-solving skills and mathematical thinking',
        'Science': 'Explore scientific concepts and experimental methods',
        'English': 'Improve language proficiency and communication skills',
        'Bangla': 'Master Bengali language and literature',
        'Social Studies': 'Understand society, culture, and human interactions',
        'default': `Comprehensive ${subjectName} curriculum designed to build strong foundational knowledge and practical skills.`
      };
      return descriptions[subjectName] || descriptions.default;
    };

    const handleLessonClick = (lesson) => {
      if (lesson.preview) {
        alert(`Playing preview: ${lesson.title}`);
      } else if (!isEnrolled.value) {
        enrollCourse();
      } else {
        alert(`Starting lesson: ${lesson.title}`);
      }
    };

    const getCourseFeatures = (course) => {
      return [
        { icon: 'fas fa-play-circle', text: '45 on-demand videos' },
        { icon: 'fas fa-file-alt', text: 'Downloadable resources' },
        { icon: 'fas fa-infinity', text: 'Full lifetime access' },
        { icon: 'fas fa-mobile-alt', text: 'Access on mobile and desktop' },
        { icon: 'fas fa-trophy', text: 'Certificate of completion' }
      ];
    };

    const getCourseLevel = (course) => {
      if (!course) return 'All Levels';
      if (course.type === 'regular') {
        if (course.grade <= 5) return 'Beginner';
        if (course.grade <= 8) return 'Intermediate';
        return 'Advanced';
      }
      return 'All Levels';
    };

    const getTotalDuration = (course) => {
      return '11h 20m';
    };

    const getTotalLessons = (course) => {
      return '45';
    };

    // Initialize on mount
    onMounted(async () => {
      if (!props.course) {
        await fetchCourse();
      }
      await fetchOtherSubjects();
    });

    return {
      loading,
      loadingSubjects,
      loadingEnroll,
      error,
      activeTab,
      openModule,
      subjects,
      otherSubjects,
      isEnrolled,
      course: computed(() => currentCourse.value),
      getClassName,
      getCourseTitle,
      getCourseDescription,
      fetchCourse,
      fetchOtherSubjects,
      navigateToSubject,
      toggleModule,
      enrollCourse,
      getCourseImage,
      getPreviewImage,
      getCourseCategory,
      getInstructorName,
      getInstructorQualification,
      getInstructorRating,
      getInstructorBio,
      getInstructorCoursesCount,
      getInstructorStudentsCount,
      getInstructorExperience,
      getInstructorEmail,
      getInstructorInstitute,
      getInstructorAvatar,
      getInstructorImage,
      formatDate,
      getLearningPoints,
      getAdditionalInfo,
      getCourseModules,
      getSubjectIcon,
      getSubjectDescription,
      handleLessonClick,
      getCourseFeatures,
      getCourseLevel,
      getTotalDuration,
      getTotalLessons
    };
  }
}
</script>

<style scoped>
/* Updated styles for subjects with teachers */
.subjects-grid {
  margin-top: 1rem;
}

.subject-card {
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
}

.subject-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  border-color: #4a6cf7;
}

.subject-header {
  border-bottom: 1px solid #e9ecef;
  padding-bottom: 1rem;
}

.subject-icon {
  color: #4a6cf7;
}

.subject-name {
  font-weight: 600;
  color: #2c3e50;
}

/* Teacher Info Styles */
.teacher-info {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-left: 4px solid #4a6cf7;
}

.teacher-avatar {
  border: 2px solid #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.teacher-name {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.teacher-qualification {
  font-size: 0.8rem;
}

.teacher-rating {
  background: rgba(255, 193, 7, 0.1);
  padding: 0.25rem 0.5rem;
  border-radius: 15px;
  font-size: 0.8rem;
}

/* Subject Details */
.subject-description {
  line-height: 1.4;
  font-size: 0.85rem;
}

.subject-meta {
  border-top: 1px solid #e9ecef;
  padding-top: 0.75rem;
}

/* Teachers Summary in Sidebar */
.teacher-summary-item {
  padding: 0.5rem;
  border-radius: 8px;
  transition: background-color 0.3s ease;
}

.teacher-summary-item:hover {
  background-color: #f8f9fa;
}

.teacher-summary-avatar {
  border: 2px solid #e9ecef;
}

.teacher-summary-name {
  font-weight: 600;
  color: #2c3e50;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .subjects-grid .col-md-6 {
    margin-bottom: 1rem;
  }
  
  .teacher-info {
    padding: 0.75rem;
  }
}

/* Enhanced card hover effects */
.subject-card {
  position: relative;
  overflow: hidden;
}

.subject-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, #4a6cf7, #3a5bd9);
  transition: left 0.3s ease;
}

.subject-card:hover::before {
  left: 0;
}

/* Teacher avatar enhancements */
.teacher-avatar, .teacher-summary-avatar {
  object-fit: cover;
}

/* Rating star enhancements */
.teacher-rating i {
  font-size: 0.7rem;
}

/* Add new styles for subjects */
.subjects-grid {
  margin-top: 1rem;
}

.subject-card {
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
}

.subject-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  border-color: #4a6cf7;
}

.subject-icon {
  color: #4a6cf7;
}

.subject-name {
  font-weight: 600;
  color: #2c3e50;
}

.subject-description {
  line-height: 1.4;
}

.subjects-summary .small {
  font-size: 0.85rem;
}

/* Ensure the new tab content is properly styled */
.tab-content {
  min-height: 400px;
}

/* Responsive adjustments for subjects */
@media (max-width: 768px) {
  .subjects-grid .col-md-6 {
    margin-bottom: 1rem;
  }
}


/* Improved CSS with better organization and modern design */
.loading-container, .error-container {
  min-height: 50vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.error-container .alert {
  max-width: 600px;
  margin: 0 auto;
}

/* Course Header */
.course-header {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 2rem;
  border-radius: 15px;
  margin-bottom: 2rem;
}

.course-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2c3e50;
  line-height: 1.2;
}

.course-description {
  font-size: 1.1rem;
  line-height: 1.6;
}

/* Instructor Info */
.instructor-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

/* Tabs */
.course-tabs .nav-pills .nav-link {
  padding: 1rem 1.5rem;
  font-weight: 600;
  color: #6c757d;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.course-tabs .nav-pills .nav-link.active {
  background: #4a6cf7;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(74, 108, 247, 0.3);
}

/* Learning Objectives */
.learning-point {
  line-height: 1.5;
}

/* Accordion */
.accordion-button {
  padding: 1.25rem;
  font-weight: 600;
  border: none;
  box-shadow: none;
}

.accordion-button:not(.collapsed) {
  background: #f8f9fa;
  color: #2c3e50;
}

/* Instructor Main Image */
.instructor-main-img {
  border: 4px solid #e9ecef;
  padding: 4px;
}

/* Reviews */
.rating-stars {
  font-size: 1.2rem;
}

.rating-small {
  font-size: 0.9rem;
}

.review-avatar {
  width: 40px;
  height: 40px;
  object-fit: cover;
}

/* Sidebar */
.sticky-sidebar {
  position: sticky;
  top: 100px;
}

/* Preview Video */
.preview-video {
  border-radius: 15px 15px 0 0;
  overflow: hidden;
}

.video-play-btn {
  width: 70px;
  height: 70px;
  background: rgba(74, 108, 247, 0.9);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  text-decoration: none;
  transition: all 0.3s ease;
}

.video-play-btn:hover {
  background: #3a5bd9;
  transform: scale(1.1);
  color: white;
}

/* Price Section */
.price-section h3 {
  font-size: 2.5rem;
  font-weight: 700;
}

/* Course Features */
.feature-item {
  padding: 0.5rem 0;
}

/* Social Sharing */
.social-sharing .btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Responsive Design */
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
}

/* Animation for better UX */
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

/* Custom badges */
.badge {
  font-size: 0.8rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
}

/* Improved button styles */
.btn-primary {
  background: linear-gradient(135deg, #4a6cf7 0%, #3a5bd9 100%);
  border: none;
  padding: 1rem 2rem;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
}

/* Better list group items */
.list-group-item {
  border: none;
  padding: 1rem 1.25rem;
  transition: all 0.3s ease;
}

.list-group-item:hover {
  background: #f8f9fa;
  transform: translateX(5px);
}

/* Enhanced subject card styles */
.hover-card {
  transition: all 0.3s ease;
  cursor: pointer;
  border: 2px solid transparent;
}

.hover-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(74, 108, 247, 0.15);
  border-color: #4a6cf7;
}

.subject-icon-wrapper {
  width: 50px;
  height: 50px;
  background: rgba(74, 108, 247, 0.1);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.subject-name {
  font-weight: 600;
  color: #2c3e50;
  transition: color 0.3s ease;
}

.hover-card:hover .subject-name {
  color: #4a6cf7;
}

/* Current subject info styles */
.current-subject-info {
  background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
  border-radius: 10px;
}

/* Badge enhancements */
.badge {
  font-size: 0.75rem;
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .subject-icon-wrapper {
    width: 40px;
    height: 40px;
  }
  
  .subject-icon-wrapper i {
    font-size: 1rem !important;
  }
  
  .hover-card .card-body {
    padding: 1rem;
  }
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
</style>