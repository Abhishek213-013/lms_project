<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <main class="flex-1 ml-64">
      <!-- Phoenix Top Navbar -->
      <Navbar page-title="Approval Statistics" @search="handleSearch" />

      <!-- Page Content -->
      <div class="p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Ecommerce Dashboard</h1>
        <p class="text-sm text-gray-600 mb-6">
          Here's what's going on at your business right now
        </p>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <DashboardCard
            title="57 new orders"
            subtitle="Awaiting processing"
            color="blue"
            icon="shopping-bag"
          />
          <DashboardCard
            title="5 orders"
            subtitle="On hold"
            color="yellow"
            icon="clock"
          />
          <DashboardCard
            title="15 products"
            subtitle="Out of stock"
            color="red"
            icon="alert-triangle"
          />
        </div>

        <!-- Total Sales Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
          <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Total sales</h3>
            <p class="text-sm text-gray-600">Payment received across all channels</p>
          </div>
          
          <div class="border-t border-gray-200 pt-4">
            <h4 class="text-md font-medium text-gray-900 mb-4">Mar 1 - 31, 2022</h4>
            <!-- Placeholder for sales chart -->
            <div class="h-64 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center">
              <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="text-gray-500">Sales chart visualization</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, h, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import Sidebar from '../../Layout/Sidebar.vue'
import Navbar from '../../Layout/Navbar.vue'

const router = useRouter()
const activeMenu = ref('dashboard')
const isDark = ref(false)
const userMenuOpen = ref(false)
const user = ref(null)

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

// Get user initials for profile picture
const getUserInitials = computed(() => {
  if (!user.value?.name) return 'SA'
  return user.value.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

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
    
    // Call logout API
    if (token) {
      await fetch('/api/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      })
    }
  } catch (error) {
    console.error('Logout API error:', error)
  } finally {
    // Clear authentication data
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    
    // Redirect to login page
    router.push('/login')
    
    // Close the user menu
    userMenuOpen.value = false
    
    console.log('User logged out successfully')
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  const userMenu = event.target.closest('.relative')
  if (!userMenu) {
    userMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  checkAuthentication()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Simple SVG icons as components
const ClockIcon = () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
])

const UsersIcon = () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z' })
])

const BookOpenIcon = () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })
])

const BarChartIcon = () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' })
])

const SettingsIcon = () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }),
  h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })
])

const ShoppingBagIcon = () => h('svg', { class: 'w-6 h-6', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })
])

const AlertTriangleIcon = () => h('svg', { class: 'w-6 h-6', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })
])

const iconMap = {
  'clock': ClockIcon,
  'users': UsersIcon,
  'book-open': BookOpenIcon,
  'bar-chart-2': BarChartIcon,
  'settings': SettingsIcon,
  'shopping-bag': ShoppingBagIcon,
  'alert-triangle': AlertTriangleIcon
}

// SidebarItem component
const SidebarItem = {
  props: ['icon', 'label', 'menu', 'activeMenu'],
  emits: ['toggle'],
  setup(props, { emit, slots }) {
    const IconComponent = iconMap[props.icon]
    return () => h('div', [
      h('button', {
        class: [
          'w-full flex items-center justify-between px-3 py-2 rounded-md transition',
          props.activeMenu === props.menu
            ? 'bg-indigo-50 text-indigo-600 font-medium'
            : 'text-gray-700 hover:bg-gray-50'
        ],
        onClick: () => emit('toggle', props.menu)
      }, [
        h('div', { class: 'flex items-center space-x-3' }, [
          h(IconComponent),
          h('span', props.label)
        ]),
        h('svg', {
          class: [
            'w-4 h-4 transition-transform duration-200',
            { 'rotate-180': props.activeMenu === props.menu }
          ],
          fill: 'none',
          stroke: 'currentColor',
          viewBox: '0 0 24 24'
        }, [
          h('path', { strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: '2', d: 'M19 9l-7 7-7-7' })
        ])
      ]),
      props.activeMenu === props.menu && h('div', { class: 'ml-8 mt-2 space-y-1' }, slots.default?.())
    ])
  }
}

// DashboardCard component
const DashboardCard = {
  props: ['title', 'subtitle', 'color', 'icon'],
  setup(props) {
    const IconComponent = iconMap[props.icon]
    const colorClasses = {
      blue: { bg: 'bg-blue-50', text: 'text-blue-600' },
      yellow: { bg: 'bg-yellow-50', text: 'text-yellow-600' },
      red: { bg: 'bg-red-50', text: 'text-red-600' }
    }
    const colors = colorClasses[props.color] || colorClasses.blue
    
    return () => h('div', { class: 'bg-white rounded-lg border border-gray-200 p-6' }, [
      h('div', { class: 'flex justify-between items-start' }, [
        h('div', [
          h('p', { class: 'text-sm font-medium text-gray-600 mb-1' }, props.title),
          h('p', { class: 'text-xs text-gray-500' }, props.subtitle)
        ]),
        h('div', { class: `p-3 rounded-lg ${colors.bg}` }, [
          h(IconComponent, { class: `w-6 h-6 ${colors.text}` })
        ])
      ])
    ])
  }
}
</script>

<style scoped>
.submenu-link {
  display: block;
  padding: 0.5rem 0.75rem;
  color: #6b7280;
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.submenu-link:hover {
  color: #4f46e5;
  background-color: #f9fafb;
}

.rotate-180 {
  transform: rotate(180deg);
}
</style>