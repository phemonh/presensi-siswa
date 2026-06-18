<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        // Create Wali Kelas Users
        $waliKelas1 = User::create([
            'name' => 'Budi Santoso, S.Pd',
            'email' => 'guru@example.com',
            'password' => Hash::make('password'),
            'role' => 'wali_kelas',
            'nip' => '198501012010011001',
            'phone' => '081234567891',
        ]);

        $waliKelas2 = User::create([
            'name' => 'Siti Aminah, S.Pd',
            'email' => 'guru2@example.com',
            'password' => Hash::make('password'),
            'role' => 'wali_kelas',
            'nip' => '198602022011012002',
            'phone' => '081234567892',
        ]);

        // Create Classes
        $kelas1 = Kelas::create([
            'nama' => 'X-A',
            'tahun_ajaran' => '2024/2025',
            'wali_kelas_id' => $waliKelas1->id,
        ]);

        $kelas2 = Kelas::create([
            'nama' => 'XI-IPA-1',
            'tahun_ajaran' => '2024/2025',
            'wali_kelas_id' => $waliKelas2->id,
        ]);

        // Create Students
        $students = [
            ['name' => 'Ahmad Rizki', 'email' => 'siswa@example.com', 'nis' => '12345', 'nisn' => '0012345678', 'kelas' => $kelas1],
            ['name' => 'Fatimah Zahra', 'email' => 'siswa2@example.com', 'nis' => '12346', 'nisn' => '0012345679', 'kelas' => $kelas1],
            ['name' => 'Muhammad Fikri', 'email' => 'siswa3@example.com', 'nis' => '12347', 'nisn' => '0012345680', 'kelas' => $kelas1],
            ['name' => 'Nurul Hidayah', 'email' => 'siswa4@example.com', 'nis' => '12348', 'nisn' => '0012345681', 'kelas' => $kelas1],
            ['name' => 'Andi Pratama', 'email' => 'siswa5@example.com', 'nis' => '12349', 'nisn' => '0012345682', 'kelas' => $kelas1],
            ['name' => 'Dewi Sartika', 'email' => 'siswa6@example.com', 'nis' => '22345', 'nisn' => '0022345678', 'kelas' => $kelas2],
            ['name' => 'Eko Prasetyo', 'email' => 'siswa7@example.com', 'nis' => '22346', 'nisn' => '0022345679', 'kelas' => $kelas2],
            ['name' => 'Fitri Handayani', 'email' => 'siswa8@example.com', 'nis' => '22347', 'nisn' => '0022345680', 'kelas' => $kelas2],
        ];

        foreach ($students as $student) {
            $user = User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'phone' => '08' . random_int(100000000, 999999999),
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $student['kelas']->id,
                'nis' => $student['nis'],
                'nisn' => $student['nisn'],
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Wali Kelas: guru@example.com / password');
        $this->command->info('Siswa: siswa@example.com / password');
    }
}
