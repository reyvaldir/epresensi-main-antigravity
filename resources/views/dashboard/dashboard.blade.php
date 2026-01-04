@extends('layouts.app')
@section('titlepage', 'Dashboard')

@section('content')
    @section('navigasi')
        <span>Dashboard</span>
    @endsection
    <div class="row mt-3">
        <div class="col">
            <form action="">
                <div class="row">
                    <div class="col">
                        <x-input-with-icon label="Tanggal" icon="ti ti-calendar" name="tanggal" datepicker="flatpickr-date"
                            value="{{ Request('tanggal') }}" />
                    </div>
                    <div class="col">
                        <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                            selected="{{ Request('kode_cabang') }}" />
                    </div>
                    <div class="col">
                        <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept"
                            textShow="nama_dept" selected="{{ Request('kode_dept') }}" upperCase="true" />
                    </div>
                    <div class="col-1">
                        <button class="btn btn-primary"><i class="ti ti-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row mt-3">

        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i class="ti ti-user-check"></i></span>
                        </div>
                        {{-- {{ var_dump($rekappresensi->hadir) }} --}}
                        <h4 class="mb-0">{{ $rekappresensi->hadir ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Karyawan Hadir</p>
                    {{-- <p class="mb-0">
                        <span class="text-heading fw-medium me-2">+18.2%</span>
                    </p> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="ti ti-file-description"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $rekappresensi->izin ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Karyawan Izin</p>
                    {{-- <p class="mb-0">
                        <span class="text-heading fw-medium me-2">+18.2%</span>
                    </p> --}}
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i class="ti ti-first-aid-kit"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $rekappresensi->sakit ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Karyawan Sakit</p>
                    {{-- <p class="mb-0">
                        <span class="text-heading fw-medium me-2">+18.2%</span>
                    </p> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger"><i class="ti ti-calendar-event"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $rekappresensi->cuti ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Karyawan Cuti</p>
                    {{-- <p class="mb-0">
                        <span class="text-heading fw-medium me-2">+18.2%</span>
                    </p> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-12 col-sm-12 col-xs-12">
            <div class="card mb-6">
                <div class="card-widget-separator-wrapper">
                    <div class="card-body card-widget-separator">
                        <div class="row gy-4 gy-sm-1">
                            <div class="col-sm-6 col-lg-3">
                                <div
                                    class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">

                                    <div>
                                        <p class="mb-1">Data Karyawan Aktif</p>
                                        <h4 class="mb-1">{{ $status_karyawan->jml_aktif }}</h4>
                                    </div>
                                    <img src="{{ asset('assets/img/illustrations/karyawan_aktif.png') }}" height="70"
                                        alt="view sales" class="me-3">
                                </div>

                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div
                                    class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
                                    <div>
                                        <p class="mb-1">Karyawan Tetap</p>
                                        <h4 class="mb-1">{{ $status_karyawan->jml_tetap }}</h4>
                                    </div>
                                    <img src="{{ asset('assets/img/illustrations/karyawan_tetap.png') }}" height="70"
                                        alt="view sales" class="me-3">
                                </div>

                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div
                                    class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
                                    <div>
                                        <p class="mb-1">Karyawan Kontrak</p>
                                        <h4 class="mb-1">{{ $status_karyawan->jml_kontrak }}</h4>
                                    </div>
                                    <img src="{{ asset('assets/img/illustrations/karyawan_kontrak.png') }}" height="70"
                                        alt="view sales" class="me-3">
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="mb-1">Outsourcing</p>
                                        <h4 class="mb-1">{{ $status_karyawan->jml_outsourcing }}</h4>
                                    </div>
                                    <img src="{{ asset('assets/img/illustrations/karyawan_outsourcing.png') }}" height="70"
                                        alt="view sales" class="me-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="row mt-3">
        <div class="col-lg-8 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti ti-cake fs-4"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Karyawan Ulang Tahun</h4>
                            <small class="text-muted">Selamat ulang tahun untuk karyawan yang berulang tahun hari
                                ini</small>
                        </div>
                    </div>
                    <span class="badge bg-label-warning rounded-pill">{{ count($birthday) }} Karyawan</span>
                </div>
                <div class="card-body">
                    @if (count($birthday) > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-0">Kirim Ucapan Ulang Tahun</h6>
                                <small class="text-muted">Kirim ucapan ulang tahun ke semua karyawan yang berulang tahun hari
                                    ini</small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-success btn-sm" id="btnKirimUcapan"
                                    onclick="kirimUcapanSemua()">
                                    <i class="ti ti-brand-whatsapp me-1"></i>
                                    <span id="btnText">Kirim ke Semua</span>
                                    <span id="btnLoading" class="spinner-border spinner-border-sm ms-2 d-none"
                                        role="status"></span>
                                </button>
                            </div>
                        </div>
                        <div class="row g-3">
                            @foreach ($birthday as $d)
                                @php
                                    $umur = \Carbon\Carbon::parse($d->tanggal_lahir)->age;
                                    $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                    $colorIndex = $loop->index % count($colors);
                                    $color = $colors[$colorIndex];
                                @endphp
                                <div class="col-12">
                                    <div class="card card-border-shadow-{{ $color }} birthday-card"
                                        style="transition: all 0.3s ease; cursor: pointer;"
                                        onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3" style="width: 80px; height: 80px; position: relative;">
                                                    @if (!empty($d->foto))
                                                        @if (Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                            <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}"
                                                                class="rounded-circle border border-{{ $color }} border-3"
                                                                style="width: 80px; height: 80px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                                        @else
                                                            <div class="avatar-initial rounded-circle bg-label-{{ $color }} d-flex align-items-center justify-content-center border border-{{ $color }} border-3"
                                                                style="width: 80px; height: 80px; font-size: 32px;">
                                                                <i class="ti ti-user"></i>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="avatar-initial rounded-circle bg-label-{{ $color }} d-flex align-items-center justify-content-center border border-{{ $color }} border-3"
                                                            style="width: 80px; height: 80px; font-size: 32px;">
                                                            <i class="ti ti-user"></i>
                                                        </div>
                                                    @endif
                                                    <div class="position-absolute bottom-0 end-0 bg-{{ $color }} text-white rounded-circle d-flex align-items-center justify-content-center border border-white border-2"
                                                        style="width: 28px; height: 28px; font-size: 14px;">
                                                        <i class="ti ti-cake"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <h5 class="mb-0">{{ $d->nama_karyawan }}</h5>
                                                        <span class="badge bg-label-{{ $color }} rounded-pill">{{ $umur }}
                                                            Tahun</span>
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="ti ti-id me-2 text-{{ $color }}"></i>
                                                                <small class="text-muted">NIK:</small>
                                                                <strong class="ms-2">{{ $d->nik_show }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="ti ti-calendar me-2 text-{{ $color }}"></i>
                                                                <small class="text-muted">Tanggal Lahir:</small>
                                                                <strong
                                                                    class="ms-2">{{ date('d-m-Y', strtotime($d->tanggal_lahir)) }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="ti ti-briefcase me-2 text-{{ $color }}"></i>
                                                                <small class="text-muted">Jabatan:</small>
                                                                <strong class="ms-2">{{ $d->nama_jabatan }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="ti ti-building me-2 text-{{ $color }}"></i>
                                                                <small class="text-muted">Dept:</small>
                                                                <strong class="ms-2">{{ $d->kode_dept }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <i class="ti ti-map-pin me-2 text-{{ $color }}"></i>
                                                                <small class="text-muted">Cabang:</small>
                                                                <strong class="ms-2">{{ $d->nama_cabang }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar mb-3" style="width: 100px; height: 100px; margin: 0 auto;">
                                <span
                                    class="avatar-initial rounded-circle bg-label-secondary d-flex align-items-center justify-content-center"
                                    style="font-size: 48px;">
                                    <i class="ti ti-cake-off"></i>
                                </span>
                            </div>
                            <h5 class="text-muted">Tidak ada karyawan yang ulang tahun hari ini</h5>
                            <p class="text-muted mb-0">Semua karyawan akan menunggu hari ulang tahun mereka!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="row mb-2">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Status Karyawan</h4>
                        </div>
                        <div class="card-body">
                            {!! $chart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Pendidikan Karyawan</h4>
                        </div>
                        <div class="card-body">
                            {!! $pddchart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Jenis Kelamin</h4>
                        </div>
                        <div class="card-body">
                            {!! $jkchart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script src="{{ $chart->cdn() }}"></script>
    {{ $chart->script() }}
    {{ $jkchart->script() }}
    {{ $pddchart->script() }}
    <script>
        // Fungsi untuk mengirim ucapan ulang tahun ke semua karyawan menggunakan job
        function kirimUcapanSemua() {
            const btnKirim = document.getElementById('btnKirimUcapan');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            // Disable button dan tampilkan loading
            btnKirim.disabled = true;
            btnText.textContent = 'Mengirim...';
            btnLoading.classList.remove('d-none');

            // Ambil filter dari URL atau form
            const urlParams = new URLSearchParams(window.location.search);
            const kodeCabang = urlParams.get('kode_cabang') || '';
            const kodeDept = urlParams.get('kode_dept') || '';

            // Kirim request ke server
            fetch('{{ route('dashboard.kirim.ucapan.birthday') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    kode_cabang: kodeCabang,
                    kode_dept: kodeDept
                })
            })
                .then(response => response.json())
                .then(data => {
                    // Enable button kembali
                    btnKirim.disabled = false;
                    btnText.textContent = 'Kirim ke Semua';
                    btnLoading.classList.add('d-none');

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    // Enable button kembali
                    btnKirim.disabled = false;
                    btnText.textContent = 'Kirim ke Semua';
                    btnLoading.classList.add('d-none');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengirim ucapan: ' + error.message
                    });
                });
        }
    </script>
@endpush