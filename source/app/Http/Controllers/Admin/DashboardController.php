<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'today_total' => Reservation::today()->count(),
            'today_pending' => Reservation::today()->pending()->count(),
            'today_confirmed' => Reservation::today()->confirmed()->count(),
            'today_cancelled' => Reservation::today()->cancelled()->count(),
        ];

        $recentReservations = Reservation::orderByDesc('created_at')->limit(10)->get();

        return view('admin.dashboard.index', compact('stats', 'recentReservations'));
    }
}
