<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tahun_ajaran',
        'wali_kelas_id',
    ];

    /**
     * Get the wali kelas for this class
     */
    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    /**
     * Get all students in this class
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    /**
     * Get all attendance records for this class
     */
    public function presensis()
    {
        return $this->hasManyThrough(Presensi::class, Siswa::class);
    }
}
