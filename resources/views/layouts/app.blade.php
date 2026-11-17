<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Settings -->
    <title>@yield('title', 'Shri Samruddhi Society') - Society Management & Loan Accounting</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Samruddhi">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">

    <!-- PWA Installer Script -->
    <script src="/js/pwa-installer.js" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <!-- Tailwind CSS (via Vite or Standalone CDN for instantaneous 100% full utility coverage) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        /* PWA App Feel Enhancements */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            -webkit-user-select: none;
        }
        input, textarea, select {
            user-select: text;
            -webkit-user-select: text;
        }
        /* Mobile safe padding for bottom bar */
        .safe-bottom-padding {
            padding-bottom: calc(4.5rem + env(safe-area-inset-bottom, 0px));
        }
        /* Modern scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Printable clean layout */
        @media print {
            .no-print {
                display: none !important;
            }
            .print-full {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased flex flex-col">

    <!-- Flash Messages Toast -->
    @if(session('success'))
        <div id="flash-success" class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3.5 rounded-xl shadow-xl transition-all duration-300 transform translate-y-0">
            <i class="fa-solid fa-circle-check text-xl text-emerald-200"></i>
            <div>
                <p class="font-semibold text-sm">Success</p>
                <p class="text-xs text-emerald-100">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('flash-success').remove()" class="ml-3 text-emerald-200 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <script>setTimeout(() => { document.getElementById('flash-success')?.remove(); }, 4000);</script>
    @endif

    @if(session('error'))
        <div id="flash-error" class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-rose-600 text-white px-5 py-3.5 rounded-xl shadow-xl transition-all duration-300 transform translate-y-0">
            <i class="fa-solid fa-circle-exclamation text-xl text-rose-200"></i>
            <div>
                <p class="font-semibold text-sm">Alert</p>
                <p class="text-xs text-rose-100">{{ session('error') }}</p>
            </div>
            <button onclick="document.getElementById('flash-error').remove()" class="ml-3 text-rose-200 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <script>setTimeout(() => { document.getElementById('flash-error')?.remove(); }, 6000);</script>
    @endif

    @yield('layout_content')

    <!-- PWA Installation & Helper Modals -->
    @include('components.pwa-modals')

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((reg) => {
                    console.log('Samruddhi PWA Service Worker registered:', reg.scope);
                }).catch((err) => {
                    console.log('PWA SW registration failed:', err);
                });
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
