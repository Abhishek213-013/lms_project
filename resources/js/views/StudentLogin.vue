<template>
  <div class="login-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="login-card">
            <div class="text-center mb-4">
              <img src="/assets/img/logo.png" alt="SkillGro" class="logo">
              <h2 class="mt-3">Student Login</h2>
              <p class="text-muted">Welcome back to SkillGro</p>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
              <div v-if="typeof errorMessage === 'string'">{{ errorMessage }}</div>
              <div v-else>
                <div v-for="(errors, field) in errorMessage" :key="field" class="mb-1">
                  <strong>{{ field.replace('_', ' ') }}:</strong> 
                  <span v-for="error in errors" :key="error" class="d-block">{{ error }}</span>
                </div>
              </div>
              <button type="button" class="btn-close" @click="clearErrors"></button>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ successMessage }}
              <button type="button" class="btn-close" @click="successMessage = ''"></button>
            </div>

            <form @submit.prevent="handleLogin">
              <div class="mb-3">
                <label for="login" class="form-label">Username or Email Address *</label>
                <input
                  type="text"
                  class="form-control"
                  id="login"
                  v-model="loginData.login"
                  placeholder="Enter your username or email"
                  required
                  :class="{ 'is-invalid': fieldErrors.login }"
                  :disabled="loading"
                >
                <div v-if="fieldErrors.login" class="invalid-feedback">
                  {{ fieldErrors.login[0] }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="loginData.password"
                  placeholder="Enter your password"
                  required
                  :class="{ 'is-invalid': fieldErrors.password }"
                  :disabled="loading"
                >
                <div v-if="fieldErrors.password" class="invalid-feedback">
                  {{ fieldErrors.password[0] }}
                </div>
              </div>

              <div class="mb-3 form-check">
                <input 
                  type="checkbox" 
                  class="form-check-input" 
                  id="remember" 
                  v-model="rememberMe"
                  :disabled="loading"
                >
                <label class="form-check-label" for="remember">Remember me</label>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Logging in...' : 'Login' }}
              </button>

              <div class="text-center">
                <a href="#" class="text-decoration-none" @click.prevent="handleForgotPassword">
                  Forgot Password?
                </a>
              </div>
            </form>

            <!-- In StudentLogin.vue -->
            <div class="text-center mt-4">
            <p class="mb-0">New to SkillGro?</p>
            <router-link to="/phone-verification" class="btn btn-outline-primary mt-2">
                Create Student Account
            </router-link>
            </div>

            <!-- Admin/Teacher Login Links -->
            <div class="text-center mt-4 pt-3 border-top">
              <p class="text-muted small mb-2">Are you an admin or teacher?</p>
              <div class="d-flex gap-2 justify-content-center">
                <router-link to="/admin-login" class="btn btn-sm btn-outline-secondary">
                  Admin Login
                </router-link>
                <router-link to="/teacher-login" class="btn btn-sm btn-outline-secondary">
                  Teacher Login
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StudentLogin',
  data() {
    return {
      loading: false,
      rememberMe: false,
      errorMessage: '',
      successMessage: '',
      fieldErrors: {},
      loginData: {
        login: '', // Changed from 'email' to 'login' to handle both username and email
        password: ''
      }
    }
  },
  mounted() {
    // Check if user is already logged in
    const token = localStorage.getItem('token');
    const user = localStorage.getItem('user');
    
    if (token && user) {
      const userData = JSON.parse(user);
      if (userData.role === 'student') {
        this.$router.push('/student-dashboard');
      }
    }

    // Check for success message from registration
    const registrationSuccess = localStorage.getItem('registration_success');
    if (registrationSuccess) {
      this.successMessage = 'Registration successful! Please login with your credentials.';
      localStorage.removeItem('registration_success');
    }

    // Restore remember me data
    if (localStorage.getItem('rememberMe') === 'true') {
      this.rememberMe = true;
      const savedLogin = localStorage.getItem('savedLogin');
      if (savedLogin) {
        this.loginData.login = savedLogin;
      }
    }
  },
  methods: {
    clearErrors() {
      this.errorMessage = '';
      this.fieldErrors = {};
    },

    async handleLogin() {
      // Reset messages and errors
      this.clearErrors();
      this.successMessage = '';

      // Client-side validation
      if (!this.loginData.login.trim()) {
        this.fieldErrors.login = ['The username or email field is required.'];
        return;
      }

      if (!this.loginData.password.trim()) {
        this.fieldErrors.password = ['The password field is required.'];
        return;
      }

      this.loading = true;
      
      try {
        // Make API call to login endpoint
        const response = await fetch('/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            // Try different field names that your backend might expect
            email: this.loginData.login, // Some backends expect 'email'
            username: this.loginData.login, // Some backends expect 'username'
            login: this.loginData.login, // Some backends accept 'login' for both
            password: this.loginData.password,
          })
        });

        const data = await response.json();

        if (!response.ok) {
          // Handle HTTP errors (4xx, 5xx)
          throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }

        if (data.success && data.user) {
          // Check if the logged-in user is a student
          if (data.user.role !== 'student') {
            this.errorMessage = 'This login is for students only. Please use the appropriate login page.';
            return;
          }

          // Store token and user data
          if (data.token) {
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
          }

          // Store remember me preference
          if (this.rememberMe) {
            localStorage.setItem('rememberMe', 'true');
            localStorage.setItem('savedLogin', this.loginData.login);
          } else {
            localStorage.removeItem('rememberMe');
            localStorage.removeItem('savedLogin');
          }

          // Show success message
          this.successMessage = `Welcome back, ${data.user.name}!`;

          // Redirect to student dashboard after a brief delay
          setTimeout(() => {
            this.$router.push('/student-dashboard');
          }, 1000);

        } else {
          // Handle validation errors from backend
          if (data.errors) {
            this.fieldErrors = data.errors;
            this.errorMessage = 'Please fix the errors below.';
          } else {
            this.errorMessage = data.message || 'Login failed. Please check your credentials.';
          }
        }
        
      } catch (error) {
        console.error('Login failed:', error);
        
        // Handle specific error cases
        if (error.message.includes('Failed to fetch')) {
          this.errorMessage = 'Network error. Please check your internet connection.';
        } else if (error.message.includes('HTTP error')) {
          this.errorMessage = error.message;
        } else {
          this.errorMessage = 'An unexpected error occurred. Please try again.';
        }
      } finally {
        this.loading = false;
      }
    },

    // Alternative method with specific field names
    async handleLoginAlternative() {
      this.clearErrors();
      this.loading = true;

      try {
        // Try with specific field names that your backend expects
        const payload = {
          password: this.loginData.password
        };

        // Determine if input is email or username
        if (this.loginData.login.includes('@')) {
          payload.email = this.loginData.login;
        } else {
          payload.username = this.loginData.login;
        }

        const response = await fetch('/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success && data.user) {
          // ... success handling same as above
          this.handleLoginSuccess(data);
        } else {
          this.handleLoginError(data);
        }
        
      } catch (error) {
        this.handleLoginError(error);
      } finally {
        this.loading = false;
      }
    },

    handleLoginSuccess(data) {
      if (data.user.role !== 'student') {
        this.errorMessage = 'This login is for students only.';
        return;
      }

      localStorage.setItem('token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));

      if (this.rememberMe) {
        localStorage.setItem('rememberMe', 'true');
        localStorage.setItem('savedLogin', this.loginData.login);
      }

      this.successMessage = `Welcome back, ${data.user.name}!`;
      setTimeout(() => this.$router.push('/student-dashboard'), 1000);
    },

    handleLoginError(error) {
      if (error.errors) {
        this.fieldErrors = error.errors;
        this.errorMessage = 'Please fix the validation errors below.';
      } else {
        this.errorMessage = error.message || 'Login failed. Please check your credentials.';
      }
    },

    handleForgotPassword() {
      this.$router.push('/forgot-password');
    }
  }
}
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

.logo {
  max-height: 60px;
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
  .login-card {
    padding: 2rem 1.5rem;
  }
}

/* Loading animation */
.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}
</style>