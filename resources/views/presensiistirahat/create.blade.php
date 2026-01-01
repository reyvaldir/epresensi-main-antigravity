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
                                    class="text-white text-sm font-bold leading-tight mt-0.5">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="absolute top-24 right-4 z-10">
                        <div
                            class="bg-black/30 backdrop-blur-md border border-white/10 px-4 py-2 rounded-2xl flex items-center gap-2 shadow-lg">
                            <div class="flex flex-col items-end">
                                <span
                                    class="text-white/60 text-[10px] font-medium leading-none uppercase tracking-wider">Waktu</span>
                                <span class="text-white text-sm font-bold font-mono leading-tight mt-0.5"
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
                loadingIndicator.innerHTML = '<div class="spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div><div class="mt-2 text-light">Memuat model...</div>';
                loadingIndicator.style.cssText = 'position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:1000;text-align:center;';
                document.getElementById('facedetection').appendChild(loadingIndicator);

                const modelLoadingPromise = isMobile ? Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                ]) : Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                ]);

                modelLoadingPromise.then(() => {
                    document.getElementById('face-recognition-loading').remove();
                    startFaceRecognition();
                }).catch(err => {
                    document.getElementById('face-recognition-loading').remove();
                });

                async function getLabeledFaceDescriptions() {
                    const labels = ["{{ $karyawan->nik }}-{{ getNamaDepan(strtolower($karyawan->nama_karyawan)) }}"];
                    let namakaryawan;

                    try {
                        const timestamp = new Date().getTime();
                        const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
                        const data = await response.json();

                        const result = await Promise.all(
                            labels.map(async (label) => {
                                const descriptions = [];
                                let validFaceFound = false;

                                for (const faceData of data.slice(0, 5)) {
                                    try {
                                        const imagePath = `/storage/uploads/facerecognition/${label}/${faceData.wajah}?t=${timestamp}`;
                                        const img = await faceapi.fetchImage(imagePath).catch(() => null);

                                        if (img) {
                                            let detections;
                                            if (isMobile) {
                                                detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.5 })).withFaceLandmarks().withFaceDescriptor();
                                            } else {
                                                detections = await faceapi.detectSingleFace(img, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 })).withFaceLandmarks().withFaceDescriptor();
                                            }
                                            if (detections) {
                                                descriptions.push(detections.descriptor);
                                                validFaceFound = true;
                                            }
                                        }
                                    } catch (e) { }
                                }
                                return new faceapi.LabeledFaceDescriptors(validFaceFound ? label : "unknown", descriptions);
                            })
                        );
                        return result;
                    } catch (error) { throw error; }
                }

                async function startFaceRecognition() {
                    try {
                        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
                        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);
                        const video = document.querySelector('.webcam-capture video');

                        if (!video || !video.readyState) {
                            setTimeout(startFaceRecognition, 1000);
                            return;
                        }

                        const canvas = faceapi.createCanvasFromMedia(video);
                        // Set basic canvas styles
                        canvas.style.position = 'absolute';
                        canvas.style.top = '0';
                        canvas.style.left = '0';
                        const parent = video.parentElement;
                        parent.appendChild(canvas);

                        const displaySize = { width: video.videoWidth, height: video.videoHeight };
                        faceapi.matchDimensions(canvas, displaySize);

                        let lastDetectionTime = 0;
                        const detectionInterval = isMobile ? 400 : 100;
                        let isProcessing = false;
                        let consecutiveMatches = 0;
                        const requiredConsecutiveMatches = isMobile ? 2 : 4;
                        let stableDetectionCount = 0;
                        let noFaceCount = 0;
                        const minStableFrames = isMobile ? 2 : 3;
                        const maxNoFaceFrames = isMobile ? 4 : 5;
                        let lastValidDetection = null;

                        async function detectFaces() {
                            try {
                                if (isMobile) {
                                    const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.4 })).withFaceLandmarks().withFaceDescriptor();
                                    return detection ? [detection] : [];
                                } else {
                                    const detection = await faceapi.detectSingleFace(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 })).withFaceLandmarks().withFaceDescriptor();
                                    return detection ? [detection] : [];
                                }
                            } catch (e) { return []; }
                        }

                        function updateCanvas() {
                            if (!video || !canvas) return;
                            if (!isProcessing) {
                                const now = Date.now();
                                if (now - lastDetectionTime > detectionInterval) {
                                    isProcessing = true;
                                    lastDetectionTime = now;
                                    detectFaces().then(detections => {
                                        const resizedDetections = faceapi.resizeResults(detections, displaySize);
                                        const hasFace = resizedDetections && resizedDetections.length > 0;

                                        if (hasFace) {
                                            stableDetectionCount++;
                                            noFaceCount = 0;
                                            lastValidDetection = resizedDetections[0];
                                        } else {
                                            noFaceCount++;
                                            if (noFaceCount >= maxNoFaceFrames) {
                                                stableDetectionCount = 0;
                                                lastValidDetection = null;
                                            }
                                        }

                                        const ctx = canvas.getContext("2d");
                                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                                        faceRecognitionDetected = 0;

                                        if (stableDetectionCount >= minStableFrames && lastValidDetection) {
                                            const detection = lastValidDetection;
                                            if (detection.descriptor) {
                                                const match = faceMatcher.findBestMatch(detection.descriptor);
                                                const box = detection.detection.box;
                                                const isUnknown = match.toString().includes("unknown");
                                                const isNotRecognized = match.distance > 0.55;

                                                let boxColor = (isUnknown || isNotRecognized) ? '#FFC107' : '#4CAF50';
                                                let labelText = (isUnknown || isNotRecognized) ? 'Wajah Tidak Dikenali' : "{{ $karyawan->nama_karyawan }}";

                                                if (!isUnknown && !isNotRecognized) {
                                                    consecutiveMatches++;
                                                    if (consecutiveMatches >= requiredConsecutiveMatches) faceRecognitionDetected = 1;
                                                } else {
                                                    consecutiveMatches = 0;
                                                }

                                                const drawBox = new faceapi.draw.DrawBox(box, { label: labelText, boxColor: boxColor });
                                                drawBox.draw(canvas);
                                            }
                                        }
                                        isProcessing = false;
                                    });
                                }
                            }
                            requestAnimationFrame(updateCanvas);
                        }
                        updateCanvas();
                    } catch (e) { }
                }
            }
            // --- FACE RECOGNITION BLOCK END ---

            // Button Handlers
            $("#absenmasuk").click(function () {
                if (handleAbsen(1, 'absenmasuk')) return;
            });

            $("#absenpulang").click(function () {
                if (handleAbsen(2, 'absenpulang')) return;
            });

            function handleAbsen(status, btnId) {
                const btn = $("#" + btnId);
                const originalHtml = btn.html();

                btn.prop('disabled', true);
                btn.html('<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span>Loading...</span>');

                let image = '';
                Webcam.snap(function (uri) { image = uri; });

                if (faceRecognition == 1 && faceRecognitionDetected == 0) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Wajah tidak terdeteksi' });
                    btn.prop('disabled', false);
                    btn.html(originalHtml);
                    return true;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ route('presensiistirahat.store') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        image: image,
                        status: status,
                        lokasi: lokasi,
                        lokasi_cabang: lokasi_cabang,
                        kode_jam_kerja: "{{ $jam_kerja->kode_jam_kerja }}"
                    },
                    success: function (data) {
                        if (data.status == true) {
                            if (status == 1) notifikasi_mulaiabsen.play();
                            else notifikasi_akhirabsen.play();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 3000
                            }).then(function () {
                                window.location.href = '/dashboard';
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                            btn.prop('disabled', false);
                            btn.html(originalHtml);
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({ icon: 'error', title: 'Oops...', text: xhr.responseJSON.message || 'Terjadi kesalahan' });
                        btn.prop('disabled', false);
                        btn.html(originalHtml);
                    }
                });
                return false;
            }

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