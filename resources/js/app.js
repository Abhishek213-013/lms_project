import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';

// CSS import
import '../css/app.css';

// Import the translation system
import { translations, provideTranslation } from './composables/useTranslation';

// Initialize Bengali Fonts
const initializeBengaliFonts = () => {
  // Method 1: Google Fonts (Kalpurush)
  const kalpurushLink = document.createElement('link');
  kalpurushLink.href = 'https://fonts.googleapis.com/css2?family=Kalpurush&display=swap';
  kalpurushLink.rel = 'stylesheet';
  document.head.appendChild(kalpurushLink);

  // Method 2: Local Bengali font stack
  const style = document.createElement('style');
  style.textContent = `
    @font-face {
      font-family: 'SolaimanLipi';
      src: local('SolaimanLipi'), 
           url('/fonts/SolaimanLipi.ttf') format('truetype');
      font-display: swap;
    }
    
    .bn-lang {
      font-family: 'Kalpurush', 'SolaimanLipi', 'Siyam Rupali', 'AdorshoLipi', 'AponaLohit', 
                   'Bangla', 'Nikosh', 'Mina', 'Lohit Bengali', 'Noto Sans Bengali', 
                   'Arial Unicode MS', Arial, sans-serif !important;
      line-height: 1.6;
    }
    
    /* Ensure proper rendering for Bengali text */
    .bn-lang * {
      font-family: inherit !important;
    }
    
    /* Improve readability for Bengali */
    .bn-lang {
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    
    /* Specific adjustments for Bengali typography */
    .bn-lang p {
      line-height: 1.8;
    }
    
    .bn-lang h1, 
    .bn-lang h2, 
    .bn-lang h3, 
    .bn-lang h4, 
    .bn-lang h5, 
    .bn-lang h6 {
      font-weight: 700;
      line-height: 1.4;
    }
  `;
  document.head.appendChild(style);

  console.log('✅ Bengali fonts initialized');
};

// Initialize language system
const initializeLanguageSystem = () => {
  // Set default language to Bengali if not set
  if (!localStorage.getItem('preferredLanguage')) {
    localStorage.setItem('preferredLanguage', 'bn');
    console.log('🌐 Default language set to Bengali');
  }
  
  const currentLanguage = localStorage.getItem('preferredLanguage') || 'bn';
  
  // Apply language class to body
  if (currentLanguage === 'bn') {
    document.body.classList.add('bn-lang');
  } else {
    document.body.classList.remove('bn-lang');
  }
  
  console.log(`🌐 Language system initialized: ${currentLanguage}`);
};

// Global translation function for use outside Vue components
const globalT = (key, replacements = {}) => {
  const currentLang = localStorage.getItem('preferredLanguage') || 'bn';
  let translated = translations[currentLang]?.[key] || key;
  
  Object.keys(replacements).forEach(replacementKey => {
    translated = translated.replace(`{${replacementKey}}`, replacements[replacementKey]);
  });
  
  return translated;
};

// Theme system functions
const initializeThemeSystem = () => {
  // Set default theme to light if not set
  const savedTheme = localStorage.getItem('preferredTheme');
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  
  let theme = 'light';
  
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    theme = savedTheme;
  } else if (systemPrefersDark) {
    theme = 'dark';
  }
  
  localStorage.setItem('preferredTheme', theme);
  applyGlobalTheme(theme);
  
  console.log(`🎨 Theme system initialized: ${theme}`);
};

const applyGlobalTheme = (theme) => {
  if (theme === 'dark') {
    document.documentElement.classList.add('dark-theme');
    document.documentElement.classList.remove('light-theme');
    document.body.classList.add('dark-theme');
    document.body.classList.remove('light-theme');
  } else {
    document.documentElement.classList.add('light-theme');
    document.documentElement.classList.remove('dark-theme');
    document.body.classList.add('light-theme');
    document.body.classList.remove('dark-theme');
  }
};

// Combined initialization function
const initializeAppSystems = () => {
  initializeBengaliFonts();
  initializeLanguageSystem();
  initializeThemeSystem();
};

// Initialize Inertia app
createInertiaApp({
  title: (title) => {
    const currentLanguage = localStorage.getItem('preferredLanguage') || 'bn';
    const siteName = currentLanguage === 'bn' ? 'স্কিলগ্রো - অনলাইন লার্নিং প্ল্যাটফর্ম' : 'SkillGro - Online Learning Platform';
    return title ? `${title} - ${siteName}` : siteName;
  },
  
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
    // Initialize all app systems
    initializeAppSystems();
    
    const vueApp = createApp({ render: () => h(App, props) });
    
    // Use Inertia plugin
    vueApp.use(plugin);
    
    // Provide translation globally to all components
    provideTranslation(vueApp);
    
    // Add global translation function to all components
    vueApp.mixin({
      methods: {
        t: globalT
      },
      mounted() {
        // Listen for language changes from other components
        window.addEventListener('languageChanged', (event) => {
          // This will force re-render when language changes
          this.$forceUpdate();
        });
      }
    });
    
    // Add global properties for easy access
    vueApp.config.globalProperties.t = globalT;
    vueApp.config.globalProperties.currentLanguage = localStorage.getItem('preferredLanguage') || 'bn';
    vueApp.config.globalProperties.switchLanguage = (lang) => {
      localStorage.setItem('preferredLanguage', lang);
      
      // Update body class
      if (lang === 'bn') {
        document.body.classList.add('bn-lang');
      } else {
        document.body.classList.remove('bn-lang');
      }
      
      // Update page title
      document.title = lang === 'bn' 
        ? 'স্কিলগ্রো - অনলাইন লার্নিং প্ল্যাটফর্ম'
        : 'SkillGro - Online Learning Platform';
      
      // Dispatch event for all components to update
      window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
      
      // Force Vue to update all components
      vueApp.config.globalProperties.currentLanguage = lang;
    };
    
    // Add theme switching capability
    vueApp.config.globalProperties.switchTheme = (theme) => {
      localStorage.setItem('preferredTheme', theme);
      applyGlobalTheme(theme);
      window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    };
    
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
    console.log('🌐 Current language:', localStorage.getItem('preferredLanguage') || 'bn');
    console.log('🌐 Translation system: Ready');
  }
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

// Language change event listener for global updates
window.addEventListener('languageChanged', (event) => {
  console.log(`🌐 Language changed to: ${event.detail.language}`);
  
  // Update any global elements that need language-specific content
  const siteTitle = document.querySelector('title');
  if (siteTitle && event.detail.language === 'bn') {
    siteTitle.textContent = 'স্কিলগ্রো - অনলাইন লার্নিং প্ল্যাটফর্ম';
  } else if (siteTitle) {
    siteTitle.textContent = 'SkillGro - Online Learning Platform';
  }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  initializeAppSystems();
});

// Export language functions for global use
window.SkillGro = {
  switchLanguage: (lang) => {
    localStorage.setItem('preferredLanguage', lang);
    window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
    
    // Update body class
    if (lang === 'bn') {
      document.body.classList.add('bn-lang');
    } else {
      document.body.classList.remove('bn-lang');
    }
  },
  getCurrentLanguage: () => {
    return localStorage.getItem('preferredLanguage') || 'bn';
  },
  switchTheme: (theme) => {
    localStorage.setItem('preferredTheme', theme);
    applyGlobalTheme(theme);
    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
  },
  getCurrentTheme: () => {
    return localStorage.getItem('preferredTheme') || 'light';
  },
  t: globalT
};

console.log('🚀 SkillGro app initialized with Bengali support');