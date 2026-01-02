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
            class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mb-6 ring-8 ring-slate-50 shadow-xl">
            <ion-icon name="calendar-clear-outline" class="text-6xl text-slate-400"></ion-icon>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-bold text-slate-800 mb-3 font-outfit">Tidak Ada Jadwal</h2>

        <!-- Message -->
        <p class="text-slate-500 leading-relaxed mb-8 max-w-xs">
            Hari ini Anda tidak memiliki jadwal kerja (Off).
            <br>
            <span class="text-xs text-slate-400 mt-2 block">Jika ini kesalahan, silakan hubungi HRD.</span>
        </p>

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