import axios from 'axios';

// Get the base URL from environment or use default
const baseURL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000';

// Create axios instance with default config
const apiClient = axios.create({
    baseURL: baseURL + '/api', // Full URL including /api
    withCredentials: true, // Important for Sanctum
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }
});

// Request interceptor to add auth token and handle FormData
apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        
        // IMPORTANT: For FormData uploads, let browser set Content-Type automatically
        // This is crucial for file uploads to work properly
        if (config.data instanceof FormData) {
            delete config.headers['Content-Type']; // Let browser set boundary
        }
        
        // Log request for debugging
        console.log('🚀 Making API request:', {
            url: config.url,
            method: config.method,
            data: config.data instanceof FormData ? 'FormData' : config.data,
            headers: config.headers
        });
        
        return config;
    },
    (error) => {
        console.error('❌ Request interceptor error:', error);
        return Promise.reject(error);
    }
);

// Response interceptor to handle errors
apiClient.interceptors.response.use(
    (response) => {
        console.log('✅ API Response:', {
            url: response.config.url,
            status: response.status,
            data: response.data
        });
        return response;
    },
    async (error) => {
        console.error('💥 API Error:', {
            url: error.config?.url,
            status: error.response?.status,
            message: error.message,
            response: error.response?.data
        });

        // Check if error.config exists before using it
        if (!error.config) {
            console.error('❌ Error config is undefined, cannot retry request');
            return Promise.reject(error);
        }

        const originalRequest = error.config;

        if (error.response?.status === 401) {
            // Token expired or invalid
            console.log("🔑 Token expired, redirecting to login");
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
            return Promise.reject(error);
        }

        if (error.response?.status === 419) {
            // CSRF token mismatch
            if (originalRequest._retry) {
                console.log("🔄 Already retried CSRF token refresh, rejecting");
                return Promise.reject(error);
            }
            
            console.log("🔄 CSRF token expired, getting new token");
            originalRequest._retry = true;
            
            try {
                await getCsrfToken();
                // Retry the original request with new CSRF token
                const token = localStorage.getItem('token');
                if (token) {
                    originalRequest.headers.Authorization = `Bearer ${token}`;
                }
                return apiClient.request(originalRequest);
            } catch (csrfError) {
                console.error('❌ Failed to refresh CSRF token:', csrfError);
                return Promise.reject(csrfError);
            }
        }

        if (error.response?.status === 500) {
            console.error('🔥 Server error:', error.response.data);
            error.message = 'Server error. Please try again later.';
        }

        return Promise.reject(error);
    }
);

// Function to get CSRF token
export const getCsrfToken = async () => {
    try {
        console.log('🛡️ Getting CSRF token...');
        const response = await axios.get('/sanctum/csrf-cookie', {
            baseURL: baseURL,
            withCredentials: true
        });
        console.log('✅ CSRF token obtained successfully');
        return response;
    } catch (error) {
        console.error('❌ Failed to get CSRF token:', error);
        throw error;
    }
};

// Initialize CSRF token when the app starts
export const initializeApp = async () => {
    try {
        await getCsrfToken();
        console.log('🎉 App initialized with CSRF token');
    } catch (error) {
        console.error('❌ Failed to initialize app:', error);
    }
};

export default apiClient;