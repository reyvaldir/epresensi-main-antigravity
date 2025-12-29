<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Presensi')</title>

    <!-- PWA Meta Tags (Preserved from original) -->
    <meta name="application-name" content="E-Presensi GPS V2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="E-Presensi">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-TileColor" content="#2563EB"> <!-- Updated to Primary Blue -->
    <meta name="theme-color" content="#2563EB"> <!-- Updated to Primary Blue -->

    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/img/icons/pwa/icon-192x192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <!-- Map & Webcam Styles -->
    <style>
        .webcam-capture,
        #camera video {
            width: 100% !important;
            height: auto !important;
            border-radius: 1rem;
            object-fit: cover;
        }

        #map {
            height: 200px;
            border-radius: 1rem;
        }
    </style>

    @stack('styles')
</head>

<body
    class="bg-slate-50 font-sans text-slate-800 antialiased pb-32 h-full selection:bg-blue-100 selection:text-blue-600">



    <!-- Main Content -->
    <main class="p-4 min-h-[80vh]">
        @yield('content')
        <div class="w-full h-28"></div>
    </main>

    <!-- Bottom Navigation Bar -->

    <nav
        class="fixed bottom-0 left-0 w-full bg-white border-t border-slate-200 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <div class="w-full mx-auto h-16 grid grid-cols-5 relative px-2">

            <a href="/dashboard"
                class="flex flex-col items-center justify-center w-full h-full group {{ request()->is('dashboard') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">

                {{-- 1. Tambahkan 'mb-0.5' atau hapus margin icon --}}
                {{-- 2. Tambahkan 'leading-none' agar kotak icon pas seukuran icon --}}
                <div class="text-2xl transition-transform group-active:scale-90 leading-none">
                    <ion-icon name="home-outline"></ion-icon>
                </div>

                {{-- 3. Gunakan 'mt-1' untuk mengatur jarak manual (lebih presisi dari gap) --}}
                {{-- 4. Gunakan 'leading-none' pada teks --}}
                <span class="text-[10px] font-medium leading-none mt-1">Home</span>
            </a>

            <a href="{{ route('presensi.histori') }}"
                class="flex flex-col items-center justify-center w-full h-full group {{ request()->is('presensi/histori') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">
                <div class="text-2xl transition-transform group-active:scale-90 leading-none">
                    <ion-icon name="document-text-outline"></ion-icon>
                </div>
                <span class="text-[10px] font-medium leading-none mt-1">Histori</span>
            </a>

            <div class="relative flex justify-center items-center">
                <a href="/presensi/create"
                    class="absolute -top-5 flex items-center justify-center w-16 h-16 bg-primary rounded-full shadow-lg shadow-blue-400/50 text-white hover:bg-blue-700 transition-transform active:scale-95 border-4 border-white">
                    <div class="text-3xl mt-1">
                        <ion-icon name="finger-print-outline"></ion-icon>
                    </div>
                </a>
            </div>

            <a href="{{ route('pengajuanizin.index') }}"
                class="flex flex-col items-center justify-center w-full h-full group {{ request()->is('pengajuanizin') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">
                <div class="text-2xl transition-transform group-active:scale-90 leading-none">
                    <ion-icon name="calendar-outline"></ion-icon>
                </div>
                <span class="text-[10px] font-medium leading-none mt-1">Izin</span>
            </a>

            <a href="{{ route('users.editpassword', Crypt::encrypt(Auth::id())) }}"
                class="flex flex-col items-center justify-center w-full h-full group {{ request()->routeIs('users.editpassword') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">
                <div class="text-2xl transition-transform group-active:scale-90 leading-none">
                    <ion-icon name="settings-outline"></ion-icon>
                </div>
                <span class="text-[10px] font-medium leading-none mt-1">Setting</span>
            </a>

        </div>
    </nav>

    <!-- Service Worker Script (Preserved) -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js')
                    .then(function (registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(function (err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>

    @stack('scripts')
    @stack('myscript')
</body>

</html>