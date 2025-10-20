<template>
  <div id="app">
    <router-view></router-view>
    
    <!-- Global Toast Container -->
    <div id="toast-container" class="toast-container"></div>
  </div>
</template>

<script>
export default {
  name: 'App',
  mounted() {
    this.initializeApp();
  },
  methods: {
    initializeApp() {
      console.log('SkillGro LMS Frontend Initialized');
      
      // Hide loading screen
      const loadingScreen = document.querySelector('.loading-screen');
      if (loadingScreen) {
        loadingScreen.style.display = 'none';
      }
      
      // Initialize any global functionality
      this.initializeGlobalScripts();
    },
    
    initializeGlobalScripts() {
      // Initialize tooltips if Bootstrap is available
      if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      }
      
      // Initialize any other global scripts here
      console.log('Global scripts initialized');
    }
  }
}
</script>

<style>
/* Import core styles - these should be available in your public directory */
/* Vue transition styles */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.3s;
}
.page-enter,
.page-leave-to {
  opacity: 0;
}

/* Toast notifications */
.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
}

.toast {
  background: white;
  border-radius: 8px;
  padding: 15px 20px;
  margin-bottom: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #e74c3c;
  min-width: 300px;
  animation: slideInRight 0.3s ease;
}

.toast.success {
  border-left-color: #27ae60;
}

.toast.error {
  border-left-color: #e74c3c;
}

.toast.warning {
  border-left-color: #f39c12;
}

.toast.info {
  border-left-color: #3498db;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Loading states */
.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #e74c3c;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>