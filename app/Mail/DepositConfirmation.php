<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public int $depositAmount = 1_000_000;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Sapiens House] Xác nhận đặt cọc – ' . $this->reservation->code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deposit-confirmation',
        );
    }
}
