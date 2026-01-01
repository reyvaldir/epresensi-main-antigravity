@extends('layouts.mobile_modern')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="/dashboard"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Histori Presensi</h1>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
        <form action="{{ route('presensi.histori') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <!-- Date Range Picker -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Dari Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="calendar-outline"></ion-icon>
                        </div>
                        <input type="date" name="dari" value="{{ Request('dari') }}"
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-700 transition-all font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Sampai Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="calendar-outline"></ion-icon>
                        </div>
                        <input type="date" name="sampai" value="{{ Request('sampai') }}"
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-700 transition-all font-medium">
                    </div>
                </div>
            </div>
            <button type="submit"
                class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 active:scale-95 transition-transform hover:bg-blue-700 mt-2">
                <ion-icon name="search-outline" class="text-lg"></ion-icon>
                <span>Cari Data</span>
            </button>
        </form>
    </div>

    <!-- Results -->
    <div class="space-y-3 pb-20">
        @if ($datapresensi->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-300 mb-4">
                    <ion-icon name="document-text-outline" class="text-4xl"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Tidak Ada Data</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-[200px] mx-auto">Belum ada riwayat presensi yang ditemukan untuk
                    periode ini.</p>
            </div>
        @else
            @foreach ($datapresensi as $d)
                <!-- Card Logic -->
                @php
                    $jam_in_minute_ts = strtotime(date('Y-m-d H:i', strtotime($d->jam_in)));
                    $jam_masuk_minute_ts = strtotime(date('Y-m-d H:i', strtotime($d->tanggal . ' ' . $d->jam_masuk)));

                    $jam_out_minute_ts = $d->jam_out ? strtotime(date('Y-m-d H:i', strtotime($d->jam_out))) : null;
                    $jam_pulang_minute_ts = strtotime(date('Y-m-d H:i', strtotime($d->tanggal . ' ' . $d->jam_pulang)));

                    $is_late = $jam_in_minute_ts > $jam_masuk_minute_ts;
                    $is_early_out = $jam_out_minute_ts && $jam_out_minute_ts < $jam_pulang_minute_ts;

                    $late_msg = "";
                    $early_msg = "";

                    // LATE CHECK
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

                    // EARLY OUT CHECK
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
                    class="bg-white rounded-xl p-3 border border-slate-100 shadow-sm flex items-start gap-3 hover:bg-slate-50 transition-colors">
                    <!-- Icon -->
                    <div class="shrink-0 mt-0.5">
                        @php
                            $iconColor = 'bg-slate-100 text-slate-600';
                            $iconName = 'help-circle-outline';

                            if ($d->status == 'h') {
                                $iconColor = 'bg-emerald-100 text-emerald-600';
                                $iconName = 'finger-print-outline';
                            } elseif ($d->status == 'i') {
                                $iconColor = 'bg-blue-100 text-blue-600';
                                $iconName = 'document-text-outline';
                            } elseif ($d->status == 's') {
                                $iconColor = 'bg-rose-100 text-rose-600';
                                $iconName = 'medkit-outline';
                            } elseif ($d->status == 'c') {
                                $iconColor = 'bg-amber-100 text-amber-600';
                                $iconName = 'calendar-outline';
                            } elseif ($d->status == 'd') {
                                $iconColor = 'bg-indigo-100 text-indigo-600';
                                $iconName = 'briefcase-outline';
                            }
                        @endphp
                        <div class="h-12 w-12 rounded-xl {{ $iconColor }} flex items-center justify-center">
                            <ion-icon name="{{ $iconName }}" class="text-2xl"></ion-icon>
                        </div>
                    </div>

                    <!-- Split Content -->
                    <div class="flex-1 flex justify-between items-start gap-2">
                        <!-- Left Column -->
                        <div class="flex flex-col gap-1 grow">
                            <h3 class="font-bold text-slate-800 text-sm leading-tight">{{ DateToIndo($d->tanggal) }}</h3>

                            @if ($d->status == 'h')
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-0.5">
                                    <!-- IN Row -->
                                    <div class="flex items-center gap-1.5 align-middle">
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
                                    </div>

                                    <!-- Separator (Visible on wider screens) -->
                                    <div class="hidden sm:block w-[1.5px] h-4 bg-slate-200 mx-1"></div>

                                    <!-- OUT Row -->
                                    <div class="flex items-center gap-1.5 align-middle">
                                        <span
                                            class="{{ $is_early_out ? 'bg-rose-50 text-rose-500 border-rose-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} px-1.5 py-0.5 rounded text-[10px] font-bold tracking-tight inline-block leading-none border">
                                            OUT: {{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '--:--' }}
                                        </span>
                                        @if ($is_early_out && $early_msg)
                                            <span
                                                class="bg-rose-50 text-rose-500 px-1.5 py-0.5 rounded text-[10px] font-bold tracking-tight inline-block leading-none border border-rose-100">
                                                {{ $early_msg }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <!-- Non-Presence Status -->
                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    @if ($d->status == 'i')
                                        <span class="bg-blue-50 text-blue-600 border border-blue-100 px-1.5 py-0.5 rounded text-[10px] font-bold inline-block leading-none">Izin Absen</span>
                                    @elseif ($d->status == 's')
                                        <span class="bg-rose-50 text-rose-600 border border-rose-100 px-1.5 py-0.5 rounded text-[10px] font-bold inline-block leading-none">Sakit</span>
                                    @elseif ($d->status == 'c')
                                        <span class="bg-amber-100 text-amber-600 border border-amber-100 px-1.5 py-0.5 rounded text-[10px] font-bold inline-block leading-none">Cuti {{ $d->nama_cuti ?? '' }}</span>
                                    @elseif ($d->status == 'd')
                                        <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 px-1.5 py-0.5 rounded text-[10px] font-bold inline-block leading-none">Dinas Luar</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Right Column -->
                        <div class="text-right flex flex-col gap-1 mt-0.5 shrink-0">
                            <h3 class="font-bold text-slate-800 text-[10px] uppercase leading-tight">{{ $d->nama_jam_kerja }}</h3>
                            <p class="text-[10px] font-medium text-teal-500 whitespace-nowrap">
                                {{ date('H:i', strtotime($d->jam_masuk)) }} -
                                {{ $d->jam_pulang ? date('H:i', strtotime($d->jam_pulang)) : '??:??' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection