<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceBiayaPerbaikanSentToCustomer;
use App\Mail\ProgressSelesaiNotifiedAdmin;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MechanicReportController extends Controller
{

    private function ensureAdmin()
    {
        if (!session('id_user') || !session('role')) {
            abort(403);
        }

        $role = session('role');
        $roleStr = is_string($role) ? trim(strtolower($role)) : '';
        if ($roleStr !== 'admin') {
            $containsAdmin = is_string($role) && stripos($role, 'admin') !== false;
            if (!$containsAdmin) {
                abort(403);
            }
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();


        $filter = $request->query('filter');

        $query = Booking::query()
            ->whereNotNull('alamat') // ini halaman admin home service mekanik
            ->with(['mechanic', 'progressUpdates']);

        if ($filter === 'proses') {
            $query->whereIn('status', ['sedang_dikerjakan']);
        } elseif ($filter === 'approval') {
            $query->where('status', 'butuh_konfirmasi_biaya');
        } else {
            $query->whereIn('status', ['sedang_dikerjakan', 'butuh_konfirmasi_biaya']);
        }

        $bookings = $query
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get();

        return view('admin.laporan-mekanik', compact('bookings'));
    }

    public function showDetail(string $kodeBooking)
    {
        $this->ensureAdmin();

        $booking = Booking::with(['mechanic', 'progressUpdates'])
            ->where('kode_booking', $kodeBooking)
            ->first();

        if (!$booking) {
            abort(404);
        }

        return view('admin.laporan-mekanik-detail', compact('booking'));
    }

    public function confirmCost(Request $request, string $kodeBooking)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'total_biaya_perbaikan' => 'required|numeric|min:0',
            'laporan_perbaikan' => 'required|string|max:5000',
            'items' => 'nullable|array',
            'items.*.name' => 'nullable|string|max:200',
            'items.*.qty' => 'nullable|integer|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.decision' => 'nullable|in:setujui,tolak',
        ]);

        $decision = $request->input('decision');
        if (!$decision) {
            $itemDecisions = collect($request->input('items', []))
                ->pluck('decision')
                ->filter()
                ->all();
            $decision = in_array('tolak', $itemDecisions) ? 'tolak' : 'setujui';
        }

        $booking = Booking::where('kode_booking', $kodeBooking)->first();
        if (!$booking) {
            abort(404);
        }

        // Guard sesuai workflow baru
        // Beberapa kondisi UI bisa memanggil aksi ini meski status belum persis.
        // Agar tidak error 422 yang membingungkan, kembalikan ke halaman daftar antrean.
        if ($booking->status !== 'butuh_konfirmasi_biaya') {
            return redirect('/admin/laporan-mekanik')
                ->with('success', 'Booking tidak berada dalam status butuh_konfirmasi_biaya (status saat ini: '.$booking->status.').');
        }

        if ($decision === 'tolak') {
            // Minimal: kembalikan ke sedang_dikerjakan agar mekanik revisi.
            $booking->status = 'sedang_dikerjakan';
            $booking->save();

            return redirect('/admin/laporan-mekanik')
                ->with('success', 'Pengajuan biaya ditolak. Status dikembalikan ke sedang_dikerjakan.');
        }

        // Setujui: simpan nominal + laporan
        $booking->total_biaya_perbaikan = $data['total_biaya_perbaikan'];
        $booking->laporan_perbaikan = $data['laporan_perbaikan'];

        // Status payment: gunakan value yang sudah dipakai UI admin.
        // Untuk home service, status menunggu_pembayaran sudah ada.
        $booking->status = 'menunggu_pembayaran';
        $booking->save();

        // Kirim pesan ke admin + nota tagihan biaya perbaikan ke customer
        $customerEmail = null;
        $adminEmail = null;

        if (!empty($booking->user_id)) {
            $customer = User::find($booking->user_id);
            $customerEmail = $customer?->email;
        }

        $adminEmail = auth()->user()?->email;

        // Nota ke customer
        if (!empty($customerEmail)) {
            Mail::to($customerEmail)->send(new InvoiceBiayaPerbaikanSentToCustomer($booking));
        }

        // Pesan ke admin (admin yang melakukan aksi)
        if (!empty($adminEmail)) {
            Mail::to($adminEmail)->send(new ProgressSelesaiNotifiedAdmin($booking));
        }

        return redirect('/admin/laporan-mekanik')
            ->with('success', 'Biaya dikonfirmasi. Booking dipindahkan ke menunggu_pembayaran, dan notifikasi/invoice terkirim.');
    }
}

