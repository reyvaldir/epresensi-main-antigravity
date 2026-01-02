@extends('layouts.mobile_modern')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.index') }}"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Aktivitas Saya</h1>
        </div>
        <a href="{{ route('aktivitaskaryawan.export.pdf', request()->query()) }}"
            class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors"
            target="_blank">
            <ion-icon name="document-text-outline" class="text-xl"></ion-icon>
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
        <form action="{{ route('aktivitaskaryawan.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Dari Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="calendar-outline"></ion-icon>
                        </div>
                        <input type="text" name="tanggal_awal" id="datePicker" value="{{ Request('tanggal_awal') }}"
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-700 transition-all font-medium cursor-pointer"
                            placeholder="Pilih Tanggal" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 ml-1">Sampai Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="calendar-outline"></ion-icon>
                        </div>
                        <input type="text" name="tanggal_akhir" id="datePicker2" value="{{ Request('tanggal_akhir') }}"
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-slate-700 transition-all font-medium cursor-pointer"
                            placeholder="Pilih Tanggal" readonly>
                    </div>
                </div>
            </div>
            <button type="submit"
                class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 active:scale-95 transition-transform hover:bg-blue-700">
                <ion-icon name="search-outline" class="text-lg"></ion-icon>
                <span>Tampilkan Data</span>
            </button>
        </form>
    </div>

    <!-- Activity List -->
    <div class="space-y-3 pb-24">
        @foreach ($aktivitas as $d)
            @php
                $d->tanggal_indo = DateToIndo($d->created_at->format('Y-m-d'));
                $jam = $d->created_at->format('H:i');
            @endphp

            <div onclick="showDetailActivity(this)" data-detail="{{ json_encode($d) }}" data-date="{{ $d->tanggal_indo }}"
                data-time="{{ $jam }}"
                class="relative bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:shadow-md transition-all group overflow-hidden cursor-pointer">

                <!-- Action: Delete -->
                <form action="{{ route('aktivitaskaryawan.destroy', $d->id) }}" method="POST"
                    class="absolute top-4 right-4 z-20 delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        class="btn-delete flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-100 transition-colors"
                        onclick="event.stopPropagation(); Swal.fire({title: 'Hapus Aktivitas?', text: 'Data akan dihapus permanen!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', cancelButtonColor: '#64748B', confirmButtonText: 'Ya', cancelButtonText: 'Batal', customClass: { popup: 'rounded-2xl', confirmButton: 'bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-xl', cancelButton: 'bg-slate-500 hover:bg-slate-600 text-white px-5 py-2.5 rounded-xl' }, buttonsStyling: false}).then((res) => { if(res.isConfirmed) this.form.submit(); });">
                        <ion-icon name="trash-outline"></ion-icon>
                    </button>
                </form>

                <div class="flex items-start gap-4 pr-10">
                    <!-- Icon -->
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <ion-icon name="briefcase-outline" class="text-2xl"></ion-icon>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-slate-800 text-base truncate">{{ $d->tanggal_indo }}</h3>
                            <span
                                class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold border border-slate-200">
                                {{ $jam }}
                            </span>
                        </div>

                        <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100 mb-1.5">
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">"{{ $d->aktivitas }}"</p>
                        </div>

                        @if ($d->lokasi)
                            <div class="flex items-center gap-1">
                                <ion-icon name="location" class="text-rose-500 text-xs"></ion-icon>
                                <span class="text-[10px] text-slate-500 truncate max-w-[200px]">{{ $d->lokasi }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Empty State -->
        @if ($aktivitas->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-300 mb-4">
                    <ion-icon name="document-text-outline" class="text-4xl"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Belum Ada Aktivitas</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-[200px] mx-auto">Aktivitas harian yang Anda input akan muncul di
                    sini.</p>
            </div>
        @endif

        <!-- Pagination -->
        @if ($aktivitas->hasPages())
            <div class="mt-4">
                {{ $aktivitas->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>

    <!-- FAB Add Button -->
    <a href="{{ route('aktivitaskaryawan.create') }}"
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
        $(function () {
            // Datepickers
            var lang = { title: 'Pilih Tanggal', cancel: 'Batal', confirm: 'Set', year: '', month: '', day: '' };
            new Rolldate({ el: '#datePicker', format: 'YYYY-MM-DD', beginYear: 2020, endYear: 2100, lang: lang });
            new Rolldate({ el: '#datePicker2', format: 'YYYY-MM-DD', beginYear: 2020, endYear: 2100, lang: lang });
        });

        function showDetailActivity(element) {
            let data = JSON.parse($(element).attr('data-detail'));
            let date = $(element).attr('data-date');
            let time = $(element).attr('data-time');

            // Image Logic
            let imageHtml = '';
            if (data.foto) {
                imageHtml = `
                                <div class="relative w-full aspect-video rounded-xl overflow-hidden border border-slate-200 mb-4 shadow-sm group">
                                    <img src="/storage/uploads/aktivitas/${data.foto}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-white" onclick="Swal.fire({imageUrl: '/storage/uploads/aktivitas/${data.foto}', showCloseButton:true, showConfirmButton:false})">
                                        <ion-icon name="expand-outline" class="text-3xl"></ion-icon>
                                    </div>
                                </div>
                             `;
            } else {
                imageHtml = `
                                <div class="w-full aspect-[3/1] bg-slate-50 rounded-xl flex items-center justify-center border border-dashed border-slate-200 mb-4">
                                    <span class="text-xs text-slate-400 flex items-center gap-1">
                                        <ion-icon name="image-outline"></ion-icon> Tidak ada foto
                                    </span>
                                </div>
                             `;
            }

            Swal.fire({
                showCloseButton: false,
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                buttonsStyling: false,
                html: `
                                <div class="text-center">
                                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-4">
                                         <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 mb-2">
                                            <ion-icon name="briefcase-outline" class="text-2xl"></ion-icon>
                                        </div>
                                        <h3 class="text-lg font-bold text-blue-700 leading-tight">Detail Aktivitas</h3>
                                        <p class="text-sm text-slate-500 mt-2 font-medium bg-white/60 py-1 rounded-lg inline-block px-3">
                                            ${date} • ${time}
                                        </p>
                                    </div>

                                    ${imageHtml}

                                    <div class="text-left space-y-3">
                                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Deskripsi</span>
                                            <p class="text-slate-700 text-sm font-medium leading-relaxed">
                                                "${data.aktivitas}"
                                            </p>
                                        </div>

                                        ${data.lokasi ? `
                                        <div class="flex items-start gap-2 text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                            <ion-icon name="location-sharp" class="text-rose-500 mt-0.5 text-sm"></ion-icon>
                                            <span class="leading-tight font-medium">${data.lokasi}</span>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `,
                customClass: {
                    popup: 'rounded-2xl shadow-xl w-[90%] md:w-full md:max-w-md p-0 overflow-hidden',
                    htmlContainer: '!m-0 !px-4 !pt-4 !pb-0',
                    confirmButton: 'w-full bg-slate-100 text-slate-600 font-bold py-3.5 rounded-b-2xl border-t border-slate-100 hover:bg-slate-200 transition-all active:scale-[0.98]'
                }
            });
        }
    </script>
@endpush