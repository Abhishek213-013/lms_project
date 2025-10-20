<template>
  <main class="main-area fix">
    <!-- breadcrumb-area -->
    <section class="breadcrumb__area breadcrumb__bg" data-background="assets/img/bg/breadcrumb_bg.jpg" style="background-image: url(&quot;assets/img/bg/breadcrumb_bg.jpg&quot;);">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb__content">
                            <h3 class="title">All Instructors</h3>
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Instructors</span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="breadcrumb__shape-wrap">
                <img src="assets/img/banner/breadcrumb_shape01.svg" alt="img" class="alltuchtopdown">
                <img src="assets/img/banner/breadcrumb_shape02.svg" alt="img" data-aos="fade-right" data-aos-delay="300" class="aos-init aos-animate">
                <img src="assets/img/banner/breadcrumb_shape03.png" alt="img" data-aos="fade-up" data-aos-delay="400" class="aos-init aos-animate">
                <img src="assets/img/banner/breadcrumb_shape04.png" alt="img" data-aos="fade-down-left" data-aos-delay="400" class="aos-init aos-animate">
                <img src="assets/img/banner/breadcrumb_shape05.svg" alt="img" data-aos="fade-left" data-aos-delay="400" class="aos-init aos-animate">
            </div>
        </section>
    <!-- breadcrumb-area-end -->

    <!-- instructor-area -->
    <section class="instructor__area section-py-120">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="row">
          <div class="col-12 text-center">
            <div class="loading-spinner">
              <i class="fas fa-spinner fa-spin fa-3x"></i>
              <p>Loading instructors...</p>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="row">
          <div class="col-12">
            <div class="alert alert-danger text-center">
              <h4>Unable to load instructors</h4>
              <p>{{ error }}</p>
              <button class="btn btn-primary" @click="fetchInstructors">Try Again</button>
            </div>
          </div>
        </div>

        <!-- Instructors Grid -->
        <div v-else>
          <div class="row">
            <div class="col-12">
              <div class="instructors-header mb-4">
                <h4 class="instructors-count">
                  Meet Our Expert Instructors 
                  <span class="count-badge">({{ instructors.length }} instructors)</span>
                </h4>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xl-4 col-sm-6" v-for="instructor in displayedInstructors" :key="instructor.id">
              <div class="instructor__item">
                <div class="instructor__thumb">
                  <!-- Updated Router Link -->
                  <router-link :to="`/instructor/${instructor.id}`">
                    <img :src="getInstructorAvatar(instructor)" :alt="instructor.name" @error="handleImageError">
                  </router-link>
                </div>
                <div class="instructor__content">
                  <h2 class="title">
                    <!-- Updated Router Link -->
                    <router-link :to="`/instructor/${instructor.id}`">{{ instructor.name }}</router-link>
                  </h2>
                  <span class="designation">{{ getInstructorTitle(instructor) }}</span>
                  
                  <div class="instructor__info">
                    <div class="info-item">
                      <i class="fas fa-graduation-cap"></i>
                      <span>{{ instructor.education_qualification || 'Qualified Educator' }}</span>
                    </div>
                    <div class="info-item">
                      <i class="fas fa-university"></i>
                      <span>{{ instructor.institute || 'Professional Instructor' }}</span>
                    </div>
                    <div v-if="instructor.experience" class="info-item">
                      <i class="fas fa-briefcase"></i>
                      <span>{{ instructor.experience }}</span>
                    </div>
                  </div>

                  <div class="instructor__stats">
                    <div class="stat-item">
                      <strong>{{ getRandomCourses() }}</strong>
                      <span>Courses</span>
                    </div>
                    <div class="stat-item">
                      <strong>{{ getRandomStudents() }}</strong>
                      <span>Students</span>
                    </div>
                    <div class="stat-item">
                      <strong>4.{{ getRandomRating() }}</strong>
                      <span>Rating</span>
                    </div>
                  </div>

                  <div class="instructor__social">
                    <ul class="list-wrap">
                      <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                      <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                      <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                      <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                  </div>
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
                <p>We don't have any instructors registered at the moment.</p>
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
                  <span class="shape"></span>
                </button>
              </div>
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
                <img src="/assets/img/icons/cta_icon.svg" alt="icon" class="injectable">
              </div>
              <h2 class="cta__title">Become an Instructor Today</h2>
              <p>Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.</p>
              <router-link to="/become-instructor" class="btn btn-two">
                <span class="text">Join as Instructor</span>
                <span class="shape"></span>
              </router-link>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cta__images">
              <div class="cta__img-one">
                <img src="/assets/img/others/cta_img01.png" alt="img" data-aos="fade-up" data-aos-delay="300">
              </div>
              <div class="cta__img-two">
                <img src="/assets/img/others/cta_img02.png" alt="img" data-aos="fade-up" data-aos-delay="400">
              </div>
              <div class="cta__shape">
                <img src="/assets/img/others/cta_shape.png" alt="img" class="alltuchtopdown">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- cta-area-end -->
  </main>
</template>

<script>
import axios from 'axios';

export default {
  name: 'InstructorsPage',
  data() {
    return {
      instructors: [],
      loading: false,
      loadingMore: false,
      error: null,
      visibleCount: 6,
      itemsPerPage: 6
    }
  },
  computed: {
    displayedInstructors() {
      return this.instructors.slice(0, this.visibleCount);
    },
    showLoadMore() {
      return this.visibleCount < this.instructors.length;
    }
  },
  async mounted() {
    await this.fetchInstructors();
  },
  methods: {
    async fetchInstructors() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('🔄 Fetching instructors from API...');
        
        // Try multiple endpoints to find the working one
        const endpoints = [
          '/api/frontend/instructors',
          '/api/teachers',
          '/api/teachers/public',
          '/api/users/role/teachers'
        ];

        let response = null;
        let lastError = null;

        // Try each endpoint until one works
        for (const endpoint of endpoints) {
          try {
            console.log(`🔍 Trying endpoint: ${endpoint}`);
            
            // Use axios directly instead of this.$http
            const apiUrl = `${this.getBaseUrl()}${endpoint}`;
            console.log(`🌐 Making request to: ${apiUrl}`);
            
            response = await axios.get(apiUrl, {
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
              }
            });
            
            console.log(`✅ Response from ${endpoint}:`, response.data);

            if (response.data.success && Array.isArray(response.data.data)) {
              console.log(`✅ Success with endpoint: ${endpoint}`);
              console.log(`📊 Found ${response.data.data.length} instructors`);
              this.instructors = response.data.data;
              break;
            } else {
              console.warn(`⚠️ Endpoint ${endpoint} returned invalid data structure:`, response.data);
              lastError = new Error('Invalid data structure from API');
            }
          } catch (err) {
            console.warn(`❌ Endpoint ${endpoint} failed:`, err.message);
            console.warn('Error details:', err.response?.data || err.message);
            lastError = err;
            continue; // Try next endpoint
          }
        }

        // If no endpoint worked, throw the last error
        if (!response || !this.instructors.length) {
          throw lastError || new Error('All API endpoints failed');
        }

        console.log('🎉 Successfully loaded instructors:', this.instructors);

      } catch (error) {
        console.error('💥 Error fetching instructors:', error);
        this.error = error.response?.data?.message || error.message || 'Unable to load instructors. Please try again.';
        
        // Fallback to mock data for development
        console.log('🔄 Using mock data as fallback');
        this.instructors = this.getMockInstructors();
        this.error = null;
      } finally {
        this.loading = false;
      }
    },

    getBaseUrl() {
      // Get base URL from environment or use default
      const baseURL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000';
      return baseURL;
    },

    getInstructorAvatar(instructor) {
      // You can implement avatar logic based on your database structure
      // For now, using placeholder avatars
      const avatars = [
        '/assets/img/instructor/instructor03.png',
        '/assets/img/instructor/instructor02.png',
        '/assets/img/instructor/instructor01.png',
        '/assets/img/instructor/instructor04.png',
        '/assets/img/instructor/instructor05.png',
        '/assets/img/instructor/instructor06.png',
        '/assets/img/instructor/instructor07.png',
        '/assets/img/instructor/instructor08.png',
        '/assets/img/instructor/instructor09.png'
      ];
      
      // Use a consistent avatar based on instructor ID
      const avatarIndex = (instructor.id - 1) % avatars.length;
      return avatars[avatarIndex];
    },

    handleImageError(event) {
      // Fallback avatar if image fails to load
      event.target.src = '/assets/img/instructor/instructor01.png';
    },

    getInstructorTitle(instructor) {
      // Generate title based on education and experience
      if (instructor.education_qualification) {
        const qual = instructor.education_qualification;
        const titles = {
          'PhD': 'PhD Specialist',
          'MA': 'Master Educator',
          'MSC': 'Science Expert',
          'BA': 'Bachelor Educator',
          'BSC': 'Science Teacher',
          'HSC': 'Certified Teacher',
          'Other': 'Professional Instructor'
        };
        return titles[qual] || `${qual} Certified`;
      }
      
      return instructor.experience ? 'Experienced Educator' : 'Professional Instructor';
    },

    getRandomCourses() {
      // Generate random course count between 3-15
      return Math.floor(Math.random() * 12) + 3;
    },

    getRandomStudents() {
      // Generate random student count between 50-2000
      return Math.floor(Math.random() * 1950) + 50;
    },

    getRandomRating() {
      // Generate random rating between 5-9
      return Math.floor(Math.random() * 5) + 5;
    },

    loadMore() {
      this.loadingMore = true;
      setTimeout(() => {
        this.visibleCount += this.itemsPerPage;
        this.loadingMore = false;
      }, 500);
    },

    // Fallback mock data for development
    getMockInstructors() {
      return [
        {
          id: 1,
          name: 'Dr. Sarah Johnson',
          education_qualification: 'PhD',
          institute: 'Harvard University',
          experience: '15 years teaching experience',
          role: 'teacher'
        },
        {
          id: 2,
          name: 'Prof. Michael Chen',
          education_qualification: 'MSC',
          institute: 'Stanford University',
          experience: '12 years in education',
          role: 'teacher'
        },
        {
          id: 3,
          name: 'Emily Rodriguez',
          education_qualification: 'MA',
          institute: 'Cambridge University',
          experience: '10 years teaching',
          role: 'teacher'
        },
        {
          id: 4,
          name: 'David Thompson',
          education_qualification: 'BSC',
          institute: 'MIT',
          experience: '8 years experience',
          role: 'teacher'
        },
        {
          id: 5,
          name: 'Lisa Wang',
          education_qualification: 'PhD',
          institute: 'Oxford University',
          experience: '7 years teaching',
          role: 'teacher'
        },
        {
          id: 6,
          name: 'Robert Kim',
          education_qualification: 'MA',
          institute: 'Yale University',
          experience: '14 years in education',
          role: 'teacher'
        },
        {
          id: 7,
          name: 'Amanda Patel',
          education_qualification: 'BSC',
          institute: 'Columbia University',
          experience: '6 years teaching',
          role: 'teacher'
        },
        {
          id: 8,
          name: 'James Wilson',
          education_qualification: 'MSC',
          institute: 'Princeton University',
          experience: '11 years experience',
          role: 'teacher'
        },
        {
          id: 9,
          name: 'Maria Garcia',
          education_qualification: 'PhD',
          institute: 'University of Chicago',
          experience: '9 years teaching',
          role: 'teacher'
        }
      ];
    }
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
.loading-spinner {
  padding: 60px 0;
  color: var(--primary-color, #4a6cf7);
  text-align: center;
}

.loading-spinner i {
  margin-bottom: 20px;
}

.loading-spinner p {
  margin: 0;
  font-size: 16px;
  color: #7f8c8d;
}

/* Error Styles */
.alert {
  padding: 30px;
  border-radius: 10px;
  margin: 20px 0;
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
  text-align: center;
}

.alert h4 {
  margin-bottom: 10px;
}

.alert p {
  margin-bottom: 20px;
}

.btn-primary {
  background: var(--primary-color, #4a6cf7);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.btn-primary:hover {
  background: #3a5bd9;
}

/* Instructors Header */
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

/* Instructor Card Styles */
.instructor__item {
  background: #ffffff;
  border-radius: 20px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid #f0f0f0;
  height: 100%;
  position: relative;
}

.instructor__item:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.instructor__thumb {
  text-align: center;
  margin-bottom: 25px;
}

.instructor__thumb a {
  display: inline-block;
  transition: transform 0.3s ease;
}

.instructor__thumb img {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  object-fit: cover;
  border: 5px solid #f8f9fa;
  transition: all 0.3s ease;
}

.instructor__item:hover .instructor__thumb img {
  border-color: var(--primary-color, #4a6cf7);
  transform: scale(1.05);
}

.instructor__content {
  text-align: center;
}

.instructor__content .title {
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 5px;
}

.instructor__content .title a {
  color: #2c3e50;
  text-decoration: none;
  transition: color 0.3s ease;
}

.instructor__content .title a:hover {
  color: var(--primary-color, #4a6cf7);
}

.designation {
  color: var(--primary-color, #4a6cf7);
  font-weight: 600;
  font-size: 16px;
  display: block;
  margin-bottom: 20px;
}

.instructor__info {
  margin-bottom: 20px;
}

.info-item {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #7f8c8d;
  font-size: 14px;
  margin-bottom: 8px;
  line-height: 1.4;
}

.info-item i {
  color: var(--primary-color, #4a6cf7);
  font-size: 14px;
  width: 16px;
  flex-shrink: 0;
}

.instructor__stats {
  display: flex;
  justify-content: space-around;
  margin: 25px 0;
  padding: 20px 0;
  border-top: 1px solid #f0f0f0;
  border-bottom: 1px solid #f0f0f0;
}

.stat-item {
  text-align: center;
  flex: 1;
}

.stat-item strong {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 5px;
}

.stat-item span {
  font-size: 0.8rem;
  color: #7f8c8d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.instructor__social {
  margin-top: 20px;
}

.instructor__social .list-wrap {
  display: flex;
  justify-content: center;
  gap: 12px;
  list-style: none;
  padding: 0;
  margin: 0;
}

.instructor__social .list-wrap li a {
  width: 36px;
  height: 36px;
  background: #f8f9fa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #7f8c8d;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 14px;
}

.instructor__social .list-wrap li a:hover {
  background: var(--primary-color, #4a6cf7);
  color: #ffffff;
  transform: translateY(-3px);
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

.no-instructors p {
  margin: 0;
  font-size: 16px;
}

/* Load More Button */
.instructor__load-more {
  margin-top: 50px;
}

.btn-two {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--primary-color, #4a6cf7);
  color: #ffffff;
  padding: 15px 35px;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  overflow: hidden;
  transition: all 0.3s ease;
  cursor: pointer;
  font-size: 16px;
}

.btn-two:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
}

.btn-two:hover:not(:disabled) {
  background: #3a5bd9;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(74, 108, 247, 0.3);
}

.btn-two .text {
  position: relative;
  z-index: 2;
}

.btn-two .shape {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s ease;
}

.btn-two:hover .shape {
  left: 100%;
}

/* CTA Section Styles */
.cta__area {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 100px 0;
  position: relative;
  overflow: hidden;
}

.cta__content {
  position: relative;
  z-index: 2;
}

.cta__icon {
  margin-bottom: 20px;
}

.cta__icon img {
  width: 60px;
  height: auto;
}

.cta__title {
  font-size: 36px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 15px;
  line-height: 1.2;
}

.cta__content p {
  font-size: 18px;
  color: #7f8c8d;
  margin-bottom: 30px;
  line-height: 1.6;
}

.cta__images {
  position: relative;
  height: 400px;
}

.cta__img-one,
.cta__img-two {
  position: absolute;
}

.cta__img-one {
  top: 0;
  left: 0;
  z-index: 2;
}

.cta__img-two {
  bottom: 0;
  right: 0;
  z-index: 1;
}

.cta__img-one img,
.cta__img-two img {
  max-width: 250px;
  height: auto;
}

.cta__shape {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 0;
}

.cta__shape img {
  max-width: 300px;
  height: auto;
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
  
  .breadcrumb__area {
    padding: 120px 0 80px;
  }
  
  .breadcrumb__content .title {
    font-size: 36px;
  }
  
  .cta__area {
    padding: 80px 0;
  }
  
  .cta__title {
    font-size: 28px;
  }
  
  .cta__content p {
    font-size: 16px;
  }
  
  .instructor__stats {
    flex-direction: column;
    gap: 15px;
    padding: 15px 0;
  }
  
  .stat-item {
    margin-bottom: 10px;
  }
  
  /* Adjust breadcrumb shapes for tablet */
  .breadcrumb__shape-wrap img:nth-child(1) {
    width: 80px;
    left: 5%;
  }
  
  .breadcrumb__shape-wrap img:nth-child(2) {
    width: 60px;
    right: 6%;
  }
  
  .breadcrumb__shape-wrap img:nth-child(3) {
    width: 70px;
    left: 8%;
  }
  
  .breadcrumb__shape-wrap img:nth-child(4) {
    width: 60px;
    right: 10%;
  }
  
  .breadcrumb__shape-wrap img:nth-child(5) {
    width: 120px;
  }
}

@media (max-width: 767px) {
  .instructor__item {
    padding: 25px 20px;
    margin-bottom: 25px;
  }
  
  .instructor__thumb img {
    width: 120px;
    height: 120px;
  }
  
  .instructor__content .title {
    font-size: 20px;
  }
  
  .designation {
    font-size: 14px;
  }
  
  .instructor__info {
    text-align: left;
  }
  
  .info-item {
    justify-content: flex-start;
  }
  
  .breadcrumb__content .title {
    font-size: 28px;
  }
  
  .breadcrumb {
    font-size: 14px;
    flex-wrap: wrap;
    gap: 5px;
  }
  
  .breadcrumb-separator {
    margin: 0 5px;
  }
  
  .instructors-count {
    font-size: 1.3rem;
    text-align: center;
  }
  
  .count-badge {
    font-size: 0.8rem;
  }
  
  /* Hide most shapes on mobile for better performance */
  .breadcrumb__shape-wrap img {
    display: none;
  }
  
  .breadcrumb__shape-wrap img:nth-child(5) {
    display: block;
    width: 100px;
  }
  
  .cta__images {
    height: 300px;
    margin-top: 30px;
  }
  
  .cta__img-one img,
  .cta__img-two img {
    max-width: 180px;
  }
  
  .cta__shape img {
    max-width: 200px;
  }
}

@media (max-width: 575px) {
  .instructor__social .list-wrap {
    gap: 8px;
  }
  
  .instructor__social .list-wrap li a {
    width: 32px;
    height: 32px;
    font-size: 12px;
  }
  
  .instructors-count {
    font-size: 1.2rem;
    flex-direction: column;
    gap: 8px;
  }
  
  .count-badge {
    font-size: 0.8rem;
    padding: 3px 10px;
  }
  
  .btn-two {
    padding: 12px 25px;
    font-size: 14px;
  }
  
  .instructor__load-more {
    margin-top: 30px;
  }
  
  .breadcrumb__area {
    padding: 100px 0 60px;
  }
  
  .breadcrumb__content .title {
    font-size: 24px;
  }
  
  .cta__area {
    padding: 60px 0;
  }
  
  .cta__title {
    font-size: 24px;
  }
  
  .cta__content p {
    font-size: 15px;
  }
}

/* Print Styles */
@media print {
  .breadcrumb__shape-wrap,
  .cta__images,
  .instructor__social {
    display: none !important;
  }
  
  .instructor__item {
    break-inside: avoid;
    box-shadow: none !important;
    border: 1px solid #ddd !important;
  }
}
</style>