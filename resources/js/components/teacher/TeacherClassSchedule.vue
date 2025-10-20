<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Class Schedule - {{ classData.name }}</h1>
        <p class="text-gray-600">Manage your class schedule and timings</p>
      </div>
      <div class="flex space-x-3">
        <button 
          @click="$router.back()"
          class="text-blue-600 hover:text-blue-800 text-sm font-medium"
        >
          ← Back to Class
        </button>
        <button 
          @click="showCreateModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          Schedule Class
        </button>
      </div>
    </div>

    <!-- Schedule View Toggle -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
      <div class="flex space-x-4">
        <button 
          @click="currentView = 'weekly'"
          :class="`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
            currentView === 'weekly' 
              ? 'bg-blue-600 text-white' 
              : 'text-gray-600 hover:bg-gray-100'
          }`"
        >
          Weekly View
        </button>
        <button 
          @click="currentView = 'monthly'"
          :class="`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
            currentView === 'monthly' 
              ? 'bg-blue-600 text-white' 
              : 'text-gray-600 hover:bg-gray-100'
          }`"
        >
          Monthly View
        </button>
        <button 
          @click="currentView = 'list'"
          :class="`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
            currentView === 'list' 
              ? 'bg-blue-600 text-white' 
              : 'text-gray-600 hover:bg-gray-100'
          }`"
        >
          List View
        </button>
      </div>
    </div>

    <!-- Weekly Calendar View -->
    <div v-if="currentView === 'weekly'" class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <!-- Calendar Header -->
      <div class="grid grid-cols-8 border-b border-gray-200">
        <div class="p-4 border-r border-gray-200 font-semibold text-gray-700">Time</div>
        <div 
          v-for="day in weekDays" 
          :key="day.date"
          class="p-4 text-center border-r border-gray-200 last:border-r-0"
          :class="{
            'bg-blue-50': isToday(day.date),
            'font-semibold': isToday(day.date)
          }"
        >
          <div class="text-sm text-gray-600">{{ day.name }}</div>
          <div class="text-lg" :class="isToday(day.date) ? 'text-blue-600' : 'text-gray-900'">
            {{ day.date.getDate() }}
          </div>
        </div>
      </div>

      <!-- Time Slots -->
      <div class="divide-y divide-gray-200">
        <div 
          v-for="timeSlot in timeSlots" 
          :key="timeSlot"
          class="grid grid-cols-8"
        >
          <div class="p-4 border-r border-gray-200 text-sm text-gray-500 font-medium">
            {{ timeSlot }}
          </div>
          <div 
            v-for="day in weekDays" 
            :key="day.date"
            class="p-2 border-r border-gray-200 last:border-r-0 min-h-20 relative"
            @click="openScheduleModal(getSlotTime(day.date, timeSlot))"
          >
            <!-- Scheduled classes for this time slot -->
            <div 
              v-for="schedule in getSchedulesForSlot(day.date, timeSlot)" 
              :key="schedule.id"
              class="absolute inset-1 bg-blue-100 border border-blue-300 rounded-lg p-2 cursor-pointer hover:bg-blue-200 transition-colors"
              @click.stop="editSchedule(schedule)"
            >
              <div class="text-xs font-semibold text-blue-800">{{ schedule.title }}</div>
              <div class="text-xs text-blue-600">{{ schedule.time }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Monthly Calendar View -->
    <div v-if="currentView === 'monthly'" class="bg-white rounded-lg border border-gray-200">
      <div class="p-4 border-b border-gray-200 flex justify-between items-center">
        <button @click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <h3 class="text-lg font-semibold text-gray-800">{{ currentMonth }}</h3>
        <button @click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-7 border-b border-gray-200">
        <div 
          v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" 
          :key="day"
          class="p-3 text-center text-sm font-semibold text-gray-600"
        >
          {{ day }}
        </div>
      </div>

      <div class="grid grid-cols-7">
        <div 
          v-for="day in calendarDays" 
          :key="day.date"
          class="min-h-24 border-r border-b border-gray-200 p-2 last:border-r-0"
          :class="{
            'bg-gray-50 text-gray-400': !day.isCurrentMonth,
            'bg-blue-50': day.isToday
          }"
        >
          <div class="flex justify-between items-start mb-1">
            <span 
              class="text-sm font-medium"
              :class="day.isToday ? 'text-blue-600' : 'text-gray-900'"
            >
              {{ day.date.getDate() }}
            </span>
            <button 
              @click="openScheduleModal(day.date)"
              class="w-5 h-5 text-gray-400 hover:text-blue-600 rounded-full hover:bg-blue-100 flex items-center justify-center"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
            </button>
          </div>
          
          <!-- Scheduled classes for this day -->
          <div class="space-y-1">
            <div 
              v-for="schedule in getSchedulesForDay(day.date)" 
              :key="schedule.id"
              class="text-xs bg-blue-100 text-blue-800 rounded px-1 py-0.5 cursor-pointer hover:bg-blue-200"
              @click="editSchedule(schedule)"
            >
              {{ schedule.time }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div v-if="currentView === 'list'" class="bg-white rounded-lg border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">All Scheduled Classes</h3>
      </div>
      
      <div class="divide-y divide-gray-200">
        <div 
          v-for="schedule in schedules" 
          :key="schedule.id"
          class="p-6 hover:bg-gray-50 transition-colors"
        >
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-2">
                <h4 class="text-lg font-semibold text-gray-900">{{ schedule.title }}</h4>
                <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getScheduleTypeColor(schedule.type)}`">
                  {{ schedule.type }}
                </span>
                <span :class="`px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(schedule.status)}`">
                  {{ schedule.status }}
                </span>
              </div>
              
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mb-3">
                <div class="flex items-center space-x-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span>{{ formatDate(schedule.date) }}</span>
                </div>
                <div class="flex items-center space-x-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>{{ schedule.time }}</span>
                </div>
                <div class="flex items-center space-x-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>Duration: {{ schedule.duration }}</span>
                </div>
                <div class="flex items-center space-x-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                  <span>{{ schedule.students_attending }}/{{ classData.studentCount }} students</span>
                </div>
              </div>
              
              <p class="text-gray-600" v-if="schedule.description">{{ schedule.description }}</p>
              
              <!-- Recurrence info -->
              <div v-if="schedule.recurring" class="mt-2">
                <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  Repeats {{ schedule.recurrence_pattern }}
                </span>
              </div>
            </div>
            
            <div class="flex space-x-2">
              <button 
                @click="editSchedule(schedule)"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
              >
                Edit
              </button>
              <button 
                @click="deleteSchedule(schedule.id)"
                class="text-red-600 hover:text-red-800 text-sm font-medium"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
        
        <div v-if="schedules.length === 0" class="p-12 text-center text-gray-500">
          <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <p class="text-lg font-medium mb-2">No scheduled classes yet</p>
          <p class="text-sm">Schedule your first class to get started</p>
        </div>
      </div>
    </div>

    <!-- Create/Edit Schedule Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ showEditModal ? 'Edit Schedule' : 'Schedule New Class' }}
          </h3>
        </div>

        <form @submit.prevent="saveSchedule" class="p-6 space-y-4">
          <!-- Class Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Class Title *</label>
            <input 
              v-model="scheduleForm.title"
              type="text" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Enter class title"
            >
          </div>

          <!-- Date and Time -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
              <input 
                v-model="scheduleForm.date"
                type="date" 
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Time *</label>
              <input 
                v-model="scheduleForm.time"
                type="time" 
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
          </div>

          <!-- Duration -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Duration *</label>
            <select 
              v-model="scheduleForm.duration"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Select duration</option>
              <option value="30 min">30 minutes</option>
              <option value="45 min">45 minutes</option>
              <option value="1 hour">1 hour</option>
              <option value="1.5 hours">1.5 hours</option>
              <option value="2 hours">2 hours</option>
            </select>
          </div>

          <!-- Class Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Class Type</label>
            <select 
              v-model="scheduleForm.type"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="regular">Regular Class</option>
              <option value="extra">Extra Class</option>
              <option value="revision">Revision</option>
              <option value="test">Test/Exam</option>
            </select>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea 
              v-model="scheduleForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Add any additional details about this class..."
            ></textarea>
          </div>

          <!-- Recurring Schedule -->
          <div>
            <label class="flex items-center space-x-2">
              <input 
                v-model="scheduleForm.recurring"
                type="checkbox" 
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              >
              <span class="text-sm font-medium text-gray-700">Recurring schedule</span>
            </label>
            
            <div v-if="scheduleForm.recurring" class="mt-3 grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Repeat Every</label>
                <select 
                  v-model="scheduleForm.recurrence_pattern"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="weekly">Week</option>
                  <option value="biweekly">2 Weeks</option>
                  <option value="monthly">Month</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input 
                  v-model="scheduleForm.recurrence_end_date"
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
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
              :disabled="saving"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
              {{ saving ? 'Saving...' : (showEditModal ? 'Update Schedule' : 'Create Schedule') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import apiClient from '../../api/client'

const route = useRoute()
const router = useRouter()

const classData = ref({
  id: route.params.classId,
  name: 'Loading...',
  studentCount: 0
})

const schedules = ref([])
const currentView = ref('weekly')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const saving = ref(false)
const loading = ref(true)
const currentDate = ref(new Date())

const scheduleForm = ref({
  id: null,
  title: '',
  date: '',
  time: '',
  duration: '1 hour',
  type: 'regular',
  description: '',
  recurring: false,
  recurrence_pattern: 'weekly',
  recurrence_end_date: '',
  status: 'scheduled'
})

// Fetch schedules from API
const fetchSchedules = async () => {
  try {
    loading.value = true
    const response = await apiClient.get(`/schedules/class/${classData.value.id}`)
    
    if (response.data.success) {
      schedules.value = response.data.data
    } else {
      console.error('Failed to fetch schedules:', response.data.message)
    }
  } catch (error) {
    console.error('Error fetching schedules:', error)
  } finally {
    loading.value = false
  }
}

// Fetch class data
const fetchClassData = async () => {
  try {
    const response = await apiClient.get(`/courses/${route.params.classId}`)
    if (response.data.success) {
      classData.value = {
        id: response.data.data.id,
        name: response.data.data.name,
        studentCount: response.data.data.studentCount || 0
      }
    }
  } catch (error) {
    console.error('Error fetching class data:', error)
  }
}

// Save schedule to API
const saveSchedule = async () => {
  saving.value = true
  try {
    const formData = {
      title: scheduleForm.value.title,
      description: scheduleForm.value.description,
      date: scheduleForm.value.date,
      time: scheduleForm.value.time,
      duration: scheduleForm.value.duration,
      type: scheduleForm.value.type,
      recurring: scheduleForm.value.recurring,
      recurrence_pattern: scheduleForm.value.recurring ? scheduleForm.value.recurrence_pattern : null,
      recurrence_end_date: scheduleForm.value.recurring ? scheduleForm.value.recurrence_end_date : null
    }

    let response
    if (showEditModal.value) {
      response = await apiClient.put(`/schedules/${scheduleForm.value.id}`, formData)
    } else {
      response = await apiClient.post(`/schedules/class/${classData.value.id}`, formData)
    }

    if (response.data.success) {
      await fetchSchedules() // Refresh the list
      closeModal()
    } else {
      alert('Failed to save schedule: ' + response.data.message)
    }
  } catch (error) {
    console.error('Error saving schedule:', error)
    alert('Error saving schedule. Please try again.')
  } finally {
    saving.value = false
  }
}

// Delete schedule from API
const deleteSchedule = async (scheduleId) => {
  if (confirm('Are you sure you want to delete this schedule?')) {
    try {
      const response = await apiClient.delete(`/schedules/${scheduleId}`)
      if (response.data.success) {
        await fetchSchedules() // Refresh the list
      } else {
        alert('Failed to delete schedule: ' + response.data.message)
      }
    } catch (error) {
      console.error('Error deleting schedule:', error)
      alert('Error deleting schedule. Please try again.')
    }
  }
}

// Edit schedule
const editSchedule = (schedule) => {
  scheduleForm.value = { ...schedule }
  showEditModal.value = true
}

// Open schedule modal with default date/time
const openScheduleModal = (date) => {
  const defaultDate = date.toISOString().split('T')[0]
  const defaultTime = date.toTimeString().slice(0, 5)
  
  scheduleForm.value.date = defaultDate
  scheduleForm.value.time = defaultTime
  showCreateModal.value = true
}

// Close modal and reset form
const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  resetForm()
}

// Reset form
const resetForm = () => {
  scheduleForm.value = {
    id: null,
    title: '',
    date: '',
    time: '',
    duration: '1 hour',
    type: 'regular',
    description: '',
    recurring: false,
    recurrence_pattern: 'weekly',
    recurrence_end_date: '',
    status: 'scheduled'
  }
}

// Calendar data and methods (keep your existing calendar logic)
const weekDays = computed(() => {
  const days = []
  const today = new Date(currentDate.value)
  const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()))
  
  for (let i = 0; i < 7; i++) {
    const date = new Date(startOfWeek)
    date.setDate(startOfWeek.getDate() + i)
    days.push({
      name: date.toLocaleDateString('en-US', { weekday: 'short' }),
      date: date
    })
  }
  return days
})

const timeSlots = computed(() => {
  const slots = []
  for (let hour = 8; hour <= 17; hour++) {
    slots.push(`${hour}:00 - ${hour + 1}:00`)
  }
  return slots
})

const calendarDays = computed(() => {
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const days = []
  
  // Add previous month's days
  const prevMonthLastDay = new Date(year, month, 0).getDate()
  const firstDayOfWeek = firstDay.getDay()
  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const date = new Date(year, month - 1, prevMonthLastDay - i)
    days.push({ date, isCurrentMonth: false, isToday: false })
  }
  
  // Add current month's days
  const today = new Date()
  for (let day = 1; day <= lastDay.getDate(); day++) {
    const date = new Date(year, month, day)
    days.push({ 
      date, 
      isCurrentMonth: true, 
      isToday: date.toDateString() === today.toDateString()
    })
  }
  
  // Add next month's days
  const totalCells = 42 // 6 weeks
  const nextMonthDays = totalCells - days.length
  for (let day = 1; day <= nextMonthDays; day++) {
    const date = new Date(year, month + 1, day)
    days.push({ date, isCurrentMonth: false, isToday: false })
  }
  
  return days
})

const currentMonth = computed(() => {
  return currentDate.value.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long' 
  })
})

// Helper methods (keep your existing helper methods)
const getScheduleTypeColor = (type) => {
  const colors = {
    regular: 'bg-blue-100 text-blue-800',
    extra: 'bg-green-100 text-green-800',
    revision: 'bg-purple-100 text-purple-800',
    test: 'bg-orange-100 text-orange-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusColor = (status) => {
  const colors = {
    scheduled: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    completed: 'bg-blue-100 text-blue-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const isToday = (date) => {
  const today = new Date()
  return date.toDateString() === today.toDateString()
}

const getSchedulesForSlot = (date, timeSlot) => {
  return schedules.value.filter(schedule => {
    const scheduleDate = new Date(schedule.date)
    return scheduleDate.toDateString() === date.toDateString() && 
           schedule.time.startsWith(timeSlot.split(' - ')[0])
  })
}

const getSchedulesForDay = (date) => {
  return schedules.value.filter(schedule => {
    const scheduleDate = new Date(schedule.date)
    return scheduleDate.toDateString() === date.toDateString()
  })
}

const getSlotTime = (date, timeSlot) => {
  const [startTime] = timeSlot.split(' - ')
  const [hours, minutes] = startTime.split(':')
  const slotDate = new Date(date)
  slotDate.setHours(parseInt(hours), parseInt(minutes))
  return slotDate
}

// Calendar navigation
const previousMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
}

// Initialize
onMounted(async () => {
  await fetchClassData()
  await fetchSchedules()
})
</script>