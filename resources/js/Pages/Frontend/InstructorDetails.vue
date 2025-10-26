<template>
  <FrontendLayout>
    <main class="main-area fix">
      <!-- instructor-details-area -->
      <section class="instructor__details-area section-pt-120 section-pb-90">
        <div class="container">
          <div class="row">
            <!-- Sidebar - Profile, Experience, Education -->
            <div class="col-xl-4 col-lg-5">
              <div class="instructor__sidebar">
                <!-- Teacher Profile Card -->
                <div class="sidebar-card profile-card">
                  <div class="profile-header">
                    <!-- Profile Avatar - Now above the name -->
                    <div class="profile-avatar">
                      <img :src="instructor.avatar" :alt="instructor.name" @error="handleImageError">
                    </div>
                    <div class="profile-info">
                      <h2 class="title">{{ instructor.name }}</h2>
                      <span class="designation">{{ getInstructorTitle(instructor) }}</span>
                      <div class="rating">
                        <i class="fas fa-star"></i>
                        <span>{{ instructor.rating }} ({{ instructor.reviews }} reviews)</span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Rest of the profile card remains the same -->
                  <div class="profile-stats">
                    <div class="stat-item">
                      <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                      </div>
                      <div class="stat-info">
                        <span class="stat-number">{{ stats.totalClasses }}</span>
                        <span class="stat-label">Total Classes</span>
                      </div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-icon">
                        <i class="fas fa-users"></i>
                      </div>
                      <div class="stat-info">
                        <span class="stat-number">{{ stats.totalStudents }}+</span>
                        <span class="stat-label">Students Taught</span>
                      </div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-icon">
                        <i class="fas fa-video"></i>
                      </div>
                      <div class="stat-info">
                        <span class="stat-number">{{ stats.totalVideos }}</span>
                        <span class="stat-label">Demo Videos</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="profile-details">
                    <div class="detail-item">
                      <label><i class="fas fa-user-graduate"></i> Expertise</label>
                      <span>{{ instructor.expertise }}</span>
                    </div>
                    <div class="detail-item">
                      <label><i class="fas fa-language"></i> Languages</label>
                      <span>{{ instructor.languages }}</span>
                    </div>
                    <div class="detail-item">
                      <label><i class="fas fa-clock"></i> Response Time</label>
                      <span>{{ instructor.response_time }}</span>
                    </div>
                    <div class="detail-item">
                      <label><i class="fas fa-envelope"></i> Email</label>
                      <span><a :href="`mailto:${instructor.email}`">{{ instructor.email }}</a></span>
                    </div>
                    <div class="detail-item">
                      <label><i class="fas fa-phone"></i> Experience</label>
                      <span>{{ stats.experience_years }}+ years</span>
                    </div>
                  </div>
                  
                  <div class="profile-bio">
                    <p>{{ instructor.bio }}</p>
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
                    <div class="experience-item">
                      <div class="exp-period">{{ stats.experience_years }}+ Years</div>
                      <div class="exp-dot"></div>
                      <div class="exp-content">
                        <h6>{{ getInstructorTitle(instructor) }}</h6>
                        <p class="exp-company">{{ instructor.institute || 'Educational Institution' }}</p>
                        <p class="exp-description">Teaching {{ instructor.expertise || 'various subjects' }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Education Card -->
                <div class="sidebar-card education-card">
                  <h4 class="title">Education & Certification</h4>
                  <div class="education-list">
                    <div class="education-item">
                      <div class="edu-icon">
                        <i class="fas fa-graduation-cap"></i>
                      </div>
                      <div class="edu-content">
                        <h6>{{ instructor.education_qualification || 'Teaching Certification' }}</h6>
                        <p class="edu-institution">{{ instructor.institute || 'Educational Institution' }}</p>
                        <p class="edu-year">{{ new Date().getFullYear() - stats.experience_years }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Main Content - Demo Class Videos, Classes -->
            <div class="col-xl-8 col-lg-7">
              <div class="instructor__details-wrap">
                <!-- Demo Class Videos Section -->
                <div class="demo-videos-section">
                  <div class="section-header">
                    <h3 class="main-title">Class Videos</h3>
                    <p>Watch classes to experience teaching style</p>
                  </div>

                  <!-- Video Categories Navigation -->
                  <div class="video-categories-nav" v-if="videos.length > 0">
                    <button 
                      @click="setActiveCategory('all')"
                      :class="['category-btn', { 'active': activeCategory === 'all' }]"
                    >
                      All Videos
                    </button>
                    <button 
                      v-for="classItem in uniqueClasses" 
                      :key="classItem.id"
                      @click="setActiveCategory(classItem.id)"
                      :class="['category-btn', { 'active': activeCategory === classItem.id }]"
                    >
                      {{ classItem.name }}
                    </button>
                  </div>

                  <!-- Videos Content -->
                  <div class="videos-content" v-if="filteredVideos.length > 0">
                    
                    <!-- Intro Video Section (First video of the first class) -->
                    <div class="intro-video-section" v-if="introVideo">
                      <h4 class="section-subtitle">Intro</h4>
                      <div class="intro-video-card" @click="openVideoModal(introVideo)">
                        <div class="intro-video-thumbnail">
                          <img :src="getVideoThumbnail(introVideo)" :alt="introVideo.title" @error="handleVideoThumbnailError">
                          <div class="video-overlay">
                            <div class="play-button">
                              <i class="fas fa-play"></i>
                            </div>
                            <div class="video-duration">{{ introVideo.duration || '15:30' }}</div>
                            <div class="video-badge">Intro</div>
                          </div>
                        </div>
                        <div class="intro-video-content">
                          <h5 class="video-title">{{ introVideo.title }}</h5>
                          <p class="video-description">{{ introVideo.description || 'Introduction video showcasing teaching methodology' }}</p>
                          <div class="video-stats">
                            <span><i class="far fa-eye"></i> {{ introVideo.views || '0' }} views</span>
                            <span><i class="far fa-thumbs-up"></i> {{ introVideo.likes || '0' }} likes</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Class-wise Video Sections -->
                    <div class="class-videos-sections">
                      <div 
                        v-for="classItem in classesWithVideos" 
                        :key="classItem.id"
                        class="class-videos-section"
                      >
                        <h4 class="section-subtitle">{{ classItem.name }}</h4>
                        <div class="class-videos-horizontal">
                          <div 
                            v-for="video in getVideosByClass(classItem.id)" 
                            :key="video.id"
                            class="video-card-horizontal"
                            @click="openVideoModal(video)"
                          >
                            <div class="video-thumbnail-horizontal">
                              <img :src="getVideoThumbnail(video)" :alt="video.title" @error="handleVideoThumbnailError">
                              <div class="video-overlay-horizontal">
                                <div class="play-button-horizontal">
                                  <i class="fas fa-play"></i>
                                </div>
                                <div class="video-duration-horizontal">{{ video.duration || '15:30' }}</div>
                                <div class="video-badge-horizontal">Demo</div>
                              </div>
                            </div>
                            <div class="video-content-horizontal">
                              <h5 class="video-title-horizontal">{{ video.title }}</h5>
                              <div class="video-meta-horizontal">
                                <span class="video-date-horizontal">{{ formatDate(video.created_at) }}</span>
                              </div>
                              <div class="video-stats-horizontal">
                                <span><i class="far fa-eye"></i> {{ video.views || '0' }}</span>
                                <span><i class="far fa-thumbs-up"></i> {{ video.likes || '0' }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- No Videos Message -->
                  <div v-if="videos.length === 0" class="text-center py-8">
                    <i class="fas fa-video-slash fa-3x text-gray-400 mb-4"></i>
                    <h4 class="text-gray-600">No Videos Available</h4>
                    <p class="text-gray-500">This instructor hasn't uploaded any demo videos yet.</p>
                  </div>

                  <!-- No Videos in Category Message -->
                  <div v-if="filteredVideos.length === 0 && videos.length > 0" class="text-center py-8">
                    <i class="fas fa-filter fa-3x text-gray-400 mb-4"></i>
                    <h4 class="text-gray-600">No videos in this class</h4>
                    <p class="text-gray-500">Try selecting a different class to see more videos.</p>
                  </div>

                  <!-- Load More Button -->
                  <div v-if="showLoadMore" class="text-center mt-5">
                    <button class="btn btn-primary" @click="loadMoreVideos">
                      Load More Videos
                    </button>
                  </div>
                </div>

                <!-- Classes Taught Section -->
                <div class="classes-section" v-if="classes.length > 0">
                  <div class="section-header">
                    <h3 class="main-title">Classes Taught</h3>
                    <p>Subjects and classes currently being taught</p>
                  </div>
                  <div class="classes-grid">
                    <div 
                      v-for="classItem in classes" 
                      :key="classItem.id"
                      class="class-card"
                    >
                      <div class="class-header">
                        <h5 class="class-name">{{ classItem.name }}</h5>
                        <span class="class-status active">
                          Active
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
                          <span>{{ classItem.student_count }} students</span>
                        </div>
                      </div>
                      <div class="class-description">
                        <p>{{ classItem.description || 'Comprehensive course covering essential topics' }}</p>
                      </div>
                      <div class="class-actions">
                        <Link :href="`/course/${classItem.id}`" class="btn btn-sm btn-outline">
                          View Course
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No Classes Message -->
                <div v-if="classes.length === 0 && videos.length === 0" class="text-center py-8">
                  <i class="fas fa-info-circle fa-3x text-gray-400 mb-4"></i>
                  <h4 class="text-gray-600">Content Coming Soon</h4>
                  <p class="text-gray-500">This instructor hasn't uploaded any content yet.</p>
                </div>

                <!-- Teaching Philosophy -->
                <div class="instructor__details-biography" v-if="instructor.teaching_philosophy">
                  <h4 class="title">Teaching Philosophy</h4>
                  <p>{{ instructor.teaching_philosophy }}</p>
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
              v-if="selectedVideo.is_youtube"
              :src="getEmbedUrl(selectedVideo)" 
              frameborder="0" 
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
              allowfullscreen>
            </iframe>
            <video v-else-if="selectedVideo.file_url" controls class="w-100">
              <source :src="selectedVideo.file_url" type="video/mp4">
              Your browser does not support the video tag.
            </video>
            <div v-else class="no-video-available">
              <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
              <p>Video content not available</p>
            </div>
          </div>
          <div class="video-info">
            <h4>{{ selectedVideo.title }}</h4>
            <p>{{ selectedVideo.description || 'No description available' }}</p>
            <div class="video-meta-modal">
              <span><i class="far fa-clock"></i> {{ selectedVideo.duration || '15:30' }}</span>
              <span><i class="far fa-calendar"></i> {{ formatDate(selectedVideo.created_at) }}</span>
              <span><i class="far fa-eye"></i> {{ selectedVideo.views || '0' }} views</span>
              <span class="video-class-modal">{{ getClassName(selectedVideo.class_id) }}</span>
            </div>
            <div class="video-actions" v-if="selectedVideo.is_youtube">
              <a :href="selectedVideo.videoUrl" target="_blank" class="btn btn-outline">
                <i class="fab fa-youtube"></i> Watch on YouTube
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </FrontendLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import FrontendLayout from '../Layout/FrontendLayout.vue';

export default {
  name: 'InstructorDetailsPage',
  components: {
    FrontendLayout,
    Link
  },
  props: {
    instructor: {
      type: Object,
      required: true
    },
    classes: {
      type: Array,
      default: () => []
    },
    videos: {
      type: Array,
      default: () => []
    },
    stats: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props) {
    const selectedVideo = ref(null);
    const activeCategory = ref('all');
    const videosPerPage = ref(6);
    const currentPage = ref(1);

    // Computed properties
    const uniqueClasses = computed(() => {
      return props.classes.filter((classItem, index, self) => 
        index === self.findIndex(c => c.id === classItem.id)
      );
    });

    const introVideo = computed(() => {
      // Get the first video of the first class as intro
      if (props.videos.length === 0) return null;
      
      const firstClassWithVideos = classesWithVideos.value[0];
      if (!firstClassWithVideos) return props.videos[0];
      
      const firstClassVideos = getVideosByClass(firstClassWithVideos.id);
      return firstClassVideos[0] || props.videos[0];
    });

    const classesWithVideos = computed(() => {
      // Group videos by class only, ignoring subjects
      const classMap = new Map();
      
      props.classes.forEach(classItem => {
        const classVideos = props.videos.filter(video => video.class_id == classItem.id);
        if (classVideos.length > 0) {
          classMap.set(classItem.id, classItem);
        }
      });
      
      return Array.from(classMap.values());
    });

    const filteredVideos = computed(() => {
      if (activeCategory.value === 'all') {
        return props.videos.slice(0, videosPerPage.value * currentPage.value);
      }
      
      return props.videos
        .filter(video => video.class_id == activeCategory.value)
        .slice(0, videosPerPage.value * currentPage.value);
    });

    const showLoadMore = computed(() => {
      const totalVideos = activeCategory.value === 'all' 
        ? props.videos.length
        : props.videos.filter(v => v.class_id == activeCategory.value).length;
      return filteredVideos.value.length < totalVideos;
    });

    // Methods
    const getInstructorTitle = (instructor) => {
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
      
      return 'Professional Instructor';
    };

    const getClassName = (classId) => {
      if (!classId) return 'General Education';
      
      const classItem = props.classes.find(c => c.id == classId);
      return classItem ? classItem.name : 'General Education';
    };

    const getVideoThumbnail = (video) => {
      if (video.thumbnail && video.thumbnail !== '/images/default-video-thumbnail.jpg') {
        return video.thumbnail;
      }
      
      if (video.is_youtube && video.youtube_video_id) {
        return `https://img.youtube.com/vi/${video.youtube_video_id}/hqdefault.jpg`;
      }
      
      return '/assets/img/courses/video_thumb01.jpg';
    };

    const getEmbedUrl = (video) => {
      if (video.youtube_embed_url) {
        return video.youtube_embed_url;
      }
      
      if (video.is_youtube && video.youtube_video_id) {
        return `https://www.youtube.com/embed/${video.youtube_video_id}?autoplay=1&rel=0&modestbranding=1`;
      }
      
      return video.videoUrl || '';
    };

    const getVideosByClass = (classId) => {
      const allVideos = props.videos.filter(video => video.class_id == classId);
      
      // If this is the first class and we have an intro video, exclude the first video
      const firstClass = classesWithVideos.value[0];
      if (firstClass && classId === firstClass.id && introVideo.value) {
        return allVideos.filter(video => video.id !== introVideo.value.id);
      }
      
      return allVideos;
    };

    const formatDate = (dateString) => {
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
    };

    const openVideoModal = (video) => {
      selectedVideo.value = video;
      document.body.style.overflow = 'hidden';
    };

    const closeVideoModal = () => {
      selectedVideo.value = null;
      document.body.style.overflow = 'auto';
    };

    const setActiveCategory = (categoryId) => {
      activeCategory.value = categoryId;
      currentPage.value = 1;
    };

    const loadMoreVideos = () => {
      currentPage.value++;
    };

    const handleImageError = (event) => {
      event.target.src = '/assets/img/instructor/instructor01.png';
    };

    const handleVideoThumbnailError = (event) => {
      event.target.src = '/assets/img/courses/video_thumb01.jpg';
    };

    return {
      selectedVideo,
      activeCategory,
      uniqueClasses,
      introVideo,
      classesWithVideos,
      filteredVideos,
      showLoadMore,
      getInstructorTitle,
      getClassName,
      getVideoThumbnail,
      getEmbedUrl,
      getVideosByClass,
      formatDate,
      openVideoModal,
      closeVideoModal,
      setActiveCategory,
      loadMoreVideos,
      handleImageError,
      handleVideoThumbnailError
    };
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

/* Profile Card Styles - UPDATED */
.profile-header {
  text-align: center;
  margin-bottom: 25px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.profile-avatar {
  margin-bottom: 20px;
  display: flex;
  justify-content: center;
  width: 100%;
}

.profile-avatar img {
  width: 140px; /* Slightly larger for better prominence */
  height: 140px;
  border-radius: 50%;
  object-fit: cover;
  border: 5px solid #f8f9fa;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.profile-info {
  width: 100%;
  text-align: center;
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

/* Rest of the CSS remains exactly the same... */

/* Main Content Sections */
.demo-videos-section,
.classes-section,
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

/* Intro Video Section */
.intro-video-section {
  margin-bottom: 40px;
  padding-bottom: 30px;
  border-bottom: 2px solid #e9ecef;
}

.section-subtitle {
  font-size: 20px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid var(--primary-color, #4a6cf7);
  display: inline-block;
}

.intro-video-card {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 30px;
  background: #f8f9fa;
  border-radius: 15px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.intro-video-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.intro-video-thumbnail {
  position: relative;
  overflow: hidden;
  height: 250px;
}

.intro-video-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.intro-video-card:hover .intro-video-thumbnail img {
  transform: scale(1.05);
}

.intro-video-content {
  padding: 25px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.intro-video-content .video-title {
  font-size: 22px;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 15px;
  line-height: 1.3;
}

.intro-video-content .video-description {
  font-size: 16px;
  line-height: 1.6;
  color: #7f8c8d;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Class Videos Sections - HORIZONTAL LAYOUT */
.class-videos-sections {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.class-videos-section {
  margin-bottom: 30px;
}

.class-videos-horizontal {
  display: flex;
  gap: 20px;
  overflow-x: auto;
  padding: 10px 5px 20px;
  scrollbar-width: thin;
  scrollbar-color: var(--primary-color, #4a6cf7) #f1f1f1;
}

.class-videos-horizontal::-webkit-scrollbar {
  height: 8px;
}

.class-videos-horizontal::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.class-videos-horizontal::-webkit-scrollbar-thumb {
  background: var(--primary-color, #4a6cf7);
  border-radius: 10px;
}

.class-videos-horizontal::-webkit-scrollbar-thumb:hover {
  background: #3a5bd9;
}

/* Horizontal Video Cards */
.video-card-horizontal {
  flex: 0 0 280px;
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
}

.video-card-horizontal:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.video-thumbnail-horizontal {
  position: relative;
  overflow: hidden;
  height: 160px;
}

.video-thumbnail-horizontal img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.video-card-horizontal:hover .video-thumbnail-horizontal img {
  transform: scale(1.1);
}

.video-overlay-horizontal {
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

.video-card-horizontal:hover .video-overlay-horizontal {
  opacity: 1;
}

.play-button-horizontal {
  width: 45px;
  height: 45px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary-color, #4a6cf7);
  font-size: 16px;
  transition: all 0.3s ease;
}

.video-card-horizontal:hover .play-button-horizontal {
  background: #ffffff;
  transform: scale(1.1);
}

.video-duration-horizontal {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: rgba(0, 0, 0, 0.7);
  color: #ffffff;
  padding: 3px 6px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 500;
  z-index: 2;
}

.video-badge-horizontal {
  position: absolute;
  top: 8px;
  left: 8px;
  background: #ff6b6b;
  color: white;
  padding: 3px 6px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: bold;
  z-index: 2;
}

.video-content-horizontal {
  padding: 15px;
}

.video-title-horizontal {
  font-size: 14px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 8px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.video-meta-horizontal {
  margin-bottom: 8px;
}

.video-date-horizontal {
  color: #7f8c8d;
  font-size: 11px;
}

.video-stats-horizontal {
  display: flex;
  gap: 12px;
  font-size: 11px;
  color: #7f8c8d;
}

.video-stats-horizontal span {
  display: flex;
  align-items: center;
  gap: 4px;
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

.class-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 15px;
}

.class-subject,
.class-grade,
.class-students {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #7f8c8d;
}

.class-subject i,
.class-grade i,
.class-students i {
  width: 14px;
  color: var(--primary-color, #4a6cf7);
}

.class-description {
  color: #718096;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 15px;
}

.class-actions {
  display: flex;
  justify-content: flex-end;
}

/* Video Modal */
.video-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
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
  background: #1a1a1a;
  border-radius: 15px;
  max-width: 1000px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s ease;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
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
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  font-size: 18px;
  backdrop-filter: blur(10px);
}

.close-modal:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
  transform: scale(1.1);
}

.video-container {
  position: relative;
  width: 100%;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
  height: 0;
  background: #000000;
  border-radius: 15px 15px 0 0;
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
  padding: 30px;
  background: #1a1a1a;
  color: #ffffff;
}

.video-info h4 {
  font-size: 22px;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 15px;
  line-height: 1.3;
}

.video-info p {
  color: #b0b0b0;
  line-height: 1.6;
  margin-bottom: 20px;
  font-size: 15px;
}

.video-meta-modal {
  display: flex;
  gap: 25px;
  font-size: 14px;
  color: #888888;
  margin-bottom: 25px;
  flex-wrap: wrap;
}

.video-meta-modal span {
  display: flex;
  align-items: center;
  gap: 8px;
}

.video-actions {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: transparent;
  border: 2px solid #ff0000;
  color: #ff0000;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
  font-size: 14px;
}

.btn-outline:hover {
  background: #ff0000;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
}

.no-video-available {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #718096;
  padding: 40px;
}

/* Text Utilities */
.text-gray-400 { color: #9ca3af; }
.text-gray-500 { color: #6b7280; }
.text-gray-600 { color: #4b5563; }

.py-8 { padding-top: 2rem; padding-bottom: 2rem; }
.mb-4 { margin-bottom: 1rem; }
.mt-5 { margin-top: 1.25rem; }

/* Button Styles */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.btn-primary {
  background: var(--primary-color, #4a6cf7);
  color: #ffffff;
}

.btn-primary:hover {
  background: #3a5bd9;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
}

.btn-sm {
  padding: 8px 16px;
  font-size: 12px;
}

.btn-outline {
  background: transparent;
  border: 2px solid var(--primary-color, #4a6cf7);
  color: var(--primary-color, #4a6cf7);
}

.btn-outline:hover {
  background: var(--primary-color, #4a6cf7);
  color: #ffffff;
}

/* Responsive Design */
@media (max-width: 1199px) {
  .video-card-horizontal {
    flex: 0 0 260px;
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
  
  .intro-video-card {
    grid-template-columns: 1fr;
  }
  
  .intro-video-thumbnail {
    height: 200px;
  }
  
  .video-card-horizontal {
    flex: 0 0 240px;
  }
}

@media (max-width: 767px) {
  .demo-videos-section,
  .sidebar-card,
  .classes-section,
  .instructor__details-biography {
    padding: 25px;
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
  
  .intro-video-content {
    padding: 20px;
  }
  
  .intro-video-content .video-title {
    font-size: 18px;
  }
  
  .section-subtitle {
    font-size: 18px;
  }
  
  .video-card-horizontal {
    flex: 0 0 220px;
  }
  
  .video-thumbnail-horizontal {
    height: 140px;
  }
}

@media (max-width: 575px) {
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
  
  .class-header {
    flex-direction: column;
    gap: 10px;
  }
  
  .class-status {
    align-self: flex-start;
  }
  
  .profile-avatar img {
    width: 120px;
    height: 120px;
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
  
  .video-info p {
    font-size: 14px;
  }
  
  .video-actions {
    flex-direction: column;
  }
  
  .btn-outline {
    width: 100%;
    justify-content: center;
  }
  
  .intro-video-thumbnail {
    height: 180px;
  }
  
  .intro-video-content .video-title {
    font-size: 16px;
  }
  
  .intro-video-content .video-description {
    font-size: 14px;
  }
  
  .video-card-horizontal {
    flex: 0 0 200px;
  }
  
  .video-thumbnail-horizontal {
    height: 120px;
  }
  
  .video-title-horizontal {
    font-size: 13px;
  }
}
</style>