<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Presensi')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: true }">

    <!-- Mobile Header -->
    <div
        class="lg:hidden flex items-center justify-between p-4 bg-white shadow-sm border-b border-slate-200 fixed top-0 w-full z-30">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-8 w-auto">
            <span class="font-bold text-lg text-primary">E-Presensi</span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-primary transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Backdrop -->
    <div x-show="!sidebarOpen" @click="sidebarOpen = true" x-transition.opacity
        class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed top-0 left-0 z-50 h-screen w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col pt-16 lg:pt-0">

        <!-- Sidebar Header -->
        <div class="hidden lg:flex items-center justify-center h-16 border-b border-slate-100 bg-white">
            <div class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="h-8 w-auto">
                <span class="font-bold text-xl text-primary tracking-tight">E-Presensi</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <!-- Dashboard -->
            <a href="/dashboard"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition-all group {{ request()->is('dashboard') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 group-hover:scale-110 transition-transform">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Data Master Group -->
            @role('super admin')
            <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Master Data</div>

            <a href="/karyawan"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition-all group {{ request()->is('karyawan*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <span>Data Karyawan</span>
            </a>

            <a href="/departemen"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition-all group {{ request()->is('departemen*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
                <span>Departemen</span>
            </a>
            @endrole

            <!-- Settings -->
            <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Settings</div>

            <form method="POST" action="/proseslogout">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 hover:text-red-600 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 min-h-screen pt-16 lg:pt-0">
        <!-- Top Navbar (Desktop) -->
        <header
            class="hidden lg:flex items-center justify-between h-16 px-8 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-20">
            <div class="flex items-center gap-4 text-sm text-slate-500">
                <span class="font-medium text-slate-800">@yield('header')</span>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    @if(Auth::guard('karyawan')->check())
                        <div class="text-right">
                            <div class="text-sm font-semibold text-slate-800">
                                {{ Auth::guard('karyawan')->user()->nama_karyawan }}</div>
                            <div class="text-xs text-slate-500">{{ Auth::guard('karyawan')->user()->kode_jabatan }}</div>
                        </div>
                        <div
                            class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                            {{ substr(Auth::guard('karyawan')->user()->nama_karyawan, 0, 1) }}
                        </div>
                    @elseif(Auth::guard('user')->check())
                        <div class="text-right">
                            <div class="text-sm font-semibold text-slate-800">{{ Auth::guard('user')->user()->name }}</div>
                            <div class="text-xs text-slate-500">{{ Auth::guard('user')->user()->email }}</div>
                        </div>
                        <div
                            class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                            {{ substr(Auth::guard('user')->user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-4 lg:p-8">
            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    @stack('scripts')
    @stack('myscript')
</body>

</html>