<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-2xl bg-white rounded-lg shadow border border-gray-200 p-8">
      <!-- Pathshala LMS Logo -->
      <div class="text-center mb-6">
        <div class="logo-container">
          <img src="/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo">
        </div>
      </div>

      <!-- Title -->
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">Teacher Registration</h2>
      <p class="text-center text-gray-500 mb-6">Join Pathshala LMS as an educator</p>

      <!-- Teacher Notice -->
      <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <p class="text-sm text-yellow-700">
            Teacher accounts require admin approval before activation
          </p>
        </div>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Full Name -->
          <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Full Name</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              required
              placeholder="Enter your full name"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
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
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
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
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
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
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
              :class="{ 'border-red-500': form.errors.dob }"
            />
            <div v-if="form.errors.dob" class="text-red-500 text-xs mt-1">{{ form.errors.dob }}</div>
          </div>

          <!-- Education Qualification -->
          <div class="md:col-span-2">
            <label for="education" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Education Qualification</label>
            <select
              id="education"
              v-model="form.education_qualification"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
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
          <div class="md:col-span-2">
            <label for="institute" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Institute</label>
            <input
              id="institute"
              type="text"
              v-model="form.institute"
              required
              placeholder="Current institute or university"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
              :class="{ 'border-red-500': form.errors.institute }"
            />
            <div v-if="form.errors.institute" class="text-red-500 text-xs mt-1">{{ form.errors.institute }}</div>
          </div>

          <!-- Experience -->
          <div class="md:col-span-2">
            <label for="experience" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Teaching Experience</label>
            <input
              id="experience"
              type="text"
              v-model="form.experience"
              required
              placeholder="e.g., 5 years in Mathematics"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
              :class="{ 'border-red-500': form.errors.experience }"
            />
            <div v-if="form.errors.experience" class="text-red-500 text-xs mt-1">{{ form.errors.experience }}</div>
          </div>

          <!-- Bio -->
          <div class="md:col-span-2">
            <label for="bio" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Teaching Philosophy & Bio</label>
            <textarea
              id="bio"
              v-model="form.bio"
              placeholder="Tell us about your teaching experience, subjects you specialize in, and your teaching philosophy..."
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
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
              placeholder="Create a secure password"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
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
              placeholder="Confirm your password"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent"
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
            'w-full mt-6 py-3 rounded-md text-white font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2',
            form.processing 
              ? 'bg-gray-400 cursor-not-allowed focus:ring-gray-400' 
              : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
          ]"
        >
          {{ form.processing ? 'Submitting Application...' : 'Apply as Teacher' }}
        </button>

        <!-- Footer -->
        <div class="text-center mt-6">
          <p class="text-sm text-gray-600">
            Already have an account?
            <a href="/login" class="text-red-600 hover:text-red-800 font-medium">
              Sign in here
            </a>
          </p>
        </div>

        <!-- Student Registration Link -->
        <div class="text-center mt-4 pt-4 border-t border-gray-200">
          <p class="text-sm text-gray-600">
            Looking for student registration?
            <a href="/student-registration" class="text-red-600 hover:text-red-800 font-medium">
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

const props = defineProps({
  errors: Object,
  status: String, // Make sure this is defined
});

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
  bio: '', // This will work after adding the database column
  password: '',
  password_confirmation: '',
  role: 'teacher',
});

const submit = () => {
  console.log('Form data being submitted:', form.data());
  
  form.post('/teacher-registration', {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Teacher registration submitted successfully');
    },
    onError: (errors) => {
      console.log('❌ Teacher registration errors:', errors);
      console.log('Detailed error response:', errors);
    },
  });
};
</script>

<style scoped>

:deep(*) {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    font-weight: 400;
}

.custom-heading {
    font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
}
/* Logo Container */
.logo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  margin-bottom: 10px;
}

.logo {
  max-height: 50px;
  width: auto;
}

.logo-text {
  text-align: left;
}

.logo-main {
  font-size: 1.5rem;
  font-weight: 700;
  color: #e74c3c;
  margin: 0;
  line-height: 1.2;
}

.logo-subtitle {
  font-size: 0.8rem;
  font-weight: 500;
  color: #6c757d;
  margin: 0;
  letter-spacing: 1px;
  text-transform: uppercase;
}

/* Responsive Design */
@media (max-width: 768px) {
  .logo-container {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .logo-text {
    text-align: center;
  }
  
  .logo-main {
    font-size: 1.3rem;
  }
  
  .logo-subtitle {
    font-size: 0.7rem;
  }
}

@media (max-width: 576px) {
  .logo {
    max-height: 45px;
  }
  
  .logo-main {
    font-size: 1.2rem;
  }
}

/* Enhanced focus styles */
input:focus, select:focus, textarea:focus {
  outline: none;
  ring-width: 2px;
  ring-color: #e74c3c;
}

/* Button hover effects */
button:not(:disabled):hover {
  transform: translateY(-1px);
  transition: all 0.2s ease;
}

/* Link hover effects */
a:hover {
  transition: color 0.2s ease;
}

/* Form improvements */
.form-group {
  margin-bottom: 1rem;
}

/* Warning notice styling */
.bg-yellow-50 {
  background-color: #fffbeb;
}

.border-yellow-200 {
  border-color: #fcd34d;
}
</style>