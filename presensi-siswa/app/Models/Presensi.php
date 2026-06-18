<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status', // hadir, terlambat, izin, sakit, alpa
        'keterangan',
        'lokasi',
    ];

    /**
     * Get the student for this attendance
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Scope to filter by date
     */
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope to filter by month and year
     */
    public function scopeBulan($query, $bulan, $tahun)
    {
        return $query->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan);
    }

    /**
     * Check if attendance is late
     */
    public function isTerlambat(): bool
    {
        return $this->status === 'terlambat';
    }

    /**
     * Calculate attendance percentage for a student in a month
     */
    public static function getAttendancePercentage($siswaId, $bulan, $tahun): float
    {
        $totalHariSekolah = self::where('siswa_id', $siswaId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->count();

        if ($totalHariSekolah === 0) {
            return 0;
        }

        $hadir = self::where('siswa_id', $siswaId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        return ($hadir / $totalHariSekolah) * 100;
    }
}
