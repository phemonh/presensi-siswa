@extends('layouts.app')

@section('title', 'Riwayat Presensi Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Riwayat Presensi Saya</h1>
        <p class="mt-2 text-gray-600">Pantau kehadiran dan progres presensi Anda</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Hari</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalHari }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-calendar text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Hadir</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $hadir }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Persentase Kehadiran</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($persentase, 1) }}%</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-chart-pie text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('siswa.history') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="bulan" id="bulan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" id="tahun" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(range(date('Y'), date('Y') - 5) as $y)
                        <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance History Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">
                Riwayat Presensi - {{ \Carbon\Carbon::create()->month($bulan)->year($tahun)->format('F Y') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($presensis as $presensi)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $presensi->tanggal->format('d F Y') }}
                                <span class="text-xs text-gray-500 ml-2">({{ $presensi->tanggal->diffForHumans() }})</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $presensi->jam_pulang ? \Carbon\Carbon::parse($presensi->jam_pulang)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $presensi->status === 'hadir' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $presensi->status === 'terlambat' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $presensi->status === 'izin' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $presensi->status === 'sakit' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $presensi->status === 'alpa' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($presensi->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $presensi->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                <p>Belum ada data presensi untuk bulan ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- QR Code Info Card -->
    <div class="mt-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-md p-6 text-white">
        <div class="flex items-center space-x-4">
            <i class="fas fa-qrcode text-5xl"></i>
            <div>
                <h3 class="text-lg font-semibold">Scan QR Code untuk Presensi</h3>
                <p class="text-blue-100 text-sm">Gunakan QR Code yang tersedia di sekolah untuk melakukan presensi setiap hari</p>
            </div>
        </div>
    </div>
</div>
@endsection
