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

      <!-- Role Selection - Only 2 options in UI -->
      <div class="mb-5">
        <label class="block text-gray-700 text-sm font-medium mb-2">Select Role</label>
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
      </div>

      <!-- Username -->
      <div class="mb-4">
        <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
        <input
          id="username"
          v-model="loginData.username"
          type="text"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          placeholder="Enter your username"
          @keyup.enter="handleLogin"
        />
      </div>

      <!-- Password -->
      <div class="mb-5">
        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
        <input
          id="password"
          v-model="loginData.password"
          type="password"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          placeholder="Enter your password"
          @keyup.enter="handleLogin"
        />
      </div>

      <!-- Remember me / Forgot -->
      <div class="flex items-center justify-between mb-6">
        <label class="flex items-center text-sm text-gray-700">
          <input type="checkbox" class="mr-2 rounded border-gray-300 focus:ring-indigo-500" /> Remember me
        </label>
        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
          Forgot Password?
        </a>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="loading"
        @click="handleLogin"
        :class="[
          'w-full py-2 rounded-md text-white font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2',
          loading 
            ? 'bg-gray-400 cursor-not-allowed focus:ring-gray-400' 
            : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500'
        ]"
      >
        {{ loading ? 'Signing in...' : 'Sign In' }}
      </button>

      <!-- Footer -->
      <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <router-link to="/registration" class="text-indigo-600 hover:text-indigo-800 font-medium">
            Create one
          </router-link>
        </p>
      </div>

      <!-- Error -->
      <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
        <p class="text-red-600 text-sm text-center">{{ error }}</p>
      </div>
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
      selectedRole: "teacher", // Default to teacher
      roles: [
        { label: "Teacher", value: "teacher" },
        { label: "Administrator", value: "admin" } // Only 2 roles in UI
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
      // Basic validation
      if (!this.loginData.username || !this.loginData.password) {
        this.error = "Please fill in all fields";
        return;
      }

      this.loading = true;
      this.error = "";

      try {
        // For Administrator selection, we need to determine if it's admin or super_admin
        // We'll try admin first, then super_admin if admin fails
        let roleToSend = this.selectedRole;
        
        if (this.selectedRole === 'admin') {
          // For Administrator selection, we need to figure out the actual role
          // We'll make the API call and handle the role mismatch gracefully
          roleToSend = 'admin'; // Start with admin
        }

        const response = await axios.post("/api/login", {
          username: this.loginData.username,
          password: this.loginData.password,
          role: roleToSend
        });

        if (response.data.success) {
          localStorage.setItem("token", response.data.token);
          localStorage.setItem("user", JSON.stringify(response.data.user));

          // Redirect based on actual user role from response
          const userRole = response.data.user.role;
          const roleRoutes = {
            super_admin: '/super-admin',
            admin: '/admin',
            teacher: '/teacher'
          };
          
          const redirectPath = roleRoutes[userRole];
          if (redirectPath) {
            this.$router.push(redirectPath);
          } else {
            this.error = "Invalid role configuration";
          }
        }
      } catch (error) {
        console.error("Login error:", error);
        
        // Handle role mismatch for Administrator case
        if (error.response?.status === 403 && this.selectedRole === 'admin') {
          // Try with super_admin role for Administrator selection
          try {
            const retryResponse = await axios.post("/api/login", {
              username: this.loginData.username,
              password: this.loginData.password,
              role: 'super_admin'
            });

            if (retryResponse.data.success) {
              localStorage.setItem("token", retryResponse.data.token);
              localStorage.setItem("user", JSON.stringify(retryResponse.data.user));
              
              // Redirect based on actual role (super_admin)
              this.$router.push('/super-admin');
              return;
            }
          } catch (retryError) {
            // If both fail, show the original error
            this.error = "Access denied. Please check your role selection.";
          }
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