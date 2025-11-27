<template>
  <div class="min-h-screen bg-gray-50 flex" style="font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol' !important;">
    <!-- Mobile Menu Overlay -->
    <div 
      v-if="isMobileMenuOpen" 
      class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
      @click="closeMobileMenu"
    ></div>

    <!-- Sidebar -->
    <Sidebar 
      :is-mobile-menu-open="isMobileMenuOpen" 
      @menu-click="closeMobileMenu" 
    />

    <!-- Main Content -->
    <div class="flex-1 w-full lg:ml-64 transition-all duration-300">
      <!-- Top Navbar -->
      <Navbar 
        page-title="Announcement Management" 
        @search="handleSearch"
        @toggle-mobile-menu="toggleMobileMenu"
      />
      
      <!-- Page Content -->
      <div class="p-3 sm:p-4 lg:p-6">
        <!-- Success Message -->
        <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
          {{ $page.props.flash.success }}
        </div>

        <!-- Section 1: Create Announcement -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Create New Announcement</h2>
          
          <button 
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Create Announcement</span>
          </button>
        </div>

        <!-- Section 2: Existing Announcements -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Existing Announcements</h2>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="announcement in announcements" :key="announcement.id">
                  <td class="px-4 py-4 whitespace-nowrap">
                    <div v-if="announcement.image" class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center">
                      <img 
                        :src="`/storage/${announcement.image}`" 
                        :alt="announcement.title"
                        class="w-full h-full object-cover rounded-lg border border-gray-200"
                      >
                    </div>
                    <div v-else class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200">
                      <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                  </td>
                  <td class="px-4 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ announcement.title }}</div>
                    <div class="text-sm text-gray-500">{{ announcement.title_bn }}</div>
                    <div class="text-xs text-gray-400 mt-1 line-clamp-2">{{ truncateContent(announcement.content) }}</div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div>{{ formatDate(announcement.date) }}</div>
                    <div class="text-gray-500">{{ announcement.date_bn }}</div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <span 
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        announcement.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ announcement.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <button 
                      @click="editAnnouncement(announcement)"
                      class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors"
                    >
                      Edit
                    </button>
                    <button 
                      @click="toggleAnnouncementStatus(announcement)"
                      :class="[
                        'hover:text-gray-900 px-3 py-1 rounded-md transition-colors',
                        announcement.is_active 
                          ? 'text-yellow-600 bg-yellow-50 hover:bg-yellow-100' 
                          : 'text-green-600 bg-green-50 hover:bg-green-100'
                      ]"
                    >
                      {{ announcement.is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                    <button 
                      @click="deleteAnnouncement(announcement)"
                      class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
                
                <tr v-if="announcements.length === 0">
                  <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center space-y-2">
                      <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                      </svg>
                      <p>No announcements found.</p>
                      <button 
                        @click="showCreateModal = true"
                        class="text-blue-600 hover:text-blue-700 text-sm font-medium"
                      >
                        Create your first announcement
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">
              {{ showEditModal ? 'Edit Announcement' : 'Create New Announcement' }}
            </h3>
            <button 
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <form @submit.prevent="submitForm">
            <!-- Title -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Announcement Title *</label>
              <input
                v-model="form.title"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter announcement title"
              >
            </div>

            <!-- Title (Bengali) -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Announcement Title (Bengali)</label>
              <input
                v-model="form.title_bn"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="বাংলা শিরোনাম লিখুন"
              >
            </div>

            <!-- Image -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
              <input
                type="file"
                @change="handleImageUpload"
                accept="image/*"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <p class="text-xs text-gray-500 mt-1">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
              
              <!-- Image Preview -->
              <div v-if="form.imagePreview" class="mt-3">
                <p class="text-sm font-medium text-gray-700 mb-2">Image Preview:</p>
                <div class="relative inline-block">
                  <img :src="form.imagePreview" class="h-32 w-auto object-cover rounded-lg border border-gray-200">
                  <button 
                    type="button"
                    @click="removeImagePreview"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
              
              <!-- Current Image (for edit mode) -->
              <div v-if="showEditModal && currentAnnouncement?.image && !form.imagePreview" class="mt-3">
                <p class="text-sm font-medium text-gray-700 mb-2">Current Image:</p>
                <div class="relative inline-block">
                  <img :src="`/storage/${currentAnnouncement.image}`" class="h-32 w-auto object-cover rounded-lg border border-gray-200">
                  <button 
                    type="button"
                    @click="removeCurrentImage"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
              <textarea
                v-model="form.content"
                required
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter announcement content"
              ></textarea>
            </div>

            <!-- Content (Bengali) -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Content (Bengali)</label>
              <textarea
                v-model="form.content_bn"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="বাংলা কন্টেন্ট লিখুন"
              ></textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors disabled:opacity-50"
              >
                {{ form.processing ? 'Saving...' : (showEditModal ? 'Update' : 'Create') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import Navbar from '../../Layout/Navbar.vue'
import Sidebar from '../../Layout/Sidebar.vue'

// Props
const props = defineProps({
  announcements: {
    type: Array,
    default: () => []
  }
})

// Mobile menu state
const isMobileMenuOpen = ref(false)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const currentAnnouncement = ref(null)

// Form
const form = useForm({
  title: '',
  title_bn: '',
  content: '',
  content_bn: '',
  image: null,
  imagePreview: null,
  remove_image: false
})

// Mobile menu functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

// Handle search
const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Handle image upload
const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']
    if (!validTypes.includes(file.type)) {
      alert('Please select a valid image file (JPEG, PNG, JPG, GIF)')
      return
    }
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('Image size should be less than 2MB')
      return
    }
    
    form.image = file
    form.imagePreview = URL.createObjectURL(file)
    form.remove_image = false
  }
}

// Remove image preview
const removeImagePreview = () => {
  form.image = null
  form.imagePreview = null
  form.remove_image = false
}

// Remove current image (for edit mode)
const removeCurrentImage = () => {
  form.remove_image = true
  form.image = null
}

// Truncate content for table display
const truncateContent = (content, length = 50) => {
  if (!content) return ''
  return content.length > length ? content.substring(0, length) + '...' : content
}

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Submit form
const submitForm = () => {
  if (showEditModal.value && currentAnnouncement.value) {
    // For update, we need to use POST with _method=PUT
    const formData = new FormData()
    formData.append('_method', 'PUT')
    formData.append('title', form.title)
    formData.append('title_bn', form.title_bn)
    formData.append('content', form.content)
    formData.append('content_bn', form.content_bn)
    formData.append('remove_image', form.remove_image)
    
    if (form.image) {
      formData.append('image', form.image)
    }
    
    form.processing = true
    router.post(`/admin/announcements/${currentAnnouncement.value.id}`, formData, {
      onSuccess: () => {
        closeModal()
        form.reset()
      },
      onFinish: () => {
        form.processing = false
      }
    })
  } else {
    // For create, use regular POST with FormData
    const formData = new FormData()
    formData.append('title', form.title)
    formData.append('title_bn', form.title_bn)
    formData.append('content', form.content)
    formData.append('content_bn', form.content_bn)
    
    if (form.image) {
      formData.append('image', form.image)
    }
    
    form.processing = true
    router.post('/admin/announcements', formData, {
      onSuccess: () => {
        closeModal()
        form.reset()
      },
      onFinish: () => {
        form.processing = false
      }
    })
  }
}

// Edit announcement
const editAnnouncement = (announcement) => {
  currentAnnouncement.value = announcement
  form.title = announcement.title
  form.title_bn = announcement.title_bn || ''
  form.content = announcement.content
  form.content_bn = announcement.content_bn || ''
  form.remove_image = false
  showEditModal.value = true
}

// Delete announcement
const deleteAnnouncement = (announcement) => {
  if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
    router.delete(`/admin/announcements/${announcement.id}`)
  }
}

// Toggle announcement status
const toggleAnnouncementStatus = (announcement) => {
  const action = announcement.is_active ? 'deactivate' : 'activate'
  if (confirm(`Are you sure you want to ${action} this announcement?`)) {
    router.post(`/admin/announcements/${announcement.id}/toggle-status`)
  }
}

// Close modal
const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  currentAnnouncement.value = null
  form.reset()
  form.imagePreview = null
  form.remove_image = false
}
</script>

<style scoped>
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 300;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>