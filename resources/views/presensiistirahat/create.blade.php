@extends('layouts.mobile_modern')
@section('header')
    <!-- Standard App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">E-Presensi</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <!-- Leaflet & Custom Styles -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        /* CSS Reset for Cameras */
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

        /* V7 Specific Styles */
        body {
            background-color: #0f172a;
        }

        #facedetection {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        /* Scanning Animation Frame */
        .scan-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -60%);
            width: 250px;
            height: 250px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            z-index: 20;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.4);
        }

        .scan-frame::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(to bottom, #3b82f6 0%, transparent 40%, transparent 60%, #3b82f6 100%);
            border-radius: 22px;
            z-index: -1;
            opacity: 0.8;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            -webkit-mask-composite: xor;
            padding: 2px;
        }

        .scan-line {
            width: 100%;
            height: 4px;
            background: #3b82f6;
            box-shadow: 0 0 15px #3b82f6;
            position: absolute;
            top: 0;
            animation: scan 2s linear infinite;
            opacity: 0.6;
        }

        @keyframes scan {
            0% {
                top: 5%;
                opacity: 0;
            }

            10% {
                opacity: 0.8;
            }

            90% {
                opacity: 0.8;
            }

            100% {
                top: 95%;
                opacity: 0;
            }
        }

        /* Loading Spinner Overrides */
        #face-recognition-loading,
        #face-data-loading {
            z-index: 50 !important;
            background: rgba(0, 0, 0, 0.7) !important;
            padding: 20px !important;
            border-radius: 12px !important;
        }
    </style>

    <!-- Main Mobile Container -->
    <div class="-m-4 relative w-[calc(100%+2rem)] h-[calc(100vh-112px)] overflow-hidden bg-slate-900 flex flex-col">

        <!-- Top Section: Camera -->
        <div class="relative w-full h-[70%] shrink-0">

            <!-- Main Container -->
            <div class="relative w-full h-[calc(100vh-70px)] bg-slate-900 overflow-hidden font-inter">

                <!-- 1. HEADER (Floating & Transparent) -->
                <div class="absolute top-0 left-0 right-0 z-40 p-4 flex items-center justify-between pointer-events-none">
                    <a href="/dashboard"
                        class="pointer-events-auto w-10 h-10 flex items-center justify-center bg-black/20 backdrop-blur-md rounded-full text-white active:bg-black/40 transition-colors">
                        <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                    </a>
                    <div class="absolute left-1/2 -translate-x-1/2 px-4 py-1.5 bg-black/20 backdrop-blur-md rounded-full">
                        <h1 class="text-white text-xs font-semibold tracking-wide uppercase whitespace-nowrap">
                            E-Presensi Istirahat
                        </h1>
                    </div>
                </div>

                <!-- 2. CAMERA LAYER -->
                <div id="facedetection" class="absolute inset-0 z-0">
                    <div class="webcam-capture w-full h-full object-cover"></div>
                    <div id="camera"></div>

                    <!-- Scanning Overlay -->
                    <div
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-[60%] w-64 h-80 rounded-[3rem] border-2 border-white/30 box-content shadow-[0_0_100px_rgba(0,0,0,0.5)_inset]">
                        <div
                            class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 border-emerald-400 rounded-tl-[2.5rem]">
                        </div>
                        <div
                            class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 border-emerald-400 rounded-tr-[2.5rem]">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 border-emerald-400 rounded-bl-[2.5rem]">
                        </div>
                        <div
                            class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 border-emerald-400 rounded-br-[2.5rem]">
                        </div>
                        <div
                            class="absolute top-0 left-0 right-0 h-1 bg-emerald-400/50 shadow-[0_0_20px_rgba(52,211,153,1)] animate-scan">
                        </div>
                    </div>

                    <!-- Date & Time Floating Pills -->
                    <div class="absolute top-24 left-4 z-10">
                        <div
                            class="bg-black/30 backdrop-blur-md border border-white/10 px-4 py-2 rounded-2xl flex items-center gap-2 shadow-lg">
                            <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <ion-icon name="calendar-outline" class="text-emerald-400 text-lg"></ion-icon>
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-white/60 text-[10px] font-medium leading-none uppercase tracking-wider">Tanggal</span>
                                <span
                                    class="text-white text-base font-bold font-mono leading-normal mt-0.5">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
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
                                    id="live-clock">{{ date('H:i:s') }}</span>
                            </div>
                            <div
                                class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center animate-pulse">
                                <ion-icon name="time-outline" class="text-amber-400 text-lg"></ion-icon>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. BOTTOM SHEET -->
                <div x-data="{ isOpen: false }"
                    class="absolute bottom-0 left-0 right-0 z-50 bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.3)] transition-transform duration-500 ease-[cubic-bezier(0.32,0.72,0,1)]"
                    :class="isOpen ? 'translate-y-0' : 'translate-y-[calc(100%-60px)]'">

                    <div @click="isOpen = !isOpen"
                        class="absolute top-[-15px] left-0 right-0 h-6 w-full z-10 cursor-pointer flex justify-center items-center mt-3">
                        <div class="w-16 h-1.5 bg-slate-400 rounded-full hover:bg-slate-500 transition-colors"></div>
                    </div>

                    <div class="pb-20 pt-8 px-6">
                        <!-- Action Buttons -->
                        <div class="absolute -top-[70px] left-0 right-0 z-[60] px-6">
                            <div class="grid grid-cols-1 gap-4">
                                @if ($presensi->istirahat_in == null)
                                    <button id="absenmasuk"
                                        class="group relative overflow-hidden bg-emerald-500 hover:bg-emerald-600 active:scale-95 transition-all duration-200 h-14 rounded-2xl shadow-lg shadow-emerald-200 flex items-center justify-center gap-3">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                        </div>
                                        <div
                                            class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                            <ion-icon name="cafe-outline" class="text-white text-lg"></ion-icon>
                                        </div>
                                        <span class="text-white font-bold tracking-wide">Mulai Istirahat</span>
                                    </button>
                                @else
                                    <button id="absenpulang"
                                        class="group relative overflow-hidden bg-rose-500 hover:bg-rose-600 active:scale-95 transition-all duration-200 h-14 rounded-2xl shadow-lg shadow-rose-200 flex items-center justify-center gap-3">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                        </div>
                                        <div
                                            class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                            <ion-icon name="briefcase-outline" class="text-white text-lg"></ion-icon>
                                        </div>
                                        <span class="text-white font-bold tracking-wide">Selesai Istirahat</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Floating Location Capsule -->
                        <div
                            class="w-[90%] max-w-sm bg-slate-900 text-white p-1.5 pr-4 rounded-full shadow-xl flex items-center justify-between ring-4 ring-white/10 backdrop-blur-sm relative overflow-hidden mx-auto -mt-2 mb-4">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/30">
                                    <ion-icon name="location" class="text-white text-sm"></ion-icon>
                                </div>
                                <div class="flex-col flex min-w-0 pr-2">
                                    <span
                                        class="text-[9px] text-slate-400 uppercase tracking-wider font-bold leading-tight">Lokasi
                                        Anda</span>
                                    @php
                                        $nama_lokasi = $lokasi_kantor->nama_cabang;
                                        if (isset($cabang) && $general_setting->multi_lokasi) {
                                            $cabang_terpilih = $cabang->where('kode_cabang', $karyawan->kode_cabang)->first();
                                            $nama_lokasi = $cabang_terpilih ? $cabang_terpilih->nama_cabang : 'Pilih Lokasi';
                                        }
                                    @endphp
                                    <span class="text-xs font-bold truncate leading-tight"
                                        id="location-display">{{ $nama_lokasi }}</span>
                                </div>
                            </div>

                            @if (isset($cabang) && $general_setting->multi_lokasi)
                                <div><ion-icon name="chevron-down-outline" class="text-slate-400 text-xl"></ion-icon></div>
                                <select name="cabang" id="cabang"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-50 text-xs"
                                    onchange="document.getElementById('location-display').innerText = this.options[this.selectedIndex].text">
                                    @foreach ($cabang as $item)
                                        <option value="{{ $item->lokasi_cabang }}" {{ $item->kode_cabang == $karyawan->kode_cabang ? 'selected' : '' }} class="text-black bg-white text-xs">
                                            {{ $item->nama_cabang }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="w-2"><input type="hidden" id="cabang" value="{{ $lokasi_kantor->lokasi_cabang }}">
                                </div>
                            @endif
                        </div>

                        <div class="h-2"></div>

                        <!-- Shift Info Card -->
                        <div
                            class="bg-slate-50 border border-slate-100 rounded-2xl p-4 mb-6 flex items-center justify-between">
                            <div class="text-center">
                                <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Shift Kerja</p>
                                <div
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-600 border border-blue-100">
                                    <span class="text-xs font-bold">{{ $jam_kerja->nama_jam_kerja }}</span>
                                </div>
                            </div>
                            <div class="w-px h-8 bg-slate-200"></div>
                            <div class="text-center">
                                <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Jam Kerja</p>
                                <div class="flex items-center gap-1 text-slate-700">
                                    <span
                                        class="text-sm font-bold font-mono">{{ date('H:i', strtotime($jam_kerja->jam_masuk)) }}</span>
                                    <span class="text-slate-300">-</span>
                                    <span
                                        class="text-sm font-bold font-mono">{{ $jam_kerja->jam_pulang ? date('H:i', strtotime($jam_kerja->jam_pulang)) : 'Selesai' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- MAP SECTION -->
                        <div class="w-full text-center mt-2">
                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-2">Lokasi Anda</p>
                            <div id="map"
                                class="w-full h-48 rounded-2xl border border-slate-200 shadow-sm overflow-hidden z-20 relative">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Removed hidden map container as it is moved above -->
            <div id="map-loading" class="hidden"></div>

            <!-- Animation Styles -->
            <style>
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
        </div>

        <!-- Hidden Audio Assets -->
        <div class="hidden">
            <audio id="notifikasi_radius">
                <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
            </audio>
            <audio id="notifikasi_mulaiabsen">
                <source src="{{ asset('assets/sound/mulaiabsen.wav') }}" type="audio/mpeg">
            </audio>
            <audio id="notifikasi_akhirabsen">
                <source src="{{ asset('assets/sound/akhirabsen.wav') }}" type="audio/mpeg">
            </audio>
            <audio id="notifikasi_sudahabsen">
                <source src="{{ asset('assets/sound/sudahabsen.wav') }}" type="audio/mpeg">
            </audio>
            <audio id="notifikasi_absenmasuk">
                <source src="{{ asset('assets/sound/absenmasuk.wav') }}" type="audio/mpeg">
            </audio>
            <audio id="notifikasi_sudahabsenpulang">
                <source src="{{ asset('assets/sound/sudahabsenpulang.mp3') }}" type="audio/mpeg">
            </audio>
            <audio id="notifikasi_absenpulang">
                <source src="{{ asset('assets/sound/absenpulang.mp3') }}" type="audio/mpeg">
            </audio>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        // Realtime Clock
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
            const el = document.getElementById('live-clock');
            if (el) el.innerText = timeString;
        }, 1000);

        $(function () {
            let lokasi;
            let lokasi_user;
            let multi_lokasi = {{ isset($general_setting->multi_lokasi) && $general_setting->multi_lokasi ? 'true' : 'false' }};
            let lokasi_cabang = multi_lokasi && document.getElementById('cabang') ? document.getElementById('cabang').value : "{{ $lokasi_kantor->lokasi_cabang }}";
            let map;

            let notifikasi_radius = document.getElementById('notifikasi_radius');
            let notifikasi_mulaiabsen = document.getElementById('notifikasi_mulaiabsen');
            let notifikasi_akhirabsen = document.getElementById('notifikasi_akhirabsen');
            let notifikasi_sudahabsen = document.getElementById('notifikasi_sudahabsen');
            let notifikasi_absenmasuk = document.getElementById('notifikasi_absenmasuk');
            let notifikasi_sudahabsenpulang = document.getElementById('notifikasi_sudahabsenpulang');
            let notifikasi_absenpulang = document.getElementById('notifikasi_absenpulang');

            let faceRecognitionDetected = 0;
            let faceMatchState = 0; // 0: No Face, 1: Matched, 2: Unknown/Not Matched
            let faceRecognition = "{{ $general_setting->face_recognition }}";

            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            function initWebcam() {
                if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    console.warn('Insecure Context Detected');
                }
                startWebcamJS();
            }

            function startWebcamJS() {
                Webcam.set({
                    height: 480,
                    width: 640,
                    image_format: 'jpeg',
                    jpeg_quality: isMobile ? 80 : 95,
                    fps: isMobile ? 15 : 30,
                    constraints: {
                        video: {
                            width: { ideal: isMobile ? 240 : 640 },
                            height: { ideal: isMobile ? 180 : 480 },
                            facingMode: "user",
                            frameRate: { ideal: isMobile ? 15 : 30 }
                        }
                    }
                });
                Webcam.attach('.webcam-capture');
            }

            Webcam.on('load', function () {
                setTimeout(() => {
                    const video = document.querySelector('.webcam-capture video');
                    if (video) {
                        video.removeAttribute('style');
                        video.style.width = '100%';
                        video.style.height = '100%';
                        video.style.objectFit = 'cover';
                        video.style.position = 'absolute';
                        video.style.top = '0';
                        video.style.left = '0';
                        video.setAttribute('playsinline', 'true');
                        video.setAttribute('muted', 'true');
                        video.setAttribute('autoplay', 'true');
                        video.play().catch(e => console.error(e));
                    }
                }, 500);
            });

            initWebcam();

            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible' && !Webcam.isInitialized()) {
                    initWebcam();
                }
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
            }

            function successCallback(position) {
                try {
                    lokasi = position.coords.latitude + "," + position.coords.longitude;
                    map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);

                    var lok = lokasi_cabang.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];
                    var radius = "{{ $lokasi_kantor->radius_cabang }}";

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
                    L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);

                    document.getElementById('map-loading').style.display = 'none';
                    setTimeout(() => map.invalidateSize(), 500);
                } catch (error) {
                    console.error(error);
                    document.getElementById('map-loading').style.display = 'none';
                }
            }

            function errorCallback(error) {
                console.error(error);
                try {
                    var lok = lokasi_cabang.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];
                    map = L.map('map').setView([lat_kantor, long_kantor], 18);
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                    L.circle([lat_kantor, long_kantor], { color: 'red', fillColor: '#f03', fillOpacity: 0.5, radius: "{{ $lokasi_kantor->radius_cabang }}" }).addTo(map);
                } catch (e) { }
                document.getElementById('map-loading').style.display = 'none';
            }

            // --- FACE RECOGNITION BLOCK START ---
            if (faceRecognition == 1) {
                const loadingIndicator = document.createElement('div');
                loadingIndicator.id = 'face-recognition-loading';
                loadingIndicator.innerHTML = `
                                                        <div class="spinner-border text-light" role="status">
                                                            <span class="sr-only">Memuat pengenalan wajah...</span>
                                                        </div>
                                                        <div class="mt-2 text-light">Memuat model pengenalan wajah...</div>
                                                    `;
                loadingIndicator.style.position = 'absolute';
                loadingIndicator.style.top = '50%';
                loadingIndicator.style.left = '50%';
                loadingIndicator.style.transform = 'translate(-50%, -50%)';
                loadingIndicator.style.zIndex = '1000';
                loadingIndicator.style.textAlign = 'center';
                document.getElementById('facedetection').appendChild(loadingIndicator);

                // Preload model di background
                const modelLoadingPromise = isMobile ? Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                ]) : Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                ]);

                // Mulai pengenalan wajah setelah model dimuat
                modelLoadingPromise.then(() => {
                    document.getElementById('face-recognition-loading').remove();

                    // Debugging: Periksa video stream sebelum memulai face recognition
                    const video = document.querySelector('.webcam-capture video');
                    if (video) {
                        console.log('Video element found:', video);
                        console.log('Video readyState:', video.readyState);
                    }

                    startFaceRecognition();
                }).catch(err => {
                    console.error("Error loading models:", err);
                    document.getElementById('face-recognition-loading').remove();
                    // Coba muat ulang model jika terjadi error
                    setTimeout(() => {
                        console.log('Retrying to load face recognition models');
                        modelLoadingPromise.then(() => {
                            startFaceRecognition();
                        });
                    }, 2000);
                });

                async function getLabeledFaceDescriptions() {
                    const labels = [
                        "{{ $karyawan->nik }}-{{ getNamaDepan(strtolower($karyawan->nama_karyawan)) }}"
                    ];
                    let namakaryawan;

                    // Tampilkan indikator loading khusus untuk data wajah
                    const faceDataLoading = document.createElement('div');
                    faceDataLoading.id = 'face-data-loading';
                    faceDataLoading.innerHTML = `
                                                            <div class="spinner-border text-light" role="status">
                                                                <span class="sr-only">Loading...</span>
                                                            </div>
                                                            <div class="mt-2 text-light">Memuat data wajah...</div>
                                                        `;
                    faceDataLoading.style.position = 'absolute';
                    faceDataLoading.style.top = '50%';
                    faceDataLoading.style.left = '50%';
                    faceDataLoading.style.transform = 'translate(-50%, -50%)';
                    faceDataLoading.style.zIndex = '1000';
                    faceDataLoading.style.textAlign = 'center';
                    document.getElementById('facedetection').appendChild(faceDataLoading);

                    try {
                        const timestamp = new Date().getTime();
                        const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
                        const data = await response.json();
                        console.log('Data wajah yang diterima:', data);

                        const result = await Promise.all(
                            labels.map(async (label) => {
                                const descriptions = [];
                                let validFaceFound = false;

                                // Proses setiap data wajah yang diterima
                                // Batasi hanya 5 foto pertama yang diproses
                                for (const faceData of data.slice(0, 5)) {
                                    try {
                                        console.log('Memproses data wajah:', faceData);

                                        // Cek keberadaan file foto wajah terlebih dahulu
                                        const checkImage = async (label, wajahFile) => {
                                            try {
                                                const imagePath =
                                                    `/storage/uploads/facerecognition/${label}/${wajahFile}?t=${timestamp}`;
                                                console.log('Mencoba mengakses file:', imagePath);

                                                const response = await fetch(imagePath);
                                                if (!response.ok) {
                                                    console.warn(
                                                        `File foto wajah ${wajahFile} tidak ditemukan untuk ${label}`);
                                                    return null;
                                                }
                                                console.log('File wajah berhasil diakses:', imagePath);
                                                return await faceapi.fetchImage(imagePath);
                                            } catch (err) {
                                                console.error(`Error checking image ${wajahFile} for ${label}:`, err);
                                                return null;
                                            }
                                        };

                                        const img = await checkImage(label, faceData.wajah);

                                        if (img) {
                                            try {
                                                console.log('Memulai deteksi wajah untuk file:', faceData.wajah);
                                                let detections;
                                                if (isMobile) {
                                                    detections = await faceapi.detectSingleFace(
                                                        img, new faceapi.TinyFaceDetectorOptions({
                                                            inputSize: 160,
                                                            scoreThreshold: 0.5
                                                        })
                                                    )
                                                        .withFaceLandmarks()
                                                        .withFaceDescriptor();
                                                } else {
                                                    detections = await faceapi.detectSingleFace(
                                                        img, new faceapi.SsdMobilenetv1Options({
                                                            minConfidence: 0.5
                                                        })
                                                    )
                                                        .withFaceLandmarks()
                                                        .withFaceDescriptor();
                                                }
                                                if (detections) {
                                                    console.log('Wajah berhasil dideteksi dan descriptor dibuat');
                                                    descriptions.push(detections.descriptor);
                                                    validFaceFound = true;
                                                }
                                            } catch (err) {
                                                console.error(`Error processing image ${faceData.wajah} for ${label}:`, err);
                                            }
                                        }
                                    } catch (err) {
                                        console.error(`Error processing face data:`, err);
                                    }
                                }

                                if (!validFaceFound) {
                                    console.warn(`Tidak ditemukan wajah valid untuk ${label}`);
                                    namakaryawan = "unknown";
                                } else {
                                    namakaryawan = label;
                                }

                                return new faceapi.LabeledFaceDescriptors(namakaryawan, descriptions);
                            })
                        );

                        // Hapus indikator loading setelah data wajah dimuat
                        document.getElementById('face-data-loading').remove();
                        return result;
                    } catch (error) {
                        console.error('Error dalam getLabeledFaceDescriptions:', error);
                        document.getElementById('face-data-loading').remove();
                        throw error;
                    }
                }

                async function startFaceRecognition() {
                    try {
                        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
                        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);

                        const video = document.querySelector('.webcam-capture video');

                        if (!video) {
                            console.error('Video element tidak ditemukan');
                            setTimeout(startFaceRecognition, 1000);
                            return;
                        }

                        // Tunggu video benar-benar ready
                        if (!video.videoWidth || !video.videoHeight || video.readyState < 2) {
                            console.log('Video belum ready, waiting... readyState:', video.readyState);
                            setTimeout(startFaceRecognition, 500);
                            return;
                        }

                        console.log('Video ready:', video.videoWidth, 'x', video.videoHeight);

                        // Dapatkan parent element
                        const parent = video.parentElement;
                        if (!parent) {
                            console.error('Parent video tidak ditemukan');
                            return;
                        }

                        // Periksa apakah canvas sudah ada untuk menghindari duplikasi
                        const existingCanvas = parent.querySelector('canvas');
                        if (existingCanvas) {
                            console.log('Canvas sudah ada, menghapus yang lama');
                            existingCanvas.remove();
                        }

                        const canvas = faceapi.createCanvasFromMedia(video);

                        // Tunggu sebentar untuk memastikan video dimensions sudah stabil
                        await new Promise(resolve => setTimeout(resolve, 100));

                        // Set dimensi canvas sesuai dengan video
                        const videoWidth = video.videoWidth || video.clientWidth;
                        const videoHeight = video.videoHeight || video.clientHeight;

                        console.log('Setting canvas dimensions:', videoWidth, 'x', videoHeight);

                        canvas.width = videoWidth;
                        canvas.height = videoHeight;
                        canvas.style.position = 'absolute';
                        canvas.style.top = '0';
                        canvas.style.left = '0';
                        canvas.style.width = '100%';
                        canvas.style.height = '100%';
                        canvas.style.pointerEvents = 'none';
                        canvas.style.zIndex = '20'; // Pastikan canvas di atas video

                        // Append canvas ke parent yang sama dengan video
                        parent.appendChild(canvas);
                        console.log('Canvas berhasil ditambahkan ke parent');

                        // --- ABSEN BUTTONS ---
                        let absenButtons = [document.getElementById('absenmasuk'), document.getElementById('absenpulang')];
                        absenButtons = absenButtons.filter(btn => btn !== null);
                        absenButtons.forEach(btn => btn.disabled = true);

                        const ctx = canvas.getContext("2d");
                        if (!ctx) {
                            console.error('Tidak bisa mendapatkan canvas context');
                            return;
                        }

                        const displaySize = {
                            width: videoWidth,
                            height: videoHeight
                        };
                        faceapi.matchDimensions(canvas, displaySize);

                        console.log('Face recognition setup completed, starting detection...');

                        // PERBAIKAN UTAMA: Variable untuk anti-flicker yang lebih stabil
                        let lastDetectionTime = 0;
                        let detectionInterval = isMobile ? 400 : 100; // Interval lebih stabil untuk mobile
                        let isProcessing = false;
                        let consecutiveMatches = 0;
                        const requiredConsecutiveMatches = isMobile ? 2 : 4; // Lebih mudah untuk mobile

                        // PERBAIKAN: Anti-flicker system yang lebih reliable
                        let stableDetectionCount = 0;
                        let noFaceCount = 0;
                        const minStableFrames = isMobile ? 2 : 3; // Minimum frame untuk stabilitas
                        const maxNoFaceFrames = isMobile ? 4 : 5; // Maximum frame tanpa wajah sebelum reset

                        // State tracking untuk smoothing
                        let lastValidDetection = null;
                        let detectionHistory = [];
                        const historySize = isMobile ? 3 : 5;

                        async function detectFaces() {
                            try {
                                // Pastikan video masih aktif
                                if (video.paused || video.ended) {
                                    console.log('Video tidak aktif, menghentikan deteksi');
                                    return [];
                                }

                                if (isMobile) {
                                    const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                                        inputSize: 160,
                                        scoreThreshold: 0.4 // Sedikit lebih rendah untuk mobile
                                    }))
                                        .withFaceLandmarks()
                                        .withFaceDescriptor();
                                    return detection ? [detection] : [];
                                } else {
                                    const detection = await faceapi.detectSingleFace(video, new faceapi.SsdMobilenetv1Options({
                                        minConfidence: 0.5
                                    }))
                                        .withFaceLandmarks()
                                        .withFaceDescriptor();
                                    return detection ? [detection] : [];
                                }
                            } catch (error) {
                                console.error("Error dalam deteksi wajah:", error);
                                return [];
                            }
                        }

                        function updateCanvas() {
                            // Periksa apakah video dan canvas masih valid
                            if (!video || !canvas || !ctx) {
                                console.error('Video, canvas atau context tidak valid');
                                return;
                            }

                            // Periksa apakah video masih memiliki dimensi valid
                            if (!video.videoWidth || !video.videoHeight) {
                                console.log('Video dimensions tidak valid, menunggu...');
                                setTimeout(updateCanvas, 500);
                                return;
                            }

                            if (!isProcessing) {
                                const now = Date.now();
                                if (now - lastDetectionTime > detectionInterval) {
                                    isProcessing = true;
                                    lastDetectionTime = now;

                                    detectFaces()
                                        .then(detections => {
                                            const resizedDetections = faceapi.resizeResults(detections, displaySize);

                                            // PERBAIKAN: Update detection history untuk smoothing
                                            const hasFace = resizedDetections && resizedDetections.length > 0;
                                            detectionHistory.push(hasFace);
                                            if (detectionHistory.length > historySize) {
                                                detectionHistory.shift();
                                            }

                                            // Hitung persentase deteksi positif dalam history
                                            const positiveDetections = detectionHistory.filter(d => d).length;
                                            const detectionRatio = positiveDetections / detectionHistory.length;

                                            // PERBAIKAN: Stabilitas berdasarkan history
                                            if (hasFace && detectionRatio >= 0.6) { // 60% dari history harus positif
                                                stableDetectionCount++;
                                                noFaceCount = 0;
                                                lastValidDetection = resizedDetections[0];
                                            } else if (!hasFace) {
                                                noFaceCount++;
                                                if (noFaceCount >= maxNoFaceFrames) {
                                                    stableDetectionCount = 0;
                                                    lastValidDetection = null;
                                                    // Reset state jika tidak ada wajah
                                                    faceMatchState = 0;
                                                }
                                            }

                                            ctx.clearRect(0, 0, canvas.width, canvas.height);

                                            // Reset status deteksi
                                            faceRecognitionDetected = 0;

                                            // PERBAIKAN: Tampilkan deteksi hanya jika sudah stabil
                                            const shouldShowDetection = stableDetectionCount >= minStableFrames && lastValidDetection;

                                            if (shouldShowDetection) {
                                                const detection = lastValidDetection;

                                                if (detection && detection.descriptor) {
                                                    const match = faceMatcher.findBestMatch(detection.descriptor);

                                                    const box = detection.detection.box;
                                                    const isUnknown = match.toString().includes("unknown");
                                                    const isNotRecognized = match.distance > 0.55;

                                                    // Menentukan warna berdasarkan kondisi
                                                    let boxColor, labelColor, labelText;

                                                    if (isUnknown || isNotRecognized) {
                                                        // Wajah tidak dikenali - warna kuning
                                                        boxColor = '#FFC107';
                                                        labelColor = 'rgba(255, 193, 7, 0.8)';
                                                        labelText = 'Wajah Tidak Dikenali';
                                                        consecutiveMatches = 0;
                                                        faceMatchState = 2; // Set state to Unknown
                                                    } else {
                                                        // Wajah dikenali - warna hijau
                                                        boxColor = '#4CAF50';
                                                        labelColor = 'rgba(76, 175, 80, 0.8)';
                                                        labelText = "{{ $karyawan->nama_karyawan }}";
                                                        consecutiveMatches++;
                                                        if (consecutiveMatches >= requiredConsecutiveMatches) {
                                                            faceRecognitionDetected = 1;
                                                            faceMatchState = 1; // Set state to Matched
                                                        }
                                                    }

                                                    // Menggunakan style modern untuk box deteksi wajah
                                                    ctx.strokeStyle = boxColor;
                                                    ctx.lineWidth = 3;
                                                    ctx.lineJoin = 'round';
                                                    ctx.lineCap = 'round';

                                                    // Fungsi menggambar kotak dengan sudut membulat
                                                    function drawRoundedRect(ctx, x, y, width, height, radius) {
                                                        ctx.beginPath();
                                                        ctx.moveTo(x + radius, y);
                                                        ctx.lineTo(x + width - radius, y);
                                                        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
                                                        ctx.lineTo(x + width, y + height - radius);
                                                        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
                                                        ctx.lineTo(x + radius, y + height);
                                                        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
                                                        ctx.lineTo(x, y + radius);
                                                        ctx.quadraticCurveTo(x, y, x + radius, y);
                                                        ctx.closePath();
                                                        ctx.stroke();
                                                    }

                                                    // Kotak modern dengan efek glow
                                                    ctx.save();
                                                    ctx.shadowColor = boxColor.includes('#4CAF50') ? 'rgba(76, 175, 80, 0.6)' :
                                                        'rgba(255, 193, 7, 0.6)';
                                                    ctx.shadowBlur = 18;
                                                    ctx.strokeStyle = boxColor;
                                                    ctx.lineWidth = 3;

                                                    // Aspect Ratio Correction
                                                    const rect = canvas.getBoundingClientRect();
                                                    const scaleX = rect.width / canvas.width;
                                                    const scaleY = rect.height / canvas.height;
                                                    const aspectCorrection = scaleX / scaleY;

                                                    // Square Logic with Compensation
                                                    const scaleFactor = 1.8;
                                                    const squareSize = Math.min(box.width, box.height) * scaleFactor;

                                                    // Adjust internal height
                                                    const correctedHeight = squareSize * aspectCorrection;

                                                    const squareX = box.x + (box.width - squareSize) / 2;
                                                    const squareY = box.y + (box.height - correctedHeight) / 2;

                                                    // Pass correctedHeight instead of squareSize for height
                                                    drawRoundedRect(ctx, squareX, squareY, squareSize, correctedHeight, 16);
                                                    ctx.restore();

                                                    // Garis pandu horizontal (GRID)
                                                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
                                                    ctx.lineWidth = 1;
                                                    ctx.setLineDash([5, 5]);

                                                    const gridHeight = correctedHeight;

                                                    ctx.beginPath();
                                                    ctx.moveTo(squareX, squareY + gridHeight / 3);
                                                    ctx.lineTo(squareX + squareSize, squareY + gridHeight / 3);
                                                    ctx.stroke();

                                                    ctx.beginPath();
                                                    ctx.moveTo(squareX, squareY + (gridHeight * 2) / 3);
                                                    ctx.lineTo(squareX + squareSize, squareY + (gridHeight * 2) / 3);
                                                    ctx.stroke();

                                                    // Garis pandu vertikal
                                                    ctx.beginPath();
                                                    ctx.moveTo(squareX + squareSize / 3, squareY);
                                                    ctx.lineTo(squareX + squareSize / 3, squareY + gridHeight);
                                                    ctx.stroke();

                                                    ctx.beginPath();
                                                    ctx.moveTo(squareX + (squareSize * 2) / 3, squareY);
                                                    ctx.lineTo(squareX + (squareSize * 2) / 3, squareY + gridHeight);
                                                    ctx.stroke();

                                                    // Reset line style
                                                    ctx.setLineDash([]);

                                                    // Label dengan style modern
                                                    const fontSize = 20;
                                                    ctx.font = `800 ${fontSize}px 'Inter', system-ui, -apple-system, sans-serif`;
                                                    const textWidth = ctx.measureText(labelText).width;

                                                    // Background label
                                                    const labelPadding = 8;
                                                    const labelHeight = fontSize + labelPadding * 2;
                                                    const labelWidth = Math.max(textWidth + labelPadding * 2 + 10, squareSize * 0.6);
                                                    const labelX = squareX + (squareSize - labelWidth) / 2;
                                                    const labelY = squareY + correctedHeight + 4;

                                                    // Gambar background label dengan sudut membulat
                                                    ctx.fillStyle = labelColor;
                                                    ctx.beginPath();
                                                    ctx.moveTo(labelX + 8, labelY);
                                                    ctx.lineTo(labelX + labelWidth - 8, labelY);
                                                    ctx.quadraticCurveTo(labelX + labelWidth, labelY, labelX + labelWidth, labelY + 8);
                                                    ctx.lineTo(labelX + labelWidth, labelY + labelHeight - 8);
                                                    ctx.quadraticCurveTo(labelX + labelWidth, labelY + labelHeight, labelX + labelWidth -
                                                        8, labelY + labelHeight);
                                                    ctx.lineTo(labelX + 8, labelY + labelHeight);
                                                    ctx.quadraticCurveTo(labelX, labelY + labelHeight, labelX, labelY + labelHeight - 8);
                                                    ctx.lineTo(labelX, labelY + 8);
                                                    ctx.quadraticCurveTo(labelX, labelY, labelX + 8, labelY);
                                                    ctx.closePath();
                                                    ctx.fill();

                                                    // Teks label
                                                    ctx.fillStyle = 'white';
                                                    ctx.textAlign = 'center';
                                                    ctx.textBaseline = 'middle';
                                                    ctx.fillText(labelText, squareX + squareSize / 2, labelY + labelHeight / 2);

                                                    // Update status tombol absen
                                                    absenButtons.forEach(btn => btn.disabled = false);
                                                }
                                            } else if (noFaceCount >= maxNoFaceFrames) {
                                                // Tampilkan label di tengah canvas dengan tampilan menarik
                                                const label = "Wajah Tidak Terdeteksi";
                                                const fontSize = 28;
                                                ctx.font = `bold ${fontSize}px Arial`;
                                                ctx.textAlign = "center";
                                                ctx.textBaseline = "middle";
                                                const centerX = canvas.width / 2;
                                                const centerY = canvas.height / 2;

                                                // Ukuran background
                                                const paddingX = 32;
                                                const paddingY = 18;
                                                const textWidth = ctx.measureText(label).width;
                                                const boxWidth = textWidth + paddingX * 2;
                                                const boxHeight = fontSize + paddingY * 2;
                                                const boxX = centerX - boxWidth / 2;
                                                const boxY = centerY - boxHeight / 2;

                                                // Background semi transparan & rounded
                                                ctx.save();
                                                ctx.globalAlpha = 0.85;
                                                ctx.fillStyle = "#F44336";
                                                ctx.beginPath();
                                                ctx.moveTo(boxX + 16, boxY);
                                                ctx.lineTo(boxX + boxWidth - 16, boxY);
                                                ctx.quadraticCurveTo(boxX + boxWidth, boxY, boxX + boxWidth, boxY + 16);
                                                ctx.lineTo(boxX + boxWidth, boxY + boxHeight - 16);
                                                ctx.quadraticCurveTo(boxX + boxWidth, boxY + boxHeight, boxX + boxWidth - 16, boxY +
                                                    boxHeight);
                                                ctx.lineTo(boxX + 16, boxY + boxHeight);
                                                ctx.quadraticCurveTo(boxX, boxY + boxHeight, boxX, boxY + boxHeight - 16);
                                                ctx.lineTo(boxX, boxY + 16);
                                                ctx.quadraticCurveTo(boxX, boxY, boxX + 16, boxY);
                                                ctx.closePath();
                                                ctx.fill();
                                                ctx.restore();

                                                // Efek shadow/glow pada teks
                                                ctx.save();
                                                ctx.shadowColor = "#fff";
                                                ctx.shadowBlur = 8;
                                                ctx.fillStyle = "#fff";
                                                ctx.fillText(label, centerX, centerY);
                                                ctx.restore();

                                                // Disable tombol absen
                                                absenButtons.forEach(btn => btn.disabled = true);
                                            }

                                            isProcessing = false;
                                        })
                                        .catch(err => {
                                            console.error("Error dalam deteksi wajah:", err);
                                            isProcessing = false;
                                        });
                                }
                            }

                            // PERBAIKAN: Gunakan setTimeout untuk mobile agar lebih stabil
                            if (isMobile) {
                                setTimeout(updateCanvas, detectionInterval);
                            } else {
                                requestAnimationFrame(updateCanvas);
                            }
                        }

                        // Mulai loop animasi
                        updateCanvas();

                    } catch (error) {
                        console.error("Error starting face recognition:", error);
                        // Coba inisialisasi ulang face recognition jika terjadi error
                        setTimeout(() => {
                            console.log('Retrying face recognition initialization');
                            startFaceRecognition();
                        }, 2000);
                    }
                }
            }
            // --- FACE RECOGNITION BLOCK END ---

            // ACTION BUTTONS HANDLER
            // HELPER: Validate Face
            function validateFace() {
                if (faceRecognition != 1) return true;

                if (faceMatchState == 2) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajah tidak dikenali',
                        didClose: function () {
                            $("#absenmasuk").prop('disabled', false);
                            $("#absenpulang").prop('disabled', false);
                        }
                    });
                    return false;
                } else if (faceMatchState == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajah tidak terdeteksi',
                        didClose: function () {
                            $("#absenmasuk").prop('disabled', false);
                            $("#absenpulang").prop('disabled', false);
                        }
                    });
                    return false;
                }

                if (faceRecognitionDetected == 0) {
                    Swal.fire({
                        title: 'Oops...',
                        text: 'Wajah tidak dikenali / Belum terdeteksi sepenuhnya',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#F59E0B'
                    });
                    return false;
                }
                return true;
            }

            // HELPER: Submit Presensi Istirahat
            function submitPresensi(status, successAudioObj) {
                // Check Location first
                if (!lokasi) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lokasi Belum Ditemukan',
                        text: 'Sedang mengambil lokasi Anda. Pastikan GPS aktif dan izin lokasi diberikan.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    return false;
                }

                // Check Face
                if (!validateFace()) return false;

                Webcam.snap(function (data_uri) {
                    var image = data_uri;
                    var kode_jam_kerja = "{{ $presensi->kode_jam_kerja }}";

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('presensiistirahat.store') }}',
                        data: {
                            _token: "{{ csrf_token() }}",
                            image: image,
                            status: status,
                            lokasi: lokasi,
                            kode_jam_kerja: kode_jam_kerja,
                            lokasi_cabang: lokasi_cabang
                        },
                        cache: false,
                        success: function (respond) {
                            if (respond.status == true) {
                                if (successAudioObj) successAudioObj.play();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: respond.message,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#10B981'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '/dashboard';
                                    }
                                });
                            } else {
                                if (notifikasi_radius) notifikasi_radius.play();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: respond.message,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#EF4444'
                                });
                            }
                        },
                        error: function (xhr) {
                            let errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi Kesalahan Server';
                            let notifikasi = xhr.responseJSON ? xhr.responseJSON.notifikasi : null;
                            if (notifikasi == "notifikasi_radius" && notifikasi_radius) notifikasi_radius.play();

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errorMessage,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#EF4444'
                            });
                        }
                    });
                });
            }

            // ACTION BUTTONS HANDLER
            $("#absenmasuk").click(function (e) {
                submitPresensi(1, notifikasi_absenmasuk);
            });

            $("#absenpulang").click(function (e) {
                submitPresensi(2, notifikasi_absenpulang);
            });

            $("#cabang").change(function () {
                lokasi_cabang = $(this).val();
                let cabangText = $("#cabang option:selected").text();
                Swal.fire({ icon: 'info', title: 'Lokasi Berubah', text: 'Lokasi cabang: ' + cabangText, timer: 1500, showConfirmButton: false });

                if (map) map.remove();
                document.getElementById('map-loading').style.display = 'block';

                // Re-init map
                var lok = lokasi_cabang.split(",");
                var lat_kantor = lok[0];
                var long_kantor = lok[1];
                map = L.map('map').setView([lat_kantor, long_kantor], 18);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                L.circle([lat_kantor, long_kantor], { color: 'red', fillColor: '#f03', fillOpacity: 0.5, radius: "{{ $lokasi_kantor->radius_cabang }}" }).addTo(map);

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        lokasi = pos.coords.latitude + "," + pos.coords.longitude;
                        L.marker([pos.coords.latitude, pos.coords.longitude]).addTo(map);
                        map.setView([pos.coords.latitude, pos.coords.longitude], 18);
                        document.getElementById('map-loading').style.display = 'none';
                    });
                } else {
                    document.getElementById('map-loading').style.display = 'none';
                }
            });

        });
    </script>
@endpush