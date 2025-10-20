<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64 min-w-0">
      <!-- Top Navbar -->
      
      <Navbar page-title="Approval Statistics" @search="handleSearch" />
      <!-- Page Content -->
      <div class="p-6 max-w-full overflow-x-hidden">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
          <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">Teachers Management</h1>
            <p class="text-gray-600 mt-2">Manage all teachers accounts</p>
          </div>
          <button 
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-3 rounded-lg flex items-center justify-center sm:justify-start space-x-2 transition-colors w-full sm:w-auto"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Create Teacher</span>
          </button>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700">{{ error }}</span>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-sm font-medium text-gray-600 mb-2">Total Teachers</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-blue-600">{{ teachersCount }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-blue-100 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-sm font-medium text-gray-600 mb-2">Total Students</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-green-600">{{ studentsCount }}</h3>
              </div>
              <div class="p-2 sm:p-3 bg-green-100 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-sm font-medium text-gray-600 mb-2">Pending Approvals</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-yellow-600">12</h3>
              </div>
              <div class="p-2 sm:p-3 bg-yellow-100 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div class="min-w-0">
                <p class="text-sm font-medium text-gray-600 mb-2">Active Today</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-purple-600">45</h3>
              </div>
              <div class="p-2 sm:p-3 bg-purple-100 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
          <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
              <h3 class="text-lg font-semibold text-gray-800">Teachers</h3>
              <div class="relative w-full sm:w-64">
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search users..." 
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full"
                >
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">User</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Contact</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Education</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                  <td class="px-4 py-4">
                    <div class="flex items-center min-w-0">
                      <div :class="`w-10 h-10 rounded-full flex items-center justify-center ${getRoleColor(user.role)} flex-shrink-0`">
                        <span class="text-white text-sm font-semibold">
                          {{ getUserInitials(user.name) }}
                        </span>
                      </div>
                      <div class="ml-3 min-w-0 flex-1">
                        <div class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</div>
                        <div class="text-sm text-gray-500 truncate">@{{ user.username }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4">
                    <div class="text-sm text-gray-900 truncate">{{ user.email }}</div>
                    <div class="text-sm text-gray-500 truncate">DOB: {{ formatDate(user.dob) }}</div>
                  </td>
                  <td class="px-4 py-4">
                    <div class="text-sm text-gray-900 truncate">{{ user.education_qualification }}</div>
                    <div class="text-sm text-gray-500 truncate">{{ user.institute }}</div>
                  </td>
                  <td class="px-4 py-4">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      Active
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex items-center space-x-2">
                      <router-link 
                        v-if="user.role === 'teacher'"
                        :to="`/admin/teacher-portal/${user.id}`"
                        class="text-blue-600 hover:text-blue-900 text-sm"
                      >
                        View Portal
                      </router-link>
                      <button 
                        @click="editUser(user)"
                        class="text-blue-600 hover:text-blue-900 text-sm"
                      >
                        Edit
                      </button>
                      <button 
                        @click="deleteUser(user.id)"
                        class="text-red-600 hover:text-red-900 text-sm"
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="filteredUsers.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
            <p class="text-gray-500 mb-4">There are no other users in the system.</p>
          </div>
        </div>

        <!-- Create Teacher Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">{{ editingUser ? 'Edit User' : 'Create New Teacher' }}</h3>
            </div>

            <form @submit.prevent="editingUser ? updateUser() : createTeacher()" class="p-6 space-y-4">
              <!-- Personal Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                  <input 
                    v-model="newTeacher.name"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter full name"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                  <input 
                    v-model="newTeacher.username"
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
                    v-model="newTeacher.email"
                    type="email" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter email address"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                  <input 
                    v-model="newTeacher.dob"
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
                    v-model="newTeacher.education_qualification"
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
                    v-model="newTeacher.institute"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter institute name"
                  >
                </div>
              </div>
              <!-- Experience Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Experience *</label>
                  <select 
                    v-model="newTeacher.experience"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Select years of experience</option>
                    <option value="0-1 years">0-1 years</option>
                    <option value="1-3 years">1-3 years</option>
                    <option value="3-5 years">3-5 years</option>
                    <option value="5-7 years">5-7 years</option>
                    <option value="7-10 years">7-10 years</option>
                    <option value="10+ years">10+ years</option>
                    <option value="15+ years">15+ years</option>
                    <option value="20+ years">20+ years</option>
                  </select>
                </div>
              </div>

              <!-- Password (only show for create, optional for edit) -->
              <div v-if="!editingUser" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                  <input 
                    v-model="newTeacher.password"
                    type="password" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter password"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                  <input 
                    v-model="newTeacher.password_confirmation"
                    type="password" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Confirm password"
                  >
                </div>
              </div>

              <!-- Optional password update for editing -->
              <div v-if="editingUser" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">New Password (optional)</label>
                  <input 
                    v-model="newTeacher.password"
                    type="password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Leave blank to keep current"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                  <input 
                    v-model="newTeacher.password_confirmation"
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
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ loading ? (editingUser ? 'Updating...' : 'Creating...') : (editingUser ? 'Update User' : 'Create Teacher') }}
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
const otherUsers = ref([])
const error = ref('')
const editingUser = ref(null)

// Sidebar and navbar state
const activeMenu = ref('users')
const userMenuOpen = ref(false)
const user = ref(null)
const isDark = ref(false)

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}
const newTeacher = ref({
  name: '',
  username: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  experience: '', // Add this line
  password: '',
  password_confirmation: '',
  role: 'teacher'
})

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

// Computed properties
const teachersCount = computed(() => {
  return otherUsers.value.filter(user => user.role === 'teacher').length
})

const studentsCount = computed(() => {
  return otherUsers.value.filter(user => user.role === 'student').length
})

// Filter users based on search
const filteredUsers = computed(() => {
  if (!searchQuery.value) return otherUsers.value
  
  const query = searchQuery.value.toLowerCase()
  return otherUsers.value.filter(user => 
    user.name?.toLowerCase().includes(query) ||
    user.username?.toLowerCase().includes(query) ||
    user.email?.toLowerCase().includes(query) ||
    user.role?.toLowerCase().includes(query)
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

// View Teacher Portal
const viewTeacherPortal = (teacher) => {
  console.log('Viewing teacher portal for:', teacher)
  // Navigate to teacher portal with teacher ID
  router.push(`/admin/teacher-portal/${teacher.id}`)
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Fetch other users
const fetchOtherUsers = async () => {
  try {
    error.value = ''
    const token = localStorage.getItem('token')
    
    if (!token) {
      router.push('/login')
      return
    }

    console.log('Fetching other users...')
    const response = await apiClient.get('/users/other-users')
    
    console.log('Other Users API Response:', response.data)
    
    if (response.data.success) {
      otherUsers.value = response.data.data
      console.log('Other users fetched:', otherUsers.value)
    } else {
      error.value = response.data.message || 'Failed to fetch other users'
      console.error('API Error:', response.data)
    }
  } catch (err) {
    console.error('Error fetching other users:', err)
    if (err.response?.status === 401) {
      router.push('/login')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Failed to load other users. Please try again.'
    }
    
    // Fallback to mock data for development
    otherUsers.value = getMockOtherUsers()
  }
}

// Create new teacher
const createTeacher = async () => {
  if (newTeacher.value.password !== newTeacher.value.password_confirmation) {
    alert('Passwords do not match!')
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    console.log('🛡️ Getting CSRF token for create request...')
    await getCsrfToken()
    
    console.log('📡 Creating new teacher...')
    const response = await apiClient.post('/users/teachers', newTeacher.value)
    
    if (response.data.success) {
      otherUsers.value.push(response.data.user)
      closeModal()
      alert('Teacher created successfully!')
    } else {
      error.value = response.data.message || 'Failed to create teacher'
    }
  } catch (err) {
    console.error('💥 Error creating teacher:', err)
    if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = 'Validation errors: ' + errors.join(', ')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error creating teacher. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

// Edit user
const editUser = (user) => {
  editingUser.value = user
  newTeacher.value = {
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

// Update user
const updateUser = async () => {
  if (newTeacher.value.password && newTeacher.value.password !== newTeacher.value.password_confirmation) {
    alert('Passwords do not match!')
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    console.log('🛡️ Getting CSRF token for update request...')
    await getCsrfToken()
    
    console.log('📡 Updating user...', editingUser.value.id)
    const response = await apiClient.put(`/users/${editingUser.value.id}`, newTeacher.value)
    
    if (response.data.success) {
      // Update the user in the local list
      const index = otherUsers.value.findIndex(u => u.id === editingUser.value.id)
      if (index !== -1) {
        otherUsers.value[index] = { ...otherUsers.value[index], ...response.data.user }
      }
      closeModal()
      alert('User updated successfully!')
    } else {
      error.value = response.data.message || 'Failed to update user'
    }
  } catch (err) {
    console.error('💥 Error updating user:', err)
    if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = 'Validation errors: ' + errors.join(', ')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error updating user. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

// Delete user
const deleteUser = async (userId) => {
  if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
    return
  }

  try {
    console.log('🛡️ Getting CSRF token for delete request...')
    await getCsrfToken()
    
    console.log('🗑️ Deleting user...', userId)
    const response = await apiClient.delete(`/users/${userId}`)
    
    if (response.data.success) {
      // Remove the user from the local list
      otherUsers.value = otherUsers.value.filter(user => user.id !== userId)
      alert('User deleted successfully!')
    } else {
      error.value = response.data.message || 'Failed to delete user'
    }
  } catch (err) {
    console.error('💥 Error deleting user:', err)
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error deleting user. Please try again.'
    }
  }
}

// Close modal and reset form
const closeModal = () => {
  showCreateModal.value = false
  editingUser.value = null
  resetForm()
}

// Mock data for development
// Mock data for development
const getMockOtherUsers = () => {
  return [
    {
      id: 4,
      name: 'Teacher User',
      username: 'teacher',
      email: 'teacher@example.com',
      dob: '1990-01-01',
      education_qualification: 'BSC',
      institute: 'Test University',
      experience: '5 years in Mathematics', // Add this line
      role: 'teacher'
    },
    {
      id: 5,
      name: 'Student User',
      username: 'student',
      email: 'student@example.com',
      dob: '2000-01-01',
      education_qualification: 'HSC',
      institute: 'Test College',
      experience: '', // Add empty for student
      role: 'student'
    }
  ]
}

// Helper functions
const getUserInitials = (name) => {
  if (!name) return 'US'
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const getRoleColor = (role) => {
  const colors = {
    teacher: 'bg-purple-600',
    student: 'bg-indigo-600'
  }
  return colors[role] || 'bg-gray-600'
}

const getRoleBadgeColor = (role) => {
  const colors = {
    teacher: 'bg-purple-100 text-purple-800',
    student: 'bg-indigo-100 text-indigo-800'
  }
  return colors[role] || 'bg-gray-100 text-gray-800'
}

const resetForm = () => {
  newTeacher.value = {
    name: '',
    username: '',
    email: '',
    dob: '',
    education_qualification: '',
    institute: '',
    experience: '', // Add this line
    password: '',
    password_confirmation: '',
    role: 'teacher'
  }
}

// Initialize app when component mounts
onMounted(() => {
  console.log('Teachers Management component mounted')
  
  // Initialize authentication and sidebar functionality
  document.addEventListener('click', handleClickOutside)
  checkAuthentication()
  
  // Initialize CSRF token first
  initializeApp().then(() => {
    console.log('✅ App initialized successfully')
  }).catch(error => {
    console.error('❌ Failed to initialize app:', error)
  })
  
  // Fetch data
  fetchOtherUsers()
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

.overflow-x-auto {
  -webkit-overflow-scrolling: touch;
}

/* Improve table responsiveness */
@media (max-width: 768px) {
  .min-w-\[800px\] {
    min-width: 100%;
  }
}

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
</style>