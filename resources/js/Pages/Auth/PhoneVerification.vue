<template>
  <FrontendLayout>
    <div class="verification-page">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            <div class="verification-card">
              <div class="text-center mb-4">
                <h2>Phone Verification</h2>
                <p class="text-muted">Enter your phone number to receive OTP</p>
                
              </div>

              <!-- Error Message -->
              <div v-if="form.errors.message" class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ form.errors.message }}
                <button type="button" class="btn-close" @click="form.errors.message = ''"></button>
              </div>

              <form @submit.prevent="sendOTP" v-if="!otpSent">
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone Number *</label>
                  <div class="input-group">
                    <select class="form-select country-code" v-model="form.countryCode">
                      <option value="+880">Bangladesh (+880)</option>
                      <option value="+91">India (+91)</option>
                      <option value="+1">USA (+1)</option>
                    </select>
                    <input
                      type="tel"
                      class="form-control"
                      id="phone"
                      v-model="form.phoneNumber"
                      placeholder="Enter your phone number"
                      required
                      :class="{ 'is-invalid': form.errors.phoneNumber }"
                      :disabled="form.processing"
                    >
                  </div>
                  <div v-if="form.errors.phoneNumber" class="invalid-feedback d-block">
                    {{ form.errors.phoneNumber }}
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                  <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                  {{ form.processing ? 'Sending OTP...' : 'Send OTP' }}
                </button>
              </form>

              <form @submit.prevent="verifyOTP" v-else>
                <div class="mb-3">
                  <label for="otp" class="form-label">Enter OTP *</label>
                  <input
                    type="text"
                    class="form-control text-center"
                    id="otp"
                    v-model="otpForm.otp"
                    placeholder="Enter 6-digit OTP"
                    maxlength="6"
                    required
                    :class="{ 'is-invalid': otpForm.errors.otp }"
                    :disabled="otpForm.processing"
                  >
                  <div v-if="otpForm.errors.otp" class="invalid-feedback d-block">
                    {{ otpForm.errors.otp }}
                  </div>
                  <div class="form-text">
                    OTP has been sent to {{ form.countryCode }}{{ form.phoneNumber }}
                    <a href="#" @click="resendOTP" class="ms-2">Resend OTP</a>
                  </div>
                  
                  <!-- OTP Hint for Demo -->
                  <div class="alert alert-warning mt-2" role="alert">
                    <small>
                      <i class="fas fa-lightbulb me-1"></i>
                      <strong>Demo Tip:</strong> Use <code>123456</code> as OTP
                    </small>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" :disabled="otpForm.processing">
                  <span v-if="otpForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                  {{ otpForm.processing ? 'Verifying...' : 'Verify OTP' }}
                </button>
              </form>

              <div class="text-center mt-3">
                <Link href="/student-login" class="text-decoration-none">
                  ← Back to Login
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  errors: Object,
});

const otpSent = ref(false);

const form = useForm({
  countryCode: '+880',
  phoneNumber: '',
});

const otpForm = useForm({
  otp: '',
});

const sendOTP = () => {
  // For demo purposes, we'll simulate OTP sending
  form.processing = true;
  
  // Simulate API call delay
  setTimeout(() => {
    form.processing = false;
    otpSent.value = true;
    
    // Auto-fill OTP for demo convenience
    otpForm.otp = '123456';
    
    console.log(`📱 OTP sent to ${form.countryCode}${form.phoneNumber}`);
    console.log('🔑 Demo OTP: 123456');
  }, 1000);
};

const verifyOTP = () => {
  // For demo purposes, we'll accept only 123456
  if (otpForm.otp !== '123456') {
    otpForm.errors.otp = 'Invalid OTP. Please use 123456 for demo.';
    return;
  }

  otpForm.processing = true;
  
  // Simulate verification delay
  setTimeout(() => {
    otpForm.processing = false;
    
    // Redirect to registration with phone data
    router.get('/student-registration', {
      phone: `${form.countryCode}${form.phoneNumber}`
    });
  }, 1000);
};

const resendOTP = () => {
  otpForm.otp = '';
  sendOTP();
};
</script>

<style scoped>
.verification-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  align-items: center;
  padding: 20px 0;
}

.verification-card {
  background: white;
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.country-code {
  max-width: 180px;
}

.form-control {
  padding: 0.75rem 1rem;
  border-radius: 10px;
  border: 2px solid #e9ecef;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #e74c3c;
  box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
}

.form-control.is-invalid {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
}

.alert {
  border-radius: 10px;
  border: none;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.form-control:disabled {
  background-color: #f8f9fa;
  opacity: 0.7;
}

.invalid-feedback {
  display: block;
  font-size: 0.875em;
  color: #dc3545;
}

/* Demo info styles */
.alert-info {
  background: #d1ecf1;
  border: 1px solid #bee5eb;
  color: #0c5460;
}

.alert-warning {
  background: #fff3cd;
  border: 1px solid #ffeaa7;
  color: #856404;
}

code {
  background: #f8f9fa;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-weight: bold;
  color: #e74c3c;
}

@media (max-width: 576px) {
  .verification-card {
    padding: 2rem 1.5rem;
  }
  
  .country-code {
    max-width: 140px;
  }
}
</style>