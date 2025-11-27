<template>
  <div class="min-h-screen bg-gray-50">
    <TeacherSidebar />
    
    <div class="ml-64 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Class Schedule</h1>
          <p class="text-gray-600">Manage your teaching schedule across all classes</p>
        </div>

        <!-- Schedule View -->
        <div class="bg-white rounded-lg border border-gray-200">
          <!-- Calendar Navigation -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <div class="flex items-center space-x-4">
                <button @click="previousWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                <h3 class="text-lg font-semibold text-gray-800">{{ currentWeekRange }}</h3>
                <button @click="nextWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                <button @click="goToToday" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                  Today
                </button>
              </div>
              <div class="flex space-x-3">
                <select v-model="viewType" class="px-3 py-2 border border-gray-300 rounded-lg">
                  <option value="week">Week View</option>
                  <option value="month">Month View</option>
                  <option value="day">Day View</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Weekly Schedule -->
          <div class="p-6">
            <div class="grid grid-cols-8 gap-4">
              <!-- Time Column -->
              <div class="col-span-1">
                <div class="h-16"></div>
                <div 
                  v-for="time in timeSlots" 
                  :key="time"
                  class="h-20 border-t border-gray-200 text-sm text-gray-500 pt-2"
                >
                  {{ time }}
                </div>
              </div>

              <!-- Days -->
              <div 
                v-for="day in weekDays" 
                :key="day.date.toString()"
                class="col-span-1"
              >
                <div class="h-16 text-center border-b border-gray-200">
                  <div class="text-sm font-semibold text-gray-600">{{ day.name }}</div>
                  <div class="text-lg font-semibold" :class="isToday(day.date) ? 'text-blue-600' : 'text-gray-900'">
                    {{ day.date.getDate() }}
                  </div>
                </div>
                <div 
                  v-for="time in timeSlots" 
                  :key="time"
                  class="h-20 border-t border-gray-200 p-1 relative"
                >
                  <div 
                    v-for="schedule in getSchedulesForSlot(day.date, time)" 
                    :key="schedule.id"
                    class="absolute inset-1 bg-blue-100 border border-blue-300 rounded p-2 cursor-pointer hover:bg-blue-200"
                    @click="viewSchedule(schedule)"
                  >
                    <div class="text-xs font-semibold text-blue-800">{{ schedule.title }}</div>
                    <div class="text-xs text-blue-600">{{ schedule.class_name }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Upcoming Classes -->
        <div class="mt-6 bg-white rounded-lg border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Upcoming Classes</h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div 
                v-for="schedule in upcomingSchedules" 
                :key="schedule.id"
                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
              >
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900">{{ schedule.title }}</h4>
                    <p class="text-sm text-gray-600">{{ schedule.class_name }}</p>
                    <p class="text-xs text-gray-500">{{ formatDateTime(schedule.date, schedule.time) }}</p>
                  </div>
                </div>
                <button 
                  @click="viewClass(schedule.class_id)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  View Class
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
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'

// State
const currentDate = ref(new Date())
const viewType = ref('week')
const schedules = ref([])

// Computed
const weekDays = computed(() => {
  const days = []
  const startOfWeek = new Date(currentDate.value)
  startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay())
  
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

const currentWeekRange = computed(() => {
  const start = weekDays.value[0].date
  const end = weekDays.value[6].date
  return `${start.toLocaleDateString()} - ${end.toLocaleDateString()}`
})

const timeSlots = computed(() => {
  return [
    '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM',
    '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM'
  ]
})

const upcomingSchedules = computed(() => {
  const now = new Date()
  return schedules.value
    .filter(schedule => new Date(schedule.date + ' ' + schedule.time) > now)
    .slice(0, 5)
})

// Methods
const fetchSchedules = async () => {
  try {
    const response = await fetch('/api/teacher/schedule')
    const result = await response.json()
    if (result.success) {
      schedules.value = result.data
    }
  } catch (error) {
    console.error('Error fetching schedules:', error)
  }
}

const getSchedulesForSlot = (date, timeSlot) => {
  const dayString = date.toDateString()
  return schedules.value.filter(schedule => {
    const scheduleDate = new Date(schedule.date).toDateString()
    return scheduleDate === dayString && schedule.time.startsWith(timeSlot)
  })
}

const viewSchedule = (schedule) => {
  router.visit(`/teacher/class/${schedule.class_id}/schedule`)
}

const viewClass = (classId) => {
  router.visit(`/teacher/class/${classId}`)
}

const previousWeek = () => {
  currentDate.value.setDate(currentDate.value.getDate() - 7)
  currentDate.value = new Date(currentDate.value)
}

const nextWeek = () => {
  currentDate.value.setDate(currentDate.value.getDate() + 7)
  currentDate.value = new Date(currentDate.value)
}

const goToToday = () => {
  currentDate.value = new Date()
}

const isToday = (date) => {
  return date.toDateString() === new Date().toDateString()
}

const formatDateTime = (dateString, timeString) => {
  const date = new Date(dateString)
  return `${date.toLocaleDateString()} at ${timeString}`
}

// Lifecycle
onMounted(() => {
  fetchSchedules()
})
</script>