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
            <h1 class="text-2xl font-bold text-gray-900">Super Admins Management</h1>
            <p class="text-gray-600">Manage all super administrator accounts</p>
          </div>
          <button 
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Add Super Admin</span>
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
            <p class="text-gray-600">Loading super admins...</p>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Super Admins</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ superAdmins.length }}</h3>
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
                <h3 class="text-3xl font-bold text-green-600">3</h3>
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
                <h3 class="text-3xl font-bold text-purple-600">1</h3>
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
                <h3 class="text-3xl font-bold text-orange-600">Full</h3>
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
              <h3 class="text-lg font-semibold text-gray-800">Super Administrators</h3>
              <div class="relative">
                <input 
                  type="text" 
                  v-model="searchQuery"
                  placeholder="Search super admins..." 
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
                <tr v-for="user in filteredSuperAdmins" :key="user.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
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
                      @click="editSuperAdmin(user)"
                      class="text-blue-600 hover:text-blue-900 mr-3"
                    >
                      Edit
                    </button>
                    <button 
                      @click="deleteSuperAdmin(user)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="filteredSuperAdmins.length === 0 && !isLoading" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No super admins found</h3>
            <p class="text-gray-500 mb-4">Get started by creating your first super administrator.</p>
            <button 
              @click="showCreateModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
            >
              Add Super Admin
            </button>
          </div>
        </div>

        <!-- Create Super Admin Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Create New Super Admin</h3>
            </div>

            <form @submit.prevent="createSuperAdmin" class="p-6 space-y-4">
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

              <!-- Password -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

              <!-- Form Actions -->
              <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button 
                  type="button"
                  @click="showCreateModal = false"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="loading"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ loading ? 'Creating...' : 'Create Super Admin' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Edit Super Admin Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Edit Super Admin</h3>
            </div>

            <form @submit.prevent="updateSuperAdmin" class="p-6 space-y-4">
              <!-- Personal Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                  <input 
                    v-model="editUser.name"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter full name"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                  <input 
                    v-model="editUser.username"
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
                    v-model="editUser.email"
                    type="email" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter email address"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                  <input 
                    v-model="editUser.dob"
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
                    v-model="editUser.education_qualification"
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
                    v-model="editUser.institute"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter institute name"
                  >
                </div>
              </div>

              <!-- Password (Optional) -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">New Password (Leave blank to keep current)</label>
                  <input 
                    v-model="editUser.password"
                    type="password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter new password"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                  <input 
                    v-model="editUser.password_confirmation"
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
                  @click="showEditModal = false"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="editLoading"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ editLoading ? 'Updating...' : 'Update Super Admin' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Confirm Delete</h3>
            </div>
            <div class="p-6">
              <p class="text-gray-700 mb-4">Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>? This action cannot be undone.</p>
              <div class="flex justify-end space-x-3">
                <button 
                  @click="showDeleteModal = false"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  @click="confirmDelete"
                  :disabled="deleteLoading"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ deleteLoading ? 'Deleting...' : 'Delete' }}
                </button>
              </div>
            </div>
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

// State variables
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const searchQuery = ref('')
const loading = ref(false)
const editLoading = ref(false)
const deleteLoading = ref(false)
const superAdmins = ref([])
const error = ref('')
const isLoading = ref(true)

// Sidebar and navbar state
const activeMenu = ref('users')
const userMenuOpen = ref(false)
const user = ref(null)
const isDark = ref(false)

// User data
const newUser = ref({
  name: '',
  username: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  password: '',
  password_confirmation: '',
  role: 'super_admin'
})

const editUser = ref({
  id: null,
  name: '',
  username: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  password: '',
  password_confirmation: ''
})

const userToDelete = ref(null)

const editSuperAdmin = (user) => {
  editUser.value = {
    id: user.id,
    name: user.name,
    username: user.username,
    email: user.email,
    dob: user.dob,
    education_qualification: user.education_qualification,
    institute: user.institute,
    password: '',
    password_confirmation: ''
  }
  showEditModal.value = true
}
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
  // Implement search functionality here
}

// Update Super Admin
const updateSuperAdmin = async () => {
  if (editUser.value.password && editUser.value.password !== editUser.value.password_confirmation) {
    alert('Passwords do not match!')
    return
  }

  editLoading.value = true
  error.value = ''
  
  try {
    console.log('🛡️ Getting CSRF token for update request...')
    await getCsrfToken()
    
    console.log('📡 Updating super admin...')
    
    // Remove password fields if they are empty
    const updateData = { ...editUser.value }
    if (!updateData.password) {
      delete updateData.password
      delete updateData.password_confirmation
    }

    const response = await apiClient.put(`/users/${editUser.value.id}`, updateData)
    
    if (response.data.success) {
      // Update the user in the local list
      const index = superAdmins.value.findIndex(user => user.id === editUser.value.id)
      if (index !== -1) {
        superAdmins.value[index] = { ...superAdmins.value[index], ...response.data.user }
      }
      
      showEditModal.value = false
      resetEditForm()
      alert('Super admin updated successfully!')
    } else {
      error.value = response.data.message || 'Failed to update super admin'
    }
  } catch (err) {
    console.error('💥 Error updating super admin:', err)
    if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = 'Validation errors: ' + errors.join(', ')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error updating super admin. Please try again.'
    }
  } finally {
    editLoading.value = false
  }
}

// Delete Super Admin
const deleteSuperAdmin = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

// Confirm Delete
const confirmDelete = async () => {
  if (!userToDelete.value) return

  deleteLoading.value = true
  error.value = ''
  
  try {
    console.log('🛡️ Getting CSRF token for delete request...')
    await getCsrfToken()
    
    console.log('📡 Deleting super admin...')
    const response = await apiClient.delete(`/users/${userToDelete.value.id}`)
    
    if (response.data.success) {
      // Remove the user from the local list
      superAdmins.value = superAdmins.value.filter(user => user.id !== userToDelete.value.id)
      
      showDeleteModal.value = false
      userToDelete.value = null
      alert('Super admin deleted successfully!')
    } else {
      error.value = response.data.message || 'Failed to delete super admin'
    }
  } catch (err) {
    console.error('💥 Error deleting super admin:', err)
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error deleting super admin. Please try again.'
    }
  } finally {
    deleteLoading.value = false
  }
}
// Helper functions
const resetEditForm = () => {
  editUser.value = {
    id: null,
    name: '',
    username: '',
    email: '',
    dob: '',
    education_qualification: '',
    institute: '',
    password: '',
    password_confirmation: ''
  }
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

// Filter super admins based on search
const filteredSuperAdmins = computed(() => {
  if (!searchQuery.value) return superAdmins.value
  
  const query = searchQuery.value.toLowerCase()
  return superAdmins.value.filter(user => 
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

// Fetch super admins
const fetchSuperAdmins = async () => {
  try {
    error.value = ''
    isLoading.value = true
    
    console.log('🔍 Starting to fetch super admins...')
    
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
    
    console.log('📡 Making API request to /users/super-admins')
    const response = await apiClient.get('/users/super-admins')
    
    console.log('✅ API Response received:', response.status)
    console.log('📊 Response data:', response.data)
    
    if (response.data.success) {
      superAdmins.value = response.data.data || []
      console.log(`📊 Loaded ${superAdmins.value.length} super admins`)
    } else {
      error.value = response.data.message || 'Failed to fetch super admins'
      console.error('❌ API returned error:', response.data)
    }
  } catch (err) {
    console.error('💥 Error fetching super admins:', err);
    
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
        error.value = 'Access denied. You do not have permission to view super admins.';
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
      superAdmins.value = getMockSuperAdmins();
      error.value = 'Using mock data - ' + (error.value || 'API connection failed');
    }
  } finally {
    loading.value = false;
    isLoading.value = false;
  }
}

// Mock data function
const getMockSuperAdmins = () => {
  return [
    {
      id: 1,
      name: 'Super Admin',
      username: 'superadmin',
      email: 'superadmin@example.com',
      dob: '1990-01-01',
      education_qualification: 'MSC',
      institute: 'Test University',
      role: 'super_admin',
      created_at: '2024-01-01T00:00:00.000000Z'
    },
    {
      id: 2,
      name: 'System Administrator',
      username: 'sysadmin',
      email: 'sysadmin@example.com',
      dob: '1985-05-15',
      education_qualification: 'PhD',
      institute: 'Tech Institute',
      role: 'super_admin',
      created_at: '2024-01-02T00:00:00.000000Z'
    }
  ]
}

// Create new super admin
const createSuperAdmin = async () => {
  if (newUser.value.password !== newUser.value.password_confirmation) {
    alert('Passwords do not match!')
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    console.log('🛡️ Getting CSRF token for create request...')
    await getCsrfToken()
    
    console.log('📡 Creating new super admin...')
    const response = await apiClient.post('/users/super-admins', newUser.value)
    
    if (response.data.success) {
      superAdmins.value.push(response.data.user)
      showCreateModal.value = false
      resetForm()
      alert('Super admin created successfully!')
    } else {
      error.value = response.data.message || 'Failed to create super admin'
    }
  } catch (err) {
    console.error('💥 Error creating super admin:', err)
    if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = 'Validation errors: ' + errors.join(', ')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error creating super admin. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

// Helper functions
const getUserInitials = (name) => {
  if (!name) return 'SA'
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
    role: 'super_admin'
  }
}

// Initialize app when component mounts
onMounted(() => {
  console.log('🚀 SuperAdmins component mounted')
  
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
    fetchSuperAdmins()
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
</style>