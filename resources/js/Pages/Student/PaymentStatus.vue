<template>
  <FrontendLayout>
    <main class="main-area fix">
      <!-- breadcrumb-area -->
      <!-- <section class="breadcrumb__area breadcrumb__bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="breadcrumb__content">
                <h3 class="title">{{ t('Payment Status') }}</h3>
                <nav class="breadcrumb">
                  <span property="itemListElement" typeof="ListItem">
                    <Link href="/">{{ t('Home') }}</Link>
                  </span>
                  <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                  <span property="itemListElement" typeof="ListItem">{{ t('Payment Status') }}</span>
                </nav>
              </div>
            </div>
          </div>
        </div>
        <div class="breadcrumb__shape-wrap">
          <img src="/assets/img/banner/breadcrumb_shape01.svg" alt="img" class="alltuchtopdown">
          <img src="/assets/img/banner/breadcrumb_shape02.svg" alt="img" data-aos="fade-right" data-aos-delay="300" class="aos-init aos-animate">
          <img src="/assets/img/banner/breadcrumb_shape03.png" alt="img" data-aos="fade-up" data-aos-delay="400" class="aos-init aos-animate">
          <img src="/assets/img/banner/breadcrumb_shape04.png" alt="img" data-aos="fade-down-left" data-aos-delay="400" class="aos-init aos-animate">
          <img src="/assets/img/banner/breadcrumb_shape05.svg" alt="img" data-aos="fade-left" data-aos-delay="400" class="aos-init aos-animate">
        </div>
      </section> -->
      <!-- breadcrumb-area-end -->

      <!-- Payment Status Section -->
      <section class="payment-status-area section-py-120">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-10">
              <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-4">
                  <h4 class="card-title mb-0">{{ t('Your Payment History') }}</h4>
                  <p class="text-muted mb-0">{{ t('Track all your course payments and their status') }}</p>
                </div>
                <div class="card-body">
                  
                  <!-- Loading State -->
                  <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted">{{ t('Loading payment information...') }}</p>
                  </div>

                  <!-- Payment Status Cards -->
                  <div v-else>
                    <!-- Stats Overview -->
                    <div class="row mb-4">
                      <div class="col-md-4">
                        <div class="stat-card text-center p-3 rounded-3 bg-primary bg-opacity-10">
                          <h3 class="text-primary mb-1">{{ stats.totalPayments || 0 }}</h3>
                          <p class="text-muted mb-0 small">{{ t('Total Payments') }}</p>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="stat-card text-center p-3 rounded-3 bg-success bg-opacity-10">
                          <h3 class="text-success mb-1">{{ stats.completedPayments || 0 }}</h3>
                          <p class="text-muted mb-0 small">{{ t('Verified') }}</p>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="stat-card text-center p-3 rounded-3 bg-warning bg-opacity-10">
                          <h3 class="text-warning mb-1">{{ stats.pendingPayments || 0 }}</h3>
                          <p class="text-muted mb-0 small">{{ t('Pending') }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Payments List -->
                    <div v-if="payments.length > 0">
                      <div v-for="payment in payments" :key="payment.id" class="payment-status-card mb-4">
                        <div class="row align-items-center">
                          <div class="col-md-8">
                            <h5 class="course-title mb-2">
                              <i class="fas fa-book me-2 text-primary"></i>
                              {{ payment.class?.name || 'Course' }}
                            </h5>
                            <div class="payment-details">
                              <div class="detail-item">
                                <span class="label">{{ t('Amount') }}:</span>
                                <span class="value fw-bold text-primary">৳ {{ payment.amount }}</span>
                              </div>
                              <div class="detail-item">
                                <span class="label">{{ t('Payment Method') }}:</span>
                                <span class="value">{{ getPaymentMethodName(payment.payment_method) }}</span>
                              </div>
                              <div class="detail-item">
                                <span class="label">{{ t('Date') }}:</span>
                                <span class="value">{{ formatDate(payment.created_at) }}</span>
                              </div>
                              <div v-if="payment.transaction_id" class="detail-item">
                                <span class="label">{{ t('Transaction ID') }}:</span>
                                <span class="value font-monospace">{{ payment.transaction_id }}</span>
                              </div>
                              <div v-if="payment.phone_number" class="detail-item">
                                <span class="label">{{ t('Phone Number') }}:</span>
                                <span class="value">{{ payment.phone_number }}</span>
                              </div>
                              <div v-if="payment.additional_services" class="detail-item">
                                <span class="label">{{ t('Additional Services') }}:</span>
                                <span class="value">
                                  <span v-if="payment.additional_services.certificate" class="badge bg-info me-1">
                                    <i class="fas fa-certificate me-1"></i>{{ t('Certificate') }}
                                  </span>
                                  <span v-if="payment.additional_services.consulting" class="badge bg-info">
                                    <i class="fas fa-user-tie me-1"></i>{{ t('Consulting') }}
                                  </span>
                                  <span v-if="!payment.additional_services.certificate && !payment.additional_services.consulting" class="text-muted">
                                    {{ t('None') }}
                                  </span>
                                </span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4 text-end">
                            <div class="status-badge mb-2" :class="getStatusClass(payment.status)">
                              <i :class="getStatusIcon(payment.status)" class="me-1"></i>
                              {{ getStatusText(payment.status) }}
                            </div>
                            
                            <div v-if="payment.status === 'pending'" class="pending-info">
                              <small class="text-muted d-block">
                                <i class="fas fa-clock me-1"></i>
                                {{ t('Under verification') }}
                              </small>
                              <small class="text-muted">
                                {{ t('Usually takes 24 hours') }}
                              </small>
                            </div>
                            
                            <div v-if="payment.status === 'rejected'" class="rejected-info mt-2">
                              <small class="text-danger d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ t('Rejected') }}
                              </small>
                              <small class="text-danger" v-if="payment.rejection_reason">
                                {{ payment.rejection_reason }}
                              </small>
                            </div>
                            
                            <div v-if="payment.status === 'completed'" class="verified-info mt-2">
                              <small class="text-success d-block">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ t('Verified on') }} {{ formatDate(payment.verified_at) }}
                              </small>
                              <Link 
                                v-if="payment.class" 
                                :href="`/student/learning/${payment.class_id}`"
                                class="btn btn-sm btn-outline-primary mt-2"
                              >
                                <i class="fas fa-play-circle me-1"></i>
                                {{ t('Start Learning') }}
                              </Link>
                            </div>

                            <!-- Receipt Download -->
                            <div v-if="payment.receipt_url" class="mt-2">
                              <button 
                                @click="downloadReceipt(payment.receipt_url)"
                                class="btn btn-sm btn-outline-secondary"
                              >
                                <i class="fas fa-download me-1"></i>
                                {{ t('Receipt') }}
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-5">
                      <i class="fas fa-receipt fa-4x text-muted mb-4"></i>
                      <h5 class="text-muted mb-3">{{ t('No payments found') }}</h5>
                      <p class="text-muted mb-4">{{ t('You haven\'t made any payments yet.') }}</p>
                      <Link href="/courses" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>
                        {{ t('Browse Courses') }}
                      </Link>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Support Card -->
              <div class="card border-0 shadow-sm mt-4">
                <div class="card-body text-center">
                  <i class="fas fa-headset text-primary fa-2x mb-3"></i>
                  <h5 class="card-title">{{ t('Need Help with Payments?') }}</h5>
                  <p class="text-muted mb-3">
                    {{ t('Our support team is here to help you with any payment-related issues') }}
                  </p>
                  <div class="d-flex justify-content-center gap-3">
                    <a href="tel:+8801234567890" class="btn btn-outline-primary btn-sm">
                      <i class="fas fa-phone me-1"></i>
                      +880 1234 567890
                    </a>
                    <a href="mailto:support@pathshala.com" class="btn btn-outline-primary btn-sm">
                      <i class="fas fa-envelope me-1"></i>
                      {{ t('Email Support') }}
                    </a>
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
import { ref, onMounted } from 'vue'
import { useTranslation } from '@/composables/useTranslation'

const { t } = useTranslation()

const loading = ref(true)
const payments = ref([])
const stats = ref({
  totalPayments: 0,
  completedPayments: 0,
  pendingPayments: 0
})

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getPaymentMethodName = (method) => {
  const methods = {
    'bkash': 'bKash',
    'nagad': 'Nagad',
    'rocket': 'Rocket',
    'upay': 'uPay',
    'bank_transfer': 'Bank Transfer'
  }
  return methods[method] || method
}

const getStatusClass = (status) => {
  const classes = {
    'completed': 'status-completed',
    'pending': 'status-pending',
    'rejected': 'status-rejected'
  }
  return classes[status] || 'status-pending'
}

const getStatusIcon = (status) => {
  const icons = {
    'completed': 'fas fa-check-circle',
    'pending': 'fas fa-clock',
    'rejected': 'fas fa-times-circle'
  }
  return icons[status] || 'fas fa-clock'
}

const getStatusText = (status) => {
  const texts = {
    'completed': t('Verified'),
    'pending': t('Pending'),
    'rejected': t('Rejected')
  }
  return texts[status] || status
}

const downloadReceipt = (receiptUrl) => {
  if (receiptUrl) {
    window.open(receiptUrl, '_blank')
  }
}

const fetchPayments = async () => {
  try {
    const response = await fetch('/api/payments/history')
    const result = await response.json()
    
    if (result.success) {
      payments.value = result.data
      
      // Calculate stats
      stats.value.totalPayments = payments.value.length
      stats.value.completedPayments = payments.value.filter(p => p.status === 'completed').length
      stats.value.pendingPayments = payments.value.filter(p => p.status === 'pending').length
    } else {
      console.error('Failed to fetch payments:', result.message)
    }
  } catch (error) {
    console.error('Error fetching payments:', error)
    alert(t('Failed to load payment history. Please try again.'))
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPayments()
})
</script>

<style scoped>
.payment-status-card {
  background: var(--bg-secondary);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 1.5rem;
  transition: all 0.3s ease;
  position: relative;
}

.payment-status-card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.payment-status-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  border-radius: 4px 0 0 4px;
}

.payment-status-card.status-completed::before {
  background: var(--success-color);
}

.payment-status-card.status-pending::before {
  background: var(--warning-color);
}

.payment-status-card.status-rejected::before {
  background: var(--danger-color);
}

.course-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.payment-details {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border-light);
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-item .label {
  font-weight: 500;
  color: var(--text-muted);
  font-size: 0.9rem;
}

.detail-item .value {
  color: var(--text-primary);
  font-weight: 500;
  font-size: 0.9rem;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
}

.status-completed {
  background: color-mix(in srgb, var(--success-color) 10%, transparent);
  color: var(--success-color);
  border: 1px solid color-mix(in srgb, var(--success-color) 30%, transparent);
}

.status-pending {
  background: color-mix(in srgb, var(--warning-color) 10%, transparent);
  color: var(--warning-color);
  border: 1px solid color-mix(in srgb, var(--warning-color) 30%, transparent);
}

.status-rejected {
  background: color-mix(in srgb, var(--danger-color) 10%, transparent);
  color: var(--danger-color);
  border: 1px solid color-mix(in srgb, var(--danger-color) 30%, transparent);
}

.stat-card {
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.font-monospace {
  font-family: 'Courier New', monospace;
}

@media (max-width: 768px) {
  .payment-details {
    grid-template-columns: 1fr;
  }
  
  .col-md-4.text-end {
    text-align: left !important;
    margin-top: 1rem;
    border-top: 1px solid var(--border-light);
    padding-top: 1rem;
  }
  
  .detail-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
  
  .stat-card {
    margin-bottom: 1rem;
  }
}

/* Animation for status badges */
@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(var(--warning-rgb), 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(var(--warning-rgb), 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(var(--warning-rgb), 0);
  }
}

.status-pending {
  animation: pulse 2s infinite;
}
</style>