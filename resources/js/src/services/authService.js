// src/services/authService.js
class AuthService {
  constructor() {
    this.token = localStorage.getItem('token');
    this.user = JSON.parse(localStorage.getItem('user') || '{}');
  }

  // Check if user is authenticated
  isAuthenticated() {
    return !!(this.token && this.user.role === 'student');
  }

  // Get authentication headers
  getAuthHeaders() {
    return {
      'Authorization': `Bearer ${this.token}`,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
  }

  // Validate token by making a test API call
  async validateToken() {
    if (!this.token) return false;

    try {
      const response = await fetch('/api/user', {
        method: 'GET',
        headers: this.getAuthHeaders()
      });

      return response.ok;
    } catch (error) {
      console.error('Token validation failed:', error);
      return false;
    }
  }

  // Refresh token (you can implement this if you have refresh token logic)
  async refreshToken() {
    // Implement token refresh logic here if your backend supports it
    // For now, we'll just clear and redirect to login
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
    if (!this.isAuthenticated()) {
      throw new Error('Authentication required. Please login again.');
    }

    // Add auth headers to the request
    const headers = {
      ...this.getAuthHeaders(),
      ...options.headers
    };

    try {
      const response = await fetch(url, {
        ...options,
        headers
      });

      // Handle 401 Unauthorized
      if (response.status === 401) {
        console.log('🔐 Token expired, attempting refresh...');
        
        // Try to refresh token
        const newToken = await this.refreshToken();
        if (!newToken) {
          this.clearAuth();
          throw new Error('Your session has expired. Please login again.');
        }

        // Retry the request with new token
        headers.Authorization = `Bearer ${newToken}`;
        const retryResponse = await fetch(url, {
          ...options,
          headers
        });

        if (!retryResponse.ok) {
          throw new Error(`API error: ${retryResponse.status}`);
        }

        return await retryResponse.json();
      }

      if (!response.ok) {
        throw new Error(`API error: ${response.status}`);
      }

      return await response.json();

    } catch (error) {
      console.error('API call failed:', error);
      
      // If it's an auth error, clear storage
      if (error.message.includes('session') || error.message.includes('Authentication') || error.message.includes('401')) {
        this.clearAuth();
      }
      
      throw error;
    }
  }
}

// Create a singleton instance
export const authService = new AuthService();