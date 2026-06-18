@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
        <p class="mt-2 text-gray-600">Kelola sistem presensi siswa</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Siswa -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_siswa'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Guru</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_guru'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-chalkboard-teacher text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Kelas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_kelas'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-door-open text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Hadir Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['hadir_hari_ini'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('admin.students') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-md p-6 hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center space-x-4">
                <i class="fas fa-users text-3xl"></i>
                <div>
                    <h3 class="text-lg font-semibold">Kelola Siswa</h3>
                    <p class="text-blue-100 text-sm">Tambah, edit, hapus data siswa</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.classes') }}" class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-md p-6 hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center space-x-4">
                <i class="fas fa-school text-3xl"></i>
                <div>
                    <h3 class="text-lg font-semibold">Kelola Kelas</h3>
                    <p class="text-green-100 text-sm">Atur kelas dan wali kelas</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.teachers') }}" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl shadow-md p-6 hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center space-x-4">
                <i class="fas fa-chalkboard-teacher text-3xl"></i>
                <div>
                    <h3 class="text-lg font-semibold">Kelola Guru</h3>
                    <p class="text-purple-100 text-sm">Manage guru dan wali kelas</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Presensi Terbaru</h2>
                <a href="{{ route('admin.reports') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentPresensis as $presensi)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $presensi->siswa->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $presensi->siswa->nis }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $presensi->siswa->kelas->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $presensi->tanggal->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-' }}
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Belum ada data presensi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
