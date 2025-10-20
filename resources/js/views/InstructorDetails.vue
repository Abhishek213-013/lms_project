<template>
  <main class="main-area fix">
    <!-- instructor-details-area -->
    <section class="instructor__details-area section-pt-120 section-pb-90">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="row">
          <div class="col-12 text-center">
            <div class="loading-spinner">
              <i class="fas fa-spinner fa-spin fa-3x"></i>
              <p>Loading instructor details...</p>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="row">
          <div class="col-12">
            <div class="alert alert-danger text-center">
              <h4>Unable to load instructor details</h4>
              <p>{{ error }}</p>
              <button class="btn btn-primary" @click="fetchInstructorData">Try Again</button>
            </div>
          </div>
        </div>

        <!-- Instructor Details Content -->
        <div v-else class="row">
          <!-- Sidebar - Profile, Experience, Education -->
          <div class="col-xl-4 col-lg-5">
            <div class="instructor__sidebar">
              <!-- Teacher Profile Card -->
              <div class="sidebar-card profile-card">
                <div class="profile-header">
                  <div class="profile-avatar">
                    <img :src="getInstructorAvatar(instructor)" :alt="instructor.name" @error="handleImageError">
                  </div>
                  <div class="profile-info">
                    <h2 class="title">{{ instructor.name }}</h2>
                    <span class="designation">{{ getInstructorTitle(instructor) }}</span>
                    <div class="rating">
                      <i class="fas fa-star"></i>
                      <span>{{ instructor.rating || '4.8' }} ({{ instructor.reviews || '128' }} reviews)</span>
                    </div>
                  </div>
                </div>
                
                <div class="profile-stats">
                  <div class="stat-item">
                    <div class="stat-icon">
                      <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-info">
                      <span class="stat-number">{{ instructorStats.totalClasses }}</span>
                      <span class="stat-label">Total Classes</span>
                    </div>
                  </div>
                  <div class="stat-item">
                    <div class="stat-icon">
                      <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                      <span class="stat-number">{{ instructorStats.totalStudents }}+</span>
                      <span class="stat-label">Students Taught</span>
                    </div>
                  </div>
                  <div class="stat-item">
                    <div class="stat-icon">
                      <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                      <span class="stat-number">{{ instructor.rating || '4.8' }}</span>
                      <span class="stat-label">Rating</span>
                    </div>
                  </div>
                </div>
                
                <div class="profile-details">
                  <div class="detail-item">
                    <label><i class="fas fa-user-graduate"></i> Expertise</label>
                    <span>{{ getExpertiseAreas() }}</span>
                  </div>
                  <div class="detail-item">
                    <label><i class="fas fa-language"></i> Languages</label>
                    <span>{{ instructor.languages || 'English, Spanish' }}</span>
                  </div>
                  <div class="detail-item">
                    <label><i class="fas fa-clock"></i> Response Time</label>
                    <span>{{ instructor.response_time || 'Within 24 hours' }}</span>
                  </div>
                  <div class="detail-item">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <span><a :href="`mailto:${instructor.email}`">{{ instructor.email }}</a></span>
                  </div>
                  <div class="detail-item">
                    <label><i class="fas fa-phone"></i> Phone</label>
                    <span><a :href="`tel:${instructor.phone || '+1239500600'}`">{{ instructor.phone || '+123 9500 600' }}</a></span>
                  </div>
                </div>
                
                <div class="profile-bio">
                  <p>{{ instructor.bio || 'Experienced educator with a passion for teaching and student success.' }}</p>
                </div>
                
                <div class="profile-social">
                  <h5>Follow Instructor</h5>
                  <ul class="list-wrap">
                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                    <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                  </ul>
                </div>
              </div>

              <!-- Experience Card -->
              <div class="sidebar-card experience-card">
                <h4 class="title">Teaching Experience</h4>
                <div class="experience-timeline">
                  <div 
                    v-for="exp in instructor.experience" 
                    :key="exp.id"
                    class="experience-item"
                  >
                    <div class="exp-period">{{ exp.period }}</div>
                    <div class="exp-dot"></div>
                    <div class="exp-content">
                      <h6>{{ exp.position }}</h6>
                      <p class="exp-company">{{ exp.company }}</p>
                      <p class="exp-description">{{ exp.description }}</p>
                    </div>
                  </div>
                  
                  <!-- Fallback experience items -->
                  <div v-if="!instructor.experience || instructor.experience.length === 0" class="experience-item">
                    <div class="exp-period">{{ formatExperiencePeriod(instructor.experience) }}</div>
                    <div class="exp-dot"></div>
                    <div class="exp-content">
                      <h6>{{ getInstructorTitle(instructor) }}</h6>
                      <p class="exp-company">{{ instructor.institute || 'Educational Institution' }}</p>
                      <p class="exp-description">Teaching {{ getExpertiseAreas() || 'various subjects' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Education Card -->
              <div class="sidebar-card education-card">
                <h4 class="title">Education & Certification</h4>
                <div class="education-list">
                  <div 
                    v-for="edu in instructor.education" 
                    :key="edu.id"
                    class="education-item"
                  >
                    <div class="edu-icon">
                      <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="edu-content">
                      <h6>{{ edu.degree }}</h6>
                      <p class="edu-institution">{{ edu.institution }}</p>
                      <p class="edu-year">{{ edu.year }}</p>
                      <p class="edu-details" v-if="edu.details">{{ edu.details }}</p>
                    </div>
                  </div>
                  
                  <!-- Fallback education items -->
                  <div v-if="!instructor.education || instructor.education.length === 0" class="education-item">
                    <div class="edu-icon">
                      <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="edu-content">
                      <h6>{{ instructor.education_qualification || 'Teaching Certification' }}</h6>
                      <p class="edu-institution">{{ instructor.institute || 'Educational Institution' }}</p>
                      <p class="edu-year">{{ new Date().getFullYear() - 5 }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quick Contact -->
              <div class="sidebar-card contact-card">
                <h4 class="title">Quick Contact</h4>
                <form @submit.prevent="submitContactForm" class="contact-form">
                  <div class="form-grp">
                    <input type="text" placeholder="Your Name" v-model="contactForm.name" required>
                  </div>
                  <div class="form-grp">
                    <input type="email" placeholder="Your Email" v-model="contactForm.email" required>
                  </div>
                  <div class="form-grp">
                    <input type="text" placeholder="Subject" v-model="contactForm.subject" required>
                  </div>
                  <div class="form-grp">
                    <textarea placeholder="Your Message" v-model="contactForm.message" required></textarea>
                  </div>
                  <button type="submit" class="btn arrow-btn" :disabled="contactSubmitting">
                    <span v-if="!contactSubmitting">Send Message</span>
                    <span v-else>Sending...</span>
                    <i class="fas fa-paper-plane"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
          
          <!-- Main Content - Demo Class Videos, Classes, Assignments -->
          <div class="col-xl-8 col-lg-7">
            <div class="instructor__details-wrap">
              <!-- Demo Class Videos Section -->
              <div class="demo-videos-section">
                <div class="section-header">
                  <h3 class="main-title">Demo Class Videos</h3>
                  <p>Watch sample classes to experience teaching style</p>
                </div>

                <!-- Video Categories Navigation -->
                <div class="video-categories-nav">
                  <button 
                    v-for="category in videoCategories" 
                    :key="category.id"
                    @click="setActiveCategory(category.id)"
                    :class="['category-btn', { 'active': activeCategory === category.id }]"
                  >
                    {{ category.name }}
                  </button>
                </div>

                <!-- Videos Grid -->
                <div class="videos-grid">
                  <div 
                    v-for="video in filteredVideos" 
                    :key="video.id"
                    class="video-card"
                    @click="openVideoModal(video)"
                  >
                    <div class="video-thumbnail">
                      <img :src="video.thumbnail" :alt="video.title">
                      <div class="video-overlay">
                        <div class="play-button">
                          <i class="fas fa-play"></i>
                        </div>
                        <div class="video-duration">{{ video.duration }}</div>
                      </div>
                    </div>
                    <div class="video-content">
                      <h5 class="video-title">{{ video.title }}</h5>
                      <div class="video-meta">
                        <span class="video-class">{{ video.class }}</span>
                        <span class="video-date">{{ formatDate(video.created_at) }}</span>
                      </div>
                      <p class="video-description">{{ video.description }}</p>
                      <div class="video-stats">
                        <span><i class="far fa-eye"></i> {{ video.views || '0' }} views</span>
                        <span><i class="far fa-thumbs-up"></i> {{ video.likes || '0' }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Load More Button -->
                <div v-if="showLoadMore" class="text-center mt-5">
                  <button class="btn btn-primary" @click="loadMoreVideos">
                    Load More Videos
                  </button>
                </div>

                <!-- No Videos Message -->
                <div v-if="filteredVideos.length === 0" class="text-center py-8">
                  <i class="fas fa-video-slash fa-3x text-gray-400 mb-4"></i>
                  <h4 class="text-gray-600">No demo videos available</h4>
                  <p class="text-gray-500">This instructor hasn't uploaded any demo videos yet.</p>
                </div>
              </div>

              <!-- Classes Taught Section -->
              <div class="classes-section" v-if="teacherClasses.length > 0">
                <div class="section-header">
                  <h3 class="main-title">Classes Taught</h3>
                  <p>Subjects and classes currently being taught</p>
                </div>
                <div class="classes-grid">
                  <div 
                    v-for="classItem in teacherClasses" 
                    :key="classItem.id"
                    class="class-card"
                  >
                    <div class="class-header">
                      <h5 class="class-name">{{ classItem.name }}</h5>
                      <span :class="`class-status ${classItem.status}`">
                        {{ classItem.status }}
                      </span>
                    </div>
                    <div class="class-details">
                      <div class="class-subject">
                        <i class="fas fa-book"></i>
                        <span>{{ classItem.subject }}</span>
                      </div>
                      <div class="class-grade" v-if="classItem.grade">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Grade {{ classItem.grade }}</span>
                      </div>
                      <div class="class-students">
                        <i class="fas fa-users"></i>
                        <span>{{ classItem.studentCount || classItem.students_count || 0 }} students</span>
                      </div>
                    </div>
                    <div class="class-code" v-if="classItem.code">
                      <i class="fas fa-code"></i>
                      <span>Code: {{ classItem.code }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Recent Assignments -->
              <div class="assignments-section" v-if="assignments.length > 0">
                <div class="section-header">
                  <h3 class="main-title">Recent Assignments</h3>
                  <p>Latest assignments created by this instructor</p>
                </div>
                <div class="assignments-grid">
                  <div 
                    v-for="assignment in assignments" 
                    :key="assignment.id"
                    class="assignment-card"
                  >
                    <div class="assignment-header">
                      <h5 class="assignment-title">{{ assignment.title }}</h5>
                      <span :class="`assignment-status ${assignment.status}`">
                        {{ assignment.status }}
                      </span>
                    </div>
                    <p class="assignment-description">{{ assignment.description }}</p>
                    <div class="assignment-meta">
                      <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Due: {{ formatDate(assignment.due_date) }}</span>
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-star"></i>
                        <span>Points: {{ assignment.points }}</span>
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-book"></i>
                        <span>{{ assignment.class?.name || 'General' }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Teaching Philosophy -->
              <div class="instructor__details-biography">
                <h4 class="title">Teaching Philosophy</h4>
                <p>{{ instructor.teaching_philosophy || 'Dorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.dolor sit amet, consectetur adipiscing elited do eiusmod tempor incididunt ut labore et dolore magna aliqua.' }}</p>
                <p>Dorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt utte labore et dolore magna aliquauis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- instructor-details-area-end -->

    <!-- Video Modal -->
    <div v-if="selectedVideo" class="video-modal" @click="closeVideoModal">
      <div class="modal-content" @click.stop>
        <button class="close-modal" @click="closeVideoModal">
          <i class="fas fa-times"></i>
        </button>
        <div class="video-container">
          <iframe 
            :src="selectedVideo.videoUrl" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen>
          </iframe>
        </div>
        <div class="video-info">
          <h4>{{ selectedVideo.title }}</h4>
          <p>{{ selectedVideo.description }}</p>
          <div class="video-meta-modal">
            <span><i class="far fa-clock"></i> {{ selectedVideo.duration }}</span>
            <span><i class="far fa-calendar"></i> {{ formatDate(selectedVideo.created_at) }}</span>
            <span><i class="far fa-eye"></i> {{ selectedVideo.views || '0' }} views</span>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import axios from 'axios';

export default {
  name: 'InstructorDetailsPage',
  data() {
    return {
      instructor: {},
      loading: false,
      error: null,
      contactForm: {
        name: '',
        email: '',
        subject: '',
        message: ''
      },
      contactSubmitting: false,
      activeCategory: 'all',
      selectedVideo: null,
      videos: [],
      assignments: [],
      teacherClasses: [],
      videoCategories: [
        { id: 'all', name: 'All Videos' },
        { id: 'mathematics', name: 'Mathematics' },
        { id: 'physics', name: 'Physics' },
        { id: 'programming', name: 'Programming' },
        { id: 'science', name: 'Science' }
      ],
      videosPerPage: 6,
      currentPage: 1
    }
  },
  computed: {
    instructorId() {
      return this.$route.params.id;
    },
    instructorStats() {
      // Calculate real stats from the data we have
      const totalClasses = this.teacherClasses.length;
      const totalStudents = this.teacherClasses.reduce((sum, classItem) => {
        return sum + (classItem.studentCount || classItem.students_count || 0);
      }, 0);
      
      return {
        totalClasses: totalClasses,
        totalStudents: totalStudents,
        totalResources: this.videos.length
      };
    },
    filteredVideos() {
      if (this.activeCategory === 'all') {
        return this.videos.slice(0, this.videosPerPage * this.currentPage);
      }
      return this.videos
        .filter(video => video.category === this.activeCategory)
        .slice(0, this.videosPerPage * this.currentPage);
    },
    showLoadMore() {
      const totalVideos = this.activeCategory === 'all' 
        ? this.videos.length 
        : this.videos.filter(v => v.category === this.activeCategory).length;
      return this.filteredVideos.length < totalVideos;
    }
  },
  async mounted() {
    await this.fetchInstructorData();
  },
  methods: {
    async fetchInstructorData() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('🔄 Fetching instructor data for ID:', this.instructorId);
        
        // Fetch instructor basic info
        await this.fetchInstructor();
        
        // Fetch real classes data
        await this.fetchTeacherClasses();
        
        // Fetch real assignments data
        await this.fetchTeacherAssignments();
        
        // Initialize demo data for videos (since video API might still require auth)
        this.initializeDemoVideos();
        
        console.log('🎉 All instructor data loaded successfully');

      } catch (error) {
        console.error('💥 Error fetching instructor data:', error);
        this.error = error.message || 'Unable to load instructor details. Please try again.';
        
        // Fallback to mock data
        console.log('🔄 Using mock data as fallback');
        this.instructor = this.getMockInstructor();
        this.teacherClasses = this.getMockClasses();
        this.assignments = this.getMockAssignments();
        this.initializeDemoVideos();
        this.error = null;
      } finally {
        this.loading = false;
      }
    },

    async fetchInstructor() {
      try {
        console.log('👤 Fetching instructor details...');
        
        const response = await axios.get(`${this.getBaseUrl()}/api/frontend/instructors/${this.instructorId}`);
        
        if (response.data.success && response.data.data) {
          this.instructor = response.data.data;
          console.log('✅ Instructor details loaded:', this.instructor);
        } else {
          throw new Error('Invalid instructor data structure');
        }
      } catch (error) {
        console.error('❌ Error fetching instructor:', error);
        // Don't throw error here, we'll use mock data
        this.instructor = this.getMockInstructor();
      }
    },

    async fetchTeacherClasses() {
      try {
        console.log('🏫 Fetching teacher classes for ID:', this.instructorId);
        
        const token = localStorage.getItem('token');
        const headers = token ? { Authorization: `Bearer ${token}` } : {};
        
        const response = await axios.get(`${this.getBaseUrl()}/api/teachers/${this.instructorId}/classes`, { headers });
        
        if (response.data.success) {
          this.teacherClasses = response.data.data.map(classItem => ({
            id: classItem.id,
            name: classItem.name,
            subject: classItem.subject,
            grade: classItem.grade,
            code: classItem.code,
            status: classItem.status,
            studentCount: classItem.studentCount || classItem.students_count || 0,
            capacity: classItem.capacity,
            description: classItem.description,
            type: classItem.type,
            category: classItem.category
          }));
          
          console.log(`✅ Loaded ${this.teacherClasses.length} classes:`, this.teacherClasses);
        } else {
          console.warn('⚠️ No classes found or invalid response structure');
          this.teacherClasses = this.getMockClasses();
        }
      } catch (error) {
        console.error('❌ Error fetching teacher classes:', error);
        if (error.response?.status === 401) {
          console.log('🔐 API requires authentication, using mock data');
        }
        // Fallback to mock classes
        this.teacherClasses = this.getMockClasses();
      }
    },

    async fetchTeacherAssignments() {
      try {
        console.log('📚 Fetching teacher assignments...');
        
        // If we have real classes, try to fetch assignments for them
        if (this.teacherClasses.length > 0 && this.teacherClasses[0].id) {
          const token = localStorage.getItem('token');
          const headers = token ? { Authorization: `Bearer ${token}` } : {};
          
          // Try to get assignments for the first class as a sample
          const classId = this.teacherClasses[0].id;
          const response = await axios.get(`${this.getBaseUrl()}/api/classes/${classId}/assignments`, { headers });
          
          if (response.data.success) {
            this.assignments = response.data.data.slice(0, 4); // Show only first 4 assignments
            console.log(`✅ Loaded ${this.assignments.length} assignments from class ${classId}`);
            return;
          }
        }
        
        // If no real assignments, use mock data
        console.log('📚 Using mock assignments data');
        this.assignments = this.getMockAssignments();
        
      } catch (error) {
        console.error('❌ Error fetching teacher assignments:', error);
        if (error.response?.status === 401) {
          console.log('🔐 Assignments API requires authentication, using mock data');
        }
        // Fallback to mock assignments
        this.assignments = this.getMockAssignments();
      }
    },

    getExpertiseAreas() {
      if (this.instructor.expertise) {
        return this.instructor.expertise;
      }
      
      // Generate expertise from subjects taught
      if (this.teacherClasses.length > 0) {
        const subjects = [...new Set(this.teacherClasses.map(classItem => classItem.subject))];
        return subjects.join(', ');
      }
      
      return 'Mathematics, Physics, Computer Science';
    },

    initializeDemoVideos() {
      console.log('📹 Using demo videos data');
      this.videos = [
        {
          id: 1,
          title: 'Advanced Calculus - Limits and Derivatives',
          description: 'Complete introduction to calculus concepts with practical examples',
          thumbnail: '/assets/img/courses/video_thumb01.jpg',
          videoUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
          duration: '45:30',
          class: 'Mathematics - Grade 12',
          created_at: '2024-01-15T10:00:00Z',
          views: '1.2K',
          likes: '245',
          category: 'mathematics'
        },
        {
          id: 2,
          title: 'Quantum Physics Fundamentals',
          description: 'Understanding the basics of quantum mechanics and wave-particle duality',
          thumbnail: '/assets/img/courses/video_thumb02.jpg',
          videoUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
          duration: '38:15',
          class: 'Physics - University Level',
          created_at: '2024-01-12T14:30:00Z',
          views: '890',
          likes: '156',
          category: 'physics'
        },
        {
          id: 3,
          title: 'Python Programming for Beginners',
          description: 'Learn Python from scratch with hands-on coding exercises',
          thumbnail: '/assets/img/courses/video_thumb03.jpg',
          videoUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
          duration: '52:10',
          class: 'Computer Science - Beginner',
          created_at: '2024-01-10T09:15:00Z',
          views: '2.1K',
          likes: '312',
          category: 'programming'
        }
      ];
    },

    getMockClasses() {
      // Fallback mock classes data
      return [
        {
          id: 1,
          name: 'Class 10A',
          subject: 'Mathematics',
          grade: 10,
          code: 'MATH10A',
          status: 'active',
          studentCount: 25,
          capacity: 30
        },
        {
          id: 2,
          name: 'Class 11B',
          subject: 'Physics',
          grade: 11,
          code: 'PHY11B',
          status: 'active',
          studentCount: 30,
          capacity: 35
        },
        {
          id: 3,
          name: 'Class 12',
          subject: 'Computer Science',
          grade: 12,
          code: 'CS12',
          status: 'active',
          studentCount: 20,
          capacity: 25
        }
      ];
    },

    getMockAssignments() {
      // Fallback mock assignments data
      return [
        {
          id: 1,
          title: 'Calculus Problem Set 1',
          description: 'Practice problems on limits and derivatives with step-by-step solutions',
          points: 100,
          due_date: '2024-02-15T23:59:00Z',
          status: 'active',
          class: { name: 'Advanced Mathematics' }
        },
        {
          id: 2,
          title: 'Physics Lab Report',
          description: 'Write a detailed report on the quantum mechanics experiment conducted in class',
          points: 85,
          due_date: '2024-02-10T23:59:00Z',
          status: 'active',
          class: { name: 'Quantum Physics' }
        },
        {
          id: 3,
          title: 'Programming Project',
          description: 'Create a simple Python application demonstrating object-oriented programming concepts',
          points: 120,
          due_date: '2024-02-20T23:59:00Z',
          status: 'active',
          class: { name: 'Computer Science' }
        }
      ];
    },

    setActiveCategory(categoryId) {
      this.activeCategory = categoryId;
      this.currentPage = 1;
    },

    openVideoModal(video) {
      this.selectedVideo = video;
      document.body.style.overflow = 'hidden';
    },

    closeVideoModal() {
      this.selectedVideo = null;
      document.body.style.overflow = 'auto';
    },

    loadMoreVideos() {
      this.currentPage++;
    },

    formatDate(dateString) {
      if (!dateString) return 'N/A';
      try {
        return new Date(dateString).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      } catch (error) {
        return 'Invalid Date';
      }
    },

    formatExperiencePeriod(experience) {
      if (!experience) return 'Present';
      
      let years = 0;
      if (typeof experience === 'string') {
        const match = experience.match(/(\d+)/);
        years = match ? parseInt(match[1]) : 0;
      } else if (typeof experience === 'number') {
        years = experience;
      }
      
      const startYear = new Date().getFullYear() - years;
      return `${startYear} - Present`;
    },

    getBaseUrl() {
      const baseURL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000';
      return baseURL;
    },

    getInstructorAvatar(instructor) {
      const avatars = [
        '/assets/img/instructor/instructor_details_thumb.jpg',
        '/assets/img/instructor/instructor01.png',
        '/assets/img/instructor/instructor02.png',
        '/assets/img/instructor/instructor03.png',
        '/assets/img/instructor/instructor04.png'
      ];
      
      const avatarIndex = (instructor.id - 1) % avatars.length;
      return avatars[avatarIndex];
    },

    handleImageError(event) {
      event.target.src = '/assets/img/instructor/instructor_details_thumb.jpg';
    },

    getInstructorTitle(instructor) {
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

    async submitContactForm() {
      this.contactSubmitting = true;
      
      try {
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        console.log('Contact form would be submitted:', {
          instructor_id: this.instructorId,
          ...this.contactForm
        });
        
        this.contactForm = {
          name: '',
          email: '',
          subject: '',
          message: ''
        };
        
        alert('Message sent successfully! (Demo mode)');
        
      } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
      } finally {
        this.contactSubmitting = false;
      }
    },

    getMockInstructor() {
      return {
        id: this.instructorId,
        name: 'Dr. Sarah Johnson',
        email: 'sarah.johnson@example.com',
        education_qualification: 'PhD',
        institute: 'Harvard University',
        experience: '15 years teaching experience',
        bio: 'Passionate educator with 15 years of experience in teaching mathematics and physics. Committed to student success and innovative teaching methods.',
        teaching_philosophy: 'I believe in creating an engaging learning environment where students feel empowered to explore and discover knowledge.',
        expertise: 'Mathematics, Physics, Computer Science',
        languages: 'English, Spanish, French',
        response_time: 'Within 12 hours',
        rating: '4.9',
        total_classes: 15,
        total_students: 420,
        classes_count: 15,
        experience: [
          {
            id: 1,
            period: '2018 - Present',
            position: 'Senior Mathematics Teacher',
            company: 'Prestige High School',
            description: 'Teaching advanced mathematics and mentoring students'
          }
        ],
        education: [
          {
            id: 1,
            degree: 'PhD in Mathematics Education',
            institution: 'Harvard University',
            year: '2015',
            details: 'Specialized in innovative teaching methodologies'
          }
        ]
      };
    }
  },
  watch: {
    '$route.params.id': {
      handler(newId) {
        if (newId) {
          this.fetchInstructorData();
        }
      },
      immediate: true
    }
  }
}
</script>

<style scoped>
/* Layout Styles */
.instructor__details-wrap {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* Sidebar Styles */
.instructor__sidebar {
  position: sticky;
  top: 100px;
}

.sidebar-card {
  background: #ffffff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  margin-bottom: 25px;
}

.sidebar-card .title {
  font-size: 18px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f8f9fa;
}

/* Profile Card Styles */
.profile-header {
  text-align: center;
  margin-bottom: 25px;
}

.profile-avatar {
  margin-bottom: 20px;
}

.profile-avatar img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 5px solid #f8f9fa;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.profile-info .title {
  font-size: 24px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 5px;
  border-bottom: none;
  padding-bottom: 0;
}

.designation {
  color: var(--primary-color, #4a6cf7);
  font-weight: 600;
  font-size: 16px;
  display: block;
  margin-bottom: 10px;
}

.rating {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #7f8c8d;
  font-size: 14px;
}

.rating i {
  color: #f39c12;
}

/* Profile Stats */
.profile-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
  margin-bottom: 25px;
}

.stat-item {
  text-align: center;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 10px;
}

.stat-icon {
  width: 40px;
  height: 40px;
  background: var(--primary-color, #4a6cf7);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 10px;
  color: #ffffff;
}

.stat-number {
  display: block;
  font-size: 18px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 12px;
  color: #7f8c8d;
}

/* Profile Details */
.profile-details {
  margin-bottom: 20px;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f8f9fa;
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-item label {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #7f8c8d;
  font-weight: 500;
  font-size: 14px;
}

.detail-item span {
  color: #2c3e50;
  font-weight: 600;
  font-size: 14px;
  text-align: right;
}

.detail-item a {
  color: #2c3e50;
  text-decoration: none;
  transition: color 0.3s ease;
}

.detail-item a:hover {
  color: var(--primary-color, #4a6cf7);
}

/* Profile Bio */
.profile-bio {
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 10px;
}

.profile-bio p {
  color: #7f8c8d;
  line-height: 1.6;
  margin: 0;
  font-size: 14px;
}

/* Profile Social */
.profile-social {
  text-align: center;
}

.profile-social h5 {
  font-size: 16px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 15px;
}

.profile-social .list-wrap {
  display: flex;
  justify-content: center;
  gap: 12px;
  list-style: none;
  padding: 0;
  margin: 0;
}

.profile-social .list-wrap li a {
  width: 40px;
  height: 40px;
  background: #f8f9fa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #7f8c8d;
  text-decoration: none;
  transition: all 0.3s ease;
}

.profile-social .list-wrap li a:hover {
  background: var(--primary-color, #4a6cf7);
  color: #ffffff;
  transform: translateY(-3px);
}

/* Main Content Sections */
.demo-videos-section,
.classes-section,
.assignments-section,
.instructor__details-biography {
  background: #ffffff;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.section-header {
  margin-bottom: 30px;
}

.section-header .main-title {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 10px;
}

.section-header p {
  color: #7f8c8d;
  margin-bottom: 0;
}

/* Video Categories Navigation */
.video-categories-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e9ecef;
}

.category-btn {
  padding: 10px 20px;
  border: 2px solid #e9ecef;
  border-radius: 25px;
  background: transparent;
  color: #7f8c8d;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.category-btn:hover,
.category-btn.active {
  background: var(--primary-color, #4a6cf7);
  border-color: var(--primary-color, #4a6cf7);
  color: #ffffff;
}

/* Videos Grid */
.videos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 25px;
  margin-bottom: 30px;
}

.video-card {
  background: #ffffff;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
}

.video-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.video-thumbnail {
  position: relative;
  overflow: hidden;
  height: 200px;
}

.video-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.video-card:hover .video-thumbnail img {
  transform: scale(1.1);
}

.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.video-card:hover .video-overlay {
  opacity: 1;
}

.play-button {
  width: 60px;
  height: 60px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary-color, #4a6cf7);
  font-size: 20px;
  transition: all 0.3s ease;
}

.play-button:hover {
  background: #ffffff;
  transform: scale(1.1);
}

.video-duration {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.7);
  color: #ffffff;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.video-content {
  padding: 20px;
}

.video-title {
  font-size: 16px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 10px;
  line-height: 1.4;
}

.video-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.video-class {
  background: var(--primary-color, #4a6cf7);
  color: #ffffff;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.video-date {
  color: #7f8c8d;
  font-size: 12px;
}

.video-description {
  color: #7f8c8d;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 15px;
  /* Fallback for line-clamp */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  /* Standard line-clamp property */
  display: -webkit-box;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.video-stats {
  display: flex;
  gap: 15px;
  font-size: 12px;
  color: #7f8c8d;
}

.video-stats span {
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Classes Grid */
.classes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.class-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  border-left: 4px solid var(--primary-color, #4a6cf7);
  transition: all 0.3s ease;
}

.class-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.class-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
}

.class-name {
  font-size: 16px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
  flex: 1;
  margin-right: 10px;
}

.class-status {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.class-status.active {
  background: #d1fae5;
  color: #065f46;
}

.class-status.inactive {
  background: #fef3c7;
  color: #92400e;
}

.class-status.upcoming {
  background: #dbeafe;
  color: #1e40af;
}

.class-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 15px;
}

.class-subject,
.class-grade,
.class-students,
.class-code {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #7f8c8d;
}

.class-subject i,
.class-grade i,
.class-students i,
.class-code i {
  width: 14px;
  color: var(--primary-color, #4a6cf7);
}

.class-code {
  font-size: 12px;
  color: #6b7280;
  border-top: 1px solid #e5e7eb;
  padding-top: 10px;
  margin-top: 10px;
}

/* Assignments Grid */
.assignments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.assignment-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  border-left: 4px solid var(--primary-color, #4a6cf7);
  transition: all 0.3s ease;
}

.assignment-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.assignment-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.assignment-title {
  font-size: 16px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
  flex: 1;
  margin-right: 10px;
}

.assignment-status {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.assignment-status.active {
  background: #d1fae5;
  color: #065f46;
}

.assignment-status.draft {
  background: #fef3c7;
  color: #92400e;
}

.assignment-status.completed {
  background: #dbeafe;
  color: #1e40af;
}

.assignment-description {
  color: #7f8c8d;
  font-size: 14px;
  line-height: 1.4;
  margin-bottom: 15px;
  /* Fallback for line-clamp */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  /* Standard line-clamp property */
  display: -webkit-box;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.assignment-meta {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #7f8c8d;
}

.meta-item i {
  width: 14px;
  color: var(--primary-color, #4a6cf7);
}

/* Experience Timeline */
.experience-timeline {
  position: relative;
  padding-left: 20px;
}

.experience-timeline::before {
  content: '';
  position: absolute;
  left: 6px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e9ecef;
}

.experience-item {
  position: relative;
  margin-bottom: 25px;
}

.experience-item:last-child {
  margin-bottom: 0;
}

.exp-period {
  font-size: 12px;
  font-weight: 600;
  color: var(--primary-color, #4a6cf7);
  margin-bottom: 5px;
}

.exp-dot {
  position: absolute;
  left: -20px;
  top: 5px;
  width: 12px;
  height: 12px;
  background: var(--primary-color, #4a6cf7);
  border-radius: 50%;
  border: 3px solid #ffffff;
  box-shadow: 0 0 0 2px var(--primary-color, #4a6cf7);
}

.exp-content h6 {
  font-size: 14px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 5px;
}

.exp-company {
  font-size: 13px;
  color: var(--primary-color, #4a6cf7);
  font-weight: 500;
  margin-bottom: 5px;
}

.exp-description {
  font-size: 12px;
  color: #7f8c8d;
  line-height: 1.4;
}

/* Education List - Fixed space-y issue */
.education-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.education-item {
  display: flex;
  gap: 15px;
  padding-bottom: 20px;
  border-bottom: 1px solid #f8f9fa;
}

.education-item:last-child {
  padding-bottom: 0;
  border-bottom: none;
}

.edu-icon {
  width: 40px;
  height: 40px;
  background: var(--primary-color, #4a6cf7);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  flex-shrink: 0;
}

.edu-content h6 {
  font-size: 14px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 5px;
}

.edu-institution {
  font-size: 13px;
  color: var(--primary-color, #4a6cf7);
  font-weight: 500;
  margin-bottom: 3px;
}

.edu-year {
  font-size: 12px;
  color: #7f8c8d;
  margin-bottom: 5px;
}

.edu-details {
  font-size: 12px;
  color: #7f8c8d;
  line-height: 1.4;
}

/* Contact Form */
.contact-form .form-grp {
  margin-bottom: 15px;
}

.contact-form input,
.contact-form textarea {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  background: #f8f9fa;
  color: #2c3e50;
  transition: all 0.3s ease;
  font-size: 14px;
  font-family: inherit;
}

.contact-form input:focus,
.contact-form textarea:focus {
  outline: none;
  border-color: var(--primary-color, #4a6cf7);
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
}

.contact-form textarea {
  height: 100px;
  resize: vertical;
  line-height: 1.5;
}

.arrow-btn {
  width: 100%;
  background: var(--primary-color, #4a6cf7);
  color: #ffffff;
  border: none;
  padding: 12px 25px;
  border-radius: 8px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: all 0.3s ease;
  cursor: pointer;
  font-size: 14px;
  font-family: inherit;
}

.arrow-btn:hover:not(:disabled) {
  background: #3a5bd9;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
}

.arrow-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* Video Modal */
.video-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal-content {
  position: relative;
  background: #ffffff;
  border-radius: 15px;
  max-width: 900px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.close-modal {
  position: absolute;
  top: 15px;
  right: 15px;
  width: 40px;
  height: 40px;
  background: rgba(0, 0, 0, 0.7);
  border: none;
  border-radius: 50%;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  font-size: 16px;
}

.close-modal:hover {
  background: rgba(0, 0, 0, 0.9);
  transform: scale(1.1);
}

.video-container {
  position: relative;
  width: 100%;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
  height: 0;
}

.video-container iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 15px 15px 0 0;
  border: none;
}

.video-info {
  padding: 25px;
}

.video-info h4 {
  font-size: 20px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 10px;
  line-height: 1.3;
}

.video-info p {
  color: #7f8c8d;
  line-height: 1.6;
  margin-bottom: 15px;
}

.video-meta-modal {
  display: flex;
  gap: 20px;
  font-size: 14px;
  color: #7f8c8d;
  flex-wrap: wrap;
}

.video-meta-modal span {
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Additional utility classes for line clamping */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 3;
}

/* Responsive Design */
@media (max-width: 1199px) {
  .videos-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }
  
  .assignments-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }
}

@media (max-width: 991px) {
  .instructor__sidebar {
    position: static;
    margin-bottom: 40px;
  }
  
  .profile-stats {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .classes-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
}

@media (max-width: 767px) {
  .demo-videos-section,
  .sidebar-card,
  .assignments-section,
  .classes-section,
  .instructor__details-biography {
    padding: 25px;
  }
  
  .videos-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .assignments-grid {
    grid-template-columns: 1fr;
  }
  
  .classes-grid {
    grid-template-columns: 1fr;
  }
  
  .video-categories-nav {
    justify-content: center;
  }
  
  .profile-stats {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  
  .modal-content {
    margin: 10px;
  }
  
  .video-modal {
    padding: 10px;
  }
  
  .section-header .main-title {
    font-size: 24px;
  }
}

@media (max-width: 575px) {
  .video-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
  
  .video-meta-modal {
    flex-direction: column;
    gap: 10px;
  }
  
  .detail-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
  
  .detail-item span {
    text-align: left;
  }
  
  .assignment-header {
    flex-direction: column;
    gap: 10px;
  }
  
  .assignment-status {
    align-self: flex-start;
  }
  
  .class-header {
    flex-direction: column;
    gap: 10px;
  }
  
  .class-status {
    align-self: flex-start;
  }
  
  .education-item {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .edu-icon {
    margin: 0 auto;
  }
  
  .profile-avatar img {
    width: 100px;
    height: 100px;
  }
  
  .profile-info .title {
    font-size: 20px;
  }
  
  .video-info {
    padding: 20px;
  }
  
  .video-info h4 {
    font-size: 18px;
  }
  
  .contact-form input,
  .contact-form textarea {
    padding: 10px 12px;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .sidebar-card,
  .demo-videos-section,
  .classes-section,
  .assignments-section,
  .instructor__details-biography {
    border: 2px solid #2c3e50;
  }
  
  .video-card,
  .class-card,
  .assignment-card {
    border: 1px solid #2c3e50;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  
  .video-card:hover,
  .class-card:hover,
  .assignment-card:hover,
  .instructor__item:hover {
    transform: none;
  }
  
  .alltuchtopdown {
    animation: none;
  }
}
</style>