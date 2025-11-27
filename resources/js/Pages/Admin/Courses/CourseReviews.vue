<template>
  <div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
    <!-- Sidebar - Hidden on mobile, shown on desktop -->
    <div class="hidden lg:block">
      <Sidebar />
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div v-if="mobileSidebarOpen" class="fixed inset-0 z-40 lg:hidden">
      <div class="fixed inset-0 bg-gray-600 opacity-75" @click="closeMobileMenu"></div>
      <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
        <Sidebar 
          :isMobileMenuOpen="mobileSidebarOpen"
          @close-mobile="closeMobileMenu"
          @menu-click="closeMobileMenu"
        />
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64">
      <!-- Top Navbar with Mobile Menu Button -->
      <Navbar 
        page-title="Course Reviews" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />

      <!-- Page Content -->
      <div class="p-4 lg:p-6">
        <!-- Header -->
        <div class="mb-6">
          <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Course Reviews</h1>
            <p class="text-gray-600 text-sm lg:text-base">Monitor and analyze course feedback and performance</p>
            <p v-if="dataSource === 'mock'" class="text-yellow-600 text-xs lg:text-sm mt-1">
              ⚠️ Using demonstration data
            </p>
          </div>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mb-4 lg:mb-6 p-3 lg:p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700 text-sm lg:text-base">{{ error }}</span>
          </div>
        </div>

        <!-- Info Display for Mock Data -->
        <div v-if="dataSource === 'mock' && !error" class="mb-4 lg:mb-6 p-3 lg:p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <div class="flex items-start">
            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
              <p class="text-blue-700 font-medium text-sm lg:text-base">Demo Mode</p>
              <p class="text-blue-600 text-xs lg:text-sm">Showing demonstration data. Add reviews to your database to see real data.</p>
            </div>
          </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8">
          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4 xl:p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex justify-between items-start">
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600 mb-1">Average Rating</p>
                <h3 class="text-xl lg:text-2xl xl:text-3xl font-bold text-blue-600">{{ averageRating }}</h3>
                <div class="flex items-center mt-1">
                  <span class="text-yellow-400 text-sm lg:text-base">{{ generateStars(averageRating) }}</span>
                  <span class="text-xs lg:text-sm text-gray-500 ml-2">/ 5.0</span>
                </div>
              </div>
              <div class="p-2 lg:p-3 bg-blue-100 rounded-lg flex-shrink-0 ml-2">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4 xl:p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex justify-between items-start">
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600 mb-1">Total Reviews</p>
                <h3 class="text-xl lg:text-2xl xl:text-3xl font-bold text-green-600">{{ totalReviews }}</h3>
                <p class="text-xs text-green-600 mt-1">↑ 12% this month</p>
              </div>
              <div class="p-2 lg:p-3 bg-green-100 rounded-lg flex-shrink-0 ml-2">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4 xl:p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex justify-between items-start">
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600 mb-1">Response Rate</p>
                <h3 class="text-xl lg:text-2xl xl:text-3xl font-bold text-purple-600">{{ responseRate }}%</h3>
                <p class="text-xs text-gray-500 mt-1">Teacher responses</p>
              </div>
              <div class="p-2 lg:p-3 bg-purple-100 rounded-lg flex-shrink-0 ml-2">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4 xl:p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex justify-between items-start">
              <div class="min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600 mb-1">Satisfaction</p>
                <h3 class="text-xl lg:text-2xl xl:text-3xl font-bold text-orange-600">{{ satisfactionRate }}%</h3>
                <p class="text-xs text-gray-500 mt-1">Student satisfaction</p>
              </div>
              <div class="p-2 lg:p-3 bg-orange-100 rounded-lg flex-shrink-0 ml-2">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 xl:w-6 xl:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Classes Grid for Reviews -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 lg:gap-4 mb-6 lg:mb-8">
          <div 
            v-for="classItem in classes" 
            :key="classItem.id"
            class="bg-white rounded-lg border border-gray-200 p-3 lg:p-4 xl:p-6 text-center cursor-pointer hover:shadow-lg transition-all duration-200 group"
            @click="viewClassReviews(classItem.grade)"
          >
            <div class="w-10 h-10 lg:w-12 lg:h-12 xl:w-16 xl:h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2 lg:mb-3 xl:mb-4 group-hover:bg-blue-200 transition-colors">
              <span class="text-lg lg:text-xl xl:text-2xl font-bold text-blue-600">{{ classItem.grade }}</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1 lg:mb-2 text-sm lg:text-base group-hover:text-blue-600 transition-colors">{{ classItem.name }}</h3>
            
            <!-- Rating Display -->
            <div class="flex justify-center items-center mb-1 lg:mb-2">
              <span class="text-yellow-400 text-xs lg:text-sm">{{ generateStars(classItem.rating) }}</span>
              <span class="text-xs text-gray-500 ml-1">{{ classItem.rating }}</span>
            </div>
            
            <p class="text-xs text-gray-500">{{ classItem.reviewCount }} reviews</p>
            
            <!-- Progress Bar -->
            <div class="mt-2 lg:mt-3">
              <div class="w-full bg-gray-200 rounded-full h-1.5 lg:h-2">
                <div 
                  class="h-1.5 lg:h-2 rounded-full transition-all duration-300"
                  :class="getRatingColor(classItem.rating)"
                  :style="{ width: `${(classItem.rating / 5) * 100}%` }"
                ></div>
              </div>
              <p class="text-xs text-gray-500 mt-1">{{ Math.round((classItem.rating / 5) * 100) }}% positive</p>
            </div>
          </div>
        </div>

        <!-- Recent Reviews -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-2 sm:space-y-0">
              <h3 class="text-lg font-semibold text-gray-800">Recent Reviews</h3>
              <span class="px-2 lg:px-3 py-1 bg-blue-100 text-blue-800 text-xs lg:text-sm rounded-full font-medium w-fit">
                {{ recentReviews.length }} reviews
              </span>
            </div>
          </div>
          <div class="p-4 lg:p-6">
            <div class="space-y-3 lg:space-y-4">
              <div 
                v-for="review in recentReviews" 
                :key="review.id"
                class="border border-gray-200 rounded-lg p-3 lg:p-4 hover:border-blue-300 transition-colors duration-200"
              >
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-3 space-y-2 lg:space-y-0">
                  <div class="flex items-center space-x-2 lg:space-x-3">
                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                      <span class="text-white font-semibold text-xs lg:text-sm">{{ getInitials(review.studentName) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <h4 class="font-medium text-gray-800 text-sm lg:text-base">{{ review.studentName }}</h4>
                      <p class="text-xs lg:text-sm text-gray-600 truncate">{{ review.className }} • {{ review.subject }}</p>
                    </div>
                  </div>
                  <div class="text-left lg:text-right">
                    <div class="flex items-center">
                      <span class="text-yellow-400 mr-1 text-sm lg:text-base">{{ generateStars(review.rating) }}</span>
                      <span class="text-sm font-medium text-gray-700">{{ review.rating }}/5</span>
                    </div>
                    <p class="text-xs text-gray-500">{{ review.date }}</p>
                  </div>
                </div>
                
                <p class="text-gray-700 text-sm lg:text-base mb-2 lg:mb-3 line-clamp-2 lg:line-clamp-3">{{ review.comment }}</p>
                
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-2 lg:space-y-0">
                  <div class="flex flex-wrap gap-1 lg:gap-2">
                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                      {{ review.teacherName }}
                    </span>
                    <span v-if="review.response" class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">
                      Replied
                    </span>
                    <span v-else class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">
                      Pending
                    </span>
                  </div>
                  <button class="text-blue-600 hover:text-blue-800 text-xs lg:text-sm font-medium transition-colors text-left lg:text-right mt-1 lg:mt-0">
                    View Details
                  </button>
                </div>
                
                <!-- Teacher Response -->
                <div v-if="review.response" class="mt-2 lg:mt-3 p-2 lg:p-3 bg-gray-50 rounded-lg border border-gray-200">
                  <div class="flex items-start space-x-2">
                    <svg class="w-3 h-3 lg:w-4 lg:h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 min-w-0">
                      <p class="text-xs lg:text-sm text-gray-700">
                        <span class="font-medium">Teacher Response:</span> {{ review.response }}
                      </p>
                      <p class="text-xs text-gray-500 mt-1">Responded on {{ review.responseDate }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Empty State -->
            <div v-if="recentReviews.length === 0 && !loading" class="text-center py-6 lg:py-8">
              <svg class="w-12 h-12 lg:w-16 lg:h-16 text-gray-400 mx-auto mb-3 lg:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
              </svg>
              <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
              <p class="text-gray-500 text-sm lg:text-base">Student reviews will appear here once they are submitted.</p>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8 lg:py-12">
          <div class="animate-spin rounded-full h-10 w-10 lg:h-12 lg:w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-600 mt-3 lg:mt-4 text-sm lg:text-base">Loading review data...</p>
        </div>

        <!-- Database Setup Instructions -->
        <div v-if="!loading && dataSource === 'mock'" class="mt-6 lg:mt-8 p-4 lg:p-6 bg-gray-50 rounded-lg border border-gray-200">
          <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-3">Setup Your Database</h3>
          <p class="text-gray-600 text-sm lg:text-base mb-4">To use real review data, you need to:</p>
          <div class="space-y-2 text-xs lg:text-sm text-gray-600">
            <p>1. Run migrations: <code class="bg-gray-200 px-2 py-1 rounded font-mono text-xs">php artisan migrate</code></p>
            <p>2. Seed the database: <code class="bg-gray-200 px-2 py-1 rounded font-mono text-xs">php artisan db:seed</code></p>
            <p>3. Add reviews and ratings to your database</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

const classes = ref([])
const recentReviews = ref([])
const loading = ref(true)
const error = ref('')
const dataSource = ref('database')
const mobileSidebarOpen = ref(false)

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Mobile menu functions
const toggleMobileMenu = () => {
  mobileSidebarOpen.value = !mobileSidebarOpen.value
}

const closeMobileMenu = () => {
  mobileSidebarOpen.value = false
}

// Authentication check
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    router.visit('/login')
    return
  }
}

// Existing computed properties and functions
const averageRating = computed(() => {
  if (recentReviews.value.length === 0) return 4.2
  const total = recentReviews.value.reduce((sum, review) => sum + review.rating, 0)
  return (total / recentReviews.value.length).toFixed(1)
})

const totalReviews = computed(() => {
  return classes.value.reduce((total, classItem) => total + classItem.reviewCount, 0)
})

const responseRate = computed(() => {
  if (recentReviews.value.length === 0) return 89
  const responded = recentReviews.value.filter(review => review.response).length
  return Math.round((responded / recentReviews.value.length) * 100)
})

const satisfactionRate = computed(() => {
  if (recentReviews.value.length === 0) return 92
  const satisfied = recentReviews.value.filter(review => review.rating >= 4).length
  return Math.round((satisfied / recentReviews.value.length) * 100)
})

const fetchReviewData = async () => {
  try {
    loading.value = true
    error.value = ''
    dataSource.value = 'database'
    
    console.log('📡 Fetching review data from API...')
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Use mock data for demonstration
    classes.value = getMockClasses()
    recentReviews.value = getMockReviews()
    
  } catch (err) {
    console.error('💥 Error fetching review data:', err)
    error.value = 'Failed to load review data. Please try again.'
    dataSource.value = 'mock'
    
    // Fallback to mock data
    classes.value = getMockClasses()
    recentReviews.value = getMockReviews()
  } finally {
    loading.value = false
  }
}

const getMockClasses = () => {
  return Array.from({ length: 12 }, (_, i) => ({
    id: i + 1,
    grade: i + 1,
    name: `Class ${i + 1}`,
    reviewCount: Math.floor(Math.random() * 50) + 10,
    rating: parseFloat((Math.random() * 1.5 + 3.5).toFixed(1))
  }))
}

const getMockReviews = () => {
  return [
    {
      id: 1,
      studentName: 'John Doe',
      className: 'Class 10',
      subject: 'Mathematics',
      rating: 4.5,
      comment: 'Excellent teaching methodology. The concepts were explained very clearly and the practical examples helped me understand complex topics better.',
      date: '2 days ago',
      teacherName: 'Dr. Smith',
      response: 'Thank you for your feedback, John! I\'m glad you found the lessons helpful. Keep up the great work!',
      responseDate: '1 day ago'
    },
    {
      id: 2,
      studentName: 'Sarah Wilson',
      className: 'Class 8',
      subject: 'Science',
      rating: 4.0,
      comment: 'Good practical examples and engaging experiments in class. The teacher explains concepts well but sometimes goes too fast.',
      date: '3 days ago',
      teacherName: 'Mr. Brown',
      response: null
    },
    {
      id: 3,
      studentName: 'Mike Johnson',
      className: 'Class 11',
      subject: 'Physics',
      rating: 5.0,
      comment: 'Outstanding teacher! Makes complex topics easy to understand. The real-world applications discussed in class were very insightful.',
      date: '5 days ago',
      teacherName: 'Dr. Taylor',
      response: 'Thank you, Mike! I appreciate your kind words and dedication to learning. Your progress has been remarkable.',
      responseDate: '4 days ago'
    },
    {
      id: 4,
      studentName: 'Emma Davis',
      className: 'Class 9',
      subject: 'English Literature',
      rating: 4.8,
      comment: 'The way literature is connected to modern contexts makes the subject much more interesting and relevant.',
      date: '1 week ago',
      teacherName: 'Ms. Wilson',
      response: 'I\'m thrilled to hear that, Emma! Making literature relevant is exactly what we aim for. Great observation!',
      responseDate: '6 days ago'
    }
  ]
}

const viewClassReviews = (grade) => {
  console.log('Viewing detailed reviews for class:', grade)
  alert(`View detailed reviews for Class ${grade} - This would navigate to a detailed review page`)
}

const getInitials = (name) => {
  return name ? name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2) : '??'
}

const generateStars = (rating) => {
  const fullStars = Math.floor(rating)
  const halfStar = rating % 1 >= 0.5
  const emptyStars = 5 - fullStars - (halfStar ? 1 : 0)
  
  return '★'.repeat(fullStars) + (halfStar ? '½' : '') + '☆'.repeat(emptyStars)
}

const getRatingColor = (rating) => {
  if (rating >= 4.5) return 'bg-green-500'
  if (rating >= 4.0) return 'bg-blue-500'
  if (rating >= 3.0) return 'bg-yellow-500'
  return 'bg-red-500'
}

onMounted(() => {
  // checkAuthentication()
  fetchReviewData()
})
</script>

<style scoped>
/* Use deep selector to override */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

/* Line clamp utility for comment text */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>