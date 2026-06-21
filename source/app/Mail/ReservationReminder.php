<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Sapiens House] Nhắc lịch đặt bàn – ' . $this->reservation->reservation_time,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-reminder',
        );
    }
}
