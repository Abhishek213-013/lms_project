<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'Pathshala LMS') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/pathshala-logo.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Nunito Sans + Noto Sans Bengali for Bengali support -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Summernote -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Inertia Head -->
    @inertiaHead

    <!-- Emergency Font Detection Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Test if Bengali fonts are loading properly
            setTimeout(function() {
                const testElement = document.createElement('div');
                testElement.style.fontFamily = 'Noto Sans Bengali, Nunito Sans, Arial, sans-serif';
                testElement.style.position = 'absolute';
                testElement.style.left = '-9999px';
                testElement.style.fontSize = '40px';
                testElement.innerHTML = 'আমি বাংলায় গান গাই'; // Bengali test text
                document.body.appendChild(testElement);
                
                const testElement2 = document.createElement('div');
                testElement2.style.fontFamily = 'Arial, sans-serif';
                testElement2.style.position = 'absolute';
                testElement2.style.left = '-9999px';
                testElement2.style.fontSize = '40px';
                testElement2.innerHTML = 'আমি বাংলায় গান গাই'; // Same Bengali text
                document.body.appendChild(testElement2);
                
                // If both have same width, Bengali font failed
                if (Math.abs(testElement.offsetWidth - testElement2.offsetWidth) < 10) {
                    console.warn('⚠️ Bengali fonts not loading, applying emergency fallback');
                    document.body.classList.add('font-fallback-bengali');
                } else {
                    console.log('✅ Bengali fonts loaded successfully');
                }
                
                document.body.removeChild(testElement);
                document.body.removeChild(testElement2);
            }, 1000);
        });
    </script>
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>