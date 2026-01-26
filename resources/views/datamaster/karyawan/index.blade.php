@extends('layouts.app')
@section('titlepage', 'Karyawan')

@section('content')
@section('navigasi')
    <span>Karyawan</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('karyawan.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah
                        Karyawan</a>
                    <a href="#" class="btn btn-success" id="btnImport"><i class="ti ti-file-import me-2"></i> Import Excel</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('karyawan.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                        icon="ti ti-search" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                        selected="{{ Request('kode_cabang') }}" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                        selected="{{ Request('kode_dept') }}" upperCase="true" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary"><i class="ti ti-icons ti-search me-1"></i>Cari</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">

                        <div class="table-responsive mb-2">
                            <table class="table  table-hover table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center align-middle">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nik', 'order' => request('sort') == 'nik' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none">
                                                NIK 
                                                @if(request('sort') == 'nik') <i class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                            </a>
                                        </th>
                                        <th class="text-center align-middle">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_karyawan', 'order' => request('sort') == 'nama_karyawan' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none">
                                                NAMA KARYAWAN
                                                @if(request('sort') == 'nama_karyawan') <i class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                            </a>
                                        </th>
                                        <th class="text-center align-middle">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_dept', 'order' => request('sort') == 'nama_dept' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none">
                                                DEPARTEMEN
                                                @if(request('sort') == 'nama_dept') <i class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                            </a>
                                        </th>
                                        <th class="text-center align-middle">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_jabatan', 'order' => request('sort') == 'nama_jabatan' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none">
                                                JABATAN
                                                @if(request('sort') == 'nama_jabatan') <i class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                            </a>
                                        </th>
                                        <th class="text-center align-middle">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_cabang', 'order' => request('sort') == 'nama_cabang' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none">
                                                CABANG
                                                @if(request('sort') == 'nama_cabang') <i class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                            </a>
                                        </th>
                                        <th class="text-center align-middle">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status_aktif_karyawan', 'order' => request('sort') == 'status_aktif_karyawan' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none">
                                                STATUS
                                                @if(request('sort') == 'status_aktif_karyawan') <i class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                            </a>
                                        </th>
                                        <th class="text-center align-middle">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal_masuk', 'order' => request('sort') == 'tanggal_masuk' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-white text-decoration-none">
                                                TANGGAL MASUK
                                                @if(request('sort') == 'tanggal_masuk') <i class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                            </a>
                                        </th>
                                        <th class="text-center align-middle"><i class="ti ti-map-pin"></i></th>
                                        <th class="text-center align-middle"><i class="ti ti-clock-hour-3"></i></th>
                                        <th class="text-center align-middle"><i class="ti ti-fingerprint"></i></th>
                                        <th class="text-center align-middle">FOTO</th>
                                        <th class="text-center align-middle">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($karyawan as $d)
                                        <tr>
                                            <td>{{ $d->nik_show ?? $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->nama_dept }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>
                                            <td>{{ $d->nama_cabang }}</td>
                                            <td>
                                                @if ($d->status_aktif_karyawan == '1')
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Non Aktif</span>
                                                @endif
                                            </td>
                                            <td>{{ date('d-m-y', strtotime($d->tanggal_masuk)) }}</td>
                                            <td class="text-center">

                                                @if ($d->lock_location == '1')
                                                    <a href="{{ route('karyawan.lockunlocklocation', Crypt::encrypt($d->nik)) }}">
                                                        <i class="ti ti-lock text-success"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('karyawan.lockunlocklocation', Crypt::encrypt($d->nik)) }}">
                                                        <i class="ti ti-lock-open text-danger"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                @if ($d->lock_jam_kerja == '1')
                                                    <a href="{{ route('karyawan.lockunlockjamkerja', Crypt::encrypt($d->nik)) }}">
                                                        <i class="ti ti-lock text-success"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('karyawan.lockunlockjamkerja', Crypt::encrypt($d->nik)) }}">
                                                        <i class="ti ti-lock-open text-danger"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $d->pin }}</td>
                                            <td>
                                                @if (!empty($d->foto))
                                                    @if (Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                        <div class="avatar avatar-xs me-2">
                                                            <img src="{{ getfotoKaryawan($d->foto) }}" alt="" class="rounded-circle">
                                                        </div>
                                                    @else
                                                        <div class="avatar avatar-xs me-2">
                                                            <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt=""
                                                                class="rounded-circle">
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="avatar avatar-xs me-2">
                                                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt=""
                                                            class="rounded-circle">
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('karyawan.setjamkerja')
                                                        <div>
                                                            <a href="#" class="me-2 btnSetJamkerja" nik="{{ Crypt::encrypt($d->nik) }}">
                                                                <i class="ti ti-device-watch text-primary"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('karyawan.setcabang')
                                                        <div>
                                                            <a href="#" class="me-2 btnSetCabang" nik="{{ Crypt::encrypt($d->nik) }}">
                                                                <i class="ti ti-map text-warning"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('karyawan.edit')
                                                        <div>
                                                            <a href="#" class="me-2 btnEdit" nik="{{ Crypt::encrypt($d->nik) }}">
                                                                <i class="ti ti-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('karyawan.show')
                                                        <div>
                                                            <a href="{{ route('karyawan.show', Crypt::encrypt($d->nik)) }}" class="me-2">
                                                                <i class="ti ti-file-description text-info"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('karyawan.delete')
                                                        <div>
                                                            <form method="POST" name="deleteform" class="deleteform me-1"
                                                                action="{{ route('karyawan.delete', Crypt::encrypt($d->nik)) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="delete-confirm ml-1">
                                                                    <i class="ti ti-trash text-danger"></i>
                                                                </a>
                                                            </form>
                                                        </div>
                                                    @endcan

                                                    @can('users.create')
                                                        @if (empty($d->id_user))
                                                            <a href="{{ route('karyawan.createuser', Crypt::encrypt($d->nik)) }}">
                                                                <i class="ti ti-user-plus text-danger"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('karyawan.deleteuser', Crypt::encrypt($d->nik)) }}">
                                                                <i class="ti ti-user text-success"></i>
                                                            </a>
                                                        @endif
                                                    @endcan

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <div style="float: right;">
                            {{ $karyawan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="modal" show="loadmodal" />
<x-modal-form id="modalSetJamkerja" show="loadmodalSetJamkerja" size="modal-lg" title="Set Jam Kerja" />
<x-modal-form id="modalSetCabang" show="loadmodalSetCabang" size="modal-lg" title="Set Cabang Karyawan" />
<x-modal-form id="modalImport" show="loadmodalImport" size="modal-lg" title="Import Data Karyawan" />
@endsection
@push('myscript')
<script>
    $(function() {

        function loading() {
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
        };
        loading();
        $("#btnCreate").click(function() {
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Karyawan");
            $("#loadmodal").load("{{ route('karyawan.create') }}");
        });

        $("#btnImport").click(function() {
            $("#modalImport").modal("show");
            $("#loadmodalImport").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
            $("#loadmodalImport").load("{{ route('karyawan.import') }}");
        });

        $(".btnEdit").click(function() {
            loading();
            const nik = $(this).attr("nik");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Karyawan");
            $("#loadmodal").load(`/karyawan/${nik}/edit`);
        });

        $(".btnSetJamkerja").click(function() {
            const nik = $(this).attr("nik");
            $("#modalSetJamkerja").modal("show");
            $("#loadmodalSetJamkerja").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);

            $("#loadmodalSetJamkerja").load(`/karyawan/${nik}/setjamkerja`);
        });

        $(".btnSetCabang").click(function() {
            const nik = $(this).attr("nik");
            $("#modalSetCabang").modal("show");
            $("#loadmodalSetCabang").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);

            $("#loadmodalSetCabang").load(`/karyawan/${nik}/setcabang`);
        });


    });
</script>
@endpush
