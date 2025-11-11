// resources/js/language-init.js

export function initializeGlobalLanguage() {
  // Function to ensure language parameter is always present
  const ensureLanguageParameter = () => {
    const currentUrl = new URL(window.location.href);
    const urlLang = currentUrl.searchParams.get('lang');
    const savedLang = localStorage.getItem('preferredLanguage') || 'bn';
    
    // If no lang parameter but we have saved language, update URL
    if (!urlLang && savedLang) {
      currentUrl.searchParams.set('lang', savedLang);
      window.history.replaceState({}, '', currentUrl.toString());
      console.log('🔗 Global: Added language parameter from localStorage:', savedLang);
    }
    
    // If URL has different language than saved, update localStorage
    if (urlLang && urlLang !== savedLang && ['en', 'bn'].includes(urlLang)) {
      localStorage.setItem('preferredLanguage', urlLang);
      console.log('🔗 Global: Updated localStorage from URL parameter:', urlLang);
    }
    
    // If no lang parameter and no saved language, set default
    if (!urlLang && !savedLang) {
      const defaultLang = 'bn';
      currentUrl.searchParams.set('lang', defaultLang);
      window.history.replaceState({}, '', currentUrl.toString());
      localStorage.setItem('preferredLanguage', defaultLang);
      console.log('🔗 Global: Set default language:', defaultLang);
    }
    
    return urlLang || savedLang || 'bn';
  };

  // Run immediately
  const finalLang = ensureLanguageParameter();
  
  // Apply language class immediately
  if (finalLang === 'bn') {
    document.body.classList.add('bn-lang');
    document.body.classList.remove('en-lang');
  } else {
    document.body.classList.add('en-lang');
    document.body.classList.remove('bn-lang');
  }
  
  // Also run on DOMContentLoaded for safety
  document.addEventListener('DOMContentLoaded', function() {
    ensureLanguageParameter();
    
    // Force a language change event to trigger translations
    setTimeout(() => {
      window.dispatchEvent(new CustomEvent('languageInitialized', {
        detail: { language: finalLang }
      }));
    }, 100);
  });

  console.log('🌐 Global language system initialized:', finalLang);
  return finalLang;
}

// Initialize immediately
initializeGlobalLanguage();