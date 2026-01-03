@extends('layouts.mobile_modern')

@section('header')
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="{{ route('aktivitaskaryawan.index') }}" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Input Aktivitas</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <style>
        .webcam-capture {
            width: 100% !important;
            height: 100% !important;
            position: absolute;
            top: 0;
            left: 0;
            object-fit: cover;
            z-index: 0;
            overflow: hidden;
        }

        .webcam-capture video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 10;
            background: #000;
        }

        body {
            background-color: #0f172a;
        }

        @keyframes scan {
            0% {
                top: 0;
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                top: 100%;
                opacity: 0;
            }
        }

        .animate-scan {
            animation: scan 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
    </style>

    <!-- Main Mobile Container -->
    <div class="-m-4 relative w-[calc(100%+2rem)] h-[calc(100vh-112px)] overflow-hidden bg-slate-900 flex flex-col"
        x-data="activityHandler()">

        <!-- Top Section: Camera (70%) -->
        <div class="relative w-full h-[70%] shrink-0">
            <div class="relative w-full h-[calc(100vh-70px)] bg-slate-900 overflow-hidden font-inter">

                <!-- 1. HEADER (Floating & Transparent) -->
                <div class="absolute top-0 left-0 right-0 z-40 p-4 flex items-center justify-between pointer-events-none">
                    <a href="{{ route('aktivitaskaryawan.index') }}"
                        class="pointer-events-auto w-10 h-10 flex items-center justify-center bg-black/20 backdrop-blur-md rounded-full text-white active:bg-black/40 transition-colors">
                        <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                    </a>
                    <div class="absolute left-1/2 -translate-x-1/2 px-4 py-1.5 bg-black/20 backdrop-blur-md rounded-full">
                        <h1 class="text-white text-xs font-semibold tracking-wide uppercase whitespace-nowrap">
                            Input Aktivitas
                        </h1>
                    </div>
                </div>

                <!-- 2. CAMERA LAYER -->
                <div id="camera-wrapper" class="absolute inset-0 z-0">
                    <div class="webcam-capture w-full h-full object-cover" id="webcam-container"></div>

                    <!-- Date & Time Pills -->
                    <div class="absolute top-24 left-4 z-10">
                        <div
                            class="bg-black/30 backdrop-blur-md border border-white/10 px-4 py-2 rounded-2xl flex items-center gap-2 shadow-lg">
                            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <ion-icon name="calendar-outline" class="text-blue-400 text-lg"></ion-icon>
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-white/60 text-[10px] font-medium leading-none uppercase tracking-wider">Tanggal</span>
                                <span
                                    class="text-white text-base font-bold font-mono leading-normal mt-0.5">{{ DateToIndo(date('Y-m-d')) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-24 right-4 z-10">
                        <div
                            class="bg-black/30 backdrop-blur-md border border-white/10 px-4 py-2 rounded-2xl flex items-center gap-2 shadow-lg">
                            <div class="flex flex-col items-end">
                                <span
                                    class="text-white/60 text-[10px] font-medium leading-none uppercase tracking-wider">Waktu</span>
                                <span class="text-white text-base font-bold font-mono leading-normal mt-0.5"
                                    id="live-clock">{{ date('H:i') }}</span>
                            </div>
                            <div
                                class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center animate-pulse">
                                <ion-icon name="time-outline" class="text-amber-400 text-lg"></ion-icon>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. BOTTOM SHEET -->
                {{--
                COLLAPSE HEIGHT SETTING:
                translate-y-[calc(100%-Xpx)] where X is the visible peek height
                - 40px = minimal peek (just handle)
                - 60px = small peek
                - 80px = medium peek
                Current: 40px for minimal collapsed state
                --}}
                <!-- 3. BOTTOM SHEET -->
                <div class="absolute bottom-0 left-0 right-0 z-50 bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.3)] transition-transform duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] max-h-[70vh] flex flex-col"
                    :class="isOpen ? 'translate-y-0' : 'translate-y-[calc(100%-60px)]'">

                    <!-- Toggle Handle Area (Fixed at top) -->
                    <div @click="isOpen = !isOpen"
                        class="absolute top-[-15px] left-0 right-0 h-6 w-full z-10 cursor-pointer flex justify-center items-center mt-3">
                        <div class="w-16 h-1.5 bg-slate-400 rounded-full hover:bg-slate-500 transition-colors"></div>
                    </div>

                    <!-- Main Content (Scrollable) -->
                    <div class="pb-20 pt-10 px-6 overflow-y-auto">

                        <!-- Floating Action Buttons (Capture + Switch Camera) -->
                        <div class="absolute -top-[70px] left-0 right-0 z-[60] px-6">
                            <div class="flex gap-3">
                                <!-- Capture Button -->
                                <button @click="capturePhoto()"
                                    class="group relative overflow-hidden bg-blue-600 hover:bg-blue-700 active:scale-95 transition-all duration-200 h-14 rounded-2xl shadow-lg shadow-blue-200 flex items-center justify-center gap-3 flex-1">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                        <ion-icon name="camera" class="text-white text-lg"></ion-icon>
                                    </div>
                                    <span class="text-white font-bold tracking-wide">Ambil Foto</span>
                                </button>
                                <!-- Switch Camera Button -->
                                <button @click="switchCamera()"
                                    class="w-14 h-14 bg-amber-500 hover:bg-amber-600 active:scale-95 transition-all duration-200 rounded-2xl shadow-lg shadow-amber-200 flex items-center justify-center shrink-0">
                                    <ion-icon name="camera-reverse-outline" class="text-white text-2xl"></ion-icon>
                                </button>
                            </div>
                        </div>

                        <!-- Location Capsule -->
                        <div
                            class="w-[90%] max-w-sm bg-slate-900 text-white p-1.5 pr-4 rounded-full shadow-xl flex items-center justify-between ring-4 ring-white/10 backdrop-blur-sm relative overflow-hidden mx-auto -mt-2 mb-4">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/30">
                                    <ion-icon name="location" class="text-white text-sm"></ion-icon>
                                </div>
                                <div class="flex-col flex min-w-0 pr-2">
                                    <span
                                        class="text-[9px] text-slate-400 uppercase tracking-wider font-bold leading-tight">Lokasi
                                        Anda</span>
                                    <span class="text-xs font-bold truncate leading-tight" id="location-display">Mencari
                                        lokasi...</span>
                                </div>
                            </div>
                        </div>

                        <div class="h-2"></div>

                        <!-- Photo Preview Section -->
                        <div class="mb-4">
                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-2">Foto Aktivitas</p>
                            <div
                                class="w-full aspect-video rounded-2xl border border-slate-200 shadow-sm overflow-hidden relative bg-slate-100">
                                <!-- Empty State -->
                                <div x-show="!hasCaptured"
                                    class="absolute inset-0 flex flex-col items-center justify-center text-slate-400">
                                    <ion-icon name="image-outline" class="text-4xl mb-2"></ion-icon>
                                    <span class="text-xs font-medium">Belum Mengambil Foto Aktivitas</span>
                                </div>
                                <!-- Captured Photo -->
                                <img x-show="hasCaptured" :src="capturedImage" class="w-full h-full object-cover"
                                    style="display: none;">
                            </div>
                        </div>

                        <!-- Description Input -->
                        <div class="mb-4">
                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-2">Deskripsi Aktivitas</p>
                            <textarea id="aktivitas-input"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-slate-700 text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder:text-slate-400 h-28 resize-none"
                                placeholder="Tuliskan aktivitas yang sedang dilakukan..."></textarea>
                        </div>

                        <!-- Reset Button -->
                        <div class="mb-4">
                            <button @click="resetAll()"
                                class="w-full bg-rose-50 hover:bg-rose-100 text-rose-600 h-12 rounded-xl font-bold transition-all active:scale-95 flex items-center justify-center gap-2 border border-rose-200">
                                <ion-icon name="refresh-outline" class="text-xl"></ion-icon>
                                <span class="text-sm">Reset</span>
                            </button>
                        </div>

                        <!-- Save Button -->
                        <button @click="submitActivity()"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white h-14 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <ion-icon name="save-outline" class="text-xl"></ion-icon>
                            <span>Simpan Aktivitas</span>
                        </button>

                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Form -->
        <form action="{{ route('aktivitaskaryawan.store') }}" method="POST" enctype="multipart/form-data" id="formAktivitas"
            class="hidden">
            @csrf
            <input type="hidden" name="foto" id="fotoData">
            <input type="hidden" name="lokasi" id="lokasiData">
            <input type="hidden" name="aktivitas" id="aktivitasData">
            @if (auth()->user()->hasRole('karyawan'))
                <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
            @endif
        </form>
    </div>
@endsection

@push('myscript')
    <script>
        // Live Clock
        setInterval(() => {
            const now = new Date();
            const el = document.getElementById('live-clock');
            if (el) el.innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }, 1000);

        // Alpine Component
        document.addEventListener('alpine:init', () => {
            Alpine.data('activityHandler', () => ({
                isOpen: false,
                hasCaptured: false,
                capturedImage: null,
                facingMode: 'user', // Front camera default

                init() {
                    this.initCamera();
                    this.initLocation();
                },

                initCamera() {
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    Webcam.set({
                        width: 640,
                        height: 480,
                        image_format: 'jpeg',
                        jpeg_quality: 90,
                        constraints: {
                            video: {
                                facingMode: this.facingMode,
                                width: { ideal: isMobile ? 240 : 640 },
                                height: { ideal: isMobile ? 180 : 480 }
                            }
                        }
                    });
                    Webcam.attach('.webcam-capture');

                    Webcam.on('load', () => { setTimeout(() => this.fixVideoAttributes(), 500); });
                },

                fixVideoAttributes() {
                    const video = document.querySelector('.webcam-capture video');
                    if (video) {
                        video.removeAttribute('style');
                        video.style.width = '100%';
                        video.style.height = '100%';
                        video.style.objectFit = 'cover';
                        video.setAttribute('playsinline', 'true');
                        video.setAttribute('muted', 'true');
                        video.setAttribute('autoplay', 'true');
                        video.play().catch(e => console.log(e));
                    }
                },

                initLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                const lat = position.coords.latitude;
                                const lng = position.coords.longitude;
                                document.getElementById('lokasiData').value = lat + "," + lng;
                                document.getElementById('location-display').innerText = lat.toFixed(5) + ", " + lng.toFixed(5);
                            },
                            (error) => {
                                document.getElementById('location-display').innerText = "Gagal mengambil lokasi";
                            }
                        );
                    }
                },

                switchCamera() {
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';

                    Webcam.reset();

                    setTimeout(() => {
                        Webcam.set({
                            width: 640,
                            height: 480,
                            image_format: 'jpeg',
                            jpeg_quality: 90,
                            facingMode: this.facingMode,
                            constraints: {
                                video: {
                                    facingMode: this.facingMode,
                                    width: { ideal: isMobile ? 240 : 640 },
                                    height: { ideal: isMobile ? 180 : 480 }
                                }
                            }
                        });
                        Webcam.attach('.webcam-capture');
                        setTimeout(() => this.fixVideoAttributes(), 1000);
                    }, 50);
                },

                capturePhoto() {
                    Webcam.snap((data_uri) => {
                        this.capturedImage = data_uri;
                        this.hasCaptured = true;
                        this.isOpen = true; // EXPAND sheet after capture so user can fill description
                        document.getElementById('fotoData').value = data_uri;
                    });
                },

                resetAll() {
                    this.hasCaptured = false;
                    this.capturedImage = null;
                    document.getElementById('fotoData').value = '';
                    document.getElementById('aktivitas-input').value = '';
                },

                submitActivity() {
                    if (!this.hasCaptured) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Foto Belum Diambil',
                            text: 'Silakan ambil foto aktivitas terlebih dahulu!',
                        });
                        return;
                    }

                    const desc = document.getElementById('aktivitas-input').value.trim();
                    if (!desc) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Deskripsi Kosong',
                            text: 'Silakan isi deskripsi aktivitas!',
                        });
                        this.isOpen = true;
                        return;
                    }

                    const lokasi = document.getElementById('lokasiData').value;
                    if (!lokasi) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Lokasi Belum Ditemukan',
                            text: 'Sedang mengambil lokasi Anda. Pastikan GPS aktif!',
                        });
                        return;
                    }
                    // Force update

                    document.getElementById('aktivitasData').value = desc;

                    Swal.fire({
                        title: 'Simpan Aktivitas?',
                        text: "Pastikan data sudah benar",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'bg-emerald-500 text-white px-5 py-2.5 rounded-xl font-bold',
                            cancelButton: 'bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('formAktivitas').submit();
                        }
                    });
                }
            }));
        });
    </script>
@endpush