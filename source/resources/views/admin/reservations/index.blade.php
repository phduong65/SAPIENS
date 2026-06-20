@extends('layouts.admin')

@section('title', 'Reservations')
@section('breadcrumb', 'Reservations')

@section('content')

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <h1 class="font-display" style="font-size:1.75rem; color:#E5D9C8;">Reservations</h1>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.reservations.index') }}"
      class="flex flex-wrap gap-3 mb-6">
    <input type="date" name="date" value="{{ request('date') }}"
           class="form-input" style="width:auto;">
    <select name="status" class="form-input" style="width:auto;">
        <option value="">All Status</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
    <button type="submit" class="btn-gold text-xs" style="padding:0.625rem 1.25rem;">Filter</button>
    @if(request()->hasAny(['date','status']))
    <a href="{{ route('admin.reservations.index') }}" class="btn-outline text-xs" style="padding:0.625rem 1.25rem;">Clear</a>
    @endif
</form>

<div style="background-color:#242420; border:1px solid #2E2E2A;">
    <div class="overflow-x-auto">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background-color:#1A1A18;">
                    <th class="admin-th">Code</th>
                    <th class="admin-th">Name</th>
                    <th class="admin-th">Phone</th>
                    <th class="admin-th">Email</th>
                    <th class="admin-th">Date</th>
                    <th class="admin-th">Time</th>
                    <th class="admin-th">Guests</th>
                    <th class="admin-th">Area</th>
                    <th class="admin-th">Status</th>
                    <th class="admin-th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $r)
                <tr style="border-bottom:1px solid #2E2E2A;">
                    <td class="admin-td" style="color:#B8925A; font-family:monospace; font-size:0.7rem;">{{ $r->code }}</td>
                    <td class="admin-td" style="color:#C9B99A;">
                        {{ $r->full_name }}
                        @if($r->is_birthday)
                        <span title="Birthday" style="color:#B8925A; margin-left:4px;">🎂</span>
                        @endif
                    </td>
                    <td class="admin-td" style="color:#8C7E6A; font-size:0.8rem;">{{ $r->phone }}</td>
                    <td class="admin-td" style="color:#8C7E6A; font-size:0.8rem;">{{ $r->email }}</td>
                    <td class="admin-td" style="color:#8C7E6A;">{{ $r->reservation_date->format('d/m/Y') }}</td>
                    <td class="admin-td" style="color:#8C7E6A;">{{ $r->reservation_time }}</td>
                    <td class="admin-td" style="color:#8C7E6A; text-align:center;">{{ $r->guest_count }}</td>
                    <td class="admin-td" style="color:#8C7E6A; text-transform:capitalize; font-size:0.75rem;">{{ $r->seating_area ?? '—' }}</td>
                    <td class="admin-td"><span class="badge-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                    <td class="admin-td">
                        <div class="flex gap-2">
                            @if($r->status === 'pending')
                            <form method="POST" action="{{ route('admin.reservations.confirm', $r) }}"
                                  onsubmit="return confirm('Xác nhận đặt bàn {{ $r->code }}?')">
                                @csrf
                                <button type="submit"
                                        style="font-size:0.7rem; color:#34d399; letter-spacing:0.05em; background:none; border:none; cursor:pointer; text-transform:uppercase; padding:0;"
                                        class="hover:underline">
                                    Confirm
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.reservations.cancel', $r) }}"
                                  onsubmit="return confirm('Huỷ đặt bàn {{ $r->code }}?')">
                                @csrf
                                <button type="submit"
                                        style="font-size:0.7rem; color:#ef4444; letter-spacing:0.05em; background:none; border:none; cursor:pointer; text-transform:uppercase; padding:0;"
                                        class="hover:underline">
                                    Cancel
                                </button>
                            </form>
                            @endif
                            @if($r->note || $r->food_allergy || $r->special_request)
                            <button onclick="showNotes({{ $r->id }})"
                                    style="font-size:0.7rem; color:#8C7E6A; letter-spacing:0.05em; background:none; border:none; cursor:pointer; text-transform:uppercase; padding:0;"
                                    class="hover:underline">
                                Notes
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                {{-- Notes row --}}
                @if($r->note || $r->food_allergy || $r->special_request)
                <tr id="notes-{{ $r->id }}" style="display:none; background-color:#1A1A18;">
                    <td colspan="10" class="admin-td">
                        <div class="flex gap-6 flex-wrap" style="font-size:0.8rem; color:#8C7E6A; padding:0.5rem 0;">
                            @if($r->food_allergy)<p><strong style="color:#C9B99A;">Allergy:</strong> {{ $r->food_allergy }}</p>@endif
                            @if($r->special_request)<p><strong style="color:#C9B99A;">Special:</strong> {{ $r->special_request }}</p>@endif
                            @if($r->note)<p><strong style="color:#C9B99A;">Note:</strong> {{ $r->note }}</p>@endif
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="10" class="admin-td text-center" style="color:#3A3A35; padding:3rem;">
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

<style>
.admin-th { padding:0.75rem 1rem; color:#8C7E6A; font-size:0.65rem; letter-spacing:0.12em; text-transform:uppercase; text-align:left; font-weight:500; white-space:nowrap; }
.admin-td { padding:0.75rem 1rem; font-size:0.8125rem; vertical-align:middle; }
</style>

@push('scripts')
<script>
function showNotes(id) {
    var row = document.getElementById('notes-' + id);
    if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
@endpush

@endsection
