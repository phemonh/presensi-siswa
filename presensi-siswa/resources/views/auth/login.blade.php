@extends('layouts.app')

@section('title', 'Login - Sistem Presensi Siswa')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-2xl">
        <!-- Header -->
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <i class="fas fa-school text-blue-600 text-5xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">
                Sistem Presensi Siswa
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Silakan login untuk melanjutkan
            </p>
        </div>

        <!-- Login Form -->
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3 border @error('email') border-red-500 @enderror"
                           placeholder="nama@email.com" value="{{ old('email') }}">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                           class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3 border @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Ingat saya
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login
                </button>
            </div>
        </form>

        <!-- Demo Credentials -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Demo Login:</h3>
            <div class="space-y-1 text-xs text-gray-600">
                <p><strong>Admin:</strong> admin@example.com / password</p>
                <p><strong>Guru:</strong> guru@example.com / password</p>
                <p><strong>Siswa:</strong> siswa@example.com / password</p>
            </div>
        </div>
    </div>
</div>
@endsection
