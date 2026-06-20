<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <label class="modal-label">Title <span style="color:#B8925A;">*</span></label>
        <input type="text" name="title" class="form-input" required
               value="{{ $event?->title ?? old('title') }}">
    </div>
    <div>
        <label class="modal-label">Type <span style="color:#B8925A;">*</span></label>
        <select name="type" class="form-input" required>
            <option value="" style="background:#242420;">Select type...</option>
            @foreach(['event' => 'Event', 'guest_shift' => 'Guest Shift', 'workshop' => 'Workshop', 'special_night' => 'Special Night', 'community' => 'Community'] as $val => $label)
            <option value="{{ $val }}" style="background:#242420;"
                    {{ ($event?->type === $val) ? 'selected' : '' }}>
                {{ $label }}
            </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="modal-label">Date <span style="color:#B8925A;">*</span></label>
        <input type="date" name="event_date" class="form-input" required
               value="{{ $event?->event_date?->format('Y-m-d') ?? old('event_date') }}">
    </div>
    <div>
        <label class="modal-label">Time <span style="color:#B8925A;">*</span></label>
        <input type="time" name="event_time" class="form-input" required
               value="{{ $event?->event_time ?? old('event_time') }}">
    </div>
    <div class="col-span-2">
        <label class="modal-label">Description <span style="color:#B8925A;">*</span></label>
        <textarea name="description" class="form-input" rows="4" required>{{ $event?->description ?? old('description') }}</textarea>
    </div>
    <div class="col-span-2">
        <label class="modal-label">Image</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
               class="form-input" style="padding:0.5rem;">
    </div>
    <div class="flex items-center gap-2 col-span-2">
        <input type="checkbox" name="is_published" id="evt_pub_{{ $event?->id ?? 'new' }}"
               value="1" style="accent-color:#B8925A;"
               {{ $event?->is_published ? 'checked' : '' }}>
        <label for="evt_pub_{{ $event?->id ?? 'new' }}" style="color:#8C7E6A; font-size:0.8rem; cursor:pointer;">
            Publish this event
        </label>
    </div>
</div>

<style>
.modal-label { display:block; color:#8C7E6A; font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.4rem; }
</style>
