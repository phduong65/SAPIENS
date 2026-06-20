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

@push('scripts')
<script>
function showNotes(id) {
    var row = document.getElementById('notes-' + id);
    if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
@endpush

@endsection
