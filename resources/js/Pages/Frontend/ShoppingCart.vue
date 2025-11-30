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

      <!-- Payment Method Modal -->
      <div v-if="showPaymentModal" class="payment-modal-overlay" @click.self="closePaymentModal">
        <div class="payment-modal">
          <div class="payment-modal-header">
            <h3 class="payment-modal-title">{{ t('Select Payment Method') }}</h3>
            <button class="payment-modal-close" @click="closePaymentModal">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="payment-modal-body">
            <!-- Payment Method Tabs -->
            <div class="payment-tabs">
              <button 
                class="payment-tab" 
                :class="{ active: activePaymentTab === 'mobile' }"
                @click="activePaymentTab = 'mobile'"
              >
                <i class="fas fa-mobile-alt"></i>
                {{ t('Mobile Banking') }}
              </button>
              <button 
                class="payment-tab" 
                :class="{ active: activePaymentTab === 'bank' }"
                @click="activePaymentTab = 'bank'"
              >
                <i class="fas fa-university"></i>
                {{ t('Bank Transfer') }}
              </button>
            </div>

            <!-- Mobile Banking Tab -->
            <div v-if="activePaymentTab === 'mobile'" class="payment-tab-content">
              <div class="payment-methods-grid">
                <div 
                  v-for="method in mobilePaymentMethods" 
                  :key="method.id"
                  class="payment-method-card"
                  :class="{ selected: selectedPaymentMethod === method.id }"
                  @click="selectPaymentMethod(method.id)"
                >
                  <div class="payment-method-icon">
                    <img :src="method.logo" :alt="method.name" @error="handlePaymentLogoError">
                  </div>
                  <div class="payment-method-info">
                    <h5 class="payment-method-name">{{ method.name }}</h5>
                    <p class="payment-method-description">{{ method.description }}</p>
                  </div>
                  <div class="payment-method-check">
                    <i class="fas fa-check" v-if="selectedPaymentMethod === method.id"></i>
                  </div>
                </div>
              </div>

              <!-- Payment Details Form -->
              <div v-if="selectedPaymentMethod" class="payment-details-form">
                <h5 class="form-title">{{ t('Payment Details') }}</h5>
                
                <!-- Mobile Number Display -->
                <div class="payment-info-card mb-4">
                  <div class="payment-info-header">
                    <i class="fas fa-mobile-alt text-primary"></i>
                    <h6 class="payment-info-title">{{ t('Send Payment To') }}</h6>
                  </div>
                  <div class="payment-info-content">
                    <div class="mobile-number-display">
                      <span class="mobile-number-label">{{ t('Mobile Number') }}:</span>
                      <span class="mobile-number-value">+880 1842 376477</span>
                      <button 
                        class="copy-btn" 
                        @click="copyMobileNumber"
                        :title="t('Copy mobile number')"
                      >
                        <i class="fas fa-copy"></i>
                      </button>
                    </div>
                    <p class="payment-note text-muted small mt-2">
                      {{ t('Send the exact amount to this number using your') }} {{ getPaymentMethodName(selectedPaymentMethod) }} {{ t('app') }}
                    </p>
                  </div>
                </div>

                <!-- Payment Steps -->
                <div class="payment-steps-card mb-4">
                  <div class="payment-steps-header">
                    <i class="fas fa-list-ol text-primary"></i>
                    <h6 class="payment-steps-title">{{ t('Payment Steps') }}</h6>
                  </div>
                  <div class="payment-steps-content">
                    <ol class="steps-list">
                      <li class="step-item">
                        <span class="step-number">1</span>
                        <span class="step-text">{{ t('Open your') }} {{ getPaymentMethodName(selectedPaymentMethod) }} {{ t('app') }}</span>
                      </li>
                      <li class="step-item">
                        <span class="step-number">2</span>
                        <span class="step-text">{{ t('Go to "Send Money" or "Payment" section') }}</span>
                      </li>
                      <li class="step-item">
                        <span class="step-number">3</span>
                        <span class="step-text">{{ t('Enter mobile number') }}: <strong>+880 1842 376477</strong></span>
                      </li>
                      <li class="step-item">
                        <span class="step-number">4</span>
                        <span class="step-text">{{ t('Enter amount') }}: <strong>৳ {{ calculateTotal }}</strong></span>
                      </li>
                      <li class="step-item">
                        <span class="step-number">5</span>
                        <span class="step-text">{{ t('Enter your PIN to complete payment') }}</span>
                      </li>
                      <li class="step-item">
                        <span class="step-number">6</span>
                        <span class="step-text">{{ t('Copy the Transaction ID from confirmation message') }}</span>
                      </li>
                    </ol>
                  </div>
                </div>

                <!-- Transaction ID Input -->
                <div class="form-group">
                  <label class="form-label">{{ t('Your Mobile Number') }}</label>
                  <input 
                    type="tel" 
                    class="form-control" 
                    :placeholder="t('01712345678 or +8801712345678')"
                    v-model="paymentDetails.phoneNumber"
                    maxlength="14"
                  >
                  <small class="form-text text-muted">
                    {{ t('Enter the mobile number you used for payment') }}
                  </small>
                  <small v-if="paymentDetails.phoneNumber && !validatePhoneNumber()" class="text-warning">
                    {{ t('Please enter a valid Bangladeshi mobile number') }}
                  </small>
                </div>

                <div class="form-group">
                  <label class="form-label">{{ t('Transaction ID') }} *</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    :placeholder="t('Enter transaction ID from payment confirmation')"
                    v-model="paymentDetails.transactionId"
                    required
                  >
                  <small class="form-text text-muted">
                    {{ t('Enter the transaction ID you received after successful payment') }}
                  </small>
                </div>

                <div class="form-group">
                  <label class="form-label">{{ t('Payment Amount') }}</label>
                  <div class="amount-display">
                    <span class="amount-currency">৳</span>
                    <span class="amount-value">{{ calculateTotal }}</span>
                  </div>
                  <small class="form-text text-muted">
                    {{ t('Make sure to send the exact amount') }}
                  </small>
                </div>

                <!-- Important Notes -->
                <div class="alert alert-info mt-4">
                  <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle mt-1 me-2"></i>
                    <div>
                      <h6 class="alert-title mb-2">{{ t('Important Notes') }}</h6>
                      <ul class="mb-0 ps-3">
                        <li>{{ t('Payment will be verified within 15-30 minutes') }}</li>
                        <li>{{ t('Make sure transaction ID is correct') }}</li>
                        <li>{{ t('Keep screenshot of payment confirmation') }}</li>
                        <li>{{ t('Contact support if payment is not verified within 1 hour') }}</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bank Transfer Tab -->
            <div v-if="activePaymentTab === 'bank'" class="payment-tab-content">
              <div class="bank-transfer-info">
                <h5 class="bank-transfer-title">{{ t('Bank Account Details') }}</h5>
                
                <div class="bank-account-card">
                  <div class="bank-info">
                    <div class="bank-detail">
                      <span class="detail-label">{{ t('Bank Name') }}:</span>
                      <span class="detail-value">Prime Bank Limited</span>
                    </div>
                    <div class="bank-detail">
                      <span class="detail-label">{{ t('Account Name') }}:</span>
                      <span class="detail-value">Pathshala Education Platform</span>
                    </div>
                    <div class="bank-detail">
                      <span class="detail-label">{{ t('Account Number') }}:</span>
                      <span class="detail-value">1234567890123</span>
                    </div>
                    <div class="bank-detail">
                      <span class="detail-label">{{ t('Branch') }}:</span>
                      <span class="detail-value">Gulshan Branch, Dhaka</span>
                    </div>
                    <div class="bank-detail">
                      <span class="detail-label">{{ t('Routing Number') }}:</span>
                      <span class="detail-value">123456789</span>
                    </div>
                  </div>
                </div>

                <div class="payment-instructions">
                  <h6 class="instructions-title">{{ t('Payment Instructions') }}</h6>
                  <ul class="instructions-list">
                    <li>{{ t('Transfer the exact amount') }}: ৳ {{ calculateTotal }}</li>
                    <li>{{ t('Use your enrollment ID as reference') }}: ENR{{ enrolledCourse.id?.toString().padStart(6, '0') }}</li>
                    <li>{{ t('Keep the transaction receipt for verification') }}</li>
                    <li>{{ t('Payment will be verified within 24 hours') }}</li>
                  </ul>
                </div>

                <div class="upload-receipt">
                  <label class="form-label">{{ t('Upload Payment Receipt') }}</label>
                  <div class="file-upload-area" @click="triggerFileInput">
                    <input 
                      type="file" 
                      ref="fileInput"
                      @change="handleFileUpload"
                      accept=".jpg,.jpeg,.png,.pdf"
                      style="display: none;"
                    >
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p class="upload-text" v-if="!paymentDetails.receiptFile">
                      {{ t('Click to upload payment receipt') }}
                    </p>
                    <p class="upload-success" v-else>
                      <i class="fas fa-check"></i>
                      {{ paymentDetails.receiptFile.name }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

         <div class="payment-modal-footer">
            <button class="btn btn-secondary" @click="closePaymentModal">
              {{ t('Cancel') }}
            </button>
            <button 
              class="btn btn-primary" 
              :disabled="!canProceedToPayment || processingPayment"
              @click="processPayment"
            >
              <span v-if="processingPayment" class="spinner-border spinner-border-sm me-2"></span>
              {{ t('Confirm Payment') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Payment Verification Modal -->
      <div v-if="showVerificationModal" class="verification-modal-overlay">
        <div class="verification-modal">
          <div class="verification-modal-header">
            <i class="fas fa-clock text-warning verification-icon"></i>
            <h3 class="verification-modal-title">{{ t('Payment Under Verification') }}</h3>
          </div>
          
          <div class="verification-modal-body">
            <div class="verification-content">
              <p class="verification-message">
                {{ t('Your payment is currently under verification by our admin team.') }}
              </p>
              <p class="verification-details">
                {{ t('This process usually takes 15-30 minutes for mobile payments and up to 24 hours for bank transfers.') }}
              </p>
              
              <div class="verification-info">
                <div class="info-item">
                  <i class="fas fa-receipt"></i>
                  <span>{{ t('Payment Reference') }}: <strong>#PAY{{ paymentReference }}</strong></span>
                </div>
                <div class="info-item">
                  <i class="fas fa-book"></i>
                  <span>{{ t('Course') }}: <strong>{{ getCourseTitle(enrolledCourse) }}</strong></span>
                </div>
                <div class="info-item">
                  <i class="fas fa-calendar"></i>
                  <span>{{ t('Submitted') }}: <strong>{{ currentTime }}</strong></span>
                </div>
              </div>

              <div class="verification-steps">
                <h6>{{ t('What happens next?') }}</h6>
                <ul>
                  <li>{{ t('Admin will verify your payment details') }}</li>
                  <li>{{ t('You will receive a confirmation notification') }}</li>
                  <li>{{ t('Course will be automatically added to your dashboard') }}</li>
                  <li>{{ t('You can start learning immediately after verification') }}</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="verification-modal-footer">
            <button class="btn btn-primary" @click="closeVerificationModal">
              {{ t('Got it! I will wait for verification') }}
            </button>
            <button class="btn btn-outline-secondary" @click="contactSupport">
              <i class="fas fa-headset me-1"></i>
              {{ t('Contact Support') }}
            </button>
          </div>
        </div>
      </div>
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
const showVerificationModal = ref(false)
const paymentReference = ref('')
const currentTime = ref('')
// Payment modal data
const showPaymentModal = ref(false)
const activePaymentTab = ref('mobile')
const selectedPaymentMethod = ref('')
const processingPayment = ref(false)

// Payment methods data
const mobilePaymentMethods = ref([
  {
    id: 'bkash',
    name: 'bKash',
    logo: '/assets/img/payment/bkash.png',
    description: 'Fast and secure payment via bKash'
  },
  {
    id: 'nagad',
    name: 'Nagad',
    logo: '/assets/img/payment/nagad.png',
    description: 'Instant payment with Nagad'
  },
  {
    id: 'rocket',
    name: 'Rocket',
    logo: '/assets/img/payment/rocket.png',
    description: 'Easy payment through Rocket'
  },
  {
    id: 'upay',
    name: 'uPay',
    logo: '/assets/img/payment/upay.png',
    description: 'Quick payment with uPay'
  }
])

// Payment details
const paymentDetails = ref({
  phoneNumber: '',
  transactionId: '',
  receiptFile: null
})

const fileInput = ref(null)

// Computed properties
const isCourseLoaded = computed(() => {
  return enrolledCourse.value && enrolledCourse.value.id;
})

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

const canProceedToPayment = computed(() => {
  if (activePaymentTab.value === 'mobile') {
    return selectedPaymentMethod.value && 
           paymentDetails.value.phoneNumber && 
           paymentDetails.value.transactionId &&
           validatePhoneNumber();
  } else {
    return paymentDetails.value.receiptFile !== null;
  }
})

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
  
  if (course.type === 'regular') {
    const className = course.name || `Class ${course.grade || ''}`;
    const subjectName = course.subject || 'General';
    return `${className} - ${subjectName}`;
  } else {
    return course.name || course.class_name || t('Untitled Course');
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

// Phone number validation
const validatePhoneNumber = () => {
  const phone = paymentDetails.value.phoneNumber;
  // Accept various formats: 01712345678, +8801712345678, 8801712345678
  const isValid = /^(01\d{9}|\+8801\d{8}|8801\d{8})$/.test(phone);
  return isValid;
}

// Payment methods
const openPaymentModal = () => {
  showPaymentModal.value = true;
  activePaymentTab.value = 'mobile';
  selectedPaymentMethod.value = '';
  paymentDetails.value = {
    phoneNumber: '',
    transactionId: '',
    receiptFile: null
  };
};

const closePaymentModal = () => {
  showPaymentModal.value = false;
};

const selectPaymentMethod = (methodId) => {
  selectedPaymentMethod.value = methodId;
};

// Helper method to get payment method name
const getPaymentMethodName = (methodId) => {
  const method = mobilePaymentMethods.value.find(m => m.id === methodId);
  return method ? method.name : 'Mobile Banking';
}

// Copy mobile number to clipboard
const copyMobileNumber = async () => {
  const mobileNumber = '+8801842376477';
  try {
    await navigator.clipboard.writeText(mobileNumber);
    // Show success message
    const originalText = event.target.innerHTML;
    event.target.innerHTML = '<i class="fas fa-check"></i>';
    event.target.style.color = 'var(--success-color)';
    
    setTimeout(() => {
      event.target.innerHTML = originalText;
      event.target.style.color = '';
    }, 2000);
    
    // Optional: Show toast notification
    alert(t('Mobile number copied to clipboard!'));
  } catch (err) {
    console.error('Failed to copy mobile number:', err);
    alert(t('Failed to copy mobile number. Please copy manually.'));
  }
}

const handlePaymentLogoError = (event) => {
  event.target.src = '/assets/img/payment/default.png';
  event.target.onerror = null;
};

const triggerFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click();
  }
};

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file type and size
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!validTypes.includes(file.type)) {
      alert(t('Please upload a valid file (JPG, PNG, PDF)'));
      return;
    }
    
    if (file.size > maxSize) {
      alert(t('File size must be less than 5MB'));
      return;
    }
    
    paymentDetails.value.receiptFile = file;
  }
};

const processPayment = async () => {
  processingPayment.value = true;
  
  try {
    // Prepare payment data
    const paymentData = {
      course_id: enrolledCourse.value.id,
      amount: calculateTotal.value.replace(/,/g, ''), // Remove commas for numeric value
      payment_method: activePaymentTab.value === 'mobile' ? selectedPaymentMethod.value : 'bank_transfer',
      payment_details: paymentDetails.value,
      additional_services: additionalServices.value,
      coupon_code: appliedCoupon.value ? couponCode.value : null
    };
    
    // Process payment based on method
    if (activePaymentTab.value === 'mobile') {
      await processMobilePayment(paymentData);
    } else {
      await processBankTransfer(paymentData);
    }
    
  } catch (error) {
    console.error('Payment processing error:', error);
    alert(t('Payment processing failed. Please try again.'));
  } finally {
    processingPayment.value = false;
  }
};

const processMobilePayment = async (paymentData) => {
  try {
    const response = await fetch('/api/payments/process-mobile', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(paymentData)
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Show success message and redirect
      alert(result.message || t('Payment successful! You are now enrolled in the course.'));
      closePaymentModal();
      
      // Redirect to learning page or my courses
      setTimeout(() => {
        router.visit(`/student/learning/${enrolledCourse.value.id}`);
      }, 1500);
    } else {
      throw new Error(result.message || 'Payment failed');
    }
    
  } catch (error) {
    console.error('Mobile payment error:', error);
    alert(error.message || t('Payment failed. Please try again.'));
    throw error;
  }
};

const processBankTransfer = async (paymentData) => {
  try {
    // Create FormData for file upload
    const formData = new FormData();
    formData.append('course_id', paymentData.course_id);
    formData.append('amount', paymentData.amount);
    formData.append('additional_services', JSON.stringify(paymentData.additional_services));
    formData.append('coupon_code', paymentData.coupon_code || '');
    
    if (paymentDetails.value.receiptFile) {
      formData.append('receipt', paymentDetails.value.receiptFile);
    }
    
    const response = await fetch('/api/payments/process-bank-transfer', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Generate payment reference
      paymentReference.value = Date.now().toString().slice(-6);
      currentTime.value = new Date().toLocaleString();
      
      // Close payment modal and show verification modal
      closePaymentModal();
      showVerificationModal.value = true;
      
      // Optional: Redirect to home after a delay
      setTimeout(() => {
        router.visit('/');
      }, 5000);
    } else {
      throw new Error(result.message || 'Payment processing failed');
    }
    
  } catch (error) {
    console.error('Bank transfer payment error:', error);
    alert(error.message || t('Payment processing failed. Please try again.'));
    throw error;
  }
};

const closeVerificationModal = () => {
  showVerificationModal.value = false;
  // Redirect to home page
  router.visit('/');
}

const contactSupport = () => {
  window.location.href = 'tel:+8801234567890';
}

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

const proceedToCheckout = () => {
  // Instead of direct payment, open payment modal
  openPaymentModal();
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
    console.log('Enrolled course data:', enrolledCourse.value);
  }
  
  // If coming from enrollment, you might have additional data
  if (props.enrollmentData) {
    console.log('Enrollment data:', props.enrollmentData);
  }
  
  // Also check URL parameters for course_id
  const urlParams = new URLSearchParams(window.location.search);
  const courseId = urlParams.get('course_id');
  
  if (courseId && !props.course) {
    // Fetch course data if only ID is provided
    fetchCourseData(courseId);
  }
})

// Fetch course data if only ID is provided
const fetchCourseData = async (courseId) => {
  try {
    console.log('Fetching course data for ID:', courseId);
    
    const response = await fetch(`/api/public/courses/${courseId}`);
    
    if (response.ok) {
      const data = await response.json();
      
      if (data.success && data.data) {
        enrolledCourse.value = {
          ...enrolledCourse.value,
          ...data.data
        };
        console.log('Course data fetched successfully:', enrolledCourse.value);
      }
    }
  } catch (error) {
    console.error('Error fetching course data:', error);
  }
}
</script>

<style scoped>
/* Payment Modal Styles */
.payment-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}

.payment-modal {
  background: var(--card-bg);
  border-radius: 12px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  border: 1px solid var(--border-color);
}

.payment-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.payment-modal-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.payment-modal-close {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: var(--text-muted);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.payment-modal-close:hover {
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.payment-modal-body {
  padding: 1.5rem;
}

.payment-modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid var(--border-color);
}

/* Payment Tabs */
.payment-tabs {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.payment-tab {
  flex: 1;
  padding: 1rem;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  color: var(--text-muted);
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  justify-content: center;
}

.payment-tab:hover {
  color: var(--primary-color);
}

.payment-tab.active {
  color: var(--primary-color);
  border-bottom-color: var(--primary-color);
}

/* Payment Methods Grid */
.payment-methods-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.payment-method-card {
  border: 2px solid var(--border-color);
  border-radius: 8px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.payment-method-card:hover {
  border-color: var(--primary-color);
  transform: translateY(-2px);
}

.payment-method-card.selected {
  border-color: var(--primary-color);
  background: color-mix(in srgb, var(--primary-color) 5%, transparent);
}

.payment-method-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.payment-method-icon img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.payment-method-info {
  flex: 1;
}

.payment-method-name {
  margin: 0 0 0.25rem 0;
  font-weight: 600;
  color: var(--text-primary);
}

.payment-method-description {
  margin: 0;
  font-size: 0.875rem;
  color: var(--text-muted);
}

.payment-method-check {
  color: var(--primary-color);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.payment-method-card.selected .payment-method-check {
  opacity: 1;
}

/* Payment Details Form */
.payment-details-form {
  background: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 8px;
  margin-top: 1.5rem;
}

.form-title {
  margin: 0 0 1rem 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text-primary);
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--card-bg);
  color: var(--text-primary);
  transition: border-color 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary-color);
}

.form-text {
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.amount-display {
  background: var(--bg-secondary);
  padding: 1rem;
  border-radius: 6px;
  text-align: center;
}

.amount-currency {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-secondary);
}

.amount-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-left: 0.5rem;
}

/* Payment Info Card */
.payment-info-card {
  background: var(--bg-secondary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 1rem;
}

.payment-info-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.payment-info-title {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-primary);
}

.mobile-number-display {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--card-bg);
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid var(--border-color);
}

.mobile-number-label {
  font-weight: 500;
  color: var(--text-muted);
}

.mobile-number-value {
  font-weight: 600;
  color: var(--primary-color);
  font-family: monospace;
}

.copy-btn {
  background: none;
  border: none;
  color: var(--primary-color);
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

.copy-btn:hover {
  background: var(--bg-tertiary);
}

.payment-note {
  margin: 0;
  line-height: 1.4;
}

/* Payment Steps Card */
.payment-steps-card {
  background: var(--bg-secondary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 1rem;
}

.payment-steps-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.payment-steps-title {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-primary);
}

.steps-list {
  margin: 0;
  padding-left: 0;
  list-style: none;
}

.step-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--border-color);
}

.step-item:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.step-number {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  background: var(--primary-color);
  color: white;
  border-radius: 50%;
  font-size: 0.75rem;
  font-weight: 600;
  flex-shrink: 0;
}

.step-text {
  flex: 1;
  font-size: 0.875rem;
  line-height: 1.4;
  color: var(--text-primary);
}

/* Alert Styles */
.alert {
  border: 1px solid transparent;
  border-radius: 6px;
  padding: 0.75rem 1rem;
}

.alert-info {
  background: color-mix(in srgb, var(--info-color) 10%, transparent);
  border-color: color-mix(in srgb, var(--info-color) 30%, transparent);
  color: var(--text-primary);
}

.alert-title {
  font-size: 0.9rem;
  font-weight: 600;
  margin: 0 0 0.5rem 0;
}

/* Bank Transfer Styles */
.bank-transfer-info {
  space-y: 1.5rem;
}

.bank-transfer-title {
  margin: 0 0 1rem 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
}

.bank-account-card {
  background: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 8px;
  border-left: 4px solid var(--primary-color);
}

.bank-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border-color);
}

.bank-detail:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: 500;
  color: var(--text-primary);
}

.detail-value {
  font-weight: 600;
  color: var(--primary-color);
}

.payment-instructions {
  background: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 8px;
}

.instructions-title {
  margin: 0 0 1rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
}

.instructions-list {
  margin: 0;
  padding-left: 1.5rem;
  color: var(--text-primary);
}

.instructions-list li {
  margin-bottom: 0.5rem;
}

.upload-receipt {
  margin-top: 1.5rem;
}

.file-upload-area {
  border: 2px dashed var(--border-color);
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.file-upload-area:hover {
  border-color: var(--primary-color);
  background: color-mix(in srgb, var(--primary-color) 5%, transparent);
}

.upload-text, .upload-success {
  margin: 1rem 0 0 0;
  color: var(--text-muted);
}

.upload-success {
  color: var(--success-color);
}

/* Verification Modal Styles */
.verification-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  padding: 20px;
}

.verification-modal {
  background: var(--card-bg);
  border-radius: 16px;
  max-width: 500px;
  width: 100%;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  border: 1px solid var(--border-color);
  animation: modalSlideIn 0.3s ease-out;
}

.verification-modal-header {
  padding: 25px;
  border-bottom: 1px solid var(--border-color);
  text-align: center;
  background: var(--bg-secondary);
  border-radius: 16px 16px 0 0;
}

.verification-icon {
  font-size: 3rem;
  margin-bottom: 15px;
}

.verification-modal-title {
  margin: 0;
  color: var(--text-primary);
  font-size: 1.5rem;
  font-weight: 600;
}

.verification-modal-body {
  padding: 25px;
}

.verification-content {
  text-align: center;
}

.verification-message {
  font-size: 1.1rem;
  color: var(--text-primary);
  margin-bottom: 15px;
  font-weight: 500;
}

.verification-details {
  color: var(--text-secondary);
  margin-bottom: 25px;
  line-height: 1.5;
}

.verification-info {
  background: var(--bg-secondary);
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 25px;
  text-align: left;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
  padding: 8px 0;
  border-bottom: 1px solid var(--border-light);
}

.info-item:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.info-item i {
  color: var(--primary-color);
  width: 20px;
  text-align: center;
}

.verification-steps {
  text-align: left;
  background: var(--bg-secondary);
  padding: 20px;
  border-radius: 8px;
}

.verification-steps h6 {
  margin: 0 0 15px 0;
  color: var(--text-primary);
  font-weight: 600;
}

.verification-steps ul {
  margin: 0;
  padding-left: 20px;
  color: var(--text-secondary);
}

.verification-steps li {
  margin-bottom: 8px;
  line-height: 1.4;
}

.verification-steps li:last-child {
  margin-bottom: 0;
}

.verification-modal-footer {
  padding: 20px;
  border-top: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Responsive design */
@media (max-width: 768px) {
  .payment-modal {
    margin: 1rem;
    max-height: 95vh;
  }
  
  .payment-tabs {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .payment-methods-grid {
    grid-template-columns: 1fr;
  }
  
  .bank-detail {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
  
  .mobile-number-display {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .step-item {
    align-items: flex-start;
  }
  
  .step-text {
    font-size: 0.8rem;
  }
  
  .verification-modal {
    margin: 1rem;
    max-height: 90vh;
  }
  
  .verification-modal-footer {
    flex-direction: column;
  }
}

/* Animation */
@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Keep all your existing CSS styles from previous implementation */
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
</style>