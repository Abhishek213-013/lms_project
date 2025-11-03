<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Teachers Management" @search="handleSearch" />
      
      <!-- Page Content -->
      <div class="p-6 max-w-full overflow-x-hidden">
        <!-- Debug Section - Temporary -->
        <!-- <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
          <h3 class="text-lg font-semibold text-yellow-800 mb-2">Debug Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <strong>Teachers Count:</strong> {{ teachersCount }}<br>
              <strong>Students Count:</strong> {{ studentsCount }}<br>
              <strong>Filtered Users:</strong> {{ filteredUsers.length }}
            </div>
            <div>
              <strong>Status:</strong> {{ status }}<br>
              <strong>Form Errors:</strong> {{ form.errors }}
            </div>
            <div>
              <button @click="refreshData" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">
                Refresh Data
              </button>
              <button @click="debugForm" class="bg-blue-500 text-white px-3 py-1 rounded text-sm ml-2">
                Debug Form
              </button>
            </div>
          </div>
        </div> -->

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
        <div v-if="form.errors.message" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700">{{ form.errors.message }}</span>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-green-700">{{ status }}</span>
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
                      <a 
                        v-if="user.role === 'teacher'"
                        :href="`/admin/teacher-portal/${user.id}`"
                        class="text-blue-600 hover:text-blue-900 text-sm"
                      >
                        View Portal
                      </a>
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
              <h3 class="text-lg font-semibold text-gray-800">Create New Teacher</h3>
            </div>

            <form @submit.prevent="createTeacher" class="p-6 space-y-4">
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

              <!-- Experience Information -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Experience *</label>
                <select 
                  v-model="form.experience"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-300': form.errors.experience }"
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
                <div v-if="form.errors.experience" class="text-red-500 text-sm mt-1">{{ form.errors.experience }}</div>
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
                  {{ form.processing ? 'Creating...' : 'Create Teacher' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Edit Teacher Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Edit Teacher</h3>
            </div>

            <form @submit.prevent="updateTeacher" class="p-6 space-y-4">
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

              <!-- Experience Information -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Experience *</label>
                <select 
                  v-model="editForm.experience"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-300': editForm.errors.experience }"
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
                <div v-if="editForm.errors.experience" class="text-red-500 text-sm mt-1">{{ editForm.errors.experience }}</div>
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
                  {{ editForm.processing ? 'Updating...' : 'Update Teacher' }}
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
import { ref, computed, nextTick } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Props from Laravel backend
const props = defineProps({
  teachers: {
    type: Array,
    default: () => []
  },
  students: {
    type: Array,
    default: () => []
  },
  status: String
})

// Log props for debugging
console.log('Teachers Props:', props.teachers)
console.log('Students Props:', props.students)
console.log('Status Props:', props.status)

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
  experience: '',
  password: '',
  password_confirmation: '',
  role: 'teacher'
})

const editForm = useForm({
  id: null,
  name: '',
  username: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  experience: '',
  password: '',
  password_confirmation: ''
})

// Computed properties
const teachersCount = computed(() => props.teachers.length)
const studentsCount = computed(() => props.students.length)

const filteredUsers = computed(() => {
  if (!searchQuery.value) return [...props.teachers, ...props.students]
  
  const query = searchQuery.value.toLowerCase()
  return [...props.teachers, ...props.students].filter(user => 
    user.name?.toLowerCase().includes(query) ||
    user.username?.toLowerCase().includes(query) ||
    user.email?.toLowerCase().includes(query) ||
    user.role?.toLowerCase().includes(query)
  )
})

// Methods
const handleSearch = (query) => {
  searchQuery.value = query
}

const debugForm = () => {
  console.log('Form data:', form.data())
  console.log('Form processing:', form.processing)
  console.log('Form has errors:', form.hasErrors)
  console.log('Form errors:', form.errors)
  console.log('Teachers from props:', props.teachers)
}

const createTeacher = () => {
  console.log('Creating teacher with data:', form.data())
  
  form.post('/admin/users/teachers/', {
    preserveScroll: true,
    preserveState: false,
    onSuccess: (page) => {
      console.log('Teacher created successfully')
      console.log('Page data:', page)
      
      // Close modal and reset form
      showCreateModal.value = false
      form.reset()
      form.clearErrors()
      
      // Force reload to get updated data
      setTimeout(() => {
        router.reload({ 
          only: ['teachers', 'students', 'status'],
          preserveScroll: true 
        })
      }, 500)
    },
    onError: (errors) => {
      console.log('Form errors:', errors)
    },
    onFinish: () => {
      console.log('Form submission finished')
    }
  })
}

const editUser = (user) => {
  editingUser.value = user
  editForm.id = user.id
  editForm.name = user.name
  editForm.username = user.username
  editForm.email = user.email
  editForm.dob = user.dob ? user.dob.split('T')[0] : ''
  editForm.education_qualification = user.education_qualification
  editForm.institute = user.institute
  editForm.experience = user.experience
  editForm.password = ''
  editForm.password_confirmation = ''
  showEditModal.value = true
}

const updateTeacher = () => {
  if (!editingUser.value) return
  
  editForm.put(`/admin/users/teachers/${editingUser.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      closeEditModal()
      editForm.reset()
      // Refresh the data
      router.reload({ only: ['teachers'] })
    },
  })
}

const deleteUser = (userId) => {
  if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
    router.delete(`/admin/users/teachers/${userId}`, {
      preserveScroll: true,
      onSuccess: () => {
        // Refresh teachers list after deletion
        router.reload({ only: ['teachers'] })
      }
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

const refreshData = () => {
  console.log('Refreshing data...')
  router.reload({ 
    only: ['teachers', 'students', 'status'],
    preserveScroll: true 
  })
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
</script>