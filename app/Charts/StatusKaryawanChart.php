<?php

namespace App\Charts;

use App\Models\Karyawan;
use marineusde\LarapexCharts\Charts\PieChart;
use Illuminate\Support\Facades\DB;

class StatusKaryawanChart
{
    public function build($request = null): PieChart
    {
        // Ambil jumlah karyawan berdasarkan status (T, K, O)

        $query = Karyawan::query();
        $query->select('status_karyawan', DB::raw('count(*) as total'));
        $query->groupBy('status_karyawan');
        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        $rawData = $query->pluck('total', 'status_karyawan')->toArray();

        // Mapping status singkatan ke nama lengkap
        $statusLabels = [
            'T' => 'Tetap',
            'K' => 'Kontrak',
            'O' => 'Outsourcing'
        ];

        // Konversi kode status ke label lengkap
        $labels = [];
        $data = [];

        foreach ($statusLabels as $key => $label) {
            $labels[] = $label;
            $data[] = (int) ($rawData[$key] ?? 0); // Jika tidak ada data, set 0
        }

        return (new PieChart)
            ->addData($data)
            ->setLabels($labels)
            ->setColors(['#FF6384', '#36A2EB', '#FFCE56'])
            ->setDataLabels(true);
    }
}
