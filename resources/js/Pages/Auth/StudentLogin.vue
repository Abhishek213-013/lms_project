<template>
  <div class="login-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="login-card">
            <div class="text-center mb-4">
              <!-- Pathshala LMS Logo -->
              <div class="logo-container">
                <img src="/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo">
              </div>
              <h2 class="mt-4 login-title">Student Login</h2>
              <p class="text-muted">Welcome back to Pathshala LMS</p>
            </div>

            <!-- Error Message -->
            <div v-if="form.errors.message" class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ form.errors.message }}
              <button type="button" class="btn-close" @click="form.errors.message = ''"></button>
            </div>

            <!-- Success Message -->
            <div v-if="status" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ status }}
              <button type="button" class="btn-close" @click="status = ''"></button>
            </div>

            <form @submit.prevent="submit">
              <div class="mb-3">
                <label for="login" class="form-label">Username or Email Address *</label>
                <input
                  type="text"
                  class="form-control"
                  id="login"
                  v-model="form.login"
                  placeholder="Enter your username or email"
                  required
                  :class="{ 'is-invalid': form.errors.login }"
                  :disabled="form.processing"
                >
                <div v-if="form.errors.login" class="invalid-feedback">
                  {{ form.errors.login }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="form.password"
                  placeholder="Enter your password"
                  required
                  :class="{ 'is-invalid': form.errors.password }"
                  :disabled="form.processing"
                >
                <div v-if="form.errors.password" class="invalid-feedback">
                  {{ form.errors.password }}
                </div>
              </div>

              <div class="mb-3 form-check">
                <input 
                  type="checkbox" 
                  class="form-check-input" 
                  id="remember" 
                  v-model="form.remember"
                  :disabled="form.processing"
                >
                <label class="form-check-label" for="remember">Remember me</label>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3" :disabled="form.processing">
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                {{ form.processing ? 'Logging in...' : 'Login' }}
              </button>

              <div class="text-center">
                <a href="/forgot-password" class="text-decoration-none">
                  Forgot Password?
                </a>
              </div>
            </form>

            <!-- Registration Link -->
            <div class="text-center mt-4">
              <p class="mb-0">New to Pathshala LMS?</p>
              <a href="/phone-verification" class="btn btn-outline-primary mt-2">
                Create Student Account
              </a>
            </div>

            <!-- Admin/Teacher Login Links -->
            <div class="text-center mt-4 pt-3 border-top">
              <p class="text-muted small mb-2">Are you an admin or teacher?</p>
              <div class="d-flex gap-2 justify-content-center">
                <a href="/login" class="btn btn-sm btn-outline-secondary">
                  Admin/Teacher Login
                </a>
              </div>
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
});

const form = useForm({
  login: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post('/student-login', {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Student login successful');
    },
    onError: (errors) => {
      console.log('❌ Student login errors:', errors);
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

.login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
  display: flex;
  align-items: center;
  padding: 20px 0;
}

.login-card {
  background: white;
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(59, 130, 246, 0.1);
  border: 1px solid #e0f2fe;
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

.login-title {
  color: #1f2937;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.form-control {
  padding: 0.75rem 1rem;
  border-radius: 10px;
  border: 2px solid #e5e7eb;
  transition: all 0.3s ease;
  font-size: 14px;
}

.form-control:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.form-control.is-invalid {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
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

.btn-outline-primary {
  border: 2px solid #3b82f6;
  color: #3b82f6;
  background: transparent;
}

.btn-outline-primary:hover {
  background-color: #3b82f6;
  border-color: #3b82f6;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
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

.alert {
  border-radius: 10px;
  border: none;
  font-size: 14px;
}

.alert-danger {
  background-color: #fef2f2;
  color: #dc2626;
  border-left: 4px solid #dc2626;
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

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.form-control:disabled {
  background-color: #f8f9fa;
  opacity: 0.7;
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
  
  .d-flex.gap-2 {
    flex-direction: column;
    gap: 0.5rem !important;
  }
}

@media (max-width: 576px) {
  .login-card {
    padding: 2rem 1.5rem;
    margin: 0 10px;
  }
  
  .logo {
    max-height: 45px;
  }
  
  .logo-main {
    font-size: 1.2rem;
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

/* Additional hover effects */
.login-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
}

/* Focus states for accessibility */
.btn:focus,
.form-control:focus,
.form-check-input:focus {
  outline: none;
}

/* Loading state improvements */
.btn:disabled .spinner-border-sm {
  border-color: currentColor transparent currentColor transparent;
}
</style>