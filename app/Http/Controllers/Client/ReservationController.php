<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Mail\NewReservationAlert;
use App\Mail\ReservationConfirmation;
use App\Models\BlockedSlot;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function index()
    {
        return view('client.reservation.index');
    }

    public function blockedSlots(Request $request): JsonResponse
    {
        $date = $request->validate(['date' => 'required|date'])['date'];

        $blocked = BlockedSlot::whereDate('blocked_date', $date)
            ->pluck('blocked_time');

        return response()->json(['blocked' => $blocked]);
    }

    public function store(ReservationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['code'] = Reservation::generateCode();
        $data['is_birthday'] = $request->boolean('is_birthday');

        $reservation = Reservation::create($data);

        try {
            Mail::to($reservation->email)->send(new ReservationConfirmation($reservation));
        } catch (\Throwable $e) {
            logger()->error('Reservation confirmation email failed: ' . $e->getMessage());
        }

        try {
            $adminEmail = env('ADMIN_ALERT_EMAIL', config('mail.from.address'));
            Mail::to($adminEmail)->send(new NewReservationAlert($reservation));
        } catch (\Throwable $e) {
            logger()->error('Admin reservation alert failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Đặt bàn thành công! Chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ.',
            'code' => $reservation->code,
        ]);
    }
}
