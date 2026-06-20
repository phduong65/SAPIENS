<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Small Plates', 'slug' => 'small-plates', 'type' => 'food', 'sort_order' => 1],
            ['name' => 'Main Dishes', 'slug' => 'main-dishes', 'type' => 'food', 'sort_order' => 2],
            ['name' => 'Sharing Dishes', 'slug' => 'sharing-dishes', 'type' => 'food', 'sort_order' => 3],
            ['name' => 'Desserts', 'slug' => 'desserts', 'type' => 'food', 'sort_order' => 4],
            ['name' => 'Signature Mocktails', 'slug' => 'signature-mocktails', 'type' => 'drink', 'sort_order' => 5],
            ['name' => 'Highballs', 'slug' => 'highballs', 'type' => 'drink', 'sort_order' => 6],
            ['name' => 'Sake', 'slug' => 'sake', 'type' => 'drink', 'sort_order' => 7],
            ['name' => 'Wine', 'slug' => 'wine', 'type' => 'drink', 'sort_order' => 8],
            ['name' => 'Non-Alcoholic', 'slug' => 'non-alcoholic', 'type' => 'drink', 'sort_order' => 9],
        ];

        foreach ($categories as $cat) {
            MenuCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
