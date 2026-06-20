@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

<div class="adm-page-header">
    <h1 class="adm-page-title">Dashboard</h1>
    <p class="adm-page-sub">{{ now()->format('l, d F Y') }}</p>
</div>

<div class="adm-stats">
    <div class="adm-stat">
        <p class="adm-stat-label">Today Total</p>
        <p class="adm-stat-value" style="color:var(--adm-text);">{{ $stats['today_total'] }}</p>
    </div>
    <div class="adm-stat">
        <p class="adm-stat-label">Pending</p>
        <p class="adm-stat-value" style="color:var(--adm-warning);">{{ $stats['today_pending'] }}</p>
    </div>
    <div class="adm-stat">
        <p class="adm-stat-label">Confirmed</p>
        <p class="adm-stat-value" style="color:var(--adm-success);">{{ $stats['today_confirmed'] }}</p>
    </div>
    <div class="adm-stat">
        <p class="adm-stat-label">Cancelled</p>
        <p class="adm-stat-value" style="color:var(--adm-danger);">{{ $stats['today_cancelled'] }}</p>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <span class="adm-card-title">Recent Reservations</span>
        <a href="{{ route('admin.reservations.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm">View All →</a>
    </div>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th class="adm-th">Code</th>
                    <th class="adm-th">Name</th>
                    <th class="adm-th">Date</th>
                    <th class="adm-th">Time</th>
                    <th class="adm-th">Guests</th>
                    <th class="adm-th">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReservations as $r)
                <tr class="adm-tr">
                    <td class="adm-td"><code style="font-size:0.75rem;">{{ $r->code }}</code></td>
                    <td class="adm-td">{{ $r->full_name }}</td>
                    <td class="adm-td">{{ $r->reservation_date->format('d M Y') }}</td>
                    <td class="adm-td">{{ $r->reservation_time }}</td>
                    <td class="adm-td">{{ $r->guest_count }}</td>
                    <td class="adm-td">
                        @if($r->status === 'pending')
                            <span class="adm-badge adm-badge-warn">Pending</span>
                        @elseif($r->status === 'confirmed')
                            <span class="adm-badge adm-badge-ok">Confirmed</span>
                        @else
                            <span class="adm-badge adm-badge-err">Cancelled</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="adm-td" colspan="6" style="text-align:center; color:var(--adm-muted);">
                        No reservations today.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
