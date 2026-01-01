<?php

namespace App\Http\Controllers;

use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izindinas;
use App\Models\Izinsakit;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanizinController extends Controller
{
    public function index()
    {
        $user = User::where('id', auth()->user()->id)->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        $izinabsen = Izinabsen::where('nik', $userkaryawan->nik)
            ->select('kode_izin as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'i\' as ket'), 'status as status_izin', DB::raw('NULL as doc_sid'), DB::raw('NULL as nama_cuti'));

        $izinsakit = Izinsakit::where('nik', $userkaryawan->nik)
            ->select('kode_izin_sakit as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'s\' as ket'), 'status as status_izin', 'doc_sid', DB::raw('NULL as nama_cuti'));

        $izincuti = Izincuti::where('presensi_izincuti.nik', $userkaryawan->nik)
            ->join('cuti', 'presensi_izincuti.kode_cuti', '=', 'cuti.kode_cuti')
            ->select('kode_izin_cuti as kode', 'presensi_izincuti.tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'c\' as ket'), 'status as status_izin', DB::raw('NULL as doc_sid'), 'cuti.jenis_cuti as nama_cuti');

        $izin_dinas = Izindinas::where('nik', $userkaryawan->nik)
            ->select('kode_izin_dinas as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'d\' as ket'), 'status as status_izin', DB::raw('NULL as doc_sid'), DB::raw('NULL as nama_cuti'));

        $pengajuan_izin = $izinabsen->union($izinsakit)->union($izincuti)->union($izin_dinas)->orderBy('tanggal', 'desc')->get();
        $data['pengajuan_izin'] = $pengajuan_izin;
        return view('pengajuanizin.index', $data);
    }
}
