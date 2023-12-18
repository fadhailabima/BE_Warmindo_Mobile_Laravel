<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AktivitasPengguna;
use App\Models\Warung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function signUp(Request $request)
    {
        $warung = Warung::first(); // Mengambil warung pertama dalam sistem
        if (!$warung) {
            return response()->json(['message' => 'Tidak ada warung yang tersedia'], 404);
        }

        $kodeWarung = $warung->idwarung; // Mengambil kode warung dari data warung yang ada
        $tahunMasuk = date('Y'); // Tahun masuk
        $bulanMasuk = date('m'); // Bulan masuk
        $userCount = User::whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', date('m'))
            ->count() + 1;

        $employeeId = $kodeWarung . $tahunMasuk . $bulanMasuk . 'X' . str_pad($userCount, 2, '0', STR_PAD_LEFT);

        // $role = $request->input('role') ?? 'koki';

        // Simpan data user beserta employee_id dan password
        $user = new User();
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password')); // Menggunakan password dan di-hash sebelum disimpan
        $user->idkaryawan = $employeeId;
        $user->leveluser = $request->input('leveluser');
        $user->level = $request->input('level');
        $user->save();

        // Lakukan apa pun yang perlu dilakukan setelah sign up berhasil

        return response()->json(['message' => 'Sign up successful', 'idkaryawan' => $employeeId], 201);
    }

    protected function okResponse($message, $data = [])
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    protected function unautheticatedResponse($message)
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $loginData['username'])->first();

        if (!$user || !Hash::check($loginData['password'], $user->password)) {
            return $this->unautheticatedResponse('Kombinasi username dan password tidak valid.');
        }

        $token = $user->createToken('authToken')->plainTextToken;
        $userData = array_merge($user->toArray(), ['token' => $token]);

        AktivitasPengguna::create([
            'tanggal' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d'),
            'waktu' => now()->setTimezone('Asia/Jakarta')->format('H:i:s'),
            'id_pengguna' => $user->idkaryawan,
            'aktivitas' => 'login',
        ]);

        return $this->okResponse("Login Berhasil", ['user' => $userData]);
    }

    public function logout(Request $request)
    {
        $user = $request->user(); // Mendapatkan data pengguna yang sedang login

        if ($user) {
            AktivitasPengguna::create([
                'tanggal' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d'),
                'waktu' => now()->setTimezone('Asia/Jakarta')->format('H:i:s'),
                'id_pengguna' => $user->idkaryawan, // Pastikan ID pengguna tersedia dan valid
                'aktivitas' => 'logout', // Aktivitas logout
            ]);

            $user->currentAccessToken()->delete(); // Menghapus token saat logout

            return response()->json(['message' => 'Logout berhasil']);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function shift(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();

            // Mendapatkan jam saat ini dalam format 24 jam
            $currentHour = now()->setTimezone('Asia/Jakarta')->format('H');

            // Menentukan shift berdasarkan jam
            if ($currentHour >= 10 && $currentHour <= 16) {
                $shift = 'Shift 1';
            } elseif ($currentHour >= 17 && $currentHour <= 22) {
                $shift = 'Shift 2';
            } else {
                // Jika di luar jam shift
                return response()->json(['error' => 'Outside shift hours'], 403);
            }

            AktivitasPengguna::create([
                'tanggal' => now()->format('Y-m-d'),
                'waktu' => now()->format('H:i:s'),
                'id_pengguna' => $user->idkaryawan,
                'aktivitas' => 'akses shift',
            ]);

            // Memberikan informasi shift kepada pengguna
            return response()->json(['message' => 'Anda saat ini berada di Shift ' . substr($shift, -1)], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
