@extends('layouts.admin')

@section('title', 'Menu Items')
@section('breadcrumb', 'Menu Items')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h1 class="font-display" style="font-size:1.75rem; color:#E5D9C8;">Menu Items</h1>
    <button onclick="document.getElementById('create-modal').style.display='flex'"
            class="btn-gold text-xs">
        + Add Item
    </button>
</div>

<div style="background-color:#242420; border:1px solid #2E2E2A;">
    <div class="overflow-x-auto">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background-color:#1A1A18;">
                    <th class="admin-th">Image</th>
                    <th class="admin-th">Name</th>
                    <th class="admin-th">Category</th>
                    <th class="admin-th">Price</th>
                    <th class="admin-th">Featured</th>
                    <th class="admin-th">Active</th>
                    <th class="admin-th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr style="border-bottom:1px solid #2E2E2A;">
                    <td class="admin-td">
                        @if($item->image_path)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name_en }}"
                             style="width:48px; height:36px; object-fit:cover; background:#1A1A18;">
                        @else
                        <div style="width:48px; height:36px; background:#1A1A18; display:flex; align-items:center; justify-content:center;">
                            <span style="color:#3A3A35; font-size:0.6rem;">—</span>
                        </div>
                        @endif
                    </td>
                    <td class="admin-td">
                        <p style="color:#C9B99A; font-size:0.875rem;">{{ $item->name_en }}</p>
                        <p style="color:#8C7E6A; font-size:0.75rem;">{{ $item->name_vi }}</p>
                    </td>
                    <td class="admin-td" style="color:#8C7E6A; font-size:0.8rem;">{{ $item->category->name ?? '—' }}</td>
                    <td class="admin-td" style="color:#B8925A; font-size:0.875rem;">{{ $item->formatted_price }}</td>
                    <td class="admin-td text-center">
                        <span style="color:{{ $item->is_featured ? '#34d399' : '#3A3A35' }};">
                            {{ $item->is_featured ? '✓' : '—' }}
                        </span>
                    </td>
                    <td class="admin-td text-center">
                        <span style="color:{{ $item->is_active ? '#34d399' : '#ef4444' }};">
                            {{ $item->is_active ? '✓' : '✗' }}
                        </span>
                    </td>
                    <td class="admin-td">
                        <div class="flex gap-3">
                            <button onclick="openEditModal({{ $item->id }}, @json($item->only(['menu_category_id','name_en','name_vi','description_en','description_vi','price','is_featured','is_active','sort_order']))"
                                    style="color:#B8925A; font-size:0.75rem; background:none; border:none; cursor:pointer; text-transform:uppercase; letter-spacing:0.05em; padding:0;"
                                    class="hover:underline">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.menu-items.destroy', $item) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($item->name_en) }}?')">
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
                    <td colspan="7" class="admin-td text-center" style="color:#3A3A35; padding:3rem;">
                        No menu items yet. <button onclick="document.getElementById('create-modal').style.display='flex'" style="color:#B8925A; background:none; border:none; cursor:pointer;" class="hover:underline">Add one.</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($items->hasPages())
<div class="mt-6">{{ $items->links() }}</div>
@endif

{{-- Create Modal --}}
<div id="create-modal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#242420; border:1px solid #2E2E2A; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; padding:2rem;">
        <div class="flex justify-between items-center mb-6">
            <h3 style="color:#E5D9C8; font-size:1.1rem;">Add Menu Item</h3>
            <button onclick="document.getElementById('create-modal').style.display='none'"
                    style="color:#8C7E6A; background:none; border:none; cursor:pointer; font-size:1.25rem;">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.menu-items.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.menu-items.partials.form', ['item' => null, 'categories' => $categories])
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-gold text-xs">Save Item</button>
                <button type="button" onclick="document.getElementById('create-modal').style.display='none'"
                        class="btn-outline text-xs">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-modal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#242420; border:1px solid #2E2E2A; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; padding:2rem;">
        <div class="flex justify-between items-center mb-6">
            <h3 style="color:#E5D9C8; font-size:1.1rem;">Edit Menu Item</h3>
            <button onclick="document.getElementById('edit-modal').style.display='none'"
                    style="color:#8C7E6A; background:none; border:none; cursor:pointer; font-size:1.25rem;">✕</button>
        </div>
        <form id="edit-form" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.menu-items.partials.form', ['item' => null, 'categories' => $categories])
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-gold text-xs">Update Item</button>
                <button type="button" onclick="document.getElementById('edit-modal').style.display='none'"
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
function openEditModal(id, data) {
    var form = document.getElementById('edit-form');
    form.action = '/admin/menu-items/' + id;

    Object.entries(data).forEach(function([key, val]) {
        var el = form.elements[key];
        if (!el) return;
        if (el.type === 'checkbox') { el.checked = !!val; }
        else { el.value = val !== null ? val : ''; }
    });

    document.getElementById('edit-modal').style.display = 'flex';
}
</script>
@endpush

@endsection
