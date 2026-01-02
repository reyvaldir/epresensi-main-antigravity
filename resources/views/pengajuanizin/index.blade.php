@extends('layouts.mobile_modern')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.index') }}"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Data Izin / Sakit</h1>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
        <form action="{{ route('pengajuanizin.index') }}" method="GET" class="space-y-3">
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

    <!-- Content -->
    <div class="space-y-3 pb-24">
        @if (count($pengajuan_izin) > 0)
            @foreach ($pengajuan_izin as $d)
                @php
                    if ($d->ket == 'i') {
                        $route = 'izinabsen.delete';
                        $icon = 'document-text-outline';
                        $color = 'text-blue-600 bg-blue-100';
                        $label = 'Izin Absen';
                    } elseif ($d->ket == 's') {
                        $route = 'izinsakit.delete';
                        $icon = 'medkit-outline';
                        $color = 'text-rose-600 bg-rose-100';
                        $label = 'Izin Sakit';
                    } elseif ($d->ket == 'c') {
                        $route = 'izincuti.delete';
                        $icon = 'calendar-outline';
                        $color = 'text-amber-600 bg-amber-100';
                        $label = 'Izin Cuti';
                    } elseif ($d->ket == 'd') {
                        $route = 'izindinas.delete';
                        $icon = 'briefcase-outline';
                        $color = 'text-purple-600 bg-purple-100';
                        $label = 'Izin Dinas';
                    }

                    $statusBadge = '';
                    if ($d->status_izin == '0') {
                        $statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">Pending</span>';
                    } elseif ($d->status_izin == '1') {
                        $statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">Disetujui</span>';
                    } elseif ($d->status_izin == '2') {
                        $statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold border border-red-200">Ditolak</span>';
                    }
                @endphp

                <div onclick="showDetail(this)" data-detail="{{ json_encode($d) }}"
                    data-date="{{ DateToIndo($d->dari) }} s/d {{ DateToIndo($d->sampai) }}"
                    class="relative bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-all group overflow-hidden cursor-pointer">
                    <!-- Swipe Actions/Delete Button -->
                    <form method="POST" action="{{ route($route, Crypt::encrypt($d->kode)) }}" class="absolute top-4 right-4 z-20">
                        @csrf
                        @method('DELETE')
                        @if ($d->status_izin == 0)
                            <button type="submit"
                                class="delete-btn flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        @endif
                    </form>

                    <div class="flex items-start gap-4 pr-10">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $color }} flex items-center justify-center">
                            <ion-icon name="{{ $icon }}" class="text-2xl"></ion-icon>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-slate-800 text-base truncate">{{ $label }}</h3>
                                {!! $statusBadge !!}
                            </div>

                            <p class="text-xs text-slate-500 font-medium flex items-center gap-1 mb-1.5">
                                <ion-icon name="calendar-outline" class="text-slate-400"></ion-icon>
                                {{ DateToIndo($d->dari) }} s/d {{ DateToIndo($d->sampai) }}
                            </p>

                            @if($d->keterangan)
                                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">"{{ $d->keterangan }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-12">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-300 mb-4">
                    <ion-icon name="document-text-outline" class="text-4xl"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Tidak Ada Data</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-[200px] mx-auto">Belum ada pengajuan izin yang ditemukan untuk
                    periode ini.</p>
            </div>
        @endif
    </div>

    <!-- Floating Action Button -->
    <div x-data="{ open: false }" class="fixed bottom-24 right-6 z-50">
        <!-- Main Button -->
        <button @click="open = !open"
            class="w-14 h-14 bg-primary text-white rounded-full shadow-xl shadow-blue-500/40 flex items-center justify-center transform transition-transform duration-200 hover:scale-105 active:scale-95"
            :class="{ 'rotate-45': open }">
            <ion-icon name="add-outline" class="text-3xl font-bold"></ion-icon>
        </button>

        <!-- Menu Items -->
        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-90"
            class="absolute bottom-16 right-0 space-y-3 min-w-[160px] pb-2">

            <a href="{{ route('izindinas.create') }}" class="flex items-center justify-end gap-3 group">
                <span
                    class="bg-primary text-white px-3 py-1.5 rounded-lg shadow-sm text-xs font-medium border border-blue-500 group-hover:bg-blue-600 transition-colors">Izin
                    Dinas</span>
                <div
                    class="w-10 h-10 bg-emerald-500 text-white rounded-full shadow-lg flex items-center justify-center transform group-hover:scale-105 transition-all">
                    <ion-icon name="airplane-outline" class="text-lg"></ion-icon>
                </div>
            </a>

            <a href="{{ route('izincuti.create') }}" class="flex items-center justify-end gap-3 group">
                <span
                    class="bg-primary text-white px-3 py-1.5 rounded-lg shadow-sm text-xs font-medium border border-blue-500 group-hover:bg-blue-600 transition-colors">Izin
                    Cuti</span>
                <div
                    class="w-10 h-10 bg-amber-500 text-white rounded-full shadow-lg flex items-center justify-center transform group-hover:scale-105 transition-all">
                    <ion-icon name="briefcase-outline" class="text-lg"></ion-icon>
                </div>
            </a>

            <a href="{{ route('izinsakit.create') }}" class="flex items-center justify-end gap-3 group">
                <span
                    class="bg-primary text-white px-3 py-1.5 rounded-lg shadow-sm text-xs font-medium border border-blue-500 group-hover:bg-blue-600 transition-colors">Izin
                    Sakit</span>
                <div
                    class="w-10 h-10 bg-pink-500 text-white rounded-full shadow-lg flex items-center justify-center transform group-hover:scale-105 transition-all">
                    <ion-icon name="medkit-outline" class="text-lg"></ion-icon>
                </div>
            </a>

            <a href="{{ route('izinabsen.create') }}" class="flex items-center justify-end gap-3 group">
                <span
                    class="bg-primary text-white px-3 py-1.5 rounded-lg shadow-sm text-xs font-medium border border-blue-500 group-hover:bg-blue-600 transition-colors">Izin
                    Absen</span>
                <div
                    class="w-10 h-10 bg-blue-500 text-white rounded-full shadow-lg flex items-center justify-center transform group-hover:scale-105 transition-all">
                    <ion-icon name="document-text-outline" class="text-lg"></ion-icon>
                </div>
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        .swal2-confirm-custom {
            background-color: #5b50e3 !important;
            border-color: #5b50e3 !important;
            box-shadow: 0 4px 6px -1px rgba(91, 80, 227, 0.2), 0 2px 4px -1px rgba(91, 80, 227, 0.1);
        }

        .swal2-confirm-custom:hover {
            background-color: #4a41c5 !important;
            border-color: #4a41c5 !important;
            transform: scale(1.02);
            box-shadow: 0 10px 15px -3px rgba(91, 80, 227, 0.3), 0 4px 6px -2px rgba(91, 80, 227, 0.1);
        }

        .swal2-cancel-custom {
            background-color: #ef4444 !important;
            /* Red-500 */
            border-color: #ef4444 !important;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2), 0 2px 4px -1px rgba(239, 68, 68, 0.1);
        }

        .swal2-cancel-custom:hover {
            background-color: #dc2626 !important;
            /* Red-600 */
            border-color: #dc2626 !important;
            transform: scale(1.02);
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3), 0 4px 6px -2px rgba(239, 68, 68, 0.1);
        }
    </style>
    <script>
        // Handle Delete Confirmation
        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-btn')) {
                e.preventDefault();
                const form = e.target.closest('form');

                Swal.fire({
                    title: 'Apakah Anda Yakin Ingin Membatalkan Data Ini ?',
                    text: "Data ini akan dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Batalkan Saja Saja!',
                    cancelButtonText: 'Cancel',
                    heightAuto: false, // Prevent layout shift
                    scrollbarPadding: false, // Prevent width jump
                    customClass: {
                        confirmButton: 'swal2-confirm-custom',
                        cancelButton: 'swal2-cancel-custom'
                    },
                    buttonsStyling: true // Enable default styling base, but we override colors
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            }
        });

        function showDetail(element) {
            const data = JSON.parse(element.getAttribute('data-detail'));
            const dateRangeTitle = element.getAttribute('data-date');

            let statusLabel = '';
            let statusColor = '';
            let iconName = '';
            let extraHtml = '';
            let daysCount = 0;

            // Helper to count days
            function countDays(start, end) {
                const date1 = new Date(start);
                const date2 = new Date(end);
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                return diffDays;
            }

            daysCount = countDays(data.dari, data.sampai);

            if (data.ket === 'i') {
                statusLabel = 'Izin Absen';
                statusColor = 'blue';
                iconName = 'document-text-outline';
            } else if (data.ket === 's') {
                statusLabel = 'Izin Sakit';
                statusColor = 'rose';
                iconName = 'medkit-outline';
                if (data.doc_sid) {
                    extraHtml = `
                                                                <div class="mt-3 text-left">
                                                                    <span class="text-xs font-bold text-slate-500 block mb-1">Surat Dokter (SID)</span>
                                                                    <div onclick="event.stopPropagation(); Swal.fire({imageUrl: '/storage/uploads/sid/${data.doc_sid}', showCloseButton:true, showConfirmButton:false})" class="cursor-pointer relative group overflow-hidden rounded-lg border border-slate-200">
                                                                        <img src="/storage/uploads/sid/${data.doc_sid}" class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                                                                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                                            <ion-icon name="eye-outline" class="text-white text-2xl"></ion-icon>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                             `;
                }
            } else if (data.ket === 'c') {
                statusLabel = `Cuti: ${data.nama_cuti || 'Tahunan'}`;
                statusColor = 'amber';
                iconName = 'calendar-outline';
            } else if (data.ket === 'd') {
                statusLabel = 'Dinas Luar';
                statusColor = 'indigo';
                iconName = 'briefcase-outline';
            }

            // Status Badge Logic
            let approvalBadge = '';
            if (data.status_izin == '0') {
                approvalBadge = '<span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">Pending</span>';
            } else if (data.status_izin == '1') {
                approvalBadge = '<span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">Disetujui</span>';
            } else if (data.status_izin == '2') {
                approvalBadge = '<span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold border border-red-200">Ditolak</span>';
            }

            // Default Styles
            let bgClass = `bg-${statusColor}-50`;
            let borderClass = `border-${statusColor}-100`;

            // Specific override for Cuti to make yellow more visible (75/100 scale)
            if (data.ket === 'c') {
                bgClass = 'bg-amber-100'; // Make it slightly darker/visible
                borderClass = 'border-amber-200';
            }

            const contentHtml = `
                                                        <div class="bg-white text-center">
                                                             <div class="${bgClass} p-4 rounded-xl border ${borderClass}">
                                                                 <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-${statusColor}-100 text-${statusColor}-600 mb-2">
                                                                    <ion-icon name="${iconName}" class="text-2xl"></ion-icon>
                                                                </div>
                                                                <h3 class="text-lg font-bold text-${statusColor}-700 leading-tight">${statusLabel}</h3>
                                                                <div class="mt-2 mb-1">
                                                                    ${approvalBadge}
                                                                </div>
                                                             </div>

                                                             <div class="mt-4 text-left bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                                <div class="flex items-center gap-3 mb-2">
                                                                     <span class="bg-white px-3 py-1 rounded-lg border border-slate-200 font-medium text-xs text-slate-600 shrink-0 whitespace-nowrap">
                                                                        <ion-icon name="calendar-outline" class="align-middle mb-0.5"></ion-icon> ${daysCount} Hari
                                                                    </span>
                                                                    <span class="text-xs text-slate-500 font-medium">${dateRangeTitle}</span>
                                                                </div>
                                                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Keterangan</span>
                                                                <p class="text-slate-700 text-sm mt-0.5 font-medium leading-relaxed">
                                                                    "${data.keterangan || '-'}"
                                                                </p>
                                                             </div>

                                                             ${extraHtml}
                                                        </div>
                                                    `;

            Swal.fire({
                heightAuto: false,
                scrollbarPadding: false,
                html: contentHtml,
                showCloseButton: false,
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-2xl shadow-xl w-[90%] md:w-full md:max-w-3xl p-0 overflow-hidden',
                    htmlContainer: '!m-0 !px-4 !pt-4 !pb-0',
                    confirmButton: 'w-full bg-slate-100 text-slate-600 font-bold py-3.5 rounded-b-2xl border-t border-slate-100 hover:bg-slate-200 transition-all active:scale-[0.98]'
                }
            });
        }
    </script>
@endpush