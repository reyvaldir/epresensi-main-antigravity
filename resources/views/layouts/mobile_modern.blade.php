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
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/icons/pwa/icon-192x192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->


    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <!-- Map & Webcam Styles -->
    <style>
        html {
            scrollbar-gutter: stable;
        }

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

        /* Fix for Leaflet tiles disappearing due to Tailwind/Global styles */
        .leaflet-container img {
            max-width: none !important;
            max-height: none !important;
        }

        /* =====================================================
           SWEETALERT2 GLOBAL STYLING - Modern & Visible Buttons
           ===================================================== */

        /* Container z-index fix */
        .swal2-container {
            z-index: 9999 !important;
        }

        /* Prevent body scroll jump when SweetAlert opens */
        body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        /* Prevent body from scrolling to top */
        .swal2-shown {
            height: auto !important;
        }

        /* Popup styling */
        .swal2-popup {
            border-radius: 1.25rem !important;
            padding: 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }

        /* Title styling */
        .swal2-title {
            font-weight: 700 !important;
            color: #1e293b !important;
        }

        /* Icon styling - make them more vibrant */
        .swal2-icon.swal2-error {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }

        .swal2-icon.swal2-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #10b981 !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        .swal2-icon.swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }

        .swal2-icon.swal2-info {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }

        /* Button Actions Container */
        .swal2-actions {
            gap: 0.75rem !important;
        }

        /* ====== CONFIRM BUTTON - Primary Blue ====== */
        .swal2-confirm {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 2rem !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.4) !important;
            transition: all 0.2s ease !important;
        }

        .swal2-confirm:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            box-shadow: 0 6px 20px 0 rgba(30, 64, 175, 0.6) !important;
            transform: translateY(-2px) !important;
        }

        .swal2-confirm:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 10px 0 rgba(37, 99, 235, 0.4) !important;
        }

        .swal2-confirm:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4) !important;
        }

        /* ====== CANCEL BUTTON - Slate Gray ====== */
        .swal2-cancel {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 2rem !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            box-shadow: 0 4px 14px 0 rgba(71, 85, 105, 0.3) !important;
            transition: all 0.2s ease !important;
        }

        .swal2-cancel:hover {
            background: linear-gradient(135deg, #334155 0%, #1e293b 100%) !important;
            box-shadow: 0 6px 20px 0 rgba(30, 41, 59, 0.6) !important;
            transform: translateY(-2px) !important;
        }

        .swal2-cancel:active {
            transform: translateY(0) !important;
        }

        /* ====== DENY BUTTON - Red ====== */
        .swal2-deny {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 2rem !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            box-shadow: 0 4px 14px 0 rgba(220, 38, 38, 0.4) !important;
            transition: all 0.2s ease !important;
        }

        .swal2-deny:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%) !important;
            box-shadow: 0 6px 20px 0 rgba(153, 27, 27, 0.6) !important;
            transform: translateY(-2px) !important;
        }

        /* ====== SUCCESS SPECIFIC - Green Confirm Button ====== */
        .swal2-icon-success~.swal2-actions .swal2-confirm {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.4) !important;
        }

        .swal2-icon-success~.swal2-actions .swal2-confirm:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%) !important;
            box-shadow: 0 6px 20px 0 rgba(6, 95, 70, 0.6) !important;
        }

        /* ====== ERROR SPECIFIC - Red Confirm Button ====== */
        .swal2-icon-error~.swal2-actions .swal2-confirm {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            box-shadow: 0 4px 14px 0 rgba(220, 38, 38, 0.4) !important;
        }

        .swal2-icon-error~.swal2-actions .swal2-confirm:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%) !important;
            box-shadow: 0 6px 20px 0 rgba(153, 27, 27, 0.6) !important;
        }

        /* ====== WARNING SPECIFIC - Amber Confirm Button ====== */
        .swal2-icon-warning~.swal2-actions .swal2-confirm {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            box-shadow: 0 4px 14px 0 rgba(217, 119, 6, 0.4) !important;
        }

        .swal2-icon-warning~.swal2-actions .swal2-confirm:hover {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%) !important;
            box-shadow: 0 6px 20px 0 rgba(146, 64, 14, 0.6) !important;
        }

        /* ====== INFO SPECIFIC - Blue Confirm Button ====== */
        .swal2-icon-info~.swal2-actions .swal2-confirm {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.4) !important;
        }

        .swal2-icon-info~.swal2-actions .swal2-confirm:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            box-shadow: 0 6px 20px 0 rgba(30, 64, 175, 0.6) !important;
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
                <span class="text-[10px] font-medium leading-none mt-1">Pengajuan Izin</span>
            </a>

            <a href="{{ route('profile.index') }}"
                class="flex flex-col items-center justify-center w-full h-full group {{ request()->routeIs('profile.index') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">
                <div class="text-2xl transition-transform group-active:scale-90 leading-none">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <span class="text-[10px] font-medium leading-none mt-1">Profile</span>
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

    <!-- Critical Dependencies for Attendance Feature -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global SweetAlert2 Configuration
        // Prevent page scrolling/jumping when modals open by disabling heightAuto
        const SwalOriginal = Swal;
        window.Swal = SwalOriginal.mixin({
            heightAuto: false,
            buttonsStyling: false // We use custom Tailwind classes
        });
    </script>

    <!-- Leaflet Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <!-- Face API (Local Asset) -->
    <script src="{{ asset('assets/vendor/face-api.min.js') }}"></script>

    <!-- CSRF Token Setup -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Session Alert Handling -->
    <script>
        const swalConfig = {
            confirmButtonColor: '#4F46E5',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold w-full border-0 outline-none focus:outline-none ring-0 focus:ring-0 shadow-lg shadow-indigo-200'
            },
            buttonsStyling: false
        };

        @if (Session::get('success'))
            Swal.fire({
                ...swalConfig,
                icon: 'success',
                title: 'Berhasil',
                text: "{!! Session::get('success') !!}",
            });
        @endif

        @if (Session::get('error'))
            Swal.fire({
                ...swalConfig,
                icon: 'error',
                title: 'Gagal',
                text: "{!! Session::get('error') !!}",
                confirmButtonColor: '#EF4444',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-rose-500 hover:bg-rose-600 text-white px-6 py-2.5 rounded-xl font-bold w-full border-0 outline-none focus:outline-none ring-0 focus:ring-0 shadow-lg shadow-rose-200'
                }
            });
        @endif

        @if (Session::get('warning'))
            Swal.fire({
                ...swalConfig,
                icon: 'warning',
                title: 'Peringatan',
                text: "{!! Session::get('warning') !!}",
                confirmButtonColor: '#F59E0B',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl font-bold w-full border-0 outline-none focus:outline-none ring-0 focus:ring-0 shadow-lg shadow-amber-200'
                }
            });
        @endif
    </script>

    @stack('scripts')
    @stack('myscript')
</body>

</html>