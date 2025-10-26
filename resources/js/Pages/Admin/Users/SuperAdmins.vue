<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Super Admins Management" @search="handleSearch" />

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

        <!-- Flash Messages -->
        <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-green-700">{{ $page.props.flash.success }}</span>
          </div>
        </div>

        <div v-if="$page.props.flash.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700">{{ $page.props.flash.error }}</span>
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
                <h3 class="text-3xl font-bold text-green-600">{{ stats.activeToday || 0 }}</h3>
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
                <h3 class="text-3xl font-bold text-purple-600">{{ stats.last7Days || 0 }}</h3>
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
          <div v-if="filteredSuperAdmins.length === 0" class="text-center py-12">
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
                    v-model="form.name"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter full name"
                    :class="{ 'border-red-300': form.errors.name }"
                  >
                  <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                  <input 
                    v-model="form.username"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter username"
                    :class="{ 'border-red-300': form.errors.username }"
                  >
                  <div v-if="form.errors.username" class="text-red-500 text-sm mt-1">{{ form.errors.username }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                  <input 
                    v-model="form.email"
                    type="email" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter email address"
                    :class="{ 'border-red-300': form.errors.email }"
                  >
                  <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                  <input 
                    v-model="form.dob"
                    type="date" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-300': form.errors.dob }"
                  >
                  <div v-if="form.errors.dob" class="text-red-500 text-sm mt-1">{{ form.errors.dob }}</div>
                </div>
              </div>

              <!-- Education Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Education Qualification *</label>
                  <select 
                    v-model="form.education_qualification"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-300': form.errors.education_qualification }"
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
                  <div v-if="form.errors.education_qualification" class="text-red-500 text-sm mt-1">{{ form.errors.education_qualification }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Institute *</label>
                  <input 
                    v-model="form.institute"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter institute name"
                    :class="{ 'border-red-300': form.errors.institute }"
                  >
                  <div v-if="form.errors.institute" class="text-red-500 text-sm mt-1">{{ form.errors.institute }}</div>
                </div>
              </div>

              <!-- Password -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                  <input 
                    v-model="form.password"
                    type="password" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter password"
                    :class="{ 'border-red-300': form.errors.password }"
                  >
                  <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                  <input 
                    v-model="form.password_confirmation"
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
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="form.processing"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ form.processing ? 'Creating...' : 'Create Super Admin' }}
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
                    v-model="editForm.name"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter full name"
                    :class="{ 'border-red-300': editForm.errors.name }"
                  >
                  <div v-if="editForm.errors.name" class="text-red-500 text-sm mt-1">{{ editForm.errors.name }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                  <input 
                    v-model="editForm.username"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter username"
                    :class="{ 'border-red-300': editForm.errors.username }"
                  >
                  <div v-if="editForm.errors.username" class="text-red-500 text-sm mt-1">{{ editForm.errors.username }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                  <input 
                    v-model="editForm.email"
                    type="email" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter email address"
                    :class="{ 'border-red-300': editForm.errors.email }"
                  >
                  <div v-if="editForm.errors.email" class="text-red-500 text-sm mt-1">{{ editForm.errors.email }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                  <input 
                    v-model="editForm.dob"
                    type="date" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-300': editForm.errors.dob }"
                  >
                  <div v-if="editForm.errors.dob" class="text-red-500 text-sm mt-1">{{ editForm.errors.dob }}</div>
                </div>
              </div>

              <!-- Education Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Education Qualification *</label>
                  <select 
                    v-model="editForm.education_qualification"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-300': editForm.errors.education_qualification }"
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
                  <div v-if="editForm.errors.education_qualification" class="text-red-500 text-sm mt-1">{{ editForm.errors.education_qualification }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Institute *</label>
                  <input 
                    v-model="editForm.institute"
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter institute name"
                    :class="{ 'border-red-300': editForm.errors.institute }"
                  >
                  <div v-if="editForm.errors.institute" class="text-red-500 text-sm mt-1">{{ editForm.errors.institute }}</div>
                </div>
              </div>

              <!-- Password (Optional for edit) -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">New Password (Leave blank to keep current)</label>
                  <input 
                    v-model="editForm.password"
                    type="password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter new password"
                    :class="{ 'border-red-300': editForm.errors.password }"
                  >
                  <div v-if="editForm.errors.password" class="text-red-500 text-sm mt-1">{{ editForm.errors.password }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                  <input 
                    v-model="editForm.password_confirmation"
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
                  @click="closeEditModal"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="editForm.processing"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {{ editForm.processing ? 'Updating...' : 'Update Super Admin' }}
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
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Props from Laravel backend
const props = defineProps({
  superAdmins: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  }
})

// Reactive data
const showCreateModal = ref(false)
const showEditModal = ref(false)
const searchQuery = ref('')
const editingUser = ref(null)

// Forms
const form = useForm({
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

const editForm = useForm({
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

// Computed properties
const filteredSuperAdmins = computed(() => {
  if (!searchQuery.value) return props.superAdmins
  
  const query = searchQuery.value.toLowerCase()
  return props.superAdmins.filter(user => 
    user.name?.toLowerCase().includes(query) ||
    user.username?.toLowerCase().includes(query) ||
    user.email?.toLowerCase().includes(query)
  )
})

// Methods
const handleSearch = (query) => {
  searchQuery.value = query
}

const createSuperAdmin = () => {
  form.post('/admin/users/super-admins', {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
      form.reset()
    },
  })
}

const editSuperAdmin = (user) => {
  editingUser.value = user
  editForm.id = user.id
  editForm.name = user.name
  editForm.username = user.username
  editForm.email = user.email
  editForm.dob = user.dob ? user.dob.split('T')[0] : '' // Format date for input
  editForm.education_qualification = user.education_qualification
  editForm.institute = user.institute
  editForm.password = ''
  editForm.password_confirmation = ''
  showEditModal.value = true
}

const updateSuperAdmin = () => {
  if (!editingUser.value) return
  
  editForm.put(`/admin/users/super-admins/${editingUser.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      closeEditModal()
      editForm.reset()
    },
  })
}

const deleteSuperAdmin = (user) => {
  if (confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
    router.delete(`/admin/users/super-admins/${user.id}`, {
      preserveScroll: true,
    })
  }
}

const closeModal = () => {
  showCreateModal.value = false
  form.reset()
  form.clearErrors()
}

const closeEditModal = () => {
  showEditModal.value = false
  editingUser.value = null
  editForm.reset()
  editForm.clearErrors()
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