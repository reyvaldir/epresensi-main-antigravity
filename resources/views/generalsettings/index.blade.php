@extends('layouts.app')
@section('titlepage', 'General Settings')

@section('content')
    @section('navigasi')
        <span>General Settings</span>
    @endsection
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    <style>
        .tgl {
            display: none;
        }

        .tgl,
        .tgl:after,
        .tgl:before,
        .tgl *,
        .tgl *:after,
        .tgl *:before,
        .tgl+.tgl-btn {
            box-sizing: border-box;
        }

        .tgl::selection {
            background: none;
        }

        .tgl+.tgl-btn {
            outline: 0;
            display: block;
            width: 4em;
            height: 2em;
            position: relative;
            cursor: pointer;
            user-select: none;
        }

        .tgl+.tgl-btn:after,
        .tgl+.tgl-btn:before {
            position: relative;
            display: block;
            content: "";
            width: 50%;
            height: 100%;
        }

        .tgl+.tgl-btn:after {
            left: 0;
        }

        .tgl+.tgl-btn:before {
            display: none;
        }

        .tgl:checked+.tgl-btn:after {
            left: 50%;
        }

        .tgl-ios+.tgl-btn {
            background: #e9ecef;
            border-radius: 2em;
            padding: 2px;
            transition: all .4s ease;
            border: 1px solid #d1d3e2;
        }

        .tgl-ios+.tgl-btn:after {
            border-radius: 2em;
            background: #fbfbfb;
            transition: left .3s cubic-bezier(0.175, 0.885, 0.320, 1.275), padding .3s ease, margin .3s ease;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, .1), 0 4px 0 rgba(0, 0, 0, .08);
        }

        .tgl-ios+.tgl-btn:active {
            box-shadow: inset 0 0 0 2em #e8eae9;
        }

        .tgl-ios+.tgl-btn:active:after {
            padding-right: .8em;
        }

        .tgl-ios:checked+.tgl-btn {
            background: #86d993;
        }

        .tgl-ios:checked+.tgl-btn:active {
            box-shadow: none;
        }

        .tgl-ios:checked+.tgl-btn:active:after {
            margin-left: -.8em;
        }

        /* Custom Range Slider Styling */
        #face_threshold {
            height: 25px;
            /* Increase overall height */
        }

        #face_threshold::-webkit-slider-runnable-track {
            height: 5px;
            /* Thicker track */
            border-radius: 5px;
            background: #e9ecef;
        }

        #face_threshold::-webkit-slider-thumb {
            width: 25px;
            /* Bigger thumb */
            height: 25px;
            /* Bigger thumb */
            margin-top: -10px;
            /* Center thumb on track */
            background-color: #0d6efd;
            border: 2px solid #fff;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
        }

        #face_threshold:focus::-webkit-slider-thumb {
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }
    </style>
    <div class="row">
        <div class="col-lg-4 col-sm-12 col-xs-12">
            <form action="{{ route('generalsetting.update', Crypt::encrypt($setting->id)) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Perusahaan</h6>
                    </div>
                    <div class="card-body">
                        <x-input-with-icon-label label="Nama Perusahaan" name="nama_perusahaan" icon="ti ti-home"
                            :value="$setting->nama_perusahaan ?? ''" />
                        <x-textarea-label label="Alamat Perusahaan" name="alamat" icon="ti ti-map-pin"
                            :value="$setting->alamat ?? ''" />
                        <x-input-with-icon-label label="Telepon" name="telepon" icon="ti ti-phone" :value="$setting->telepon ?? ''" />
                        <div class="form-group mb-3">
                            <label for="logo" style="font-weight: 600" class="form-label">Logo Perusahaan</label>
                            <input type="file" class="form-control" name="logo" id="logo">
                            <div class="mt-2 text-center">
                                @if ($setting->logo && Storage::exists('public/logo/' . $setting->logo))
                                    <img src="{{ asset('storage/logo/' . $setting->logo) }}" alt="Logo Perusahaan"
                                        style="max-width: 200px;">
                                @else
                                    <img src="https://placehold.co/200x200?text=Logo+Perusahaan&font=roboto" alt="Logo Default"
                                        style="max-width: 200px;">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Pengaturan Laporan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <x-input-with-icon-label label="Periode Laporan Dari" icon="ti ti-calendar"
                                    name="periode_laporan_dari" :value="$setting->periode_laporan_dari ?? ''" />
                            </div>
                            <div class="col">
                                <x-input-with-icon-label label="Periode Laporan Sampai" icon="ti ti-calendar"
                                    name="periode_laporan_sampai" :value="$setting->periode_laporan_sampai ?? ''" />
                            </div>
                        </div>
                        <label for="" style="font-weight: 600" class="form-label">Periode Laporan Lintas Bulan</label>
                        <div class="form-group">
                            <input class="tgl tgl-ios" id="periode_laporan_next_bulan" name="periode_laporan_next_bulan" type="checkbox" @checked($setting->periode_laporan_next_bulan ?? false)/>
                            <label class="tgl-btn" for="periode_laporan_next_bulan"></label>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Pengaturan Email</h6>
                    </div>
                    <div class="card-body">
                        <x-input-with-icon-label label="Domain Email (contoh: adamadifa.site)" name="domain_email"
                            icon="ti ti-mail" :value="$setting->domain_email ?? ''" />
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Pengaturan Presensi</h6>
                    </div>
                    <div class="card-body">
                        <x-input-with-icon-label label="Total Jam Kerja dalam 1 Bulan" name="total_jam_bulan"
                            icon="ti ti-clock" :value="$setting->total_jam_bulan ?? ''" />
                        <label for="" style="font-weight: 600" class="form-label">Denda</label>
                        <div class="form-group mb-2">
                            <input class="tgl tgl-ios" id="denda" name="denda" type="checkbox" @checked($setting->denda ?? false)/>
                            <label class="tgl-btn" for="denda"></label>
                        </div>
                        <label for="" style="font-weight: 600" class="form-label">Face Recognition</label>
                        <div class="form-group mb-2">
                            <input class="tgl tgl-ios" id="face_recognition" name="face_recognition" type="checkbox" @checked($setting->face_recognition ?? false)/>
                            <label class="tgl-btn" for="face_recognition"></label>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" style="font-weight: 600">Face Recognition Threshold (Sensitivitas)</label>
                            <div class="d-flex align-items-center">
                                <input type="range" class="form-range" name="face_threshold" id="face_threshold"
                                    min="0" max="100" step="1"
                                    value="{{ ($setting->face_threshold ?? 0.6) * 100 }}"
                                    oninput="document.getElementById('threshold_value').innerText = this.value + '%'">
                                <span id="threshold_value" class="ms-2 badge bg-primary">
                                    {{ ($setting->face_threshold ?? 0.6) * 100 }}%
                                </span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Semakin kecil persentase, semakin ketat/akurat pencocokan wajah (Default: 60%).<br>
                                <span class="text-danger">*Disarankan 60% - 80%</span>
                            </small>
                        </div>
                        <label for="" style="font-weight: 600" class="form-label">Multi Lokasi</label>
                        <div class="form-group mb-2">
                            <input class="tgl tgl-ios" id="multi_lokasi" name="multi_lokasi" type="checkbox" @checked($setting->multi_lokasi ?? false)/>
                            <label class="tgl-btn" for="multi_lokasi"></label>
                        </div>
                        <label for="" style="font-weight: 600" class="form-label">Batasi Jam Presensi</label>
                        <div class="form-group mb-2">
                            <input class="tgl tgl-ios" id="batasi_absen" name="batasi_absen" type="checkbox" @checked($setting->batasi_absen ?? false)/>
                            <label class="tgl-btn" for="batasi_absen"></label>
                        </div>
                        <x-input-with-icon-label label="Batas Jam Presensi Masuk (Dalam Jam) Sebelum Jam Masuk"
                            name="batas_jam_absen" icon="ti ti-clock" :value="$setting->batas_jam_absen ?? ''" />
                        <small class="text-muted">Wajib Diisi Jika Batasi Jam Presensi Diaktifkan</small>
                        <x-input-with-icon-label label="Batas Jam Presensi Pulang (Dalam Jam) Sebelum Jam Pulang"
                            name="batas_jam_absen_pulang" icon="ti ti-clock" :value="$setting->batas_jam_absen_pulang ?? ''" />
                        <div class="form-group">
                            <small class="text-muted">Wajib Diisi Jika Batasi Jam Presensi Diaktifkan</small>
                        </div>
                        <label for="" style="font-weight: 600" class="form-label">Batasi Hari Izin</label>
                        <div class="form-group mb-2">
                            <input class="tgl tgl-ios" id="batasi_hari_izin" name="batasi_hari_izin" type="checkbox" @checked($setting->batasi_hari_izin ?? false)/>
                            <label class="tgl-btn" for="batasi_hari_izin"></label>
                        </div>
                        <x-input-with-icon-label label="Batas Hari Izin (Dalam Hari)" name="jml_hari_izin_max"
                            icon="ti ti-clock" :value="$setting->jml_hari_izin_max ?? ''" />
                        <x-input-with-icon-label label="Batas Presensi Lintas Hari" name="batas_presensi_lintashari"
                            icon="ti ti-clock" :value="$setting->batas_presensi_lintashari ?? ''" />
                    </div>
                </div>

                {{-- <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Pengaturan Integrasi Mesin Fingerprint</h6>
                    </div>
                    <div class="card-body">
                        <x-input-with-icon-label label="Cloud Id" name="cloud_id" icon="ti ti-cloud"
                            :value="$setting->cloud_id ?? ''" />
                        <x-input-with-icon-label label="API Key" name="api_key" icon="ti ti-key"
                            :value="$setting->api_key ?? ''" />
                    </div>
                </div> --}}

                <input type="hidden" name="provider_wa" value="{{ $setting->provider_wa ?? 'ig' }}">
                <input type="hidden" name="tujuan_notifikasi_wa" value="{{ $setting->tujuan_notifikasi_wa ?? 0 }}">
                <input type="hidden" name="wa_api_key" value="{{ $setting->wa_api_key ?? '' }}">
                <input type="hidden" name="domain_wa_gateway" value="{{ $setting->domain_wa_gateway ?? '' }}">
                <input type="hidden" name="id_group_wa" value="{{ $setting->id_group_wa ?? '' }}">
                <input type="hidden" name="cloud_id" value="{{ $setting->cloud_id ?? '' }}">
                <input type="hidden" name="api_key" value="{{ $setting->api_key ?? '' }}">
                {{-- <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Whatsapp Gateway</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="provider_wa" style="font-weight: 600" class="form-label">Provider WA</label>
                            <select class="form-select" name="provider_wa" id="provider_wa">
                                <option value="ig" @selected(($setting->provider_wa ?? 'ig') == 'ig')>Internal Gateway
                                </option>
                                <option value="fe" @selected(($setting->provider_wa ?? 'ig') == 'fe')>Fonnte</option>
                            </select>
                        </div>
                        <label for="" style="font-weight: 600" class="form-label">Notifikasi WA</label>
                        <div class="checkbox-wrapper-55 mb-2">
                            <label class="rocker rocker-small">
                                <input type="checkbox" name="notifikasi_wa" @checked($setting->notifikasi_wa ?? false)>
                                <span class="switch-left">Yes</span>
                                <span class="switch-right">No</span>
                            </label>
                        </div>
                        <label for="" style="font-weight: 600" class="form-label">Tujuan Notifikasi WA</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="tujuan_notifikasi_wa" id="tujuan_grup"
                                value="1" @checked(($setting->tujuan_notifikasi_wa ?? 0) == 1)>
                            <label class="form-check-label" for="tujuan_grup">
                                Kirim ke Grup
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="tujuan_notifikasi_wa" id="tujuan_karyawan"
                                value="0" @checked(($setting->tujuan_notifikasi_wa ?? 0) == 0)>
                            <label class="form-check-label" for="tujuan_karyawan">
                                Kirim ke Karyawan
                            </label>
                        </div>
                        <div id="group_wa_input" style="display: none;">
                            <x-input-with-icon-label label="ID Group WA" name="id_group_wa" icon="ti ti-users"
                                :value="$setting->id_group_wa ?? ''" />
                        </div>
                        <x-input-with-icon-label label="Domain WA Gateway (contoh: https://wa.adamadifa.site)"
                            name="domain_wa_gateway" icon="ti ti-message" :value="$setting->domain_wa_gateway ?? ''" />
                        <x-input-with-icon-label label="WA API Key" name="wa_api_key" icon="ti ti-brand-whatsapp"
                            :value="$setting->wa_api_key ?? ''" />
                    </div>
                </div> --}}

                <button class="btn btn-primary w-100" id="btnSimpan">
                    <i class="ti ti-refresh me-1"></i> Update
                </button>
            </form>
        </div>
        <div class="col-lg-4 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="pwa_icon" style="font-weight: 600" class="form-label">
                            Upload Icon Master (1080x1080px)
                        </label>
                        <input type="file" class="form-control" name="pwa_icon" id="pwa_icon" accept="image/*">
                        <small class="text-muted">
                            Upload gambar dengan ukuran 1080x1080px atau lebih besar.
                            Sistem akan otomatis generate berbagai ukuran untuk PWA.
                        </small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success w-100" id="btnGenerateIcons">
                                <i class="ti ti-device-mobile me-1"></i> Generate PWA Icons
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-warning w-100" id="btnPreviewIcons">
                                <i class="ti ti-eye me-1"></i> Preview Icons
                            </button>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div id="progressContainer" style="display: none;">
                        <div class="progress mb-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: 0%">
                            </div>
                        </div>
                        <small class="text-muted" id="progressText">Menggenerate icons...</small>
                    </div>

                    <!-- Generated Icons Preview -->
                    <div id="iconsPreview" class="mt-3" style="display: none;">
                        <h6>Generated Icons:</h6>
                        <div class="row" id="iconsGrid">
                            <!-- Icons will be loaded here -->
                        </div>
                    </div>

                    <!-- Current PWA Icons -->
                    <div class="mt-3">
                        <h6>Current PWA Icons:</h6>
                        <div class="row" id="currentIconsGrid">
                            @php
                                $iconDir = public_path('assets/img/icons/pwa');
                                $currentIcons = [];
                                if (file_exists($iconDir)) {
                                    $files = glob($iconDir . '/icon-*.png');
                                    foreach ($files as $file) {
                                        $filename = basename($file);
                                        $size = str_replace(['icon-', '.png'], '', $filename);
                                        $currentIcons[] = [
                                            'size' => $size,
                                            'path' => 'assets/img/icons/pwa/' . $filename,
                                        ];
                                    }
                                }
                            @endphp

                            @if (count($currentIcons) > 0)
                                @foreach ($currentIcons as $icon)
                                    <div class="col-2 mb-2">
                                        <div class="text-center">
                                            <img src="{{ asset($icon['path']) }}" alt="Icon {{ $icon['size'] }}"
                                                class="img-thumbnail" style="width: 50px; height: 50px;">
                                            <small class="d-block">{{ $icon['size'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Belum ada icon PWA yang di-generate. Upload icon master untuk memulai.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@push('myscript')
    <script>
        $(document).ready(function () {
            $('#batas_presensi_lintashari').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
            });

            // Toggle Group WA Input
            function toggleGroupInput() {
                const tujuanGrup = $('#tujuan_grup').is(':checked');
                if (tujuanGrup) {
                    $('#group_wa_input').show();
                } else {
                    $('#group_wa_input').hide();
                }
            }

            // Initialize on page load
            toggleGroupInput();

            // Toggle on radio button change
            $('input[name="tujuan_notifikasi_wa"]').change(function () {
                toggleGroupInput();
            });

            // PWA Icon Generator
            $('#btnGenerateIcons').click(function () {
                const fileInput = document.getElementById('pwa_icon');
                const file = fileInput.files[0];

                if (!file) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih file icon terlebih dahulu!'
                    });
                    return;
                }

                // Validate file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ukuran file terlalu besar! Maksimal 10MB.'
                    });
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'File harus berupa gambar!'
                    });
                    return;
                }

                generateIcons(file);
            });

            $('#btnPreviewIcons').click(function () {
                previewCurrentIcons();
            });

            function generateIcons(file) {
                const formData = new FormData();
                formData.append('icon', file);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Show progress
                $('#progressContainer').show();
                $('#btnGenerateIcons').prop('disabled', true);
                updateProgress(0, 'Memulai proses...');

                $.ajax({
                    url: '{{ route('pwa.generate-icons') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function () {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                updateProgress(percentComplete, 'Uploading file...');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (response) {
                        updateProgress(100, 'Selesai!');

                        setTimeout(() => {
                            $('#progressContainer').hide();
                            $('#btnGenerateIcons').prop('disabled', false);

                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: `Berhasil generate ${response.count} icon PWA!`
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        }, 1000);
                    },
                    error: function (xhr) {
                        $('#progressContainer').hide();
                        $('#btnGenerateIcons').prop('disabled', false);

                        let errorMessage = 'Terjadi kesalahan saat generate icons!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            }

            function updateProgress(percent, text) {
                $('.progress-bar').css('width', percent + '%');
                $('#progressText').text(text);
            }

            function previewCurrentIcons() {
                $.ajax({
                    url: '{{ route('pwa.preview-icons') }}',
                    type: 'GET',
                    success: function (response) {
                        if (response.length > 0) {
                            let html = '';
                            response.forEach(function (icon) {
                                html += `
                                        <div class="col-2 mb-2">
                                            <div class="text-center">
                                                <img src="${icon.url}"
                                                     alt="Icon ${icon.size}"
                                                     class="img-thumbnail"
                                                     style="width: 50px; height: 50px;">
                                                <small class="d-block">${icon.size}</small>
                                            </div>
                                        </div>
                                    `;
                            });

                            $('#iconsGrid').html(html);
                            $('#iconsPreview').show();

                            Swal.fire({
                                icon: 'info',
                                title: 'Preview Icons',
                                text: `Ditemukan ${response.length} icon PWA yang sudah di-generate.`
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: 'Belum ada icon PWA yang di-generate.'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat preview icons!'
                        });
                    }
                });
            }
        });
    </script>
@endpush