@extends('layouts.mobile_modern')

@section('header')
    <!-- Standard App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Edit Aktivitas</div>
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

        /* V7 Specific Styles matching Lembur */
        body {
            background-color: #0f172a;
        }
    </style>

    <!-- Main Mobile Container -->
    <div class="-m-4 relative w-[calc(100%+2rem)] h-[calc(100vh-112px)] overflow-hidden bg-slate-900 flex flex-col">

        <!-- Top Section: Visual (Camera or Image) -->
        <div class="relative w-full h-[60%] shrink-0 group">

            <!-- Main Container -->
            <div class="relative w-full h-full bg-slate-900 overflow-hidden font-inter">

                <!-- 1. HEADER (Floating & Transparent) -->
                <div class="absolute top-0 left-0 right-0 z-40 p-4 flex items-center justify-between pointer-events-none">
                    <a href="{{ route('aktivitaskaryawan.index') }}"
                        class="pointer-events-auto w-10 h-10 flex items-center justify-center bg-black/20 backdrop-blur-md rounded-full text-white active:bg-black/40 transition-colors">
                        <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                    </a>
                    <div class="absolute left-1/2 -translate-x-1/2 px-4 py-1.5 bg-black/20 backdrop-blur-md rounded-full">
                        <h1 class="text-white text-xs font-semibold tracking-wide uppercase whitespace-nowrap">
                            Edit Mode
                        </h1>
                    </div>
                </div>

                <!-- 2. VISUAL LAYER -->
                <div id="camera-wrapper" class="absolute inset-0 z-0">
                    <!-- Specific for Edit: Static Image First -->
                    @if ($aktivitaskaryawan->foto)
                        <img id="current-image-display"
                            src="{{ asset('storage/uploads/aktivitas/' . $aktivitaskaryawan->foto) }}"
                            class="w-full h-full object-cover relative z-20">
                    @else
                        <div id="no-image-display"
                            class="w-full h-full bg-slate-800 flex items-center justify-center relative z-20">
                            <span class="text-slate-500">Tidak ada foto</span>
                        </div>
                    @endif

                    <!-- Webcam Container (Hidden initially) -->
                    <div class="webcam-capture hidden absolute inset-0 z-30" id="webcam-container"></div>

                    <!-- Viewfinder Overlay (Only visible when Camera Active) -->
                    <div id="viewfinder"
                        class="hidden absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-[60%] w-64 h-64 rounded-[2rem] border-2 border-white/20 box-content shadow-[0_0_100px_rgba(0,0,0,0.3)_inset] z-40 pointer-events-none">
                        <div
                            class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white/50 rounded-tl-[1.5rem]">
                        </div>
                        <div
                            class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white/50 rounded-tr-[1.5rem]">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white/50 rounded-bl-[1.5rem]">
                        </div>
                        <div
                            class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white/50 rounded-br-[1.5rem]">
                        </div>
                    </div>

                    <!-- Retake Button (Floating) -->
                    <button type="button" id="btn-toggle-camera"
                        class="absolute bottom-8 right-4 z-50 bg-black/30 backdrop-blur-md border border-white/10 px-4 py-2 rounded-2xl flex items-center gap-2 shadow-lg active:scale-95 transition-all text-white hover:bg-black/50">
                        <ion-icon name="camera-reverse-outline" class="text-xl"></ion-icon>
                        <span class="text-xs font-bold uppercase tracking-wide">Ubah Foto</span>
                    </button>

                    <!-- Cancel Camera Button (Hidden) -->
                    <button type="button" id="btn-cancel-camera"
                        class="hidden absolute bottom-8 left-4 z-50 bg-rose-500/80 backdrop-blur-md border border-white/10 px-4 py-2 rounded-2xl flex items-center gap-2 shadow-lg active:scale-95 transition-all text-white hover:bg-rose-600/80">
                        <ion-icon name="close-circle-outline" class="text-xl"></ion-icon>
                        <span class="text-xs font-bold uppercase tracking-wide">Batal</span>
                    </button>

                </div>
            </div>
        </div>

        <!-- 3. BOTTOM SHEET FORM -->
        <div
            class="flex-1 bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.3)] relative z-20 -mt-10 overflow-hidden flex flex-col">

            <!-- Drag Handle Visual -->
            <div class="absolute top-0 left-0 right-0 h-8 w-full z-10 flex justify-center items-center">
                <div class="w-16 h-1.5 bg-slate-200 rounded-full"></div>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-6 pt-10 pb-20">
                <form action="{{ route('aktivitaskaryawan.update', $aktivitaskaryawan->id) }}" method="POST"
                    enctype="multipart/form-data" id="formAktivitas">
                    @csrf
                    @method('PUT')
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="foto" id="fotoData">
                    <input type="hidden" name="lokasi" id="lokasiData" value="{{ $aktivitaskaryawan->lokasi }}">
                    @if (auth()->user()->hasRole('karyawan'))
                        <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
                    @endif

                    <!-- Location Display -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 mb-6 flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-indigo-600">
                            <ion-icon name="location" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-indigo-400 font-bold uppercase mb-0.5">Lokasi Tersimpan</p>
                            <input type="text" value="{{ $aktivitaskaryawan->lokasi ?? 'Lokasi tidak tersedia' }}"
                                class="bg-transparent border-none p-0 text-xs font-bold text-slate-700 w-full focus:ring-0 active:outline-none"
                                readonly>
                        </div>
                        <button type="button" id="btn-refresh-loc"
                            class="w-8 h-8 rounded-full bg-white text-indigo-500 shadow-sm flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all">
                            <ion-icon name="refresh-outline"></ion-icon>
                        </button>
                    </div>

                    <!-- Description Input -->
                    <div class="form-group mb-6">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Deskripsi Aktivitas</label>
                        <textarea name="aktivitas"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-slate-700 text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder:text-slate-400 h-32 resize-none"
                            placeholder="Contoh: Meeting progress project di client...">{{ $aktivitaskaryawan->aktivitas }}</textarea>
                    </div>

                    <!-- Main Action Button -->
                    <button type="button" id="btn-update"
                        class="w-full h-14 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg shadow-blue-200 flex items-center justify-center gap-3 group active:scale-[0.98] transition-all">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                            <ion-icon name="save-outline" class="text-white text-lg"></ion-icon>
                        </div>
                        <span class="text-white font-bold tracking-wide">Simpan Perubahan</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('myscript')
    <script>
        $(function () {
            let stream = null;
            let currentFacingMode = 'environment';
            let isCameraActive = false;

            function initCamera() {
                Webcam.set({
                    width: 640,
                    height: 480,
                    image_format: 'jpeg',
                    jpeg_quality: 90,
                    facingMode: currentFacingMode
                });
                Webcam.attach('.webcam-capture');

                // Styling fix
                setTimeout(() => {
                    const video = document.querySelector('.webcam-capture video');
                    if (video) {
                        video.style.width = '100%';
                        video.style.height = '100%';
                        video.style.objectFit = 'cover';
                    }
                }, 100);
            }

            // Toggle Camera
            $('#btn-toggle-camera').click(function () {
                isCameraActive = true;
                $('#current-image-display').addClass('hidden');
                $('#no-image-display').addClass('hidden');
                $('#webcam-container').removeClass('hidden');
                $('#viewfinder').removeClass('hidden');
                $('#btn-toggle-camera').addClass('hidden');
                $('#btn-cancel-camera').removeClass('hidden');

                initCamera();
            });

            // Cancel Camera
            $('#btn-cancel-camera').click(function () {
                isCameraActive = false;
                // Reset Webcam
                Webcam.reset();

                $('#current-image-display').removeClass('hidden');
                $('#no-image-display').removeClass('hidden');
                $('#webcam-container').addClass('hidden');
                $('#viewfinder').addClass('hidden');
                $('#btn-toggle-camera').removeClass('hidden');
                $('#btn-cancel-camera').addClass('hidden');
                $('#fotoData').val(''); // Clear captured data
            });

            // Refresh Location
            $('#btn-refresh-loc').click(function () {
                if (navigator.geolocation) {
                    const btn = $(this);
                    btn.addClass('animate-spin');
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            $('#lokasiData').val(lat + "," + lng);
                            $('input[readonly]').val(lat + ", " + lng); // Update visual
                            btn.removeClass('animate-spin');
                            Swal.fire({
                                icon: 'success',
                                title: 'Lokasi Terupdate',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        },
                        (error) => {
                            btn.removeClass('animate-spin');
                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil lokasi' });
                        }
                    );
                }
            });

            // Update Logic
            $('#btn-update').click(function (e) {
                e.preventDefault();

                const aktivitas = $('textarea[name="aktivitas"]').val().trim();

                if (!aktivitas) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Deskripsi Kosong',
                        text: 'Mohon isi deskripsi aktivitas!',
                    });
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span> Menyimpan...');

                if (isCameraActive) {
                    // Capture new photo
                    Webcam.snap(function (data_uri) {
                        $('#fotoData').val(data_uri);
                        $('#formAktivitas').submit();
                    });
                } else {
                    // Submit without new photo
                    $('#formAktivitas').submit();
                }
            });
        });
    </script>
@endpush