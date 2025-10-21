<template>
  <div id="app">
    <router-view></router-view>
    
    <!-- Global Toast Container -->
    <div id="toast-container" class="toast-container"></div>
    
    <!-- Global Loading Overlay -->
    <div v-if="globalLoading" class="global-loading-overlay">
      <div class="loading-content">
        <div class="loading-spinner-large"></div>
        <p>Loading...</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      globalLoading: false
    }
  },
  mounted() {
    this.initializeApp();
  },
  methods: {
    initializeApp() {
      console.log('SkillGro LMS Frontend Initialized');
      
      // Hide any initial loading screen
      const loadingScreen = document.querySelector('.loading-screen');
      if (loadingScreen) {
        loadingScreen.style.display = 'none';
      }
      
      // Check if user is already logged in and redirect accordingly
      this.checkAuthenticationStatus();
      
      // Initialize global functionality
      this.initializeGlobalScripts();
      this.initializeToastSystem();
      this.initializeGlobalLoading();
      
      // Set up axios interceptors for global loading (only if available)
      this.setupAxiosInterceptors();
    },
    
    checkAuthenticationStatus() {
      const token = localStorage.getItem('token');
      const user = JSON.parse(localStorage.getItem('user') || '{}');
      
      if (token && user.role) {
        console.log('User already authenticated:', user.role);
        // User is already logged in, they'll be redirected by the router guard
      } else {
        console.log('No active session, showing student login');
        // Ensure we're on the student login page
        if (this.$route.path !== '/student-login') {
          this.$router.push('/student-login');
        }
      }
    },
    
    initializeGlobalScripts() {
      // Initialize tooltips if Bootstrap is available
      if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      }
      
      console.log('Global scripts initialized');
    },
    
    initializeToastSystem() {
      // Global toast function
      window.showToast = (message, type = 'info', duration = 5000) => {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        // Toast icon based on type
        const icons = {
          success: '✓',
          error: '✕',
          warning: '⚠',
          info: 'ℹ'
        };
        
        toast.innerHTML = `
          <div class="toast-content">
            <span class="toast-icon">${icons[type] || icons.info}</span>
            <div class="toast-message">
              <strong class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
              <span class="toast-text">${message}</span>
            </div>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
          </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Add enter animation
        setTimeout(() => {
          toast.classList.add('show');
        }, 10);
        
        // Auto remove after duration
        if (duration > 0) {
          setTimeout(() => {
            if (toast.parentElement) {
              toast.classList.remove('show');
              setTimeout(() => {
                if (toast.parentElement) {
                  toast.remove();
                }
              }, 300);
            }
          }, duration);
        }
      };
      
      // Global error handler
      window.handleError = (error, customMessage = null) => {
        console.error('Application Error:', error);
        
        let message = customMessage || 'An unexpected error occurred';
        
        if (error.response) {
          // Server responded with error status
          message = error.response.data?.message || `Server Error: ${error.response.status}`;
        } else if (error.request) {
          // Request made but no response received
          message = 'Network error. Please check your connection.';
        } else {
          // Something else happened
          message = error.message || message;
        }
        
        window.showToast(message, 'error');
      };
      
      // Global success handler
      window.handleSuccess = (message = 'Operation completed successfully') => {
        window.showToast(message, 'success');
      };
    },
    
    initializeGlobalLoading() {
      // Global loading state management
      window.setGlobalLoading = (loading) => {
        this.globalLoading = loading;
      };
    },
    
    setupAxiosInterceptors() {
      // Check if $http is available (axios instance)
      if (!this.$http) {
        console.log('Axios instance ($http) not available - skipping interceptors setup');
        return;
      }
      
      // Add request interceptor for global loading
      this.$http.interceptors.request.use(
        (config) => {
          // Show global loading for non-GET requests or long-running requests
          if (config.method !== 'get') {
            this.globalLoading = true;
          }
          return config;
        },
        (error) => {
          this.globalLoading = false;
          return Promise.reject(error);
        }
      );
      
      // Add response interceptor
      this.$http.interceptors.response.use(
        (response) => {
          this.globalLoading = false;
          return response;
        },
        (error) => {
          this.globalLoading = false;
          
          // Handle common HTTP errors
          if (error.response?.status === 401) {
            // Unauthorized - clear storage and redirect to login
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('loginType');
            window.showToast('Session expired. Please login again.', 'error');
            this.$router.push('/student-login');
          } else if (error.response?.status === 403) {
            window.showToast('You do not have permission to perform this action.', 'error');
          } else if (error.response?.status === 404) {
            window.showToast('Requested resource not found.', 'error');
          } else if (error.response?.status >= 500) {
            window.showToast('Server error. Please try again later.', 'error');
          }
          
          return Promise.reject(error);
        }
      );

      console.log('Axios interceptors setup completed');
    }
  },
  watch: {
    // Watch for route changes
    $route(to, from) {
      // Reset global loading on route change
      this.globalLoading = false;
      
      // Track page view for analytics (if needed)
      if (typeof gtag !== 'undefined') {
        gtag('config', 'GA_MEASUREMENT_ID', {
          page_title: to.meta.title || 'SkillGro',
          page_path: to.path
        });
      }
    }
  }
}
</script>

<style>
/* Import core styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  color: #333;
  background-color: #f8f9fa;
}

:root {
  --primary-color: #e74c3c;
  --secondary-color: #2c3e50;
  --success-color: #27ae60;
  --warning-color: #f39c12;
  --error-color: #e74c3c;
  --info-color: #3498db;
  --light-color: #f8f9fa;
  --dark-color: #343a40;
}

/* Vue transition styles */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}
.page-enter,
.page-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

/* Toast notifications */
.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.toast {
  background: white;
  border-radius: 12px;
  padding: 0;
  margin-bottom: 0;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  border-left: 4px solid var(--info-color);
  min-width: 320px;
  max-width: 400px;
  transform: translateX(100%);
  opacity: 0;
  transition: all 0.3s ease;
}

.toast.show {
  transform: translateX(0);
  opacity: 1;
}

.toast.success {
  border-left-color: var(--success-color);
}

.toast.error {
  border-left-color: var(--error-color);
}

.toast.warning {
  border-left-color: var(--warning-color);
}

.toast.info {
  border-left-color: var(--info-color);
}

.toast-content {
  display: flex;
  align-items: flex-start;
  padding: 16px;
  gap: 12px;
}

.toast-icon {
  font-size: 18px;
  font-weight: bold;
  line-height: 1;
  margin-top: 2px;
}

.toast.success .toast-icon {
  color: var(--success-color);
}

.toast.error .toast-icon {
  color: var(--error-color);
}

.toast.warning .toast-icon {
  color: var(--warning-color);
}

.toast.info .toast-icon {
  color: var(--info-color);
}

.toast-message {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.toast-title {
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.toast-text {
  font-size: 14px;
  color: #666;
  line-height: 1.4;
}

.toast-close {
  background: none;
  border: none;
  font-size: 18px;
  color: #999;
  cursor: pointer;
  padding: 0;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s ease;
}

.toast-close:hover {
  color: #666;
}

/* Loading states */
.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.loading-spinner-large {
  width: 40px;
  height: 40px;
  border-width: 3px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Global loading overlay */
.global-loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9998;
  backdrop-filter: blur(5px);
}

.loading-content {
  text-align: center;
  background: white;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
}

.loading-content p {
  margin-top: 16px;
  color: var(--secondary-color);
  font-weight: 500;
}

/* Utility classes */
.text-primary { color: var(--primary-color) !important; }
.text-secondary { color: var(--secondary-color) !important; }
.text-success { color: var(--success-color) !important; }
.text-warning { color: var(--warning-color) !important; }
.text-error { color: var(--error-color) !important; }
.text-info { color: var(--info-color) !important; }

.bg-primary { background-color: var(--primary-color) !important; }
.bg-secondary { background-color: var(--secondary-color) !important; }
.bg-success { background-color: var(--success-color) !important; }
.bg-warning { background-color: var(--warning-color) !important; }
.bg-error { background-color: var(--error-color) !important; }
.bg-info { background-color: var(--info-color) !important; }

/* Responsive design helpers */
@media (max-width: 768px) {
  .toast-container {
    top: 10px;
    right: 10px;
    left: 10px;
  }
  
  .toast {
    min-width: auto;
    max-width: none;
  }
  
  .loading-content {
    margin: 20px;
    padding: 30px 20px;
  }
}

/* Print styles */
@media print {
  .global-loading-overlay,
  .toast-container {
    display: none !important;
  }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* Focus styles for accessibility */
button:focus,
input:focus,
select:focus,
textarea:focus,
a:focus {
  outline: 2px solid var(--primary-color);
  outline-offset: 2px;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  body {
    background-color: #1a1a1a;
    color: #e0e0e0;
  }
  
  .toast {
    background: #2d2d2d;
    color: #e0e0e0;
  }
  
  .toast-text {
    color: #b0b0b0;
  }
  
  .loading-content {
    background: #2d2d2d;
    border-color: #404040;
  }
  
  .loading-content p {
    color: #e0e0e0;
  }
}
</style>