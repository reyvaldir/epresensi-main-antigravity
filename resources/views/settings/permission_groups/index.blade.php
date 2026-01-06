@extends('layouts.app')
@section('titlepage', 'Permission Groups')

@section('content')
    @section('navigasi')
        <span>Permission Groups</span>
    @endsection
    <div class="row">
        <div class="col-lg-8 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="btncreateGroup"><i class="fa fa-plus me-2"></i> Tambah
                        Group</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('permissiongroups.index') }}">
                                <div class="row">
                                    <div class="col-lg-10 col-sm-12 col-md-12">
                                        <x-input-with-icon label="Search Group" value="{{ Request('name') }}" name="name"
                                            icon="ti ti-search" />
                                    </div>
                                    <div class="col-lg-2 col-sm-12 col-md-12">
                                        <button class="btn btn-primary">Cari</button>
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
                                            <th>No.</th>
                                            <th>Group</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($permission_groups as $d)
                                            <tr>
                                                <td>{{ $loop->iteration + $permission_groups->firstItem() - 1 }}</td>
                                                <td>{{ $d->name }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <div>
                                                            <a href="#" class="me-2 editGroup"
                                                                id="{{ Crypt::encrypt($d->id) }}">
                                                                <i class="fa fa-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                        <div>
                                                            <form method="POST" name="deleteform" class="deleteform"
                                                                action="{{ route('permissiongroups.delete', Crypt::encrypt($d->id)) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="delete-confirm ml-1">
                                                                    <i class="fa fa-trash-alt text-danger"></i>
                                                                </a>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                {{ $permission_groups->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal-form id="mdlcreateGroup" size="" show="loadcreateGroup" title="Tambah Group" />
    <x-modal-form id="mdleditGroup" size="" show="loadeditGroup" title="Edit Group" />

@endsection

@push('myscript')
    <script>
        $(function () {
            $("#btncreateGroup").click(function (e) {
                e.preventDefault();
                $('#mdlcreateGroup').modal("show");
                $("#loadcreateGroup").load('/permissiongroups/create');
            });

            $(".editGroup").click(function (e) {
                var id = $(this).attr("id");
                e.preventDefault();
                $('#mdleditGroup').modal("show");
                $("#loadeditGroup").load('/permissiongroups/' + id + '/edit');
            });

            // Delete Confirmation Logic matching Users Page
            $(".delete-confirm").click(function (e) {
                e.preventDefault();
                var form = $(this).closest('form');

                if (typeof Swal !== 'undefined' && $.isFunction(Swal.fire)) {
                    Swal.fire({
                        title: 'Apakah Anda Yakin Ingin Menghapus Data Ini ?',
                        text: "Jika dihapus maka data akan hilang permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Hapus Saja!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                        form.submit();
                    }
                }
            });
        });
    </script>
@endpush