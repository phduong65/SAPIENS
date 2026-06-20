<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    protected $fillable = [
        'title', 'slug', 'type', 'description',
        'image_path', 'event_date', 'event_time', 'is_published',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return Storage::url($this->image_path);
        }
        return asset('images/event-placeholder.jpg');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'event' => 'Event',
            'guest_shift' => 'Guest Shift',
            'workshop' => 'Workshop',
            'special_night' => 'Special Night',
            'community' => 'Community',
            default => 'Event',
        };
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', today());
    }
}
