<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DepositConfirmation;
use App\Mail\ReservationConfirmation;
use App\Models\BlockedSlot;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::orderByDesc('reservation_date')->orderByDesc('reservation_time');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        $reservations = $query->paginate(20)->withQueryString();

        $blockedSlots = BlockedSlot::whereDate('blocked_date', '>=', today()->toDateString())
            ->orderBy('blocked_date')
            ->orderBy('blocked_time')
            ->get();

        return view('admin.reservations.index', compact('reservations', 'blockedSlots'));
    }

    public function confirm(Reservation $reservation)
    {
        $reservation->update(['status' => 'confirmed', 'confirmed_at' => now()]);

        return back()->with('success', "Đã xác nhận đặt bàn #{$reservation->code}");
    }

    public function cancel(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return back()->with('success', "Đã huỷ đặt bàn #{$reservation->code}");
    }

    public function sendDeposit(Reservation $reservation)
    {
        try {
            Mail::to($reservation->email)->send(new DepositConfirmation($reservation));
            $reservation->update(['deposit_sent_at' => now()]);
            return back()->with('success', "Đã gửi email đặt cọc cho #{$reservation->code}");
        } catch (\Throwable $e) {
            return back()->with('error', "Gửi email thất bại: " . $e->getMessage());
        }
    }

    public function resendConfirmation(Reservation $reservation)
    {
        try {
            Mail::to($reservation->email)->send(new ReservationConfirmation($reservation));
            return back()->with('success', "Đã gửi lại email xác nhận cho #{$reservation->code}");
        } catch (\Throwable $e) {
            return back()->with('error', "Gửi email thất bại: " . $e->getMessage());
        }
    }
}
