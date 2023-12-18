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

class TransaksiController extends Controller
{
    private function determineShift()
    {
        $currentHour = now()->format('H');

        if ($currentHour >= 10 && $currentHour <= 16) {
            return '1';
        } elseif ($currentHour >= 17 && $currentHour <= 23) {
            return '2';
        } else {
            return null;
        }
    }

    public function tambahTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pelanggan' => 'nullable|exists:pelanggans,id',
            'status' => 'required',
            'kode_meja' => 'required',
            'metode_pembayaran' => 'required',
            'totaldiskon' => 'required',
            'idpromosi' => 'nullable|exists:promosis,id', // Validasi untuk id_promosi
            'detail_transaksi' => 'required|array',
            'detail_transaksi.*.idmenu' => 'required|exists:menus,id', // Validasi untuk idmenu di setiap detail transaksi
            'detail_transaksi.*.jumlah' => 'required|numeric|min:1', // Validasi jumlah di setiap detail transaksi
            'detail_transaksi.*.status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();

            $shift = $this->determineShift();

            if ($shift === null) {
                return response()->json(['error' => 'Outside shift hours'], 403);
            }

            $warung = Warung::first();
            if (!$warung) {
                return response()->json(['message' => 'Tidak ada warung yang tersedia'], 404);
            }

            $idPengguna = $user->idkaryawan;

            $kodeWarung = $warung->idwarung;
            $tahun = date('Y');
            $bulan = date('m');
            $tanggal = date('d');

            $nomorTransaksi = Transaksi::whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->count() + 1;

            $nomorTransaksiFormatted = str_pad($nomorTransaksi, 3, '0', STR_PAD_LEFT);
            $idTransaksi = $kodeWarung . $tahun . $bulan . $tanggal . $nomorTransaksiFormatted;

            try {
                DB::beginTransaction();

                $transaksiBaru = new Transaksi();
                $transaksiBaru->idtransaksi = $idTransaksi;
                $transaksiBaru->tanggal = now()->format('Y-m-d');
                $transaksiBaru->waktu = now()->format('H:i:s');
                $transaksiBaru->shift = $shift;
                $transaksiBaru->id_pengguna = $idPengguna;
                $transaksiBaru->id_pelanggan = $request->input('id_pelanggan');
                $transaksiBaru->status = $request->input('status');
                $transaksiBaru->kode_meja = $request->input('kode_meja');
                $transaksiBaru->namapelanggan = $request->input('namapelanggan');
                $transaksiBaru->total = 0;
                $transaksiBaru->metode_pembayaran = $request->input('metode_pembayaran');
                $transaksiBaru->totaldiskon = $request->input('totaldiskon');
                $transaksiBaru->idpromosi = $request->input('idpromosi');

                // Mengisi namapelanggan jika id_pelanggan diisi
                if ($request->filled('id_pelanggan')) {
                    $pelanggan = Pelanggan::find($request->input('id_pelanggan'));
                    if ($pelanggan) {
                        $transaksiBaru->namapelanggan = $pelanggan->namapelanggan;
                    } else {
                        DB::rollBack();
                        return response()->json(['error' => 'Pelanggan tidak ditemukan'], 404);
                    }
                }

                // Mengambil idpromosi dari tabel promosi
                if ($request->filled('idpromosi')) {
                    $promosi = Promosi::find($request->input('idpromosi'));
                    if ($promosi) {
                        $transaksiBaru->idpromosi = $promosi->id;
                    } else {
                        DB::rollBack();
                        return response()->json(['error' => 'Promosi tidak ditemukan'], 404);
                    }
                }

                $transaksiBaru->save();

                // Iterasi dan tambahkan detail transaksi
                $totalTransaksi = 0;
                foreach ($request->input('detail_transaksi') as $detail) {
                    $menu = Menu::find($detail['idmenu']);

                    if ($menu) {
                        $harga = $menu->harga;

                        $dataDetailTransaksi = [
                            'idtransaksi' => $transaksiBaru->idtransaksi,
                            'idmenu' => $menu->id,
                            'namamenu' => $menu->nama_menu,
                            'harga' => $harga,
                            'jumlah' => $detail['jumlah'],
                            'subtotal' => $harga * $detail['jumlah'],
                            'status' => $detail['status'],
                        ];

                        $totalTransaksi += $dataDetailTransaksi['subtotal'];

                        DetailTransaksi::create($dataDetailTransaksi);
                    } else {
                        DB::rollBack();
                        return response()->json(['error' => 'Menu tidak ditemukan'], 404);
                    }
                }

                $transaksiBaru->update(['total' => $totalTransaksi]);

                DB::commit();

                return response()->json(['message' => 'Transaksi berhasil ditambahkan'], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Terjadi kesalahan saat menambahkan transaksi: ' . $e->getMessage()], 500);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function daftarTransaksi()
    {
        // Mendapatkan shift yang sedang berlangsung
        $shiftSedang = $this->determineShift();

        if ($shiftSedang === null) {
            return response()->json(['error' => 'Outside shift hours'], 403);
        }

        // Query transaksi berdasarkan shift yang sedang berlangsung
        $daftarTransaksi = Transaksi::select('idtransaksi', 'kode_meja', 'status')
            ->where('shift', $shiftSedang)
            ->get();

        if ($shiftSedang === 1 && $daftarTransaksi->isEmpty()) {
            return response()->json(['error' => 'Tidak ada transaksi pada Shift 1'], 404);
        } elseif ($shiftSedang === 2 && $daftarTransaksi->isEmpty()) {
            return response()->json(['error' => 'Tidak ada transaksi pada Shift 2'], 404);
        }

        return response()->json(['daftar_transaksi' => $daftarTransaksi], 200);
    }

    public function showDetailTransaksi($idtransaksi)
    {
        try {
            // Ambil data transaksi dari ID transaksi beserta detail transaksi terkait
            $transaksi = Transaksi::with('DetailTransaksi')->findOrFail($idtransaksi);

            // Ambil data detail transaksi dari relasi
            $detailTransaksi = $transaksi->detailTransaksi;

            // Mengambil informasi metode pembayaran dan total dari transaksi
            $metodePembayaran = $transaksi->metode_pembayaran;
            $totalTransaksi = $transaksi->total;

            // Menyiapkan opsi status untuk transaksi
            $transaksiStatusOptions = ['baru', 'diproses', 'disajikan', 'selesai']; // Ganti dengan opsi status transaksi yang sesuai dengan struktur enum di database

            // Menyiapkan opsi status untuk detail transaksi
            $detailTransaksiStatusOptions = ['aktif', 'batal']; // Ganti dengan opsi status detail transaksi yang sesuai dengan struktur enum di database

            // Mengembalikan data dalam format JSON
            return response()->json([
                'transaksi' => [
                    'idtransaksi' => $transaksi->idtransaksi,
                    'tanggal' => $transaksi->tanggal,
                    'waktu' => $transaksi->waktu,
                    'shift' => $transaksi->shift,
                    'id_pelanggan' => $transaksi->id_pelanggan,
                    'status' => $transaksi->status,
                    'kode_meja' => $transaksi->kode_meja,
                    'namapelanggan' => $transaksi->namapelanggan,
                    'total' => $totalTransaksi,
                    'metode_pembayaran' => $metodePembayaran,
                    'totaldiskon' => $transaksi->totaldiskon,
                    'idpromosi' => $transaksi->idpromosi,
                    'detail_transaksi' => $detailTransaksi,
                ],
                'transaksiStatusOptions' => $transaksiStatusOptions, // Opsi status untuk transaksi
                'detailTransaksiStatusOptions' => $detailTransaksiStatusOptions, // Opsi status untuk detail transaksi
            ], 200);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, kembalikan respons error
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatusTransaksi(Request $request, $idtransaksi)
    {
        try {
            // Ambil data transaksi dari ID transaksi
            $transaksi = Transaksi::findOrFail($idtransaksi);

            // Update status transaksi sesuai dengan request
            $transaksi->status = $request->input('status');
            $transaksi->save();

            return response()->json(['message' => 'Status transaksi berhasil diubah'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatusDetailTransaksi(Request $request, $id)
    {
        try {
            // Ambil data detail transaksi dari ID detail transaksi
            $detailTransaksi = DetailTransaksi::findOrFail($id);

            // Update status detail transaksi sesuai dengan request
            $detailTransaksi->status = $request->input('status');
            $detailTransaksi->save();

            return response()->json(['message' => 'Status detail transaksi berhasil diubah'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

}
