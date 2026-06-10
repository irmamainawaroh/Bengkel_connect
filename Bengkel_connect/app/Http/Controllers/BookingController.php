<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'layanan' => 'required|string',
            'tanggal' => 'required|date',
            'waktu' => 'required|string',
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:50',
            'kendaraan' => 'required|string|max:255',
            'nopol' => 'required|string|max:50',
            'catatan' => 'nullable|string',
        ]);

        $layanan = $data['layanan'];

        $totalPembayaran = 'Rp 250.000';
        if (str_contains($layanan, 'AC')) {
            $totalPembayaran = 'Rp 350.000';
        } elseif (str_contains($layanan, 'Mesin')) {
            $totalPembayaran = 'Rp 500.000';
        }

        $userId = session('id_user');

        $kodeBooking = $this->generateKodeBooking();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($kodeBooking, $userId, $layanan, $data) {
                Booking::create([
                    'kode_booking' => $kodeBooking,
                    'user_id' => $userId,
                    'nama' => $data['nama'],
                    'telepon' => $data['telepon'],
                    'kendaraan' => $data['kendaraan'],
                    'nopol' => $data['nopol'],
                    'layanan' => $layanan,
                    'tanggal' => $data['tanggal'],
                    'waktu' => $data['waktu'],
                    'catatan' => $data['catatan'] ?? null,
                    'status' => 'menunggu_pembayaran',
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $message = 'Slot untuk tanggal & waktu tersebut sudah penuh. Silakan pilih waktu lain.';

            return redirect()->back()
                ->withInput()
                ->withErrors(['waktu' => $message]);
        }


        Session::flash('totalPembayaran', $totalPembayaran);
        Session::flash('jenisLayanan', $layanan);
        Session::flash('kode_booking', $kodeBooking);
        Session::flash('nama_booking', $data['nama']);
        Session::flash('telepon_booking', $data['telepon']);
        Session::flash('kendaraan_booking', $data['kendaraan']);
        Session::flash('nopol_booking', $data['nopol']);


        return redirect('/payment/qris');
    }

    public function indexAdmin()
    {
        // masih ada untuk kompatibilitas lama, tapi route utama sudah pindah ke AdminBookingController
        if (!session('role') || session('role') !== 'admin') {
            abort(403);
        }

        $bookings = Booking::orderBy('created_at', 'desc')->limit(50)->get();

        return view('admin.dashboard', compact('bookings'));
    }


    private function generateKodeBooking(): string
    {
        // Contoh: BC-20260522-AB12CD
        $date = now()->format('Ymd');
        return 'BC-' . $date . '-' . strtoupper(Str::random(6));
    }

}

