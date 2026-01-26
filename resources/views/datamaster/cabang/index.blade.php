@extends('layouts.app')
@section('titlepage', 'Cabang')

@section('content')
    @section('navigasi')
        <span>Cabang</span>
    @endsection
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    @can('cabang.create')
                        <a href="#" class="btn btn-primary" id="btncreateCabang"><i class="fa fa-plus me-2"></i> Tambah
                            Cabang</a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('cabang.index') }}">
                                <div class="row">
                                    <div class="col-lg-10 col-sm-12 col-md-12">
                                        <x-input-with-icon label="Cari Nama Cabang" value="{{ Request('nama_cabang') }}"
                                            name="nama_cabang" icon="ti ti-search" />
                                    </div>
                                    <div class="col-lg-2 col-sm-12 col-md-12">
                                        <button class="btn btn-primary"><i
                                                class="ti ti-icons ti-search me-1"></i>Cari</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive mb-2">
                                <table class="table">
                                    <thead class="table-dark">
                                        <tr>
                                        <tr>
                                            <th class="text-center align-middle">NO.</th>
                                            <th class="text-center align-middle">
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'kode_cabang', 'order' => request('sort') == 'kode_cabang' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                    class="text-white text-decoration-none">
                                                    KODE
                                                    @if(request('sort') == 'kode_cabang') <i
                                                        class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="text-center align-middle">
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_cabang', 'order' => request('sort') == 'nama_cabang' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                    class="text-white text-decoration-none">
                                                    NAMA CABANG
                                                    @if(request('sort') == 'nama_cabang') <i
                                                        class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="text-center align-middle">
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'alamat_cabang', 'order' => request('sort') == 'alamat_cabang' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                    class="text-white text-decoration-none">
                                                    ALAMAT
                                                    @if(request('sort') == 'alamat_cabang') <i
                                                        class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="text-center align-middle">
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'telepon_cabang', 'order' => request('sort') == 'telepon_cabang' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                    class="text-white text-decoration-none">
                                                    TELEPON
                                                    @if(request('sort') == 'telepon_cabang') <i
                                                        class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="text-center align-middle">
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'radius_cabang', 'order' => request('sort') == 'radius_cabang' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                                    class="text-white text-decoration-none">
                                                    RADIUS
                                                    @if(request('sort') == 'radius_cabang') <i
                                                        class="ti ti-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="text-center align-middle">#</th>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cabang as $d)
                                            <tr>
                                                <td> {{ $loop->iteration + $cabang->firstItem() - 1 }}</td>
                                                <td>{{ $d->kode_cabang }}</td>
                                                <td>{{ textUpperCase($d->nama_cabang) }}</td>
                                                <td>{{ $d->alamat_cabang }}</td>
                                                <td>{{ $d->telepon_cabang }}</td>
                                                <td>{{ $d->radius_cabang }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        @can('cabang.edit')
                                                            <div>
                                                                <a href="#" class="me-2 editCabang"
                                                                    kode_cabang="{{ Crypt::encrypt($d->kode_cabang) }}">
                                                                    <i class="ti ti-edit text-success"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('cabang.delete')
                                                            <div>
                                                                <form method="POST" name="deleteform" class="deleteform"
                                                                    action="{{ route('cabang.delete', Crypt::encrypt($d->kode_cabang)) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <a href="#" class="delete-confirm ml-1">
                                                                        <i class="ti ti-trash text-danger"></i>
                                                                    </a>
                                                                </form>
                                                            </div>
                                                        @endcan


                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div style="float: right;">
                                {{ $cabang->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-modal-form id="mdlcreateCabang" size="" show="loadcreateCabang" title="Tambah Cabang" />
    <x-modal-form id="mdleditCabang" size="" show="loadeditCabang" title="Edit Cabang" />
@endsection
@push('myscript')
    {{--
    <script src="{{ asset('assets/js/pages/roles/create.js') }}"></script> --}}
    <script>
        $(function () {
            $("#btncreateCabang").click(function (e) {
                $('#mdlcreateCabang').modal("show");
                $("#loadcreateCabang").load('/cabang/create');
            });

            $(".editCabang").click(function (e) {
                var kode_cabang = $(this).attr("kode_cabang");
                e.preventDefault();
                $('#mdleditCabang').modal("show");
                $("#loadeditCabang").load('/cabang/' + kode_cabang);
            });
        });
    </script>
@endpush