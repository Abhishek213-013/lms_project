import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';

// CSS import
import '../css/app.css';

// Initialize Inertia app
createInertiaApp({
  title: (title) => title ? `${title} - SkillGro` : 'SkillGro - Online Learning Platform',
  
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
    
    console.log(`🔍 Looking for page: ${name}`);
    
    // Try multiple path patterns to find the component
    const paths = [
      `./Pages/${name}.vue`,
      `./Pages/${name}/Index.vue`,
      `./Pages/Frontend/${name}.vue`,
      `./Pages/Frontend/${name}/Index.vue`,
      `./Pages/Admin/${name}.vue`,
      `./Pages/Teacher/${name}.vue`,
      `./Pages/Student/${name}.vue`,
      `./Pages/Auth/${name}.vue`,
      `./Pages/Layout/${name}.vue`,
      `./Pages/Frontend/Errors/${name}.vue`,
      `./Pages/Errors/${name}.vue`
    ];
    
    for (const path of paths) {
      if (pages[path]) {
        console.log(`✅ Loading page: ${path}`);
        const page = pages[path];
        return page.default ? page.default : page;
      }
    }
    
    console.error(`❌ Page not found: ${name}`);
    console.log('Available pages:', Object.keys(pages));
    
    // Return the 404 page instead of a simple error component
    const errorPage = pages['./Pages/Frontend/Errors/404.vue'] || pages['./Pages/Errors/404.vue'];
    if (errorPage) {
      console.log('🔄 Falling back to 404 page');
      return errorPage.default ? errorPage.default : errorPage;
    }
    
    // Final fallback
    return {
      render: () => h('div', `Page "${name}" not found.`)
    };
  },
  
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) });
    
    // Use Inertia plugin
    vueApp.use(plugin);
    
    // Use Ziggy for route() function with better error handling
    try {
      if (props.initialPage.props.ziggy) {
        vueApp.use(ZiggyVue, {
          ...props.initialPage.props.ziggy,
          location: new URL(props.initialPage.props.ziggy.url),
        });
        console.log('✅ Ziggy routes loaded successfully');
      } else {
        console.warn('⚠️ Ziggy routes not available, creating fallback route function');
        
        // Create a fallback route function
        vueApp.config.globalProperties.route = (name, params = {}, absolute = true) => {
          // Simple fallback route generator
          const baseUrl = window.location.origin;
          
          // Common routes mapping - add your most used routes here
          const routeMap = {
            'home': '/',
            'login': '/login',
            'registration': '/registration',
            'student.login': '/student-login',
            'student.registration': '/student-registration',
            'teacher.dashboard': '/teacher',
            'teacher.portal': '/teacher/portal',
            'student.dashboard': '/student',
            'admin': '/admin',
            'super.admin': '/super-admin',
            'logout': '/logout',
          };
          
          if (routeMap[name]) {
            let url = routeMap[name];
            
            // Handle parameters - simple replacement for common patterns
            if (params.id) {
              url = url.replace('{id}', params.id);
            }
            if (params.classId) {
              url = url.replace('{classId}', params.classId);
            }
            
            return absolute ? baseUrl + url : url;
          }
          
          console.warn(`Route "${name}" not found in fallback map`);
          return '#';
        };
      }
    } catch (error) {
      console.error('❌ Error setting up Ziggy:', error);
      
      // Emergency fallback
      vueApp.config.globalProperties.route = (name) => {
        console.warn(`Using emergency fallback for route: ${name}`);
        return '/';
      };
    }
    
    // Mount the app
    vueApp.mount(el);
    
    console.log('✅ Inertia.js app mounted successfully!');
  },
});

// Axios setup
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token to all axios requests
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
} else {
  console.warn('⚠️ CSRF token meta tag not found');
}