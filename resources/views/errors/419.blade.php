<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir - E-Presensi</title>
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
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'blob': "blob 7s infinite",
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        blob: {
                            "0%": { transform: "translate(0px, 0px) scale(1)" },
                            "33%": { transform: "translate(30px, -50px) scale(1.1)" },
                            "66%": { transform: "translate(-20px, 20px) scale(0.9)" },
                            "100%": { transform: "translate(0px, 0px) scale(1)" },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen flex items-center justify-center p-4 bg-slate-50 relative overflow-hidden">

    <!-- Decorative blobs similar to Login Page -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div
            class="absolute top-[20%] left-[20%] w-72 h-72 bg-blue-400/20 rounded-full blur-3xl mix-blend-multiply animate-blob">
        </div>
        <div
            class="absolute top-[20%] right-[20%] w-72 h-72 bg-indigo-400/20 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute -bottom-8 left-[40%] w-72 h-72 bg-purple-400/20 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-4000">
        </div>
    </div>

    <!-- Main Card -->
    <div
        class="w-full max-w-sm bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 p-8 text-center relative z-10 border border-slate-100">

        <!-- Illustration Placeholder -->
        <div class="mb-6 animate-float flex justify-center">
            <div class="relative w-32 h-32 flex items-center justify-center bg-blue-50 rounded-full">
                <ion-icon name="hourglass-outline" class="text-6xl text-blue-500"></ion-icon>

                <!-- Status Badge -->
                <div
                    class="absolute -right-2 -bottom-2 bg-red-500 text-white rounded-xl p-2 shadow-lg border-4 border-white">
                    <ion-icon name="alert-outline" class="text-xl"></ion-icon>
                </div>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold font-outfit text-slate-800 mb-2">
            Sesi Telah Berakhir
        </h1>

        <!-- Description -->
        <p class="text-slate-500 text-sm leading-relaxed mb-8 px-4">
            Keamanan adalah prioritas kami. Karena tidak ada aktivitas, sesi Anda otomatis diakhiri.
        </p>

        <!-- Login Button -->
        <a href="{{ route('loginuser') }}"
            class="inline-flex items-center justify-center w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-500/20 transition-all duration-300 transform active:scale-[0.98] group">
            <span class="mr-2">Login Kembali</span>
            <ion-icon name="arrow-forward" class="transition-transform group-hover:translate-x-1"></ion-icon>
        </a>

        <!-- Footer -->
        <div class="mt-6 text-xs text-slate-400">
            &copy; {{ date('Y') }} E-Presensi GPS
        </div>

    </div>

</body>

</html>