<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Mail\ReservationConfirmation;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function index()
    {
        return view('client.reservation.index');
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
            // Log but don't fail the reservation
            logger()->error('Reservation email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Đặt bàn thành công! Chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ.',
            'code' => $reservation->code,
        ]);
    }
}
