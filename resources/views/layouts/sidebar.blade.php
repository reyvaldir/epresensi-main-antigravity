<!-- Menu -->

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
                <i class="ti ti-fingerprint" style="font-size:32px !important"></i>
            </span>
            <span class="app-brand-text demo menu-text fw-bold"><i></i>E-Presensi</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- 1. DASHBOARD -->
        <li class="menu-item {{ request()->is(['dashboard', 'dashboard/*']) ? 'active' : '' }}">
            <a href="{{ route('dashboard.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-home"></i>
                <div>Dashboard</div>
            </a>
        </li>


        <!-- 2. DATA MASTER (Organisasi & Waktu) -->
        @if (auth()->user()->hasAnyPermission(['karyawan.index', 'departemen.index', 'cabang.index', 'jabatan.index', 'grup.index', 'jamkerja.index', 'harilibur.index', 'jamkerjabydept.index', 'cuti.index']))

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Data Master</span>
            </li>

            {{-- Header: Organisasi & SDM --}}
            <li
                class="menu-item {{ request()->is(['karyawan', 'karyawan/*', 'departemen', 'departemen/*', 'cabang', 'jabatan', 'grup', 'grup/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-users-group"></i> {{-- Changed Icon --}}
                    <div>Organisasi & SDM</div>
                </a>
                <ul class="menu-sub">
                    @can('karyawan.index')
                        <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*']) ? 'active' : '' }}">
                            <a href="{{ route('karyawan.index') }}" class="menu-link">
                                <div>Data Karyawan</div>
                            </a>
                        </li>
                    @endcan
                    @can('departemen.index')
                        <li class="menu-item {{ request()->is(['departemen', 'departemen/*']) ? 'active' : '' }}">
                            <a href="{{ route('departemen.index') }}" class="menu-link">
                                <div>Departemen</div>
                            </a>
                        </li>
                    @endcan
                    @can('cabang.index')
                        <li class="menu-item {{ request()->is(['cabang', 'cabang/*']) ? 'active' : '' }}">
                            <a href="{{ route('cabang.index') }}" class="menu-link">
                                <div>Kantor Cabang</div>
                            </a>
                        </li>
                    @endcan
                    @can('jabatan.index')
                        <li class="menu-item {{ request()->is(['jabatan', 'jabatan/*']) ? 'active' : '' }}">
                            <a href="{{ route('jabatan.index') }}" class="menu-link">
                                <div>Jabatan</div>
                            </a>
                        </li>
                    @endcan
                    @can('grup.index')
                        <li class="menu-item {{ request()->is(['grup', 'grup/*']) ? 'active' : '' }}">
                            <a href="{{ route('grup.index') }}" class="menu-link">
                                <div>Grup</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>

            {{-- Header: Aturan Waktu & Libur --}}
            <li
                class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*', 'jamkerjabydept', 'jamkerjabydept/*', 'harilibur', 'harilibur/*', 'cuti', 'cuti/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-clock-cog"></i> {{-- Changed Icon --}}
                    <div>Aturan Waktu & Libur</div>
                </a>
                <ul class="menu-sub">
                    @can('jamkerja.index')
                        <li class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*']) ? 'active' : '' }}">
                            <a href="{{ route('jamkerja.index') }}" class="menu-link">
                                <div>Pola Jam Kerja</div>
                            </a>
                        </li>
                    @endcan
                    @can('jamkerjabydept.index')
                        <li class="menu-item {{ request()->is(['jamkerjabydept', 'jamkerjabydept/*']) ? 'active' : '' }}">
                            <a href="{{ route('jamkerjabydept.index') }}" class="menu-link">
                                <div>Jam Kerja Departemen</div>
                            </a>
                        </li>
                    @endcan
                    @can('harilibur.index')
                        <li class="menu-item {{ request()->is(['harilibur', 'harilibur/*']) ? 'active' : '' }}">
                            <a href="{{ route('harilibur.index') }}" class="menu-link">
                                <div>Hari Libur</div>
                            </a>
                        </li>
                    @endcan
                    @can('cuti.index')
                        <li class="menu-item {{ request()->is(['cuti', 'cuti/*']) ? 'active' : '' }}">
                            <a href="{{ route('cuti.index') }}" class="menu-link">
                                <div>Master Cuti</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif


        <!-- 3. MANAJEMEN PRESENSI (Operasional) -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Manajemen Presensi</span>
        </li>

        @can('presensi.index')
            <li class="menu-item {{ request()->is(['presensi', 'presensi/*']) ? 'active' : '' }}">
                <a href="{{ route('presensi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-device-desktop"></i>
                    <div>Monitoring Presensi</div>
                </a>
            </li>
        @endcan

        @can('trackingpresensi.index')
            <li class="menu-item {{ request()->is(['trackingpresensi', 'trackingpresensi/*']) ? 'active' : '' }}">
                <a href="{{ route('trackingpresensi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-map-pin"></i>
                    <div>Tracking Presensi</div>
                </a>
            </li>
        @endcan

        @can('aktivitaskaryawan.index')
            <li class="menu-item {{ request()->is(['aktivitaskaryawan', 'aktivitaskaryawan/*']) ? 'active' : '' }}">
                <a href="{{ route('aktivitaskaryawan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-activity"></i>
                    <div>Aktivitas Karyawan</div>
                </a>
            </li>
        @endcan

        @if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index', 'izindinas.index']))
            <li
                class="menu-item {{ request()->is(['izinabsen', 'izinabsen/*', 'izinsakit', 'izincuti', 'izindinas']) ? 'active' : '' }}">
                <a href="{{ route('izinabsen.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-folder-check"></i>
                    <div>Pengajuan Izin/Sakit</div>
                    @if (!empty($notifikasi_ajuan_absen))
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $notifikasi_ajuan_absen }}</div>
                    @endif
                </a>
            </li>
        @endif

        @can('lembur.index')
            <li class="menu-item {{ request()->is(['lembur', 'lembur/*']) ? 'active' : '' }}">
                <a href="{{ route('lembur.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-calendar-time"></i> {{-- Changed Icon --}}
                    <div>Lembur</div>
                    @if (!empty($notifikasi_lembur))
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $notifikasi_lembur }}</div>
                    @endif
                </a>
            </li>
        @endcan

        @if(auth()->user()->can('kunjungan.index'))
            <li
                class="menu-item {{ request()->is(['kunjungan', 'kunjungan/*', 'tracking-kunjungan', 'tracking-kunjungan/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-map"></i>
                    <div>Data Kunjungan & Tracking</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is(['kunjungan', 'kunjungan/*']) ? 'active' : '' }}">
                        <a href="{{ route('kunjungan.index') }}" class="menu-link">
                            <div>Data Kunjungan</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is(['tracking-kunjungan', 'tracking-kunjungan/*']) ? 'active' : '' }}">
                        <a href="{{ route('tracking-kunjungan.index') }}" class="menu-link">
                            <div>Tracking Kunjungan</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif


        <!-- 4. PAYROLL (Pusat Keuangan) -->
        @if (auth()->user()->hasAnyPermission(['gajipokok.index', 'jenistunjangan.index', 'tunjangan.index', 'bpjskesehatan.index', 'bpjstenagakerja.index', 'penyesuaiangaji.index', 'denda.index', 'slipgaji.index']))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Payroll</span>
            </li>

            {{-- Header: Master Komponen Gaji --}}
            <li
                class="menu-item {{ request()->is(['gajipokok', 'jenistunjangan', 'tunjangan', 'bpjskesehatan', 'bpjstenagakerja', 'denda']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-coins"></i> {{-- Changed Icon --}}
                    <div>Master Komponen Gaji</div>
                </a>
                <ul class="menu-sub">
                    @can('gajipokok.index')
                        <li class="menu-item {{ request()->is(['gajipokok', 'gajipokok/*']) ? 'active' : '' }}">
                            <a href="{{ route('gajipokok.index') }}" class="menu-link">
                                <div>Gaji Pokok</div>
                            </a>
                        </li>
                    @endcan
                    @can('jenistunjangan.index')
                        <li class="menu-item {{ request()->is(['jenistunjangan', 'jenistunjangan/*']) ? 'active' : '' }}">
                            <a href="{{ route('jenistunjangan.index') }}" class="menu-link">
                                <div>Jenis Tunjangan</div>
                            </a>
                        </li>
                    @endcan
                    @can('tunjangan.index')
                        <li class="menu-item {{ request()->is(['tunjangan', 'tunjangan/*']) ? 'active' : '' }}">
                            <a href="{{ route('tunjangan.index') }}" class="menu-link">
                                <div>Data Tunjangan</div>
                            </a>
                        </li>
                    @endcan
                    @can('bpjskesehatan.index')
                        <li class="menu-item {{ request()->is(['bpjskesehatan', 'bpjskesehatan/*']) ? 'active' : '' }}">
                            <a href="{{ route('bpjskesehatan.index') }}" class="menu-link">
                                <div>BPJS Kesehatan</div>
                            </a>
                        </li>
                    @endcan
                    @can('bpjstenagakerja.index')
                        <li class="menu-item {{ request()->is(['bpjstenagakerja', 'bpjstenagakerja/*']) ? 'active' : '' }}">
                            <a href="{{ route('bpjstenagakerja.index') }}" class="menu-link">
                                <div>BPJS Ketenagakerjaan</div>
                            </a>
                        </li>
                    @endcan
                    <!-- Denda Keterlambatan (Moved from Config) -->
                    @if (isset($general_setting) && $general_setting->denda)
                        {{-- Need to ensure $general_setting is available, typically shared via ViewComposer or similar --}}
                        <li class="menu-item {{ request()->is(['denda', 'denda/*']) ? 'active' : '' }}">
                            <a href="{{ route('denda.index') }}" class="menu-link">
                                <div>Denda Keterlambatan</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            {{-- Header: Transaksi Gaji --}}
            <li
                class="menu-item {{ request()->is(['penyesuaiangaji', 'penyesuaiangaji/*', 'slipgaji', 'slipgaji/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-file-dollar"></i> {{-- Changed Icon --}}
                    <div>Transaksi Gaji</div>
                </a>
                <ul class="menu-sub">
                    @can('penyesuaiangaji.index')
                        <li class="menu-item {{ request()->is(['penyesuaiangaji', 'penyesuaiangaji/*']) ? 'active' : '' }}">
                            <a href="{{ route('penyesuaiangaji.index') }}" class="menu-link">
                                <div>Penyesuaian Gaji</div>
                            </a>
                        </li>
                    @endcan
                    @can('slipgaji.index')
                        <li class="menu-item {{ request()->is(['slipgaji', 'slipgaji/*']) ? 'active' : '' }}">
                            <a href="{{ route('slipgaji.index') }}" class="menu-link">
                                <div>Slip Gaji</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif


        <!-- 5. LAPORAN -->
        @if (auth()->user()->hasAnyPermission(['laporan.presensi']))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Laporan</span>
            </li>
            <li class="menu-item {{ request()->is(['laporan/presensi']) ? 'active' : '' }}">
                <a href="{{ route('laporan.presensi') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-file-analytics"></i>
                    <div>Laporan Presensi & Gaji</div>
                </a>
            </li>
        @endif


        <!-- 6. PENGATURAN SISTEM -->
        @if (auth()->user()->hasAnyPermission(['generalsetting.index', 'bersihkanfoto.index']) || auth()->user()->hasRole(['super admin']))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Pengaturan Sistem</span>
            </li>

            @can('generalsetting.index')
                <li class="menu-item {{ request()->is(['generalsetting', 'generalsetting/*']) ? 'active' : '' }}">
                    <a href="{{ route('generalsetting.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-settings"></i>
                        <div>General Setting</div>
                    </a>
                </li>
            @endcan

            {{-- WA Gateway (Disabled/Commented) --}}
            {{--
            @if (auth()->user()->hasRole(['super admin']))
            <li class="menu-item {{ request()->is(['wagateway', 'wagateway/*']) ? 'active' : '' }}">
                <a href="{{ route('wagateway.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-brand-whatsapp"></i>
                    <div>WA Gateway</div>
                </a>
            </li>
            @endif
            --}}


            @can('bersihkanfoto.index')
                <li class="menu-item {{ request()->is(['bersihkanfoto', 'bersihkanfoto/*']) ? 'active' : '' }}">
                    <a href="{{ route('bersihkanfoto.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-trash"></i>
                        <div>Bersihkan Foto</div>
                    </a>
                </li>
            @endcan

            {{-- Header: Akses User --}}
            @if (auth()->user()->hasRole(['super admin']))
                <li
                    class="menu-item {{ request()->is(['roles', 'roles/*', 'permissions', 'permissions/*', 'permissiongroups', 'permissiongroups/*', 'users', 'users/*']) ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons ti ti-shield-lock"></i>
                        <div>Akses User</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->is(['users', 'users/*']) ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <div>User</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is(['roles', 'roles/*']) ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}" class="menu-link">
                                <div>Role</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is(['permissions', 'permissions/*']) ? 'active' : '' }}">
                            <a href="{{ route('permissions.index') }}" class="menu-link">
                                <div>Permission</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is(['permissiongroups', 'permissiongroups/*']) ? 'active' : '' }}">
                            <a href="{{ route('permissiongroups.index') }}" class="menu-link">
                                <div>Group Permission</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

        @endif

    </ul>
</aside>
<!-- / Menu -->