<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pathshala LMS - Online Learning Platform</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" href="../../public/assets/img/pathshala-logo.ico">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback for production -->
    @production
        <style>
            /* Basic loading styles */
            .loading-screen {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background: #f8f9fa;
            }
            .spinner-border {
                width: 3rem;
                height: 3rem;
            }
        </style>
    @endproduction
</head>
<body>
    <div id="app">
        <!-- Vue app will mount here -->
        <div class="loading-screen">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading SkillGro...</p>
            </div>
        </div>
    </div>

    <!-- Fallback for no-JavaScript -->
    <noscript>
        <div class="container mt-5">
            <div class="alert alert-warning text-center">
                <h4>JavaScript Required</h4>
                <p>Please enable JavaScript to use SkillGro LMS.</p>
            </div>
        </div>
    </noscript>

    <script>
        // Remove loading screen when Vue app mounts
        document.addEventListener('DOMContentLoaded', function() {
            // This will be removed by Vue when the app mounts
            console.log('SkillGro LMS Loading...');
        });
    </script>
</body>
</html>