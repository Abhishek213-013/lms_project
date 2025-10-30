import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';

// CSS import
import '../css/app.css';
import './bootstrap';


const appName = import.meta.env.VITE_APP_NAME || 'Pathshala LMS';
// Complete translation system
const translations = {
    en: {
        'Home': 'Home',
        'Courses': 'Courses',
        'Instructors': 'Instructors',
        'About': 'About',
        'Search Courses...': 'Search Courses...',
        'My Profile': 'My Profile',
        'My Courses': 'My Courses',
        'Learning Progress': 'Learning Progress',
        'Settings': 'Settings',
        'Logout': 'Logout',
        'Login': 'Login',
        'Get Started': 'Get Started',
        'Manage your profile and track your learning journey': 'Manage your profile and track your learning journey',
        'Member since': 'Member since',
        'Courses Enrolled': 'Courses Enrolled',
        'Courses Completed': 'Courses Completed',
        'Learning Hours': 'Learning Hours',
        'Day Streak': 'Day Streak',
        'Recent Activity': 'Recent Activity',
        'View All': 'View All',
        'Progress': 'Progress',
        'Quick Actions': 'Quick Actions',
        'Certificates': 'Certificates',
        'Edit Profile': 'Edit Profile',
        'Continue your learning journey': 'Continue your learning journey',
        'Enrolled': 'Enrolled',
        'Completed': 'Completed',
        'Wishlist': 'Wishlist',
        'Complete': 'Complete',
        'By': 'By',
        'Lessons': 'Lessons',
        'Start Learning': 'Start Learning',
        'Continue': 'Continue',
        'Last accessed': 'Last accessed',
        'Get Certificate': 'Get Certificate',
        'Write Review': 'Write Review',
        'Enroll Now': 'Enroll Now',
        'No courses enrolled': 'No courses enrolled',
        'No courses completed': 'No courses completed',
        'Wishlist is empty': 'Wishlist is empty',
        'No courses found': 'No courses found',
        'Start your learning journey by enrolling in courses': 'Start your learning journey by enrolling in courses',
        'Complete your enrolled courses to see them here': 'Complete your enrolled courses to see them here',
        'Add courses to your wishlist to save them for later': 'Add courses to your wishlist to save them for later',
        'Explore our course catalog to find interesting courses': 'Explore our course catalog to find interesting courses',
        'Browse Courses': 'Browse Courses',
        'Track your learning journey and achievements': 'Track your learning journey and achievements',
        'Average Progress': 'Average Progress',
        'Weekly Learning Activity': 'Weekly Learning Activity',
        'Learning Hours': 'Learning Hours',
        'Courses Accessed': 'Courses Accessed',
        'Course Progress': 'Course Progress',
        'View All Courses': 'View All Courses',
        'Achievements': 'Achievements',
        'Completed': 'Completed',
        'Earned on': 'Earned on',
        'Manage your account preferences and settings': 'Manage your account preferences and settings',
        'Profile Settings': 'Profile Settings',
        'Manage your personal information and profile details': 'Manage your personal information and profile details',
        'Full Name': 'Full Name',
        'Enter your full name': 'Enter your full name',
        'Email Address': 'Email Address',
        'Enter your email address': 'Enter your email address',
        'Phone Number': 'Phone Number',
        'Enter your phone number': 'Enter your phone number',
        'Location': 'Location',
        'Enter your location': 'Enter your location',
        'Bio': 'Bio',
        'Tell us about yourself': 'Tell us about yourself',
        'Website': 'Website',
        'Preferred Language': 'Preferred Language',
        'Timezone': 'Timezone',
        'Cancel': 'Cancel',
        'Save Changes': 'Save Changes',
        'Preferences': 'Preferences',
        'Customize your learning experience and notifications': 'Customize your learning experience and notifications',
        'Email Notifications': 'Email Notifications',
        'Receive updates and announcements via email': 'Receive updates and announcements via email',
        'Push Notifications': 'Push Notifications',
        'Get instant notifications in your browser': 'Get instant notifications in your browser',
        'SMS Notifications': 'SMS Notifications',
        'Receive important updates via SMS': 'Receive important updates via SMS',
        'Course Updates': 'Course Updates',
        'Get notified about new content in your enrolled courses': 'Get notified about new content in your enrolled courses',
        'Newsletter': 'Newsletter',
        'Receive weekly learning tips and course recommendations': 'Receive weekly learning tips and course recommendations',
        'Learning Reminders': 'Learning Reminders',
        'Get reminders to continue your learning journey': 'Get reminders to continue your learning journey',
        'Dark Mode': 'Dark Mode',
        'Switch between light and dark theme': 'Switch between light and dark theme',
        'Security': 'Security',
        'Manage your account security and privacy settings': 'Manage your account security and privacy settings',
        'Two-Factor Authentication': 'Two-Factor Authentication',
        'Add an extra layer of security to your account': 'Add an extra layer of security to your account',
        'Login Alerts': 'Login Alerts',
        'Get notified when someone logs into your account': 'Get notified when someone logs into your account',
        'Device Management': 'Device Management',
        'View and manage devices that have access to your account': 'View and manage devices that have access to your account',
        'Change Password': 'Change Password',
        'Update your password regularly to keep your account secure': 'Update your password regularly to keep your account secure',
        'Enable': 'Enable',
        'Disable': 'Disable',
        'Billing & Plan': 'Billing & Plan',
        'Manage your subscription and billing information': 'Manage your subscription and billing information',
        'Upgrade to Premium': 'Upgrade to Premium',
        'Billing Information': 'Billing Information',
        'Next Billing Date': 'Next Billing Date',
        'Payment Method': 'Payment Method',
        'Not set': 'Not set'
    },
    bn: {
        'Home': 'হোম',
        'Courses': 'কোর্সসমূহ',
        'Instructors': 'ইন্সট্রাক্টর',
        'About': 'আমাদের সম্পর্কে',
        'Search Courses...': 'কোর্স খুঁজুন...',
        'My Profile': 'আমার প্রোফাইল',
        'My Courses': 'আমার কোর্সসমূহ',
        'Learning Progress': 'শেখার অগ্রগতি',
        'Settings': 'সেটিংস',
        'Logout': 'লগআউট',
        'Login': 'লগইন',
        'Get Started': 'শুরু করুন',
        'Manage your profile and track your learning journey': 'আপনার প্রোফাইল পরিচালনা করুন এবং আপনার শেখার যাত্রা ট্র্যাক করুন',
        'Member since': 'সদস্য sejak',
        'Courses Enrolled': 'নিবন্ধিত কোর্স',
        'Courses Completed': 'সম্পন্ন কোর্স',
        'Learning Hours': 'শেখার ঘন্টা',
        'Day Streak': 'দিনের স্ট্রিক',
        'Recent Activity': 'সাম্প্রতিক কার্যকলাপ',
        'View All': 'সব দেখুন',
        'Progress': 'অগ্রগতি',
        'Quick Actions': 'দ্রুত কাজ',
        'Certificates': 'সনদপত্র',
        'Edit Profile': 'প্রোফাইল সম্পাদনা',
        'Continue your learning journey': 'আপনার শেখার যাত্রা চালিয়ে যান',
        'Enrolled': 'নিবন্ধিত',
        'Completed': 'সম্পন্ন',
        'Wishlist': 'ইচ্ছেতালিকা',
        'Complete': 'সম্পূর্ণ',
        'By': 'দ্বারা',
        'Lessons': 'পাঠ',
        'Start Learning': 'শেখা শুরু করুন',
        'Continue': 'চালিয়ে যান',
        'Last accessed': 'সর্বশেষ অ্যাক্সেস',
        'Get Certificate': 'সনদপত্র নিন',
        'Write Review': 'রিভিউ লিখুন',
        'Enroll Now': 'এখনই নিবন্ধন করুন',
        'No courses enrolled': 'কোন কোর্স নিবন্ধিত নেই',
        'No courses completed': 'কোন কোর্স সম্পন্ন হয়নি',
        'Wishlist is empty': 'ইচ্ছেতালিকা খালি',
        'No courses found': 'কোন কোর্স পাওয়া যায়নি',
        'Start your learning journey by enrolling in courses': 'কোর্সে নিবন্ধন করে আপনার শেখার যাত্রা শুরু করুন',
        'Complete your enrolled courses to see them here': 'এখানে দেখতে আপনার নিবন্ধিত কোর্সগুলি সম্পন্ন করুন',
        'Add courses to your wishlist to save them for later': 'পরে ব্যবহারের জন্য কোর্সগুলি আপনার ইচ্ছেতালিকায় যোগ করুন',
        'Explore our course catalog to find interesting courses': 'আকর্ষণীয় কোর্স খুঁজতে আমাদের কোর্স ক্যাটালগ অন্বেষণ করুন',
        'Browse Courses': 'কোর্স ব্রাউজ করুন',
        'Track your learning journey and achievements': 'আপনার শেখার যাত্রা এবং অর্জন ট্র্যাক করুন',
        'Average Progress': 'গড় অগ্রগতি',
        'Weekly Learning Activity': 'সাপ্তাহিক শেখার কার্যকলাপ',
        'Learning Hours': 'শেখার ঘন্টা',
        'Courses Accessed': 'অ্যাক্সেস করা কোর্স',
        'Course Progress': 'কোর্স অগ্রগতি',
        'View All Courses': 'সব কোর্স দেখুন',
        'Achievements': 'অর্জন',
        'Completed': 'সম্পন্ন',
        'Earned on': 'অর্জিত হয়েছে',
        'Manage your account preferences and settings': 'আপনার অ্যাকাউন্ট পছন্দ এবং সেটিংস পরিচালনা করুন',
        'Profile Settings': 'প্রোফাইল সেটিংস',
        'Manage your personal information and profile details': 'আপনার ব্যক্তিগত তথ্য এবং প্রোফাইল বিবরণ পরিচালনা করুন',
        'Full Name': 'পুরো নাম',
        'Enter your full name': 'আপনার পুরো নাম লিখুন',
        'Email Address': 'ইমেল ঠিকানা',
        'Enter your email address': 'আপনার ইমেল ঠিকানা লিখুন',
        'Phone Number': 'ফোন নম্বর',
        'Enter your phone number': 'আপনার ফোন নম্বর লিখুন',
        'Location': 'অবস্থান',
        'Enter your location': 'আপনার অবস্থান লিখুন',
        'Bio': 'বায়ো',
        'Tell us about yourself': 'আমাদের আপনার সম্পর্কে বলুন',
        'Website': 'ওয়েবসাইট',
        'Preferred Language': 'পছন্দের ভাষা',
        'Timezone': 'সময় অঞ্চল',
        'Cancel': 'বাতিল',
        'Save Changes': 'পরিবর্তনগুলি সংরক্ষণ করুন',
        'Preferences': 'পছন্দসমূহ',
        'Customize your learning experience and notifications': 'আপনার শেখার অভিজ্ঞতা এবং বিজ্ঞপ্তিগুলি কাস্টমাইজ করুন',
        'Email Notifications': 'ইমেল বিজ্ঞপ্তি',
        'Receive updates and announcements via email': 'ইমেলের মাধ্যমে আপডেট এবং ঘোষণা পান',
        'Push Notifications': 'পুশ বিজ্ঞপ্তি',
        'Get instant notifications in your browser': 'আপনার ব্রাউজারে তাত্ক্ষণিক বিজ্ঞপ্তি পান',
        'SMS Notifications': 'এসএমএস বিজ্ঞপ্তি',
        'Receive important updates via SMS': 'এসএমএসের মাধ্যমে গুরুত্বপূর্ণ আপডেট পান',
        'Course Updates': 'কোর্স আপডেট',
        'Get notified about new content in your enrolled courses': 'আপনার নিবন্ধিত কোর্সগুলিতে নতুন বিষয়বস্তু সম্পর্কে বিজ্ঞপ্তি পান',
        'Newsletter': 'নিউজলেটার',
        'Receive weekly learning tips and course recommendations': 'সাপ্তাহিক শেখার টিপস এবং কোর্স সুপারিশ পান',
        'Learning Reminders': 'শেখার অনুস্মারক',
        'Get reminders to continue your learning journey': 'আপনার শেখার যাত্রা চালিয়ে যাওয়ার জন্য অনুস্মারক পান',
        'Dark Mode': 'ডার্ক মোড',
        'Switch between light and dark theme': 'হালকা এবং গাঢ় থিমের মধ্যে স্যুইচ করুন',
        'Security': 'নিরাপত্তা',
        'Manage your account security and privacy settings': 'আপনার অ্যাকাউন্ট নিরাপত্তা এবং গোপনীয়তা সেটিংস পরিচালনা করুন',
        'Two-Factor Authentication': 'দুই-ফ্যাক্টর প্রমাণীকরণ',
        'Add an extra layer of security to your account': 'আপনার অ্যাকাউন্টে একটি অতিরিক্ত নিরাপত্তা স্তর যোগ করুন',
        'Login Alerts': 'লগইন সতর্কতা',
        'Get notified when someone logs into your account': 'কেউ আপনার অ্যাকাউন্টে লগ ইন করলে বিজ্ঞপ্তি পান',
        'Device Management': 'ডিভাইস ব্যবস্থাপনা',
        'View and manage devices that have access to your account': 'আপনার অ্যাকাউন্টে অ্যাক্সেস আছে এমন ডিভাইসগুলি দেখুন এবং পরিচালনা করুন',
        'Change Password': 'পাসওয়ার্ড পরিবর্তন করুন',
        'Update your password regularly to keep your account secure': 'আপনার অ্যাকাউন্ট সুরক্ষিত রাখতে নিয়মিত আপনার পাসওয়ার্ড আপডেট করুন',
        'Enable': 'সক্ষম করুন',
        'Disable': 'অক্ষম করুন',
        'Billing & Plan': 'বিলিং এবং পরিকল্পনা',
        'Manage your subscription and billing information': 'আপনার সাবস্ক্রিপশন এবং বিলিং তথ্য পরিচালনা করুন',
        'Upgrade to Premium': 'প্রিমিয়ামে আপগ্রেড করুন',
        'Billing Information': 'বিলিং তথ্য',
        'Next Billing Date': 'পরবর্তী বিলিং তারিখ',
        'Payment Method': 'পেমেন্ট পদ্ধতি',
        'Not set': 'সেট করা নেই'
    }
};

// Global translation function
const globalT = (key, replacements = {}) => {
    const currentLang = localStorage.getItem('preferredLanguage') || 'bn';
    let translated = translations[currentLang]?.[key] || key;
    
    Object.keys(replacements).forEach(replacementKey => {
        translated = translated.replace(`{${replacementKey}}`, replacements[replacementKey]);
    });
    
    return translated;
};

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
    return currentLanguage;
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
    return theme;
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
        document.body.classList.remove('light-theme');
    }
};

// Combined initialization function
const initializeAppSystems = () => {
    initializeBengaliFonts();
    const currentLang = initializeLanguageSystem();
    const currentTheme = initializeThemeSystem();
    
    return { currentLang, currentTheme };
};

// Create and provide translation system to Vue app
const provideTranslation = (vueApp) => {
    // Add global translation function
    vueApp.config.globalProperties.t = globalT;
    
    // Add reactive language state
    vueApp.config.globalProperties.currentLanguage = localStorage.getItem('preferredLanguage') || 'bn';
    vueApp.config.globalProperties.currentTheme = localStorage.getItem('preferredTheme') || 'light';
    
    // Add language switching method
    vueApp.config.globalProperties.switchLanguage = (lang) => {
        if (lang === 'en' || lang === 'bn') {
            localStorage.setItem('preferredLanguage', lang);
            vueApp.config.globalProperties.currentLanguage = lang;
            
            // Update body class
            if (lang === 'bn') {
                document.body.classList.add('bn-lang');
            } else {
                document.body.classList.remove('bn-lang');
            }
            
            // Update page title
            document.title = lang === 'bn' 
                ? 'পাঠশালা LMS - জ্ঞানকে শক্তিতে রূপান্তর'
                : 'Pathshala LMS - Empower Minds';
            
            // Dispatch event for all components to update
            window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
            
            console.log(`🌐 Language switched to: ${lang}`);
        }
    };
    
    // Add theme switching method
    vueApp.config.globalProperties.switchTheme = (theme) => {
        if (theme === 'light' || theme === 'dark') {
            localStorage.setItem('preferredTheme', theme);
            vueApp.config.globalProperties.currentTheme = theme;
            applyGlobalTheme(theme);
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
            console.log(`🎨 Theme switched to: ${theme}`);
        }
    };
};

// Initialize Inertia app
createInertiaApp({
    title: (title) => {
        const currentLanguage = localStorage.getItem('preferredLanguage') || 'bn';
        const siteName = currentLanguage === 'bn' ? 'পাঠশালা LMS - জ্ঞানকে শক্তিতে রূপান্তর' : 'Pathshala LMS - Empower Minds';
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
            `./Pages/Errors/${name}.vue`,
            `./Pages/StudentProfile/${name}.vue`,
            `./Pages/StudentProfile/${name}/Index.vue`
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
        console.log('🚀 Starting Inertia app setup...');
        
        // Safe props access with fallback
        const safeProps = props || { initialPage: { props: { auth: { user: null }, ziggy: {} } } };
        
        // Initialize all app systems with error handling
        let currentLang = 'bn';
        let currentTheme = 'light';
        
        try {
            const systems = initializeAppSystems();
            currentLang = systems?.currentLang || 'bn';
            currentTheme = systems?.currentTheme || 'light';
            console.log('✅ App systems initialized');
        } catch (error) {
            console.error('❌ Error initializing app systems:', error);
            // Set defaults
            currentLang = localStorage.getItem('preferredLanguage') || 'bn';
            currentTheme = localStorage.getItem('preferredTheme') || 'light';
        }
        
        // Create Vue app with error boundary
        const vueApp = createApp({ 
            render: () => {
                try {
                    return h(App, safeProps);
                } catch (error) {
                    console.error('❌ Error rendering Inertia app:', error);
                    return h('div', { class: 'p-4 bg-red-50 text-red-600' }, [
                        'Application error. Please refresh the page.'
                    ]);
                }
            }
        });
        
        // Use Inertia plugin
        vueApp.use(plugin);
        
        // Provide translation globally to all components with error handling
        try {
            provideTranslation(vueApp);
            console.log('✅ Translation system provided');
        } catch (error) {
            console.error('❌ Error providing translation:', error);
        }
        
        // Add global mixin for translation with fallback
        vueApp.mixin({
            methods: {
                t(key, replacements = {}) {
                    try {
                        return globalT(key, replacements);
                    } catch (error) {
                        console.warn(`Translation error for key "${key}":`, error);
                        return key; // Return the key as fallback
                    }
                }
            },
            mounted() {
                // Listen for language changes from other components
                window.addEventListener('languageChanged', (event) => {
                    try {
                        this.$forceUpdate?.();
                    } catch (error) {
                        console.warn('Error in language change handler:', error);
                    }
                });
            }
        });
        
        // Use Ziggy for route() function with robust error handling
        try {
            const ziggyData = safeProps.initialPage?.props?.ziggy;
            if (ziggyData && ziggyData.url) {
                vueApp.use(ZiggyVue, {
                    ...ziggyData,
                    location: new URL(ziggyData.url),
                });
                console.log('✅ Ziggy routes loaded successfully');
            } else {
                console.warn('⚠️ Ziggy routes not available, creating fallback route function');
                
                // Enhanced fallback route function
                vueApp.config.globalProperties.route = (name, params = {}, absolute = true) => {
                    try {
                        // Enhanced route mapping with more routes
                        const routeMap = {
                            // Core routes
                            'home': '/',
                            'login': '/login',
                            'registration': '/registration',
                            'logout': '/logout',
                            
                            // Student routes
                            'student.login': '/student-login',
                            'student.registration': '/student-registration',
                            'student.dashboard': '/student',
                            'student.profile': '/student/profile',
                            'student.my-courses': '/student/my-courses',
                            'student.assignments': '/student/assignments',
                            'student.schedule': '/student/schedule',
                            'student.grades': '/student/grades',
                            'student.progress': '/student/progress',
                            'student.settings': '/student/settings',
                            'student.profile.new': '/student-profile',
                            'my-courses.new': '/my-courses',
                            'learning-progress.new': '/learning-progress',
                            'settings.new': '/settings',
                            
                            // Teacher routes
                            'teacher.dashboard': '/teacher',
                            'teacher.portal': '/teacher/portal',
                            'teacher.classes': '/teacher/classes',
                            'teacher.resources': '/teacher/resources',
                            'teacher.assignments': '/teacher/assignments',
                            'teacher.analytics': '/teacher/analytics',
                            'teacher.settings': '/teacher/settings',
                            
                            // Admin routes
                            'admin': '/admin',
                            'super.admin': '/super-admin',
                            
                            // Frontend routes
                            'courses': '/courses',
                            'instructors': '/instructors',
                            'about': '/about',
                            'contact': '/contact',
                            'blog': '/blog',
                            
                            // Auth routes
                            'phone.verification': '/phone-verification',
                            'send.otp': '/send-otp',
                            'verify.otp': '/verify-otp'
                        };
                        
                        let url = routeMap[name];
                        
                        if (!url) {
                            console.warn(`Route "${name}" not found in fallback map`);
                            return absolute ? window.location.origin + '/' : '/';
                        }
                        
                        // Handle parameters - enhanced replacement
                        if (params && typeof params === 'object') {
                            Object.keys(params).forEach(key => {
                                const placeholder = `{${key}}`;
                                if (url.includes(placeholder)) {
                                    url = url.replace(new RegExp(placeholder, 'g'), params[key]);
                                }
                            });
                            
                            // Add query parameters for remaining params
                            const remainingParams = { ...params };
                            Object.keys(params).forEach(key => {
                                if (url.includes(`{${key}}`)) {
                                    delete remainingParams[key];
                                }
                            });
                            
                            const queryParams = new URLSearchParams(remainingParams).toString();
                            if (queryParams) {
                                url += (url.includes('?') ? '&' : '?') + queryParams;
                            }
                        }
                        
                        return absolute ? window.location.origin + url : url;
                    } catch (error) {
                        console.error('Error in fallback route function:', error);
                        return window.location.origin + '/';
                    }
                };
            }
        } catch (error) {
            console.error('❌ Error setting up Ziggy:', error);
            
            // Emergency fallback
            vueApp.config.globalProperties.route = (name) => {
                console.warn(`Using emergency fallback for route: ${name}`);
                return window.location.origin + '/';
            };
        }
        
        // Add global error handler
        vueApp.config.errorHandler = (err, vm, info) => {
            console.error('Vue error caught:', err);
            console.error('Component:', vm);
            console.error('Info:', info);
        };
        
        // Mount the app with error handling
        try {
            vueApp.mount(el);
            console.log('✅ Inertia.js app mounted successfully!');
            console.log('🌐 Current language:', currentLang);
            console.log('🎨 Current theme:', currentTheme);
            console.log('👤 User auth:', safeProps.initialPage?.props?.auth);
        } catch (mountError) {
            console.error('❌ Failed to mount Vue app:', mountError);
            
            // Emergency rendering
            const emergencyApp = createApp({
                render() {
                    return h('div', { 
                        class: 'p-6 bg-red-50 border border-red-200 rounded-lg m-4' 
                    }, [
                        h('h1', { class: 'text-xl font-bold text-red-600 mb-2' }, 'Application Error'),
                        h('p', { class: 'text-red-700 mb-4' }, 'Failed to load the application. Please refresh the page.'),
                        h('button', { 
                            class: 'px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700',
                            onClick: () => window.location.reload()
                        }, 'Refresh Page')
                    ]);
                }
            });
            
            emergencyApp.mount(el);
        }
        
        // Global error listener
        window.addEventListener('error', (event) => {
            console.error('Global error:', event.error);
        });
        
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled promise rejection:', event.reason);
        });
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
        siteTitle.textContent = 'পাঠশালা LMS - জ্ঞানকে শক্তিতে রূপান্তর';
    } else if (siteTitle) {
        siteTitle.textContent = 'Pathshala LMS - Empower Minds';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeAppSystems();
});

// Export language functions for global use
window.PathshalaLMS = {
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

console.log('🚀 Pathshala LMS app initialized with Bengali support');