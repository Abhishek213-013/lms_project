<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-2xl bg-white rounded-lg shadow border border-gray-200 p-8">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <img src="/images/logo.svg" alt="Phoenix Logo" class="h-12" />
      </div>

      <!-- Title -->
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">Sign Up</h2>
      <p class="text-center text-gray-500 mb-6">Create your account today</p>

      <!-- Registration Form -->
      <form @submit.prevent="handleRegister">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Full Name -->
          <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Name</label>
            <input
              id="name"
              type="text"
              v-model="registerData.name"
              required
              placeholder="Full name"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Username</label>
            <input
              id="username"
              type="text"
              v-model="registerData.username"
              required
              placeholder="Choose a username"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Email Address</label>
            <input
              id="email"
              type="email"
              v-model="registerData.email"
              required
              placeholder="name@example.com"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Date of Birth -->
          <div>
            <label for="dob" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Date of Birth</label>
            <input
              id="dob"
              type="date"
              v-model="registerData.dob"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Education Qualification -->
          <div>
            <label for="education" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Education Qualification</label>
            <select
              id="education"
              v-model="registerData.education_qualification"
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
          </div>

          <!-- Institute -->
          <div>
            <label for="institute" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Institute</label>
            <input
              id="institute"
              type="text"
              v-model="registerData.institute"
              required
              placeholder="Current institute"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Password</label>
            <input
              id="password"
              type="password"
              v-model="registerData.password"
              required
              placeholder="Password"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Confirm Password</label>
            <input
              id="password_confirmation"
              type="password"
              v-model="registerData.password_confirmation"
              required
              placeholder="Confirm Password"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          :class="[
            'w-full mt-6 py-2 rounded-md text-white font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2',
            loading 
              ? 'bg-gray-400 cursor-not-allowed focus:ring-gray-400' 
              : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500'
          ]"
        >
          {{ loading ? 'Registering...' : 'Sign Up' }}
        </button>

        <!-- Footer -->
        <div class="text-center mt-6">
          <p class="text-sm text-gray-600">
            Already have an account?
            <router-link to="/login" class="text-indigo-600 hover:text-indigo-800 font-medium">
              Sign in
            </router-link>
          </p>
        </div>

        <!-- Alerts -->
        <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
          <p class="text-red-600 text-sm text-center">{{ error }}</p>
        </div>

        <div v-if="success" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md">
          <p class="text-green-600 text-sm text-center">Registration successful! Redirecting...</p>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { getCsrfToken } from '../api/client';

export default {
  name: "Login",
  data() {
    return {
      selectedRole: "teacher",
      roles: [
        { label: "Teacher", value: "teacher" },
        { label: "Administrator", value: "admin" }
      ],
      loginData: {
        username: "",
        password: "",
      },
      loading: false,
      error: "",
    };
  },
  async mounted() {
    // Get CSRF token when component mounts
    await getCsrfToken();
  },
  methods: {
    async handleLogin() {
      if (!this.loginData.username || !this.loginData.password) {
        this.error = "Please fill in all fields";
        return;
      }

      this.loading = true;
      this.error = "";

      try {
        // Ensure we have CSRF token
        await getCsrfToken();

        const response = await axios.post("/api/login", {
          username: this.loginData.username,
          password: this.loginData.password,
          role: this.selectedRole
        }, {
          withCredentials: true // Important for Sanctum
        });

        if (response.data.success) {
          localStorage.setItem("token", response.data.token);
          localStorage.setItem("user", JSON.stringify(response.data.user));

          // Redirect based on role
          const roleRoutes = {
            super_admin: '/super-admin',
            admin: '/admin',
            teacher: '/teacher'
          };
          
          const redirectPath = roleRoutes[response.data.user.role];
          if (redirectPath) {
            this.$router.push(redirectPath);
          } else {
            this.error = "Invalid role configuration";
          }
        }
      } catch (error) {
        console.error("Login error:", error);
        
        if (error.response?.status === 403) {
          this.error = "Role mismatch: The selected role doesn't match your account. Please select the correct role.";
        } else if (error.response?.status === 401) {
          this.error = "Invalid credentials. Please check your username and password.";
        } else if (error.response?.data?.message) {
          this.error = error.response.data.message;
        } else {
          this.error = "Login failed. Please check your credentials and try again.";
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
