<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;

class MenuController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::active()
            ->ordered()
            ->with(['activeItems'])
            ->get();

        $foodCategories = $categories->where('type', 'food');
        $drinkCategories = $categories->where('type', 'drink');

        return view('client.menu.index', compact('foodCategories', 'drinkCategories'));
    }
}
