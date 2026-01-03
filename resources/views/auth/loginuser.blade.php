<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Login - E-Presensi GPS</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="E-Presensi">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta name="apple-mobile-web-app-title" content="E-Presensi">
    <meta name="description" content="Aplikasi Presensi GPS untuk Karyawan">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#ffffff">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/assets/img/icons/pwa/icon-512x512.png">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;700&display=swap"
        rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        blob: "blob 7s infinite",
                    },
                    keyframes: {
                        blob: {
                            "0%": {
                                transform: "translate(0px, 0px) scale(1)",
                            },
                            "33%": {
                                transform: "translate(30px, -50px) scale(1.1)",
                            },
                            "66%": {
                                transform: "translate(-20px, 20px) scale(0.9)",
                            },
                            "100%": {
                                transform: "translate(0px, 0px) scale(1)",
                            },
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #f8fafc;
            /* Slate 50 */
            background-image:
                radial-gradient(at 0% 0%, hsla(217, 91%, 95%, 1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(252, 100%, 96%, 1) 0, transparent 50%);
        }

        .form-input {
            background-color: #f8fafc;
            /* Slate 50 */
            transition: all 0.3s ease;
        }

        .form-input:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 md:p-8">
    @php
        $namainstansi = App\Models\Pengaturanumum::first()->nama_perusahaan ?? 'E-Presensi';
    @endphp

    <!-- Main Card Container -->
    <main
        class="w-full max-w-[1000px] bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden flex flex-col md:flex-row relative z-10 transition-all duration-500">

        <!-- Decorative Background Blur (Behind Card - Only visible if card has transparency, here strictly for effect) -->
        <div class="fixed top-0 left-0 w-full h-full -z-10 pointer-events-none overflow-hidden">
            <div
                class="absolute top-[10%] left-[10%] w-96 h-96 bg-blue-400/20 rounded-full blur-3xl mix-blend-multiply animate-blob">
            </div>
            <div
                class="absolute top-[10%] right-[10%] w-96 h-96 bg-indigo-400/20 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-32 left-[20%] w-96 h-96 bg-slate-400/20 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-4000">
            </div>
        </div>

        <!-- Left Side: Branding (Top on Mobile, Left on Desktop) -->
        <div
            class="w-full md:w-5/12 bg-gradient-to-br from-blue-600 to-indigo-800 p-10 md:p-12 flex flex-col justify-center items-center text-center relative overflow-hidden group">

            <!-- Abstract Shapes -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
                <div class="absolute -top-24 -left-24 w-64 h-64 rounded-full bg-white blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-64 h-64 rounded-full bg-blue-400 blur-3xl"></div>
            </div>

            <div class="relative z-10">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 shadow-xl mb-6 transform group-hover:rotate-6 transition-transform duration-500">
                    <ion-icon name="finger-print-outline" class="text-5xl text-white"></ion-icon>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold font-outfit text-white tracking-tight mb-3">E-Presensi</h1>
                <p class="text-blue-100/80 text-sm md:text-base font-medium max-w-[250px] mx-auto leading-relaxed">
                    Sistem Presensi Karyawan Berbasis GPS & Biometrik
                </p>
            </div>

            <!-- Footer for Desktop (Hidden on Mobile) -->
            <div class="absolute bottom-8 left-0 w-full text-center hidden md:block">
                <p class="text-blue-200/60 text-xs">&copy; {{ date('Y') }} {{ $namainstansi }}</p>
            </div>
        </div>

        <!-- Right Side: Form (Bottom on Mobile, Right on Desktop) -->
        <div class="w-full md:w-7/12 bg-white p-8 md:p-12 lg:p-16 flex flex-col justify-center relative">

            <div class="max-w-md mx-auto w-full">
                <div class="mb-8 md:mb-10 text-center md:text-left">
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-800 font-outfit mb-2">Selamat Datang 👋</h2>
                    <p class="text-slate-400 text-sm md:text-base">Silahkan masuk akun Anda.</p>
                </div>

                <!-- Alerts -->
                @if (session('error'))
                    <div
                        class="bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-xl text-sm mb-6 flex items-start gap-3 shadow-sm">
                        <ion-icon name="alert-circle" class="text-lg mt-0.5 shrink-0"></ion-icon>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-xl text-sm mb-6 flex items-start gap-3 shadow-sm">
                        <ion-icon name="alert-circle" class="text-lg mt-0.5 shrink-0"></ion-icon>
                        <div class="font-medium">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Username -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Username /
                            Email</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500 text-slate-400">
                                <ion-icon name="person-outline" class="text-xl"></ion-icon>
                            </div>
                            <input type="text" name="id_user"
                                class="form-input w-full rounded-2xl border border-slate-200 py-3.5 pl-11 pr-4 text-sm font-semibold text-slate-700 placeholder:text-slate-300 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Masukan Username atau Email" value="{{ old('id_user') }}" required
                                autocomplete="off">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Password</label>
                            <a href="#"
                                class="text-xs font-semibold text-blue-500 hover:text-blue-600 transition-colors">Lupa
                                Password?</a>
                        </div>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500 text-slate-400">
                                <ion-icon name="lock-closed-outline" class="text-xl"></ion-icon>
                            </div>
                            <input type="password" name="password" id="password"
                                class="form-input w-full rounded-2xl border border-slate-200 py-3.5 pl-11 pr-12 text-sm font-semibold text-slate-700 placeholder:text-slate-300 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                                placeholder="Masukan Password" required>
                            <!-- Toggle Button -->
                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors focus:outline-none">
                                <ion-icon name="eye-outline" class="text-xl"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Button -->
                    <div class="pt-2">
                        <label class="flex items-center gap-3 cursor-pointer group mb-6 w-fit select-none">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="remember" id="remember"
                                    class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border-2 border-slate-300 bg-slate-50 checked:border-blue-500 checked:bg-blue-500 transition-all hover:border-blue-400">
                                <ion-icon name="checkmark"
                                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100 pointer-events-none text-sm font-bold"></ion-icon>
                            </div>
                            <span
                                class="text-sm font-medium text-slate-500 group-hover:text-slate-700 transition-colors">Ingat
                                Saya</span>
                        </label>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 group text-base">
                            <span>Masuk Aplikasi</span>
                            <ion-icon name="arrow-forward"
                                class="transition-transform group-hover:translate-x-1 font-bold"></ion-icon>
                        </button>
                    </div>

                </form>
            </div>

            <!-- Footer for Mobile (Hidden on Desktop) -->
            <div class="mt-8 text-center md:hidden">
                <p class="text-slate-400 text-xs">&copy; {{ date('Y') }} {{ $namainstansi }}</p>
            </div>

        </div>

    </main>

    <!-- Service Worker Registration -->
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

    <!-- PWA Install Prompt -->
    @include('components.pwa-install-prompt')

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye icon
            const icon = this.querySelector('ion-icon');
            if (type === 'password') {
                icon.setAttribute('name', 'eye-outline');
            } else {
                icon.setAttribute('name', 'eye-off-outline');
            }
        });
    </script>

</body>

</html>