<?php

namespace App\Http\Requests;

use App\Models\BlockedSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:200',
            'phone' => ['required', 'string', 'regex:/^[0-9+\s\-]{9,15}$/'],
            'email' => 'required|email|max:200',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => [
                'required',
                Rule::in(['18:00','18:30','19:00','19:30','20:00','20:30',
                          '21:00','21:30','22:00','22:30','23:00','23:30','00:00','00:30']),
                function ($attribute, $value, $fail) {
                    $date = $this->input('reservation_date');
                    if ($date && BlockedSlot::whereDate('blocked_date', $date)
                            ->where('blocked_time', $value)->exists()) {
                        $fail('Khung giờ này không còn nhận đặt bàn. Vui lòng chọn giờ khác.');
                    }
                },
            ],
            'guest_count' => 'required|integer|min:1|max:50',
            'seating_area' => 'nullable|in:indoor,outdoor,bar',
            'note' => 'nullable|string|max:1000',
            'food_allergy' => 'nullable|string|max:500',
            'is_birthday' => 'boolean',
            'special_request' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Vui lòng nhập họ tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'reservation_date.required' => 'Vui lòng chọn ngày đặt bàn.',
            'reservation_date.after_or_equal' => 'Ngày đặt bàn phải từ hôm nay trở đi.',
            'reservation_time.required' => 'Vui lòng chọn giờ đặt bàn.',
            'reservation_time.in'       => 'Giờ đặt bàn không hợp lệ.',
            'guest_count.required' => 'Vui lòng nhập số lượng khách.',
            'guest_count.min' => 'Số khách tối thiểu là 1.',
            'guest_count.max' => 'Số khách tối đa là 50.',
        ];
    }
}
