<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Video Player' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #000;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        
        .player-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
        }
        
        .video-frame {
            width: 100%;
            height: 100%;
            border: none;
            background: #000;
        }
        
        /* Hide all scrollbars and browser UI */
        ::-webkit-scrollbar {
            display: none;
        }
        
        /* Hide YouTube elements */
        .ytp-chrome-top,
        .ytp-title-link,
        .ytp-title-text,
        .ytp-title-channel,
        .ytp-share-button,
        .ytp-watch-later-button,
        .ytp-button.ytp-youtube-button,
        .ytp-watermark {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
    </style>
</head>
<body>
    <div class="player-container">
        <iframe 
            class="video-frame"
            src="https://www.youtube-nocookie.com/embed/{{ $videoId }}?autoplay=1&rel=0&modestbranding=1&controls=1&showinfo=0&iv_load_policy=3&fs=1&disablekb=1&playsinline=1&enablejsapi=1&origin={{ url('/') }}&widget_referrer={{ url('/') }}&cc_load_policy=0&color=white&hl=en&cc_lang_pref=en&version=3&loop=0&playlist={{ $videoId }}"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
        ></iframe>
    </div>

    <script>
        // Hide YouTube elements using JavaScript
        function hideYouTubeElements() {
            const style = document.createElement('style');
            style.textContent = `
                .ytp-chrome-top,
                .ytp-title-link,
                .ytp-title-text,
                .ytp-title-channel,
                .ytp-share-button,
                .ytp-watch-later-button,
                .ytp-button.ytp-youtube-button,
                .ytp-watermark {
                    display: none !important;
                    visibility: hidden !important;
                    opacity: 0 !important;
                }
            `;
            document.head.appendChild(style);
        }

        // Try to hide elements periodically
        setInterval(hideYouTubeElements, 1000);
        hideYouTubeElements();
        
        // Also try to hide elements when iframe loads
        document.querySelector('.video-frame').addEventListener('load', function() {
            setTimeout(hideYouTubeElements, 2000);
        });
    </script>
</body>
</html>