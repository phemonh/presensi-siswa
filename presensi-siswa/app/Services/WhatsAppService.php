<?php

namespace App\Services;

use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    /**
     * Send WhatsApp message using API (contoh: Fonnte, Twilio, dll)
     */
    public function sendMessage(string $phone, string $message): bool
    {
        // Format nomor telepon (hapus 0 di depan, ganti dengan 62)
        $phone = $this->formatPhoneNumber($phone);

        try {
            $response = Http::withHeaders([
                'Authorization' => config('services.whatsapp.api_key'),
                'Content-Type' => 'application/json',
            ])->post(config('services.whatsapp.api_url'), [
                'target' => $phone,
                'message' => $message,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send attendance notification to parent/guardian
     */
    public function sendAttendanceNotification(Siswa $siswa, Presensi $presensi): bool
    {
        $message = $this->buildAttendanceMessage($siswa, $presensi);
        
        // Kirim ke nomor orang tua/wali yang terdaftar di user
        if ($siswa->user && $siswa->user->phone) {
            return $this->sendMessage($siswa->user->phone, $message);
        }

        return false;
    }

    /**
     * Build attendance message
     */
    private function buildAttendanceMessage(Siswa $siswa, Presensi $presensi): string
    {
        $statusMap = [
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpa' => 'Alpa',
        ];

        $statusText = $statusMap[$presensi->status] ?? $presensi->status;

        $message = "*NOTIFIKASI PRESENSI SISWA*\n\n";
        $message .= "Nama: {$siswa->user->name}\n";
        $message .= "Kelas: {$siswa->kelas->nama}\n";
        $message .= "Tanggal: " . $presensi->tanggal->format('d F Y') . "\n";
        $message .= "Status: *{$statusText}*\n";
        
        if ($presensi->jam_masuk) {
            $message .= "Jam Masuk: " . date('H:i', strtotime($presensi->jam_masuk)) . "\n";
        }
        
        if ($presensi->keterangan) {
            $message .= "Keterangan: {$presensi->keterangan}\n";
        }

        $message .= "\nTerima kasih.";

        return $message;
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Hapus karakter non-numeric
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ganti 0 di depan dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
