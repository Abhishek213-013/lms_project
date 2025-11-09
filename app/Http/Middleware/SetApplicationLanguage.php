<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetApplicationLanguage
{
    public function handle(Request $request, Closure $next)
    {
        // Get language from URL parameter, session, or default to 'en'
        $language = $request->get('lang', Session::get('lang', 'en'));
        
        // Validate language
        if (!in_array($language, ['en', 'bn'])) {
            $language = 'en';
        }
        
        // Set application locale
        App::setLocale($language);
        Session::put('lang', $language);
        
        // Share language with all views
        view()->share('currentLanguage', $language);
        view()->share('availableLanguages', ['en', 'bn']);
        
        return $next($request);
    }
}