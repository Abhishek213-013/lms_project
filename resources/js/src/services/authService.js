// src/services/authService.js
class AuthService {
  constructor() {
    this.token = localStorage.getItem('token');
    this.user = JSON.parse(localStorage.getItem('user') || '{}');
  }

  // Check if user is authenticated
  isAuthenticated() {
    return !!(this.token && this.user.id);
  }

  // NEW: Test API connectivity
  async testConnection() {
    try {
      console.log('🔌 Testing API connectivity...');
      
      // Try a simple endpoint that doesn't require authentication
      const testEndpoints = [
        '/api/csrf-cookie',
        '/sanctum/csrf-cookie', 
        '/api/health',
        '/api/status'
      ];
      
      for (const endpoint of testEndpoints) {
        try {
          const response = await fetch(endpoint, {
            method: 'GET',
            credentials: 'include',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
          
          if (response.ok) {
            console.log(`✅ API connection test passed via ${endpoint}`);
            return true;
          }
        } catch (error) {
          console.log(`❌ Endpoint ${endpoint} failed:`, error.message);
          continue;
        }
      }
      
      // If all endpoints fail, try a simple HEAD request to the base URL
      try {
        const response = await fetch('/', {
          method: 'HEAD',
          credentials: 'include'
        });
        
        if (response.ok) {
          console.log('✅ Base URL connection test passed');
          return true;
        }
      } catch (error) {
        console.log('❌ Base URL test failed:', error.message);
      }
      
      console.log('❌ All API connection tests failed');
      return false;
      
    } catch (error) {
      console.error('❌ API connection test error:', error);
      return false;
    }
  }

  // Get authentication headers
  getAuthHeaders() {
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    };
    
    // Add CSRF token from cookie if available
    const csrfToken = this.getCSRFToken();
    if (csrfToken) {
      headers['X-XSRF-TOKEN'] = csrfToken;
    }
    
    // Add Bearer token if available
    if (this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }
    
    return headers;
  }

  // Get CSRF token from cookies
  getCSRFToken() {
    return this.getCookie('XSRF-TOKEN');
  }

  // Get cookie value
  getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
      return decodeURIComponent(parts.pop().split(';').shift());
    }
    return null;
  }

  // Validate token by making a test API call
  async validateToken() {
    if (!this.token) return false;

    try {
      const response = await fetch('/api/user', {
        method: 'GET',
        headers: this.getAuthHeaders(),
        credentials: 'include'
      });

      return response.ok;
    } catch (error) {
      console.error('Token validation failed:', error);
      return false;
    }
  }

  // Refresh token
  async refreshToken() {
    this.clearAuth();
    return null;
  }

  // Clear authentication data
  clearAuth() {
    this.token = null;
    this.user = {};
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }

  // Set authentication data
  setAuth(token, user) {
    this.token = token;
    this.user = user;
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
  }

  // Handle API calls with automatic token validation
  async apiCall(url, options = {}) {
    if (!this.isAuthenticated() && !url.includes('/login') && !url.includes('/register')) {
      throw new Error('Authentication required. Please login again.');
    }

    // Prepare request options with credentials and headers
    const requestOptions = {
      credentials: 'include',
      ...options,
      headers: {
        ...this.getAuthHeaders(),
        ...options.headers
      }
    };

    try {
      const response = await fetch(url, requestOptions);

      // Handle 403 Forbidden (CSRF token issues)
      if (response.status === 403) {
        const errorData = await response.json().catch(() => ({}));
        if (errorData.message?.includes('CSRF') || response.statusText.includes('Forbidden')) {
          throw new Error('CSRF token validation failed. Please refresh the page and try again.');
        }
      }

      // Handle 401 Unauthorized
      if (response.status === 401) {
        this.clearAuth();
        throw new Error('Your session has expired. Please login again.');
      }

      if (!response.ok) {
        let errorMessage = `API error: ${response.status}`;
        try {
          const errorData = await response.json();
          errorMessage = errorData.message || errorMessage;
        } catch {
          // Ignore JSON parse errors
        }
        throw new Error(errorMessage);
      }

      return await response.json();

    } catch (error) {
      console.error('API call failed:', error);
      
      // Clear auth on specific errors
      if (error.message.includes('session') || 
          error.message.includes('Authentication') || 
          error.message.includes('401') ||
          error.message.includes('403') ||
          error.message.includes('CSRF')) {
        this.clearAuth();
      }
      
      throw error;
    }
  }
}

// Create a singleton instance
export const authService = new AuthService();