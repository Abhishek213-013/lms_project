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
.login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  align-items: center;
  padding: 20px 0;
}

.login-card {
  background: white;
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
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
  color: #e74c3c;
  margin: 0;
  line-height: 1.2;
}

.logo-subtitle {
  font-size: 0.8rem;
  font-weight: 500;
  color: #6c757d;
  margin: 0;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.login-title {
  color: #2c3e50;
  font-weight: 600;
  margin-bottom: 0.5rem;
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

.btn-primary {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  border: none;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
}

.btn-outline-primary {
  border-color: #e74c3c;
  color: #e74c3c;
  transition: all 0.3s ease;
}

.btn-outline-primary:hover {
  background-color: #e74c3c;
  border-color: #e74c3c;
  transform: translateY(-2px);
}

.alert {
  border-radius: 10px;
  border: none;
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
  .login-card {
    padding: 2rem 1.5rem;
  }
  
  .logo {
    max-height: 45px;
  }
  
  .logo-main {
    font-size: 1.2rem;
  }
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

/* Text link styling */
.text-decoration-none {
  color: #e74c3c;
  transition: color 0.3s ease;
}

.text-decoration-none:hover {
  color: #c0392b;
}

/* Border top for admin/teacher section */
.border-top {
  border-top: 1px solid #e9ecef !important;
}
</style>