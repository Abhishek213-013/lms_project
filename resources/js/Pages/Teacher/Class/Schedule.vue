<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <TeacherSidebar 
      @showUploadModal="showUploadModal = true"
      @createAssignment="createAssignment"
      @goBackToAdmin="goBackToAdmin"
    />

    <!-- Main Content -->
    <div class="flex-1 ml-64 min-w-0">
      <!-- Top Navbar -->
      <nav class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center min-w-0">
              <h1 class="custom-heading truncate">Class Schedule - {{ classInfo?.name || 'Loading...' }}</h1>
            </div>
            
            <div class="flex items-center space-x-4 flex-shrink-0">
              <!-- Search -->
              <div class="relative hidden md:block">
                <input 
                  type="text" 
                  placeholder="Search..." 
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64"
                >
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              

              <!-- User Menu -->
              <div class="relative flex-shrink-0">
                <button 
                  @click="toggleUserMenu"
                  class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 min-w-0"
                >
                  <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                    <img 
                      v-if="effectiveTeacher.profile_picture_url" 
                      :src="effectiveTeacher.profile_picture_url" 
                      :alt="effectiveTeacher.name"
                      class="w-full h-full object-cover"
                    >
                    <span v-else class="text-white text-sm font-semibold">{{ userInitials }}</span>
                  </div>
                  <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>

                <!-- User Dropdown -->
                <div v-show="userMenuOpen" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-20">
                  <!-- User Info in Dropdown Header -->
                  <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center overflow-hidden">
                        <img 
                          v-if="effectiveTeacher.profile_picture_url" 
                          :src="effectiveTeacher.profile_picture_url" 
                          :alt="effectiveTeacher.name"
                          class="w-full h-full object-cover"
                        >
                        <span v-else class="text-white text-sm font-semibold">{{ userInitials }}</span>
                      </div>
                      <div class="text-left min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">{{ effectiveTeacher.name || 'Teacher' }}</p>
                        <p class="text-xs text-gray-500 capitalize truncate">Teacher</p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Dropdown Menu Items -->
                  <a href="#" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center" @click="editProfile">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                  </a>
                  <a href="#" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center" @click="navigateToSettings">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                  </a>
                  <div class="border-t border-gray-200 my-1"></div>
                  <button 
                    @click="logout"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Sign out
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>


      <!-- Page Content -->
      <div class="p-6 max-w-full overflow-x-hidden">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Class Schedule - {{ classInfo?.name || 'Class' }}</h1>
            <p class="text-gray-600">Manage your class schedule and timings</p>
          </div>
          <div class="flex space-x-3">
            <Link 
              :href="`/teacher/class/${classId}`"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium no-underline"
            >
              ← Back to Class
            </Link>
            
            <!-- Refresh Button -->
            <button 
              @click="fetchSchedules"
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors no-underline"
              title="Refresh Schedules"
            >
              🔄 Refresh
            </button>
            
            <button 
              @click="showCreateModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors no-underline"
            >
              Schedule Class
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Schedules</p>
                <p class="text-2xl font-semibold text-gray-900">{{ scheduleStats.total_schedules }}</p>
              </div>
            </div>
          </div>
        
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Upcoming</p>
                <p class="text-2xl font-semibold text-gray-900">{{ scheduleStats.upcoming_schedules }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Completed</p>
                <p class="text-2xl font-semibold text-gray-900">{{ scheduleStats.completed_schedules }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                <p class="text-2xl font-semibold text-gray-900">{{ scheduleStats.completion_rate }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Schedule View Toggle -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
          <div class="flex space-x-4">
            <button 
              @click="currentView = 'weekly'"
              :class="`px-4 py-2 rounded-lg text-sm font-medium transition-colors no-underline ${
                currentView === 'weekly' 
                  ? 'bg-blue-600 text-white' 
                  : 'text-gray-600 hover:bg-gray-100'
              }`"
            >
              Weekly View
            </button>
            <button 
              @click="currentView = 'monthly'"
              :class="`px-4 py-2 rounded-lg text-sm font-medium transition-colors no-underline ${
                currentView === 'monthly' 
                  ? 'bg-blue-600 text-white' 
                  : 'text-gray-600 hover:bg-gray-100'
              }`"
            >
              Monthly View
            </button>
            <button 
              @click="currentView = 'list'"
              :class="`px-4 py-2 rounded-lg text-sm font-medium transition-colors no-underline ${
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
              :key="day.date.toString()"
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
                :key="day.date.toString()"
                class="p-2 border-r border-gray-200 last:border-r-0 min-h-20 relative"
                @click="openScheduleModal(getSlotTime(day.date, timeSlot))"
              >
                <!-- Scheduled classes for this time slot -->
                <div 
                  v-for="schedule in getSchedulesForSlot(day.date, timeSlot)" 
                  :key="schedule.id"
                  class="absolute inset-1 border rounded-lg p-2 cursor-pointer transition-colors"
                  :class="getScheduleTypeClasses(schedule.type)"
                  @click.stop="editSchedule(schedule)"
                >
                  <div class="text-xs font-semibold">{{ schedule.title }}</div>
                  <div class="text-xs">{{ schedule.start_time }} - {{ schedule.end_time }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Monthly Calendar View -->
        <div v-if="currentView === 'monthly'" class="bg-white rounded-lg border border-gray-200">
          <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <button @click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg no-underline">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <h3 class="text-lg font-semibold text-gray-800">{{ currentMonth }}</h3>
            <button @click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg no-underline">
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
              :key="day.date.toString()"
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
                  class="w-5 h-5 text-gray-400 hover:text-blue-600 rounded-full hover:bg-blue-100 flex items-center justify-center no-underline"
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
                  class="text-xs rounded px-1 py-0.5 cursor-pointer"
                  :class="getScheduleTypeClasses(schedule.type)"
                  @click="editSchedule(schedule)"
                >
                  {{ schedule.start_time }}
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
                      {{ schedule.type_label }}
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
                      <span>{{ schedule.start_time }} - {{ schedule.end_time }}</span>
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
                      <span>{{ schedule.students_attending }}/{{ classInfo?.student_count || 0 }} students</span>
                    </div>
                  </div>
                  
                  <p class="text-gray-600" v-if="schedule.description">{{ schedule.description }}</p>
                  
                  <!-- Location -->
                  <div v-if="schedule.location" class="mt-2">
                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      {{ schedule.location }}
                    </span>
                  </div>
                  
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
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium no-underline"
                  >
                    Edit
                  </button>
                  <button 
                    @click="deleteSchedule(schedule.id)"
                    class="text-red-600 hover:text-red-800 text-sm font-medium no-underline"
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
                  <option value="30 minutes">30 minutes</option>
                  <option value="45 minutes">45 minutes</option>
                  <option value="60 minutes">1 hour</option>
                  <option value="90 minutes">1.5 hours</option>
                  <option value="120 minutes">2 hours</option>
                </select>
              </div>

              <!-- Class Type -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Class Type</label>
                <select 
                  v-model="scheduleForm.type"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option v-for="type in scheduleTypes" :key="type.value" :value="type.value">
                    {{ type.label }}
                  </option>
                </select>
              </div>

              <!-- Location -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input 
                  v-model="scheduleForm.location"
                  type="text" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Optional location (e.g., Room 101)"
                >
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
                      <option v-for="pattern in recurrencePatterns" :key="pattern.value" :value="pattern.value">
                        {{ pattern.label }}
                      </option>
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
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors no-underline"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  :disabled="saving"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed no-underline"
                >
                  {{ saving ? 'Saving...' : (showEditModal ? 'Update Schedule' : 'Create Schedule') }}
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
import { ref, computed, onMounted } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import TeacherSidebar from '../../Layout/TeacherSidebar.vue'

// Get props from Laravel
const props = defineProps({
  classId: String,
  classData: Object,
  teacher: Object,
  initialData: Object,
  auth: Object
})

// DEBUG: Comprehensive logging
console.log('🔍 === SCHEDULE COMPONENT DEBUG ===')
console.log('📦 All props:', props)
console.log('👨‍🏫 Teacher prop:', props.teacher)
console.log('🔐 Auth user:', props.auth?.user)
console.log('🏫 Class data:', props.classData)
console.log('📊 Initial data:', props.initialData)
console.log('🆔 Class ID:', props.classId)

// Check if teacher data exists in different locations
console.log('🔎 Teacher data sources:')
console.log('   - From teacher prop:', props.teacher ? '✅ YES' : '❌ NO')
console.log('   - From auth user:', props.auth?.user ? '✅ YES' : '❌ NO')
console.log('   - From classData teacher:', props.classData?.teacher ? '✅ YES' : '❌ NO')

if (props.teacher) {
  console.log('📸 Teacher profile picture details:')
  console.log('   - profile_picture:', props.teacher.profile_picture)
  console.log('   - profile_picture_url:', props.teacher.profile_picture_url)
  console.log('   - name:', props.teacher.name)
  console.log('   - id:', props.teacher.id)
}

console.log('🔍 === END DEBUG ===')

// FALLBACK APPROACH: Use multiple sources for teacher data
const effectiveTeacher = computed(() => {
  console.log('🔄 Computing effectiveTeacher...')
  
  // Priority 1: Use teacher prop from Laravel
  if (props.teacher && Object.keys(props.teacher).length > 0) {
    console.log('🎯 Using teacher from props')
    const teacher = { ...props.teacher }
    // Ensure profile_picture_url is set
    if (teacher.profile_picture && !teacher.profile_picture_url) {
      teacher.profile_picture_url = `/storage/${teacher.profile_picture}`
      console.log('🖼️ Generated profile_picture_url:', teacher.profile_picture_url)
    }
    console.log('✅ Final teacher data:', teacher)
    return teacher
  }
  
  // Priority 2: Use auth user (fallback)
  if (props.auth?.user) {
    console.log('🔐 Using teacher from auth user')
    const teacher = { ...props.auth.user }
    // Ensure profile_picture_url is set
    if (teacher.profile_picture && !teacher.profile_picture_url) {
      teacher.profile_picture_url = `/storage/${teacher.profile_picture}`
      console.log('🖼️ Generated profile_picture_url from auth:', teacher.profile_picture_url)
    }
    console.log('✅ Final teacher data from auth:', teacher)
    return teacher
  }
  
  // Priority 3: Try to get from classData
  if (props.classData?.teacher) {
    console.log('🏫 Using teacher from classData')
    const teacher = { ...props.classData.teacher }
    if (teacher.profile_picture && !teacher.profile_picture_url) {
      teacher.profile_picture_url = `/storage/${teacher.profile_picture}`
      console.log('🖼️ Generated profile_picture_url from classData:', teacher.profile_picture_url)
    }
    return teacher
  }
  
  // Priority 4: Default fallback
  console.log('⚠️ Using default teacher data - no teacher data found!')
  return {
    name: 'Teacher',
    email: '',
    profile_picture_url: null,
    profile_picture: null
  }
})

// User initials for profile picture fallback
const userInitials = computed(() => {
  const teacherName = effectiveTeacher.value?.name
  console.log('💫 User initials computed, teacher name:', teacherName)
  
  if (!teacherName) {
    console.log('❌ No teacher name found, using default "T"')
    return 'T'
  }
  
  const initials = teacherName
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
  
  console.log('✅ Generated initials:', initials)
  return initials
})

// Initialize classInfo with proper fallbacks
const classInfo = computed(() => {
  return props.classData || props.initialData?.classInfo || {
    id: props.classId,
    name: 'Class',
    student_count: 0
  }
})

const schedules = ref(props.initialData?.schedules || [])
const scheduleStats = ref(props.initialData?.scheduleStats || {
  total_schedules: 0,
  upcoming_schedules: 0,
  completed_schedules: 0,
  cancelled_schedules: 0,
  completion_rate: 0
})

const scheduleTypes = ref(props.initialData?.scheduleTypes || [
  { value: 'regular', label: 'Regular Class', color: 'blue' },
  { value: 'extra', label: 'Extra Class', color: 'green' },
  { value: 'revision', label: 'Revision', color: 'purple' },
  { value: 'test', label: 'Test/Exam', color: 'red' },
  { value: 'meeting', label: 'Meeting', color: 'orange' },
  { value: 'workshop', label: 'Workshop', color: 'teal' },
])

const recurrencePatterns = ref(props.initialData?.recurrencePatterns || [
  { value: 'daily', label: 'Daily' },
  { value: 'weekly', label: 'Weekly' },
  { value: 'monthly', label: 'Monthly' },
])

// Component state
const currentView = ref('weekly')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const saving = ref(false)
const currentDate = ref(new Date())
const userMenuOpen = ref(false)

const scheduleForm = ref({
  id: null,
  title: '',
  date: '',
  time: '',
  duration: '60 minutes',
  type: 'regular',
  description: '',
  location: '',
  recurring: false,
  recurrence_pattern: 'weekly',
  recurrence_end_date: '',
  status: 'scheduled'
})

// Navigation methods
const navigateToSettings = () => {
  router.visit('/teacher/settings')
}

const goBackToAdmin = () => {
  router.visit('/admin/users/other-users')
}

const editProfile = () => {
  router.visit('/teacher/profile/edit')
}

const logout = async () => {
  try {
    router.post('/logout')
  } catch (error) {
    console.error('Logout error:', error)
  }
}

// UI Methods
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    userMenuOpen.value = false
  }
}

// Menu management
const toggleMenu = (menu) => {
  // Your existing code
}

const createAssignment = () => {
  router.visit(`/teacher/class/${props.classId}/assignments/create`)
}

// Schedule Methods
const saveSchedule = async () => {
  saving.value = true
  
  const formData = {
    title: scheduleForm.value.title,
    description: scheduleForm.value.description || '',
    date: scheduleForm.value.date,
    time: scheduleForm.value.time,
    duration: scheduleForm.value.duration,
    type: scheduleForm.value.type,
    location: scheduleForm.value.location || '',
    recurring: scheduleForm.value.recurring,
    recurrence_pattern: scheduleForm.value.recurring ? scheduleForm.value.recurrence_pattern : null,
    recurrence_end_date: scheduleForm.value.recurring ? scheduleForm.value.recurrence_end_date : null
  }

  console.log('Submitting schedule data:', JSON.stringify(formData, null, 2))

  try {
    let url, method;
    
    if (showEditModal.value) {
      url = `/api/schedules/${scheduleForm.value.id}`;
      method = 'PUT';
    } else {
      url = `/api/schedules/class/${props.classId}`;
      method = 'POST';
    }

    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify(formData)
    });

    const result = await response.json();

    if (result.success) {
      console.log('✅ Schedule saved successfully:', result.data)
      closeModal()
      // Fetch updated schedules immediately
      await fetchSchedules()
    } else {
      throw new Error(result.message || 'Failed to save schedule')
    }
  } catch (error) {
    console.error('❌ Error saving schedule:', error)
    alert('Failed to save schedule: ' + error.message)
  } finally {
    saving.value = false
  }
}

// Fetch schedules from API
const fetchSchedules = async () => {
  try {
    console.log('🔄 Fetching updated schedules...')
    const response = await fetch(`/api/schedules/class/${props.classId}`, {
      headers: {
        'Accept': 'application/json'
      }
    })
    
    if (response.ok) {
      const result = await response.json()
      if (result.success) {
        schedules.value = result.data
        console.log('✅ Schedules updated:', schedules.value.length, 'schedules')
        
        // Update stats if available
        if (result.scheduleStats) {
          scheduleStats.value = result.scheduleStats
        }
      } else {
        throw new Error(result.message || 'Failed to fetch schedules')
      }
    } else {
      throw new Error('Failed to fetch schedules: ' + response.status)
    }
  } catch (error) {
    console.error('❌ Error fetching schedules:', error)
    alert('Failed to refresh schedules: ' + error.message)
  }
}

// Delete schedule using API
const deleteSchedule = async (scheduleId) => {
  if (confirm('Are you sure you want to delete this schedule?')) {
    try {
      const response = await fetch(`/api/schedules/${scheduleId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      });

      const result = await response.json();

      if (result.success) {
        console.log('✅ Schedule deleted successfully')
        // Fetch updated schedules
        await fetchSchedules()
      } else {
        throw new Error(result.message || 'Failed to delete schedule')
      }
    } catch (error) {
      console.error('❌ Error deleting schedule:', error)
      alert('Failed to delete schedule: ' + error.message)
    }
  }
}

// Edit schedule
const editSchedule = (schedule) => {
  scheduleForm.value = { 
    ...schedule,
    recurrence_end_date: schedule.recurrence_end_date || '',
    location: schedule.location || ''
  }
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
    duration: '60 minutes',
    type: 'regular',
    description: '',
    location: '',
    recurring: false,
    recurrence_pattern: 'weekly',
    recurrence_end_date: '',
    status: 'scheduled'
  }
}

// Calendar data and methods
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

// Helper methods
const getScheduleTypeColor = (type) => {
  const colors = {
    regular: 'bg-blue-100 text-blue-800',
    extra: 'bg-green-100 text-green-800',
    revision: 'bg-purple-100 text-purple-800',
    test: 'bg-orange-100 text-orange-800',
    meeting: 'bg-yellow-100 text-yellow-800',
    workshop: 'bg-teal-100 text-teal-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getScheduleTypeClasses = (type) => {
  const classes = {
    regular: 'bg-blue-100 border-blue-300 text-blue-800 hover:bg-blue-200',
    extra: 'bg-green-100 border-green-300 text-green-800 hover:bg-green-200',
    revision: 'bg-purple-100 border-purple-300 text-purple-800 hover:bg-purple-200',
    test: 'bg-orange-100 border-orange-300 text-orange-800 hover:bg-orange-200',
    meeting: 'bg-yellow-100 border-yellow-300 text-yellow-800 hover:bg-yellow-200',
    workshop: 'bg-teal-100 border-teal-300 text-teal-800 hover:bg-teal-200'
  }
  return classes[type] || 'bg-gray-100 border-gray-300 text-gray-800 hover:bg-gray-200'
}

const getStatusColor = (status) => {
  const colors = {
    scheduled: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    completed: 'bg-blue-100 text-blue-800',
    postponed: 'bg-yellow-100 text-yellow-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'No date'
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
  const dayString = date.toDateString()
  const [startTime] = timeSlot.split(' - ')
  
  const slotSchedules = schedules.value.filter(schedule => {
    if (!schedule.date || !schedule.time) return false
    
    const scheduleDate = new Date(schedule.date)
    const scheduleDayString = scheduleDate.toDateString()
    const timeMatch = schedule.time.startsWith(startTime)
    const dateMatch = scheduleDayString === dayString
    
    return dateMatch && timeMatch
  })
  
  return slotSchedules
}

const getSchedulesForDay = (date) => {
  const dayString = date.toDateString()
  const daySchedules = schedules.value.filter(schedule => {
    if (!schedule.date) return false
    
    const scheduleDate = new Date(schedule.date)
    const scheduleDayString = scheduleDate.toDateString()
    
    return scheduleDayString === dayString
  })
  
  return daySchedules
}

const getSlotTime = (date, timeSlot) => {
  const [hours, minutes] = timeSlot.split(':')
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

// Lifecycle
onMounted(() => {
  console.log('📅 Schedule component mounted')
  console.log('Effective teacher:', effectiveTeacher.value)
  console.log('Class info:', classInfo.value)
  
  document.addEventListener('click', handleClickOutside)
  
  // Fetch initial schedules
  fetchSchedules()
})

// REMOVED onUnmounted since we don't need it for this fix
</script>

<style scoped>
/* Your existing styles remain the same */
:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
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
  text-decoration: none;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
  text-decoration: none;
}

.no-underline {
  text-decoration: none !important;
}

button:hover,
a:hover {
  text-decoration: none !important;
}
</style>