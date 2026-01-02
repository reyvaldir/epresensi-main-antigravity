@extends('layouts.mobile_modern')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.index') }}"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Slip Gaji</h1>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
        <form action="{{ route('slipgaji.index') }}" method="GET" class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Tahun</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <ion-icon name="calendar-outline"></ion-icon>
                    </div>
                    <select name="tahun"
                        class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-700 font-medium appearance-none cursor-pointer">
                        <option value="" disabled {{ !request('tahun') ? 'selected' : '' }}>Pilih Tahun</option>
                        @for ($t = $start_year; $t <= date('Y'); $t++)
                            <option {{ request('tahun', date('Y')) == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                        @endfor
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 active:scale-95 transition-transform hover:bg-blue-700">
                <ion-icon name="search-outline" class="text-lg"></ion-icon>
                <span>Cari Data</span>
            </button>
        </form>
    </div>

    <!-- Slip Gaji List -->
    <div class="space-y-3 pb-24">
        @if (count($slipgaji) > 0)
            @foreach ($slipgaji as $d)
                <!-- Card Item -->
                <div
                    class="relative bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-all group overflow-hidden">
                    <div class="flex items-start gap-4">
                        <!-- Icon Box -->
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                        </div>

                        <!-- Content -->
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-800 text-base mb-1">
                                {{ getNamabulan($d->bulan) }} {{ $d->tahun }}
                            </h3>

                            <!-- Periode Badge -->
                            @php
                                $periode_laporan_dari = $general_setting->periode_laporan_dari;
                                $periode_laporan_sampai = $general_setting->periode_laporan_sampai;
                                $periode_laporan_lintas_bulan = $general_setting->periode_laporan_next_bulan;

                                if ($periode_laporan_lintas_bulan == 1) {
                                    if ($d->bulan == 1) {
                                        $bulan = 12;
                                        $tahun = $d->tahun - 1;
                                    } else {
                                        $bulan = $d->bulan - 1;
                                        $tahun = $d->tahun;
                                    }
                                } else {
                                    $bulan = $d->bulan;
                                    $tahun = $d->tahun;
                                }

                                $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
                                $bulan_next = str_pad($d->bulan, 2, '0', STR_PAD_LEFT);
                                $periode_dari = $tahun . '-' . $bulan . '-' . $periode_laporan_dari;
                                $periode_sampai = $tahun . '-' . $bulan_next . '-' . $periode_laporan_sampai;
                            @endphp

                            <div
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 mb-3">
                                <ion-icon name="calendar-clear-outline" class="text-slate-400 text-xs"></ion-icon>
                                <span class="text-xs font-medium text-slate-600">
                                    {{ date('d M', strtotime($periode_dari)) }} - {{ date('d M Y', strtotime($periode_sampai)) }}
                                </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <!-- Download/Print -->
                                <a href="/laporan/cetakslipgaji?bulan={{ $d->bulan }}&tahun={{ $d->tahun }}&periode_laporan=1"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 bg-primary/5 text-primary rounded-xl text-xs font-bold border border-primary/20 hover:bg-primary hover:text-white transition-all active:scale-95 group/btn">
                                    <ion-icon name="print-outline" class="text-sm group-hover/btn:text-white"></ion-icon>
                                    Cetak Slip
                                </a>

                                @can('slipgaji.edit')
                                    <a href="#"
                                        class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-500 rounded-xl border border-amber-100 hover:bg-amber-100 hover:text-amber-600 transition-colors btnEdit"
                                        kode_slip_gaji="{{ Crypt::encrypt($d->kode_slip_gaji) }}">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </a>
                                @endcan

                                @can('slipgaji.delete')
                                    <form method="POST" action="{{ route('slipgaji.delete', Crypt::encrypt($d->kode_slip_gaji)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl border border-red-100 hover:bg-red-100 hover:text-red-600 transition-colors delete-confirm"
                                            onclick="event.stopPropagation(); Swal.fire({title: 'Hapus Slip Gaji?', text: 'Data akan dihapus permanen!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', cancelButtonColor: '#64748B', confirmButtonText: 'Ya', cancelButtonText: 'Batal', customClass: { popup: 'rounded-2xl', confirmButton: 'bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-xl', cancelButton: 'bg-slate-500 hover:bg-slate-600 text-white px-5 py-2.5 rounded-xl' }, buttonsStyling: false}).then((res) => { if(res.isConfirmed) this.form.submit(); });">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div
                    class="bg-indigo-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                    <ion-icon name="document-text-outline" class="text-4xl text-indigo-400"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-lg mb-1">Belum Ada Slip Gaji</h3>
                <p class="text-slate-500 text-sm max-w-xs mx-auto">
                    Slip gaji untuk tahun {{ request('tahun', date('Y')) }} belum tersedia.
                </p>
                @can('slipgaji.create')
                    <a href="#" id="btnCreate"
                        class="inline-flex items-center gap-2 mt-4 px-6 py-2.5 bg-primary text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all active:scale-95">
                        <ion-icon name="add-outline" class="text-lg"></ion-icon>
                        Buat Slip Gaji
                    </a>
                @endcan
            </div>
        @endif
    </div>

    <!-- Create Button (FAB) for Admin -->
    @can('slipgaji.create')
        <a href="#" id="btnCreateFab"
            class="fixed bottom-24 right-6 h-14 w-14 bg-primary text-white rounded-full shadow-xl shadow-blue-500/40 flex items-center justify-center hover:scale-105 active:scale-95 transition-all z-50">
            <ion-icon name="add-outline" class="text-3xl"></ion-icon>
        </a>
    @endcan
@endsection

@push('myscript')
    <script>
        $(function () {
            // Bind actions to buttons (Add any specific JS logic here if needed)
            $('#btnCreate, #btnCreateFab').click(function (e) {
                e.preventDefault();
                // Logic to open create modal or page
                // Assuming standard modal logic or redirect, checking original file
                // Original file had <a href="#" id="btnCreate"> with no inline JS, likely mostly handled within common scripts or controller
                // If it needs to open a modal, we'd add that here. 
                // For now, retaining similar link behavior. 
                // Based on controller, create is a page return view('payroll.slipgaji.create')
                // But the button was <a href="#" id="btnCreate">. 
                // Let's check routes. Route::get('/slipgaji/create', 'create') exists.
                window.location.href = "{{ route('slipgaji.create') }}";
            });

            $('.btnEdit').click(function (e) {
                e.preventDefault();
                var kode_slip_gaji = $(this).attr('kode_slip_gaji');
                // Original code was just a link with #, likely opening a modal via global handler or missing JS in snippet
                // Route::get('/slipgaji/{kode_slip}/edit', 'edit') exists.
                // Assuming page navigation for edit based on standard pattern
                // Or if it's a modal, we need to load it. 
                // Let's assume page navigation to be safe for now or modal load.
                // Given the context of "create" being a view return, edit likely is too.
                // However, original code used class `btnEdit` which might trigger modal.
                // Checking controller Edit method... returns view('payroll.slipgaji.edit').
                // So it is a full page or a view to be loaded in modal.
                // Let's try redirect first or check adjacent scripts.
                // Actually, let's load it into a modal if that's the pattern, but usually full page edit for complex forms.
                // For Mobile, full page is often better. 
                // Let's use simple redirect for now:
                // window.location.href = '/slipgaji/' + kode_slip_gaji + '/edit';
                // Wait, kode_slip_gaji is encrypted. 
                // Better to use a standard route if possible or AJAX load.
                // Let's look at Route: /slipgaji/{kode_slip}/edit
                // So:
                window.location.href = "{{ url('/slipgaji') }}/" + kode_slip_gaji + "/edit";
            });
        });
    </script>
@endpush