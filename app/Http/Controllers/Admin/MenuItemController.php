<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $items = MenuItem::with('category')->orderBy('menu_category_id')->orderBy('sort_order')->paginate(20);
        $categories = MenuCategory::active()->ordered()->get();

        return view('admin.menu-items.index', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name_en' => 'required|string|max:200',
            'name_vi' => 'required|string|max:200',
            'description_en' => 'nullable|string',
            'description_vi' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        MenuItem::create($data);

        return redirect()->route('admin.menu-items.index')->with('success', 'Đã thêm món ăn thành công.');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name_en' => 'required|string|max:200',
            'name_vi' => 'required|string|max:200',
            'description_en' => 'nullable|string',
            'description_vi' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->hasFile('image')) {
            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        $menuItem->update($data);

        return redirect()->route('admin.menu-items.index')->with('success', 'Đã cập nhật món ăn.');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image_path) {
            Storage::disk('public')->delete($menuItem->image_path);
        }
        $menuItem->delete();

        return redirect()->route('admin.menu-items.index')->with('success', 'Đã xoá món ăn.');
    }
}
