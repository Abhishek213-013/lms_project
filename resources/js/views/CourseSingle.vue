<template>
  <div class="main-area fix" v-if="course">
    <!-- Course Hero Section -->
    <section class="course-hero-area section-pt-120 section-pb-80" 
             :style="{ backgroundImage: 'url(/assets/img/bg/course_hero_bg.jpg)' }">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <div class="course-hero-content">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item">
                    <router-link to="/">Home</router-link>
                  </li>
                  <li class="breadcrumb-item">
                    <router-link to="/courses">Courses</router-link>
                  </li>
                  <li class="breadcrumb-item active" aria-current="page">{{ course.name }}</li>
                </ol>
              </nav>
              <h1 class="course-title">{{ course.name }}</h1>
              <p class="course-description">{{ course.description }}</p>
              
              <div class="course-meta">
                <div class="meta-item">
                  <div class="meta-icon">
                    <i class="flaticon-mortarboard"></i>
                  </div>
                  <div class="meta-content">
                    <span>Enrolled Students</span>
                    <strong>{{ course.studentCount || 0 }}</strong>
                  </div>
                </div>
                <div class="meta-item">
                  <div class="meta-icon">
                    <i class="flaticon-book"></i>
                  </div>
                  <div class="meta-content">
                    <span>Lessons</span>
                    <strong>{{ course.subjectCount || 0 }}</strong>
                  </div>
                </div>
                <div class="meta-item">
                  <div class="meta-icon">
                    <i class="flaticon-clock"></i>
                  </div>
                  <div class="meta-content">
                    <span>Duration</span>
                    <strong>{{ course.duration || 'Self-paced' }}</strong>
                  </div>
                </div>
                <div class="meta-item">
                  <div class="meta-icon">
                    <i class="fas fa-star"></i>
                  </div>
                  <div class="meta-content">
                    <span>Rating</span>
                    <strong>{{ course.rating || '4.8' }}/5.0</strong>
                  </div>
                </div>
              </div>

              <div class="course-actions">
                <button class="btn btn-primary" @click="enrollCourse" v-if="!isEnrolled">
                  <i class="fas fa-shopping-cart"></i>
                  {{ course.fee ? `Enroll Now - $${course.fee}` : 'Enroll for Free' }}
                </button>
                <button class="btn btn-success" disabled v-else>
                  <i class="fas fa-check"></i>
                  Already Enrolled
                </button>
                <button class="btn btn-outline">
                  <i class="far fa-heart"></i>
                  Add to Wishlist
                </button>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="course-hero-image">
              <img :src="course.thumbnail || '/assets/img/courses/course_thumb01.png'" :alt="course.name">
              <div class="course-badge" v-if="course.fee">
                <span class="badge">Premium</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Course Details Section -->
    <section class="course-details-area section-pb-120">
      <div class="container">
        <div class="row">
          <!-- Main Content -->
          <div class="col-lg-8">
            <!-- Course Tabs -->
            <div class="course-tabs">
              <nav>
                <div class="nav nav-tabs" id="courseTab" role="tablist">
                  <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                    Overview
                  </button>
                  <button class="nav-link" id="curriculum-tab" data-bs-toggle="tab" data-bs-target="#curriculum" type="button">
                    Curriculum
                  </button>
                  <button class="nav-link" id="instructor-tab" data-bs-toggle="tab" data-bs-target="#instructor" type="button">
                    Instructor
                  </button>
                  <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                    Reviews
                  </button>
                </div>
              </nav>

              <div class="tab-content" id="courseTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                  <div class="course-overview">
                    <h3>Course Description</h3>
                    <p>{{ course.fullDescription || course.description }}</p>
                    
                    <div class="what-you-learn">
                      <h4>What You'll Learn</h4>
                      <div class="row">
                        <div class="col-md-6">
                          <ul class="learn-list">
                            <li v-for="(item, index) in learningPoints.slice(0, Math.ceil(learningPoints.length/2))" :key="index">
                              <i class="fas fa-check"></i> {{ item }}
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-6">
                          <ul class="learn-list">
                            <li v-for="(item, index) in learningPoints.slice(Math.ceil(learningPoints.length/2))" :key="index">
                              <i class="fas fa-check"></i> {{ item }}
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <div class="course-requirements">
                      <h4>Requirements</h4>
                      <ul>
                        <li>Basic computer knowledge</li>
                        <li>Internet connection</li>
                        <li>Dedication to learn</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Curriculum Tab -->
                <div class="tab-pane fade" id="curriculum" role="tabpanel">
                  <div class="course-curriculum">
                    <h3>Course Curriculum</h3>
                    <div class="accordion" id="curriculumAccordion">
                      <div class="accordion-item" v-for="(module, index) in curriculum" :key="module.id">
                        <h2 class="accordion-header">
                          <button class="accordion-button" type="button" :class="{ collapsed: index !== 0 }" 
                                  data-bs-toggle="collapse" :data-bs-target="`#module${module.id}`">
                            <span class="module-title">{{ module.title }}</span>
                            <span class="module-meta">
                              {{ module.lessons }} lessons • {{ module.duration }}
                            </span>
                          </button>
                        </h2>
                        <div :id="`module${module.id}`" class="accordion-collapse collapse" :class="{ show: index === 0 }"
                             data-bs-parent="#curriculumAccordion">
                          <div class="accordion-body">
                            <ul class="lesson-list">
                              <li v-for="lesson in module.lessonsList" :key="lesson.id" 
                                  :class="{ locked: !isEnrolled && lesson.locked }">
                                <div class="lesson-info">
                                  <i class="fas" :class="lesson.icon"></i>
                                  <span class="lesson-title">{{ lesson.title }}</span>
                                  <span class="lesson-duration">{{ lesson.duration }}</span>
                                </div>
                                <div class="lesson-action">
                                  <span class="lesson-preview" v-if="lesson.preview">Preview</span>
                                  <i class="fas fa-lock" v-else-if="!isEnrolled && lesson.locked"></i>
                                  <i class="fas fa-play" v-else></i>
                                </div>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Instructor Tab -->
                <div class="tab-pane fade" id="instructor" role="tabpanel">
                  <div class="course-instructor" v-if="course.teacher">
                    <div class="instructor-profile">
                      <div class="instructor-avatar">
                        <img :src="course.teacher.avatar || '/assets/img/courses/course_author001.png'" :alt="course.teacher.name">
                      </div>
                      <div class="instructor-info">
                        <h3>{{ course.teacher.name }}</h3>
                        <p class="instructor-title">{{ course.teacher.title || 'Senior Instructor' }}</p>
                        <div class="instructor-rating">
                          <div class="rating-stars">
                            <i class="fas fa-star" v-for="star in 5" :key="star"></i>
                          </div>
                          <span class="rating-text">4.8 Instructor Rating</span>
                        </div>
                        <div class="instructor-stats">
                          <div class="stat">
                            <strong>{{ course.teacher.students || '1.2k' }}</strong>
                            <span>Students</span>
                          </div>
                          <div class="stat">
                            <strong>{{ course.teacher.courses || '15' }}</strong>
                            <span>Courses</span>
                          </div>
                          <div class="stat">
                            <strong>{{ course.teacher.reviews || '2.3k' }}</strong>
                            <span>Reviews</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="instructor-bio">
                      <p>{{ course.teacher.bio || 'Experienced instructor with years of teaching experience and industry knowledge.' }}</p>
                    </div>
                  </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                  <div class="course-reviews">
                    <div class="reviews-header">
                      <div class="rating-overview">
                        <div class="overview-rating">
                          <h2>4.8</h2>
                          <div class="stars">
                            <i class="fas fa-star" v-for="star in 5" :key="star"></i>
                          </div>
                          <p>Course Rating</p>
                        </div>
                        <div class="rating-bars">
                          <div class="rating-bar" v-for="n in 5" :key="n">
                            <span class="stars">{{ 6 - n }} <i class="fas fa-star"></i></span>
                            <div class="bar">
                              <div class="fill" :style="{ width: `${(6 - n) * 20}%` }"></div>
                            </div>
                            <span class="percentage">{{ (6 - n) * 20 }}%</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="reviews-list">
                      <div class="review-item" v-for="review in reviews" :key="review.id">
                        <div class="reviewer-avatar">
                          <img :src="review.avatar" :alt="review.name">
                        </div>
                        <div class="review-content">
                          <div class="review-header">
                            <h5>{{ review.name }}</h5>
                            <div class="review-rating">
                              <i class="fas fa-star" v-for="star in review.rating" :key="star"></i>
                            </div>
                            <span class="review-date">{{ review.date }}</span>
                          </div>
                          <p>{{ review.comment }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            <div class="course-sidebar">
              <div class="sidebar-widget course-features">
                <h4>Course Features</h4>
                <ul class="features-list">
                  <li>
                    <i class="fas fa-play-circle"></i>
                    <span>Total Lessons</span>
                    <strong>{{ course.subjectCount || 0 }}</strong>
                  </li>
                  <li>
                    <i class="fas fa-clock"></i>
                    <span>Duration</span>
                    <strong>{{ course.duration || 'Self-paced' }}</strong>
                  </li>
                  <li>
                    <i class="fas fa-sliders-h"></i>
                    <span>Skill Level</span>
                    <strong>{{ course.level || 'All Levels' }}</strong>
                  </li>
                  <li>
                    <i class="fas fa-language"></i>
                    <span>Language</span>
                    <strong>English</strong>
                  </li>
                  <li>
                    <i class="fas fa-certificate"></i>
                    <span>Certificate</span>
                    <strong>Yes</strong>
                  </li>
                </ul>
              </div>

              <div class="sidebar-widget course-price" v-if="course.fee">
                <div class="price-card">
                  <div class="price-header">
                    <h4>Course Price</h4>
                    <div class="price-amount">${{ course.fee }}</div>
                  </div>
                  <ul class="price-features">
                    <li><i class="fas fa-check"></i> Full Lifetime Access</li>
                    <li><i class="fas fa-check"></i> Certificate of Completion</li>
                    <li><i class="fas fa-check"></i> Instructor Support</li>
                    <li><i class="fas fa-check"></i> Downloadable Resources</li>
                  </ul>
                  <button class="btn btn-primary btn-block" @click="enrollCourse">
                    Enroll Now
                  </button>
                </div>
              </div>

              <div class="sidebar-widget share-widget">
                <h4>Share This Course</h4>
                <div class="share-buttons">
                  <a href="#" class="share-btn facebook"><i class="fab fa-facebook-f"></i></a>
                  <a href="#" class="share-btn twitter"><i class="fab fa-twitter"></i></a>
                  <a href="#" class="share-btn linkedin"><i class="fab fa-linkedin-in"></i></a>
                  <a href="#" class="share-btn whatsapp"><i class="fab fa-whatsapp"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Loading State -->
  <div v-else class="loading-section">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p>Loading course details...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CourseSingle',
  data() {
    return {
      course: null,
      isEnrolled: false,
      learningPoints: [
        'Master fundamental concepts and principles',
        'Develop practical skills through hands-on projects',
        'Understand advanced techniques and methodologies',
        'Build confidence in applying knowledge',
        'Prepare for real-world challenges',
        'Enhance problem-solving abilities',
        'Learn from industry experts',
        'Gain valuable certifications'
      ],
      curriculum: [
        {
          id: 1,
          title: 'Introduction to the Course',
          lessons: 3,
          duration: '45m',
          lessonsList: [
            { id: 1, title: 'Welcome to the Course', duration: '10m', icon: 'fa-play-circle', preview: true },
            { id: 2, title: 'Course Overview', duration: '15m', icon: 'fa-video', locked: true },
            { id: 3, title: 'Setting Up Environment', duration: '20m', icon: 'fa-cog', locked: true }
          ]
        },
        {
          id: 2,
          title: 'Fundamental Concepts',
          lessons: 5,
          duration: '2h 30m',
          lessonsList: [
            { id: 4, title: 'Basic Principles', duration: '25m', icon: 'fa-book', locked: true },
            { id: 5, title: 'Core Concepts', duration: '35m', icon: 'fa-lightbulb', locked: true },
            { id: 6, title: 'Practical Examples', duration: '45m', icon: 'fa-code', locked: true }
          ]
        }
      ],
      reviews: [
        {
          id: 1,
          name: 'John Doe',
          avatar: '/assets/img/avatars/avatar1.jpg',
          rating: 5,
          date: '2 weeks ago',
          comment: 'Excellent course! The instructor explains complex concepts in a very understandable way.'
        },
        {
          id: 2,
          name: 'Sarah Smith',
          avatar: '/assets/img/avatars/avatar2.jpg',
          rating: 4,
          date: '1 month ago',
          comment: 'Very comprehensive and well-structured. Learned a lot from this course.'
        }
      ]
    }
  },
  async mounted() {
    await this.fetchCourse();
  },
  methods: {
    async fetchCourse() {
      const slug = this.$route.params.slug;
      try {
        const response = await this.$http.get(`/api/courses/${slug}`);
        this.course = response.data.data || this.getMockCourse(slug);
      } catch (error) {
        console.error('Error fetching course:', error);
        this.course = this.getMockCourse(slug);
      }
    },
    getMockCourse(slug) {
      const mockCourses = {
        'mathematics-class-10': {
          id: 1,
          name: 'Mathematics - Class 10',
          slug: 'mathematics-class-10',
          description: 'Comprehensive mathematics course for class 10 students covering algebra, geometry, trigonometry, and statistics.',
          fullDescription: 'This comprehensive mathematics course is designed specifically for class 10 students. Covering all major topics including algebra, geometry, trigonometry, and statistics, this course provides in-depth explanations, practical examples, and extensive practice problems. Our expert instructors guide you through each concept with clear explanations and real-world applications.',
          category: 'secondary',
          type: 'regular',
          studentCount: 45,
          subjectCount: 15,
          rating: '4.8',
          duration: '30 hours',
          level: 'Intermediate',
          teacher: {
            name: 'John Smith',
            avatar: '/assets/img/courses/course_author001.png',
            title: 'Mathematics Expert',
            bio: 'John has been teaching mathematics for over 10 years. He holds a Masters degree in Mathematics and has helped thousands of students achieve academic excellence.',
            students: '2.5k',
            courses: '12',
            reviews: '1.8k'
          }
        },
        'science-class-8': {
          id: 2,
          name: 'Science - Class 8',
          slug: 'science-class-8',
          description: 'Interactive science course with practical experiments and demonstrations for class 8 students.',
          fullDescription: 'Explore the fascinating world of science with our interactive course designed for class 8 students. This course covers physics, chemistry, and biology with engaging experiments, demonstrations, and real-world applications.',
          category: 'junior',
          type: 'regular',
          studentCount: 38,
          subjectCount: 12,
          rating: '4.7',
          duration: '25 hours',
          level: 'Beginner',
          teacher: {
            name: 'Sarah Johnson',
            avatar: '/assets/img/courses/course_author002.png',
            title: 'Science Educator',
            bio: 'Sarah is a passionate science educator with 8 years of experience. She specializes in making complex scientific concepts accessible and exciting for young learners.',
            students: '1.8k',
            courses: '8',
            reviews: '1.2k'
          }
        },
        'life-skills-program': {
          id: 3,
          name: 'Life Skills Program',
          slug: 'life-skills-program',
          description: 'Essential life skills training for personal and professional development.',
          fullDescription: 'This comprehensive life skills program covers essential skills needed for personal and professional success. Learn communication, problem-solving, time management, financial literacy, and emotional intelligence from industry experts.',
          category: 'life-skills',
          type: 'other',
          studentCount: 25,
          subjectCount: 8,
          rating: '4.9',
          duration: '20 hours',
          level: 'All Levels',
          fee: 99,
          teacher: {
            name: 'Mike Wilson',
            avatar: '/assets/img/courses/course_author003.png',
            title: 'Life Coach & Trainer',
            bio: 'Mike is a certified life coach with 15 years of experience in personal development training. He has helped thousands of individuals achieve their personal and professional goals.',
            students: '3.2k',
            courses: '15',
            reviews: '2.1k'
          }
        }
      };
      
      return mockCourses[slug] || mockCourses['mathematics-class-10'];
    },
    enrollCourse() {
      if (!this.$store.getters.isAuthenticated) {
        this.$router.push('/login');
        return;
      }
      
      if (this.course.fee) {
        // Redirect to payment page or show payment modal
        console.log('Redirecting to payment for course:', this.course.name);
      } else {
        // Enroll directly for free courses
        this.isEnrolled = true;
        this.$toast.success('Successfully enrolled in the course!');
      }
    }
  }
}
</script>

<style scoped>
.course-hero-area {
  background-size: cover;
  background-position: center;
  position: relative;
}

.course-hero-area::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(44, 62, 80, 0.9) 0%, rgba(52, 73, 94, 0.8) 100%);
}

.course-hero-content {
  position: relative;
  z-index: 2;
  color: white;
}

.breadcrumb {
  background: transparent;
  padding: 0;
  margin-bottom: 20px;
}

.breadcrumb-item a {
  color: #bdc3c7;
  text-decoration: none;
}

.breadcrumb-item.active {
  color: #ecf0f1;
}

.course-title {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: white;
}

.course-description {
  font-size: 1.2rem;
  margin-bottom: 30px;
  color: #ecf0f1;
}

.course-meta {
  display: flex;
  gap: 30px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 15px;
}

.meta-icon {
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.meta-icon i {
  font-size: 1.2rem;
  color: #e74c3c;
}

.meta-content span {
  display: block;
  font-size: 0.9rem;
  color: #bdc3c7;
}

.meta-content strong {
  display: block;
  font-size: 1.2rem;
  color: white;
}

.course-actions {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.btn {
  padding: 12px 30px;
  border-radius: 5px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #e74c3c;
  color: white;
}

.btn-primary:hover {
  background: #c0392b;
  transform: translateY(-2px);
}

.btn-success {
  background: #27ae60;
  color: white;
}

.btn-outline {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.btn-outline:hover {
  background: white;
  color: #2c3e50;
}

.course-hero-image {
  position: relative;
  text-align: center;
}

.course-hero-image img {
  max-width: 100%;
  border-radius: 10px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.course-badge {
  position: absolute;
  top: 20px;
  right: 20px;
}

.badge {
  background: #e74c3c;
  color: white;
  padding: 8px 15px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.8rem;
}

/* Course Tabs */
.course-tabs {
  background: white;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.nav-tabs {
  border-bottom: 1px solid #e9ecef;
  padding: 0 20px;
}

.nav-link {
  border: none;
  padding: 20px 25px;
  color: #6c757d;
  font-weight: 600;
  background: transparent;
}

.nav-link.active {
  color: #e74c3c;
  border-bottom: 3px solid #e74c3c;
}

.tab-content {
  padding: 30px;
}

/* Overview Styles */
.learn-list {
  list-style: none;
  padding: 0;
}

.learn-list li {
  padding: 8px 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.learn-list li i {
  color: #27ae60;
}

/* Curriculum Styles */
.accordion-button {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
}

.module-meta {
  font-size: 0.9rem;
  color: #6c757d;
  font-weight: normal;
}

.lesson-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.lesson-list li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f8f9fa;
}

.lesson-list li:last-child {
  border-bottom: none;
}

.lesson-list li.locked {
  opacity: 0.6;
}

.lesson-info {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
}

.lesson-title {
  flex: 1;
}

.lesson-duration {
  color: #6c757d;
  font-size: 0.9rem;
}

.lesson-preview {
  background: #e74c3c;
  color: white;
  padding: 2px 8px;
  border-radius: 3px;
  font-size: 0.8rem;
}

/* Instructor Styles */
.instructor-profile {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}

.instructor-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
}

.instructor-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.instructor-stats {
  display: flex;
  gap: 20px;
  margin-top: 15px;
}

.stat {
  text-align: center;
}

.stat strong {
  display: block;
  font-size: 1.2rem;
  color: #2c3e50;
}

.stat span {
  font-size: 0.9rem;
  color: #6c757d;
}

/* Reviews Styles */
.rating-overview {
  display: flex;
  gap: 40px;
  align-items: center;
  padding: 30px;
  background: #f8f9fa;
  border-radius: 10px;
  margin-bottom: 30px;
}

.overview-rating {
  text-align: center;
}

.overview-rating h2 {
  font-size: 3rem;
  color: #2c3e50;
  margin: 0;
}

.rating-bars {
  flex: 1;
}

.rating-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
}

.bar {
  flex: 1;
  height: 8px;
  background: #e9ecef;
  border-radius: 4px;
  overflow: hidden;
}

.fill {
  height: 100%;
  background: #f39c12;
}

.review-item {
  display: flex;
  gap: 20px;
  padding: 20px 0;
  border-bottom: 1px solid #e9ecef;
}

.reviewer-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  overflow: hidden;
}

.reviewer-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 10px;
  flex-wrap: wrap;
}

.review-date {
  color: #6c757d;
  font-size: 0.9rem;
}

/* Sidebar Styles */
.course-sidebar {
  position: sticky;
  top: 100px;
}

.sidebar-widget {
  background: white;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  padding: 25px;
  margin-bottom: 30px;
}

.features-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.features-list li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f8f9fa;
}

.features-list li:last-child {
  border-bottom: none;
}

.features-list li i {
  color: #e74c3c;
  width: 20px;
}

.price-card {
  text-align: center;
}

.price-header {
  margin-bottom: 20px;
}

.price-amount {
  font-size: 2.5rem;
  font-weight: 700;
  color: #e74c3c;
  margin: 10px 0;
}

.price-features {
  list-style: none;
  padding: 0;
  margin: 20px 0;
  text-align: left;
}

.price-features li {
  padding: 8px 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.price-features li i {
  color: #27ae60;
}

.btn-block {
  width: 100%;
  justify-content: center;
}

.share-buttons {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.share-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-decoration: none;
  transition: transform 0.3s ease;
}

.share-btn:hover {
  transform: translateY(-3px);
}

.facebook { background: #3b5998; }
.twitter { background: #1da1f2; }
.linkedin { background: #0077b5; }
.whatsapp { background: #25d366; }

.loading-section {
  padding: 100px 0;
  text-align: center;
}

@media (max-width: 768px) {
  .course-meta {
    flex-direction: column;
    gap: 15px;
  }
  
  .course-actions {
    flex-direction: column;
  }
  
  .instructor-profile {
    flex-direction: column;
    text-align: center;
  }
  
  .rating-overview {
    flex-direction: column;
    text-align: center;
  }
}
</style>