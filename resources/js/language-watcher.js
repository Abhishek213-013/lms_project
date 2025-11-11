// resources/js/language-watcher.js

export function setupLanguageWatcher() {
  console.log('🔍 Setting up language watcher...');
  
  const handleUrlChange = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const urlLang = urlParams.get('lang');
    const savedLang = localStorage.getItem('preferredLanguage') || 'bn';
    
    if (urlLang && urlLang !== savedLang && ['en', 'bn'].includes(urlLang)) {
      console.log('🔄 URL language change detected:', urlLang);
      
      // Update localStorage
      localStorage.setItem('preferredLanguage', urlLang);
      
      // Update body classes
      if (urlLang === 'bn') {
        document.body.classList.add('bn-lang');
        document.body.classList.remove('en-lang');
      } else {
        document.body.classList.add('en-lang');
        document.body.classList.remove('bn-lang');
      }
      
      // Dispatch comprehensive language change event
      window.dispatchEvent(new CustomEvent('languageChangedFromURL', {
        detail: { 
          language: urlLang,
          source: 'url',
          timestamp: Date.now()
        }
      }));
      
      // Also dispatch the standard languageChanged event
      window.dispatchEvent(new CustomEvent('languageChanged', {
        detail: { 
          language: urlLang,
          source: 'url_watcher',
          timestamp: Date.now()
        }
      }));
      
      console.log('✅ Language updated from URL:', urlLang);
    }
  };

  // Monitor URL changes
  const originalPushState = history.pushState;
  const originalReplaceState = history.replaceState;

  history.pushState = function (...args) {
    originalPushState.apply(this, args);
    setTimeout(handleUrlChange, 50);
  };

  history.replaceState = function (...args) {
    originalReplaceState.apply(this, args);
    setTimeout(handleUrlChange, 50);
  };

  // Monitor back/forward navigation
  window.addEventListener('popstate', handleUrlChange);

  // Initial check
  setTimeout(handleUrlChange, 100);
  
  console.log('✅ Language watcher setup complete');
}