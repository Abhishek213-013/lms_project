<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-2xl bg-white rounded-lg shadow border border-gray-200 p-8">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-lg">P</span>
        </div>
      </div>

      <!-- Title -->
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">Sign Up</h2>
      <p class="text-center text-gray-500 mb-6">Create your account today</p>

      <!-- Registration Form -->
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Full Name -->
          <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Name</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              required
              placeholder="Full name"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</div>
          </div>

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Username</label>
            <input
              id="username"
              type="text"
              v-model="form.username"
              required
              placeholder="Choose a username"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div v-if="errors.username" class="text-red-500 text-xs mt-1">{{ errors.username }}</div>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Email Address</label>
            <input
              id="email"
              type="email"
              v-model="form.email"
              required
              placeholder="name@example.com"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</div>
          </div>

          <!-- Date of Birth -->
          <div>
            <label for="dob" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Date of Birth</label>
            <input
              id="dob"
              type="date"
              v-model="form.dob"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div v-if="errors.dob" class="text-red-500 text-xs mt-1">{{ errors.dob }}</div>
          </div>

          <!-- Education Qualification -->
          <div>
            <label for="education" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Education Qualification</label>
            <select
              id="education"
              v-model="form.education_qualification"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="">Select Qualification</option>
              <option value="HSC">HSC</option>
              <option value="BSC">BSC</option>
              <option value="BA">BA</option>
              <option value="MA">MA</option>
              <option value="MSC">MSC</option>
              <option value="PhD">PhD</option>
              <option value="Other">Other</option>
            </select>
            <div v-if="errors.education_qualification" class="text-red-500 text-xs mt-1">{{ errors.education_qualification }}</div>
          </div>

          <!-- Institute -->
          <div>
            <label for="institute" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Institute</label>
            <input
              id="institute"
              type="text"
              v-model="form.institute"
              required
              placeholder="Current institute"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div v-if="errors.institute" class="text-red-500 text-xs mt-1">{{ errors.institute }}</div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Password</label>
            <input
              id="password"
              type="password"
              v-model="form.password"
              required
              placeholder="Password"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</div>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Confirm Password</label>
            <input
              id="password_confirmation"
              type="password"
              v-model="form.password_confirmation"
              required
              placeholder="Confirm Password"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="form.processing"
          :class="[
            'w-full mt-6 py-2 rounded-md text-white font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2',
            form.processing 
              ? 'bg-gray-400 cursor-not-allowed focus:ring-gray-400' 
              : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500'
          ]"
        >
          {{ form.processing ? 'Registering...' : 'Sign Up' }}
        </button>

        <!-- Footer -->
        <div class="text-center mt-6">
          <p class="text-sm text-gray-600">
            Already have an account?
            <a href="/login" class="text-indigo-600 hover:text-indigo-800 font-medium">
              Sign in
            </a>
          </p>
        </div>

        <!-- Success Message -->
        <div v-if="status" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md">
          <p class="text-green-600 text-sm text-center">{{ status }}</p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

defineProps({
  errors: Object,
  status: String,
});

const form = useForm({
  name: '',
  username: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  password: '',
  password_confirmation: '',
  role: 'teacher', // Default role for registration
});

const submit = () => {
  form.post('/register', {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};
</script>