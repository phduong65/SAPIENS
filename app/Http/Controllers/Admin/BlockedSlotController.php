<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedSlot;
use Illuminate\Http\Request;

class BlockedSlotController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'blocked_date' => 'required|date|after_or_equal:today',
            'blocked_time' => 'required|in:18:00,18:30,19:00,19:30,20:00,20:30,21:00,21:30,22:00,22:30,23:00,23:30,00:00,00:30',
            'reason'       => 'nullable|string|max:200',
        ]);

        $exists = BlockedSlot::whereDate('blocked_date', $data['blocked_date'])
            ->where('blocked_time', $data['blocked_time'])
            ->exists();

        if (! $exists) {
            BlockedSlot::create([
                'blocked_date' => $data['blocked_date'],
                'blocked_time' => $data['blocked_time'],
                'reason'       => $data['reason'] ?? null,
            ]);
        }

        return back()->with('success', 'Time slot blocked.');
    }

    public function destroy(BlockedSlot $blockedSlot)
    {
        $blockedSlot->delete();

        return back()->with('success', 'Block removed.');
    }
}
