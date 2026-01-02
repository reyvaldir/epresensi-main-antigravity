@extends('layouts.mobile_modern')

@section('content')
    <style>
        /* Legacy Slip Styles */
        .slip-struk {
            width: 100%;
            background: white;
            border: 1px solid #333;
            border-radius: 3px;
            padding: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            page-break-inside: avoid;
            margin-bottom: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #1e293b;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #333;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .company-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .slip-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .periode {
            font-size: 12px;
            color: #666;
        }

        .employee-section {
            margin-bottom: 8px;
            border-bottom: 1px dotted #666;
            padding-bottom: 6px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 14px;
        }

        .label {
            font-weight: bold;
        }

        .value {
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            margin: 8px 0 4px 0;
            padding: 2px;
            background: #f0f0f0;
            border: 1px solid #ddd;
        }

        .earning {
            background: #e8f5e8;
            border-color: #28a745;
        }

        .deduction {
            background: #fde8e8;
            border-color: #dc3545;
        }

        .adjustment {
            background: #e8f4f8;
            border-color: #17a2b8;
        }

        .total-section {
            margin-top: 8px;
            border-top: 2px solid #333;
            padding-top: 6px;
        }

        .net-salary {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 16px;
            padding: 4px;
            background: #f8f9fa;
            border: 1px solid #333;
        }

        .work-info {
            font-size: 12px;
            color: #666;
            text-align: center;
            margin: 6px 0;
            border-top: 1px dotted #666;
            padding-top: 4px;
        }

        .currency {
            font-family: 'Courier New', monospace;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #888;
            margin-top: 8px;
            border-top: 1px dashed #333;
            padding-top: 6px;
        }

        @media print {
            @page {
                margin: 0;
                size: auto;
            }

            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }

            /* Hide Bottom Navigation from Layout */
            nav.fixed.bottom-0 {
                display: none !important;
            }

            .no-print {
                display: none !important;
            }

            .slip-struk {
                box-shadow: none;
                border: 1px solid #000;
                margin: 0;
                width: 100%;
                max-width: 100%;
                page-break-after: always;
            }

            /* Resets for print */
            #content-section {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>

    <!-- Header (Modern) -->
    <div class="flex items-center justify-between mb-5 mt-2 no-print">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Cetak Slip Gaji</h1>
        </div>

    </div>

    <!-- Content (Standard Padding) -->
    <div class="pb-10">
        @foreach ($laporan_presensi as $d)
            @php
                $tanggal_presensi = $periode_dari;
                $total_denda = 0;
                $total_potongan_jam = 0;
                $total_tunjangan = 0;
                $total_jam_lembur = 0;

                // Kalkulasi tunjangan
                foreach ($jenis_tunjangan as $j) {
                    $total_tunjangan += $d[$j->kode_jenis_tunjangan];
                }

                $bruto = $d['gaji_pokok'] + $total_tunjangan;

                // Kalkulasi upah per jam
                $upah_perjam = $d['gaji_pokok'] / $generalsetting->total_jam_bulan;

                while (strtotime($tanggal_presensi) <= strtotime($periode_sampai)) {
                    $denda = 0;
                    $potongan_jam = 0;
                    $search = [
                        'nik' => $d['nik'],
                        'tanggal' => $tanggal_presensi,
                    ];
                    $ceklembur = ceklembur($datalembur, $search); // Asumsi helper available
                    $lembur = hitungLembur($ceklembur);
                    if (!empty($ceklembur)) {
                        $jml_jam_lembur = $lembur;
                    } else {
                        $jml_jam_lembur = 0;
                    }

                    if (isset($d[$tanggal_presensi])) {
                        if ($d[$tanggal_presensi]['status'] == 'h') {
                            $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                            $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);

                            if ($terlambat != null) {
                                if ($terlambat['desimal_terlambat'] < 1) {
                                    $potongan_jam_terlambat = 0;
                                    $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                                } else {
                                    $potongan_jam_terlambat =
                                        $terlambat['desimal_terlambat'] > $d[$tanggal_presensi]['total_jam']
                                        ? $d[$tanggal_presensi]['total_jam']
                                        : $terlambat['desimal_terlambat'];
                                    $denda = 0;
                                }
                            } else {
                                $potongan_jam_terlambat = 0;
                                $denda = 0;
                            }

                            $pulangcepat = hitungpulangcepat(
                                $tanggal_presensi,
                                $d[$tanggal_presensi]['jam_out'],
                                $d[$tanggal_presensi]['jam_pulang'],
                                $d[$tanggal_presensi]['istirahat'],
                                $d[$tanggal_presensi]['jam_awal_istirahat'],
                                $d[$tanggal_presensi]['jam_akhir_istirahat'],
                                $d[$tanggal_presensi]['lintashari'],
                            );
                            $pulangcepat = $pulangcepat > $d[$tanggal_presensi]['total_jam'] ? $d[$tanggal_presensi]['total_jam'] : $pulangcepat;
                            $potongan_tidak_absen_masuk_atau_pulang =
                                empty($d[$tanggal_presensi]['jam_out']) || empty($d[$tanggal_presensi]['jam_in'])
                                ? $d[$tanggal_presensi]['total_jam']
                                : 0;
                            $potongan_jam =
                                $potongan_tidak_absen_masuk_atau_pulang == 0
                                ? $pulangcepat + $potongan_jam_terlambat
                                : $potongan_tidak_absen_masuk_atau_pulang;
                        } elseif ($d[$tanggal_presensi]['status'] == 'i') {
                            $potongan_jam = $d[$tanggal_presensi]['total_jam'];
                        } elseif ($d[$tanggal_presensi]['status'] == 'a') {
                            $potongan_jam = $d[$tanggal_presensi]['total_jam'];
                        }
                    }

                    $total_denda += $denda;
                    $total_potongan_jam += $potongan_jam;
                    $total_jam_lembur += $jml_jam_lembur;
                    $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                }

                $jumlah_potongan_jam = ROUND($upah_perjam) * $total_potongan_jam;
                $total_potongan = ROUND($jumlah_potongan_jam) + $total_denda + $d['bpjs_kesehatan'] + $d['bpjs_tenagakerja'];
                // $gaji_bersih = $d['gaji_pokok'] + $total_tunjangan - $total_potongan + $d['penambah'] - $d['pengurang'];
            @endphp

            <div class="slip-struk">
                <!-- Header -->
                <div class="header">
                    <div class="company-name">{{ $generalsetting->nama_perusahaan }}</div>
                    <div class="slip-title">SLIP GAJI</div>
                    <div class="periode">{{ date('d/m/Y', strtotime($periode_dari)) }} -
                        {{ date('d/m/Y', strtotime($periode_sampai)) }}
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="employee-section">
                    <div class="row">
                        <span class="label">NIK:</span>
                        <span class="value">{{ $d['nik'] }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Nama:</span>
                        <span class="value">{{ $d['nama_karyawan'] }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Jabatan:</span>
                        <span class="value">{{ $d['nama_jabatan'] }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Dept:</span>
                        <span class="value">{{ $d['kode_dept'] }}</span>
                    </div>
                </div>

                <!-- Work Summary -->
                <div class="work-info">
                    {{ $generalsetting->total_jam_bulan }} jam | Rp {{ number_format($upah_perjam, 0, ',', '.') }}/jam |
                    {{ number_format($total_potongan_jam, 1) }} jam potong
                </div>

                <!-- Penghasilan -->
                <div class="section-title earning">PENGHASILAN</div>
                <div class="row">
                    <span>Gaji Pokok</span>
                    <span class="currency">{{ number_format($d['gaji_pokok'], 0, ',', '.') }}</span>
                </div>
                @foreach ($jenis_tunjangan as $j)
                    @if ($d[$j->kode_jenis_tunjangan] > 0)
                        <div class="row">
                            <span>{{ $j->jenis_tunjangan }}</span>
                            <span class="currency">{{ number_format($d[$j->kode_jenis_tunjangan], 0, ',', '.') }}</span>
                        </div>
                    @endif
                @endforeach
                @if ($total_jam_lembur > 0)
                    <div class="row">
                        <span>Lembur {{ formatAngkaDesimal($total_jam_lembur) }} jam </span>
                        <span class="currency">
                            @php
                                $upah_lembur = ROUND($upah_perjam) * ROUND($total_jam_lembur, 2);
                            @endphp
                            {{ formatAngka($upah_lembur) }}
                        </span>
                    </div>
                @else
                    @php
                        $upah_lembur = 0;
                    @endphp
                @endif
                <div class="row" style="font-weight: bold; border-top: 1px dotted #333; padding-top: 2px;">
                    <span>Sub Total</span>
                    @php
                        $bruto_total = $bruto + ROUND($upah_lembur);
                    @endphp
                    <span class="currency">{{ number_format($bruto_total, 0, ',', '.') }}</span>
                </div>

                <!-- Potongan -->
                <div class="section-title deduction">POTONGAN</div>
                @if ($total_denda > 0)
                    <div class="row">
                        <span>Denda</span>
                        <span class="currency">{{ number_format($total_denda, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($jumlah_potongan_jam > 0)
                    <div class="row">
                        <span>Pot. Jam ({{ number_format($total_potongan_jam, 2) }})</span>
                        <span class="currency">{{ number_format($jumlah_potongan_jam, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($d['bpjs_kesehatan'] > 0)
                    <div class="row">
                        <span>BPJS Kes</span>
                        <span class="currency">{{ number_format($d['bpjs_kesehatan'], 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($d['bpjs_tenagakerja'] > 0)
                    <div class="row">
                        <span>BPJS TK</span>
                        <span class="currency">{{ number_format($d['bpjs_tenagakerja'], 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="row" style="font-weight: bold; border-top: 1px dotted #333; padding-top: 2px;">
                    <span>Sub Total</span>
                    <span class="currency">{{ number_format($total_potongan, 0, ',', '.') }}</span>
                </div>

                <!-- Penyesuaian -->
                @if ($d['penambah'] > 0 || $d['pengurang'] > 0)
                    <div class="section-title adjustment">PENYESUAIAN</div>
                    @if ($d['penambah'] > 0)
                        <div class="row">
                            <span>Penambah</span>
                            <span class="currency">{{ number_format($d['penambah'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($d['pengurang'] > 0)
                        <div class="row">
                            <span>Pengurang</span>
                            <span class="currency">{{ number_format($d['pengurang'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                @endif

                <!-- Total -->
                <div class="total-section">
                    <div class="net-salary">
                        <span>GAJI BERSIH</span>
                        @php
                            $gaji_bersih =
                                $d['gaji_pokok'] + $total_tunjangan - $total_potongan + $d['penambah'] - $d['pengurang'] + ROUND($upah_lembur);
                        @endphp
                        <span class="currency">{{ number_format($gaji_bersih, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="footer">
                    Dicetak: {{ date('d/m/Y H:i') }}<br>
                    Sistem Payroll v1.0
                </div>
            </div>

            <!-- Print Button (Outside Slip) -->
            <button onclick="window.print()"
                class="w-full bg-primary text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 mt-6 active:scale-95 transition-transform hover:bg-blue-700 no-print max-w-[800px] mx-auto">
                <ion-icon name="print-outline" class="text-xl"></ion-icon>
                <span class="text-base">Cetak Slip Gaji</span>
            </button>
        @endforeach
    </div>
@endsection