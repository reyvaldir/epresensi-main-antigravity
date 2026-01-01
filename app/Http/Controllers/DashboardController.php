<?php

namespace App\Http\Controllers;

use App\Charts\JeniskelaminkaryawanChart;
use App\Charts\PendidikankaryawanChart;
use App\Charts\StatusKaryawanChart;
use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\Presensi;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Models\Izindinas;
use App\Jobs\SendWaMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class DashboardController extends Controller
{
    public function index(StatusKaryawanChart $chart, JeniskelaminkaryawanChart $jkchart, PendidikankaryawanChart $pddchart, Request $request)
    {
        $agent = new Agent();
        $user = User::where('id', auth()->user()->id)->first();
        $hari_ini = date("Y-m-d");
        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
            $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)
                ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->first();

            $data['presensi'] = Presensi::where('presensi.nik', $userkaryawan->nik)->where('presensi.tanggal', $hari_ini)->first();
            $data['datapresensi'] = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('presensi.nik', $userkaryawan->nik)
                ->leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
                ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')

                ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
                ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')

                ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
                ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
                // Join Master Cuti for Name
                ->leftJoin('cuti', 'presensi_izincuti.kode_cuti', '=', 'cuti.kode_cuti')
                ->select(
                    'presensi.*',
                    'presensi_jamkerja.nama_jam_kerja',
                    'presensi_jamkerja.jam_masuk',
                    'presensi_jamkerja.jam_pulang',
                    'presensi_jamkerja.total_jam',
                    'presensi_jamkerja.lintashari',
                    'presensi_izinabsen.keterangan as keterangan_izin',
                    'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                    'presensi_izincuti.keterangan as keterangan_izin_cuti',

                    // Extra fields for Detail Modal
                    'presensi_izinabsen.dari as izin_dari',
                    'presensi_izinabsen.sampai as izin_sampai',

                    'presensi_izinsakit.dari as sakit_dari',
                    'presensi_izinsakit.sampai as sakit_sampai',
                    'presensi_izinsakit.doc_sid as sakit_sid',

                    'presensi_izincuti.dari as cuti_dari',
                    'presensi_izincuti.sampai as cuti_sampai',
                    'cuti.jenis_cuti as nama_cuti'
                )
                ->orderBy('tanggal', 'desc')
                ->limit(30)
                ->get();

            // Add Izin Dinas
            $izindinas = Izindinas::where('nik', $userkaryawan->nik)
                ->where('status', 1)
                ->get();

            foreach ($izindinas as $d) {
                $start = Carbon::parse($d->dari);
                $end = Carbon::parse($d->sampai);

                while ($start->lte($end)) {
                    $tgl = $start->format('Y-m-d');

                    // Check duplicate
                    if ($data['datapresensi']->where('tanggal', $tgl)->count() == 0) {
                        $obj = new \stdClass();
                        $obj->tanggal = $tgl;
                        $obj->status = 'd';
                        $obj->nama_jam_kerja = 'Dinas Luar';
                        $obj->jam_in = null;
                        $obj->jam_out = null;
                        $obj->jam_masuk = null;
                        $obj->jam_pulang = null;
                        $obj->keterangan_izin = null;
                        $obj->keterangan_izin_sakit = null;
                        $obj->keterangan_izin_cuti = null;
                        $obj->keterangan_izin_dinas = $d->keterangan;
                        $obj->foto_in = null;
                        $obj->foto_out = null;
                        $obj->lokasi_in = null;
                        $obj->lokasi_out = null;

                        // Extra fields for Dinas
                        $obj->izin_dari = null;
                        $obj->izin_sampai = null;
                        $obj->sakit_dari = null;
                        $obj->sakit_sampai = null;
                        $obj->sakit_sid = null;
                        $obj->cuti_dari = null;
                        $obj->cuti_sampai = null;
                        $obj->nama_cuti = null;

                        // Dinas Date Range
                        $obj->dinas_dari = $d->dari;
                        $obj->dinas_sampai = $d->sampai;

                        $data['datapresensi']->push($obj);
                    }
                    $start->addDay();
                }
            }

            // Sort by tanggal desc
            $data['datapresensi'] = $data['datapresensi']->sortByDesc('tanggal')->values()->take(30);
            $data['rekappresensi'] = Presensi::select(
                DB::raw("SUM(IF(status='h',1,0)) as hadir"),
                DB::raw("SUM(IF(status='i',1,0)) as izin"),
                DB::raw("SUM(IF(status='s',1,0)) as sakit"),
                DB::raw("SUM(IF(status='a',1,0)) as alpa"),
                DB::raw("SUM(IF(status='c',1,0)) as cuti")
            )
                ->groupBy('presensi.nik')
                ->whereRaw('MONTH(presensi.tanggal) = MONTH(?)', [$hari_ini])
                ->whereRaw('YEAR(presensi.tanggal) = YEAR(?)', [$hari_ini])
                ->where('presensi.nik', $userkaryawan->nik)
                ->first();

            $data['lembur'] = Lembur::where('nik', $userkaryawan->nik)->where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();
            $data['notiflembur'] = Lembur::where('nik', $userkaryawan->nik)
                ->where('status', 1)
                ->where('lembur_in', null)
                ->orWhere('lembur_out', null)
                ->where('status', 1)
                ->count();

            // Cek apakah hari ini adalah ulang tahun karyawan
            $isBirthday = false;
            $umur = null;
            if ($data['karyawan'] && $data['karyawan']->tanggal_lahir) {
                $tanggalLahir = Carbon::parse($data['karyawan']->tanggal_lahir);
                $today = Carbon::now();
                if ($tanggalLahir->month == $today->month && $tanggalLahir->day == $today->day) {
                    $isBirthday = true;
                    $umur = $tanggalLahir->age;
                }
            }
            $data['is_birthday'] = $isBirthday;
            $data['umur'] = $umur;

            return view('dashboard.karyawan', $data);
        } else {

            //Dashboard Admin
            $sk = new Karyawan();
            $data['status_karyawan'] = $sk->getRekapstatuskaryawan($request);
            $data['chart'] = $chart->build($request);
            $data['jkchart'] = $jkchart->build($request);
            $data['pddchart'] = $pddchart->build($request);

            $queryPresensi = Presensi::query();
            $queryPresensi->join('karyawan', 'presensi.nik', '=', 'karyawan.nik');
            $queryPresensi->select(
                DB::raw("SUM(IF(status='h',1,0)) as hadir"),
                DB::raw("SUM(IF(status='i',1,0)) as izin"),
                DB::raw("SUM(IF(status='s',1,0)) as sakit"),
                DB::raw("SUM(IF(status='a',1,0)) as alpa"),
                DB::raw("SUM(IF(status='c',1,0)) as cuti")
            );
            if (!empty($request->tanggal)) {
                $queryPresensi->where('tanggal', $request->tanggal);
            } else {
                $queryPresensi->where('tanggal', date('Y-m-d'));
            }

            if (!empty($request->kode_cabang)) {
                $queryPresensi->where('karyawan.kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->kode_dept)) {
                $queryPresensi->where('karyawan.kode_dept', $request->kode_dept);
            }
            $data['rekappresensi'] = $queryPresensi->first();
            $data['departemen'] = Departemen::orderBy('kode_dept')->get();
            $data['cabang'] = Cabang::orderBy('kode_cabang')->get();
            $data['birthday'] = Karyawan::whereMonth('tanggal_lahir', date('m'))->whereDay('tanggal_lahir', date('d'))
                ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->select(
                    'karyawan.*',
                    'jabatan.nama_jabatan',
                    'departemen.nama_dept',
                    'cabang.nama_cabang',
                    'karyawan.status_karyawan'
                )
                ->when($request->kode_cabang, function ($query) use ($request) {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                })
                ->when($request->kode_dept, function ($query) use ($request) {
                    $query->where('karyawan.kode_dept', $request->kode_dept);
                })
                ->orderBy('tanggal_lahir', 'asc')->get();
            // dd($data['rekappresensi']);
            return view('dashboard.dashboard', $data);
        }
    }

    public function kirimUcapanBirthday(Request $request)
    {
        try {
            // Ambil karyawan yang ulang tahun hari ini
            $birthday = Karyawan::whereMonth('tanggal_lahir', date('m'))
                ->whereDay('tanggal_lahir', date('d'))
                ->when($request->kode_cabang, function ($query) use ($request) {
                    $query->where('kode_cabang', $request->kode_cabang);
                })
                ->when($request->kode_dept, function ($query) use ($request) {
                    $query->where('kode_dept', $request->kode_dept);
                })
                ->whereNotNull('no_hp')
                ->where('no_hp', '!=', '')
                ->get();

            if ($birthday->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada karyawan yang ulang tahun hari ini atau tidak ada nomor HP yang tersedia.'
                ], 400);
            }

            $count = 0;
            foreach ($birthday as $karyawan) {
                // Hitung umur
                $umur = Carbon::parse($karyawan->tanggal_lahir)->age;

                // Format pesan ucapan ulang tahun
                $message = "🎉 *Selamat Ulang Tahun!* 🎂\n\n";
                $message .= "Halo *{$karyawan->nama_karyawan}*,\n\n";
                $message .= "Di hari yang istimewa ini, kami ingin mengucapkan:\n\n";
                $message .= "🎂 *Selamat Ulang Tahun yang ke-{$umur}!* 🎂\n\n";
                $message .= "Semoga di hari ulang tahunmu ini:\n";
                $message .= "✨ Panjang umur\n";
                $message .= "✨ Sehat selalu\n";
                $message .= "✨ Bahagia selalu\n";
                $message .= "✨ Sukses dalam karir\n";
                $message .= "✨ Diberkahi rezeki yang berlimpah\n\n";
                $message .= "Terima kasih atas dedikasi dan kontribusinya selama ini. Semoga hubungan kerja kita terus berjalan dengan baik!\n\n";
                $message .= "*Salam Hangat,*\nTim HR";

                // Format nomor HP (hapus 0 di depan jika ada, pastikan format 62xxx)
                $phoneNumber = $karyawan->no_hp;
                $phoneNumber = preg_replace('/^0+/', '', $phoneNumber);
                if (!str_starts_with($phoneNumber, '62')) {
                    $phoneNumber = '62' . $phoneNumber;
                }

                // Dispatch job untuk mengirim WhatsApp
                SendWaMessage::dispatch($phoneNumber, $message, true);
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Ucapan ulang tahun sedang dikirim ke {$count} karyawan."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
