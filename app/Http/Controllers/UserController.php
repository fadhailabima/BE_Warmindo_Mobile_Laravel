<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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

        $employeeId = $kodeWarung . $tahunMasuk . $bulanMasuk . 'X' . str_pad($userCount, 2, '0', STR_PAD_LEFT) ;

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

        // $user = User::where('username', $loginData['username'])->first();

        if (Auth::attempt($loginData)) {
            $token = Auth::user()->createToken('authToken')->plainTextToken;
            $user = array_merge(Auth::user()->toArray(), ['token' => $token]);
            return $this->okResponse("Login Berhasil", $user);
        }

        return $this->unautheticatedResponse('Login Gagal');
    }
}
