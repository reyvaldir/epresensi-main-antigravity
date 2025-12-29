@extends('layouts.app')
@section('titlepage', 'Update Aplikasi')

@section('content')
@section('navigasi')
    <span>Update Aplikasi</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Informasi Versi Aplikasi</h5>
                <button type="button" class="btn btn-primary btn-sm" onclick="checkUpdate()">
                    <i class="ti ti-refresh me-1"></i> Cek Update
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Versi Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-tag"></i></span>
                                <input type="text" class="form-control" value="{{ $currentVersion }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Aplikasi</label>
                            <div class="mt-2">
                                <span class="badge bg-success fs-6">
                                    <i class="ti ti-check-circle me-1"></i> Aplikasi Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Terakhir Update</label>
                            <div class="mt-2">
                                @php
                                    $lastUpdate = $updateLogs->where('status', 'success')->first();
                                @endphp
                                @if ($lastUpdate)
                                    <span
                                        class="text-muted">{{ $lastUpdate->completed_at ? $lastUpdate->completed_at->format('d/m/Y H:i') : $lastUpdate->created_at->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="updateInfo" style="display: none;">
    <div class="col-12">
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning bg-opacity-10">
                <h5 class="card-title mb-0">
                    <i class="ti ti-bell-ringing me-2"></i>Update Tersedia
                </h5>
            </div>
            <div class="card-body" id="updateContent">
                <!-- Content akan diisi via AJAX -->
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Riwayat Update</h5>
                <a href="{{ route('update.history') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-list me-1"></i> Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Versi</th>
                                <th width="15%">Status</th>
                                <th width="20%">User</th>
                                <th width="20%">Tanggal</th>
                                <th width="15%">Versi Sebelumnya</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($updateLogs as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->version }}</strong>
                                    </td>
                                    <td>
                                        @if ($log->status == 'success')
                                            <span class="badge bg-success">
                                                <i class="ti ti-check me-1"></i>Berhasil
                                            </span>
                                        @elseif($log->status == 'failed')
                                            <span class="badge bg-danger">
                                                <i class="ti ti-x me-1"></i>Gagal
                                            </span>
                                        @elseif($log->status == 'downloading')
                                            <span class="badge bg-info">
                                                <i class="ti ti-download me-1"></i>Mengunduh
                                            </span>
                                        @elseif($log->status == 'installing')
                                            <span class="badge bg-warning">
                                                <i class="ti ti-settings me-1"></i>Menginstall
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="ti ti-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>{{ $log->user->name ?? '-' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $log->created_at->format('d/m/Y') }}<br>
                                            <span class="text-muted">{{ $log->created_at->format('H:i') }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $log->previous_version ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('update.log', $log->id) }}" class="btn btn-sm btn-label-info">
                                            <i class="ti ti-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ti ti-inbox fs-1 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Belum ada riwayat update</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p id="loadingText">Memproses...</p>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    function checkUpdate() {
        $('#loadingModal').modal('show');
        $('#loadingText').text('Mengecek update...');

        $.ajax({
            url: '{{ route('update.check') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#loadingModal').modal('hide');

                if (response.has_update) {
                    showUpdateInfo(response);
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Update',
                        text: 'Aplikasi sudah menggunakan versi terbaru',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.error || 'Gagal mengecek update',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function showUpdateInfo(data) {
        let html = `
            <div class="alert alert-info border-info mb-3">
                <div class="d-flex align-items-center">
                    <i class="ti ti-info-circle fs-4 me-2"></i>
                    <div>
                        <h6 class="mb-1">Versi Terbaru Tersedia!</h6>
                        <p class="mb-0">Versi Terbaru: <strong>${data.latest_version}</strong> | Versi Saat Ini: <strong>${data.current_version}</strong></p>
                    </div>
                </div>
            </div>
        `;

        if (data.update) {
            html += `
                <div class="mb-4">
                    <h6 class="mb-2">
                        <i class="ti ti-package me-2"></i>
                        ${data.update.title || 'Update ' + data.update.version}
                    </h6>
                    ${data.update.description ? `<p class="text-muted">${data.update.description}</p>` : ''}
                    ${data.update.changelog ? `
                            <div class="mt-3">
                                <h6 class="mb-2">Changelog:</h6>
                                <pre class="bg-light p-3 rounded border" style="max-height: 300px; overflow-y: auto; white-space: pre-wrap;">${data.update.changelog}</pre>
                            </div>
                        ` : ''}
                    ${data.update.file_size ? `
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="ti ti-file-zip me-1"></i>
                                    Ukuran File: ${formatFileSize(data.update.file_size)}
                                </small>
                            </div>
                        ` : ''}
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-primary" onclick="updateNow('${data.update.version}')">
                        <i class="ti ti-download me-1"></i> Update Sekarang
                    </button>
                    <button class="btn btn-outline-secondary" onclick="downloadUpdate('${data.update.version}')">
                        <i class="ti ti-download me-1"></i> Download Saja
                    </button>
                </div>
            `;
        }

        $('#updateContent').html(html);
        $('#updateInfo').show();
        $('html, body').animate({
            scrollTop: $('#updateInfo').offset().top - 100
        }, 500);
    }

    function formatFileSize(bytes) {
        if (!bytes) return '-';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function updateNow(version) {
        Swal.fire({
            title: 'Konfirmasi Update',
            text: 'Apakah Anda yakin ingin mengupdate aplikasi sekarang? Pastikan sudah backup database.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loadingModal').modal('show');
                $('#loadingText').text('Mengupdate aplikasi...');

                $.ajax({
                    url: `/update/${version}/update-now`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#loadingModal').modal('hide');
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Update berhasil diinstall. Halaman akan direload.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal menginstall update',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#loadingModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.error || 'Gagal mengupdate aplikasi',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    function downloadUpdate(version) {
        $('#loadingModal').modal('show');
        $('#loadingText').text('Mengunduh file update...');

        $.ajax({
            url: `/update/${version}/download`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#loadingModal').modal('hide');
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'File update berhasil diunduh',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal mengunduh file',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.error || 'Gagal mengunduh file update',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>
@endpush
