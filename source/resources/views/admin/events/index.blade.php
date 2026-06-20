@extends('layouts.admin')

@section('title', 'Events')
@section('breadcrumb', 'Events')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h1 class="font-display" style="font-size:1.75rem; color:#E5D9C8;">Events</h1>
    <button onclick="document.getElementById('create-event-modal').style.display='flex'"
            class="btn-gold text-xs">
        + Add Event
    </button>
</div>

<div style="background-color:#242420; border:1px solid #2E2E2A;">
    <div class="overflow-x-auto">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background-color:#1A1A18;">
                    <th class="admin-th">Title</th>
                    <th class="admin-th">Type</th>
                    <th class="admin-th">Date</th>
                    <th class="admin-th">Time</th>
                    <th class="admin-th">Published</th>
                    <th class="admin-th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr style="border-bottom:1px solid #2E2E2A;">
                    <td class="admin-td" style="color:#C9B99A;">{{ $event->title }}</td>
                    <td class="admin-td">
                        <span style="color:#B8925A; font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase;">
                            {{ $event->type_label }}
                        </span>
                    </td>
                    <td class="admin-td" style="color:#8C7E6A;">{{ $event->event_date->format('d/m/Y') }}</td>
                    <td class="admin-td" style="color:#8C7E6A;">{{ $event->event_time }}</td>
                    <td class="admin-td text-center">
                        <span style="color:{{ $event->is_published ? '#34d399' : '#3A3A35' }};">
                            {{ $event->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="admin-td">
                        <div class="flex gap-3">
                            <button onclick="openEditEvent({{ $event->id }}, @json($event->only(['title','type','description','event_date','event_time','is_published']))"
                                    style="color:#B8925A; font-size:0.75rem; background:none; border:none; cursor:pointer; text-transform:uppercase; letter-spacing:0.05em; padding:0;"
                                    class="hover:underline">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                  onsubmit="return confirm('Delete this event?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="color:#ef4444; font-size:0.75rem; background:none; border:none; cursor:pointer; text-transform:uppercase; letter-spacing:0.05em; padding:0;"
                                        class="hover:underline">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="admin-td text-center" style="color:#3A3A35; padding:3rem;">No events yet.</td>
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
<div id="create-event-modal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#242420; border:1px solid #2E2E2A; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; padding:2rem;">
        <div class="flex justify-between items-center mb-6">
            <h3 style="color:#E5D9C8;">Add Event</h3>
            <button onclick="document.getElementById('create-event-modal').style.display='none'"
                    style="color:#8C7E6A; background:none; border:none; cursor:pointer; font-size:1.25rem;">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.events.partials.form', ['event' => null])
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-gold text-xs">Save Event</button>
                <button type="button" onclick="document.getElementById('create-event-modal').style.display='none'"
                        class="btn-outline text-xs">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-event-modal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#242420; border:1px solid #2E2E2A; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; padding:2rem;">
        <div class="flex justify-between items-center mb-6">
            <h3 style="color:#E5D9C8;">Edit Event</h3>
            <button onclick="document.getElementById('edit-event-modal').style.display='none'"
                    style="color:#8C7E6A; background:none; border:none; cursor:pointer; font-size:1.25rem;">✕</button>
        </div>
        <form id="edit-event-form" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.events.partials.form', ['event' => null])
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-gold text-xs">Update Event</button>
                <button type="button" onclick="document.getElementById('edit-event-modal').style.display='none'"
                        class="btn-outline text-xs">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
.admin-th { padding:0.75rem 1rem; color:#8C7E6A; font-size:0.65rem; letter-spacing:0.12em; text-transform:uppercase; text-align:left; font-weight:500; }
.admin-td { padding:0.875rem 1rem; font-size:0.8125rem; vertical-align:middle; }
</style>

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
