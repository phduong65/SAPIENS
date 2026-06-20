<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <label class="modal-label">Category <span style="color:#B8925A;">*</span></label>
        <select name="menu_category_id" class="form-input" required>
            <option value="" style="background:#242420;">Select...</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" style="background:#242420;"
                    {{ (isset($item) && $item?->menu_category_id == $cat->id) ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="modal-label">Name (EN) <span style="color:#B8925A;">*</span></label>
        <input type="text" name="name_en" class="form-input" required
               value="{{ $item?->name_en ?? old('name_en') }}">
    </div>
    <div>
        <label class="modal-label">Name (VI) <span style="color:#B8925A;">*</span></label>
        <input type="text" name="name_vi" class="form-input" required
               value="{{ $item?->name_vi ?? old('name_vi') }}">
    </div>
    <div>
        <label class="modal-label">Description (EN)</label>
        <textarea name="description_en" class="form-input" rows="2">{{ $item?->description_en ?? old('description_en') }}</textarea>
    </div>
    <div>
        <label class="modal-label">Description (VI)</label>
        <textarea name="description_vi" class="form-input" rows="2">{{ $item?->description_vi ?? old('description_vi') }}</textarea>
    </div>
    <div>
        <label class="modal-label">Price (000đ) <span style="color:#B8925A;">*</span></label>
        <input type="number" name="price" class="form-input" min="0" required
               value="{{ $item?->price ?? old('price') }}">
    </div>
    <div>
        <label class="modal-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-input" min="0"
               value="{{ $item?->sort_order ?? old('sort_order', 0) }}">
    </div>
    <div class="col-span-2">
        <label class="modal-label">Image</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
               class="form-input" style="padding:0.5rem;">
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_featured" id="feat_{{ $item?->id ?? 'new' }}"
               value="1" style="accent-color:#B8925A;"
               {{ $item?->is_featured ? 'checked' : '' }}>
        <label for="feat_{{ $item?->id ?? 'new' }}" class="modal-label" style="margin:0; cursor:pointer;">Featured</label>
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="act_{{ $item?->id ?? 'new' }}"
               value="1" style="accent-color:#B8925A;"
               {{ ($item === null || $item->is_active) ? 'checked' : '' }}>
        <label for="act_{{ $item?->id ?? 'new' }}" class="modal-label" style="margin:0; cursor:pointer;">Active</label>
    </div>
</div>

<style>
.modal-label { display:block; color:#8C7E6A; font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.4rem; }
</style>
