<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceBiayaPerbaikanSentToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this
            ->subject('Nota Tagihan Biaya Perbaikan - ' . $this->booking->kode_booking)
            ->view('emails.invoice-biaya-perbaikan-sent')
            ->with([
                'booking' => $this->booking,
            ]);
    }
}

