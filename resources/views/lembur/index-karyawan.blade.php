@extends('layouts.mobile_modern')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.index') }}"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Data Lembur</h1>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
        <form action="{{ route('lembur.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Dari Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="calendar-outline"></ion-icon>
                        </div>
                        <input type="text" name="dari" id="datePicker" readonly
                            value="{{ Request('dari') }}"
                            class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-700 font-medium transition-all cursor-pointer"
                            placeholder="Pilih Tanggal">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 ml-1">Sampai Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="calendar-outline"></ion-icon>
                        </div>
                        <input type="text" name="sampai" id="datePicker2" readonly
                            value="{{ Request('sampai') }}"
                            class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-700 font-medium transition-all cursor-pointer"
                            placeholder="Pilih Tanggal">
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

    <div class="space-y-3 pb-24">
        @foreach ($lembur as $d)
            @php
                // Status Badge Logic
                $statusBadge = '';
                if ($d->status == 0) {
                    $statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">Pending</span>';
                } elseif ($d->status == 1) {
                    $statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">Disetujui</span>';
                } elseif ($d->status == 2) {
                    $statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200">Ditolak</span>';
                }

                // Add indo formatted date to data object
                $d->tanggal_indo = DateToIndo($d->tanggal);
            @endphp

            <div onclick="showDetailLembur(this)" data-detail="{{ json_encode($d) }}"
                class="relative bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-all group overflow-hidden cursor-pointer">
                <!-- Action Buttons: Delete (Pending) or Presence (Approved) -->
                @if ($d->status == 0)
                    <form action="{{ route('lembur.delete', Crypt::encrypt($d->id)) }}" method="POST"
                        class="absolute top-4 right-4 z-20 delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            class="btn-delete flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </form>
                @elseif ($d->status == 1)
                    <a href="{{ route('lembur.createpresensi', Crypt::encrypt($d->id)) }}"
                        class="absolute top-4 right-4 z-20 flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors shadow-sm"
                        onclick="event.stopPropagation()">
                        <ion-icon name="camera-outline"></ion-icon>
                    </a>
                @endif

                <div class="flex items-start gap-4 pr-10">
                    <!-- Icon -->
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <ion-icon name="time-outline" class="text-2xl"></ion-icon>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-slate-800 text-base truncate">Lembur</h3>
                            {!! $statusBadge !!}
                        </div>

                        <p class="text-xs text-slate-500 font-medium flex items-center gap-1 mb-1.5 leading-tight">
                            <ion-icon name="calendar-outline" class="text-slate-400"></ion-icon>
                            {{ DateToIndo($d->tanggal) }} <span class="mx-0.5">•</span>
                            {{ date('H:i', strtotime($d->lembur_mulai)) }} - {{ date('H:i', strtotime($d->lembur_selesai)) }}
                        </p>

                        @if($d->keterangan)
                            <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                                <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">"{{ $d->keterangan }}"</p>
                            </div>
                        @endif

                        @if ($d->status == 1)
                            <div class="flex items-center gap-2 mt-2">
                                @if ($d->lembur_in != null)
                                    <span class="text-[10px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded border border-emerald-100 font-bold">
                                        IN: {{ date('H:i', strtotime($d->lembur_in)) }}
                                    </span>
                                @else
                                    <span class="text-[10px] bg-rose-50 text-rose-600 px-1.5 py-0.5 rounded border border-rose-100 font-bold">
                                        Belum Absen
                                    </span>
                                @endif
                                <span class="text-[10px] text-slate-300">|</span>
                                @if ($d->lembur_out != null)
                                    <span class="text-[10px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded border border-emerald-100 font-bold">
                                        OUT: {{ date('H:i', strtotime($d->lembur_out)) }}
                                    </span>
                                @else
                                    <span class="text-[10px] bg-rose-50 text-rose-600 px-1.5 py-0.5 rounded border border-rose-100 font-bold">
                                        Belum Absen
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Empty State -->
        @if ($lembur->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-300 mb-4">
                    <ion-icon name="document-text-outline" class="text-4xl"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Tidak Ada Data</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-[200px] mx-auto">Belum ada riwayat lembur yang ditemukan untuk
                    periode ini.</p>
            </div>
        @endif
    </div>

    <!-- FAB Add Button -->
    <a href="{{ route('lembur.create') }}"
        class="fixed bottom-24 right-6 h-14 w-14 bg-primary text-white rounded-full shadow-xl shadow-blue-500/40 flex items-center justify-center hover:scale-105 active:scale-95 transition-all z-50">
        <ion-icon name="add-outline" class="text-3xl"></ion-icon>
    </a>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/rolldate@3.1.3/dist/rolldate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/rolldate@3.1.3/dist/rolldate.min.js"></script>
@endpush

@push('myscript')
    <script>
        // Initialize Rolldate for date filters
        var lang = { title: 'Pilih Tanggal', cancel: 'Batal', confirm: 'Set', year: '', month: '', day: '' };
        new Rolldate({ el: '#datePicker', format: 'YYYY-MM-DD', beginYear: 2020, endYear: 2100, lang: lang });
        new Rolldate({ el: '#datePicker2', format: 'YYYY-MM-DD', beginYear: 2020, endYear: 2100, lang: lang });

        $('.btn-delete').click(function (e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent opening modal when clicking delete
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                text: "Data lembur yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-xl font-bold border-0 outline-none focus:outline-none ring-0 shadow-lg shadow-rose-200',
                    cancelButton: 'bg-slate-500 hover:bg-slate-600 text-white px-5 py-2.5 rounded-xl font-bold border-0 outline-none focus:outline-none ring-0'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });

        function showDetailLembur(element) {
            let data = JSON.parse($(element).attr('data-detail'));
            let statusBadge, statusColor, iconName;

            // Logic status styling
            if (data.status == 0) {
                statusBadge = 'Pending';
                statusColor = 'blue';
                iconName = 'time-outline';
            } else if (data.status == 1) {
                statusBadge = 'Disetujui';
                statusColor = 'emerald';
                iconName = 'checkmark-circle-outline';
            } else if (data.status == 2) {
                statusBadge = 'Ditolak';
                statusColor = 'rose';
                iconName = 'close-circle-outline';
            }

            // Bukti Lembur Logic (Only if Approved and Present)
            let buktiHtml = '';
            if (data.status == 1) {
                let fotoIn = data.foto_lembur_in ? `<img src="/storage/uploads/lembur/${data.foto_lembur_in}" class="w-full h-32 object-cover rounded-lg border border-slate-200">` : '<div class="h-32 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 text-xs border border-dashed border-slate-300">Belum Absen Masuk</div>';
                let fotoOut = data.foto_lembur_out ? `<img src="/storage/uploads/lembur/${data.foto_lembur_out}" class="w-full h-32 object-cover rounded-lg border border-slate-200">` : '<div class="h-32 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 text-xs border border-dashed border-slate-300">Belum Absen Pulang</div>';

                buktiHtml = `
                                                        <div class="grid grid-cols-2 gap-3 mt-4 text-left">
                                                            <div>
                                                                <span class="text-[10px] font-bold text-slate-500 block mb-1 uppercase tracking-wide">Absen Masuk</span>
                                                                ${fotoIn}
                                                                <div class="mt-1 text-xs font-medium text-slate-600">
                                                                    <ion-icon name="time-outline" class="align-middle"></ion-icon> ${data.lembur_in ? data.lembur_in.substring(0, 5) : '--:--'}
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <span class="text-[10px] font-bold text-slate-500 block mb-1 uppercase tracking-wide">Absen Pulang</span>
                                                                ${fotoOut}
                                                                <div class="mt-1 text-xs font-medium text-slate-600">
                                                                    <ion-icon name="time-outline" class="align-middle"></ion-icon> ${data.lembur_out ? data.lembur_out.substring(0, 5) : '--:--'}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;
            }

            // Safe time parsing (Handle "Y-m-d H:i:s" or "H:i:s")
            let getJam = (str) => {
                if (!str) return '--:--';
                if (str.includes(' ')) return str.split(' ')[1].substring(0, 5);
                return str.substring(0, 5);
            };

            let lemburMulai = getJam(data.lembur_mulai);
            let lemburSelesai = getJam(data.lembur_selesai);

            // Calculate duration safely (Format: 1j 30m)
            let durasi = '0j 0m';
            if (data.lembur_mulai && data.lembur_selesai) {
                let start, end;
                if (data.lembur_mulai.includes(' ') && data.lembur_selesai.includes(' ')) {
                    start = new Date(data.lembur_mulai);
                    end = new Date(data.lembur_selesai);
                } else {
                    start = new Date("2000-01-01 " + data.lembur_mulai);
                    end = new Date("2000-01-01 " + data.lembur_selesai);
                }

                let diffMs = end - start;
                if (!isNaN(diffMs) && diffMs > 0) {
                    let diffMins = Math.floor(diffMs / 1000 / 60);
                    let hours = Math.floor(diffMins / 60);
                    let mins = diffMins % 60;
                    durasi = `${hours}j ${mins}m`;
                }
            }

            // Status Badge Logic
            let approvalBadge = '';
            if (data.status == '0') {
                approvalBadge = '<span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">Pending</span>';
            } else if (data.status == '1') {
                approvalBadge = '<span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">Disetujui</span>';
            } else if (data.status == '2') {
                approvalBadge = '<span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200">Ditolak</span>';
            }

            // Status Styling Override
            let bgClass = `bg-${statusColor}-50`;
            let borderClass = `border-${statusColor}-100`;

            Swal.fire({
                showCloseButton: false,
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                buttonsStyling: false,
                html: `
                                                <div class="text-center">
                                                    <div class="${bgClass} p-4 rounded-xl border ${borderClass} mb-4">
                                                         <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-${statusColor}-100 text-${statusColor}-600 mb-2">
                                                            <ion-icon name="${iconName}" class="text-2xl"></ion-icon>
                                                        </div>
                                                        <h3 class="text-lg font-bold text-${statusColor}-700 leading-tight">Lembur</h3>
                                                        <div class="mt-2 mb-1">
                                                            ${approvalBadge}
                                                        </div>
                                                        <p class="text-sm text-slate-500 mt-2 font-medium bg-white/60 py-1 rounded-lg inline-block px-3">
                                                            ${data.tanggal_indo}
                                                        </p>
                                                    </div>

                                                    <div class="text-left space-y-3">
                                                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center justify-between">
                                                                    <div>
                                                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jam Lembur</span>
                                                                        <span class="text-sm font-bold text-slate-700 font-mono">
                                                                            ${lemburMulai} - ${lemburSelesai}
                                                                        </span>
                                                                    </div>
                                                                    <div class="text-right">
                                                                         <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total</span>
                                                                         <span class="text-sm font-bold text-blue-600">
                                                                            ${durasi}
                                                                         </span>
                                                                    </div>
                                                                </div>

                                                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Keterangan Tugas</span>
                                                                    <p class="text-slate-700 text-sm font-medium leading-relaxed">
                                                                        "${data.keterangan}"
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            ${buktiHtml}
                                                        </div>
                                                    `,
                customClass: {
                    popup: 'rounded-2xl shadow-xl w-[90%] md:w-full md:max-w-md p-0 overflow-hidden',
                    htmlContainer: '!m-0 !px-4 !pt-4 !pb-0',
                    confirmButton: 'w-full bg-slate-100 text-slate-600 font-bold py-3.5 rounded-b-2xl border-t border-slate-100 hover:bg-slate-200 transition-all active:scale-[0.98]'
                }
            });
        }

        // Helper not needed inside anymore, but keeping if used elsewhere, 
        // essentially the logic above replaces the need for calculateDuration dependent on format
        function calculateDuration(start, end) {
            // simplified legacy support if needed
            return '0';
        }
    </script>
@endpush