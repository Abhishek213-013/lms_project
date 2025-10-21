<template>
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
            <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ errorMessage }}
              <button type="button" class="btn-close" @click="errorMessage = ''"></button>
            </div>

            <form @submit.prevent="sendOTP" v-if="!otpSent">
              <div class="mb-3">
                <label for="phone" class="form-label">Phone Number *</label>
                <div class="input-group">
                  <select class="form-select country-code" v-model="phoneData.countryCode">
                    <option value="+880">Bangladesh (+880)</option>
                    <option value="+91">India (+91)</option>
                    <option value="+1">USA (+1)</option>
                  </select>
                  <input
                    type="tel"
                    class="form-control"
                    id="phone"
                    v-model="phoneData.phoneNumber"
                    placeholder="Enter your phone number"
                    required
                    :class="{ 'is-invalid': fieldErrors.phoneNumber }"
                    :disabled="loading"
                  >
                </div>
                <div v-if="fieldErrors.phoneNumber" class="invalid-feedback d-block">
                  {{ fieldErrors.phoneNumber[0] }}
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Sending OTP...' : 'Send OTP' }}
              </button>
            </form>

            <form @submit.prevent="verifyOTP" v-else>
              <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP *</label>
                <input
                  type="text"
                  class="form-control text-center"
                  id="otp"
                  v-model="otpData.otp"
                  placeholder="Enter 6-digit OTP"
                  maxlength="6"
                  required
                  :class="{ 'is-invalid': fieldErrors.otp }"
                  :disabled="loading"
                >
                <div v-if="fieldErrors.otp" class="invalid-feedback d-block">
                  {{ fieldErrors.otp[0] }}
                </div>
                <div class="form-text">
                  OTP has been sent to {{ phoneData.countryCode }}{{ phoneData.phoneNumber }}
                  <a href="#" @click="resendOTP" class="ms-2">Resend OTP</a>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Verifying...' : 'Verify OTP' }}
              </button>
            </form>

            <div class="text-center mt-3">
              <router-link to="/student-login" class="text-decoration-none">
                ← Back to Login
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PhoneVerification',
  data() {
    return {
      loading: false,
      otpSent: false,
      errorMessage: '',
      fieldErrors: {},
      phoneData: {
        countryCode: '+880',
        phoneNumber: ''
      },
      otpData: {
        otp: ''
      }
    }
  },
  methods: {
    clearErrors() {
      this.errorMessage = '';
      this.fieldErrors = {};
    },

    async sendOTP() {
      this.clearErrors();
      
      // Client-side validation
      if (!this.phoneData.phoneNumber.trim()) {
        this.fieldErrors.phoneNumber = ['Phone number is required.'];
        return;
      }

      // Validate phone number format
      const phoneRegex = /^\d{10}$/;
      if (!phoneRegex.test(this.phoneData.phoneNumber.replace(/\D/g, ''))) {
        this.fieldErrors.phoneNumber = ['Please enter a valid 10-digit phone number.'];
        return;
      }

      this.loading = true;
      
      try {
        // Simulate API call to send OTP
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        // For demo purposes, we'll always succeed
        this.otpSent = true;
        this.errorMessage = '';
        
        // In real app, you would show a success message
        console.log(`OTP sent to ${this.phoneData.countryCode}${this.phoneData.phoneNumber}`);
        
      } catch (error) {
        console.error('Failed to send OTP:', error);
        this.errorMessage = 'Failed to send OTP. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    async verifyOTP() {
      this.clearErrors();

      if (!this.otpData.otp.trim()) {
        this.fieldErrors.otp = ['OTP is required.'];
        return;
      }

      // For demo purposes, accept any 6-digit OTP
      const otpRegex = /^\d{6}$/;
      if (!otpRegex.test(this.otpData.otp)) {
        this.fieldErrors.otp = ['Please enter a valid 6-digit OTP.'];
        return;
      }

      this.loading = true;
      
      try {
        // Simulate OTP verification
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // Store verified phone number for registration
        const verifiedPhone = {
          countryCode: this.phoneData.countryCode,
          phoneNumber: this.phoneData.phoneNumber,
          verified: true
        };
        
        localStorage.setItem('verifiedPhone', JSON.stringify(verifiedPhone));
        
        // Redirect to registration page with phone data
        this.$router.push({
          path: '/student-registration',
          query: {
            phone: `${this.phoneData.countryCode}${this.phoneData.phoneNumber}`
          }
        });
        
      } catch (error) {
        console.error('OTP verification failed:', error);
        this.errorMessage = 'OTP verification failed. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    resendOTP() {
      this.otpData.otp = '';
      this.sendOTP();
    }
  }
}
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

@media (max-width: 576px) {
  .verification-card {
    padding: 2rem 1.5rem;
  }
  
  .country-code {
    max-width: 140px;
  }
}
</style>