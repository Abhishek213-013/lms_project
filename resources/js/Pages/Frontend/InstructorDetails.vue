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
                    <div class="profile-avatar">
                      <img 
                        :src="profilePictureUrl" 
                        :alt="instructor.name" 
                        @error="handleProfilePictureError"
                        class="profile-picture"
                      >
                    </div>
                    <div class="profile-info">
                      <h2 class="title">{{ instructor.name }}</h2>
                      <span class="designation">{{ getInstructorTitle(instructor) }}</span>
                      <div class="rating">
                        <i class="fas fa-star"></i>
                        <span>{{ instructor.rating }} ({{ instructor.reviews }} {{ t('reviews') }})</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="profile-stats">
                    <div class="stat-item">
                      <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                      </div>
                      <div class="stat-info">
                        <span class="stat-number">{{ stats.totalClasses }}</span>
                        <span class="stat-label">{{ t('Total Classes') }}</span>
                      </div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-icon">
                        <i class="fas fa-users"></i>
                      </div>
                      <div class="stat-info">
                        <span class="stat-number">{{ stats.totalStudents }}+</span>
                        <span class="stat-label">{{ t('Students Taught') }}</span>
                      </div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-icon">
                        <i class="fas fa-video"></i>
                      </div>
                      <div class="stat-info">
                        <span class="stat-number">{{ stats.totalVideos }}</span>
                        <span class="stat-label">{{ t('Demo Videos') }}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="profile-details">
                    <div class="detail-item">
                      <label><i class="fas fa-user-graduate"></i> {{ t('Expertise') }}</label>
                      <span>{{ instructor.expertise }}</span>
                    </div>
                    <div class="detail-item">
                      <label><i class="fas fa-language"></i> {{ t('Languages') }}</label>
                      <span>{{ instructor.languages }}</span>
                    </div>
                    <div class="detail-item">
                      <label><i class="fas fa-clock"></i> {{ t('Response Time') }}</label>
                      <span>{{ instructor.response_time }}</span>
                    </div>
                    <div class="detail-item">
                      <label><i class="fas fa-envelope"></i> {{ t('Email') }}</label>
                      <span><a :href="`mailto:${instructor.email}`">{{ instructor.email }}</a></span>
                    </div>
                    <div class="detail-item">
                      <label><i class="fas fa-phone"></i> {{ t('Experience') }}</label>
                      <span>{{ stats.experience_years }}+ {{ t('years') }}</span>
                    </div>
                  </div>
                  
                  <div class="profile-bio">
                    <p>{{ instructor.bio }}</p>
                  </div>
                  
                  <div class="profile-social">
                    <h5>{{ t('Follow Instructor') }}</h5>
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
                  <h4 class="title">{{ t('Teaching Experience') }}</h4>
                  <div class="experience-timeline">
                    <div class="experience-item">
                      <div class="exp-period">{{ stats.experience_years }}+ {{ t('Years') }}</div>
                      <div class="exp-dot"></div>
                      <div class="exp-content">
                        <h6>{{ getInstructorTitle(instructor) }}</h6>
                        <p class="exp-company">{{ instructor.institute || t('Educational Institution') }}</p>
                        <p class="exp-description">{{ t('Teaching') }} {{ instructor.expertise || t('various subjects') }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Education Card -->
                <div class="sidebar-card education-card">
                  <h4 class="title">{{ t('Education & Certification') }}</h4>
                  <div class="education-list">
                    <div class="education-item">
                      <div class="edu-icon">
                        <i class="fas fa-graduation-cap"></i>
                      </div>
                      <div class="edu-content">
                        <h6>{{ instructor.education_qualification || t('Teaching Certification') }}</h6>
                        <p class="edu-institution">{{ instructor.institute || t('Educational Institution') }}</p>
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
                    <h3 class="main-title">{{ t('Class Videos') }}</h3>
                    <p>{{ t('Watch classes to experience teaching style') }}</p>
                  </div>

                  <!-- Video Categories Navigation -->
                  <div class="video-categories-nav" v-if="videos.length > 0">
                    <button 
                      @click="setActiveCategory('all')"
                      :class="['category-btn', { 'active': activeCategory === 'all' }]"
                    >
                      {{ t('All Videos') }}
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
                    
                    <!-- Intro Video Section -->
                    <div class="intro-video-section" v-if="introVideo">
                      <h4 class="section-subtitle">{{ t('Intro') }}</h4>
                      <div class="intro-video-card" @click="playVideo(introVideo)">
                        <div class="intro-video-thumbnail">
                          <img :src="getVideoThumbnail(introVideo)" :alt="introVideo.title" @error="handleVideoThumbnailError">
                          <div class="video-overlay">
                            <div class="play-button">
                              <i class="fas fa-play"></i>
                            </div>
                            <div class="video-duration">{{ introVideo.duration || '15:30' }}</div>
                            <div class="video-badge">{{ t('Intro') }}</div>
                          </div>
                        </div>
                        <div class="intro-video-content">
                          <h5 class="video-title">{{ getCleanTitle(introVideo.title) }}</h5>
                          <div class="video-stats-simple">
                            <span><i class="far fa-clock"></i> {{ introVideo.duration || '15:30' }}</span>
                            <span><i class="far fa-calendar"></i> {{ formatDate(introVideo.created_at) }}</span>
                            <span class="video-class">{{ getClassName(introVideo.class_id) }}</span>
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
                        v-show="activeCategory === 'all' || activeCategory === classItem.id"
                      >
                        <h4 class="section-subtitle">{{ classItem.name }}</h4>
                        <div class="class-videos-horizontal">
                          <div 
                            v-for="video in getVideosByClass(classItem.id)" 
                            :key="video.id"
                            class="video-card-horizontal"
                            @click="playVideo(video)"
                          >
                            <div class="video-thumbnail-horizontal">
                              <img :src="getVideoThumbnail(video)" :alt="video.title" @error="handleVideoThumbnailError">
                              <div class="video-overlay-horizontal">
                                <div class="play-button-horizontal">
                                  <i class="fas fa-play"></i>
                                </div>
                                <div class="video-duration-horizontal">{{ video.duration || '15:30' }}</div>
                                <div class="video-badge-horizontal">{{ t('Demo') }}</div>
                              </div>
                            </div>
                            <div class="video-content-horizontal">
                              <h5 class="video-title-horizontal">{{ getCleanTitle(video.title) }}</h5>
                              <div class="video-meta-simple">
                                <span><i class="far fa-clock"></i> {{ video.duration || '15:30' }}</span>
                                <span><i class="far fa-calendar"></i> {{ formatDate(video.created_at) }}</span>
                                <span class="video-class-simple">{{ getClassName(video.class_id) }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- No Videos Message -->
                  <div v-if="videos.length === 0" class="no-content-state">
                    <i class="fas fa-video-slash fa-3x mb-4"></i>
                    <h4>{{ t('No Videos Available') }}</h4>
                    <p>{{ t('This instructor hasn\'t uploaded any demo videos yet.') }}</p>
                  </div>

                  <!-- No Videos in Category Message -->
                  <div v-if="filteredVideos.length === 0 && videos.length > 0" class="no-content-state">
                    <i class="fas fa-filter fa-3x mb-4"></i>
                    <h4>{{ t('No videos in this class') }}</h4>
                    <p>{{ t('Try selecting a different class to see more videos.') }}</p>
                  </div>

                  <!-- Load More Button -->
                  <div v-if="showLoadMore" class="load-more-section">
                    <button class="btn btn-primary" @click="loadMoreVideos">
                      {{ t('Load More Videos') }}
                    </button>
                  </div>
                </div>

                <!-- Classes Taught Section -->
                <div class="classes-section" v-if="classes.length > 0">
                  <div class="section-header">
                    <h3 class="main-title">{{ t('Classes Taught') }}</h3>
                    <p>{{ t('Subjects and classes currently being taught') }}</p>
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
                          {{ t('Active') }}
                        </span>
                      </div>
                      <div class="class-details">
                        <div class="class-subject">
                          <i class="fas fa-book"></i>
                          <span>{{ classItem.subject }}</span>
                        </div>
                        <div class="class-grade" v-if="classItem.grade">
                          <i class="fas fa-graduation-cap"></i>
                          <span>{{ t('Grade') }} {{ classItem.grade }}</span>
                        </div>
                        <div class="class-students">
                          <i class="fas fa-users"></i>
                          <span>{{ classItem.student_count }} {{ t('students') }}</span>
                        </div>
                      </div>
                      <div class="class-description">
                        <p>{{ classItem.description || t('Comprehensive course covering essential topics') }}</p>
                      </div>
                      <div class="class-actions">
                        <Link :href="`/course/${classItem.id}`" class="btn btn-sm btn-outline">
                          {{ t('View Course') }}
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No Classes Message -->
                <div v-if="classes.length === 0 && videos.length === 0" class="no-content-state">
                  <i class="fas fa-info-circle fa-3x mb-4"></i>
                  <h4>{{ t('Content Coming Soon') }}</h4>
                  <p>{{ t('This instructor hasn\'t uploaded any content yet.') }}</p>
                </div>

                <!-- Teaching Philosophy -->
                <div class="instructor__details-biography">
                  <h4 class="title">{{ t('Teaching Philosophy') }}</h4>
                  <p>{{ instructor.bio || instructor.teaching_philosophy || t('This instructor is passionate about education and dedicated to student success.') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Video Player Modal - CLEAN VERSION -->
      <div 
        v-if="showVideoPlayer && currentVideo" 
        class="video-player-modal"
        @click.self="closeVideoPlayer"
      >
        <div class="modal-container">
          <!-- Header - 20% -->
          <div class="header-section">
            <div class="header-content">
              <h3>{{ currentVideo.title }}</h3>
              <div class="video-meta">
                <span><i class="far fa-clock mr-2"></i> {{ currentVideo.duration || 'N/A' }}</span>
                <span><i class="far fa-calendar mr-2"></i> {{ formatDate(currentVideo.created_at) }}</span>
                <span class="video-class">{{ getClassName(currentVideo.class_id) }}</span>
              </div>
            </div>
            <button 
              @click="closeVideoPlayer" 
              class="close-button"
              aria-label="Close video player"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <!-- Video Player - 70% -->
          <div class="video-player-section">
            <!-- Loading State -->
            <div v-if="isLoading" class="loading-state">
              <div class="spinner"></div>
              <p>{{ t('Loading video...') }}</p>
            </div>
            
            <!-- YouTube Embed -->
            <div v-else-if="currentVideo.isEmbed" class="youtube-container">
              <iframe 
                :src="currentVideo.ultraCleanUrl" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                @load="isLoading = false"
              ></iframe>
            </div>
            
            <!-- Direct Video -->
            <div v-else-if="currentVideo.isDirectStream || currentVideo.isDirectVideo" class="direct-video-container">
              <video 
                ref="videoPlayer"
                :src="currentVideo.directVideoUrl" 
                controls
                controlsList="nodownload"
                @play="isPlaying = true"
                @pause="isPlaying = false"
                @ended="isPlaying = false"
                @loadeddata="isLoading = false"
                @error="handleVideoError"
                autoplay
              >
                {{ t('Your browser does not support the video tag.') }}
              </video>
            </div>
            
            <!-- Error State -->
            <div v-else class="error-state">
              <i class="fas fa-exclamation-triangle"></i>
              <p>{{ t('Unable to load video') }}</p>
              <p class="error-subtext">{{ t('Please try again later.') }}</p>
            </div>
          </div>
          
          <!-- Footer - 10% -->
          <div class="footer-section">
            <div class="footer-content">
              <!-- <p v-if="currentVideo.description" class="video-description">
                {{ currentVideo.description }}
              </p>
              <p v-else class="no-description">
                {{ t('No description available') }}
              </p> -->
              <button @click="closeVideoPlayer" class="close-btn">
                <i class="fas fa-times mr-2"></i>
                {{ t('Close') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </FrontendLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import FrontendLayout from '../Layout/FrontendLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

// Use the global translation composable
const { currentLanguage, t, switchLanguage } = useTranslation();

// Component props
const props = defineProps({
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
  },
  auth: {
    type: Object,
    default: () => ({})
  }
});

// Reactive data
const selectedVideo = ref(null);
const currentVideo = ref(null);
const showVideoPlayer = ref(false);
const videoPlayer = ref(null);
const activeCategory = ref('all');
const videosPerPage = ref(6);
const currentPage = ref(1);
const isPlaying = ref(false);
const isLoading = ref(false);

// Computed properties
const canEditProfile = computed(() => {
  return props.auth.user && props.auth.user.id === props.instructor.id;
});

const profilePictureUrl = computed(() => {
  if (props.instructor?.profile_picture) {
    if (props.instructor.profile_picture.startsWith('http')) {
      return props.instructor.profile_picture;
    }
    
    let picturePath = props.instructor.profile_picture;
    picturePath = picturePath.replace(/^\/+/, '').replace(/^storage\//, '');
    const finalUrl = `/storage/${picturePath}`;
    
    return finalUrl;
  }
  
  return '/assets/img/instructor/instructor01.png';
});

const uniqueClasses = computed(() => {
  return props.classes.filter((classItem, index, self) => 
    index === self.findIndex(c => c.id === classItem.id)
  );
});

const introVideo = computed(() => {
  if (props.videos.length === 0) return null;
  
  const firstClassWithVideos = classesWithVideos.value[0];
  if (!firstClassWithVideos) return props.videos[0];
  
  const firstClassVideos = getVideosByClass(firstClassWithVideos.id);
  return firstClassVideos[0] || props.videos[0];
});

const classesWithVideos = computed(() => {
  const classMap = new Map();
  
  props.classes.forEach(classItem => {
    const classVideos = props.videos.filter(video => 
      String(video.class_id) === String(classItem.id)
    );
    if (classVideos.length > 0) {
      classMap.set(classItem.id, classItem);
    }
  });
  
  return Array.from(classMap.values());
});

const filteredVideos = computed(() => {
  let filtered = [];
  
  if (activeCategory.value === 'all') {
    filtered = props.videos;
  } else {
    filtered = props.videos.filter(video => 
      String(video.class_id) === String(activeCategory.value)
    );
  }
  
  return filtered.slice(0, videosPerPage.value * currentPage.value);
});

const showLoadMore = computed(() => {
  let totalVideos = 0;
  
  if (activeCategory.value === 'all') {
    totalVideos = props.videos.length;
  } else {
    totalVideos = props.videos.filter(v => 
      String(v.class_id) === String(activeCategory.value)
    ).length;
  }
  
  return filteredVideos.value.length < totalVideos;
});

// Utility methods
const getCleanTitle = (title) => {
  if (!title) return t('Untitled Video');
  
  let cleanTitle = title.replace(/https?:\/\/[^\s]+/g, '').trim();
  cleanTitle = cleanTitle.replace(/youtu\.be\/[^\s]+/g, '');
  cleanTitle = cleanTitle.replace(/youtube\.com\/watch\?v=[^\s]+/g, '');
  cleanTitle = cleanTitle.replace(/\?si=[^\s]+/g, '');
  cleanTitle = cleanTitle.replace(/\s+/g, ' ').trim();
  
  if (!cleanTitle) return t('Demo Video');
  
  return cleanTitle;
};

const getInstructorTitle = (instructor) => {
  if (instructor.education_qualification) {
    const qual = instructor.education_qualification;
    const titles = {
      'en': {
        'PhD': 'PhD Specialist',
        'MA': 'Master Educator',
        'MSC': 'Science Expert',
        'BA': 'Bachelor Educator',
        'BSC': 'Science Teacher',
        'HSC': 'Certified Teacher',
        'Other': 'Professional Instructor'
      },
      'bn': {
        'PhD': 'পিএইচডি বিশেষজ্ঞ',
        'MA': 'মাস্টার শিক্ষক',
        'MSC': 'বিজ্ঞান বিশেষজ্ঞ',
        'BA': 'ব্যাচেলর শিক্ষক',
        'BSC': 'বিজ্ঞান শিক্ষক',
        'HSC': 'সার্টিফাইড শিক্ষক',
        'Other': 'পেশাদার ইন্সট্রাক্টর'
      }
    };
    return titles[currentLanguage.value]?.[qual] || `${qual} ${t('Certified')}`;
  }
  return t('Professional Instructor');
};

const getClassName = (classId) => {
  if (!classId) return t('General Education');
  const classItem = props.classes.find(c => String(c.id) === String(classId));
  return classItem ? classItem.name : t('General Education');
};

const getVideoThumbnail = (video) => {
  if (video.thumbnail && video.thumbnail !== '/images/default-video-thumbnail.jpg') {
    return video.thumbnail;
  }
  
  const videoContent = getVideoContent(video);
  if (videoContent && isYouTubeUrl(videoContent)) {
    const videoId = getYouTubeVideoId(videoContent);
    if (videoId) {
      return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
    }
  }
  
  return '/assets/img/courses/video_thumb01.jpg';
};

const getVideoContent = (video) => {
  if (!video) return null;
  
  const possibleFields = [
    'content',
    'file_path',
    'url',
    'video_url',
    'link',
    'source',
    'video_content',
    'youtube_url',
    'resource_url',
    'videoUrl'
  ];
  
  for (const field of possibleFields) {
    if (video[field] && typeof video[field] === 'string') {
      const value = video[field].trim();
      if (value.startsWith('http')) {
        return value;
      }
      
      if ((value.includes('youtube.com') || value.includes('youtu.be')) && !value.startsWith('http')) {
        return 'https://' + value.replace(/^\/\//, '');
      }
    }
  }
  
  return null;
};

const isYouTubeUrl = (url) => {
  if (!url || typeof url !== 'string') return false;
  
  const cleanUrl = url.trim();
  const youtubePatterns = [
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/,
    /youtu\.be\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/embed\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/v\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]+)/,
    /youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/,
    /youtube\.com\/live\/([a-zA-Z0-9_-]+)/
  ];
  
  return youtubePatterns.some(pattern => pattern.test(cleanUrl));
};

const getYouTubeVideoId = (url) => {
  if (!url || typeof url !== 'string') return null;
  
  const cleanUrl = url.trim();
  const patterns = [
    /youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/,
    /youtu\.be\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/v\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/,
    /youtube\.com\/live\/([a-zA-Z0-9_-]{11})/
  ];
  
  for (const pattern of patterns) {
    const match = cleanUrl.match(pattern);
    if (match && match[1]) {
      return match[1];
    }
  }
  
  return null;
};

const getVideosByClass = (classId) => {
  const allVideos = props.videos.filter(video => 
    String(video.class_id) === String(classId)
  );
  
  const firstClass = classesWithVideos.value[0];
  if (firstClass && String(classId) === String(firstClass.id) && introVideo.value) {
    return allVideos.filter(video => video.id !== introVideo.value.id);
  }
  
  return allVideos;
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  try {
    const options = {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    };
    
    if (currentLanguage.value === 'bn') {
      return new Date(dateString).toLocaleDateString('bn-BD', options);
    }
    
    return new Date(dateString).toLocaleDateString('en-US', options);
  } catch (error) {
    return t('Invalid Date');
  }
};

const setActiveCategory = (categoryId) => {
  activeCategory.value = categoryId;
  currentPage.value = 1;
};

const loadMoreVideos = () => {
  currentPage.value++;
};

const handleVideoThumbnailError = (event) => {
  event.target.src = '/assets/img/courses/video_thumb01.jpg';
};

const handleProfilePictureError = (event) => {
  event.target.src = '/assets/img/instructor/instructor01.png';
};

// Video player methods
const playVideo = async (video) => {
  isLoading.value = true;
  showVideoPlayer.value = true;
  
  try {
    const cleanVideo = {
      ...video,
      title: getCleanTitle(video.title),
      description: video.description || ''
    };
    
    const videoContent = getVideoContent(video);
    
    if (!videoContent) {
      throw new Error('No video content found');
    }
    
    if (isYouTubeUrl(videoContent)) {
      const videoId = getYouTubeVideoId(videoContent);
      
      if (videoId) {
        const directVideoUrl = await getDirectVideoStream(videoId);
        
        if (directVideoUrl) {
          currentVideo.value = {
            ...cleanVideo,
            directVideoUrl: directVideoUrl,
            videoId: videoId,
            thumbnail: getVideoThumbnail(video),
            isDirectStream: true,
            isEmbed: false,
            isDirectVideo: false
          };
        } else {
          currentVideo.value = {
            ...cleanVideo,
            ultraCleanUrl: generateUltraCleanEmbedUrl(videoId),
            videoId: videoId,
            originalUrl: videoContent,
            isEmbed: true,
            isDirectStream: false,
            isDirectVideo: false
          };
        }
      } else {
        throw new Error('Could not extract YouTube video ID');
      }
    } else {
      currentVideo.value = {
        ...cleanVideo,
        directVideoUrl: videoContent,
        isDirectVideo: true,
        isEmbed: false,
        isDirectStream: false
      };
    }
    
  } catch (error) {
    console.error('Error preparing video:', error);
    alert(`Error loading video: ${error.message}`);
    closeVideoPlayer();
  } finally {
    isLoading.value = false;
  }
};

const closeVideoPlayer = () => {
  if (videoPlayer.value && typeof videoPlayer.value.pause === 'function') {
    videoPlayer.value.pause();
    videoPlayer.value.currentTime = 0;
  }
  
  showVideoPlayer.value = false;
  currentVideo.value = null;
  isPlaying.value = false;
  isLoading.value = false;
  
  document.body.style.overflow = 'auto';
  
  setTimeout(() => {
    if (videoPlayer.value) {
      videoPlayer.value.src = '';
      videoPlayer.value.load();
    }
  }, 100);
};

const handleVideoError = (event) => {
  console.error('Video playback error:', event);
  isLoading.value = false;
  
  if (currentVideo.value && currentVideo.value.videoId && !currentVideo.value.isEmbed) {
    currentVideo.value = {
      ...currentVideo.value,
      ultraCleanUrl: generateUltraCleanEmbedUrl(currentVideo.value.videoId),
      isEmbed: true,
      isDirectStream: false,
      isDirectVideo: false
    };
  }
};

const generateUltraCleanEmbedUrl = (videoId) => {
  const params = new URLSearchParams({
    'autoplay': '1',
    'rel': '0',
    'modestbranding': '1',
    'controls': '1',
    'showinfo': '0',
    'iv_load_policy': '3',
    'fs': '1',
    'disablekb': '0',
    'playsinline': '1',
    'enablejsapi': '1',
    'origin': window.location.origin,
    'widget_referrer': window.location.origin,
    'cc_load_policy': '0',
    'color': 'white',
    'hl': 'en',
    'cc_lang_pref': 'en'
  });
  
  return `https://www.youtube-nocookie.com/embed/${videoId}?${params.toString()}`;
};

const getDirectVideoStream = async (videoId) => {
  try {
    const response = await fetch(`/api/youtube-direct-stream?videoId=${videoId}`);
    const data = await response.json();
    
    if (data.success && data.directUrl) {
      return data.directUrl;
    }
  } catch (apiError) {
    console.log('API method failed');
  }
  
  const proxyUrl = `/api/video-proxy/${videoId}`;
  
  try {
    const testResponse = await fetch(proxyUrl, { method: 'HEAD' });
    if (testResponse.ok) {
      return proxyUrl;
    }
  } catch (proxyError) {
    console.log('Proxy URL not accessible');
  }
  
  return null;
};

// Event handlers
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && showVideoPlayer.value) {
    closeVideoPlayer();
  }
};

// Lifecycle
onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
});
</script>

<style scoped>
/* ==================== */
/* CRITICAL ICON FIXES */
/* ==================== */
.fas, .fab, .far {
  display: inline-block !important;
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 900 !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  line-height: 1 !important;
  -webkit-font-smoothing: antialiased !important;
  -moz-osx-font-smoothing: grayscale !important;
}

.fab {
  font-family: 'Font Awesome 6 Brands' !important;
  font-weight: 400 !important;
}

.far {
  font-family: 'Font Awesome 6 Free' !important;
  font-weight: 400 !important;
}

/* ==================== */
/* CSS VARIABLES FOR THEMING */
/* ==================== */
:root {
  --bg-primary: #ffffff;
  --bg-secondary: #f8fafc;
  --bg-tertiary: #f1f5f9;
  --card-bg: #ffffff;
  --text-primary: #1e293b;
  --text-secondary: #475569;
  --text-muted: #64748b;
  --border-color: #e2e8f0;
  --primary-color: #3b82f6;
  --primary-hover: #2563eb;
  --success-color: #10b981;
  --warning-color: #f59e0b;
  --error-color: #ef4444;
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* ==================== */
/* MAIN LAYOUT */
/* ==================== */
.main-area {
  background: var(--bg-primary);
  min-height: 100vh;
}

.fix {
  position: relative;
}

.instructor__details-area {
  background: var(--bg-primary);
  padding: 80px 0;
}

.section-pt-120 {
  padding-top: 120px;
}

.section-pb-90 {
  padding-bottom: 90px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

.row {
  display: flex;
  flex-wrap: wrap;
  margin: 0 -15px;
}

.col-xl-4 {
  flex: 0 0 33.333333%;
  max-width: 33.333333%;
  padding: 0 15px;
}

.col-xl-8 {
  flex: 0 0 66.666667%;
  max-width: 66.666667%;
  padding: 0 15px;
}

.col-lg-5 {
  flex: 0 0 41.666667%;
  max-width: 41.666667%;
  padding: 0 15px;
}

.col-lg-7 {
  flex: 0 0 58.333333%;
  max-width: 58.333333%;
  padding: 0 15px;
}

/* ==================== */
/* SIDEBAR STYLES */
/* ==================== */
.instructor__sidebar {
  position: sticky;
  top: 100px;
}

.sidebar-card {
  background: var(--card-bg);
  padding: 30px;
  border-radius: 15px;
  box-shadow: var(--shadow);
  margin-bottom: 25px;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.sidebar-card:hover {
  box-shadow: var(--shadow-lg);
}

.sidebar-card .title {
  font-size: 18px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--border-color);
}

/* ==================== */
/* PROFILE CARD STYLES */
/* ==================== */
.profile-card {
  text-align: center;
}

.profile-header {
  margin-bottom: 30px;
}

.profile-avatar {
  position: relative;
  width: 210px;
  height: 180px;
  margin: 0 auto 30px;
  border-radius: 20px;
  overflow: hidden;
}

.profile-picture {
  width: 100%;
  height: 100%;
  border-radius: 20px;
  object-fit: cover;
  border: 6px solid var(--bg-secondary);
  box-shadow: var(--shadow);
}

.profile-info .title {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 5px;
  border-bottom: none;
  padding-bottom: 0;
}

.designation {
  color: var(--primary-color);
  font-weight: 600;
  font-size: 18px;
  display: block;
  margin-bottom: 10px;
}

.rating {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: var(--text-muted);
  font-size: 16px;
}

.rating i {
  color: var(--warning-color);
}

/* ==================== */
/* PROFILE STATS */
/* ==================== */
.profile-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
  margin-bottom: 25px;
}

.stat-item {
  text-align: center;
  padding: 15px;
  background: var(--bg-secondary);
  border-radius: 10px;
  transition: background-color 0.3s ease;
}

.stat-item:hover {
  background: var(--bg-tertiary);
}

.stat-icon {
  width: 40px;
  height: 40px;
  background: var(--primary-color);
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
  color: var(--text-primary);
  margin-bottom: 5px;
}

.stat-label {
  font-size: 12px;
  color: var(--text-muted);
}

/* ==================== */
/* PROFILE DETAILS */
/* ==================== */
.profile-details {
  margin-bottom: 20px;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid var(--border-color);
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-item label {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--text-muted);
  font-weight: 500;
  font-size: 14px;
}

.detail-item span {
  color: var(--text-primary);
  font-weight: 600;
  font-size: 14px;
  text-align: right;
}

.detail-item a {
  color: var(--text-primary);
  text-decoration: none;
}

.detail-item a:hover {
  color: var(--primary-color);
}

/* ==================== */
/* PROFILE BIO */
/* ==================== */
.profile-bio {
  margin-bottom: 20px;
  padding: 15px;
  background: var(--bg-secondary);
  border-radius: 10px;
}

.profile-bio p {
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0;
  font-size: 14px;
}

/* ==================== */
/* PROFILE SOCIAL */
/* ==================== */
.profile-social {
  text-align: center;
}

.profile-social h5 {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-primary);
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
  background: var(--bg-secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  text-decoration: none;
  transition: all 0.3s ease;
}

.profile-social .list-wrap li a:hover {
  background: var(--primary-color);
  color: #ffffff;
  transform: translateY(-3px);
}

/* ==================== */
/* EXPERIENCE CARD */
/* ==================== */
.experience-timeline {
  position: relative;
  padding-left: 30px;
}

.experience-item {
  position: relative;
  margin-bottom: 20px;
}

.exp-period {
  background: var(--primary-color);
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
  margin-bottom: 10px;
}

.exp-dot {
  position: absolute;
  left: -38px;
  top: 5px;
  width: 12px;
  height: 12px;
  background: var(--primary-color);
  border-radius: 50%;
  border: 3px solid var(--bg-primary);
}

.exp-dot::before {
  content: '';
  position: absolute;
  left: -24px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 2px;
  background: var(--primary-color);
}

.exp-content h6 {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 5px;
}

.exp-company {
  color: var(--primary-color);
  font-weight: 500;
  font-size: 14px;
  margin-bottom: 5px;
}

.exp-description {
  color: var(--text-muted);
  font-size: 13px;
  line-height: 1.5;
  margin: 0;
}

/* ==================== */
/* EDUCATION CARD */
/* ==================== */
.education-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.education-item {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  padding: 15px;
  background: var(--bg-secondary);
  border-radius: 10px;
  transition: background-color 0.3s ease;
}

.education-item:hover {
  background: var(--bg-tertiary);
}

.edu-icon {
  width: 40px;
  height: 40px;
  background: var(--primary-color);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.edu-content h6 {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 5px;
}

.edu-institution {
  color: var(--primary-color);
  font-weight: 500;
  font-size: 14px;
  margin-bottom: 5px;
}

.edu-year {
  color: var(--text-muted);
  font-size: 13px;
  margin: 0;
}

/* ==================== */
/* MAIN CONTENT WRAPPER */
/* ==================== */
.instructor__details-wrap {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* ==================== */
/* MAIN CONTENT SECTIONS */
/* ==================== */
.demo-videos-section,
.classes-section,
.instructor__details-biography {
  background: var(--card-bg);
  padding: 40px;
  border-radius: 20px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.demo-videos-section:hover,
.classes-section:hover,
.instructor__details-biography:hover {
  box-shadow: var(--shadow-lg);
}

.section-header {
  margin-bottom: 30px;
  text-align: center;
}

.section-header .main-title {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 10px;
}

.section-header p {
  color: var(--text-muted);
  margin-bottom: 0;
  font-size: 16px;
}

/* ==================== */
/* VIDEO CATEGORIES NAVIGATION */
/* ==================== */
.video-categories-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border-color);
}

.category-btn {
  padding: 10px 20px;
  border: 2px solid var(--border-color);
  border-radius: 25px;
  background: var(--card-bg);
  color: var(--text-muted);
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.category-btn:hover {
  background: var(--primary-color);
  border-color: var(--primary-color);
  color: #ffffff;
  transform: translateY(-2px);
}

.category-btn.active {
  background: var(--primary-color);
  border-color: var(--primary-color);
  color: #ffffff;
}

/* ==================== */
/* INTRO VIDEO SECTION */
/* ==================== */
.intro-video-section {
  margin-bottom: 40px;
  padding-bottom: 30px;
  border-bottom: 2px solid var(--border-color);
}

.section-subtitle {
  font-size: 20px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid var(--primary-color);
  display: inline-block;
}

.intro-video-card {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 30px;
  background: var(--bg-secondary);
  border-radius: 15px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: var(--shadow);
  border: 1px solid var(--border-color);
}

.intro-video-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
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

.intro-video-card:hover .video-overlay {
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
  color: var(--primary-color);
  font-size: 20px;
  transition: all 0.3s ease;
}

.intro-video-card:hover .play-button {
  background: #ffffff;
  transform: scale(1.1);
}

.video-duration {
  position: absolute;
  bottom: 15px;
  right: 15px;
  background: rgba(0, 0, 0, 0.7);
  color: #ffffff;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.video-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: var(--error-color);
  color: white;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
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
  color: var(--text-primary);
  margin-bottom: 15px;
  line-height: 1.3;
}

.video-stats-simple {
  display: flex;
  gap: 15px;
  font-size: 13px;
  color: var(--text-muted);
  flex-wrap: wrap;
}

.video-stats-simple span {
  display: flex;
  align-items: center;
  gap: 5px;
}

.video-class {
  background: var(--bg-tertiary);
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 11px;
  color: var(--text-primary);
}

/* ==================== */
/* CLASS VIDEOS SECTIONS - HORIZONTAL LAYOUT */
/* ==================== */
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
  scrollbar-color: var(--primary-color) var(--bg-secondary);
}

.class-videos-horizontal::-webkit-scrollbar {
  height: 8px;
}

.class-videos-horizontal::-webkit-scrollbar-track {
  background: var(--bg-secondary);
  border-radius: 10px;
}

.class-videos-horizontal::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 10px;
}

.class-videos-horizontal::-webkit-scrollbar-thumb:hover {
  background: var(--primary-hover);
}

/* ==================== */
/* HORIZONTAL VIDEO CARDS */
/* ==================== */
.video-card-horizontal {
  flex: 0 0 280px;
  background: var(--card-bg);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  border: 1px solid var(--border-color);
}

.video-card-horizontal:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
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
  color: var(--primary-color);
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
  background: var(--error-color);
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
  color: var(--text-primary);
  margin-bottom: 8px;
  line-height: 1.3;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  line-clamp: 2;
  box-orient: vertical;
}

.video-meta-simple {
  display: flex;
  gap: 12px;
  font-size: 11px;
  color: var(--text-muted);
}

.video-meta-simple span {
  display: flex;
  align-items: center;
  gap: 4px;
}

.video-class-simple {
  background: var(--bg-tertiary);
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 10px;
  color: var(--text-primary);
}

/* ==================== */
/* CLASSES GRID */
/* ==================== */
.classes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.class-card {
  background: var(--bg-secondary);
  border-radius: 12px;
  padding: 20px;
  border-left: 4px solid var(--primary-color);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
}

.class-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow);
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
  color: var(--text-primary);
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
  background: color-mix(in srgb, var(--success-color) 20%, transparent);
  color: color-mix(in srgb, var(--success-color) 70%, black);
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
  color: var(--text-muted);
}

.class-subject i,
.class-grade i,
.class-students i {
  width: 14px;
  color: var(--primary-color);
}

.class-description {
  color: var(--text-secondary);
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 15px;
}

.class-actions {
  display: flex;
  justify-content: flex-end;
}

/* ==================== */
/* TEACHING PHILOSOPHY */
/* ==================== */
.instructor__details-biography {
  background: var(--card-bg);
  padding: 40px;
  border-radius: 20px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border-color);
}

.instructor__details-biography .title {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--primary-color);
}

.instructor__details-biography p {
  color: var(--text-muted);
  line-height: 1.7;
  font-size: 16px;
  margin: 0;
}

/* ==================== */
/* BUTTON STYLES */
/* ==================== */
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
  background: var(--primary-color);
  color: #ffffff;
}

.btn-primary:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px color-mix(in srgb, var(--primary-color) 30%, transparent);
}

.btn-sm {
  padding: 8px 16px;
  font-size: 12px;
}

.btn-outline {
  background: transparent;
  border: 2px solid var(--primary-color);
  color: var(--primary-color);
}

.btn-outline:hover {
  background: var(--primary-color);
  color: #ffffff;
}

/* ==================== */
/* NO CONTENT STATES */
/* ==================== */
.no-content-state {
  text-align: center;
  padding: 60px 20px;
}

.no-content-state i {
  color: var(--text-muted);
  margin-bottom: 20px;
}

.no-content-state h4 {
  color: var(--text-secondary);
  margin-bottom: 10px;
  font-size: 18px;
  font-weight: 600;
}

.no-content-state p {
  color: var(--text-muted);
  font-size: 14px;
  max-width: 400px;
  margin: 0 auto;
}

/* ==================== */
/* LOAD MORE SECTION */
/* ==================== */
.load-more-section {
  text-align: center;
  margin-top: 40px;
  padding-top: 30px;
  border-top: 1px solid var(--border-color);
}

/* ==================== */
/* CLEAN VIDEO PLAYER MODAL - NO FILTERS */
/* ==================== */
.video-player-modal {
  background: rgba(0, 0, 0, 0.95) !important;
  z-index: 9999 !important;
  position: fixed !important;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
}

.modal-container {
  background: #000 !important;
  height: 90vh;
  width: 100%;
  max-width: 1200px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
  border: 1px solid #333;
  border-radius: 8px;
  overflow: hidden;
}

/* Header Section - 20% */
.header-section {
  background: #1a1a1a !important;
  border-bottom: 1px solid #333;
  padding: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  min-height: 80px;
  max-height: 120px;
  flex-shrink: 0;
}

.header-content {
  flex: 1;
  min-width: 0;
}

.header-section h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #fff !important;
  line-height: 1.3;
}

.video-meta {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  font-size: 0.875rem;
  color: #ccc !important;
}

.video-meta span {
  display: flex;
  align-items: center;
}

.close-button {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
  margin-left: 1rem;
  flex-shrink: 0;
  transition: color 0.3s ease;
}

.close-button:hover {
  color: #ccc;
}

/* Video Player Section - 70% */
.video-player-section {
  background: #000 !important;
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  min-height: 300px;
}

.youtube-container,
.direct-video-container {
  width: 100%;
  height: 100%;
  background: #000 !important;
}

.youtube-container iframe,
.direct-video-container video {
  width: 100% !important;
  height: 100% !important;
  border: none !important;
  outline: none !important;
  background: #000 !important;
}

/* Loading State */
.loading-state {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #000;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #fff;
  z-index: 10;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #333;
  border-left: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-state p {
  font-size: 1.125rem;
  color: #fff;
}

/* Error State */
.error-state {
  text-align: center;
  color: #fff;
  padding: 2rem;
}

.error-state i {
  font-size: 3rem;
  color: #f59e0b;
  margin-bottom: 1rem;
}

.error-state p {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

.error-subtext {
  font-size: 1rem;
  color: #9ca3af;
}

/* Footer Section - 10% */
.footer-section {
  background: #1a1a1a !important;
  border-top: 1px solid #333;
  padding: 1rem 1.5rem;
  min-height: 60px;
  max-height: 80px;
  flex-shrink: 0;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
}

.video-description {
  color: #fff !important;
  margin: 0;
  flex: 1;
  margin-right: 1rem;
  font-size: 0.875rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.no-description {
  color: #999 !important;
  margin: 0;
  flex: 1;
  margin-right: 1rem;
  font-size: 0.875rem;
  font-style: italic;
}

.close-btn {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 500;
  display: flex;
  align-items: center;
  transition: background-color 0.3s ease;
  flex-shrink: 0;
}

.close-btn:hover {
  background: #b91c1c;
}

/* ==================== */
/* RESPONSIVE DESIGN */
/* ==================== */
@media (max-width: 991px) {
  .instructor__sidebar {
    position: static;
    margin-bottom: 40px;
  }
  
  .col-xl-4,
  .col-xl-8,
  .col-lg-5,
  .col-lg-7 {
    flex: 0 0 100%;
    max-width: 100%;
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
  .instructor__details-area {
    padding: 60px 0;
  }
  
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
  
  .profile-avatar {
    width: 180px;
    height: 150px;
  }
  
  .profile-picture {
    width: 180px;
    height: 150px;
  }
  
  .modal-container {
    height: 95vh;
    margin: 0.5rem;
  }
  
  .header-section {
    padding: 1rem;
    min-height: 70px;
  }
  
  .footer-section {
    padding: 0.75rem 1rem;
    min-height: 50px;
  }
}

@media (max-width: 575px) {
  .video-meta-simple {
    flex-direction: column;
    gap: 5px;
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
  
  .profile-avatar {
    width: 150px;
    height: 125px;
  }
  
  .profile-picture {
    width: 150px;
    height: 125px;
  }
  
  .profile-info .title {
    font-size: 20px;
  }
  
  .intro-video-thumbnail {
    height: 180px;
  }
  
  .intro-video-content .video-title {
    font-size: 16px;
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
  
  .video-player-modal {
    padding: 0.5rem;
  }
  
  .modal-container {
    height: 100vh;
    margin: 0;
    border-radius: 0;
    border: none;
  }
  
  .header-section {
    min-height: 60px;
  }
  
  .footer-section {
    min-height: 50px;
  }
  
  .header-section h3 {
    font-size: 1.1rem;
  }
  
  .video-meta {
    font-size: 0.75rem;
    gap: 0.5rem;
  }
  
  .footer-content {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
  
  .close-btn {
    align-self: flex-end;
  }
}

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.btn:focus,
.category-btn:focus,
.close-button:focus,
.close-btn:focus {
  outline: 3px solid rgba(59, 130, 246, 0.5);
  outline-offset: 2px;
}

/* ==================== */
/* SCROLLBAR STYLING */
/* ==================== */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: var(--bg-secondary);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--primary-hover);
}
</style>