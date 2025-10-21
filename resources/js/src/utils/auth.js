// src/utils/auth.js
export const authHelper = {
  // Check if user is authenticated
  isAuthenticated() {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    return !!(token && user.role === 'student');
  },

  // Get authentication headers
  getAuthHeaders() {
    const token = localStorage.getItem('token');
    return {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
  },

  // Handle authentication errors
  handleAuthError(error) {
    console.error('Auth error:', error);
    
    // If 401 or token invalid, clear storage and redirect to login
    if (error?.response?.status === 401 || error?.message?.includes('401')) {
      this.clearAuth();
      window.location.href = '/student-login';
      return true;
    }
    return false;
  },

  // Clear authentication data
  clearAuth() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  },

  // Refresh token (you can implement this if you have refresh token logic)
  async refreshToken() {
    // Implement token refresh logic here if needed
    return null;
  }
};

// Enhanced fetch with auth handling
export const authFetch = async (url, options = {}) => {
  const token = localStorage.getItem('token');
  
  const headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    ...options.headers
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  try {
    const response = await fetch(url, {
      ...options,
      headers
    });

    if (response.status === 401) {
      authHelper.clearAuth();
      throw new Error('Authentication required. Please login again.');
    }

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error('Fetch error:', error);
    throw error;
  }
};