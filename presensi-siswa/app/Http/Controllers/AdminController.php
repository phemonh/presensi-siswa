<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_siswa' => Siswa::count(),
            'total_guru' => User::where('role', 'wali_kelas')->count(),
            'total_kelas' => Kelas::count(),
            'hadir_hari_ini' => Presensi::whereDate('tanggal', today())
                ->whereIn('status', ['hadir', 'terlambat'])
                ->count(),
        ];

        $recentPresensis = Presensi::with(['siswa.user', 'siswa.kelas'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPresensis'));
    }

    /**
     * Manage students
     */
    public function students(Request $request)
    {
        $query = Siswa::with(['user', 'kelas']);

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $students = $query->paginate(20);
        $kelasList = Kelas::all();

        return view('admin.students.index', compact('students', 'kelasList'));
    }

    /**
     * Create student
     */
    public function createStudent()
    {
        $kelasList = Kelas::all();
        return view('admin.students.create', compact('kelasList'));
    }

    /**
     * Store student
     */
    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:20',
            'nis' => 'required|string|unique:siswas,nis',
            'nisn' => 'required|string|unique:siswas,nisn',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'siswa',
                'phone' => $validated['phone'] ?? null,
            ]);

            // Create siswa profile
            Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $validated['kelas_id'],
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'],
            ]);

            DB::commit();

            return redirect()->route('admin.students')
                ->with('success', 'Siswa berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menambahkan siswa: ' . $e->getMessage()]);
        }
    }

    /**
     * Manage classes
     */
    public function classes(Request $request)
    {
        $query = Kelas::with('waliKelas');

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $kelasList = $query->paginate(20);
        $teachers = User::where('role', 'wali_kelas')->get();

        return view('admin.classes.index', compact('kelasList', 'teachers'));
    }

    /**
     * Create class
     */
    public function createClass()
    {
        $teachers = User::where('role', 'wali_kelas')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    /**
     * Store class
     */
    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:20',
            'wali_kelas_id' => 'nullable|exists:users,id',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.classes')
            ->with('success', 'Kelas berhasil ditambahkan');
    }

    /**
     * Manage teachers/wali kelas
     */
    public function teachers(Request $request)
    {
        $query = User::where('role', 'wali_kelas');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $teachers = $query->paginate(20);

        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Create teacher
     */
    public function createTeacher()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store teacher
     */
    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nip' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'wali_kelas',
            'nip' => $validated['nip'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('admin.teachers')
            ->with('success', 'Guru berhasil ditambahkan');
    }

    /**
     * Attendance reports
     */
    public function reports(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $kelasId = $request->input('kelas_id');

        $query = Presensi::with(['siswa.user', 'siswa.kelas'])
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if ($kelasId) {
            $query->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $presensis = $query->orderBy('tanggal', 'desc')->paginate(50);
        $kelasList = Kelas::all();

        return view('admin.reports.index', compact('presensis', 'kelasList', 'bulan', 'tahun'));
    }
}
