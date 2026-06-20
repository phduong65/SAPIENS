@extends('layouts.admin')

@section('title', 'Events')
@section('breadcrumb', 'Events')

@section('content')

<div class="adm-page-header" style="display:flex; align-items:center; justify-content:space-between;">
    <h1 class="adm-page-title">Events</h1>
    <button onclick="document.getElementById('create-event-modal').style.display='flex'" class="adm-btn adm-btn-primary adm-btn-sm">
        + Add Event
    </button>
</div>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th class="adm-th">Title</th>
                    <th class="adm-th">Type</th>
                    <th class="adm-th">Date</th>
                    <th class="adm-th">Time</th>
                    <th class="adm-th">Published</th>
                    <th class="adm-th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr class="adm-tr">
                    <td class="adm-td" style="font-weight:500;">{{ $event->title }}</td>
                    <td class="adm-td">
                        <span class="adm-badge adm-badge-blue">{{ $event->type_label }}</span>
                    </td>
                    <td class="adm-td">{{ $event->event_date->format('d/m/Y') }}</td>
                    <td class="adm-td">{{ $event->event_time }}</td>
                    <td class="adm-td">
                        @if($event->is_published)
                            <span class="adm-badge adm-badge-ok">Published</span>
                        @else
                            <span class="adm-badge adm-badge-gray">Draft</span>
                        @endif
                    </td>
                    <td class="adm-td">
                        <div style="display:flex; gap:0.5rem;">
                            <button onclick="openEditEvent({{ $event->id }}, @json($event->only(['title','type','description','event_date','event_time','is_published']))"
                                    class="adm-btn adm-btn-ghost adm-btn-sm">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                  onsubmit="return confirm('Delete this event?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="adm-td" style="text-align:center; color:var(--adm-muted); padding:3rem;">
                        No events yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($events->hasPages())
<div class="mt-6">{{ $events->links() }}</div>
@endif

{{-- Create Modal --}}
<div id="create-event-modal" class="adm-modal-backdrop" style="display:none;">
    <div class="adm-modal">
        <div class="adm-modal-header">
            <span class="adm-modal-title">Add Event</span>
            <button onclick="document.getElementById('create-event-modal').style.display='none'"
                    class="adm-btn adm-btn-ghost adm-btn-icon" aria-label="Close">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="adm-modal-body">
                @include('admin.events.partials.form', ['event' => null])
            </div>
            <div class="adm-modal-footer">
                <button type="button" onclick="document.getElementById('create-event-modal').style.display='none'"
                        class="adm-btn adm-btn-ghost adm-btn-sm">Cancel</button>
                <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">Save Event</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-event-modal" class="adm-modal-backdrop" style="display:none;">
    <div class="adm-modal">
        <div class="adm-modal-header">
            <span class="adm-modal-title">Edit Event</span>
            <button onclick="document.getElementById('edit-event-modal').style.display='none'"
                    class="adm-btn adm-btn-ghost adm-btn-icon" aria-label="Close">✕</button>
        </div>
        <form id="edit-event-form" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="adm-modal-body">
                @include('admin.events.partials.form', ['event' => null])
            </div>
            <div class="adm-modal-footer">
                <button type="button" onclick="document.getElementById('edit-event-modal').style.display='none'"
                        class="adm-btn adm-btn-ghost adm-btn-sm">Cancel</button>
                <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">Update Event</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditEvent(id, data) {
    var form = document.getElementById('edit-event-form');
    form.action = '/admin/events/' + id;
    Object.entries(data).forEach(function([key, val]) {
        var el = form.elements[key];
        if (!el) return;
        if (el.type === 'checkbox') el.checked = !!val;
        else el.value = val !== null ? val : '';
    });
    document.getElementById('edit-event-modal').style.display = 'flex';
}
</script>
@endpush

@endsection
