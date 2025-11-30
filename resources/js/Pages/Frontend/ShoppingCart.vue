<template>
  <FrontendLayout>
    <main class="main-area fix">
      <!-- breadcrumb-area -->
      <section class="breadcrumb__area breadcrumb__bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
          <div class="container">
              <div class="row">
                  <div class="col-12">
                      <div class="breadcrumb__content">
                          <h3 class="title">{{ t('Shopping Cart') }}</h3>
                          <nav class="breadcrumb">
                              <span property="itemListElement" typeof="ListItem">
                                  <Link href="/">{{ t('Home') }}</Link>
                              </span>
                              <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                              <span property="itemListElement" typeof="ListItem">
                                  <Link href="/courses">{{ t('Courses') }}</Link>
                              </span>
                              <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                              <span v-if="isCourseLoaded && enrolledCourse.name" property="itemListElement" typeof="ListItem">
                                  <Link :href="`/course/${enrolledCourse.id}`">{{ enrolledCourse.name }}</Link>
                              </span>
                              <span v-else-if="isCourseLoaded" property="itemListElement" typeof="ListItem">
                                  <Link :href="`/course/${enrolledCourse.id}`">{{ getCourseTitle(enrolledCourse) }}</Link>
                              </span>
                              <span v-else property="itemListElement" typeof="ListItem">
                                  {{ t('Course Details') }}
                              </span>
                              <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                              <span property="itemListElement" typeof="ListItem">{{ t('Shopping Cart') }}</span>
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

      <!-- Shopping Cart Section -->
      <section class="shopping-cart-area section-py-120">
        <div class="container">

          <div class="row g-5">
            <!-- Cart Items -->
            <div class="col-xl-8 col-lg-7">
              <div class="cart-items-section">
                <div class="card border-0 shadow-sm">
                  <div class="card-header bg-transparent border-0 py-4">
                    <h4 class="card-title mb-0">{{ t('Your Cart Items') }}</h4>
                  </div>
                  <div class="card-body p-0">
                    <!-- Cart Items List -->
                    <div class="cart-items">
                      <!-- Single Cart Item -->
                      <div class="cart-item border-bottom p-4">
                        <div class="row align-items-center">
                          <div class="col-md-2">
                            <div class="course-thumbnail">
                              <img 
                                :src="getCourseImage(enrolledCourse)" 
                                :alt="enrolledCourse.name" 
                                class="rounded-3 w-100"
                                @error="handleImageError"
                                style="height: 80px; object-fit: cover;"
                              >
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="course-info">
                              <h5 class="course-title mb-2">{{ getCourseTitle(enrolledCourse) }}</h5>
                              <p class="course-description text-muted mb-2">
                                {{ getCourseDescription(enrolledCourse) }}
                              </p>
                              <div class="course-meta">
                                <span class="instructor me-3">
                                  <i class="fas fa-user-tie me-1"></i>
                                  {{ getInstructorName(enrolledCourse.teacher) }}
                                </span>
                                <span class="duration">
                                  <i class="fas fa-clock me-1"></i>
                                  {{ enrolledCourse.duration || '12 weeks' }}
                                </span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-2 text-center">
                            <div class="price-section">
                              <div class="price-amount">
                                <span class="price-currency">৳</span>
                                <span class="price-number">{{ enrolledCourse.price || '3999' }}</span>
                              </div>
                              <div class="price-original text-muted text-decoration-line-through small">
                                ৳ 4999
                              </div>
                            </div>
                          </div>
                          <div class="col-md-2 text-center">
                            <button 
                              @click="removeFromCart" 
                              class="btn btn-outline-danger btn-sm"
                              :title="t('Remove from cart')"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                      <!-- End Single Cart Item -->
                    </div>
                  </div>
                </div>

                <!-- Additional Services -->
                <div class="card border-0 shadow-sm mt-4">
                  <div class="card-body">
                    <h5 class="card-title mb-3">{{ t('Additional Services') }}</h5>
                    <div class="additional-services">
                      <div class="form-check mb-3">
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          id="certificateService" 
                          v-model="additionalServices.certificate"
                        >
                        <label class="form-check-label" for="certificateService">
                          <strong>{{ t('Add Certificate') }}</strong>
                          <span class="text-muted d-block">+ ৳ 500</span>
                          <small class="text-muted">{{ t('Get a verified certificate upon completion') }}</small>
                        </label>
                      </div>
                      <div class="form-check">
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          id="consultingService" 
                          v-model="additionalServices.consulting"
                        >
                        <label class="form-check-label" for="consultingService">
                          <strong>{{ t('1-on-1 Consulting') }}</strong>
                          <span class="text-muted d-block">+ ৳ 2000</span>
                          <small class="text-muted">{{ t('Personalized guidance from the instructor') }}</small>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="col-xl-4 col-lg-5">
              <div class="order-summary-section">
                <div class="sticky-sidebar">
                  <!-- Order Summary Card -->
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-4">
                      <h4 class="card-title mb-0">{{ t('Order Summary') }}</h4>
                    </div>
                    <div class="card-body">
                      <!-- Price Breakdown -->
                      <div class="price-breakdown mb-4">
                        <div class="price-row d-flex justify-content-between mb-2">
                          <span class="text-muted">{{ t('Course Fee') }}:</span>
                          <span class="fw-semibold">৳ {{ enrolledCourse.price || '3999' }}</span>
                        </div>
                        <div v-if="additionalServices.certificate" class="price-row d-flex justify-content-between mb-2">
                          <span class="text-muted">{{ t('Certificate') }}:</span>
                          <span class="fw-semibold">+ ৳ 500</span>
                        </div>
                        <div v-if="additionalServices.consulting" class="price-row d-flex justify-content-between mb-2">
                          <span class="text-muted">{{ t('1-on-1 Consulting') }}:</span>
                          <span class="fw-semibold">+ ৳ 2000</span>
                        </div>
                        <div class="price-row d-flex justify-content-between border-top pt-2 mt-2">
                          <span class="fw-bold">{{ t('Total') }}:</span>
                          <span class="fw-bold text-primary fs-5">৳ {{ calculateTotal }}</span>
                        </div>
                      </div>

                      <!-- Coupon Code -->
                      <div class="coupon-section mb-4">
                        <label class="form-label small text-muted mb-2">{{ t('Apply Coupon Code') }}</label>
                        <div class="input-group">
                          <input 
                            type="text" 
                            class="form-control" 
                            :placeholder="t('Enter coupon code')"
                            v-model="couponCode"
                          >
                          <button 
                            class="btn btn-outline-primary" 
                            type="button"
                            @click="applyCoupon"
                            :disabled="!couponCode"
                          >
                            {{ t('Apply') }}
                          </button>
                        </div>
                        <div v-if="appliedCoupon" class="mt-2">
                          <span class="text-success small">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ t('Coupon applied successfully!') }}
                          </span>
                        </div>
                      </div>

                      <!-- Gift Option -->
                      <div class="gift-option mb-4">
                        <div class="form-check">
                          <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="sendAsGift" 
                            v-model="sendAsGift"
                          >
                          <label class="form-check-label" for="sendAsGift">
                            <strong>{{ t('Send as a gift') }}</strong>
                          </label>
                        </div>
                        <div v-if="sendAsGift" class="gift-details mt-2">
                          <input 
                            type="email" 
                            class="form-control form-control-sm" 
                            :placeholder="t('Recipient email address')"
                            v-model="giftRecipientEmail"
                          >
                          <textarea 
                            class="form-control form-control-sm mt-2" 
                            rows="2" 
                            :placeholder="t('Gift message (optional)')"
                            v-model="giftMessage"
                          ></textarea>
                        </div>
                      </div>

                      <!-- Checkout Button -->
                      <button 
                        class="btn btn-primary w-100 btn-lg mb-3" 
                        @click="proceedToCheckout"
                        :disabled="processingCheckout"
                      >
                        <span v-if="processingCheckout" class="spinner-border spinner-border-sm me-2"></span>
                        {{ t('Continue to Payment') }}
                      </button>

                      <!-- Security Badges -->
                      <div class="security-badges text-center">
                        <div class="d-flex justify-content-center gap-3 mb-2">
                          <i class="fas fa-lock text-success"></i>
                          <i class="fas fa-shield-alt text-primary"></i>
                          <i class="fas fa-credit-card text-info"></i>
                        </div>
                        <small class="text-muted">
                          {{ t('Secure SSL encrypted payment') }}
                        </small>
                      </div>
                    </div>
                  </div>

                  <!-- Support Card -->
                  <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body text-center">
                      <i class="fas fa-headset text-primary fa-2x mb-3"></i>
                      <h6 class="card-title">{{ t('Need Help?') }}</h6>
                      <p class="text-muted small mb-3">
                        {{ t('Our support team is here to help you') }}
                      </p>
                      <a href="tel:+8801234567890" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-phone me-1"></i>
                        +880 1234 567890
                      </a>
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
import { Link, router } from '@inertiajs/vue3'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { ref, computed, onMounted } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

// Use translation composable
const { t } = useTranslation()

// Props
const props = defineProps({
  course: {
    type: Object,
    default: null
  },
  enrollmentData: {
    type: Object,
    default: () => ({})
  }
})

// Reactive data
const enrolledCourse = ref({
  id: null,
  name: '',
  description: '',
  thumbnail: '/assets/img/courses/h5_course_thumb01.jpg',
  price: '3999',
  teacher: null,
  duration: '12 weeks',
  ...props.course
})

const additionalServices = ref({
  certificate: false,
  consulting: false
})

const couponCode = ref('')
const appliedCoupon = ref(false)
const sendAsGift = ref(false)
const giftRecipientEmail = ref('')
const giftMessage = ref('')
const processingCheckout = ref(false)

// Helper methods for course data
const getCourseImage = (course) => {
  if (!course) return '/assets/img/courses/h5_course_thumb01.jpg';
  
  // Check for various image properties
  if (course.image && course.image !== 'null' && course.image !== 'NULL') {
    return formatImageUrl(course.image);
  }
  
  if (course.thumbnail && course.thumbnail !== 'null' && course.thumbnail !== 'NULL') {
    return formatImageUrl(course.thumbnail);
  }
  
  if (course.thumbnail_url) {
    return formatImageUrl(course.thumbnail_url);
  }
  
  if (course.image_url) {
    return formatImageUrl(course.image_url);
  }
  
  // Fallback to default image
  return '/assets/img/courses/h5_course_thumb01.jpg';
}
const isCourseLoaded = computed(() => {
  return enrolledCourse.value && enrolledCourse.value.id;
});

const formatImageUrl = (imagePath) => {
  if (!imagePath) return '/assets/img/courses/h5_course_thumb01.jpg';
  
  // If it's already a full URL, return as is
  if (imagePath.startsWith('http')) {
    return imagePath;
  }
  
  // If it starts with storage/, make it accessible via public storage
  if (imagePath.startsWith('storage/')) {
    const publicPath = imagePath.replace('storage/', '');
    return `/storage/${publicPath}`;
  }
  
  // If it's a relative path, assume it's in storage
  if (imagePath.startsWith('courses/') || imagePath.startsWith('profile-pictures/')) {
    return `/storage/${imagePath}`;
  }
  
  // Default case - return as is (might be relative path)
  return imagePath.startsWith('/') ? imagePath : `/${imagePath}`;
}

const getCourseTitle = (course) => {
  if (!course) return t('Course Details');
  
  console.log('Course data for title generation:', course);
  
  // If the course already has a formatted name from CourseSingle page
  if (course.name && (course.name.includes(' - ') || course.name.includes('Class'))) {
    return course.name;
  }
  
  // Build the title from individual components
  if (course.type === 'regular') {
    const className = course.class_name || course.name || `Class ${course.grade || ''}`;
    const subjectName = course.subject || 'General';
    return `${className} - ${subjectName}`;
  } else {
    return course.name || course.class_name || t('Skill Course');
  }
};

const getCourseDescription = (course) => {
  if (course.description) {
    return course.description;
  }
  
  if (course.type === 'regular') {
    return t('Comprehensive curriculum for students. This course covers all essential subjects and prepares students for academic success.');
  } else {
    return t('Explore this course - learn essential skills and knowledge from expert instructors.');
  }
}

const getInstructorName = (teacher) => {
  if (!teacher) return t('Expert Instructor');
  
  if (typeof teacher === 'string') {
    return teacher;
  }
  
  return teacher.name || teacher.username || t('Expert Instructor');
}

const handleImageError = (event) => {
  console.warn('Failed to load course image:', event.target.src);
  event.target.src = '/assets/img/courses/h5_course_thumb01.jpg';
  event.target.onerror = null; // Prevent infinite loop
}

// Computed properties
const calculateTotal = computed(() => {
  let total = parseInt(enrolledCourse.value.price) || 3999
  
  if (additionalServices.value.certificate) {
    total += 500
  }
  
  if (additionalServices.value.consulting) {
    total += 2000
  }
  
  // Apply coupon discount (20% off for demo)
  if (appliedCoupon.value) {
    total = total * 0.8
  }
  
  return total.toLocaleString()
})

// Methods
const removeFromCart = () => {
  if (confirm(t('Are you sure you want to remove this course from your cart?'))) {
    router.visit('/courses')
  }
}

const applyCoupon = () => {
  if (couponCode.value.trim()) {
    // Simulate coupon validation
    appliedCoupon.value = true
    // In real app, you would validate the coupon via API
  }
}

const proceedToCheckout = async () => {
  processingCheckout.value = true
  
  try {
    // Prepare checkout data
    const checkoutData = {
      course_id: enrolledCourse.value.id,
      amount: calculateTotal.value,
      additional_services: additionalServices.value,
      coupon_code: appliedCoupon.value ? couponCode.value : null,
      send_as_gift: sendAsGift.value,
      gift_recipient_email: sendAsGift.value ? giftRecipientEmail.value : null,
      gift_message: sendAsGift.value ? giftMessage.value : null
    }
    
    // Simulate API call to process payment
    // In real app, you would integrate with payment gateway
    setTimeout(() => {
      router.post('/checkout/process', checkoutData)
    }, 1000)
    
  } catch (error) {
    console.error('Checkout error:', error)
    alert(t('Failed to process checkout. Please try again.'))
  } finally {
    processingCheckout.value = false
  }
}

// Lifecycle
onMounted(() => {
  console.log('Shopping cart mounted with props:', {
    course: props.course,
    enrollmentData: props.enrollmentData
  });
  
  // If course data is passed via props, use it
  if (props.course) {
    enrolledCourse.value = {
      ...enrolledCourse.value,
      ...props.course
    }
    console.log('Enrolled course data after props:', enrolledCourse.value);
    console.log('Generated course title:', getCourseTitle(enrolledCourse.value));
  }
  
  // Also check URL parameters for course_id
  const urlParams = new URLSearchParams(window.location.search);
  const courseId = urlParams.get('course_id');
  
  console.log('URL course_id parameter:', courseId);
  
  if (courseId && (!props.course || !props.course.id)) {
    // Fetch course data if only ID is provided
    fetchCourseData(courseId);
  } else if (!props.course && !courseId) {
    // If no course data at all, try to get from session or redirect
    console.warn('No course data available for shopping cart');
  }
})

// Fetch course data if only ID is provided
const fetchCourseData = async (courseId) => {
  try {
    console.log('Fetching complete course data for ID:', courseId);
    
    const response = await fetch(`/api/public/courses/${courseId}`);
    
    if (response.ok) {
      const data = await response.json();
      
      if (data.success && data.data) {
        enrolledCourse.value = {
          ...enrolledCourse.value,
          ...data.data
        };
        console.log('Course data fetched successfully:', enrolledCourse.value);
        console.log('Generated course title from fetched data:', getCourseTitle(enrolledCourse.value));
      }
    } else {
      console.error('Failed to fetch course data, status:', response.status);
    }
  } catch (error) {
    console.error('Error fetching course data:', error);
  }
}
</script>

<style scoped>
/* ... (keep all your existing CSS styles) ... */

.course-thumbnail img {
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}

.course-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.3;
}

.course-description {
  font-size: 0.9rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.course-meta {
  font-size: 0.85rem;
  color: var(--text-muted);
}

.price-amount {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-color);
}

.price-currency {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-secondary);
}

.price-number {
  font-size: 1.25rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .course-thumbnail {
    text-align: center;
    margin-bottom: 1rem;
  }
  
  .course-thumbnail img {
    height: 60px;
    width: 60px;
  }
  
  .course-info {
    text-align: center;
  }
}
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
.breadcrumb__area {
  position: relative;
  padding: 10px 0 10px;
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
  font-size: 24px;
  font-weight: 400;
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
.shopping-cart-area {
  background: var(--bg-primary);
}

.shopping-cart-header {
  text-align: center;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.page-subtitle {
  font-size: 1.1rem;
  color: var(--text-secondary);
  margin-bottom: 0;
}

.cart-item {
  transition: background-color 0.3s ease;
}

.cart-item:hover {
  background-color: var(--bg-secondary);
}

.course-thumbnail {
  max-width: 80px;
}

.course-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.3;
}

.course-description {
  font-size: 0.9rem;
  line-height: 1.4;
}

.course-meta {
  font-size: 0.85rem;
  color: var(--text-muted);
}

.price-amount {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-color);
}

.price-currency {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-secondary);
}

.price-number {
  font-size: 1.25rem;
}

.additional-services .form-check-label {
  cursor: pointer;
}

.security-badges i {
  font-size: 1.25rem;
}

.sticky-sidebar {
  position: sticky;
  top: 100px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }
  
  .cart-item .row > div {
    margin-bottom: 1rem;
  }
  
  .cart-item .row > div:last-child {
    margin-bottom: 0;
  }
}

/* Bengali Language Support */
.bn-lang .page-title,
.bn-lang .page-subtitle,
.bn-lang .course-title,
.bn-lang .course-description,
.bn-lang .card-title,
.bn-lang .form-label,
.bn-lang .btn {
  font-family: "Noto Sans Bengali", "SolaimanLipi", "Siyam Rupali", sans-serif !important;
}

.bn-lang .page-title {
  font-size: 2rem !important;
  line-height: 1.3 !important;
}

.bn-lang .page-subtitle {
  font-size: 1rem !important;
}

.bn-lang .course-title {
  font-size: 1rem !important;
}

.bn-lang .course-description {
  font-size: 0.85rem !important;
}

/* Dark theme support */
.dark-theme .cart-item:hover {
  background-color: var(--bg-tertiary);
}

.dark-theme .card {
  background: var(--card-bg);
  border-color: var(--border-color);
}
</style>