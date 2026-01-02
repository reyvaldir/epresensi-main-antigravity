@extends('layouts.mobile_modern')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Pilih Jam Kerja</h1>
        </div>
    </div>

    <!-- Page Title & Description -->
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-800 leading-tight">Jadwal Kerja</h2>
        <p class="text-sm text-slate-500 mt-1 font-medium">Silakan pilih jam kerja yang sesuai untuk presensi hari ini.</p>
    </div>

    <!-- Working Hours Grid/List -->
    <div class="space-y-4 pb-24">
        @foreach ($jamkerja as $item)
            <div onclick="pilihJamKerja('{{ $item->kode_jam_kerja }}')"
                class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all group cursor-pointer active:scale-[0.98]">

                <!-- Card Header -->
                <div class="flex items-center gap-4 mb-5">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <ion-icon name="time-outline" class="text-2xl"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 text-base leading-none mb-1.5">{{ $item->nama_jam_kerja }}</h3>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Shift Aktif</span>
                        </div>
                    </div>
                    <div class="text-slate-300 group-hover:text-emerald-500 transition-colors">
                        <ion-icon name="chevron-forward-outline" class="text-xl"></ion-icon>
                    </div>
                </div>

                <!-- Time Grid -->
                <div class="grid grid-cols-{{ $item->istirahat == 1 ? '3' : '2' }} gap-3">
                    <!-- Jam Masuk -->
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex flex-col items-center text-center">
                        <div
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                            <ion-icon name="sunny-outline" class="text-amber-500"></ion-icon> Jam Masuk
                        </div>
                        <span class="text-sm font-bold text-slate-700">{{ date('H:i', strtotime($item->jam_masuk)) }}</span>
                    </div>

                    <!-- Jam Pulang -->
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex flex-col items-center text-center">
                        <div
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                            <ion-icon name="moon-outline" class="text-indigo-500"></ion-icon> Jam Pulang
                        </div>
                        <span class="text-sm font-bold text-slate-700">{{ date('H:i', strtotime($item->jam_pulang)) }}</span>
                    </div>

                    @if ($item->istirahat == 1)
                        <!-- Istirahat -->
                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex flex-col items-center text-center">
                            <div
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                <ion-icon name="cafe-outline" class="text-emerald-500"></ion-icon> Istirahat
                            </div>
                            <span class="text-sm font-bold text-slate-700">
                                {{ date('H:i', strtotime($item->jam_awal_istirahat)) }} -
                                {{ date('H:i', strtotime($item->jam_akhir_istirahat)) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($jamkerja->isEmpty())
            <div class="text-center py-20">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-300 mb-4">
                    <ion-icon name="calendar-outline" class="text-4xl"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Jadwal Kosong</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-[200px] mx-auto">Tidak ada pilihan jam kerja yang tersedia saat ini.
                </p>
            </div>
        @endif
    </div>
@endsection

@push('myscript')
    <script>
        function pilihJamKerja(kode_jam_kerja) {
            Swal.fire({
                title: 'Sedang Memproses',
                html: 'Menyiapkan halaman presensi...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: 'rounded-2xl shadow-xl',
                }
            });

            setTimeout(function () { window.location.href = '/presensi/create?kode_jam_kerja=' + kode_jam_kerja; }, 600);
        }
    </script>
@endpush