# Rangkuman Data Seeder (Dummy Data)

Dokumen ini adalah ringkasan dari data simulasi yang telah di-generate untuk keperluan Demo Skripsi.

## 1. Profil Karyawan (30 Orang)
*   **Total**: 30 Akun (NIK `24010001` - `24010030`).
*   **Password**: `12345` (Semua akun).
*   **Format Nama**: Bersih (Tanpa kode departemen, contoh: "Budi Santoso").
*   **Distribusi Tim**:
    *   **Admin Pusat (6 Orang)**: Lokasi KRJ. 1 Head & 5 Staff.
    *   **Leadership (6 Orang)**: Tersebar di KRJ, CRB, SPG. (Kepala OPL & Kepala Toko).
    *   **Lapangan (18 Orang)**: Staff OPL & OTK tersebar merata.

## 2. Transaksi & Presensi (Nov 2025 - Jan 2026)
*   **Tingkat Kehadiran**: 92% Hadir, 8% Sakit/Izin/Cuti.
*   **Validasi Lokasi**: Presensi dibuat dalam radius **< 70 meter** dari cabang (Anti-Fraud valid).
*   **Akhir Pekan**: Sabtu/Minggu Libur (Kecuali jika kena random lembur).
*   **Status Approval**:
    *   **Nov 1 - Jan 19**: 80% Disetujui (1), 20% Ditolak (2).
    *   **Jan 20 onwards**: Status `Pending` (0) -> **Untuk Demo Approval Kepala Unit.**
    *   **Bukti Absen Lembur**: Lembur yang **Disetujui** otomatis memiliki data `Jam Masuk` & `Jam Pulang` (simulasi karyawan absen saat lembur).
*   **Variasi Waktu**:
    *   **Telat**: 10% Chance (range 8-12%).
    *   **Pulang Cepat**: 5% Chance (16:00 - 16:59).

## 3. Fitur Spesifik Departemen
*   **OPL (Lapangan)**: Memiliki data **Kunjungan (Visit)** dengan lokasi random (1-3 KM) & foto lokasi.
*   **OTK (Toko)**: Memiliki data **Aktivitas** di dalam toko.

## 4. Penggajian (Payroll)
*   **Gaji Pokok**: Rp 2.3jt - 3.5jt (Berdasarkan Jabatan).
*   **Tunjangan Jabatan**:
    *   **Head**: Rp 1.000.000
    *   **Staff**: Rp 500.000
*   **Tunjangan Tanggung Jawab**:
    *   **Head**: Rp 750.000
    *   **Staff**: Rp 250.000
*   **BPJS**: Kesehatan (1%) & Ketenagakerjaan (2%).

## 5. Akun Demo Rekomendasi
| Peran | NIK | Password | Kegunaan Demo |
| :--- | :--- | :--- | :--- |
| **HRD / Pusat** | `24010001` | `12345` | Cek Laporan Gaji & Dashboard Pusat. |
| **Kepala OPL** | `24010007` | `12345` | Demo Approval Izin/Lembur (Januari). |
| **Staff Field** | `24010015` | `12345` | Cek History Kunjungan di Peta. |
