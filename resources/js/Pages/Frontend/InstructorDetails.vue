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
                      <img :src="instructor.avatar" :alt="instructor.name" @error="handleImageError">
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
                          <!-- Removed description -->
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
                  <div v-if="videos.length === 0" class="text-center py-8">
                    <i class="fas fa-video-slash fa-3x text-gray-400 mb-4"></i>
                    <h4 class="text-gray-600">{{ t('No Videos Available') }}</h4>
                    <p class="text-gray-500">{{ t('This instructor hasn\'t uploaded any demo videos yet.') }}</p>
                  </div>

                  <!-- No Videos in Category Message -->
                  <div v-if="filteredVideos.length === 0 && videos.length > 0" class="text-center py-8">
                    <i class="fas fa-filter fa-3x text-gray-400 mb-4"></i>
                    <h4 class="text-gray-600">{{ t('No videos in this class') }}</h4>
                    <p class="text-gray-500">{{ t('Try selecting a different class to see more videos.') }}</p>
                  </div>

                  <!-- Load More Button -->
                  <div v-if="showLoadMore" class="text-center mt-5">
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
                <div v-if="classes.length === 0 && videos.length === 0" class="text-center py-8">
                  <i class="fas fa-info-circle fa-3x text-gray-400 mb-4"></i>
                  <h4 class="text-gray-600">{{ t('Content Coming Soon') }}</h4>
                  <p class="text-gray-500">{{ t('This instructor hasn\'t uploaded any content yet.') }}</p>
                </div>

                <!-- Teaching Philosophy -->
                <div class="instructor__details-biography" v-if="instructor.teaching_philosophy">
                  <h4 class="title">{{ t('Teaching Philosophy') }}</h4>
                  <p>{{ instructor.teaching_philosophy }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Video Player Modal - Safe Version -->
      <div 
        v-if="showVideoPlayer && currentVideo" 
        class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-50"
        @click="closeVideoPlayer"
      >
        <div 
          class="bg-black rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden"
          @click.stop
        >
          <!-- Custom Header -->
          <div class="px-6 py-3 bg-black flex justify-between items-center border-b border-gray-800">
            <h3 class="text-lg font-semibold text-white">{{ getCleanTitle(currentVideo.title) }}</h3>
            <button @click="closeVideoPlayer" class="text-gray-300 hover:text-white transition-colors">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="bg-black flex items-center justify-center">
            <!-- Pure Video Player for direct streams -->
            <div v-if="currentVideo.directVideoUrl" class="w-full aspect-video">
              <video 
                ref="videoPlayer"
                controls
                autoplay
                class="w-full h-full"
                :poster="currentVideo.thumbnail"
              >
                <source :src="currentVideo.directVideoUrl" type="video/mp4">
                {{ t('Your browser does not support the video tag.') }}
              </video>
            </div>
            
            <!-- Ultra-clean YouTube embed as fallback -->
            <div v-else-if="currentVideo.ultraCleanUrl" class="w-full aspect-video relative">
              <iframe 
                :src="currentVideo.ultraCleanUrl"
                class="w-full h-full"
                frameborder="0"
                allow="autoplay; encrypted-media"
                allowfullscreen
              ></iframe>
              
              <!-- Aggressive overlay to hide YouTube UI -->
              <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-0 w-full h-16 bg-black pointer-events-auto"></div>
                <div class="absolute bottom-0 left-0 w-full h-20 bg-black pointer-events-auto"></div>
                <div class="absolute top-0 right-0 w-24 h-24 bg-black pointer-events-auto"></div>
              </div>
            </div>
            
            <!-- Error state -->
            <div v-else class="w-full h-full flex items-center justify-center text-white">
              <div class="text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p class="text-lg mb-2">{{ t('Unable to load video') }}</p>
                <p class="text-sm text-gray-300 mb-4">{{ t('The video could not be loaded.') }}</p>
                <div class="flex space-x-2 justify-center">
                  <button 
                    @click="closeVideoPlayer"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                  >
                    {{ t('Close') }}
                  </button>
                  <button 
                    v-if="currentVideo.originalUrl"
                    @click="openInNewTab(currentVideo.originalUrl)"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                  >
                    {{ t('Open Original') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </FrontendLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
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
    const currentVideo = ref(null);
    const showVideoPlayer = ref(false);
    const videoPlayer = ref(null);
    const activeCategory = ref('all');
    const videosPerPage = ref(6);
    const currentPage = ref(1);
    const currentLanguage = ref('bn'); // Default to Bengali
    const currentTheme = ref('light');

    // Define translations object
    const translations = {
      en: {
        'reviews': 'reviews',
        'Total Classes': 'Total Classes',
        'Students Taught': 'Students Taught',
        'Demo Videos': 'Demo Videos',
        'Expertise': 'Expertise',
        'Languages': 'Languages',
        'Response Time': 'Response Time',
        'Email': 'Email',
        'Experience': 'Experience',
        'years': 'years',
        'Follow Instructor': 'Follow Instructor',
        'Teaching Experience': 'Teaching Experience',
        'Years': 'Years',
        'Educational Institution': 'Educational Institution',
        'Teaching': 'Teaching',
        'various subjects': 'various subjects',
        'Education & Certification': 'Education & Certification',
        'Teaching Certification': 'Teaching Certification',
        'Class Videos': 'Class Videos',
        'Watch classes to experience teaching style': 'Watch classes to experience teaching style',
        'All Videos': 'All Videos',
        'Intro': 'Intro',
        'Demo': 'Demo',
        'No Videos Available': 'No Videos Available',
        "This instructor hasn't uploaded any demo videos yet.": "This instructor hasn't uploaded any demo videos yet.",
        'No videos in this class': 'No videos in this class',
        'Try selecting a different class to see more videos.': 'Try selecting a different class to see more videos.',
        'Load More Videos': 'Load More Videos',
        'Classes Taught': 'Classes Taught',
        'Subjects and classes currently being taught': 'Subjects and classes currently being taught',
        'Active': 'Active',
        'Grade': 'Grade',
        'students': 'students',
        'Comprehensive course covering essential topics': 'Comprehensive course covering essential topics',
        'View Course': 'View Course',
        'Content Coming Soon': 'Content Coming Soon',
        "This instructor hasn't uploaded any content yet.": "This instructor hasn't uploaded any content yet.",
        'Teaching Philosophy': 'Teaching Philosophy',
        'Your browser does not support the video tag.': 'Your browser does not support the video tag.',
        'Unable to load video': 'Unable to load video',
        'The video could not be loaded.': 'The video could not be loaded.',
        'Close': 'Close',
        'Open Original': 'Open Original',
        'PhD Specialist': 'PhD Specialist',
        'Master Educator': 'Master Educator',
        'Science Expert': 'Science Expert',
        'Bachelor Educator': 'Bachelor Educator',
        'Science Teacher': 'Science Teacher',
        'Certified Teacher': 'Certified Teacher',
        'Professional Instructor': 'Professional Instructor',
        'General Education': 'General Education',
        'Invalid Date': 'Invalid Date',
        'Successfully enrolled in the course!': 'Successfully enrolled in the course!',
        'Failed to enroll. Please try again.': 'Failed to enroll. Please try again.',
        'Playing preview:': 'Playing preview:',
        'Starting lesson:': 'Starting lesson:'
      },
      bn: {
        'reviews': 'রিভিউ',
        'Total Classes': 'মোট ক্লাস',
        'Students Taught': 'শিক্ষার্থী পড়িয়েছেন',
        'Demo Videos': 'ডেমো ভিডিও',
        'Expertise': 'দক্ষতা',
        'Languages': 'ভাষা',
        'Response Time': 'প্রতিক্রিয়া সময়',
        'Email': 'ইমেইল',
        'Experience': 'অভিজ্ঞতা',
        'years': 'বছর',
        'Follow Instructor': 'ইন্সট্রাক্টর ফলো করুন',
        'Teaching Experience': 'শিক্ষণ অভিজ্ঞতা',
        'Years': 'বছর',
        'Educational Institution': 'শিক্ষাগত প্রতিষ্ঠান',
        'Teaching': 'শিক্ষাদান',
        'various subjects': 'বিভিন্ন বিষয়',
        'Education & Certification': 'শিক্ষা ও সার্টিফিকেশন',
        'Teaching Certification': 'শিক্ষণ সার্টিফিকেশন',
        'Class Videos': 'ক্লাস ভিডিও',
        'Watch classes to experience teaching style': 'শিক্ষণ শৈলী অনুভব করতে ক্লাস দেখুন',
        'All Videos': 'সমস্ত ভিডিও',
        'Intro': 'ইন্ট্রো',
        'Demo': 'ডেমো',
        'No Videos Available': 'কোন ভিডিও উপলব্ধ নেই',
        "This instructor hasn't uploaded any demo videos yet.": 'এই ইন্সট্রাক্টর এখনও কোন ডেমো ভিডিও আপলোড করেননি।',
        'No videos in this class': 'এই ক্লাসে কোন ভিডিও নেই',
        'Try selecting a different class to see more videos.': 'আরও ভিডিও দেখতে একটি ভিন্ন ক্লাস নির্বাচন করুন।',
        'Load More Videos': 'আরও ভিডিও লোড করুন',
        'Classes Taught': 'পড়ানো ক্লাস',
        'Subjects and classes currently being taught': 'বর্তমানে পড়ানো বিষয় এবং ক্লাস',
        'Active': 'সক্রিয়',
        'Grade': 'গ্রেড',
        'students': 'শিক্ষার্থী',
        'Comprehensive course covering essential topics': 'প্রয়োজনীয় বিষয় covering সম্পূর্ণ কোর্স',
        'View Course': 'কোর্স দেখুন',
        'Content Coming Soon': 'কন্টেন্ট শীঘ্রই আসছে',
        "This instructor hasn't uploaded any content yet.": 'এই ইন্সট্রাক্টর এখনও কোন কন্টেন্ট আপলোড করেননি।',
        'Teaching Philosophy': 'শিক্ষণ দর্শন',
        'Your browser does not support the video tag.': 'আপনার ব্রাউজার ভিডিও ট্যাগ সমর্থন করে না।',
        'Unable to load video': 'ভিডিও লোড করতে ব্যর্থ',
        'The video could not be loaded.': 'ভিডিও লোড করা যায়নি।',
        'Close': 'বন্ধ',
        'Open Original': 'অরিজিনাল খুলুন',
        'PhD Specialist': 'পিএইচডি বিশেষজ্ঞ',
        'Master Educator': 'মাস্টার শিক্ষক',
        'Science Expert': 'বিজ্ঞান বিশেষজ্ঞ',
        'Bachelor Educator': 'ব্যাচেলর শিক্ষক',
        'Science Teacher': 'বিজ্ঞান শিক্ষক',
        'Certified Teacher': 'সার্টিফাইড শিক্ষক',
        'Professional Instructor': 'পেশাদার ইন্সট্রাক্টর',
        'General Education': 'সাধারণ শিক্ষা',
        'Invalid Date': 'অবৈধ তারিখ',
        'Successfully enrolled in the course!': 'কোর্সে সফলভাবে নিবন্ধিত!',
        'Failed to enroll. Please try again.': 'নিবন্ধন ব্যর্থ। দয়া করে আবার চেষ্টা করুন।',
        'Playing preview:': 'প্রিভিউ চলছে:',
        'Starting lesson:': 'লেসন শুরু হচ্ছে:'
      }
    };

    // Translation function
    const t = (key, replacements = {}) => {
      let translated = translations[currentLanguage.value]?.[key] || key;
      
      Object.keys(replacements).forEach(replacementKey => {
        translated = translated.replace(`{${replacementKey}}`, replacements[replacementKey]);
      });
      
      return translated;
    };

    // Handle language changes from navbar
    const handleLanguageChange = (event) => {
      currentLanguage.value = event.detail.language;
    };

    // Handle theme changes
    const handleThemeChange = (event) => {
      currentTheme.value = event.detail.theme;
    };

    // Load language and theme preferences from localStorage
    onMounted(() => {
      const savedLang = localStorage.getItem('preferredLanguage');
      if (savedLang && (savedLang === 'en' || savedLang === 'bn')) {
        currentLanguage.value = savedLang;
      }

      const savedTheme = localStorage.getItem('preferredTheme');
      currentTheme.value = savedTheme || 'light';
      
      // Listen for language changes from navbar
      window.addEventListener('languageChanged', handleLanguageChange);
      
      // Listen for theme changes
      window.addEventListener('themeChanged', handleThemeChange);
    });

    onUnmounted(() => {
      window.removeEventListener('languageChanged', handleLanguageChange);
      window.removeEventListener('themeChanged', handleThemeChange);
    });

    // COMPUTED PROPERTIES
    const uniqueClasses = computed(() => {
      return props.classes.filter((classItem, index, self) => 
        index === self.findIndex(c => c.id === classItem.id)
      );
    });

    const introVideo = computed(() => {
      if (props.videos.length === 0) return null;
      
      // Find first class with videos
      const firstClassWithVideos = classesWithVideos.value[0];
      if (!firstClassWithVideos) return props.videos[0];
      
      // Get videos for that class
      const firstClassVideos = getVideosByClass(firstClassWithVideos.id);
      return firstClassVideos[0] || props.videos[0];
    });

    const getCleanTitle = (title) => {
      if (!title) return t('Untitled Video');
      
      // Remove URLs from title
      let cleanTitle = title.replace(/https?:\/\/[^\s]+/g, '').trim();
      
      // Remove YouTube specific patterns
      cleanTitle = cleanTitle.replace(/youtu\.be\/[^\s]+/g, '');
      cleanTitle = cleanTitle.replace(/youtube\.com\/watch\?v=[^\s]+/g, '');
      cleanTitle = cleanTitle.replace(/\?si=[^\s]+/g, '');
      
      // Remove extra spaces and clean up
      cleanTitle = cleanTitle.replace(/\s+/g, ' ').trim();
      
      // If title is empty after cleaning, use a default
      if (!cleanTitle) return t('Demo Video');
      
      return cleanTitle;
    };

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

    // VIDEO PLAYER METHODS
    const getVideoContent = (video) => {
      if (!video) return null;
      
      // Check all possible fields that might contain the URL
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
          
          // Check if it contains YouTube pattern but missing protocol
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

    const generateUltraCleanEmbedUrl = (videoId) => {
      const params = new URLSearchParams({
        'autoplay': '1',
        'rel': '0',
        'modestbranding': '1',
        'controls': '0',
        'showinfo': '0',
        'iv_load_policy': '3',
        'fs': '0',
        'disablekb': '1',
        'playsinline': '1',
        'enablejsapi': '1',
        'origin': window.location.origin,
        'widget_referrer': window.location.origin,
        'cc_load_policy': '0',
        'color': 'white',
        'hl': 'en',
        'cc_lang_pref': 'en',
        'version': '3',
        'loop': '0',
        'playlist': videoId,
        'mute': '0',
        'start': '0',
        'end': '0'
      });
      
      return `https://www.youtube-nocookie.com/embed/${videoId}?${params.toString()}`;
    };

    const playVideo = async (video) => {
      console.log('🎬 [playVideo] Attempting to play video:', video.title);
      
      // Create clean video object first
      const cleanVideo = {
        ...video,
        title: getCleanTitle(video.title)
      };
      
      const videoContent = getVideoContent(video);
      
      if (!videoContent) {
        alert(`No video content found for: ${cleanVideo.title}`);
        return;
      }
      
      if (isYouTubeUrl(videoContent)) {
        const videoId = getYouTubeVideoId(videoContent);
        
        if (videoId) {
          console.log('✅ Playing YouTube video with ID:', videoId);
          
          // Try to get direct video stream first
          const directVideoUrl = await getDirectVideoStream(videoId);
          
          if (directVideoUrl) {
            console.log('🎯 Using direct video stream');
            currentVideo.value = {
              ...cleanVideo,
              directVideoUrl: directVideoUrl,
              videoId: videoId,
              thumbnail: getVideoThumbnail(video),
              isDirectStream: true
            };
          } else {
            console.log('🔄 Using ultra-clean embed as fallback');
            currentVideo.value = {
              ...cleanVideo,
              ultraCleanUrl: generateUltraCleanEmbedUrl(videoId),
              videoId: videoId,
              originalUrl: videoContent,
              isEmbed: true
            };
          }
          
        } else {
          alert('Could not extract YouTube video ID from the URL.');
          return;
        }
      } else {
        // Handle non-YouTube URLs (direct video files)
        console.log('🎯 Using direct video file');
        currentVideo.value = {
          ...cleanVideo,
          directVideoUrl: videoContent,
          isDirectVideo: true
        };
      }
      
      showVideoPlayer.value = true;
      document.body.style.overflow = 'hidden';
    };

    const closeVideoPlayer = () => {
      // Stop video if playing
      if (videoPlayer.value && videoPlayer.value.pause) {
        videoPlayer.value.pause();
        videoPlayer.value.currentTime = 0;
      }
      
      showVideoPlayer.value = false;
      currentVideo.value = null;
      document.body.style.overflow = 'auto';
    };

    const openInNewTab = (url) => {
      window.open(url, '_blank', 'noopener,noreferrer');
    };

    const getDirectVideoStream = async (videoId) => {
      try {
        console.log('🔍 Getting direct video stream for:', videoId);
        
        // Method 1: Try our new backend API first
        try {
          const response = await fetch(`/api/youtube-direct-stream?videoId=${videoId}`);
          const data = await response.json();
          
          if (data.success && data.directUrl) {
            console.log('✅ Got direct video stream from API:', data.directUrl);
            return data.directUrl;
          }
        } catch (apiError) {
          console.log('❌ API method failed, trying proxy...');
        }
        
        // Method 2: Use our proxy as fallback
        const proxyUrl = `/api/video-proxy/${videoId}`;
        console.log('🔄 Using proxy URL:', proxyUrl);
        
        // Test if proxy URL is accessible
        try {
          const testResponse = await fetch(proxyUrl, { method: 'HEAD' });
          if (testResponse.ok) {
            console.log('✅ Proxy URL is accessible');
            return proxyUrl;
          }
        } catch (proxyError) {
          console.log('❌ Proxy URL not accessible');
        }
        
        // Method 3: Fallback to ultra-clean embed
        console.log('🔄 Falling back to ultra-clean embed');
        return null;
        
      } catch (error) {
        console.error('❌ Error getting direct video stream:', error);
        return null;
      }
    };

    // EXISTING METHODS
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

    const getEmbedUrl = (video) => {
      if (video.youtube_embed_url) {
        return video.youtube_embed_url;
      }
      
      const videoContent = getVideoContent(video);
      if (videoContent && isYouTubeUrl(videoContent)) {
        const videoId = getYouTubeVideoId(videoContent);
        if (videoId) {
          return `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1`;
        }
      }
      
      return video.videoUrl || '';
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

    const handleImageError = (event) => {
      event.target.src = '/assets/img/instructor/instructor01.png';
    };

    const handleVideoThumbnailError = (event) => {
      event.target.src = '/assets/img/courses/video_thumb01.jpg';
    };

    return {
      selectedVideo,
      currentVideo,
      showVideoPlayer,
      videoPlayer,
      activeCategory,
      uniqueClasses,
      introVideo,
      classesWithVideos,
      filteredVideos,
      showLoadMore,
      currentLanguage,
      currentTheme,
      t,
      playVideo,
      closeVideoPlayer,
      openInNewTab,
      getVideoContent,
      isYouTubeUrl,
      playVideo,
      closeVideoPlayer,
      openInNewTab,
      getCleanTitle,
      getInstructorTitle,
      getClassName,
      getVideoThumbnail,
      getEmbedUrl,
      getVideosByClass,
      formatDate,
      setActiveCategory,
      loadMoreVideos,
      handleImageError,
      handleVideoThumbnailError,
      getCleanTitle
    };
  }
}
</script>

<style scoped>
/* ==================== */
/* MAIN LAYOUT */
/* ==================== */
.main-area {
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

/* ==================== */
/* INSTRUCTOR DETAILS AREA */
/* ==================== */
.instructor__details-area {
  background: var(--bg-primary);
  padding: 80px 0;
  transition: background-color 0.3s ease;
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

.sidebar-card .title {
  font-size: 18px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--border-color);
  transition: all 0.3s ease;
}

/* ==================== */
/* PROFILE CARD STYLES */
/* ==================== */
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
  width: 140px;
  height: 140px;
  border-radius: 50%;
  object-fit: cover;
  border: 5px solid var(--bg-secondary);
  box-shadow: var(--shadow);
  transition: all 0.3s ease;
}

.profile-info {
  width: 100%;
  text-align: center;
}

.profile-info .title {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 5px;
  border-bottom: none;
  padding-bottom: 0;
  transition: color 0.3s ease;
}

.designation {
  color: var(--primary-color);
  font-weight: 600;
  font-size: 16px;
  display: block;
  margin-bottom: 10px;
  transition: color 0.3s ease;
}

.rating {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: var(--text-muted);
  font-size: 14px;
  transition: color 0.3s ease;
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
  transition: background-color 0.3s ease;
}

.stat-number {
  display: block;
  font-size: 18px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 5px;
  transition: color 0.3s ease;
}

.stat-label {
  font-size: 12px;
  color: var(--text-muted);
  transition: color 0.3s ease;
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
  transition: border-color 0.3s ease;
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
  transition: color 0.3s ease;
}

.detail-item span {
  color: var(--text-primary);
  font-weight: 600;
  font-size: 14px;
  text-align: right;
  transition: color 0.3s ease;
}

.detail-item a {
  color: var(--text-primary);
  text-decoration: none;
  transition: color 0.3s ease;
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
  transition: background-color 0.3s ease;
}

.profile-bio p {
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0;
  font-size: 14px;
  transition: color 0.3s ease;
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
  transition: color 0.3s ease;
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

.section-header {
  margin-bottom: 30px;
}

.section-header .main-title {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 10px;
  transition: color 0.3s ease;
}

.section-header p {
  color: var(--text-muted);
  margin-bottom: 0;
  transition: color 0.3s ease;
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
  transition: border-color 0.3s ease;
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
}

.category-btn:hover,
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
  transition: border-color 0.3s ease;
}

.section-subtitle {
  font-size: 20px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid var(--primary-color);
  display: inline-block;
  transition: color 0.3s ease;
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
  transition: color 0.3s ease;
}

.intro-video-content .video-description {
  font-size: 16px;
  line-height: 1.6;
  color: var(--text-muted);
  margin-bottom: 20px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  line-clamp: 3;
  box-orient: vertical;
  transition: color 0.3s ease;
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
  transition: color 0.3s ease;
}

.video-meta-horizontal {
  margin-bottom: 8px;
}

.video-date-horizontal {
  color: var(--text-muted);
  font-size: 11px;
  transition: color 0.3s ease;
}

.video-stats-horizontal {
  display: flex;
  gap: 12px;
  font-size: 11px;
  color: var(--text-muted);
  transition: color 0.3s ease;
}

.video-stats-horizontal span {
  display: flex;
  align-items: center;
  gap: 4px;
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
  transition: color 0.3s ease;
}

.class-status {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.class-status.active {
  background: color-mix(in srgb, var(--success-color) 20%, var(--bg-primary));
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
  transition: color 0.3s ease;
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
  transition: color 0.3s ease;
}

.class-actions {
  display: flex;
  justify-content: flex-end;
}

/* ==================== */
/* VIDEO MODAL */
/* ==================== */
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
  background: var(--card-bg);
  border-radius: 15px;
  max-width: 1000px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s ease;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
  border: 1px solid var(--border-color);
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
  background: var(--card-bg);
  color: var(--text-primary);
  transition: all 0.3s ease;
}

.video-info h4 {
  font-size: 22px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 15px;
  line-height: 1.3;
  transition: color 0.3s ease;
}

.video-info p {
  color: var(--text-muted);
  line-height: 1.6;
  margin-bottom: 20px;
  font-size: 15px;
  transition: color 0.3s ease;
}

.video-meta-modal {
  display: flex;
  gap: 25px;
  font-size: 14px;
  color: var(--text-muted);
  margin-bottom: 25px;
  flex-wrap: wrap;
  transition: color 0.3s ease;
}

.video-meta-modal span {
  display: flex;
  align-items: center;
  gap: 8px;
}

.video-class-modal {
  background: var(--bg-tertiary);
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  color: var(--text-primary);
  transition: all 0.3s ease;
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
  color: var(--text-muted);
  padding: 40px;
  transition: color 0.3s ease;
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
/* TEXT UTILITIES */
/* ==================== */
.text-gray-400 { color: var(--text-muted); }
.text-gray-500 { color: var(--text-muted); }
.text-gray-600 { color: var(--text-secondary); }

.py-8 { padding-top: 2rem; padding-bottom: 2rem; }
.mb-4 { margin-bottom: 1rem; }
.mt-5 { margin-top: 1.25rem; }

/* ==================== */
/* LANGUAGE SUPPORT */
/* ==================== */
.bn-lang .sidebar-card,
.bn-lang .demo-videos-section,
.bn-lang .classes-section,
.bn-lang .instructor__details-biography,
.bn-lang .btn,
.bn-lang .category-btn {
  font-family: 'Kalpurush', 'SolaimanLipi', 'Siyam Rupali', Arial, sans-serif;
}

.bn-lang .profile-info .title,
.bn-lang .section-header .main-title,
.bn-lang .video-title,
.bn-lang .video-title-horizontal,
.bn-lang .class-name {
  line-height: 1.5;
}

.bn-lang .profile-bio p,
.bn-lang .class-description,
.bn-lang .video-description {
  line-height: 1.7;
}

.bn-lang .stat-label,
.bn-lang .detail-item label,
.bn-lang .detail-item span {
  line-height: 1.4;
}

/* ==================== */
/* DARK THEME ENHANCEMENTS */
/* ==================== */
.dark-theme .video-modal .modal-content {
  background: var(--bg-secondary);
}

.dark-theme .video-info {
  background: var(--bg-secondary);
}

.dark-theme .class-card {
  background: var(--card-bg);
}

.dark-theme .intro-video-card {
  background: var(--bg-secondary);
}

.dark-theme .profile-stats .stat-item {
  background: var(--card-bg);
}

.dark-theme .profile-bio {
  background: var(--card-bg);
}

.dark-theme .profile-social .list-wrap li a {
  background: var(--card-bg);
}

/* ==================== */
/* RESPONSIVE DESIGN */
/* ==================== */
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

/* ==================== */
/* ACCESSIBILITY */
/* ==================== */
.btn:focus,
.category-btn:focus,
.close-modal:focus {
  outline: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent);
  outline-offset: 2px;
}

/* ==================== */
/* REDUCED MOTION */
/* ==================== */
@media (prefers-reduced-motion: reduce) {
  .sidebar-card,
  .video-card-horizontal,
  .class-card,
  .intro-video-card,
  .btn,
  .category-btn,
  .close-modal {
    transition: none;
  }
  
  .video-card-horizontal:hover,
  .class-card:hover,
  .intro-video-card:hover,
  .btn:hover:not(:disabled),
  .category-btn:hover {
    transform: none;
  }
  
  .video-card-horizontal:hover .video-thumbnail-horizontal img,
  .intro-video-card:hover .intro-video-thumbnail img {
    transform: none;
  }
  
  .fadeIn,
  .slideUp {
    animation: none;
  }
}
</style>