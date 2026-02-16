@extends('layouts.app')
@section('titlepage', 'Backup & Restore')

@section('content')
@section('navigasi')
    <span>Backup & Restore</span>
@endsection

<div class="row">
    {{-- Backup Section --}}
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Backup & Restore Database + File</h5>
                <button type="button" class="btn btn-primary" id="btn-create-backup">
                    <i class="ti ti-download me-2"></i>Buat Backup Baru
                </button>
            </div>
            <div class="card-body">
                {{-- Alert Info --}}
                <div class="alert alert-info mb-4">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Info:</strong> Backup berisi <strong>database MySQL</strong> dan <strong>seluruh file</strong>
                    (foto profil, logo, foto presensi, dll) dalam format ZIP. File backup disimpan di server dan
                    langsung ter-download saat dibuat.
                </div>

                {{-- Backup List Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama File</th>
                                <th width="12%">Ukuran</th>
                                <th width="18%">Tanggal</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($backups as $index => $backup)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <i class="ti ti-file-zip me-1 text-primary"></i>
                                        {{ $backup['filename'] }}
                                    </td>
                                    <td>{{ $backup['size'] }}</td>
                                    <td>{{ $backup['date'] }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('backuprestore.download', $backup['filename']) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="ti ti-download me-1"></i>Download
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning btn-restore-server"
                                                data-filename="{{ $backup['filename'] }}">
                                                <i class="ti ti-refresh me-1"></i>Restore
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-backup"
                                                data-filename="{{ $backup['filename'] }}">
                                                <i class="ti ti-trash me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="ti ti-database-off" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">Belum ada file backup.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Restore from Upload Section --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-upload me-2"></i>Restore dari File Upload
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-4">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <strong>Peringatan:</strong> Restore akan <strong>menimpa seluruh database dan file</strong> yang ada
                    saat ini. Pastikan Anda sudah membuat backup sebelum melakukan restore. Tindakan ini
                    <strong>tidak dapat dibatalkan</strong>.
                </div>

                <form id="restoreUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label for="backup_file" class="form-label">Pilih File Backup (.zip)</label>
                            <input type="file" class="form-control" id="backup_file" name="backup_file"
                                accept=".zip" required>
                            <small class="text-muted">Maksimal 500MB. File harus berformat .zip dan berisi
                                database.sql</small>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-warning w-100" id="btn-restore-upload">
                                <i class="ti ti-upload me-2"></i>Upload & Restore
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Loading Modal --}}
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0" id="loadingText">Sedang membuat backup...</p>
                <small class="text-muted">Proses ini bisa memakan waktu beberapa menit</small>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {

        // ============ CREATE BACKUP ============
        $('#btn-create-backup').click(function() {
            Swal.fire({
                title: 'Buat Backup?',
                text: 'Proses backup akan membuat salinan database dan seluruh file. Ini bisa memakan waktu beberapa menit.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Buat Backup',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#696cff',
            }).then((result) => {
                if (result.isConfirmed) {
                    createBackup();
                }
            });
        });

        function createBackup() {
            $('#loadingText').text('Sedang membuat backup...');
            $('#loadingModal').modal('show');

            $.ajax({
                url: '{{ route("backuprestore.create") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                dataType: 'json',
                timeout: 600000, // 10 minutes
                success: function(response) {
                    $('#loadingModal').modal('hide');
                    if (response && response.success && response.filename) {
                        // Trigger download via redirect
                        window.location.href = '/backuprestore/download/' + response.filename;

                        Swal.fire({
                            icon: 'success',
                            title: 'Backup Selesai!',
                            text: 'File backup sedang di-download. Halaman akan di-refresh.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#696cff',
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    $('#loadingModal').modal('hide');
                    var msg = 'Terjadi kesalahan saat membuat backup.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Backup Gagal!',
                        text: msg,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // ============ RESTORE FROM SERVER ============
        $(document).on('click', '.btn-restore-server', function() {
            var filename = $(this).data('filename');

            Swal.fire({
                title: 'Restore Backup?',
                html: '<div class="text-start">' +
                    '<p>Anda akan me-restore dari file:</p>' +
                    '<p class="fw-bold text-primary">' + filename + '</p>' +
                    '<div class="alert alert-danger py-2 mt-2">' +
                    '<i class="ti ti-alert-triangle me-1"></i>' +
                    '<strong>PERHATIAN:</strong> Seluruh database dan file saat ini akan ditimpa!' +
                    '</div></div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Restore Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ff3e1d',
            }).then((result) => {
                if (result.isConfirmed) {
                    doRestore({ backup_filename: filename });
                }
            });
        });

        // ============ RESTORE FROM UPLOAD ============
        $('#btn-restore-upload').click(function() {
            var fileInput = document.getElementById('backup_file');

            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Pilih file backup (.zip) terlebih dahulu.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            var fileName = fileInput.files[0].name;

            Swal.fire({
                title: 'Upload & Restore?',
                html: '<div class="text-start">' +
                    '<p>Anda akan me-restore dari file upload:</p>' +
                    '<p class="fw-bold text-primary">' + fileName + '</p>' +
                    '<div class="alert alert-danger py-2 mt-2">' +
                    '<i class="ti ti-alert-triangle me-1"></i>' +
                    '<strong>PERHATIAN:</strong> Seluruh database dan file saat ini akan ditimpa!' +
                    '</div></div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Upload & Restore',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ff3e1d',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.getElementById('restoreUploadForm'));
                    doRestore(formData, true);
                }
            });
        });

        function doRestore(data, isFormData = false) {
            $('#loadingText').text('Sedang merestore backup...');
            $('#loadingModal').modal('show');

            var ajaxConfig = {
                url: '{{ route("backuprestore.restore") }}',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    $('#loadingModal').modal('hide');
                    if (response && response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Restore Berhasil!',
                            text: response.success,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#696cff',
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    $('#loadingModal').modal('hide');
                    var msg = 'Terjadi kesalahan saat restore.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Restore Gagal!',
                        text: msg,
                        confirmButtonText: 'OK'
                    });
                }
            };

            if (isFormData) {
                ajaxConfig.data = data;
                ajaxConfig.processData = false;
                ajaxConfig.contentType = false;
            } else {
                data._token = '{{ csrf_token() }}';
                ajaxConfig.data = data;
            }

            $.ajax(ajaxConfig);
        }

        // ============ DELETE BACKUP ============
        $(document).on('click', '.btn-delete-backup', function() {
            var filename = $(this).data('filename');

            Swal.fire({
                title: 'Hapus Backup?',
                text: 'File ' + filename + ' akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ff3e1d',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/backuprestore/' + filename,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.success,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#696cff',
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            var msg = 'Terjadi kesalahan saat menghapus.';
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                msg = xhr.responseJSON.error;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: msg,
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

    });
</script>
@endpush
