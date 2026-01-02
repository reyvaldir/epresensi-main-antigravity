@extends('layouts.mobile_modern')

@section('header')
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Pemberitahuan</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[80vh] px-6 text-center">
        <!-- Icon Illustration -->
        <div
            class="w-32 h-32 bg-emerald-50 rounded-full flex items-center justify-center mb-6 ring-8 ring-emerald-50/50 shadow-xl">
            <ion-icon name="cafe-outline" class="text-6xl text-emerald-500"></ion-icon>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-bold text-slate-800 mb-3 font-outfit">Hari Libur</h2>

        <!-- Message -->
        <p class="text-slate-500 leading-relaxed mb-6 max-w-xs">
            Hari ini Anda tidak perlu melakukan presensi karena sedang libur.
        </p>

        <!-- Holiday Detail -->
        <div
            class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-8 w-full max-w-xs flex items-center justify-center text-center">
            <span class="text-emerald-700 font-medium">
                {{ $harilibur->keterangan }}
            </span>
        </div>

        <div class="w-full max-w-xs space-y-3">
            <!-- Action Button -->
            <a href="/dashboard"
                class="w-full btn bg-primary text-white hover:bg-primary-focus h-12 rounded-xl text-base font-medium shadow-lg shadow-primary/30 flex items-center justify-center gap-2 transition-all active:scale-95">
                <ion-icon name="home-outline"></ion-icon>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection