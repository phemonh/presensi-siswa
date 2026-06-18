<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kelas_id',
        'nis',
        'nisn',
    ];

    /**
     * Get the user associated with this student
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class for this student
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Get all attendance records for this student
     */
    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * Generate unique QR code string for this student
     */
    public function getQrCodeString(): string
    {
        return encrypt("{$this->id}|{$this->nis}|{$this->user_id}");
    }
}
