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

      <!-- Role Selection -->
      <div class="mb-6">
        <label class="block text-gray-700 text-sm font-medium mb-2">Account Type</label>
        <div class="grid grid-cols-2 gap-2">
          <button
            v-for="role in roles"
            :key="role.value"
            type="button"
            :class="[
              'py-2 px-3 rounded border text-sm font-medium transition-colors',
              form.role === role.value
                ? 'bg-indigo-600 text-white border-indigo-600'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            ]"
            @click="form.role = role.value"
          >
            {{ role.label }}
          </button>
        </div>
        <p class="text-xs text-gray-500 mt-2" v-if="form.role === 'teacher'">
          Teacher accounts require admin approval before activation
        </p>
      </div>

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
              :class="{ 'border-red-500': form.errors.name }"
            />
            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
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
              :class="{ 'border-red-500': form.errors.username }"
            />
            <div v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</div>
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
              :class="{ 'border-red-500': form.errors.email }"
            />
            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
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
              :class="{ 'border-red-500': form.errors.dob }"
            />
            <div v-if="form.errors.dob" class="text-red-500 text-xs mt-1">{{ form.errors.dob }}</div>
          </div>

          <!-- Education Qualification -->
          <div>
            <label for="education" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Education Qualification</label>
            <select
              id="education"
              v-model="form.education_qualification"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              :class="{ 'border-red-500': form.errors.education_qualification }"
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
            <div v-if="form.errors.education_qualification" class="text-red-500 text-xs mt-1">{{ form.errors.education_qualification }}</div>
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
              :class="{ 'border-red-500': form.errors.institute }"
            />
            <div v-if="form.errors.institute" class="text-red-500 text-xs mt-1">{{ form.errors.institute }}</div>
          </div>

          <!-- Experience (for teachers) -->
          <div v-if="form.role === 'teacher'">
            <label for="experience" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Teaching Experience</label>
            <input
              id="experience"
              type="text"
              v-model="form.experience"
              placeholder="e.g., 5 years"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              :class="{ 'border-red-500': form.errors.experience }"
            />
            <div v-if="form.errors.experience" class="text-red-500 text-xs mt-1">{{ form.errors.experience }}</div>
          </div>

          <!-- Bio (for teachers) -->
          <div v-if="form.role === 'teacher'">
            <label for="bio" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Bio</label>
            <textarea
              id="bio"
              v-model="form.bio"
              placeholder="Tell us about yourself..."
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              :class="{ 'border-red-500': form.errors.bio }"
            ></textarea>
            <div v-if="form.errors.bio" class="text-red-500 text-xs mt-1">{{ form.errors.bio }}</div>
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
              :class="{ 'border-red-500': form.errors.password }"
            />
            <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
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
              :class="{ 'border-red-500': form.errors.password_confirmation }"
            />
            <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ form.errors.password_confirmation }}</div>
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

        <!-- Student Registration Link -->
        <div class="text-center mt-4 pt-4 border-t border-gray-200">
          <p class="text-sm text-gray-600">
            Are you a student?
            <a href="/student-registration" class="text-indigo-600 hover:text-indigo-800 font-medium">
              Student Registration
            </a>
          </p>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="status" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md">
          <p class="text-green-600 text-sm text-center">{{ status }}</p>
        </div>
        
        <div v-if="form.errors.message" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
          <p class="text-red-600 text-sm text-center">{{ form.errors.message }}</p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const roles = [
  { label: "Teacher", value: "teacher" },
  { label: "Administrator", value: "admin" }
];

const form = useForm({
  name: '',
  username: '',
  email: '',
  dob: '',
  education_qualification: '',
  institute: '',
  experience: '',
  bio: '',
  password: '',
  password_confirmation: '',
  role: 'teacher',
});

const submit = () => {
  form.post('/register', {
    preserveScroll: true,
    onSuccess: () => {
      // Form will be reset and redirect handled by backend
      console.log('✅ Registration successful');
    },
    onError: (errors) => {
      console.log('❌ Registration errors:', errors);
    },
  });
};
</script>