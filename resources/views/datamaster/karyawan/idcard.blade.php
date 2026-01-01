@extends('layouts.mobile_modern')
@section('content')
    @php
        use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
    @endphp
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">ID Card Digital</h1>
        </div>
    </div>

    <div class="flex flex-col items-center pb-24">

        <!-- ID Card Wrapper -->
        <div id="idcard-area" class="relative bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100"
            style="width: 320px; background-color: #ffffff;">

            <!-- Elegant Header -->
            <!-- Curved Bottom shape for elegance -->
            <div class="relative w-full h-44 bg-blue-600 overflow-hidden"
                style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-bottom-left-radius: 50% 20px; border-bottom-right-radius: 50% 20px;">

                <!-- Background Pattern -->
                <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-5 rounded-full -ml-16 -mb-10 blur-xl"></div>

                <!-- Header Content: Centered Layout for better proportion -->
                <div class="absolute top-0 left-0 right-0 p-6 flex flex-col items-center justify-start z-10">

                    <!-- Logo -->
                    <div class="mb-2">
                        @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                            <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo"
                                class="h-10 w-auto brightness-0 invert drop-shadow-md">
                        @else
                            <div
                                class="h-10 w-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                                <span class="text-white font-bold text-[10px]">LOGO</span>
                            </div>
                        @endif
                    </div>

                    <!-- Company Name -->
                    <div class="text-center">
                        <h3 class="text-white font-bold tracking-widest text-sm uppercase drop-shadow-sm opacity-95">
                            {{ $generalsetting->nama_perusahaan ?? 'COMPANY NAME' }}
                        </h3>
                        <div class="h-0.5 w-12 bg-white/30 mx-auto mt-2 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Profile Picture Section -->
            <!-- Overlapping the curved header -->

            <div class="relative flex justify-center -mt-16 mb-2 z-20">
                <div class="p-1.5 bg-white rounded-full shadow-lg">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-slate-100 bg-slate-100">
                        @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" class="w-full h-full object-cover" alt="Profile">
                        @else
                            <img src="{{ asset('assets/template/img/sample/avatar/avatar1.jpg') }}"
                                class="w-full h-full object-cover" alt="Profile">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Body Content -->
            <div class="px-5 pb-5 text-center">
                <!-- Name & Role -->
                <div class="mb-3">
                    <h2 class="text-lg font-bold text-slate-800 uppercase leading-snug tracking-tight mb-1">
                        {{ $karyawan->nama_karyawan }}
                    </h2>
                    <span
                        class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider border border-blue-100">
                        {{ $karyawan->nama_jabatan }}
                    </span>
                </div>

                <!-- Info Box -->
                <div class="bg-slate-50 rounded-xl p-3 mb-4 text-left space-y-2 border border-slate-100">

                    <!-- NIK -->
                    <div class="flex items-center gap-3">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-sm text-sm border border-slate-100">
                            <ion-icon name="id-card-outline"></ion-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span
                                class="text-[11px] text-slate-400 block uppercase font-bold tracking-widest leading-tight mb-1">ID
                                Karyawan</span>
                            <span class="text-xs font-bold text-slate-700 block">{{ $karyawan->nik }}</span>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="flex items-center gap-3">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-sm text-sm border border-slate-100">
                            <ion-icon name="call-outline"></ion-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span
                                class="text-[11px] text-slate-400 block uppercase font-bold tracking-widest leading-tight mb-1">No.
                                Handphone</span>
                            <span class="text-xs font-bold text-slate-700 block">{{ $karyawan->no_hp }}</span>
                        </div>
                    </div>

                    <!-- Dept -->
                    <div class="flex items-center gap-3">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-sm text-sm border border-slate-100">
                            <ion-icon name="business-outline"></ion-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span
                                class="text-[11px] text-slate-400 block uppercase font-bold tracking-widest leading-tight mb-1">Departemen</span>
                            <span class="text-xs font-bold text-slate-700 block">{{ $karyawan->nama_dept }}</span>
                        </div>
                    </div>
                </div>

                <!-- Barcode -->
                <div class="flex flex-col items-center justify-center pt-2 border-t border-slate-100 border-dashed">
                    <div class="mix-blend-multiply mb-1" style="opacity: 0.85;">
                        {!! DNS1D::getBarcodeHTML($karyawan->nik, 'C128', 1.6, 40, 'black') !!}
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono tracking-[0.2em]">{{ $karyawan->nik }}</span>
                </div>
            </div>

            <!-- Bottom Border -->
            <div class="h-1.5 w-full bg-gradient-to-r from-blue-600 to-indigo-600"></div>
        </div>

        <!-- Action Button -->
        <button id="download-idcard"
            class="mt-8 px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 flex items-center gap-2 active:scale-95 transition-transform hover:shadow-blue-500/50">
            <ion-icon name="download-outline" class="text-xl"></ion-icon>
            <span>Simpan ID Card</span>
        </button>

    </div>

    @push('myscript')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var btn = document.getElementById('download-idcard');

                if (btn) {
                    btn.addEventListener('click', function () {
                        var originalText = btn.innerHTML;
                        btn.innerHTML = '<ion-icon name="sync-outline" class="animate-spin text-xl"></ion-icon> <span>Memproses...</span>';
                        btn.disabled = true;
                        btn.classList.add('opacity-75');

                        var area = document.getElementById('idcard-area');

                        // Force white background for the canvas capture
                        var options = {
                            scale: 3,
                            useCORS: true,
                            backgroundColor: '#ffffff',
                            logging: false
                        };

                        setTimeout(function () {
                            html2canvas(area, options).then(function (canvas) {
                                var link = document.createElement('a');
                                link.download = 'ID-Card-{{ $karyawan->nama_karyawan }}.jpg';
                                link.href = canvas.toDataURL('image/jpeg', 1.0);
                                link.click();

                                btn.innerHTML = originalText;
                                btn.disabled = false;
                                btn.classList.remove('opacity-75');
                            }).catch(function (e) {
                                console.error(e);
                                alert('Gagal menyimpan gambar. Coba lagi.');
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                                btn.classList.remove('opacity-75');
                            });
                        }, 500);
                    });
                }
            });
        </script>
    @endpush
@endsection