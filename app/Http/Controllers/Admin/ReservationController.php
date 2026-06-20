<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

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

        return view('admin.reservations.index', compact('reservations'));
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
}
