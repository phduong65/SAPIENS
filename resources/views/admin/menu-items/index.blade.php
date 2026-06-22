@extends('layouts.admin')

@section('title', 'Menu Items')
@section('breadcrumb', 'Menu Items')

@section('content')

    <div class="adm-page-header" style="display:flex; align-items:center; justify-content:space-between;">
        <h1 class="adm-page-title">Menu Items</h1>
        <button onclick="document.getElementById('create-modal').style.display='flex'"
            class="adm-btn adm-btn-primary adm-btn-sm">
            + Add Item
        </button>
    </div>

    <div class="adm-card">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th class="adm-th">Image</th>
                        <th class="adm-th">Name</th>
                        <th class="adm-th">Category</th>
                        <th class="adm-th">Price</th>
                        <th class="adm-th">Featured</th>
                        <th class="adm-th">Active</th>
                        <th class="adm-th">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="adm-tr">
                            <td class="adm-td">
                                @if ($item->image_path)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name_en }}" class="adm-thumb">
                                @else
                                    <div class="adm-thumb"
                                        style="background:var(--adm-bg); display:flex; align-items:center; justify-content:center;">
                                        <span style="color:var(--adm-border); font-size:0.6rem;">—</span>
                                    </div>
                                @endif
                            </td>
                            <td class="adm-td">
                                <p style="font-weight:500;">{{ $item->name_en }}</p>
                                <p style="color:var(--adm-muted); font-size:0.75rem;">{{ $item->name_vi }}</p>
                            </td>
                            <td class="adm-td" style="color:var(--adm-muted); font-size:0.8rem;">
                                {{ $item->category->name ?? '—' }}</td>
                            <td class="adm-td">{{ $item->formatted_price }}</td>
                            <td class="adm-td" style="text-align:center;">
                                @if ($item->is_featured)
                                    <span class="adm-badge adm-badge-blue">Yes</span>
                                @else
                                    <span style="color:var(--adm-border);">—</span>
                                @endif
                            </td>
                            <td class="adm-td" style="text-align:center;">
                                @if ($item->is_active)
                                    <span class="adm-badge adm-badge-ok">Active</span>
                                @else
                                    <span class="adm-badge adm-badge-err">Off</span>
                                @endif
                            </td>
                            <td class="adm-td">
                                <div style="display:flex; gap:0.5rem;">
                                    <button onclick="openEditModal({{ $item->id }}, JSON.parse(this.dataset.item))"
                                        data-item="{{ json_encode($item->only(['menu_category_id','name_en','name_vi','description_en','description_vi','price','is_featured','is_active','sort_order'])) }}"
                                        class="adm-btn adm-btn-ghost adm-btn-sm">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.menu-items.destroy', $item) }}"
                                        onsubmit="return confirm('Delete {{ addslashes($item->name_en) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="adm-td"
                                style="text-align:center; color:var(--adm-muted); padding:3rem;">
                                No menu items yet.
                                <button onclick="document.getElementById('create-modal').style.display='flex'"
                                    class="adm-btn adm-btn-ghost adm-btn-sm" style="margin-left:0.5rem;">
                                    Add one.
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($items->hasPages())
        <div class="mt-6">{{ $items->links() }}</div>
    @endif

    {{-- Create Modal --}}
    <div id="create-modal" class="adm-modal-backdrop" style="display:none;">
        <div class="adm-modal">
            <div class="adm-modal-header">
                <span class="adm-modal-title">Add Menu Item</span>
                <button onclick="document.getElementById('create-modal').style.display='none'"
                    class="adm-btn adm-btn-ghost adm-btn-icon" aria-label="Close">✕</button>
            </div>
            <form method="POST" action="{{ route('admin.menu-items.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="adm-modal-body">
                    @include('admin.menu-items.partials.form', [
                        'item' => null,
                        'categories' => $categories,
                    ])
                </div>
                <div class="adm-modal-footer">
                    <button type="button" onclick="document.getElementById('create-modal').style.display='none'"
                        class="adm-btn adm-btn-ghost adm-btn-sm">Cancel</button>
                    <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">Save Item</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="edit-modal" class="adm-modal-backdrop" style="display:none;">
        <div class="adm-modal">
            <div class="adm-modal-header">
                <span class="adm-modal-title">Edit Menu Item</span>
                <button onclick="document.getElementById('edit-modal').style.display='none'"
                    class="adm-btn adm-btn-ghost adm-btn-icon" aria-label="Close">✕</button>
            </div>
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="adm-modal-body">
                    @include('admin.menu-items.partials.form', [
                        'item' => null,
                        'categories' => $categories,
                    ])
                </div>
                <div class="adm-modal-footer">
                    <button type="button" onclick="document.getElementById('edit-modal').style.display='none'"
                        class="adm-btn adm-btn-ghost adm-btn-sm">Cancel</button>
                    <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">Update Item</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openEditModal(id, data) {
                var form = document.getElementById('edit-form');
                form.action = '/admin/menu-items/' + id;

                Object.entries(data).forEach(function([key, val]) {
                    var el = form.elements[key];
                    if (!el) return;
                    if (el.type === 'checkbox') {
                        el.checked = !!val;
                    } else {
                        el.value = val !== null ? val : '';
                    }
                });

                document.getElementById('edit-modal').style.display = 'flex';
            }
        </script>
    @endpush

@endsection
