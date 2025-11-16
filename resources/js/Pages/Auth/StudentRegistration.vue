<template>
  <div class="registration-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="registration-card">
            <div class="text-center mb-4">
              <!-- Pathshala LMS Logo -->
              <div class="logo-container">
                <img src="/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo">
              </div>
              <h2 class="mt-4 registration-title">Student Registration</h2>
              <p class="text-muted">Complete your profile to get started with Pathshala LMS</p>
            </div>

            <!-- Success Message -->
            <div v-if="status" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ status }}
              <button type="button" class="btn-close" @click="status = ''"></button>
            </div>

            <form @submit.prevent="submit">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="name" class="form-label">Full Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="name"
                    v-model="form.name"
                    placeholder="Enter your full name"
                    required
                    :class="{ 'is-invalid': form.errors.name }"
                  >
                  <div v-if="form.errors.name" class="invalid-feedback">
                    {{ form.errors.name }}
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="username" class="form-label">Username *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    v-model="form.username"
                    placeholder="Choose a username"
                    required
                    :class="{ 'is-invalid': form.errors.username }"
                  >
                  <div v-if="form.errors.username" class="invalid-feedback">
                    {{ form.errors.username }}
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email Address *</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="form.email"
                  placeholder="Enter your email address"
                  required
                  :class="{ 'is-invalid': form.errors.email }"
                >
                <div v-if="form.errors.email" class="invalid-feedback">
                  {{ form.errors.email }}
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="father_name" class="form-label">Father's Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="father_name"
                    v-model="form.father_name"
                    placeholder="Enter father's name"
                    required
                    :class="{ 'is-invalid': form.errors.father_name }"
                  >
                  <div v-if="form.errors.father_name" class="invalid-feedback">
                    {{ form.errors.father_name }}
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="mother_name" class="form-label">Mother's Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="mother_name"
                    v-model="form.mother_name"
                    placeholder="Enter mother's name"
                    required
                    :class="{ 'is-invalid': form.errors.mother_name }"
                  >
                  <div v-if="form.errors.mother_name" class="invalid-feedback">
                    {{ form.errors.mother_name }}
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="academic_class_id" class="form-label">Academic Class *</label>
                <select 
                  class="form-select" 
                  id="academic_class_id" 
                  v-model="form.academic_class_id" 
                  required 
                  :class="{ 'is-invalid': form.errors.academic_class_id }"
                >
                  <option value="">Select Academic Class</option>
                  <option 
                    v-for="academicClass in academicClasses" 
                    :key="academicClass.id" 
                    :value="academicClass.id"
                  >
                    {{ academicClass.name }}
                  </option>
                </select>
                <div v-if="form.errors.academic_class_id" class="invalid-feedback">
                  {{ form.errors.academic_class_id }}
                </div>
              </div>

              <div class="mb-3">
                <label for="parent_contact" class="form-label">Parent's Contact Number *</label>
                <div class="input-group">
                  <select class="form-select country-code" v-model="form.country_code" :class="{ 'is-invalid': form.errors.country_code }">
                    <option value="+880">Bangladesh (+880)</option>
                    <option value="+91">India (+91)</option>
                    <option value="+1">USA (+1)</option>
                    <option value="+44">UK (+44)</option>
                    <option value="+971">UAE (+971)</option>
                  </select>
                  <input
                    type="tel"
                    class="form-control"
                    id="parent_contact"
                    v-model="form.parent_contact"
                    placeholder="Enter parent's contact number"
                    required
                    :class="{ 'is-invalid': form.errors.parent_contact }"
                  >
                </div>
                <div v-if="form.errors.parent_contact" class="invalid-feedback">
                  {{ form.errors.parent_contact }}
                </div>
                <div v-if="form.errors.country_code" class="invalid-feedback">
                  {{ form.errors.country_code }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="form.password"
                  placeholder="Create a password"
                  required
                  :class="{ 'is-invalid': form.errors.password }"
                >
                <div v-if="form.errors.password" class="invalid-feedback">
                  {{ form.errors.password }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                <input
                  type="password"
                  class="form-control"
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  placeholder="Confirm your password"
                  required
                  :class="{ 'is-invalid': form.errors.password_confirmation }"
                >
                <div v-if="form.errors.password_confirmation" class="invalid-feedback">
                  {{ form.errors.password_confirmation }}
                </div>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="terms" v-model="form.terms" required :class="{ 'is-invalid': form.errors.terms }">
                <label class="form-check-label" for="terms">
                  I agree to the <a href="/terms" class="text-decoration-none">Terms and Conditions</a>
                </label>
                <div v-if="form.errors.terms" class="invalid-feedback">
                  {{ form.errors.terms }}
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                {{ form.processing ? 'Creating Account...' : 'Create Account' }}
              </button>
            </form>

            <div class="text-center mt-3">
              <a href="/student-login" class="text-decoration-none">
                Already have an account? Login here
              </a>
            </div>

            <!-- Admin/Teacher Registration Link -->
            <div class="text-center mt-4 pt-3 border-top">
              <p class="text-muted small mb-2">Are you an admin or teacher?</p>
              <a href="/registration" class="btn btn-sm btn-outline-secondary">
                Admin/Teacher Registration
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  errors: Object,
  status: String,
  phone: String,
  academicClasses: {
    type: Array,
    default: () => []
  }
});

const form = useForm({
  name: '',
  username: '',
  email: '',
  father_name: '',
  mother_name: '',
  academic_class_id: '',
  country_code: '+880',
  parent_contact: props.phone || '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const submit = () => {
  form.post('/student-registration', {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Student registration successful');
    },
    onError: (errors) => {
      console.log('❌ Student registration errors:', errors);
    },
  });
};
</script>

<style scoped>
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}

.registration-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
  display: flex;
  align-items: center;
  padding: 20px 0;
}

.registration-card {
  background: white;
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(59, 130, 246, 0.1);
  border: 1px solid #e0f2fe;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.registration-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
}

/* Logo Container */
.logo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  margin-bottom: 10px;
}

.logo {
  max-height: 50px;
  width: auto;
}

.logo-text {
  text-align: left;
}

.logo-main {
  font-size: 1.5rem;
  font-weight: 700;
  color: #3b82f6;
  margin: 0;
  line-height: 1.2;
}

.logo-subtitle {
  font-size: 0.8rem;
  font-weight: 500;
  color: #6b7280;
  margin: 0;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.registration-title {
  color: #1f2937;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.form-control, .form-select {
  padding: 0.75rem 1rem;
  border-radius: 10px;
  border: 2px solid #e5e7eb;
  transition: all 0.3s ease;
  font-size: 14px;
}

.form-control:focus, .form-select:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.form-control.is-invalid, .form-select.is-invalid {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.country-code {
  max-width: 180px;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
  font-size: 14px;
}

.btn-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border: none;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.btn-primary:active {
  transform: translateY(0);
}

.btn-outline-secondary {
  border: 2px solid #6b7280;
  color: #6b7280;
  background: transparent;
}

.btn-outline-secondary:hover {
  background-color: #6b7280;
  border-color: #6b7280;
  color: white;
  transform: translateY(-2px);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.alert {
  border-radius: 10px;
  border: none;
  font-size: 14px;
}

.alert-success {
  background-color: #f0fdf4;
  color: #16a34a;
  border-left: 4px solid #16a34a;
}

.btn-close {
  background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
  opacity: 0.7;
}

.btn-close:hover {
  opacity: 1;
}

.invalid-feedback {
  display: block;
  font-size: 0.875em;
  color: #dc3545;
  margin-top: 0.25rem;
}

.form-check-input:checked {
  background-color: #3b82f6;
  border-color: #3b82f6;
}

.form-check-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

/* Text link styling */
.text-decoration-none {
  color: #3b82f6;
  transition: color 0.3s ease;
  font-weight: 500;
}

.text-decoration-none:hover {
  color: #1d4ed8;
  text-decoration: underline !important;
}

/* Border top for admin/teacher section */
.border-top {
  border-top: 1px solid #e5e7eb !important;
}

/* Responsive Design */
@media (max-width: 768px) {
  .logo-container {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .logo-text {
    text-align: center;
  }
  
  .logo-main {
    font-size: 1.3rem;
  }
  
  .logo-subtitle {
    font-size: 0.7rem;
  }
}

@media (max-width: 576px) {
  .registration-card {
    padding: 2rem 1.5rem;
    margin: 0 10px;
  }
  
  .logo {
    max-height: 45px;
  }
  
  .logo-main {
    font-size: 1.2rem;
  }
  
  .country-code {
    max-width: 140px;
  }
  
  .btn {
    padding: 0.75rem 1rem;
  }
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
  border-width: 0.15em;
}

/* Form label styling */
.form-label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 14px;
}

.form-check-label {
  color: #374151;
  font-size: 14px;
}

/* Input group styling */
.input-group {
  gap: 0.5rem;
}

.input-group .form-select,
.input-group .form-control {
  border-radius: 10px !important;
}

/* Focus states for accessibility */
.btn:focus,
.form-control:focus,
.form-select:focus,
.form-check-input:focus {
  outline: none;
}

/* Loading state improvements */
.btn:disabled .spinner-border-sm {
  border-color: currentColor transparent currentColor transparent;
}

/* Additional form element hover effects */
.form-control:hover, .form-select:hover {
  border-color: #93c5fd;
}
</style>