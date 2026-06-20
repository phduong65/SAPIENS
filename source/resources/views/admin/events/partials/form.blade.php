<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <div class="adm-form-group">
            <label class="adm-label">Title <span style="color:var(--adm-danger);">*</span></label>
            <input type="text" name="title" class="adm-input" required
                   value="{{ $event?->title ?? old('title') }}">
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Type <span style="color:var(--adm-danger);">*</span></label>
            <select name="type" class="adm-input adm-select" required>
                <option value="">Select type...</option>
                @foreach(['event' => 'Event', 'guest_shift' => 'Guest Shift', 'workshop' => 'Workshop', 'special_night' => 'Special Night', 'community' => 'Community'] as $val => $label)
                <option value="{{ $val }}"
                        {{ ($event?->type === $val) ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Date <span style="color:var(--adm-danger);">*</span></label>
            <input type="date" name="event_date" class="adm-input" required
                   value="{{ $event?->event_date?->format('Y-m-d') ?? old('event_date') }}">
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Time <span style="color:var(--adm-danger);">*</span></label>
            <input type="time" name="event_time" class="adm-input" required
                   value="{{ $event?->event_time ?? old('event_time') }}">
        </div>
    </div>
    <div class="col-span-2">
        <div class="adm-form-group">
            <label class="adm-label">Description <span style="color:var(--adm-danger);">*</span></label>
            <textarea name="description" class="adm-input adm-textarea" rows="4" required>{{ $event?->description ?? old('description') }}</textarea>
        </div>
    </div>
    <div class="col-span-2">
        <div class="adm-form-group">
            <label class="adm-label">Image</label>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   class="adm-input" style="padding:0.4rem 0.75rem;">
        </div>
    </div>
    <div class="col-span-2" style="display:flex; align-items:center; gap:0.5rem;">
        <input type="checkbox" name="is_published" id="evt_pub_{{ $event?->id ?? 'new' }}"
               class="adm-checkbox" value="1"
               {{ $event?->is_published ? 'checked' : '' }}>
        <label for="evt_pub_{{ $event?->id ?? 'new' }}" class="adm-label" style="margin:0; cursor:pointer;">
            Publish this event
        </label>
    </div>
</div>
