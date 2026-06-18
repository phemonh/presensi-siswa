<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaliKelasController extends Controller
{
    /**
     * Dashboard for wali kelas - view their class attendance
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get kelas yang diwali
        $kelas = Kelas::where('wali_kelas_id', $user->id)->first();

        if (!$kelas) {
            abort(403, 'Anda bukan wali kelas dari kelas manapun');
        }

        $today = now()->format('Y-m-d');
        
        // Get all students in the class
        $siswas = Siswa::with(['user', 'presensis' => function($q) use ($today) {
            $q->whereDate('tanggal', $today);
        }])->where('kelas_id', $kelas->id)->get();

        // Statistics for today
        $totalSiswa = $siswas->count();
        $hadirHariIni = $siswas->filter(fn($s) => $s->presensis->isNotEmpty())->count();
        $belumHadir = $totalSiswa - $hadirHariIni;

        // Monthly statistics
        $bulan = now()->month;
        $tahun = now()->year;
        
        $monthlyStats = DB::table('presensis')
            ->join('siswas', 'presensis.siswa_id', '=', 'siswas.id')
            ->where('siswas.kelas_id', $kelas->id)
            ->whereYear('presensis.tanggal', $tahun)
            ->whereMonth('presensis.tanggal', $bulan)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN presensis.status IN ('hadir', 'terlambat') THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN presensis.status = 'izin' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN presensis.status = 'sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN presensis.status = 'alpa' THEN 1 ELSE 0 END) as alpa")
            )
            ->first();

        return view('wali.dashboard', compact(
            'kelas', 
            'siswas', 
            'totalSiswa', 
            'hadirHariIni', 
            'belumHadir',
            'monthlyStats',
            'bulan',
            'tahun'
        ));
    }

    /**
     * View detailed attendance for a specific date
     */
    public function detail(Request $request)
    {
        $user = auth()->user();
        $kelas = Kelas::where('wali_kelas_id', $user->id)->first();

        if (!$kelas) {
            abort(403, 'Anda bukan wali kelas dari kelas manapun');
        }

        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

        $presensis = Presensi::with(['siswa.user'])
            ->whereHas('siswa', function($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            })
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_masuk')
            ->get();

        return view('wali.detail', compact('kelas', 'presensis', 'tanggal'));
    }

    /**
     * Export attendance report
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $kelas = Kelas::where('wali_kelas_id', $user->id)->first();

        if (!$kelas) {
            abort(403, 'Anda bukan wali kelas dari kelas manapun');
        }

        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $presensis = Presensi::with(['siswa.user'])
            ->whereHas('siswa', function($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            })
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get();

        // Return view for export (could be CSV, PDF, or Excel)
        return view('wali.export', compact('kelas', 'presensis', 'bulan', 'tahun'));
    }
}
