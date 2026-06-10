<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class CustomerHomeServiceController extends Controller
{
    public function showMyBookings()
    {
        $customerId = session('id_user');
        if (!$customerId) {
            abort(403);
        }

        $bookings = Booking::where('user_id', $customerId)
            ->with('progressUpdates')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.home-service-bookings', compact('bookings'));
    }

    public function showDetail(string $kodeBooking)
    {
        $customerId = session('id_user');
        if (!$customerId) {
            abort(403);
        }

        $bookingQuery = Booking::where('kode_booking', $kodeBooking)
            ->where('user_id', $customerId)
            ->with('progressUpdates');

        $booking = $bookingQuery->first();

        if (!$booking) {
            // Agar mudah dipahami saat 404: tampilkan kode booking & user yang sedang login
            // (hapus/hardening bila sudah stabil)
            abort(404, 'Booking tidak ditemukan untuk kodeBooking=' . $kodeBooking . ' user_id=' . $customerId);
        }

        return view('customer.home-service-detail', compact('booking'));
    }

    public function showRepairInvoice(string $kodeBooking)
    {
        $customerId = session('id_user');
        if (!$customerId) {
            abort(403);
        }

        $booking = Booking::where('kode_booking', $kodeBooking)
            ->where('user_id', $customerId)
            ->where('status', 'menunggu_pembayaran')
            ->whereNotNull('total_biaya_perbaikan')
            ->first();

        if (!$booking) {
            abort(404);
        }

        return view('customer.repair-invoice', compact('booking'));
    }
}

