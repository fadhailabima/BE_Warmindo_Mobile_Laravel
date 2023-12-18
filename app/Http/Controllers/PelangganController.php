<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AktivitasPengguna;
use App\Models\Warung;
use App\Models\Transaksi;
use App\Models\Menu;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Pelanggan; // Pastikan untuk mengimport model Pelanggan di sini
use App\Models\Promosi;
use Illuminate\Validation\ValidationException;

class PelangganController extends Controller
{
    public function tambahPelanggan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'namapelanggan' => 'nullable|string',
            'poin' => 'required|numeric',
            'status' => 'nullable|in:aktif,non aktif',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $pelanggan = new Pelanggan();
            $pelanggan->namapelanggan = $request->input('namapelanggan');
            $pelanggan->poin = $request->input('poin');
            $pelanggan->status = $request->input('status');

            // Mengatur tanggal dan waktu daftar dengan waktu saat ini
            $pelanggan->tanggaldaftar = now()->format('Y-m-d');
            $pelanggan->waktudaftar = now()->format('H:i:s');

            $pelanggan->save();

            return response()->json(['message' => 'Pelanggan berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat menambahkan pelanggan: ' . $e->getMessage()], 500);
        }
    }
}
