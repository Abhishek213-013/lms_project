// resources/js/composables/useTranslation.js
import { ref, computed, onMounted, provide, inject } from 'vue'

// Your translations object
export const translations = {
  en: {
    // Header & Navigation
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
    
    // Instructors Page
    'All Instructors': 'All Instructors',
    'Search instructors by name, qualification, or institute...': 'Search instructors by name, qualification, or institute...',
    'All Specializations': 'All Specializations',
    'Showing': 'Showing',
    'of': 'of',
    'instructors': 'instructors',
    'Loading instructors...': 'Loading instructors...',
    'Meet Our Expert Instructors': 'Meet Our Expert Instructors',
    'Expertise': 'Expertise',
    'Education': 'Education',
    'Courses': 'Courses',
    'Students': 'Students',
    'Rating': 'Rating',
    'View Full Profile': 'View Full Profile',
    'No Instructors Found': 'No Instructors Found',
    'We couldn\'t find any instructors matching your criteria.': 'We couldn\'t find any instructors matching your criteria.',
    'Clear Filters': 'Clear Filters',
    'Load More Instructors': 'Load More Instructors',
    'Loading...': 'Loading...',
    'Become an Instructor Today': 'Become an Instructor Today',
    'Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.': 'Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.',
    'Join as Instructor': 'Join as Instructor',
    'Join Our Teaching Community': 'Join Our Teaching Community',
    
    // Instructor Modal
    'Become an Instructor': 'Become an Instructor',
    'Full Name': 'Full Name',
    'Enter your full name': 'Enter your full name',
    'Username': 'Username',
    'Choose a username': 'Choose a username',
    'Email Address': 'Email Address',
    'Enter your email': 'Enter your email',
    'Date of Birth': 'Date of Birth',
    'Educational Qualification': 'Educational Qualification',
    'Select Qualification': 'Select Qualification',
    'Higher Secondary Certificate (HSC)': 'Higher Secondary Certificate (HSC)',
    'Bachelor of Science (BSC)': 'Bachelor of Science (BSC)',
    'Bachelor of Arts (BA)': 'Bachelor of Arts (BA)',
    'Master of Arts (MA)': 'Master of Arts (MA)',
    'Master of Science (MSC)': 'Master of Science (MSC)',
    'Doctor of Philosophy (PhD)': 'Doctor of Philosophy (PhD)',
    'Other': 'Other',
    'Institute': 'Institute',
    'Your educational institute': 'Your educational institute',
    'Teaching Experience': 'Teaching Experience',
    'e.g., 5 years in Mathematics': 'e.g., 5 years in Mathematics',
    'Password': 'Password',
    'Create a password': 'Create a password',
    'Confirm Password': 'Confirm Password',
    'Confirm your password': 'Confirm your password',
    'Cancel': 'Cancel',
    'Submitting...': 'Submitting...',
    'Submit Request': 'Submit Request',
    'Request Submitted Successfully!': 'Request Submitted Successfully!',
    'Your instructor application has been submitted and is pending approval from our admin team. You\'ll receive an email notification once your request is processed.': 'Your instructor application has been submitted and is pending approval from our admin team. You\'ll receive an email notification once your request is processed.',
    'OK': 'OK',
    'Something went wrong. Please try again.': 'Something went wrong. Please try again.',
    'Network error. Please check your connection and try again.': 'Network error. Please check your connection and try again.',
    
    // Expertise translations
    'Science': 'Science',
    'English': 'English',
    'Mathematics': 'Mathematics',
    'Bangla': 'Bangla',
    'Sports': 'Sports',
    'Teaching Specialist': 'Teaching Specialist',
    'Teaching Degree': 'Teaching Degree'
  },
  bn: {
    // Header & Navigation
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
    
    // Instructors Page
    'All Instructors': 'সমস্ত ইন্সট্রাক্টর',
    'Search instructors by name, qualification, or institute...': 'নাম, যোগ্যতা বা প্রতিষ্ঠান দ্বারা ইন্সট্রাক্টর খুঁজুন...',
    'All Specializations': 'সমস্ত বিশেষীকরণ',
    'Showing': 'দেখানো হচ্ছে',
    'of': 'এর',
    'instructors': 'ইন্সট্রাক্টর',
    'Loading instructors...': 'ইন্সট্রাক্টর লোড হচ্ছে...',
    'Meet Our Expert Instructors': 'আমাদের বিশেষজ্ঞ ইন্সট্রাক্টরদের সাথে পরিচিত হোন',
    'Expertise': 'দক্ষতা',
    'Education': 'শিক্ষা',
    'Courses': 'কোর্স',
    'Students': 'শিক্ষার্থী',
    'Rating': 'রেটিং',
    'View Full Profile': 'সম্পূর্ণ প্রোফাইল দেখুন',
    'No Instructors Found': 'কোন ইন্সট্রাক্টর পাওয়া যায়নি',
    'We couldn\'t find any instructors matching your criteria.': 'আপনার শর্তের সাথে মিলে এমন কোন ইন্সট্রাক্টর আমরা খুঁজে পাইনি।',
    'Clear Filters': 'ফিল্টার সরান',
    'Load More Instructors': 'আরও ইন্সট্রাক্টর লোড করুন',
    'Loading...': 'লোড হচ্ছে...',
    'Become an Instructor Today': 'আজই একজন ইন্সট্রাক্টর হোন',
    'Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.': 'আমাদের বিশেষজ্ঞ শিক্ষকদের দলে যোগ দিন এবং বিশ্বব্যাপী হাজার হাজার আগ্রহী শিক্ষার্থীর সাথে আপনার জ্ঞান শেয়ার করুন।',
    'Join as Instructor': 'ইন্সট্রাক্টর হিসেবে যোগ দিন',
    'Join Our Teaching Community': 'আমাদের শিক্ষণ সম্প্রদায়ে যোগ দিন',
    
    // Instructor Modal
    'Become an Instructor': 'ইন্সট্রাক্টর হোন',
    'Full Name': 'পুরো নাম',
    'Enter your full name': 'আপনার পুরো নাম লিখুন',
    'Username': 'ব্যবহারকারীর নাম',
    'Choose a username': 'একটি ব্যবহারকারীর নাম নির্বাচন করুন',
    'Email Address': 'ইমেইল ঠিকানা',
    'Enter your email': 'আপনার ইমেইল লিখুন',
    'Date of Birth': 'জন্ম তারিখ',
    'Educational Qualification': 'শিক্ষাগত যোগ্যতা',
    'Select Qualification': 'যোগ্যতা নির্বাচন করুন',
    'Higher Secondary Certificate (HSC)': 'উচ্চ মাধ্যমিক সার্টিফিকেট (এইচএসসি)',
    'Bachelor of Science (BSC)': 'বিজ্ঞানে স্নাতক (বিএসসি)',
    'Bachelor of Arts (BA)': 'কলায় স্নাতক (বিএ)',
    'Master of Arts (MA)': 'কলায় স্নাতকোত্তর (এমএ)',
    'Master of Science (MSC)': 'বিজ্ঞানে স্নাতকোত্তর (এমএসসি)',
    'Doctor of Philosophy (PhD)': 'পিএইচডি',
    'Other': 'অন্যান্য',
    'Institute': 'প্রতিষ্ঠান',
    'Your educational institute': 'আপনার শিক্ষাগত প্রতিষ্ঠান',
    'Teaching Experience': 'শিক্ষণ অভিজ্ঞতা',
    'e.g., 5 years in Mathematics': 'উদাহরণস্বরূপ, গণিতে ৫ বছর',
    'Password': 'পাসওয়ার্ড',
    'Create a password': 'একটি পাসওয়ার্ড তৈরি করুন',
    'Confirm Password': 'পাসওয়ার্ড নিশ্চিত করুন',
    'Confirm your password': 'আপনার পাসওয়ার্ড নিশ্চিত করুন',
    'Cancel': 'বাতিল',
    'Submitting...': 'জমা দেওয়া হচ্ছে...',
    'Submit Request': 'অনুরোধ জমা দিন',
    'Request Submitted Successfully!': 'অনুরোধ সফলভাবে জমা দেওয়া হয়েছে!',
    'Your instructor application has been submitted and is pending approval from our admin team. You\'ll receive an email notification once your request is processed.': 'আপনার ইন্সট্রাক্টর আবেদন জমা দেওয়া হয়েছে এবং আমাদের অ্যাডমিন টিমের অনুমোদনের অপেক্ষায় রয়েছে। আপনার অনুরোধ প্রক্রিয়া করা হলে আপনি একটি ইমেইল নোটিফিকেশন পাবেন।',
    'OK': 'ঠিক আছে',
    'Something went wrong. Please try again.': 'কিছু সমস্যা হয়েছে। দয়া করে আবার চেষ্টা করুন।',
    'Network error. Please check your connection and try again.': 'নেটওয়ার্ক ত্রুটি। দয়া করে আপনার সংযোগ পরীক্ষা করুন এবং আবার চেষ্টা করুন।',
    
    // Expertise translations
    'Science': 'বিজ্ঞান',
    'English': 'ইংরেজি',
    'Mathematics': 'গণিত',
    'Bangla': 'বাংলা',
    'Sports': 'ক্রীড়া',
    'Teaching Specialist': 'শিক্ষণ বিশেষজ্ঞ',
    'Teaching Degree': 'শিক্ষণ ডিগ্রী'
  }
}

// Create a reactive current language
const currentLanguage = ref('bn') // Default to Bengali

// Translation function
export const t = (key, replacements = {}) => {
  let translated = translations[currentLanguage.value]?.[key] || key
  
  Object.keys(replacements).forEach(replacementKey => {
    translated = translated.replace(`{${replacementKey}}`, replacements[replacementKey])
  })
  
  return translated
}

// Switch language function
export const switchLanguage = (lang) => {
  currentLanguage.value = lang
  localStorage.setItem('preferredLanguage', lang)
  
  // Update body class for Bengali fonts
  if (lang === 'bn') {
    document.body.classList.add('bn-lang')
  } else {
    document.body.classList.remove('bn-lang')
  }
  
  // Dispatch event for other components
  window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }))
}

// Composable function
export function useTranslation() {
  // Load language preference from localStorage on mount
  onMounted(() => {
    const savedLang = localStorage.getItem('preferredLanguage')
    if (savedLang && (savedLang === 'en' || savedLang === 'bn')) {
      currentLanguage.value = savedLang
      switchLanguage(savedLang) // Apply immediately
    }
  })

  return {
    currentLanguage: computed(() => currentLanguage.value),
    t,
    switchLanguage
  }
}

// Provide/inject key
export const TranslationKey = Symbol('translation')

// Provide function for app level
export function provideTranslation(app) {
  app.provide(TranslationKey, {
    currentLanguage,
    t,
    switchLanguage
  })
}

// Inject function for components
export function useTranslationInject() {
  const translation = inject(TranslationKey)
  
  if (!translation) {
    throw new Error('Translation not provided. Make sure to call provideTranslation in your app.')
  }
  
  return translation
}