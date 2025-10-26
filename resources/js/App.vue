<template>
  <div id="app">
    <router-view></router-view>
    
    <!-- Global Toast Container -->
    <div id="toast-container" class="toast-container"></div>
    
    <!-- Global Loading Overlay -->
    <div v-if="globalLoading" class="global-loading-overlay">
      <div class="loading-content">
        <div class="loading-spinner-large"></div>
        <p>{{ loadingMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {  // ADD THIS LINE
  name: 'App',
  data() {
    return {
      globalLoading: false,
      loadingMessage: 'Loading...',
      csrfToken: null
    }
  },
  mounted() {
    this.initializeApp();
  },
  methods: {
    async initializeApp() {
      console.log('SkillGro LMS Frontend Initialized');
      
      // Hide any initial loading screen
      const loadingScreen = document.querySelector('.loading-screen');
      if (loadingScreen) {
        loadingScreen.style.display = 'none';
      }
      
      // Initialize global functionality first
      this.initializeGlobalScripts();
      this.initializeToastSystem();
      this.initializeGlobalLoading();
      
      // Then get CSRF token and setup interceptors
      await this.initializeCSRFAndInterceptors();
    },
    
    async initializeCSRFAndInterceptors() {
      // Try multiple CSRF endpoints
      await this.getCSRFToken();
      
      // Setup interceptors
      this.setupAxiosInterceptors();
    },
    
    async getCSRFToken() {
      try {
        console.log('🛡️ Getting CSRF token...');
        
        // Try multiple possible CSRF endpoints
        const csrfEndpoints = [
          '/sanctum/csrf-cookie',
          '/csrf-cookie',
          '/api/csrf-cookie'
        ];
        
        let success = false;
        
        for (const endpoint of csrfEndpoints) {
          try {
            const response = await fetch(endpoint, {
              method: 'GET',
              credentials: 'include',
              headers: {
                'Accept': 'application/json',
              }
            });
            
            if (response.ok) {
              console.log(`✅ CSRF token from ${endpoint}`);
              success = true;
              break;
            }
          } catch (error) {
            console.log(`❌ CSRF endpoint ${endpoint} failed:`, error.message);
            continue;
          }
        }
        
        if (success) {
          this.csrfToken = this.getCookie('XSRF-TOKEN') || 
                          this.getCookie('csrf_token') || 
                          this.getCookie('laravel_session');
          console.log('✅ CSRF token obtained:', this.csrfToken ? 'Yes' : 'No (but cookies set)');
        } else {
          console.warn('⚠️ All CSRF endpoints failed, proceeding without explicit token');
        }
      } catch (error) {
        console.warn('⚠️ CSRF token initialization failed:', error);
      }
    },
    
    getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) {
        return decodeURIComponent(parts.pop().split(';').shift());
      }
      return null;
    },
    
    initializeGlobalScripts() {
      if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      }
      
      console.log('Global scripts initialized');
    },
    
    initializeToastSystem() {
      window.showToast = (message, type = 'info', duration = 5000) => {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
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
        
        setTimeout(() => {
          toast.classList.add('show');
        }, 10);
        
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
      
      window.handleError = (error, customMessage = null) => {
        console.error('Application Error:', error);
        
        let message = customMessage || 'An unexpected error occurred';
        
        if (error.response) {
          const status = error.response.status;
          
          switch (status) {
            case 403:
              if (error.response.data?.message?.includes('CSRF') || 
                  error.response.data?.errors?.csrf) {
                message = 'Security token expired. Please refresh the page and try again.';
                setTimeout(() => {
                  window.location.reload();
                }, 3000);
              } else if (error.response.data?.message?.includes('permission') ||
                        error.response.data?.message?.includes('unauthorized')) {
                message = 'You do not have permission to perform this action.';
              } else {
                message = error.response.data?.message || 'Access forbidden. Please check your permissions.';
              }
              break;
              
            case 401:
              message = 'Session expired. Please login again.';
              localStorage.removeItem('token');
              localStorage.removeItem('user');
              setTimeout(() => {
                const currentPath = window.location.pathname;
                if (currentPath.includes('/admin') || currentPath.includes('/super-admin')) {
                  window.location.href = '/login';
                } else {
                  window.location.href = '/student-login';
                }
              }, 2000);
              break;
              
            case 422:
              const errors = error.response.data.errors;
              message = Object.values(errors).flat().join(', ') || 'Validation failed. Please check your input.';
              break;
              
            case 419:
              message = 'Page expired. Please refresh and try again.';
              setTimeout(() => {
                window.location.reload();
              }, 2000);
              break;
              
            default:
              message = error.response.data?.message || `Server Error: ${status}`;
          }
        } else if (error.request) {
          message = 'Network error. Please check your internet connection.';
        } else {
          message = error.message || message;
        }
        
        window.showToast(message, 'error');
      };
      
      window.handleSuccess = (message = 'Operation completed successfully') => {
        window.showToast(message, 'success');
      };
    },
    
    initializeGlobalLoading() {
      window.setGlobalLoading = (loading, message = 'Loading...') => {
        this.globalLoading = loading;
        this.loadingMessage = message;
      };
    },
    
    setupAxiosInterceptors() {
      const axios = window.axios || this.$axios || this.$http;
      
      if (axios) {
        console.log('✅ Axios found, setting up interceptors...');
        
        axios.interceptors.request.use(
          (config) => {
            if (config.method !== 'get') {
              window.setGlobalLoading(true, 'Processing request...');
            }
            
            if (config.method !== 'get') {
              config.headers = {
                ...config.headers,
                'X-Requested-With': 'XMLHttpRequest'
              };
              
              const csrfToken = this.getCookie('XSRF-TOKEN');
              if (csrfToken) {
                config.headers['X-XSRF-TOKEN'] = csrfToken;
              }
            }
            
            config.withCredentials = true;
            
            return config;
          },
          (error) => {
            window.setGlobalLoading(false);
            return Promise.reject(error);
          }
        );
        
        axios.interceptors.response.use(
          (response) => {
            window.setGlobalLoading(false);
            return response;
          },
          (error) => {
            window.setGlobalLoading(false);
            window.handleError(error);
            return Promise.reject(error);
          }
        );
        
        console.log('✅ Axios interceptors setup completed');
      } else {
        console.log('❌ Axios not available, setting up fetch interceptor...');
        this.setupFetchInterceptor();
      }
    },
    
    setupFetchInterceptor() {
      const originalFetch = window.fetch;
      
      window.fetch = async (...args) => {
        const [url, options = {}] = args;
        const isGetRequest = options.method === 'GET' || !options.method;
        
        if (!isGetRequest) {
          window.setGlobalLoading(true, 'Processing request...');
        }
        
        try {
          const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers
          };
          
          const csrfToken = this.getCookie('XSRF-TOKEN');
          if (csrfToken && url.startsWith('/')) {
            headers['X-XSRF-TOKEN'] = csrfToken;
          }
          
          const fetchOptions = {
            ...options,
            headers,
            credentials: 'include'
          };
          
          const response = await originalFetch(url, fetchOptions);
          
          if (!response.ok) {
            let errorData;
            try {
              errorData = await response.json();
            } catch {
              errorData = { message: `HTTP ${response.status}` };
            }
            
            let errorMessage = errorData.message || `HTTP ${response.status}`;
            
            if (response.status === 422 && errorData.errors) {
              const validationErrors = Object.values(errorData.errors).flat().join(', ');
              errorMessage = `Validation failed: ${validationErrors}`;
            } else if (response.status === 403) {
              errorMessage = 'Access forbidden. Please check your permissions.';
            }
            
            const error = new Error(errorMessage);
            error.response = {
              status: response.status,
              data: errorData,
              statusText: response.statusText
            };
            throw error;
          }
          
          window.setGlobalLoading(false);
          return response;
          
        } catch (error) {
          window.setGlobalLoading(false);
          window.handleError(error);
          throw error;
        }
      };
      
      console.log('✅ Fetch interceptor setup completed');
    }
  },
  
  watch: {
    $route(to, from) {
      this.globalLoading = false;
    }
  }
}  // ADD THIS CLOSING BRACE
</script>

<style>
/* Your existing styles remain the same */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.3s;
}
.page-enter,
.page-leave-to {
  opacity: 0;
}

.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
}

.toast {
  background: white;
  border-radius: 8px;
  padding: 15px 20px;
  margin-bottom: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #e74c3c;
  min-width: 300px;
  animation: slideInRight 0.3s ease;
}

.toast.success {
  border-left-color: #27ae60;
}

.toast.error {
  border-left-color: #e74c3c;
}

.toast.warning {
  border-left-color: #f39c12;
}

.toast.info {
  border-left-color: #3498db;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

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
  color: #2c3e50;
  font-weight: 500;
}

.loading-spinner-large {
  display: inline-block;
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #e74c3c;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.toast-content {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.toast-icon {
  font-size: 18px;
  font-weight: bold;
  line-height: 1;
  margin-top: 2px;
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
</style>