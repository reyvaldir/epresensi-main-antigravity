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
            class="w-32 h-32 bg-amber-50 rounded-full flex items-center justify-center mb-6 ring-8 ring-amber-50/50 shadow-xl">
            <ion-icon name="warning-outline" class="text-6xl text-amber-500"></ion-icon>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-bold text-slate-800 mb-3 font-outfit">Mohon Maaf</h2>

        <!-- Message -->
        <p class="text-slate-500 leading-relaxed mb-8 max-w-xs">
            Anda tidak dapat melakukan presensi hari ini karena sudah melakukan pengajuan <span
                class="font-bold text-amber-600">Izin/Sakit</span> sebelumnya.
        </p>

        <div class="w-full max-w-xs space-y-3">
            <div
                class="bg-amber-50 border border-amber-100 rounded-xl p-4 text-xs text-amber-700 text-left flex items-start gap-3">
                <ion-icon name="information-circle" class="text-lg shrink-0 mt-0.5"></ion-icon>
                <span>Jika terdapat kesalahan data, silahkan hubungi HRD atau Admin terkait.</span>
            </div>

            <!-- Action Button -->
            <a href="/dashboard"
                class="w-full btn bg-primary text-white hover:bg-primary-focus h-12 rounded-xl text-base font-medium shadow-lg shadow-primary/30 flex items-center justify-center gap-2 transition-all active:scale-95">
                <ion-icon name="home-outline"></ion-icon>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection