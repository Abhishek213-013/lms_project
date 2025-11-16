<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-8">
    <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg border border-gray-200 p-8 md:p-10">
      <!-- Pathshala LMS Logo -->
      <div class="text-center mb-8">
        <div class="logo-container">
          <img src="/assets/img/pathshala-logo.png" alt="Pathshala LMS" class="logo">
        </div>
      </div>

      <!-- Title -->
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">Teacher Registration</h2>
      <p class="text-center text-gray-600 mb-8 text-lg">Join Pathshala LMS as an educator</p>

      <!-- Teacher Notice -->
      <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <p class="text-sm text-blue-700 font-medium">
            Teacher accounts require admin approval before activation
          </p>
        </div>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Full Name -->
          <div class="space-y-2">
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Full Name</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              required
              placeholder="Enter your full name"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.name }"
            />
            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
          </div>

          <!-- Username -->
          <div class="space-y-2">
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Username</label>
            <input
              id="username"
              type="text"
              v-model="form.username"
              required
              placeholder="Choose a username"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.username }"
            />
            <div v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</div>
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Email Address</label>
            <input
              id="email"
              type="email"
              v-model="form.email"
              required
              placeholder="name@example.com"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.email }"
            />
            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
          </div>

          <!-- Date of Birth -->
          <div class="space-y-2">
            <label for="dob" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Date of Birth</label>
            <input
              id="dob"
              type="date"
              v-model="form.dob"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.dob }"
            />
            <div v-if="form.errors.dob" class="text-red-500 text-xs mt-1">{{ form.errors.dob }}</div>
          </div>

          <!-- Education Qualification -->
          <div class="md:col-span-2 space-y-2">
            <label for="education" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Education Qualification</label>
            <select
              id="education"
              v-model="form.education_qualification"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.education_qualification }"
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
          <div class="md:col-span-2 space-y-2">
            <label for="institute" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Institute</label>
            <input
              id="institute"
              type="text"
              v-model="form.institute"
              required
              placeholder="Current institute or university"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.institute }"
            />
            <div v-if="form.errors.institute" class="text-red-500 text-xs mt-1">{{ form.errors.institute }}</div>
          </div>

          <!-- Experience -->
          <div class="md:col-span-2 space-y-2">
            <label for="experience" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Teaching Experience</label>
            <input
              id="experience"
              type="text"
              v-model="form.experience"
              required
              placeholder="e.g., 5 years in Mathematics"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.experience }"
            />
            <div v-if="form.errors.experience" class="text-red-500 text-xs mt-1">{{ form.errors.experience }}</div>
          </div>

          <!-- Bio -->
          <div class="md:col-span-2 space-y-2">
            <label for="bio" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Teaching Philosophy & Bio</label>
            <textarea
              id="bio"
              v-model="form.bio"
              placeholder="Tell us about your teaching experience, subjects you specialize in, and your teaching philosophy..."
              rows="4"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-vertical"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.bio }"
            ></textarea>
            <div v-if="form.errors.bio" class="text-red-500 text-xs mt-1">{{ form.errors.bio }}</div>
          </div>

          <!-- Password -->
          <div class="space-y-2">
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Password</label>
            <input
              id="password"
              type="password"
              v-model="form.password"
              required
              placeholder="Create a secure password"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.password }"
            />
            <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
          </div>

          <!-- Confirm Password -->
          <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1 uppercase tracking-wide">Confirm Password</label>
            <input
              id="password_confirmation"
              type="password"
              v-model="form.password_confirmation"
              required
              placeholder="Confirm your password"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.password_confirmation }"
            />
            <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ form.errors.password_confirmation }}</div>
          </div>
        </div>

        <!-- Increased spacing before submit button -->
        <div class="mt-10">
          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="form.processing"
            :class="[
              'w-full py-4 rounded-lg text-white font-semibold text-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-200',
              form.processing 
                ? 'bg-gray-400 cursor-not-allowed' 
                : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 transform hover:-translate-y-0.5 active:translate-y-0'
            ]"
          >
            {{ form.processing ? 'Submitting Application...' : 'Apply as Teacher' }}
          </button>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 pt-6 border-t border-gray-200">
          <p class="text-sm text-gray-600">
            Already have an account?
            <a href="/login" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
              Sign in here
            </a>
          </p>
        </div>

        <!-- Student Registration Link -->
        <div class="text-center mt-4 pt-4 border-t border-gray-200">
          <p class="text-sm text-gray-600">
            Looking for student registration?
            <a href="/student-registration" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
              Student Registration
            </a>
          </p>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="status" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <p class="text-green-600 text-sm text-center font-medium">{{ status }}</p>
        </div>
        
        <div v-if="form.errors.message" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm text-center font-medium">{{ form.errors.message }}</p>
        </div>
      </form>
    </div>

    <!-- Admin Approval Popup -->
    <div v-if="showApprovalPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 transform transition-all duration-300 scale-100">
        <!-- Success Icon -->
        <div class="text-center mb-4">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
        </div>

        <!-- Popup Content -->
        <div class="text-center">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Application Submitted Successfully!</h3>
          <p class="text-gray-600 mb-4">
            Your teacher registration has been received. Please wait for admin approval. 
            You will be notified via email once your account is activated.
          </p>
          
          <!-- Countdown Timer -->
          <div class="mb-4">
            <p class="text-sm text-gray-500">
              Redirecting to login page in <span class="font-semibold text-blue-600">{{ countdown }}</span> seconds...
            </p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3">
            <button
              @click="redirectNow"
              class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium"
            >
              Go to Login Now
            </button>
            <button
              @click="stayOnPage"
              class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium"
            >
              Stay Here
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, onUnmounted } from 'vue';

const props = defineProps({
  errors: Object,
  status: String,
});

const showApprovalPopup = ref(false);
const countdown = ref(5);
let countdownInterval = null;

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

const startCountdown = () => {
  countdownInterval = setInterval(() => {
    countdown.value--;
    if (countdown.value <= 0) {
      redirectToLogin();
    }
  }, 1000);
};

const redirectToLogin = () => {
  clearInterval(countdownInterval);
  window.location.href = '/login';
};

const redirectNow = () => {
  clearInterval(countdownInterval);
  redirectToLogin();
};

const stayOnPage = () => {
  clearInterval(countdownInterval);
  showApprovalPopup.value = false;
  countdown.value = 5;
};

const submit = () => {
  console.log('Form data being submitted:', form.data());
  
  form.post('/teacher-registration', {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Teacher registration submitted successfully');
      // Show the approval popup on successful submission
      showApprovalPopup.value = true;
      startCountdown();
    },
    onError: (errors) => {
      console.log('❌ Teacher registration errors:', errors);
      console.log('Detailed error response:', errors);
    },
  });
};

// Cleanup interval when component unmounts
onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }
});
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
  max-height: 60px;
  width: auto;
}

.logo-text {
  text-align: left;
}

.logo-main {
  font-size: 1.5rem;
  font-weight: 700;
  color: #3b82f6; /* Blue color matching the theme */
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

/* Enhanced focus styles */
input:focus, select:focus, textarea:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  transition: all 0.2s ease-in-out;
}

/* Button hover effects */
button:not(:disabled):hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  transition: all 0.2s ease;
}

/* Link hover effects */
a:hover {
  transition: color 0.2s ease;
  text-decoration: underline;
}

/* Form improvements */
.form-group {
  margin-bottom: 1rem;
}

/* Custom scrollbar for textarea */
textarea {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}

textarea::-webkit-scrollbar {
  width: 6px;
}

textarea::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Popup animations */
.fixed {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.transform {
  animation: scaleIn 0.3s ease-out;
}

@keyframes scaleIn {
  from {
    transform: scale(0.9);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
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
  
  .flex.gap-3 {
    flex-direction: column;
    gap: 0.5rem;
  }
}

@media (max-width: 576px) {
  .logo {
    max-height: 50px;
  }
  
  .logo-main {
    font-size: 1.2rem;
  }
  
  .w-full.max-w-2xl {
    padding: 1.5rem;
  }
}

/* Animation for form elements */
input, select, textarea {
  transition: all 0.3s ease;
}

input:hover, select:hover, textarea:hover {
  border-color: #93c5fd;
}

/* Success and error message animations */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Loading state for button */
button:disabled {
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}
</style>