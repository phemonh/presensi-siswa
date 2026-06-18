@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Wali Kelas</h1>
        <p class="mt-2 text-gray-600">Kelas: <span class="font-semibold text-blue-600">{{ $kelas->nama }}</span> | Tahun Ajaran: {{ $kelas->tahun_ajaran }}</p>
    </div>

    <!-- Today's Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalSiswa }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Hadir Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $hadirHariIni }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Belum Hadir</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $belumHadir }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Summary -->
    @if($monthlyStats)
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Ringkasan Bulan Ini ({{ \Carbon\Carbon::create()->month($bulan)->year($tahun)->format('F Y') }})</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-2xl font-bold text-green-600">{{ $monthlyStats->hadir ?? 0 }}</p>
                <p class="text-sm text-gray-600">Hadir</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-2xl font-bold text-yellow-600">{{ $monthlyStats->izin ?? 0 }}</p>
                <p class="text-sm text-gray-600">Izin</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-2xl font-bold text-purple-600">{{ $monthlyStats->sakit ?? 0 }}</p>
                <p class="text-sm text-gray-600">Sakit</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-2xl font-bold text-red-600">{{ $monthlyStats->alpa ?? 0 }}</p>
                <p class="text-sm text-gray-600">Alpa</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-600">{{ $monthlyStats->total ?? 0 }}</p>
                <p class="text-sm text-gray-600">Total</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Student Attendance List for Today -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Daftar Kehadiran Hari Ini</h2>
            <a href="{{ route('wali.detail') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                <i class="fas fa-calendar-alt mr-2"></i>Lihat Detail
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($siswas as $index => $siswa)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $siswa->user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $siswa->nis }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($siswa->presensis->isNotEmpty())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $siswa->presensis->first()->status === 'hadir' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $siswa->presensis->first()->status === 'terlambat' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($siswa->presensis->first()->status) }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Belum Hadir
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $siswa->presensis->isNotEmpty() ? \Carbon\Carbon::parse($siswa->presensis->first()->jam_masuk)->format('H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Belum ada data siswa di kelas ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
