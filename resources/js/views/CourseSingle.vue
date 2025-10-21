<template>
  <main class="main-area fix">
    <!-- breadcrumb-area -->
    <section class="breadcrumb__area breadcrumb__bg" data-background="/assets/img/bg/breadcrumb_bg.jpg" style="background-image: url('/assets/img/bg/breadcrumb_bg.jpg');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb__content">
                        <h3 class="title">{{ course?.name || 'Course Details' }}</h3>
                        <nav class="breadcrumb">
                            <span property="itemListElement" typeof="ListItem">
                                <router-link to="/">Home</router-link>
                            </span>
                            <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                            <span property="itemListElement" typeof="ListItem">
                                <router-link to="/courses">Courses</router-link>
                            </span>
                            <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                            <span property="itemListElement" typeof="ListItem">{{ course?.name || 'Course Details' }}</span>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="breadcrumb__shape-wrap">
            <img src="/assets/img/banner/breadcrumb_shape01.svg" alt="img" class="alltuchtopdown">
            <img src="/assets/img/banner/breadcrumb_shape02.svg" alt="img" data-aos="fade-right" data-aos-delay="300">
            <img src="/assets/img/banner/breadcrumb_shape03.png" alt="img" data-aos="fade-up" data-aos-delay="400">
            <img src="/assets/img/banner/breadcrumb_shape04.png" alt="img" data-aos="fade-down-left" data-aos-delay="400">
            <img src="/assets/img/banner/breadcrumb_shape05.svg" alt="img" data-aos="fade-left" data-aos-delay="400">
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
        <router-link to="/courses" class="btn btn-secondary mt-3 ms-2">
          <i class="fas fa-arrow-left"></i> Back to Courses
        </router-link>
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
                    <span>{{ course.rating || '4.5' }}</span>
                    <span class="text-muted ms-1">({{ course.totalReviews || '12' }} reviews)</span>
                  </div>
                </div>
                <h1 class="course-title mb-3">{{ course.name }}</h1>
                <p class="course-description text-muted mb-4">{{ course.description || getDefaultDescription(course) }}</p>
                
                <!-- Course Meta -->
                <div class="course-meta d-flex flex-wrap gap-4 align-items-center">
                  <div class="instructor-info d-flex align-items-center">
                    <img :src="getInstructorAvatar(course.teachers?.[0])" :alt="course.teachers?.[0]?.name" class="instructor-avatar me-2">
                    <span>By <strong>{{ course.teachers?.[0]?.name || 'Expert Instructor' }}</strong></span>
                  </div>
                  <div class="student-count">
                    <i class="fas fa-users text-muted me-1"></i>
                    {{ course.studentCount || 0 }} students
                  </div>
                  <div class="last-updated">
                    <i class="fas fa-calendar text-muted me-1"></i>
                    {{ formatDate(course.updated_at || course.created_at) }}
                  </div>
                </div>
              </div>

              <!-- Course Thumbnail -->
              <div class="courses__details-thumb mb-5">
                <img :src="getCourseImage(course)" :alt="course.name" class="w-100 rounded-3 shadow-sm">
              </div>

              <!-- Tabs Navigation -->
              <div class="course-tabs mb-4">
                <ul class="nav nav-pills nav-fill" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#overview" type="button">
                      <i class="fas fa-info-circle me-2"></i>Overview
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#curriculum" type="button">
                      <i class="fas fa-book me-2"></i>Curriculum
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#subjects" type="button">
                      <i class="fas fa-book-open me-2"></i>Subjects & Teachers
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#instructor" type="button">
                      <i class="fas fa-user-tie me-2"></i>Instructor
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#reviews" type="button">
                      <i class="fas fa-star me-2"></i>Reviews
                    </button>
                  </li>
                </ul>
              </div>

              <!-- Tabs Content -->
              <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview">
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
                <div class="tab-pane fade" id="curriculum">
                  <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <h4 class="card-title mb-4">Course Content</h4>
                      <div class="accordion" id="curriculumAccordion">
                        <div class="accordion-item border-0 mb-3" v-for="(module, index) in getCourseModules(course)" :key="module.id">
                          <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light fw-semibold" type="button" data-bs-toggle="collapse" :data-bs-target="`#module${module.id}`">
                              <i class="fas fa-play-circle me-3 text-primary"></i>
                              {{ module.title }}
                              <span class="badge bg-secondary ms-auto me-3">{{ module.lessons.length }} lessons</span>
                            </button>
                          </h3>
                          <div :id="`module${module.id}`" class="accordion-collapse collapse" data-bs-parent="#curriculumAccordion">
                            <div class="accordion-body p-0">
                              <div class="list-group list-group-flush">
                                <a v-for="lesson in module.lessons" :key="lesson.id" 
                                   :href="lesson.link" 
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
                <div class="tab-pane fade" id="subjects">
                  <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <h4 class="card-title mb-4">Subjects & Teachers</h4>
                      
                      <!-- Loading State for Subjects -->
                      <div v-if="loadingSubjects" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                          <span class="visually-hidden">Loading subjects...</span>
                        </div>
                        <p class="mt-2">Loading subjects and teachers...</p>
                      </div>

                      <!-- Subjects Grid with Teachers -->
                      <div v-else-if="subjects && subjects.length > 0" class="subjects-grid">
                        <div class="row g-4">
                          <div class="col-md-6 col-lg-4" v-for="subject in subjects" :key="subject.id">
                            <div class="subject-card card border-0 shadow-sm h-100">
                              <div class="card-body p-4">
                                <!-- Subject Header -->
                                <div class="subject-header text-center mb-3">
                                  <div class="subject-icon mb-3">
                                    <i :class="getSubjectIcon(subject.name)" class="text-primary fa-2x"></i>
                                  </div>
                                  <h5 class="subject-name mb-2">{{ subject.name }}</h5>
                                </div>

                                <!-- Teacher Information -->
                                <div class="teacher-info mb-3 p-3 bg-light rounded">
                                  <div class="d-flex align-items-center">
                                    <img :src="getTeacherAvatar(subject.teacher)" 
                                         :alt="subject.teacher?.name" 
                                         class="teacher-avatar rounded-circle me-3"
                                         width="40"
                                         height="40">
                                    <div class="teacher-details flex-grow-1">
                                      <h6 class="teacher-name mb-1">{{ subject.teacher?.name || 'Expert Teacher' }}</h6>
                                      <p class="teacher-qualification mb-0 text-muted small">
                                        {{ subject.teacher?.qualification || 'Subject Expert' }}
                                      </p>
                                    </div>
                                    <div class="teacher-rating">
                                      <i class="fas fa-star text-warning small"></i>
                                      <span class="small ms-1">{{ subject.teacher?.rating || '4.8' }}</span>
                                    </div>
                                  </div>
                                </div>

                                <!-- Subject Details -->
                                <div class="subject-details">
                                  <p class="subject-description text-muted small mb-3">
                                    {{ subject.description || getSubjectDescription(subject.name) }}
                                  </p>
                                  <div class="subject-meta d-flex justify-content-between">
                                    <small class="text-muted">
                                      <i class="fas fa-book me-1"></i>
                                      {{ subject.lesson_count || 12 }} lessons
                                    </small>
                                    <small class="text-muted">
                                      <i class="fas fa-clock me-1"></i>
                                      {{ subject.duration || '10h 30m' }}
                                    </small>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- No Subjects State -->
                      <div v-else class="text-center py-5">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No subjects available</h5>
                        <p class="text-muted">Subjects for this course will be added soon.</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Instructor Tab -->
                <div class="tab-pane fade" id="instructor">
                  <div class="card border-0 shadow-sm" v-if="course.teachers && course.teachers.length > 0">
                    <div class="card-body p-4">
                      <div class="row g-4">
                        <div class="col-md-3 text-center">
                          <img :src="getInstructorImage(course.teachers[0])" :alt="course.teachers[0].name" class="instructor-main-img rounded-circle mb-3 w-100" style="max-width: 150px;">
                        </div>
                        <div class="col-md-9">
                          <h4 class="card-title">{{ course.teachers[0].name }}</h4>
                          <p class="text-muted mb-3">{{ course.teachers[0].qualification || 'Expert Instructor' }}</p>
                          <div class="rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <span class="ms-1 fw-semibold">{{ course.teachers[0].rating || '4.8' }}</span>
                            <span class="text-muted ms-1">Instructor Rating</span>
                          </div>
                          <p class="mb-4">{{ course.teachers[0].bio || getDefaultInstructorBio(course.teachers[0]) }}</p>
                          <div class="instructor-social">
                            <a href="#" class="btn btn-outline-secondary btn-sm me-2">
                              <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="btn btn-outline-secondary btn-sm me-2">
                              <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-outline-secondary btn-sm">
                              <i class="fab fa-github"></i>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews">
                  <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <h4 class="card-title mb-4">Student Reviews</h4>
                      <div class="row g-4">
                        <div class="col-md-4">
                          <div class="text-center">
                            <div class="display-4 fw-bold text-primary mb-2">{{ course.ratingValue || '4.8' }}</div>
                            <div class="rating-stars mb-2">
                              <i class="fas fa-star text-warning" v-for="star in 5" :key="star"></i>
                            </div>
                            <div class="text-muted">Based on {{ course.totalReviews || '12' }} reviews</div>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div v-for="review in getCourseReviews(course)" :key="review.id" class="review-item border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center mb-2">
                              <img :src="review.avatar" :alt="review.name" class="review-avatar rounded-circle me-3" width="40">
                              <div>
                                <h6 class="mb-0">{{ review.name }}</h6>
                                <div class="rating-small">
                                  <i class="fas fa-star text-warning" v-for="star in review.rating" :key="star"></i>
                                </div>
                              </div>
                              <small class="text-muted ms-auto">{{ review.date }}</small>
                            </div>
                            <h6 class="text-primary">{{ review.title }}</h6>
                            <p class="mb-0 text-muted">{{ review.comment }}</p>
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
                <!-- Preview Video -->
                <div class="card border-0 shadow-sm mb-4">
                  <div class="card-body p-0">
                    <div class="preview-video position-relative">
                      <img :src="getPreviewImage(course)" :alt="course.name" class="w-100 rounded-top">
                      <a :href="getPreviewVideo(course)" class="video-play-btn position-absolute top-50 start-50 translate-middle" @click.prevent="playPreviewVideo">
                        <i class="fas fa-play"></i>
                      </a>
                    </div>
                    <div class="p-4">
                      <div class="price-section text-center mb-3">
                        <span class="text-muted d-block mb-1">Course Fee</span>
                        <h3 class="text-primary mb-0">${{ getCoursePrice(course) }}</h3>
                        <del v-if="hasDiscount(course)" class="text-muted">${{ getOriginalPrice(course) }}</del>
                      </div>
                      <button class="btn btn-primary w-100 btn-lg mb-3" @click="enrollCourse" :disabled="isEnrolled || loadingEnroll">
                        <span v-if="loadingEnroll" class="spinner-border spinner-border-sm me-2"></span>
                        {{ isEnrolled ? 'Already Enrolled' : 'Enroll Now' }}
                      </button>
                      <div class="text-center">
                        <small class="text-muted">30-day money-back guarantee</small>
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

                <!-- Teachers Summary -->
                <div class="card border-0 shadow-sm mb-4" v-if="subjects && subjects.length > 0">
                  <div class="card-body">
                    <h6 class="card-title mb-3">Course Teachers</h6>
                    <div class="teachers-summary">
                      <div class="teacher-summary-item d-flex align-items-center mb-3" 
                           v-for="(teacher, index) in getUniqueTeachers(subjects)" 
                           :key="teacher.id || index">
                        <img :src="getTeacherAvatar(teacher)" 
                             :alt="teacher.name" 
                             class="teacher-summary-avatar rounded-circle me-3"
                             width="35"
                             height="35">
                        <div class="flex-grow-1">
                          <h6 class="teacher-summary-name mb-0 small">{{ teacher.name }}</h6>
                          <small class="text-muted">{{ getTeacherSubjects(teacher, subjects) }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Share Course -->
                <div class="card border-0 shadow-sm">
                  <div class="card-body text-center">
                    <h6 class="card-title mb-3">Share this course</h6>
                    <div class="social-sharing d-flex justify-content-center gap-2">
                      <a href="#" class="btn btn-outline-primary btn-sm rounded-circle">
                        <i class="fab fa-facebook-f"></i>
                      </a>
                      <a href="#" class="btn btn-outline-info btn-sm rounded-circle">
                        <i class="fab fa-twitter"></i>
                      </a>
                      <a href="#" class="btn btn-outline-success btn-sm rounded-circle">
                        <i class="fab fa-whatsapp"></i>
                      </a>
                      <a href="#" class="btn btn-outline-danger btn-sm rounded-circle">
                        <i class="fab fa-instagram"></i>
                      </a>
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
</template>

<script>
import { authService } from '../src/services/authService.js';

export default {
  name: 'CourseSingle',
  data() {
    return {
      course: null,
      subjects: null,
      loading: true,
      loadingSubjects: false,
      error: null,
      isEnrolled: false,
      loadingEnroll: false,
      isTokenInvalid: false
    }
  },
  async mounted() {
  await this.fetchCourse();
  
  // Temporary debug to see what the API returns
  await this.debugSubjectsAPI(this.$route.params.id);
  
  await this.fetchSubjects();
  this.checkEnrollment();
  },
  methods: {
    async fetchCourse() {
      this.loading = true;
      this.error = null;
      this.isTokenInvalid = false;
      
      try {
        const courseId = this.$route.params.id;

        console.log('🔍 Fetching course details...');
        console.log('🔍 Course ID:', courseId);

        // Use auth service for API call
        const data = await authService.apiCall(`/api/courses/${courseId}`);
        
        console.log('✅ Course data received:', data);

        if (data.success && data.data) {
          this.course = data.data;
          console.log('✅ Successfully fetched course:', this.course);
        } else {
          throw new Error(data.message || 'Failed to load course details');
        }
      } catch (error) {
        console.error('❌ Error fetching course:', error);
        this.error = error.message || 'Unable to load course details. Please try again later.';
        this.isTokenInvalid = error.message.includes('session') || error.message.includes('Authentication');
        
        if (this.isTokenInvalid) {
          setTimeout(() => {
            this.$router.push('/student-login');
          }, 2000);
        }
      } finally {
        this.loading = false;
      }
    },

    async fetchSubjects() {
  this.loadingSubjects = true;
  
  try {
    const courseId = this.$route.params.id;

    if (!authService.isAuthenticated()) {
      console.log('🔐 Authentication required for subjects');
      this.subjects = this.getMockSubjectsWithTeachers(courseId);
      return;
    }

    console.log('🔍 Attempting to fetch subjects for course:', courseId);
    
    // Use the new subjects endpoint
    try {
      const response = await authService.apiCall(`/api/courses/${courseId}/subjects`);
      console.log('✅ Subjects API response:', response);
      
      // FIX: Check if response.data exists and is an array
      if (response && response.success && Array.isArray(response.data)) {
        console.log('✅ Using real subjects data from API');
        this.subjects = response.data;
        return;
      } else {
        console.warn('⚠️ Subjects API returned invalid data structure:', response);
        throw new Error('Invalid data structure from API');
      }
    } catch (apiError) {
      console.log('⚠️ Subjects API failed:', apiError.message);
      // Continue to fallback
    }

    // Fallback to mock data only if API completely fails
    console.log('📚 Using mock subjects data as fallback');
    this.subjects = this.getMockSubjectsWithTeachers(courseId);
    
  } catch (error) {
    console.error('❌ Error in fetchSubjects:', error);
    console.log('📚 Using mock subjects due to error');
    this.subjects = this.getMockSubjectsWithTeachers(this.$route.params.id);
  } finally {
    this.loadingSubjects = false;
  }
    },

    goToLogin() {
      this.$router.push('/student-login');
    },

    // Your existing methods remain the same (getMockSubjectsWithTeachers, getTeacherAvatar, etc.)
    getMockSubjectsWithTeachers(courseId) {
  console.log('🎭 Generating mock subjects for course:', courseId);
  
  // Ensure mock data matches the API response format exactly
  const mockSubjects = [
    {
      id: courseId + 1,
      name: 'Mathematics',
      description: 'Develop problem-solving skills and mathematical thinking',
      lesson_count: 15,
      duration: '12h 30m',
      student_count: 35,
      teacher: {
        id: 1,
        name: 'Dr. Ahmed Rahman',
        email: 'ahmed.rahman@school.edu',
        qualification: 'PhD in Mathematics',
        experience: '10+ years',
        rating: '4.9',
        avatar: '/assets/img/teachers/teacher1.jpg'
      }
    },
    {
      id: courseId + 2,
      name: 'Science',
      description: 'Explore scientific concepts and experimental methods',
      lesson_count: 12,
      duration: '10h 45m',
      student_count: 28,
      teacher: {
        id: 2,
        name: 'Ms. Fatima Begum',
        email: 'fatima.begum@school.edu',
        qualification: 'MSc in Physics',
        experience: '8+ years',
        rating: '4.7',
        avatar: '/assets/img/teachers/teacher2.jpg'
      }
    },
    {
      id: courseId + 3,
      name: 'English',
      description: 'Improve language proficiency and communication skills',
      lesson_count: 18,
      duration: '15h 20m',
      student_count: 42,
      teacher: {
        id: 3,
        name: 'Mr. Kabir Hossain',
        email: 'kabir.hossain@school.edu',
        qualification: 'MA in English Literature',
        experience: '12+ years',
        rating: '4.8',
        avatar: '/assets/img/teachers/teacher3.jpg'
      }
    }
  ];

  console.log('🎭 Mock subjects generated:', mockSubjects);
  return mockSubjects;
    },

    getTeacherAvatar(teacher) {
      if (!teacher) return '/assets/img/teachers/default-teacher.jpg';
      return teacher.avatar || '/assets/img/teachers/default-teacher.jpg';
    },

    getUniqueTeachers(subjects) {
      if (!subjects) return [];
      const teachersMap = new Map();
      subjects.forEach(subject => {
        if (subject.teacher) {
          teachersMap.set(subject.teacher.id || subject.teacher.name, subject.teacher);
        }
      });
      return Array.from(teachersMap.values());
    },

    getTeacherSubjects(teacher, subjects) {
      if (!teacher || !subjects) return '';
      const teacherSubjects = subjects
        .filter(subject => subject.teacher && (subject.teacher.id === teacher.id || subject.teacher.name === teacher.name))
        .map(subject => subject.name);
      
      if (teacherSubjects.length <= 2) {
        return teacherSubjects.join(', ');
      } else {
        return `${teacherSubjects.slice(0, 2).join(', ')} +${teacherSubjects.length - 2} more`;
      }
    },

    async checkEnrollment() {
      try {
        if (!authService.isAuthenticated()) return;
        // Simulate enrollment check
        this.isEnrolled = false;
      } catch (error) {
        console.error('Error checking enrollment:', error);
      }
    },

    async enrollCourse() {
      if (!authService.isAuthenticated()) {
        this.$router.push('/student-login');
        return;
      }

      this.loadingEnroll = true;
      
      try {
        const courseId = this.course.id;
        const data = await authService.apiCall(`/api/enrollments/course/${courseId}`, {
          method: 'POST'
        });

        if (data.success) {
          this.isEnrolled = true;
          alert('Successfully enrolled in the course!');
        } else {
          throw new Error(data.message || 'Enrollment failed');
        }
      } catch (error) {
        console.error('Error enrolling in course:', error);
        
        if (error.message.includes('session') || error.message.includes('Authentication')) {
          alert('Your session has expired. Please login again.');
        } else {
          alert(error.message || 'Failed to enroll in course. Please try again.');
        }
      } finally {
        this.loadingEnroll = false;
      }
    },

    // Add this temporary debug method to inspect the API response
    async debugSubjectsAPI(courseId) {
  try {
    console.log('🔍 [DEBUG] Testing subjects API for course:', courseId);
    const response = await fetch(`/api/courses/${courseId}/subjects`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      }
    });
    
    console.log('🔍 [DEBUG] Raw API response status:', response.status);
    const rawData = await response.json();
    console.log('🔍 [DEBUG] Raw API response data:', rawData);
    
    return rawData;
  } catch (error) {
    console.error('🔍 [DEBUG] API test failed:', error);
    return null;
  }
    },

    // ... All your other existing helper methods remain the same
    getCourseImage(course) {
      if (!course) return '/assets/img/courses/courses_details.png';
      return course.image || '/assets/img/courses/courses_details.png';
    },

    getPreviewImage(course) {
      if (!course) return '/assets/img/courses/course_thumb02.png';
      return course.previewImage || '/assets/img/courses/course_thumb02.png';
    },

    getPreviewVideo(course) {
      if (!course) return 'https://www.youtube.com/watch?v=YwrHGratByU';
      return course.previewVideo || 'https://www.youtube.com/watch?v=YwrHGratByU';
    },

    getCourseCategory(course) {
      if (!course) return 'Course';
      if (course.type === 'regular') {
        if (course.grade <= 5) return 'Primary';
        if (course.grade <= 8) return 'Junior';
        if (course.grade <= 10) return 'Secondary';
        return 'Higher Secondary';
      }
      return course.category || 'Skill Course';
    },

    getInstructorAvatar(teacher) {
      if (!teacher) return '/assets/img/courses/course_author001.png';
      return teacher.avatar || '/assets/img/courses/course_author001.png';
    },

    getInstructorImage(teacher) {
      if (!teacher) return '/assets/img/courses/course_instructors.png';
      return teacher.image || '/assets/img/courses/course_instructors.png';
    },

    formatDate(dateString) {
      if (!dateString) return 'Recently';
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB');
      } catch (error) {
        return 'Recently';
      }
    },

    getDefaultDescription(course) {
      if (!course) return 'Course description not available.';
      if (course.type === 'regular') {
        return `Comprehensive ${course.name} curriculum for grade ${course.grade} students. This course covers all essential subjects and prepares students for academic success.`;
      } else {
        return `Explore ${course.name} - ${course.category || 'Specialized course'}. Learn essential skills and knowledge from expert instructors.`;
      }
    },

    getLearningPoints(course) {
      const basePoints = [
        'Comprehensive understanding of core concepts',
        'Practical application of learned skills',
        'Interactive learning materials and resources',
        'Expert guidance and support'
      ];
      
      if (!course) return basePoints;
      if (course.type === 'regular') {
        return [
          `Complete ${course.name} syllabus coverage`,
          'Problem-solving techniques and strategies',
          'Exam preparation and practice tests',
          'Interactive learning activities'
        ];
      }
      return basePoints;
    },

    getAdditionalInfo(course) {
      if (!course) return 'This course is designed to provide you with the best learning experience.';
      return `This course includes ${this.subjects?.length || 'multiple'} subjects designed to provide comprehensive learning. Join thousands of students who have benefited from our structured curriculum and expert instruction.`;
    },

    getCourseModules(course) {
      return [
        {
          id: 1,
          title: 'Introduction',
          lessons: [
            { id: 1, title: 'Course Overview', duration: '03:03', preview: true, link: 'https://www.youtube.com/watch?v=b2Az7_lLh3g' },
            { id: 2, title: 'Getting Started', duration: '07:48', preview: false, link: '#' }
          ]
        },
        {
          id: 2,
          title: 'Core Concepts',
          lessons: [
            { id: 3, title: 'Fundamental Principles', duration: '10:15', preview: false, link: '#' },
            { id: 4, title: 'Advanced Techniques', duration: '12:30', preview: false, link: '#' }
          ]
        }
      ];
    },

    getDefaultInstructorBio(teacher) {
      if (!teacher) return 'Experienced instructor committed to providing quality education.';
      return `Experienced instructor with expertise in ${teacher.qualification || 'the subject matter'}. Committed to providing quality education and helping students achieve their learning goals.`;
    },

    getCourseReviews(course) {
      return [
        {
          id: 1,
          name: 'Student User',
          avatar: '/assets/img/courses/review-author.png',
          date: 'Recently',
          rating: 5,
          title: 'Excellent Course',
          comment: 'This course provided comprehensive learning materials and excellent instruction. Highly recommended for anyone looking to improve their skills.'
        }
      ];
    },

    getCoursePrice(course) {
      return '18.00';
    },

    getOriginalPrice(course) {
      return '32.00';
    },

    hasDiscount(course) {
      return true;
    },

    getCourseLevel(course) {
      if (!course) return 'All Levels';
      if (course.type === 'regular') {
        if (course.grade <= 5) return 'Beginner';
        if (course.grade <= 8) return 'Intermediate';
        return 'Advanced';
      }
      return 'All Levels';
    },

    getTotalDuration(course) {
      return '11h 20m';
    },

    getTotalLessons(course) {
      return '12';
    },

    getQuizzesCount(course) {
      return '8';
    },

    hasCertificate(course) {
      return true;
    },

    handleLessonClick(lesson) {
      if (lesson.preview) {
        this.playPreviewVideo(lesson.link);
      } else if (!this.isEnrolled) {
        this.$router.push('/student-login');
      } else {
        console.log('Navigate to lesson:', lesson.title);
      }
    },

    playPreviewVideo(videoUrl = null) {
      const url = videoUrl || this.getPreviewVideo(this.course);
      if (url) {
        window.open(url, '_blank');
      }
    },

    getCourseFeatures(course) {
      return [
        { icon: 'fas fa-play-circle', text: `${this.getTotalLessons(course)} on-demand videos` },
        { icon: 'fas fa-file-alt', text: `${this.getQuizzesCount(course)} practice exercises` },
        { icon: 'fas fa-infinity', text: 'Full lifetime access' },
        { icon: 'fas fa-mobile-alt', text: 'Access on mobile and TV' },
        { icon: 'fas fa-trophy', text: 'Certificate of completion' },
        { icon: 'fas fa-clock', text: `${this.getTotalDuration(course)} total duration` }
      ];
    }
  },
  computed: {
    isAuthenticated() {
      return authService.isAuthenticated();
    }
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