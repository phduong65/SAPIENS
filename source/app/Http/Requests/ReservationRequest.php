<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'reservation_time' => 'required|string',
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
            'guest_count.required' => 'Vui lòng nhập số lượng khách.',
            'guest_count.min' => 'Số khách tối thiểu là 1.',
            'guest_count.max' => 'Số khách tối đa là 50.',
        ];
    }
}
