@extends('layouts.mobile_modern')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 mt-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('lembur.index') }}"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Ajukan Lembur</h1>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <form action="{{ route('lembur.store') }}" method="POST" id="formLembur" autocomplete="off" class="space-y-4">
            @csrf

            <!-- Hidden Inputs for Submission -->
            <input type="hidden" name="dari" id="dari">
            <input type="hidden" name="sampai" id="sampai">

            <!-- Dari Section -->
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2 ml-1">Dari Tanggal & Jam</label>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Date Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
                        </div>
                        <input type="text" id="dari_tgl" readonly
                            class="w-full pl-10 pr-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-800 font-medium transition-all cursor-pointer placeholder:text-slate-400"
                            placeholder="Tanggal">
                    </div>
                    <!-- Time Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="time-outline" class="text-lg"></ion-icon>
                        </div>
                        <input type="text" id="dari_jam" readonly
                            class="w-full pl-10 pr-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-800 font-medium transition-all cursor-pointer placeholder:text-slate-400"
                            placeholder="Jam">
                    </div>
                </div>
            </div>

            <!-- Sampai Section -->
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2 ml-1">Sampai Tanggal & Jam</label>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Date Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
                        </div>
                        <input type="text" id="sampai_tgl" readonly
                            class="w-full pl-10 pr-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-800 font-medium transition-all cursor-pointer placeholder:text-slate-400"
                            placeholder="Tanggal">
                    </div>
                    <!-- Time Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="time-outline" class="text-lg"></ion-icon>
                        </div>
                        <input type="text" id="sampai_jam" readonly
                            class="w-full pl-10 pr-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-800 font-medium transition-all cursor-pointer placeholder:text-slate-400"
                            placeholder="Jam">
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-1.5 ml-1">Keterangan / Tugas</label>
                <div class="relative">
                    <textarea name="keterangan" id="keterangan" rows="4"
                        class="keterangan w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-800 font-medium transition-all placeholder:text-slate-400 leading-relaxed"
                        placeholder="Contoh: Menyelesaikan laporan bulanan..."></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btnSimpan"
                class="w-full bg-primary text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 active:scale-95 transition-transform hover:bg-blue-700 mt-4">
                <ion-icon name="paper-plane-outline" class="text-xl"></ion-icon>
                <span>Kirim Pengajuan</span>
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/rolldate@3.1.3/dist/rolldate.min.js"></script>
@endpush

@push('myscript')
    <script>
        // Initialize Rolldate for Dates
        new Rolldate({
            el: '#dari_tgl',
            format: 'YYYY-MM-DD',
            lang: {
                title: 'Pilih Tanggal',
                cancel: 'Batal',
                confirm: 'Set',
                year: '',
                month: '',
                day: ''
            }
        });
        new Rolldate({
            el: '#sampai_tgl',
            format: 'YYYY-MM-DD',
            lang: {
                title: 'Pilih Tanggal',
                cancel: 'Batal',
                confirm: 'Set',
                year: '',
                month: '',
                day: ''
            }
        });

        // Initialize Rolldate for Times
        new Rolldate({
            el: '#dari_jam',
            format: 'hh:mm',
            lang: {
                title: 'Pilih Jam',
                cancel: 'Batal',
                confirm: 'Set',
                hour: '',
                min: ''
            }
        });
        new Rolldate({
            el: '#sampai_jam',
            format: 'hh:mm',
            lang: {
                title: 'Pilih Jam',
                cancel: 'Batal',
                confirm: 'Set',
                hour: '',
                min: ''
            }
        });

        $("#formLembur").submit(function (e) {
            let dari_tgl = $('#dari_tgl').val();
            let dari_jam = $('#dari_jam').val();
            let sampai_tgl = $('#sampai_tgl').val();
            let sampai_jam = $('#sampai_jam').val();
            let keterangan = $('.keterangan').val();

            // Combine Date and Time
            let dari = dari_tgl + ' ' + dari_jam;
            let sampai = sampai_tgl + ' ' + sampai_jam;

            // Set hidden inputs
            $('#dari').val(dari);
            $('#sampai').val(sampai);

            if (sampai < dari) {
                Swal.fire({
                    title: "Waktu Tidak Valid",
                    text: 'Waktu Selesai tidak boleh LEBIH KECIL dari Waktu Mulai',
                    icon: "warning",
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold'
                    },
                    buttonsStyling: false
                });
                return false;
            }

            if (dari_tgl == "" || dari_jam == "" || sampai_tgl == "" || sampai_jam == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Tanggal dan Jam Mulai/Selesai Harus Diisi Lengkap!',
                    icon: "warning",
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold'
                    },
                    buttonsStyling: false
                });
                return false;
            } else if (keterangan == '') {
                Swal.fire({
                    title: "Keterangan Kosong",
                    text: 'Mohon isi keterangan tugas lembur Anda!',
                    icon: "warning",
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold'
                    },
                    buttonsStyling: false
                });
                return false;
            }

            // Disable button
            $("#btnSimpan").prop('disabled', true);
            $("#btnSimpan").html(`
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengirim...
                        `);
        });
    </script>
@endpush