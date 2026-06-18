# Sistem Presensi Siswa dengan Laravel 11 & Tailwind CSS

Sistem presensi siswa berbasis web dengan fitur QR Code dan notifikasi WhatsApp.

## Fitur Utama

### 1. **Multi-User Role**
- **Admin**: Mengelola seluruh sistem (siswa, guru, kelas, laporan)
- **Wali Kelas**: Memantau presensi kelas yang diwalinya
- **Siswa**: Melihat riwayat dan progres presensi pribadi

### 2. **Presensi QR Code**
- Siswa melakukan presensi dengan scan QR Code
- QR Code unik untuk setiap siswa
- Deteksi otomatis keterlambatan

### 3. **Notifikasi WhatsApp**
- Notifikasi otomatis ke orang tua/wali saat siswa presensi
- Menggunakan API WhatsApp (Fonnte/Twilio/dll)

### 4. **Dashboard & Laporan**
- Statistik real-time untuk admin dan wali kelas
- Laporan presensi per bulan/tahun
- Export data presensi

## Teknologi

- **Backend**: Laravel 11
- **Frontend**: Tailwind CSS (via CDN)
- **Database**: MySQL
- **QR Code**: SimpleSoftware QRCode
- **HTTP Client**: Guzzle

## Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (opsional)

### Langkah Instalasi

1. **Clone atau copy project ke folder Anda**
```bash
cd /workspace/presensi-siswa
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfigurasi database di file `.env`**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensi_siswa
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Konfigurasi WhatsApp API (opsional)**
```
WHATSAPP_API_URL=https://api.fonnte.com/send
WHATSAPP_API_KEY=your_api_key
```

6. **Jalankan migrasi dan seeder**
```bash
php artisan migrate --seed
```

7. **Jalankan development server**
```bash
php artisan serve
```

8. **Akses aplikasi**
```
http://localhost:8000
```

## Default Login Credentials

Setelah menjalankan seeder, gunakan kredensial berikut:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Wali Kelas | guru@example.com | password |
| Siswa | siswa@example.com | password |

## Struktur Database

### Tables
- `users` - Data pengguna (admin, guru, siswa)
- `kelas` - Data kelas dan wali kelas
- `siswas` - Profil siswa
- `presensis` - Record presensi harian
- `sessions` - Session management

## Cara Penggunaan

### Untuk Admin
1. Login dengan akun admin
2. Kelola data siswa, guru, dan kelas
3. Lihat laporan presensi seluruh sekolah
4. Export laporan bulanan

### Untuk Wali Kelas
1. Login dengan akun guru
2. Lihat dashboard kelas yang diwali
3. Pantau kehadiran siswa hari ini
4. Lihat statistik bulanan

### Untuk Siswa
1. Login dengan akun siswa
2. Scan QR Code di sekolah untuk presensi
3. Lihat riwayat presensi pribadi
4. Monitor persentase kehadiran

## Customization

### Menambah WhatsApp Provider
Edit file `app/Services/WhatsAppService.php` untuk menyesuaikan dengan provider WhatsApp API pilihan Anda.

### Mengatur Jam Masuk
Edit logika di `app/Http/Controllers/PresensiController.php` pada method `processScan`:
```php
$status = $currentTime->hour >= 7 && $currentTime->minute > 0 ? 'terlambat' : 'hadir';
```

### Styling
Aplikasi menggunakan Tailwind CSS via CDN. Untuk production, disarankan build assets dengan:
```bash
npm install
npm run build
```

## Security Notes

- QR Code dienkripsi menggunakan Laravel encryption
- Role-based middleware untuk proteksi route
- Password di-hash menggunakan bcrypt
- CSRF protection aktif

## License

MIT License

## Support

Untuk pertanyaan atau issue, silakan hubungi developer.
