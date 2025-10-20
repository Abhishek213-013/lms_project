<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Approval Statistics" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Admins Management</h1>
            <p class="text-gray-600">Manage all administrator accounts</p>
          </div>
          <button 
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Add Admin</span>
          </button>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700">{{ error }}</span>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex justify-center items-center py-12">
          <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading admins...</p>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Admins</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ admins.length }}</h3>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Active Today</p>
                <h3 class="text-3xl font-bold text-green-600">5</h3>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Last 7 Days</p>
                <h3 class="text-3xl font-bold text-purple-600">2</h3>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">System Access</p>
                <h3 class="text-3xl font-bold text-orange-600">Limited</h3>
              </div>
              <div class="p-3 bg-orange-100 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-semibold text-gray-800">Administrators</h3>
              <div class="relative">
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search admins..." 
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Education</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in filteredAdmins" :key="user.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">
                          {{ getUserInitials(user.name) }}
                        </span>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                        <div class="text-sm text-gray-500">@{{ user.username }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ user.email }}</div>
                    <div class="text-sm text-gray-500">DOB: {{ formatDate(user.dob) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ user.education_qualification }}</div>
                    <div class="text-sm text-gray-500">{{ user.institute }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      Active
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button 
                      @click="editUser(user)"
                      class="text-blue-600 hover:text-blue-900 mr-3"
                      :disabled="loading"
                    >
                      Edit
                    </button>
                    <button 
                      @click="deleteUser(user.id)"
                      class="text-red-600 hover:text-red-900"
                      :disabled="loading"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="filteredAdmins.length === 0 && !isLoading" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No admins found</h3>
            <p class="text-gray-500 mb-4">Get started by creating your first administrator.</p>
            <button 
              @click="showCreateModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
            >
              Add Admin
            </button>
          </div>
        </div>

        <!-- Create Admin Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">{{ editingUser ? 'Edit Admin' : 'Create New Admin' }}</h3>
            </div>

            <!-- Error Display inside Modal -->
            <div v-if="error" class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-red-700">{{ error }}</span>
              </div>
            </div>

            <form @submit.prevent="editingUser ? updateAdmin() : createAdmin()" class="p-6 space-y-4">
              <!-- Personal Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                  <input 
                    v-model="newUser.name"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter full name"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                  <input 
                    v-model="newUser.username"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter username"
                  >
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                  <input 
                    v-model="newUser.email"
                    type="email" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter email address"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                  <input 
                    v-model="newUser.dob"
                    type="date" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                </div>
              </div>

              <!-- Education Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Education Qualification *</label>
                  <select 
                    v-model="newUser.education_qualification"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Select qualification</option>
                    <option value="HSC">HSC</option>
                    <option value="BSC">BSC</option>
                    <option value="BA">BA</option>
                    <option value="MA">MA</option>
                    <option value="MSC">MSC</option>
                    <option value="PhD">PhD</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Institute *</label>
                  <input 
                    v-model="newUser.institute"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter institute name"
                  >
                </div>
              </div>

              <!-- Password (only show for create, optional for edit) -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4" v-if="!editingUser">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                  <input 
                    v-model="newUser.password"
                    type="password" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter password"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                  <input 
                    v-model="newUser.password_confirmation"
                    type="password" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Confirm password"
                  >
                </div>
              </div>

              <!-- Optional password update for edit -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4" v-if="editingUser">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">New Password (optional)</label>
                  <input 
                    v-model="newUser.password"
                    type="password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Leave blank to keep current"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                  <input 
                    v-model="newUser.password_confirmation"
                    type="password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Confirm new password"
                  >
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button 
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="loading"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center"
                >
                  <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ loading ? (editingUser ? 'Updating...' : 'Creating...') : (editingUser ? 'Update Admin' : 'Create Admin') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient, { getCsrfToken, initializeApp } from '../api/client'
import Sidebar from './Layout/Sidebar.vue'
import Navbar from './Layout/Navbar.vue'

const router = useRouter()
const showCreateModal = ref(false)
const searchQuery = ref('')
const loading = ref(false)
const admins = ref([])
const error = ref('')
const isLoading = ref(true)

// Sidebar and navbar state
const activeMenu = ref('users')
const userMenuOpen = ref(false)
const user = ref(null)
const isDark = ref(false)

// Edit state
const editingUser = ref(null)

const newUser = ref({
  name: '',
  username: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  password: '',
  password_confirmation: '',
  role: 'admin'
})

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

// Authentication check
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    router.push('/login')
    return
  }
  
  user.value = userData
  console.log('User authenticated:', userData)
}

// Check if user is super admin
const isSuperAdmin = computed(() => {
  return user.value?.role === 'super_admin'
})

// Get user initials for profile picture
const userInitials = computed(() => {
  if (!user.value?.name) return 'AD'
  return user.value.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

// Filter admins based on search
const filteredAdmins = computed(() => {
  if (!searchQuery.value) return admins.value
  
  const query = searchQuery.value.toLowerCase()
  return admins.value.filter(user => 
    user.name?.toLowerCase().includes(query) ||
    user.username?.toLowerCase().includes(query) ||
    user.email?.toLowerCase().includes(query)
  )
})

// Sidebar and navbar functions
const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
}

const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const logout = async () => {
  try {
    const token = localStorage.getItem('token')
    await apiClient.post('/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Test API connectivity
const testAPI = async () => {
  try {
    console.log('🧪 Testing API connection...');
    
    // Test basic API connectivity
    const testResponse = await apiClient.get('/test');
    console.log('✅ API test successful:', testResponse.data);
    
    return true;
  } catch (error) {
    console.error('❌ API test failed:', error);
    
    if (error.response) {
      console.error('Response status:', error.response.status);
      console.error('Response data:', error.response.data);
    } else if (error.request) {
      console.error('No response received. Check if Laravel is running.');
      error.value = 'Cannot connect to server. Make sure Laravel is running on port 8000.';
    } else {
      console.error('Request setup error:', error.message);
    }
    
    return false;
  }
}

// Fetch admins
const fetchAdmins = async () => {
  try {
    error.value = ''
    isLoading.value = true
    
    console.log('🔍 Starting to fetch admins...')
    
    // First test API connectivity
    const apiAvailable = await testAPI()
    if (!apiAvailable) {
      throw new Error('API server not available')
    }

    // Get current token for debugging
    const token = localStorage.getItem('token')
    console.log('🔑 Current token:', token ? 'Present' : 'Missing')
    
    if (!token) {
      error.value = 'No authentication token found. Please login again.'
      router.push('/login')
      return
    }

    // Ensure CSRF token is available
    console.log('🛡️ Ensuring CSRF token...')
    await getCsrfToken()
    
    console.log('📡 Making API request to /users/admins')
    const response = await apiClient.get('/users/admins')
    
    console.log('✅ API Response received:', response.status)
    console.log('📊 Response data:', response.data)
    
    if (response.data.success) {
      admins.value = response.data.data || []
      console.log(`📊 Loaded ${admins.value.length} admins`)
    } else {
      error.value = response.data.message || 'Failed to fetch admins'
      console.error('❌ API returned error:', response.data)
    }
  } catch (err) {
    console.error('💥 Error fetching admins:', err);
    
    // More robust error handling
    if (err.response) {
      console.error('📡 Response error details:', {
        status: err.response.status,
        statusText: err.response.statusText,
        data: err.response.data
      });
      
      if (err.response.status === 401) {
        error.value = 'Authentication failed. Please login again.';
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        router.push('/login');
        return;
      } else if (err.response.status === 403) {
        error.value = 'Access denied. You do not have permission to view admins.';
      } else if (err.response.status === 404) {
        error.value = 'API endpoint not found. The server route may not be configured.';
      } else if (err.response.data?.message) {
        error.value = err.response.data.message;
      }
    } else if (err.request) {
      console.error('🌐 No response received:', err.request);
      error.value = 'No response from server. Please check if Laravel is running on port 8000.';
    } else {
      console.error('⚡ Request setup error:', err.message);
      error.value = 'Request failed: ' + err.message;
    }
    
    // Fallback to mock data only for development
    if (import.meta.env.DEV) {
      console.log('🔄 Using mock data for development');
      admins.value = getMockAdmins();
      error.value = 'Using mock data - ' + (error.value || 'API connection failed');
    }
  } finally {
    loading.value = false;
    isLoading.value = false;
  }
}

// Create new admin
const createAdmin = async () => {
  console.log('🎯 Create Admin function called')
  console.log('📝 Form data:', newUser.value)

  // Validate passwords match
  if (newUser.value.password !== newUser.value.password_confirmation) {
    error.value = 'Passwords do not match!'
    console.log('❌ Password validation failed')
    return
  }

  // Validate all required fields
  const requiredFields = ['name', 'username', 'email', 'dob', 'education_qualification', 'institute', 'password']
  const missingFields = requiredFields.filter(field => !newUser.value[field])
  
  if (missingFields.length > 0) {
    error.value = `Please fill in all required fields: ${missingFields.join(', ')}`
    console.log('❌ Missing fields:', missingFields)
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    console.log('🛡️ Getting CSRF token...')
    await getCsrfToken()
    
    console.log('📡 Making API request to /users/admins')
    const token = localStorage.getItem('token')
    console.log('🔑 Using token:', token ? 'Present' : 'Missing')
    
    const response = await apiClient.post('/users/admins', newUser.value)
    
    console.log('✅ API Response received:', response.data)
    
    if (response.data.success) {
      console.log('🎉 Admin created successfully')
      // Add the new admin to the list
      admins.value.push(response.data.user)
      
      // Close modal and reset form
      closeModal()
      
      // Show success message
      alert('Admin created successfully!')
      
      // Refresh the admin list
      await fetchAdmins()
    } else {
      error.value = response.data.message || 'Failed to create admin'
      console.error('❌ API returned error:', response.data)
    }
  } catch (err) {
    console.error('💥 Error creating admin:', err)
    
    // Detailed error handling
    if (err.response) {
      console.error('📡 Response details:', {
        status: err.response.status,
        data: err.response.data,
        headers: err.response.headers
      })
      
      if (err.response.status === 422) {
        // Validation errors from Laravel
        const errors = err.response.data.errors
        const errorMessages = Object.values(errors).flat()
        error.value = 'Validation errors: ' + errorMessages.join(', ')
      } else if (err.response.status === 401) {
        error.value = 'Authentication failed. Please login again.'
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      } else if (err.response.status === 404) {
        error.value = 'API endpoint not found. The server route may not be configured.'
      } else if (err.response.data?.message) {
        error.value = err.response.data.message
      } else if (err.response.data?.error) {
        error.value = err.response.data.error
      }
    } else if (err.request) {
      console.error('🌐 No response received:', err.request)
      error.value = 'No response from server. Please check if Laravel is running on port 8000.'
    } else {
      console.error('⚡ Request setup error:', err.message)
      error.value = 'Error creating admin. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

// Edit user
const editUser = (user) => {
  console.log('✏️ Editing user:', user)
  editingUser.value = user
  newUser.value = {
    name: user.name,
    username: user.username,
    email: user.email,
    dob: user.dob,
    education_qualification: user.education_qualification,
    institute: user.institute,
    password: '',
    password_confirmation: '',
    role: user.role
  }
  showCreateModal.value = true
}

// Update admin
const updateAdmin = async () => {
  console.log('🔄 Update Admin function called')
  console.log('📝 Form data:', newUser.value)

  // Validate passwords match if provided
  if (newUser.value.password && newUser.value.password !== newUser.value.password_confirmation) {
    error.value = 'Passwords do not match!'
    console.log('❌ Password validation failed')
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    console.log('🛡️ Getting CSRF token...')
    await getCsrfToken()
    
    console.log(`📡 Making API request to /users/${editingUser.value.id}`)
    const response = await apiClient.put(`/users/${editingUser.value.id}`, newUser.value)
    
    console.log('✅ API Response received:', response.data)
    
    if (response.data.success) {
      console.log('🎉 Admin updated successfully')
      
      // Update the user in the list
      const index = admins.value.findIndex(u => u.id === editingUser.value.id)
      if (index !== -1) {
        admins.value[index] = { ...admins.value[index], ...response.data.user }
      }
      
      // Close modal and reset form
      closeModal()
      
      // Show success message
      alert('Admin updated successfully!')
    } else {
      error.value = response.data.message || 'Failed to update admin'
      console.error('❌ API returned error:', response.data)
    }
  } catch (err) {
    console.error('💥 Error updating admin:', err)
    
    // Detailed error handling
    if (err.response) {
      console.error('📡 Response details:', {
        status: err.response.status,
        data: err.response.data,
        headers: err.response.headers
      })
      
      if (err.response.status === 422) {
        // Validation errors from Laravel
        const errors = err.response.data.errors
        const errorMessages = Object.values(errors).flat()
        error.value = 'Validation errors: ' + errorMessages.join(', ')
      } else if (err.response.status === 401) {
        error.value = 'Authentication failed. Please login again.'
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      } else if (err.response.status === 404) {
        error.value = 'API endpoint not found. The server route may not be configured.'
      } else if (err.response.data?.message) {
        error.value = err.response.data.message
      } else if (err.response.data?.error) {
        error.value = err.response.data.error
      }
    } else if (err.request) {
      console.error('🌐 No response received:', err.request)
      error.value = 'No response from server. Please check if Laravel is running on port 8000.'
    } else {
      console.error('⚡ Request setup error:', err.message)
      error.value = 'Error updating admin. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

// Delete user
const deleteUser = async (userId) => {
  if (!confirm('Are you sure you want to delete this admin? This action cannot be undone.')) {
    return
  }

  console.log('🗑️ Deleting user:', userId)
  loading.value = true
  
  try {
    console.log('🛡️ Getting CSRF token...')
    await getCsrfToken()
    
    console.log(`📡 Making API request to /users/${userId}`)
    const response = await apiClient.delete(`/users/${userId}`)
    
    console.log('✅ API Response received:', response.data)
    
    if (response.data.success) {
      console.log('🎉 Admin deleted successfully')
      
      // Remove the user from the list
      admins.value = admins.value.filter(user => user.id !== userId)
      
      // Show success message
      alert('Admin deleted successfully!')
    } else {
      error.value = response.data.message || 'Failed to delete admin'
      console.error('❌ API returned error:', response.data)
    }
  } catch (err) {
    console.error('💥 Error deleting admin:', err)
    
    // Detailed error handling
    if (err.response) {
      console.error('📡 Response details:', {
        status: err.response.status,
        data: err.response.data,
        headers: err.response.headers
      })
      
      if (err.response.status === 401) {
        error.value = 'Authentication failed. Please login again.'
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      } else if (err.response.status === 404) {
        error.value = 'User not found.'
      } else if (err.response.data?.message) {
        error.value = err.response.data.message
      } else if (err.response.data?.error) {
        error.value = err.response.data.error
      }
    } else if (err.request) {
      console.error('🌐 No response received:', err.request)
      error.value = 'No response from server. Please check if Laravel is running on port 8000.'
    } else {
      console.error('⚡ Request setup error:', err.message)
      error.value = 'Error deleting admin. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

// Close modal and reset form
const closeModal = () => {
  showCreateModal.value = false
  editingUser.value = null
  error.value = ''
  resetForm()
}

// Mock data function
const getMockAdmins = () => {
  return [
    {
      id: 3,
      name: 'Regular Admin',
      username: 'admin',
      email: 'admin@example.com',
      dob: '1990-01-01',
      education_qualification: 'BSC',
      institute: 'Test University',
      role: 'admin',
      created_at: '2024-01-03T00:00:00.000000Z'
    },
    {
      id: 4,
      name: 'Course Manager',
      username: 'coursemanager',
      email: 'course.manager@example.com',
      dob: '1988-08-20',
      education_qualification: 'MA',
      institute: 'Education Institute',
      role: 'admin',
      created_at: '2024-01-04T00:00:00.000000Z'
    }
  ]
}

// Helper functions
const getUserInitials = (name) => {
  if (!name) return 'AD'
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const resetForm = () => {
  newUser.value = {
    name: '',
    username: '',
    email: '',
    dob: '',
    education_qualification: '',
    institute: '',
    password: '',
    password_confirmation: '',
    role: 'admin'
  }
}

// Initialize app when component mounts
onMounted(() => {
  console.log('🚀 Admins component mounted')
  
  // Initialize authentication and sidebar functionality
  document.addEventListener('click', handleClickOutside)
  checkAuthentication()
  
  // Initialize CSRF token first
  initializeApp().then(() => {
    console.log('✅ App initialized successfully')
  }).catch(error => {
    console.error('❌ Failed to initialize app:', error)
  })
  
  // Test API first, then fetch data
  testAPI().then(() => {
    fetchAdmins()
  })
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.rotate-180 {
  transform: rotate(180deg);
}

.submenu-link {
  display: block;
  padding: 0.5rem 0.75rem;
  color: #4b5563;
  border-radius: 0.5rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
}

.submenu-link.router-link-active {
  color: #4f46e5;
  background-color: #f0f9ff;
}
</style>