<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-sm border border-gray-200 p-8">
      <!-- Pathshala LMS Logo -->
      <div class="flex justify-center mb-6">
        <div class="logo-container flex items-center">
          <img 
            src="../../../../public/assets/img/pathshala-logo.png" 
            alt="Pathshala LMS" 
            class="logo-image h-12 w-auto"
            @error="handleImageError"
          >
          <!-- <div class="logo-text ml-3">
            <div class="logo-main font-bold text-gray-900 text-xl">PATHSHALA LMS</div>
            <div class="logo-sub text-gray-500 text-xs tracking-wide">EMPOWER MINDS</div>
          </div> -->
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
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
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
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
            placeholder="Enter your password"
            :class="{ 'border-red-500': form.errors.password }"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Remember me / Forgot -->
        <div class="flex items-center justify-between mb-6">
          <label class="flex items-center text-sm text-gray-700 cursor-pointer">
            <input 
              type="checkbox" 
              v-model="form.remember" 
              class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-colors" 
            /> 
            Remember me
          </label>
          <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors">
            Forgot Password?
          </a>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="form.processing"
          :class="[
            'w-full py-3 rounded-md text-white font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-offset-2',
            form.processing 
              ? 'bg-gray-400 cursor-not-allowed focus:ring-gray-400' 
              : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm hover:shadow-md'
          ]"
        >
          {{ form.processing ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <!-- Footer -->
      <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <a href="/registration" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
            Create one
          </a>
        </p>
      </div>

      <!-- Student Login Link -->
      <!-- <div class="text-center mt-4 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">
          Are you a student?
          <a href="/student-login" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
            Student Login
          </a>
        </p>
      </div> -->

      <!-- General Error -->
      <div v-if="form.errors.message" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
        <p class="text-red-600 text-sm text-center">{{ form.errors.message }}</p>
      </div>

      <!-- Copyright -->
      <div class="text-center mt-6 pt-4 border-t border-gray-100">
        <p class="text-xs text-gray-500">
          &copy; {{ new Date().getFullYear() }} IT Lab Solutions Ltd. All rights reserved.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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

// Handle logo image loading error
const handleImageError = (event) => {
  console.log('Logo image failed to load, using fallback');
  // You could set a fallback image or hide the image element
  event.target.style.display = 'none';
};

const submit = () => {
  form.post('/login', {
    preserveScroll: true,
    onSuccess: () => {
      // Inertia will handle the redirect based on the backend response
      console.log('✅ Login successful');
    },
    onError: (errors) => {
      console.log('❌ Login errors:', errors);
    },
  });
};
</script>

<style scoped>
/* Logo Styles */
.logo-container {
  transition: transform 0.2s ease;
}

.logo-container:hover {
  transform: scale(1.02);
}

.logo-image {
  object-fit: contain;
  filter: brightness(0.9);
}

.logo-main {
  letter-spacing: 0.5px;
}

.logo-sub {
  letter-spacing: 1px;
  margin-top: 2px;
}

/* Smooth transitions for interactive elements */
button, a, input {
  transition: all 0.2s ease-in-out;
}

/* Focus styles for accessibility */
input:focus, 
button:focus {
  outline: 2px solid #6366f1;
  outline-offset: 2px;
}

input:focus {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

button:focus:not(:disabled) {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

/* Form input focus styles */
input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Button hover and focus states */
button:not(:disabled):hover {
  transform: translateY(-1px);
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .logo-container {
    flex-direction: column;
    text-align: center;
  }
  
  .logo-text {
    margin-left: 0;
    margin-top: 8px;
  }
  
  .logo-main {
    font-size: 18px;
  }
  
  .logo-sub {
    font-size: 10px;
  }
  
  /* Adjust padding for mobile */
  .w-full.max-w-md {
    padding: 1.5rem;
  }
}

/* Loading state for button */
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

/* Link hover effects */
a:hover {
  text-decoration: underline;
}

/* Checkbox focus style */
input[type="checkbox"]:focus {
  outline: 2px solid #6366f1;
  outline-offset: 2px;
}

/* Error state enhancements */
.border-red-500:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}
</style>