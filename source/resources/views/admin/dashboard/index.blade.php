@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

<div class="mb-8">
    <h1 class="font-display" style="font-size:1.75rem; color:#E5D9C8;">Dashboard</h1>
    <p style="color:#8C7E6A; font-size:0.8125rem; margin-top:0.25rem;">
        {{ now()->format('l, d F Y') }}
    </p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    @php
    $statCards = [
        ['label' => 'Today Total', 'value' => $stats['today_total'], 'color' => '#C9B99A'],
        ['label' => 'Pending', 'value' => $stats['today_pending'], 'color' => '#fbbf24'],
        ['label' => 'Confirmed', 'value' => $stats['today_confirmed'], 'color' => '#34d399'],
        ['label' => 'Cancelled', 'value' => $stats['today_cancelled'], 'color' => '#ef4444'],
    ];
    @endphp

    @foreach($statCards as $card)
    <div style="background-color:#242420; border:1px solid #2E2E2A; padding:1.5rem;">
        <p style="color:#8C7E6A; font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; margin-bottom:0.75rem;">
            {{ $card['label'] }}
        </p>
        <p style="color:{{ $card['color'] }}; font-size:2rem; font-weight:300; line-height:1;">
            {{ $card['value'] }}
        </p>
    </div>
    @endforeach
</div>

{{-- Recent Reservations --}}
<div style="background-color:#242420; border:1px solid #2E2E2A;">
    <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid #2E2E2A;">
        <h2 style="color:#C9B99A; font-size:0.875rem; letter-spacing:0.05em;">Recent Reservations</h2>
        <a href="{{ route('admin.reservations.index') }}"
           style="color:#B8925A; font-size:0.75rem; letter-spacing:0.05em;"
           class="hover:underline">
            View All →
        </a>
    </div>
    <div class="overflow-x-auto">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background-color:#1A1A18;">
                    <th class="admin-th">Code</th>
                    <th class="admin-th">Name</th>
                    <th class="admin-th">Date</th>
                    <th class="admin-th">Time</th>
                    <th class="admin-th">Guests</th>
                    <th class="admin-th">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReservations as $r)
                <tr style="border-bottom:1px solid #2E2E2A;" class="hover:bg-cave-mid transition-colors">
                    <td class="admin-td" style="color:#B8925A; font-size:0.75rem; font-family:monospace;">{{ $r->code }}</td>
                    <td class="admin-td" style="color:#C9B99A;">{{ $r->full_name }}</td>
                    <td class="admin-td" style="color:#8C7E6A;">{{ $r->reservation_date->format('d/m/Y') }}</td>
                    <td class="admin-td" style="color:#8C7E6A;">{{ $r->reservation_time }}</td>
                    <td class="admin-td" style="color:#8C7E6A; text-align:center;">{{ $r->guest_count }}</td>
                    <td class="admin-td">
                        <span class="badge-{{ $r->status }}">{{ ucfirst($r->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="admin-td text-center" style="color:#3A3A35; padding:2rem;">
                        No reservations yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-th { padding:0.75rem 1rem; color:#8C7E6A; font-size:0.65rem; letter-spacing:0.12em; text-transform:uppercase; text-align:left; font-weight:500; }
.admin-td { padding:0.875rem 1rem; font-size:0.8125rem; vertical-align:middle; }
</style>
@endsection
