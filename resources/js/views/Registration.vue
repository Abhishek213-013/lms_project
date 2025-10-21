<template>
  <div class="registration-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="registration-card">
            <div class="text-center mb-4">
              <h2>Student Registration</h2>
              <p class="text-muted">Complete your profile to get started</p>
            </div>

            <form @submit.prevent="handleRegistration">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="name" class="form-label">Full Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="name"
                    v-model="formData.name"
                    placeholder="Enter your full name"
                    required
                  >
                </div>

                <div class="col-md-6 mb-3">
                  <label for="username" class="form-label">Username *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    v-model="formData.username"
                    placeholder="Choose a username"
                    required
                  >
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email Address *</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="formData.email"
                  placeholder="Enter your email address"
                  required
                >
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="fatherName" class="form-label">Father's Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="fatherName"
                    v-model="formData.fatherName"
                    placeholder="Enter father's name"
                    required
                  >
                </div>

                <div class="col-md-6 mb-3">
                  <label for="motherName" class="form-label">Mother's Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="motherName"
                    v-model="formData.motherName"
                    placeholder="Enter mother's name"
                    required
                  >
                </div>
              </div>

              <div class="mb-3">
                <label for="class" class="form-label">Class *</label>
                <select class="form-select" id="class" v-model="formData.class" required>
                  <option value="">Select Class</option>
                  <option value="1">Class 1</option>
                  <option value="2">Class 2</option>
                  <option value="3">Class 3</option>
                  <option value="4">Class 4</option>
                  <option value="5">Class 5</option>
                  <option value="6">Class 6</option>
                  <option value="7">Class 7</option>
                  <option value="8">Class 8</option>
                  <option value="9">Class 9</option>
                  <option value="10">Class 10</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="parentContact" class="form-label">Parent's Contact Number *</label>
                <div class="input-group">
                  <select class="form-select country-code" v-model="formData.countryCode">
                    <option value="+880">Bangladesh (+880)</option>
                    <option value="+91">India (+91)</option>
                    <option value="+1">USA (+1)</option>
                  </select>
                  <input
                    type="tel"
                    class="form-control"
                    id="parentContact"
                    v-model="formData.parentContact"
                    placeholder="Enter parent's contact number"
                    required
                  >
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="formData.password"
                  placeholder="Create a password"
                  required
                >
              </div>

              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password *</label>
                <input
                  type="password"
                  class="form-control"
                  id="confirmPassword"
                  v-model="formData.confirmPassword"
                  placeholder="Confirm your password"
                  required
                >
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="terms" v-model="termsAccepted" required>
                <label class="form-check-label" for="terms">
                  I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a>
                </label>
              </div>

              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Creating Account...' : 'Create Account' }}
              </button>
            </form>

            <div class="text-center mt-3">
              <router-link to="/student-login" class="text-decoration-none">
                Already have an account? Login here
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
  name: 'Registration',
  data() {
    return {
      loading: false,
      termsAccepted: false,
      formData: {
        name: '',
        username: '',
        email: '',
        fatherName: '',
        motherName: '',
        class: '', // This should be class_id
        countryCode: '+880',
        parentContact: '',
        password: '',
        confirmPassword: ''
      }
    }
  },
  methods: {
    async handleRegistration() {
      // Validation
      if (this.formData.password !== this.formData.confirmPassword) {
        alert('Passwords do not match!');
        return;
      }

      if (!this.termsAccepted) {
        alert('Please accept the terms and conditions');
        return;
      }

      this.loading = true;
      
      try {
        // Send registration data to backend
        const response = await fetch('/api/register/student', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            name: this.formData.name,
            username: this.formData.username,
            email: this.formData.email,
            father_name: this.formData.fatherName,
            mother_name: this.formData.motherName,
            class_id: parseInt(this.formData.class), // Convert to integer
            country_code: this.formData.countryCode,
            parent_contact: this.formData.parentContact,
            password: this.formData.password,
            password_confirmation: this.formData.confirmPassword
          })
        });

        const data = await response.json();

        if (data.success) {
          // Store token and user data
          localStorage.setItem('token', 'student-token-' + Date.now());
          localStorage.setItem('user', JSON.stringify(data.data.user));
          
          alert('Registration successful! Welcome to SkillGro.');
          this.$router.push('/home');
        } else {
          // Handle validation errors
          if (data.errors) {
            const errorMessages = Object.values(data.errors).flat().join('\n');
            alert('Registration failed:\n' + errorMessages);
          } else {
            alert(data.message || 'Registration failed. Please try again.');
          }
        }
        
      } catch (error) {
        console.error('Registration failed:', error);
        alert('Registration failed. Please try again.');
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.registration-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  align-items: center;
  padding: 20px 0;
}

.registration-card {
  background: white;
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.form-control, .form-select {
  padding: 0.75rem 1rem;
  border-radius: 10px;
  border: 2px solid #e9ecef;
  transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
  border-color: #e74c3c;
  box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
}

.country-code {
  max-width: 180px;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
}

@media (max-width: 576px) {
  .registration-card {
    padding: 2rem 1.5rem;
  }
  
  .country-code {
    max-width: 140px;
  }
}
</style>