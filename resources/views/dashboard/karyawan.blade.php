@extends('layouts.mobile_modern')

@section('header', 'Dashboard')
@section('subheader', date('l, d F Y'))

@section('content')
    <!-- User Welcome -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 mb-6 relative overflow-hidden">
        <div class="relative z-10 flex flex-col items-center text-center">

            <!-- Card Header: Clock & Logout -->
            <div class="w-full flex justify-between items-start mb-4">
                <div class="text-left">
                    <h3 class="text-lg font-bold text-slate-800 leading-none" id="jam-card">
                        {{ date('H:i') }}
                    </h3>
                    <p class="text-[10px] text-slate-500 font-medium mt-0.5">
                        {{ DateToIndo(date('Y-m-d')) }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                        <ion-icon name="log-out-outline" class="text-2xl"></ion-icon>
                    </button>
                </form>
            </div>

            <a href="{{ route('profile.index') }}" class="flex flex-col items-center group">
                @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                    <div class="h-20 w-20 rounded-full border-4 border-white shadow-md bg-cover bg-center mb-3 group-hover:scale-105 transition-transform"
                        style="background-image: url('{{ getfotoKaryawan($karyawan->foto) }}')">
                    </div>
                @else
                    <div
                        class="h-20 w-20 rounded-full border-4 border-white shadow-md bg-slate-200 flex items-center justify-center text-slate-400 mb-3 group-hover:scale-105 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                @endif

                <h2 class="text-xl font-bold text-slate-800 group-hover:text-primary transition-colors">Hi,
                    {{ formatName2($karyawan->nama_karyawan) }} 👋</h2>
                <p class="text-slate-500 text-sm mb-4">{{ $karyawan->nama_jabatan }} • {{ $karyawan->nama_dept }}</p>
            </a>



            <!-- Stats -->
            <div class="grid grid-cols-2 gap-3 w-full border-t border-slate-100 pt-4">
                <div class="flex flex-col">
                    <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Jam Masuk</span>
                    <span class="text-lg font-bold text-slate-800">
                        @if ($presensi && $presensi->jam_in)
                            {{ date('H:i', strtotime($presensi->jam_in)) }}
                        @else
                            --:--
                        @endif
                    </span>
                </div>
                <div class="flex flex-col border-l border-slate-100">
                    <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Jam Pulang</span>
                    <span class="text-lg font-bold text-slate-800">
                        @if ($presensi && $presensi->jam_out)
                            {{ date('H:i', strtotime($presensi->jam_out)) }}
                        @else
                            --:--
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>


    <!-- Attendance Action Area (MASSIVE BUTTONS) -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <!-- Clock In -->
        @if ($presensi && $presensi->jam_in)
            <button disabled
                class="flex flex-col items-center justify-center p-6 rounded-2xl bg-slate-100 border-2 border-slate-200 text-slate-400 cursor-not-allowed opacity-75">
                <div class="h-12 w-12 rounded-full bg-slate-200 flex items-center justify-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-bold">Sudah Masuk</span>
            </button>
        @else
            <a href="/presensi/create"
                class="flex flex-col items-center justify-center p-6 rounded-2xl bg-emerald-50 border-2 border-emerald-100 text-emerald-600 shadow-sm active:scale-95 transition-transform hover:bg-emerald-100">
                <div
                    class="h-14 w-14 rounded-full bg-emerald-500 text-white flex items-center justify-center mb-3 shadow-lg shadow-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
                <span class="font-bold text-lg">Absen Masuk</span>
            </a>
        @endif

        <!-- Clock Out -->
        @if ($presensi && $presensi->jam_out)
            <button disabled
                class="flex flex-col items-center justify-center p-6 rounded-2xl bg-slate-100 border-2 border-slate-200 text-slate-400 cursor-not-allowed opacity-75">
                <div class="h-12 w-12 rounded-full bg-slate-200 flex items-center justify-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-bold">Sudah Pulang</span>
            </button>
        @else
            <a href="/presensi/create"
                class="flex flex-col items-center justify-center p-6 rounded-2xl bg-rose-50 border-2 border-rose-100 text-rose-600 shadow-sm active:scale-95 transition-transform hover:bg-rose-100">
                <div
                    class="h-14 w-14 rounded-full bg-rose-500 text-white flex items-center justify-center mb-3 shadow-lg shadow-rose-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
                <span class="font-bold text-lg">Absen Pulang</span>
            </a>
        @endif
    </div>

    <!-- Monthly Stats Summary -->
    <div class="grid grid-cols-4 gap-2 mb-6">
        <div class="bg-white p-3 rounded-xl border border-slate-100 text-center shadow-sm">
            <span
                class="block text-2xl font-bold text-emerald-500 mb-1">{{ $rekappresensi ? $rekappresensi->hadir : 0 }}</span>
            <span class="text-[10px] text-slate-400 font-bold uppercase">Hadir</span>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-100 text-center shadow-sm">
            <span
                class="block text-2xl font-bold text-rose-500 mb-1">{{ $rekappresensi ? $rekappresensi->sakit : 0 }}</span>
            <span class="text-[10px] text-slate-400 font-bold uppercase">Sakit</span>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-100 text-center shadow-sm">
            <span
                class="block text-2xl font-bold text-amber-500 mb-1">{{ $rekappresensi ? $rekappresensi->izin : 0 }}</span>
            <span class="text-[10px] text-slate-400 font-bold uppercase">Izin</span>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-100 text-center shadow-sm">
            <span class="block text-2xl font-bold text-blue-500 mb-1">{{ $rekappresensi ? $rekappresensi->cuti : 0 }}</span>
            <span class="text-[10px] text-slate-400 font-bold uppercase">Cuti</span>
        </div>
    </div>

    <!-- Quick Menu -->
    <h3 class="font-bold text-slate-800 text-lg mb-3">Menu Cepat</h3>
    <div class="grid grid-cols-4 gap-3 mb-6">
        <a href="{{ route('karyawan.idcard', Crypt::encrypt($karyawan->nik)) }}"
            class="flex flex-col items-center gap-2 group">
            <div
                class="h-14 w-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center transition-colors group-hover:bg-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <span class="text-xs font-medium text-slate-600 text-center">ID Card</span>
        </a>

        <a href="{{ route('presensiistirahat.create') }}" class="flex flex-col items-center gap-2 group">
            <div
                class="h-14 w-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center transition-colors group-hover:bg-orange-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <span class="text-xs font-medium text-slate-600 text-center">Istirahat</span>
        </a>

        <a href="{{ route('lembur.index') }}" class="flex flex-col items-center gap-2 group">
            <div
                class="h-14 w-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center transition-colors group-hover:bg-purple-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <span class="text-xs font-medium text-slate-600 text-center">Lembur</span>
        </a>

        <a href="{{ route('slipgaji.index') }}" class="flex flex-col items-center gap-2 group">
            <div
                class="h-14 w-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center transition-colors group-hover:bg-teal-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <span class="text-xs font-medium text-slate-600 text-center">Slip Gaji</span>
        </a>

        @can('aktivitaskaryawan.index')
            <a href="{{ route('aktivitaskaryawan.index') }}" class="flex flex-col items-center gap-2 group">
                <div
                    class="h-14 w-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center transition-colors group-hover:bg-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <span class="text-xs font-medium text-slate-600 text-center">Aktivitas</span>
            </a>
        @endcan

        @can('kunjungan.index')
            <a href="{{ route('kunjungan.index') }}" class="flex flex-col items-center gap-2 group">
                <div
                    class="h-14 w-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center transition-colors group-hover:bg-sky-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <span class="text-xs font-medium text-slate-600 text-center">Kunjungan</span>
            </a>
        @endcan
    </div>

    <!-- History Preview -->
    <!-- History Preview with Tabs -->
    <div x-data="{ activeTab: 'presensi' }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800 text-lg">Aktivitas Terakhir</h3>
            <a href="/presensi/histori" class="text-sm text-primary font-medium hover:underline">Lihat Semua</a>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex bg-slate-100 p-1 rounded-xl mb-4">
            <button @click="activeTab = 'presensi'"
                :class="activeTab === 'presensi' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">
                Presensi
            </button>
            <button @click="activeTab = 'lembur'"
                :class="activeTab === 'lembur' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">
                Lembur
            </button>
        </div>

        <!-- Presensi Content -->
        <div x-show="activeTab === 'presensi'" class="space-y-3" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @foreach ($datapresensi as $d)
                @php
                    $jam_in_minute_ts = strtotime(date('Y-m-d H:i', strtotime($d->jam_in)));
                    $jam_masuk_minute_ts = strtotime(date('Y-m-d H:i', strtotime($d->tanggal . ' ' . $d->jam_masuk)));
                    
                    $jam_out_minute_ts = $d->jam_out ? strtotime(date('Y-m-d H:i', strtotime($d->jam_out))) : null;
                    $jam_pulang_minute_ts = strtotime(date('Y-m-d H:i', strtotime($d->tanggal . ' ' . $d->jam_pulang)));
                    
                    $is_late = $jam_in_minute_ts > $jam_masuk_minute_ts;
                    $is_early_out = $jam_out_minute_ts && $jam_out_minute_ts < $jam_pulang_minute_ts;
                    
                    $late_msg = '';
                    if ($is_late) {
                        $delay_seconds = $jam_in_minute_ts - $jam_masuk_minute_ts;
                        $delay_hours = floor($delay_seconds / 3600);
                        $delay_minutes = floor(($delay_seconds % 3600) / 60);
                        
                        $late_msg = 'Telat ';
                        if ($delay_hours > 0) {
                            $late_msg .= $delay_hours . 'j ';
                        }
                        $late_msg .= $delay_minutes . 'm';
                    }
                    
                    $early_msg = '';
                    if ($is_early_out) {
                        $early_seconds = $jam_pulang_minute_ts - $jam_out_minute_ts;
                        $early_hours = floor($early_seconds / 3600);
                        $early_minutes = floor(($early_seconds % 3600) / 60);
                        
                        $early_msg = 'Awal ';
                        if ($early_hours > 0) {
                            $early_msg .= $early_hours . 'j ';
                        }
                        $early_msg .= $early_minutes . 'm';
                    }
                @endphp
                <div
                    class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex items-top justify-between hover:bg-slate-50 transition-colors gap-3">
                    <!-- Icon -->
                    <div class="shrink-0 mt-0.5">
                        <div
                            class="h-10 w-10 rounded-lg {{ $d->jam_in ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }} flex items-center justify-center">
                            @if ($d->status == 'h')
                                <ion-icon name="finger-print-outline" class="text-xl"></ion-icon>
                            @elseif($d->status == 'i')
                                <ion-icon name="document-text-outline" class="text-xl"></ion-icon>
                            @elseif($d->status == 's')
                                <ion-icon name="medkit-outline" class="text-xl"></ion-icon>
                            @elseif($d->status == 'c')
                                <ion-icon name="calendar-outline" class="text-xl"></ion-icon>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 text-sm leading-tight">{{ DateToIndo($d->tanggal) }}</h4>
                        <p class="text-[10px] text-slate-500 font-bold uppercase mb-1.5">{{ $d->nama_jam_kerja ?? 'Shift Umum' }}</p>

                        @if ($d->status == 'h')
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                                <!-- IN Row -->
                                <div class="flex items-center gap-1.5 align-middle">
                                    @if ($d->jam_in)
                                        <span
                                            class="{{ $is_late ? 'bg-rose-50 text-rose-500 border-rose-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} px-1.5 py-0.5 rounded text-[10px] font-bold tracking-tight inline-block leading-none border">
                                            IN: {{ date('H:i', strtotime($d->jam_in)) }}
                                        </span>
                                        @if ($is_late && $late_msg)
                                            <span
                                                class="bg-rose-50 text-rose-500 px-1.5 py-0.5 rounded text-[10px] font-bold tracking-tight inline-block leading-none border border-rose-100">
                                                {{ $late_msg }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="bg-rose-50 text-rose-600 px-1.5 py-0.5 rounded text-[10px] font-bold border border-rose-100">Belum Absen</span>
                                    @endif
                                </div>

                                <!-- Separator -->
                                <div class="hidden sm:block w-[1.5px] h-3.5 bg-slate-200 mx-0.5"></div>

                                <!-- OUT Row -->
                                <div class="flex items-center gap-1.5 align-middle">
                                    @if ($d->jam_out)
                                        <span
                                            class="{{ $is_early_out ? 'bg-rose-50 text-rose-500 border-rose-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} px-1.5 py-0.5 rounded text-[10px] font-bold tracking-tight inline-block leading-none border">
                                            OUT: {{ date('H:i', strtotime($d->jam_out)) }}
                                        </span>
                                        @if ($is_early_out && $early_msg)
                                            <span
                                                class="bg-rose-50 text-rose-500 px-1.5 py-0.5 rounded text-[10px] font-bold tracking-tight inline-block leading-none border border-rose-100">
                                                {{ $early_msg }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded text-[10px] font-bold border border-slate-200">Belum Pulang</span>
                                    @endif
                                </div>
                            </div>
                        @else
                             <!-- Non-Presence Status -->
                            <p class="text-sm font-medium text-slate-600 mt-1">
                                @if ($d->status == 'i') Izin: {{ $d->keterangan_izin }}
                                @elseif ($d->status == 's') Sakit: {{ $d->keterangan_sakit }}
                                @elseif ($d->status == 'c') Cuti: {{ $d->keterangan_cuti }}
                                @endif
                            </p>
                        @endif
                    </div>

                    <!-- Right Schedule -->
                    <div class="text-right shrink-0">
                         <span class="block text-[10px] font-bold text-slate-400 mb-0.5">Jadwal</span>
                         <span class="text-[10px] font-bold text-slate-700 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">
                            {{ date('H:i', strtotime($d->jam_masuk)) }} - {{ $d->jam_pulang ? date('H:i', strtotime($d->jam_pulang)) : '??' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Lembur Content -->
        <!-- Lembur Content -->
        <div x-show="activeTab === 'lembur'" class="space-y-3" style="display: none;"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0">
            @if (isset($lembur) && count($lembur) > 0)
                @foreach ($lembur as $d)
                    <a href="{{ route('lembur.createpresensi', Crypt::encrypt($d->id)) }}" class="block">
                        <div
                            class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                                    <ion-icon name="timer-outline" class="text-xl"></ion-icon>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">{{ DateToIndo($d->tanggal) }}</h4>
                                    <p class="text-xs text-slate-500 line-clamp-1">{{ $d->keterangan }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if ($d->lembur_in != null)
                                            <span class="text-[10px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded">
                                                IN: {{ date('H:i', strtotime($d->lembur_in)) }}
                                            </span>
                                        @else
                                            <span class="text-[10px] bg-rose-50 text-rose-600 px-1.5 py-0.5 rounded">
                                                Belum Absen
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-slate-300">|</span>
                                        @if ($d->lembur_out != null)
                                            <span class="text-[10px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded">
                                                OUT: {{ date('H:i', strtotime($d->lembur_out)) }}
                                            </span>
                                        @else
                                            <span class="text-[10px] bg-rose-50 text-rose-600 px-1.5 py-0.5 rounded">
                                                Belum Absen
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right pl-2">
                                <span class="block text-[10px] font-bold text-slate-500 mb-1">Jadwal</span>
                                <span class="text-xs font-medium text-slate-700">
                                    {{ date('H:i', strtotime($d->lembur_mulai)) }} -
                                    {{ date('H:i', strtotime($d->lembur_selesai)) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="text-center py-8">
                    <div
                        class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                        <ion-icon name="file-tray-outline" class="text-3xl"></ion-icon>
                    </div>
                    <p class="text-sm text-slate-500">Belum ada data lembur bulan ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Birthday Modal Logic (Preserved) -->
    @if (isset($is_birthday) && $is_birthday)
        <div x-data="{ open: true }" x-show="open"
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden text-center p-6 relative">
                <button @click="open = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-6xl animate-bounce mb-4">🎂</div>
                <h2 class="text-xl font-bold text-slate-800 mb-2">Selamat Ulang Tahun!</h2>
                <p class="text-slate-600 mb-4">Hi <span class="font-bold text-primary">{{ $karyawan->nama_karyawan }}</span>,
                    semoga panjang umur dan sehat selalu! 🎉</p>
                <button @click="open = false"
                    class="w-full py-3 bg-primary text-white rounded-xl font-bold hover:bg-blue-700 transition">Terima
                    Kasih!</button>
            </div>
            <!-- Confetti Effect -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden" style="z-index: 59;">
                <!-- Simple CSS confetti can be added here if needed -->
            </div>
        </div>
    @endif

@endsection

@push('myscript')
    <script>
        // Real-time Clock for Card Header
        function updateClock() {
            var now = new Date();
            var hours = String(now.getHours()).padStart(2, '0');
            var minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('jam-card').textContent = hours + ":" + minutes;
        }

        setInterval(updateClock, 1000);
        updateClock(); // Initial call
    </script>
@endpush