<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'code', 'full_name', 'phone', 'email',
        'reservation_date', 'reservation_time', 'guest_count',
        'seating_area', 'note', 'food_allergy',
        'is_birthday', 'special_request', 'status',
        'confirmed_at', 'cancelled_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'is_birthday' => 'boolean',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public static function generateCode(): string
    {
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'SPH-' . now()->format('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'confirmed',
            'cancelled' => 'cancelled',
            default => 'pending',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reservation_date', today());
    }
}
