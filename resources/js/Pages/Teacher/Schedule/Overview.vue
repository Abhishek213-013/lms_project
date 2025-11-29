<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
      <button 
        @click="toggleMobileMenu"
        class="p-2 bg-white rounded-lg shadow-md border border-gray-200"
      >
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Mobile Overlay -->
    <div 
      v-if="isMobileMenuOpen"
      @click="closeMobileMenu"
      class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
    ></div>

    <!-- Sidebar -->
    <TeacherSidebar 
      :is-mobile-menu-open="isMobileMenuOpen"
      @close-mobile="closeMobileMenu"
    />

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64 min-w-0 w-full transition-all duration-300">
      <!-- Page Content -->
      <div class="p-4 sm:p-6 max-w-full overflow-x-hidden">
        <!-- Header -->
        <div class="mb-4 sm:mb-6">
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Class Schedule</h1>
          <p class="text-gray-600 text-sm sm:text-base">Manage your teaching schedule across all classes</p>
        </div>

        <!-- Schedule View -->
        <div class="bg-white rounded-lg border border-gray-200 mb-4 sm:mb-6">
          <!-- Calendar Navigation -->
          <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
              <div class="flex items-center justify-center sm:justify-start space-x-2 sm:space-x-4">
                <button @click="previousWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 text-center sm:text-left">{{ currentWeekRange }}</h3>
                <button @click="nextWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
                <button @click="goToToday" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                  Today
                </button>
              </div>
              <div class="flex justify-center sm:justify-end">
                <select v-model="viewType" class="px-3 py-2 border border-gray-300 rounded-lg text-sm sm:text-base w-full sm:w-auto">
                  <option value="week">Week View</option>
                  <option value="month">Month View</option>
                  <option value="day">Day View</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Weekly Schedule -->
          <div class="p-4 sm:p-6 overflow-x-auto">
            <!-- Mobile Simplified View -->
            <div class="lg:hidden space-y-4">
              <div 
                v-for="day in weekDays" 
                :key="day.date.toString()"
                class="border border-gray-200 rounded-lg p-4"
              >
                <div class="flex items-center justify-between mb-3">
                  <div class="text-sm font-semibold text-gray-600">{{ day.name }}</div>
                  <div class="text-lg font-semibold" :class="isToday(day.date) ? 'text-blue-600' : 'text-gray-900'">
                    {{ day.date.getDate() }}
                  </div>
                </div>
                <div class="space-y-2">
                  <div 
                    v-for="schedule in getSchedulesForDay(day.date)" 
                    :key="schedule.id"
                    class="bg-blue-100 border border-blue-300 rounded p-3 cursor-pointer hover:bg-blue-200"
                    @click="viewSchedule(schedule)"
                  >
                    <div class="text-sm font-semibold text-blue-800">{{ schedule.title }}</div>
                    <div class="text-xs text-blue-600">{{ schedule.class_name }}</div>
                    <div class="text-xs text-blue-500 mt-1">{{ schedule.time }}</div>
                  </div>
                  <div v-if="getSchedulesForDay(day.date).length === 0" class="text-center text-gray-500 text-sm py-2">
                    No classes
                  </div>
                </div>
              </div>
            </div>

            <!-- Desktop Detailed View -->
            <div class="hidden lg:block">
              <div class="grid grid-cols-8 gap-4 min-w-[800px]">
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
        </div>

        <!-- Upcoming Classes -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-4 sm:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Upcoming Classes</h3>
          </div>
          <div class="p-4 sm:p-6">
            <div class="space-y-3 sm:space-y-4">
              <div 
                v-for="schedule in upcomingSchedules" 
                :key="schedule.id"
                class="flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 space-y-2 sm:space-y-0"
              >
                <div class="flex items-center space-x-3 sm:space-x-4">
                  <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="min-w-0 flex-1">
                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ schedule.title }}</h4>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">{{ schedule.class_name }}</p>
                    <p class="text-xs text-gray-500">{{ formatDateTime(schedule.date, schedule.time) }}</p>
                  </div>
                </div>
                <button 
                  @click="viewClass(schedule.class_id)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium self-end sm:self-auto"
                >
                  View Class
                </button>
              </div>
              
              <div v-if="upcomingSchedules.length === 0" class="text-center py-6 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm">No upcoming classes scheduled</p>
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
const isMobileMenuOpen = ref(false)

// Mobile menu functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

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
  const options = { month: 'short', day: 'numeric' }
  return `${start.toLocaleDateString('en-US', options)} - ${end.toLocaleDateString('en-US', options)}`
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
    .filter(schedule => {
      const scheduleDateTime = new Date(schedule.date + ' ' + schedule.time)
      return scheduleDateTime > now
    })
    .sort((a, b) => new Date(a.date + ' ' + a.time) - new Date(b.date + ' ' + b.time))
    .slice(0, 5)
})

// Methods
const fetchSchedules = async () => {
  try {
    // Mock data for demonstration
    schedules.value = [
      {
        id: 1,
        title: 'Mathematics Lecture',
        class_name: 'Advanced Mathematics',
        class_id: 1,
        date: getFormattedDate(new Date()),
        time: '10:00 AM'
      },
      {
        id: 2,
        title: 'Physics Lab',
        class_name: 'Physics 101',
        class_id: 2,
        date: getFormattedDate(new Date()),
        time: '2:00 PM'
      },
      {
        id: 3,
        title: 'Chemistry Discussion',
        class_name: 'Chemistry Basics',
        class_id: 3,
        date: getFormattedDate(new Date(Date.now() + 86400000)), // Tomorrow
        time: '9:00 AM'
      },
      {
        id: 4,
        title: 'Mathematics Quiz',
        class_name: 'Advanced Mathematics',
        class_id: 1,
        date: getFormattedDate(new Date(Date.now() + 86400000)),
        time: '11:00 AM'
      },
      {
        id: 5,
        title: 'Biology Lecture',
        class_name: 'Biology Fundamentals',
        class_id: 4,
        date: getFormattedDate(new Date(Date.now() + 2 * 86400000)),
        time: '1:00 PM'
      }
    ]
  } catch (error) {
    console.error('Error fetching schedules:', error)
  }
}

const getFormattedDate = (date) => {
  return date.toISOString().split('T')[0]
}

const getSchedulesForSlot = (date, timeSlot) => {
  const dayString = date.toDateString()
  return schedules.value.filter(schedule => {
    const scheduleDate = new Date(schedule.date).toDateString()
    return scheduleDate === dayString && schedule.time.startsWith(timeSlot)
  })
}

const getSchedulesForDay = (date) => {
  const dayString = date.toDateString()
  return schedules.value.filter(schedule => {
    const scheduleDate = new Date(schedule.date).toDateString()
    return scheduleDate === dayString
  }).sort((a, b) => {
    // Sort by time
    const timeA = convertTimeToMinutes(a.time)
    const timeB = convertTimeToMinutes(b.time)
    return timeA - timeB
  })
}

const convertTimeToMinutes = (timeString) => {
  const [time, modifier] = timeString.split(' ')
  let [hours, minutes] = time.split(':').map(Number)
  
  if (modifier === 'PM' && hours !== 12) hours += 12
  if (modifier === 'AM' && hours === 12) hours = 0
  
  return hours * 60 + minutes
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
  const options = { 
    weekday: 'short', 
    month: 'short', 
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  }
  return date.toLocaleDateString('en-US', options).replace(',', ' at')
}

// Handle escape key to close mobile menu
const handleEscape = (event) => {
  if (event.key === 'Escape' && isMobileMenuOpen.value) {
    closeMobileMenu()
  }
}

// Lifecycle
onMounted(() => {
  fetchSchedules()
  document.addEventListener('keydown', handleEscape)
})
</script>

<style scoped>
/* Use deep selector to override */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

/* Ensure smooth transitions */
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Z-index for proper layering */
.z-40 {
  z-index: 40;
}

.z-50 {
  z-index: 50;
}

/* Mobile-specific adjustments */
@media (max-width: 640px) {
  .custom-heading {
    font-size: 1.125rem;
  }
}

/* Custom scrollbar for schedule */
.overflow-x-auto {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}

.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>