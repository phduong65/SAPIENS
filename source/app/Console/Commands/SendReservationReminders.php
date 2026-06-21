<?php

namespace App\Console\Commands;

use App\Mail\ReservationReminder;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReservationReminders extends Command
{
    protected $signature   = 'reservations:send-reminders';
    protected $description = 'Send reminder emails 3 hours before reservation time';

    public function handle(): void
    {
        $now      = now();
        $from     = $now->copy()->addHours(3)->startOfMinute();
        $to       = $from->copy()->addMinutes(14)->endOfMinute();

        $reservations = Reservation::whereIn('status', ['pending', 'confirmed'])
            ->whereDate('reservation_date', $now->toDateString())
            ->whereNull('reminder_sent_at')
            ->get()
            ->filter(function (Reservation $r) use ($from, $to, $now) {
                $time = $now->copy()->setTimeFromTimeString($r->reservation_time);
                return $time->between($from, $to);
            });

        foreach ($reservations as $reservation) {
            try {
                Mail::to($reservation->email)->send(new ReservationReminder($reservation));
                $reservation->update(['reminder_sent_at' => now()]);
                $this->info("Reminder sent: {$reservation->code} → {$reservation->email}");
            } catch (\Throwable $e) {
                $this->error("Failed {$reservation->code}: " . $e->getMessage());
            }
        }

        $this->info("Done. Processed {$reservations->count()} reservation(s).");
    }
}
