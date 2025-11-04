<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 ml-64">
      <!-- Top Navbar -->
      <Navbar page-title="Subject Teachers" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ subject?.name }} - Teachers</h1>
            <p class="text-gray-600">Manage teacher assignments for this subject</p>
          </div>
          <div class="flex space-x-3">
            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
              <Link :href="`/admin/courses/class/${grade}/subjects`">← Back to Subjects</Link>
            </button>
            <button 
              @click="showAssignModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium"
            >
              Assign Teacher
            </button>
          </div>
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

        <!-- Current Teachers -->
        <div class="bg-white rounded-lg border border-gray-200 mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Assigned Teachers</h3>
          </div>
          <div class="p-6">
            <div v-if="assignedTeachers.length > 0" class="space-y-4">
              <div 
                v-for="teacher in assignedTeachers" 
                :key="teacher.id"
                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg"
              >
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold">{{ getInitials(teacher.name) }}</span>
                  </div>
                  <div>
                    <h4 class="font-medium text-gray-800">{{ teacher.name }}</h4>
                    <p class="text-sm text-gray-600">{{ teacher.email }}</p>
                    <p class="text-xs text-gray-500">Experience: {{ teacher.experience || 'Not specified' }} years</p>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                    Active
                  </span>
                  <button 
                    @click="unassignTeacher(teacher.id)"
                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                  >
                    Unassign
                  </button>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No teachers assigned</h3>
              <p class="text-gray-500 mb-4">Assign teachers to this subject to get started.</p>
            </div>
          </div>
        </div>

        <!-- Available Teachers -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Available Teachers</h3>
          </div>
          <div class="p-6">
            <div v-if="availableTeachers.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div 
                v-for="teacher in availableTeachers" 
                :key="teacher.id"
                class="border border-gray-200 rounded-lg p-4"
              >
                <div class="flex items-center space-x-3 mb-3">
                  <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-sm">{{ getInitials(teacher.name) }}</span>
                  </div>
                  <div>
                    <h4 class="font-medium text-gray-800">{{ teacher.name }}</h4>
                    <p class="text-xs text-gray-500">{{ teacher.qualification || 'No qualification specified' }}</p>
                  </div>
                </div>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Experience:</span>
                    <span class="font-medium">{{ teacher.experience || 'N/A' }} years</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium text-xs truncate">{{ teacher.email }}</span>
                  </div>
                </div>
                <button 
                  @click="assignTeacher(teacher.id)"
                  class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium"
                >
                  Assign to Subject
                </button>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <p class="text-gray-500">No available teachers found.</p>
            </div>
          </div>
        </div>

        <!-- Assign Teacher Modal -->
        <div v-if="showAssignModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800">Assign Teacher</h3>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                  <select 
                    v-model="selectedTeacher" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Choose a teacher</option>
                    <option 
                      v-for="teacher in availableTeachers" 
                      :key="teacher.id" 
                      :value="teacher.id"
                    >
                      {{ teacher.name }} - {{ teacher.qualification || 'No qualification' }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="flex justify-end space-x-3 mt-6">
                <button 
                  @click="showAssignModal = false" 
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button 
                  @click="confirmAssignTeacher"
                  :disabled="!selectedTeacher"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  Assign Teacher
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
import { ref, onMounted, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import apiClient from '../../../api/client.js'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

// Get props from Inertia
const props = defineProps({
  grade: {
    type: [String, Number],
    required: true
  },
  subjectId: {
    type: [String, Number],
    required: true
  }
})

// Component state
const subject = ref(null)
const assignedTeachers = ref([])
const availableTeachers = ref([])
const showAssignModal = ref(false)
const selectedTeacher = ref('')
const error = ref('')
const loading = ref(true)

// Authentication check
const checkAuthentication = () => {
  const token = localStorage.getItem('token')
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    router.visit('/login')
    return
  }
}

const handleSearch = (searchQuery) => {
  console.log('Search query:', searchQuery)
}

// Component functions
const fetchSubjectData = async () => {
  try {
    loading.value = true
    error.value = ''
    
    console.log('📡 Fetching subject teachers for subject ID:', props.subjectId)
    console.log('📡 Grade:', props.grade)
    
    // Use the correct API endpoint from your routes
    const response = await apiClient.get(`/courses/subject/${props.subjectId}/teachers`)
    
    console.log('✅ Subject Teachers API Response:', response.data)
    
    if (response.data.success) {
      subject.value = response.data.data.subject
      assignedTeachers.value = response.data.data.assignedTeachers || []
      availableTeachers.value = response.data.data.availableTeachers || []
      
      console.log(`📊 Loaded subject: ${subject.value.name}`)
      console.log(`📊 Assigned teachers: ${assignedTeachers.value.length}`)
      console.log(`📊 Available teachers: ${availableTeachers.value.length}`)
    } else {
      error.value = response.data.message || 'Failed to fetch subject data'
      console.error('❌ API returned error:', response.data)
    }
  } catch (err) {
    console.error('💥 Error fetching subject data:', err)
    
    if (err.response) {
      console.error('📡 Response error details:', {
        status: err.response.status,
        data: err.response.data
      })
      error.value = err.response.data?.message || `Server error: ${err.response.status}`
    } else if (err.request) {
      console.error('🌐 No response received')
      error.value = 'No response from server. Please check your connection.'
    } else {
      console.error('⚡ Request setup error:', err.message)
      error.value = 'Request failed: ' + err.message
    }
    
    // Fallback to mock data for development
    console.log('🔄 Using fallback mock data')
    subject.value = { 
      id: parseInt(props.subjectId), 
      name: 'Mathematics', 
      code: 'MATH' 
    }
    
    // Mock assigned teachers
    assignedTeachers.value = [
      { id: 1, name: 'Dr. Smith', email: 'smith@school.com', experience: 8 },
      { id: 2, name: 'Ms. Johnson', email: 'johnson@school.com', experience: 5 }
    ]
    
    // Mock available teachers
    availableTeachers.value = [
      { id: 3, name: 'Mr. Brown', email: 'brown@school.com', qualification: 'M.Sc. Mathematics', experience: 10 },
      { id: 4, name: 'Mrs. Davis', email: 'davis@school.com', qualification: 'B.Ed. Science', experience: 7 },
      { id: 5, name: 'Mr. Wilson', email: 'wilson@school.com', qualification: 'M.A. English', experience: 12 }
    ]
  } finally {
    loading.value = false
  }
}

const refreshParentData = () => {
  // Trigger localStorage event to refresh parent component
  localStorage.setItem('teacherAssignmentUpdate', JSON.stringify({
    subjectId: props.subjectId,
    grade: props.grade,
    timestamp: Date.now()
  }))
  
  // Dispatch custom event for real-time updates
  window.dispatchEvent(new CustomEvent('teacherAssigned', {
    detail: {
      subjectId: props.subjectId,
      grade: props.grade,
      timestamp: Date.now()
    }
  }))
}

const assignTeacher = async (teacherId) => {
  try {
    console.log(`📡 Assigning teacher ${teacherId} to subject ${props.subjectId}`)
    
    const response = await apiClient.post(`/courses/subject/${props.subjectId}/assign-teacher`, {
      teacher_id: teacherId
    })
    
    console.log('📡 Assign teacher response:', response.data)
    
    if (response.data.success) {
      console.log('✅ Teacher assigned successfully')
      
      // Update the local state with the response data
      if (response.data.data.assignedTeachers && response.data.data.assignedTeachers.length > 0) {
        assignedTeachers.value = response.data.data.assignedTeachers
        
        // Get the assigned teacher info for the event
        const assignedTeacher = response.data.data.assignedTeachers[0]
        
        // Trigger parent refresh with teacher info
        localStorage.setItem('teacherAssignmentUpdate', JSON.stringify({
          subjectId: props.subjectId,
          grade: props.grade,
          teacher: assignedTeacher,
          timestamp: Date.now()
        }))
        
        window.dispatchEvent(new CustomEvent('teacherAssigned', {
          detail: {
            subjectId: props.subjectId,
            grade: props.grade,
            teacher: assignedTeacher,
            timestamp: Date.now()
          }
        }))
      }
      
      // Update available teachers
      if (response.data.data.availableTeachers) {
        availableTeachers.value = response.data.data.availableTeachers
      }
      
      // Show success message
      alert('Teacher assigned successfully!')
    } else {
      alert('Failed to assign teacher: ' + response.data.message)
    }
  } catch (err) {
    console.error('💥 Error assigning teacher:', err)
    console.error('💥 Error details:', err.response?.data)
    alert('Error assigning teacher: ' + (err.response?.data?.message || err.message))
  }
}

const unassignTeacher = async (teacherId) => {
  try {
    console.log(`📡 Unassigning teacher ${teacherId} from subject ${props.subjectId}`)
    
    const response = await apiClient.delete(`/courses/subject/${props.subjectId}/teacher/${teacherId}`)
    
    if (response.data.success) {
      console.log('✅ Teacher unassigned successfully')
      
      // Find the teacher in assigned teachers
      const teacher = assignedTeachers.value.find(t => t.id === teacherId)
      if (teacher) {
        // Add back to available teachers
        availableTeachers.value.push(teacher)
        // Remove from assigned teachers
        assignedTeachers.value = assignedTeachers.value.filter(t => t.id !== teacherId)
        
        // Trigger parent refresh
        localStorage.setItem('teacherAssignmentUpdate', JSON.stringify({
          subjectId: props.subjectId,
          grade: props.grade,
          teacherId: teacherId,
          timestamp: Date.now()
        }))
        
        window.dispatchEvent(new CustomEvent('teacherUnassigned', {
          detail: {
            subjectId: props.subjectId,
            grade: props.grade,
            teacherId: teacherId,
            timestamp: Date.now()
          }
        }))
      }
      
      alert('Teacher unassigned successfully!')
    } else {
      alert('Failed to unassign teacher: ' + response.data.message)
    }
  } catch (err) {
    console.error('💥 Error unassigning teacher:', err)
    
    if (err.response?.status === 404) {
      error.value = 'API endpoint not found. Please check the server routes.'
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error unassigning teacher. Please try again.'
    }
    
    alert('Error unassigning teacher: ' + (err.response?.data?.message || err.message))
  }
}

const confirmAssignTeacher = () => {
  if (selectedTeacher.value) {
    assignTeacher(selectedTeacher.value)
    showAssignModal.value = false
    selectedTeacher.value = ''
  }
}

const getInitials = (name) => {
  if (!name) return '??'
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
}

onMounted(() => {
  console.log('🚀 SubjectTeachers component mounted')
  console.log('📊 Props:', { grade: props.grade, subjectId: props.subjectId })
  
  
  
  // Fetch component data
  fetchSubjectData()
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

/* Use deep selector to override */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}
</style>
