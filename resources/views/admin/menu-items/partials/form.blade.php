<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <div class="adm-form-group">
            <label class="adm-label">Category <span style="color:var(--adm-danger);">*</span></label>
            <select name="menu_category_id" class="adm-input adm-select" required>
                <option value="">Select...</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                        {{ (isset($item) && $item?->menu_category_id == $cat->id) ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Name (EN) <span style="color:var(--adm-danger);">*</span></label>
            <input type="text" name="name_en" class="adm-input" required
                   value="{{ $item?->name_en ?? old('name_en') }}">
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Name (VI) <span style="color:var(--adm-danger);">*</span></label>
            <input type="text" name="name_vi" class="adm-input" required
                   value="{{ $item?->name_vi ?? old('name_vi') }}">
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Description (EN)</label>
            <textarea name="description_en" class="adm-input adm-textarea" rows="2">{{ $item?->description_en ?? old('description_en') }}</textarea>
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Description (VI)</label>
            <textarea name="description_vi" class="adm-input adm-textarea" rows="2">{{ $item?->description_vi ?? old('description_vi') }}</textarea>
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Price (000đ) <span style="color:var(--adm-danger);">*</span></label>
            <input type="number" name="price" class="adm-input" min="0" required
                   value="{{ $item?->price ?? old('price') }}">
        </div>
    </div>
    <div>
        <div class="adm-form-group">
            <label class="adm-label">Sort Order</label>
            <input type="number" name="sort_order" class="adm-input" min="0"
                   value="{{ $item?->sort_order ?? old('sort_order', 0) }}">
        </div>
    </div>
    <div class="col-span-2">
        <div class="adm-form-group">
            <label class="adm-label">Image</label>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="adm-input" style="padding:0.4rem 0.75rem;">
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:0.5rem;">
        <input type="checkbox" name="is_featured" id="feat_{{ $item?->id ?? 'new' }}"
               class="adm-checkbox" value="1"
               {{ $item?->is_featured ? 'checked' : '' }}>
        <label for="feat_{{ $item?->id ?? 'new' }}" class="adm-label" style="margin:0; cursor:pointer;">Featured</label>
    </div>
    <div style="display:flex; align-items:center; gap:0.5rem;">
        <input type="checkbox" name="is_active" id="act_{{ $item?->id ?? 'new' }}"
               class="adm-checkbox" value="1"
               {{ ($item === null || $item->is_active) ? 'checked' : '' }}>
        <label for="act_{{ $item?->id ?? 'new' }}" class="adm-label" style="margin:0; cursor:pointer;">Active</label>
    </div>
</div>
