// resources/js/utils/languageStore.js
import { ref, watch } from 'vue';

// Global reactive language state
const currentLanguage = ref(localStorage.getItem('preferredLanguage') || 'bn');

// Translation function
export const t = (key, replacements = {}) => {
  const translations = {
    en: {
      // Instructors Page
      'All Instructors': 'All Instructors',
      'Home': 'Home',
      'Instructors': 'Instructors',
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
      'Become an Instructor': 'Become an Instructor',
      'Full Name *': 'Full Name *',
      'Enter your full name': 'Enter your full name',
      'Username *': 'Username *',
      'Choose a username': 'Choose a username',
      'Email Address *': 'Email Address *',
      'Enter your email': 'Enter your email',
      'Date of Birth *': 'Date of Birth *',
      'Educational Qualification *': 'Educational Qualification *',
      'Select Qualification': 'Select Qualification',
      'Institute *': 'Institute *',
      'Your educational institute': 'Your educational institute',
      'Teaching Experience': 'Teaching Experience',
      'e.g., 5 years in Mathematics': 'e.g., 5 years in Mathematics',
      'Password *': 'Password *',
      'Create a password': 'Create a password',
      'Confirm Password *': 'Confirm Password *',
      'Confirm your password': 'Confirm your password',
      'Cancel': 'Cancel',
      'Submit Request': 'Submit Request',
      'Submitting...': 'Submitting...',
      'Request Submitted Successfully!': 'Request Submitted Successfully!',
      'Your instructor application has been submitted and is pending approval from our admin team. You\'ll receive an email notification once your request is processed.': 'Your instructor application has been submitted and is pending approval from our admin team. You\'ll receive an email notification once your request is processed.',
      'OK': 'OK'
    },
    bn: {
      // Instructors Page
      'All Instructors': 'সমস্ত ইন্সট্রাক্টর',
      'Home': 'হোম',
      'Instructors': 'ইন্সট্রাক্টর',
      'Search instructors by name, qualification, or institute...': 'ইন্সট্রাক্টরদের নাম, যোগ্যতা বা প্রতিষ্ঠান অনুসারে খুঁজুন...',
      'All Specializations': 'সমস্ত বিশেষীকরণ',
      'Showing': 'দেখানো হচ্ছে',
      'of': 'এর',
      'instructors': 'ইন্সট্রাক্টর',
      'Loading instructors...': 'ইন্সট্রাক্টর লোড হচ্ছে...',
      'Meet Our Expert Instructors': 'আমাদের বিশেষজ্ঞ ইন্সট্রাক্টরদের সাথে পরিচিত হোন',
      'Expertise': 'দক্ষতা',
      'Education': 'শিক্ষাগত যোগ্যতা',
      'Courses': 'কোর্স',
      'Students': 'শিক্ষার্থী',
      'Rating': 'রেটিং',
      'View Full Profile': 'সম্পূর্ণ প্রোফাইল দেখুন',
      'No Instructors Found': 'কোনো ইন্সট্রাক্টর পাওয়া যায়নি',
      'We couldn\'t find any instructors matching your criteria.': 'আপনার শর্তানুসারে কোনো ইন্সট্রাক্টর পাওয়া যায়নি।',
      'Clear Filters': 'ফিল্টার সরান',
      'Load More Instructors': 'আরও ইন্সট্রাক্টর লোড করুন',
      'Loading...': 'লোড হচ্ছে...',
      'Become an Instructor Today': 'আজই একজন ইন্সট্রাক্টর হন',
      'Join our team of expert educators and share your knowledge with thousands of eager learners worldwide.': 'আমাদের বিশেষজ্ঞ শিক্ষকদের দলে যোগ দিন এবং বিশ্বজুড়ে হাজার হাজার আগ্রহী শিক্ষার্থীর সাথে আপনার জ্ঞান শেয়ার করুন।',
      'Join as Instructor': 'ইন্সট্রাক্টর হিসেবে যোগ দিন',
      'Join Our Teaching Community': 'আমাদের শিক্ষণ সম্প্রদায়ে যোগ দিন',
      'Become an Instructor': 'ইন্সট্রাক্টর হন',
      'Full Name *': 'পুরো নাম *',
      'Enter your full name': 'আপনার পুরো নাম লিখুন',
      'Username *': 'ব্যবহারকারীর নাম *',
      'Choose a username': 'একটি ব্যবহারকারীর নাম নির্বাচন করুন',
      'Email Address *': 'ইমেইল ঠিকানা *',
      'Enter your email': 'আপনার ইমেইল লিখুন',
      'Date of Birth *': 'জন্ম তারিখ *',
      'Educational Qualification *': 'শিক্ষাগত যোগ্যতা *',
      'Select Qualification': 'যোগ্যতা নির্বাচন করুন',
      'Institute *': 'শিক্ষাপ্রতিষ্ঠান *',
      'Your educational institute': 'আপনার শিক্ষাপ্রতিষ্ঠান',
      'Teaching Experience': 'শিক্ষণ অভিজ্ঞতা',
      'e.g., 5 years in Mathematics': 'যেমন, গণিতে ৫ বছরের অভিজ্ঞতা',
      'Password *': 'পাসওয়ার্ড *',
      'Create a password': 'একটি পাসওয়ার্ড তৈরি করুন',
      'Confirm Password *': 'পাসওয়ার্ড নিশ্চিত করুন *',
      'Confirm your password': 'আপনার পাসওয়ার্ড নিশ্চিত করুন',
      'Cancel': 'বাতিল',
      'Submit Request': 'অনুরোধ জমা দিন',
      'Submitting...': 'জমা দেওয়া হচ্ছে...',
      'Request Submitted Successfully!': 'অনুরোধ সফলভাবে জমা দেওয়া হয়েছে!',
      'Your instructor application has been submitted and is pending approval from our admin team. You\'ll receive an email notification once your request is processed.': 'আপনার ইন্সট্রাক্টর আবেদন জমা দেওয়া হয়েছে এবং আমাদের অ্যাডমিন টিমের অনুমোদনের অপেক্ষায় রয়েছে। আপনার অনুরোধ প্রক্রিয়া করা হলে আপনাকে একটি ইমেইল নোটিফিকেশন পাঠানো হবে।',
      'OK': 'ঠিক আছে'
    }
  };

  let translated = translations[currentLanguage.value]?.[key] || key;
  
  Object.keys(replacements).forEach(replacementKey => {
    translated = translated.replace(`{${replacementKey}}`, replacements[replacementKey]);
  });
  
  return translated;
};

// Language change function
export const switchLanguage = (lang) => {
  currentLanguage.value = lang;
  localStorage.setItem('preferredLanguage', lang);
  
  // Update body class for Bengali fonts
  if (lang === 'bn') {
    document.body.classList.add('bn-lang');
  } else {
    document.body.classList.remove('bn-lang');
  }
  
  // Dispatch global event
  window.dispatchEvent(new CustomEvent('languageChanged', { 
    detail: { language: lang } 
  }));
  
  console.log('🌐 Language changed to:', lang);
};

// Get current language
export const getCurrentLanguage = () => currentLanguage.value;

// Watch for language changes and update page title
watch(currentLanguage, (newLang) => {
  const siteName = newLang === 'bn' ? 'স্কিলগ্রো - অনলাইন লার্নিং প্ল্যাটফর্ম' : 'SkillGro - Online Learning Platform';
  document.title = siteName;
});

// Initialize body class
if (currentLanguage.value === 'bn') {
  document.body.classList.add('bn-lang');
}