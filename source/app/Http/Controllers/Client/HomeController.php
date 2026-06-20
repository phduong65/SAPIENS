<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;

class HomeController extends Controller
{
    public function index()
    {
        $featuredItems = MenuItem::with('category')
            ->active()
            ->featured()
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('client.home', compact('featuredItems'));
    }
}
