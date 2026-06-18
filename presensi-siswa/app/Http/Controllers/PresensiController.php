<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Siswa;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftware\QrCode\Facades\QrCode;

class PresensiController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Show QR code for attendance
     */
    public function showQRCode()
    {
        // Generate QR code yang berisi URL untuk presensi
        // QR code ini bisa dipajang di sekolah
        $qrData = route('presensi.scan');
        
        $qrCode = QrCode::size(300)->generate($qrData);

        return view('siswa.qr-scan', compact('qrCode'));
    }

    /**
     * Process QR code scan and record attendance
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            // Decode QR data (format: encrypted student_id|nis|user_id)
            $decoded = decrypt($request->qr_data);
            $parts = explode('|', $decoded);

            if (count($parts) !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid',
                ], 400);
            }

            $siswaId = $parts[0];
            
            $siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);

            // Cek apakah sudah presensi hari ini
            $today = now()->format('Y-m-d');
            $existingPresensi = Presensi::where('siswa_id', $siswaId)
                ->whereDate('tanggal', $today)
                ->first();

            if ($existingPresensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi hari ini',
                    'data' => $existingPresensi,
                ], 400);
            }

            // Tentukan status (terlambat jika setelah jam 7:00)
            $currentTime = now();
            $status = $currentTime->hour >= 7 && $currentTime->minute > 0 ? 'terlambat' : 'hadir';

            // Create presensi record
            $presensi = Presensi::create([
                'siswa_id' => $siswaId,
                'tanggal' => $today,
                'jam_masuk' => $currentTime->format('H:i:s'),
                'status' => $status,
                'lokasi' => $request->input('location'), // opsional dari GPS
            ]);

            // Send WhatsApp notification
            $this->whatsappService->sendAttendanceNotification($siswa, $presensi);

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil dicatat',
                'data' => [
                    'siswa' => $siswa->user->name,
                    'kelas' => $siswa->kelas->nama,
                    'status' => $status,
                    'jam_masuk' => $presensi->jam_masuk,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Student's attendance history
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $presensis = Presensi::where('siswa_id', $siswa->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Calculate statistics
        $totalHari = $presensis->count();
        $hadir = $presensis->whereIn('status', ['hadir', 'terlambat'])->count();
        $persentase = $totalHari > 0 ? ($hadir / $totalHari) * 100 : 0;

        return view('siswa.history', compact('presensis', 'bulan', 'tahun', 'persentase', 'totalHari', 'hadir'));
    }
}
