@extends('layouts.mobile_modern')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="/pengajuanizin"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Buat Izin Cuti</h1>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-6">
        <form action="{{ route('izincuti.store') }}" method="POST" id="formIzin" autocomplete="off" class="space-y-5">
            @csrf

            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 ml-1">Dari Tanggal</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
                        </div>
                        <input type="text" id="dari" name="dari"
                            class="dari w-full pl-10 pr-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            placeholder="Pilih Tanggal">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 ml-1">Sampai Tanggal</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
                        </div>
                        <input type="text" id="sampai" name="sampai"
                            class="sampai w-full pl-10 pr-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            placeholder="Pilih Tanggal">
                    </div>
                </div>
            </div>

            <!-- Total Days (Auto Calc) -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Jumlah Hari</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <ion-icon name="time-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="text" name="jml_hari"
                        class="jml_hari w-full pl-10 pr-3 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-medium text-slate-500"
                        placeholder="Otomatis dihitung" readonly>
                </div>
            </div>

            <!-- Cuti Type -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Jenis Cuti</label>
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                        <ion-icon name="briefcase-outline" class="text-lg"></ion-icon>
                    </div>
                    <select name="kode_cuti" id="kode_cuti"
                        class="kode_cuti w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all appearance-none cursor-pointer">
                        <option value="">Pilih Jenis Cuti</option>
                        @foreach ($jenis_cuti as $d)
                            <option value="{{ $d->kode_cuti }}">{{ $d->jenis_cuti }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Keterangan</label>
                <div class="relative group">
                    <div class="absolute top-3 left-3 text-slate-400 group-focus-within:text-primary transition-colors">
                        <ion-icon name="document-text-outline" class="text-lg"></ion-icon>
                    </div>
                    <textarea name="keterangan"
                        class="keterangan w-full pl-10 pr-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none h-32"
                        placeholder="Jelaskan alasan cuti..."></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btnSimpan"
                class="w-full bg-primary text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/40 hover:bg-blue-700 active:scale-95 transition-all text-sm flex items-center justify-center gap-2">
                <ion-icon name="send-outline" class="text-lg"></ion-icon>
                <span>Ajukan Izin Cuti</span>
            </button>
        </form>
    </div>

    <!-- Logic Alert Container -->
    <div id="sisa_cuti_alert" class="mt-4"></div>

@endsection

@push('scripts')
    <!-- Rolldate JS -->
    <script src="https://cdn.jsdelivr.net/npm/rolldate@3.1.3/dist/rolldate.min.js"></script>

    <script>
        // Init Rolldate (Date Picker)
        var lang = { title: 'Pilih Tanggal', cancel: 'Batal', confirm: 'Pilih', year: '', month: '', day: '', hour: '', min: '', sec: '' };

        new Rolldate({
            el: '#dari',
            format: 'YYYY-MM-DD',
            lang: lang,
            confirm: function (date) {
                let jmlhari = hitungHari(date, $('#sampai').val());
                $('.jml_hari').val(jmlhari);
                checkSisaCuti(); // Re-check if date changes
            }
        });

        new Rolldate({
            el: '#sampai',
            format: 'YYYY-MM-DD',
            lang: lang,
            confirm: function (date) {
                let jmlhari = hitungHari($('#dari').val(), date);
                $('.jml_hari').val(jmlhari);
                checkSisaCuti(); // Re-check if date changes
            }
        });

        // Day Calculation Logic
        function hitungHari(startDate, endDate) {
            if (startDate && endDate) {
                var start = new Date(startDate);
                var end = new Date(endDate);
                var timeDifference = end - start + (1000 * 3600 * 24); // Inclusive
                var dayDifference = timeDifference / (1000 * 3600 * 24);
                return dayDifference > 0 ? dayDifference : 0;
            }
            return 0;
        }

        let sisa_cuti = 0;

        // Check Sisa Cuti via AJAX
        function checkSisaCuti() {
            let kode_cuti = $("#kode_cuti").val();
            let dari = $("#dari").val();

            if (kode_cuti == "") {
                $('#sisa_cuti_alert').html('');
                return;
            }

            $.ajax({
                type: 'GET',
                url: "{{ route('izincuti.getsisaharicuti') }}",
                data: {
                    kode_cuti: kode_cuti,
                    tanggal: dari,
                },
                success: function (response) {
                    if (response.status) {
                        sisa_cuti = response.sisa_cuti;
                        // Render Tailwind Alert
                        $('#sisa_cuti_alert').html(`
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 text-blue-600">
                                        <ion-icon name="information-circle" class="text-xl"></ion-icon>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-blue-700 text-sm mb-1">Informasi Cuti</h4>
                                        <p class="text-xs text-blue-600 leading-relaxed">${response.message}</p>
                                    </div>
                                </div>
                            `);
                    } else {
                        $('#sisa_cuti_alert').html('');
                    }
                }
            });
        }

        $("#kode_cuti").change(function () {
            checkSisaCuti();
        });

        // Form Validation & Submit
        $("#formIzin").submit(function (e) {
            let dari = $('.dari').val();
            let sampai = $('.sampai').val();
            let kode_cuti = $('.kode_cuti').val();
            let jml_hari = $('.jml_hari').val();
            let keterangan = $('.keterangan').val();

            if (dari == "" || sampai == "") {
                Swal.fire({ title: "Oops!", text: 'Periode Izin Harus Diisi !', icon: "warning", confirmButtonColor: '#3b82f6' });
                return false;
            } else if (kode_cuti == "") {
                Swal.fire({ title: "Oops!", text: 'Jenis Cuti Harus Dipilih !', icon: "warning", confirmButtonColor: '#3b82f6' });
                return false;
            } else if (jml_hari == "") {
                Swal.fire({ title: "Oops!", text: 'Jumlah Hari Harus Diisi !', icon: "warning", confirmButtonColor: '#3b82f6' });
                return false;
            } else if (sampai < dari) {
                Swal.fire({ title: "Oops!", text: 'Tanggal Akhir tidak boleh sebelum Tanggal Awal !', icon: "warning", confirmButtonColor: '#3b82f6' });
                return false;
            } else if (parseInt(jml_hari) > parseInt(sisa_cuti)) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Jumlah hari melebihi sisa cuti (' + sisa_cuti + ' hari)!',
                    icon: "warning",
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            } else if (keterangan == '') {
                Swal.fire({ title: "Oops!", text: 'Keterangan Harus Diisi !', icon: "warning", confirmButtonColor: '#3b82f6' });
                return false;
            }

            // Loading State
            let btn = $("#btnSimpan");
            btn.prop('disabled', true);
            btn.html(`<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengirim...`);
        });
    </script>
@endpush