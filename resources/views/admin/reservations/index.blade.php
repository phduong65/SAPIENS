@extends('layouts.admin')

@section('title', 'Reservations')
@section('breadcrumb', 'Reservations')

@section('content')

<div class="adm-page-header">
    <h1 class="adm-page-title">Reservations</h1>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.reservations.index') }}" class="adm-filters" style="margin-bottom:1.25rem;">
    <input type="date" name="date" value="{{ request('date') }}" class="adm-input" style="width:auto;">
    <select name="status" class="adm-input adm-select" style="width:auto;">
        <option value="">All Status</option>
        <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
    <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">Filter</button>
    @if(request()->hasAny(['date','status']))
    <a href="{{ route('admin.reservations.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm">Clear</a>
    @endif
</form>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th class="adm-th">Code</th>
                    <th class="adm-th">Name</th>
                    <th class="adm-th">Phone</th>
                    <th class="adm-th">Email</th>
                    <th class="adm-th">Date</th>
                    <th class="adm-th">Time</th>
                    <th class="adm-th">Guests</th>
                    <th class="adm-th">Area</th>
                    <th class="adm-th">Status</th>
                    <th class="adm-th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $r)
                <tr class="adm-tr">
                    <td class="adm-td"><code style="font-size:0.75rem;">{{ $r->code }}</code></td>
                    <td class="adm-td">
                        {{ $r->full_name }}
                        @if($r->is_birthday)
                        <span title="Birthday" style="margin-left:4px;">🎂</span>
                        @endif
                    </td>
                    <td class="adm-td" style="color:var(--adm-muted); font-size:0.8rem;">{{ $r->phone }}</td>
                    <td class="adm-td" style="color:var(--adm-muted); font-size:0.8rem;">{{ $r->email }}</td>
                    <td class="adm-td">{{ $r->reservation_date->format('d/m/Y') }}</td>
                    <td class="adm-td">{{ $r->reservation_time }}</td>
                    <td class="adm-td" style="text-align:center;">{{ $r->guest_count }}</td>
                    <td class="adm-td" style="text-transform:capitalize; font-size:0.75rem;">{{ $r->seating_area ?? '—' }}</td>
                    <td class="adm-td">
                        @if($r->status === 'pending')
                            <span class="adm-badge adm-badge-warn">Pending</span>
                        @elseif($r->status === 'confirmed')
                            <span class="adm-badge adm-badge-ok">Confirmed</span>
                        @else
                            <span class="adm-badge adm-badge-err">Cancelled</span>
                        @endif
                    </td>
                    <td class="adm-td">
                        <div style="display:flex; gap:0.5rem; align-items:center;">
                            @if($r->status === 'pending')
                            <form method="POST" action="{{ route('admin.reservations.confirm', $r) }}"
                                  onsubmit="return confirm('Xác nhận đặt bàn {{ $r->code }}?')">
                                @csrf
                                <button type="submit" class="adm-btn adm-btn-ghost adm-btn-sm">Confirm</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reservations.cancel', $r) }}"
                                  onsubmit="return confirm('Huỷ đặt bàn {{ $r->code }}?')">
                                @csrf
                                <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm">Cancel</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.reservations.resend-confirmation', $r) }}"
                                  onsubmit="return confirm('Gửi lại email xác nhận cho {{ $r->code }}?')">
                                @csrf
                                <button type="submit" class="adm-btn adm-btn-ghost adm-btn-sm" title="Gửi lại email xác nhận">✉</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reservations.send-deposit', $r) }}"
                                  onsubmit="return confirm('Gửi email đặt cọc cho {{ $r->code }}?{{ $r->deposit_sent_at ? " (Đã gửi lúc " . $r->deposit_sent_at->format("H:i d/m") . ")" : "" }}')">
                                @csrf
                                <button type="submit"
                                        class="adm-btn adm-btn-sm {{ $r->deposit_sent_at ? 'adm-btn-ghost' : 'adm-btn-primary' }}"
                                        title="{{ $r->deposit_sent_at ? 'Đặt cọc đã gửi ' . $r->deposit_sent_at->format('H:i d/m') : 'Gửi email đặt cọc' }}"
                                        style="font-size:0.7rem;">
                                    {{ $r->deposit_sent_at ? '✓ Cọc' : 'Đặt cọc' }}
                                </button>
                            </form>
                            @if($r->note || $r->food_allergy || $r->special_request)
                            <button onclick="showNotes({{ $r->id }})" class="adm-btn adm-btn-ghost adm-btn-sm">
                                Notes
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                {{-- Notes row --}}
                @if($r->note || $r->food_allergy || $r->special_request)
                <tr id="notes-{{ $r->id }}" style="display:none;">
                    <td colspan="10" class="adm-td" style="background:var(--adm-bg);">
                        <div style="display:flex; gap:1.5rem; flex-wrap:wrap; font-size:0.8rem; color:var(--adm-muted); padding:0.25rem 0;">
                            @if($r->food_allergy)<p><strong style="color:var(--adm-text);">Allergy:</strong> {{ $r->food_allergy }}</p>@endif
                            @if($r->special_request)<p><strong style="color:var(--adm-text);">Special:</strong> {{ $r->special_request }}</p>@endif
                            @if($r->note)<p><strong style="color:var(--adm-text);">Note:</strong> {{ $r->note }}</p>@endif
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="10" class="adm-td" style="text-align:center; color:var(--adm-muted); padding:3rem;">
                        No reservations found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($reservations->hasPages())
<div class="mt-6">{{ $reservations->links() }}</div>
@endif

{{-- ─── Block Time Slots ──────────────────────────────────────── --}}
<div style="margin-top:2.5rem;">
    <h2 class="adm-page-title" style="font-size:1rem; margin-bottom:1.25rem;">Block Time Slots</h2>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; align-items:start;">

        {{-- Add form --}}
        <div class="adm-card" style="padding:1.25rem;">
            <p style="font-size:0.75rem; color:var(--adm-muted); margin-bottom:1rem;">
                Khi block một khung giờ, khách hàng sẽ không thể chọn giờ đó trong ngày tương ứng.
            </p>
            <form method="POST" action="{{ route('admin.blocked-slots.store') }}">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:0.75rem;">
                    <div>
                        <label class="adm-label">Ngày</label>
                        <input type="date" name="blocked_date" class="adm-input"
                               min="{{ date('Y-m-d') }}" required
                               value="{{ old('blocked_date') }}">
                        @error('blocked_date')<p class="adm-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="adm-label">Giờ</label>
                        <select name="blocked_time" class="adm-input adm-select" required>
                            <option value="" disabled selected>Chọn giờ</option>
                            @foreach(['18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30','00:00','00:30'] as $t)
                            <option value="{{ $t }}" {{ old('blocked_time') === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('blocked_time')<p class="adm-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div style="margin-bottom:0.75rem;">
                    <label class="adm-label">Lý do <span style="color:var(--adm-muted);">(tuỳ chọn)</span></label>
                    <input type="text" name="reason" class="adm-input"
                           placeholder="Ví dụ: Private event, Fully booked..."
                           value="{{ old('reason') }}" maxlength="200">
                </div>
                <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">
                    + Block Slot
                </button>
            </form>
        </div>

        {{-- Blocked list --}}
        <div class="adm-card" style="padding:1.25rem;">
            <p style="font-size:0.7rem; color:var(--adm-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">
                Upcoming Blocked Slots ({{ $blockedSlots->count() }})
            </p>

            @if($blockedSlots->isEmpty())
            <p style="color:var(--adm-muted); font-size:0.85rem;">Chưa có slot nào bị block.</p>
            @else
            <div style="display:flex; flex-direction:column; gap:0.5rem; max-height:340px; overflow-y:auto;">
                @foreach($blockedSlots->groupBy(fn($s) => $s->blocked_date->format('Y-m-d')) as $date => $slots)
                <div>
                    <p style="font-size:0.7rem; color:var(--adm-muted); text-transform:uppercase;
                               letter-spacing:0.06em; padding:0.35rem 0; border-bottom:1px solid var(--adm-border);">
                        {{ \Carbon\Carbon::parse($date)->format('D, d/m/Y') }}
                    </p>
                    @foreach($slots as $slot)
                    <div style="display:flex; align-items:center; justify-content:space-between;
                                 padding:0.4rem 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <span style="font-size:0.85rem; font-weight:600; color:var(--adm-text);">
                                {{ $slot->blocked_time }}
                            </span>
                            @if($slot->reason)
                            <span style="font-size:0.72rem; color:var(--adm-muted);">{{ $slot->reason }}</span>
                            @endif
                        </div>
                        <form method="POST"
                              action="{{ route('admin.blocked-slots.destroy', $slot) }}"
                              onsubmit="return confirm('Xoá block {{ $slot->blocked_time }} ngày {{ $slot->blocked_date->format('d/m') }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm"
                                    style="padding:2px 8px; font-size:0.7rem;">✕</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
function showNotes(id) {
    var row = document.getElementById('notes-' + id);
    if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
@endpush

@endsection
