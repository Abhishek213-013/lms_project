<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-sm border border-gray-200 p-8">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-lg">P</span>
        </div>
      </div>

      <!-- Title -->
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">Sign In</h2>
      <p class="text-center text-gray-500 mb-6">Get access to your account</p>

      <!-- Role Selection - Informational Only -->
      <div class="mb-5">
        <label class="block text-gray-700 text-sm font-medium mb-2">I am a</label>
        <div class="grid grid-cols-2 gap-2">
          <button
            v-for="role in roles"
            :key="role.value"
            type="button"
            :class="[
              'py-2 px-3 rounded border text-sm font-medium transition-colors',
              selectedRole === role.value
                ? 'bg-indigo-600 text-white border-indigo-600'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            ]"
            @click="selectedRole = role.value"
          >
            {{ role.label }}
          </button>
        </div>
        <p class="text-xs text-gray-500 mt-2">Select your role for better experience</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit">
        <!-- Username -->
        <div class="mb-4">
          <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
          <input
            id="username"
            v-model="form.username"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            placeholder="Enter your username"
            :class="{ 'border-red-500': form.errors.username }"
          />
          <div v-if="form.errors.username" class="text-red-500 text-sm mt-1">
            {{ form.errors.username }}
          </div>
        </div>

        <!-- Password -->
        <div class="mb-5">
          <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            placeholder="Enter your password"
            :class="{ 'border-red-500': form.errors.password }"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Remember me / Forgot -->
        <div class="flex items-center justify-between mb-6">
          <label class="flex items-center text-sm text-gray-700">
            <input type="checkbox" v-model="form.remember" class="mr-2 rounded border-gray-300 focus:ring-indigo-500" /> Remember me
          </label>
          <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
            Forgot Password?
          </a>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="form.processing"
          :class="[
            'w-full py-2 rounded-md text-white font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2',
            form.processing 
              ? 'bg-gray-400 cursor-not-allowed focus:ring-gray-400' 
              : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500'
          ]"
        >
          {{ form.processing ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <!-- Footer -->
      <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <a href="/registration" class="text-indigo-600 hover:text-indigo-800 font-medium">
            Create one
          </a>
        </p>
      </div>

      <!-- General Error -->
      <div v-if="form.errors.message" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
        <p class="text-red-600 text-sm text-center">{{ form.errors.message }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const roles = [
  { label: "Teacher", value: "teacher" },
  { label: "Administrator", value: "admin" }
];

const selectedRole = ref('teacher');

const form = useForm({
  username: '',
  password: '',
  remember: false,
});

// Debug CSRF token
onMounted(() => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]');
  console.log('🔐 CSRF Token available:', !!csrfToken);
  console.log('🔐 CSRF Token value:', csrfToken?.getAttribute('content'));
  console.log('🍪 Cookies:', document.cookie);
});

const submit = () => {
  console.log('🚀 Submitting login form...');
  console.log('📤 Form data:', { 
    username: form.username, 
    remember: form.remember 
  });

  form.post('/login', {
    preserveScroll: true,
    onStart: () => console.log('📨 Request started'),
    onSuccess: (page) => {
      console.log('✅ Login successful', page);
    },
    onError: (errors) => {
      console.log('❌ Login errors:', errors);
      console.log('📊 Response status:', form.recentlySuccessful);
      
      if (errors.response && errors.response.status === 419) {
        alert('🔄 Session expired. Refreshing page...');
        window.location.reload();
      }
    },
    onFinish: () => {
      console.log('🏁 Request finished');
    },
  });
};
</script>