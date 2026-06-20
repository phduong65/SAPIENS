<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $smallPlates = MenuCategory::where('slug', 'small-plates')->first();
        $desserts = MenuCategory::where('slug', 'desserts')->first();

        if (! $smallPlates || ! $desserts) {
            return;
        }

        $items = [
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => 'Trio Potato Mille-Feuille',
                'name_vi' => 'Khoai Tây Ngàn Lớp',
                'description_en' => 'Smoked Iberico · Burrata · Bacon · Blueberry',
                'description_vi' => 'Iberico xông khói · Phô mai Burrata · Thịt xông khói · Việt quất',
                'price' => 195,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => "Hunter's Roll",
                'name_vi' => 'Bò Cuộn Thịt Xông Khói Phô Mai',
                'description_en' => 'Premium Beef · Bacon · Cheese',
                'description_vi' => 'Thịt bò thượng hạng · Thịt xông khói · Phô mai',
                'price' => 235,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => 'Not Squid',
                'name_vi' => 'Khoai Môn và Nấm Kim Châm',
                'description_en' => 'Taro · Enoki · Spice',
                'description_vi' => 'Khoai môn · Nấm kim châm · Hỗn hợp gia vị',
                'price' => 150,
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => 'Ocean Ruby',
                'name_vi' => 'Cá Ngừ Khô Mè Rang',
                'description_en' => 'Seared Tuna · Sesame · Sauerkraut',
                'description_vi' => 'Cá ngừ áp chảo · Mè rang · Bắp cải muối chua Sauerkraut',
                'price' => 285,
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => 'Prawn Nachos & Burrata',
                'name_vi' => 'Nachos Tôm & Burrata',
                'description_en' => 'Prawn · Charred Pepper',
                'description_vi' => 'Tôm · Ớt chuông cháy cạnh',
                'price' => 235,
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => 'Ocean Greens Salad',
                'name_vi' => 'Rong Nho',
                'description_en' => 'Sea Grapes · Sesame Glaze',
                'description_vi' => 'Rong nho · Sốt mè rang',
                'price' => 99,
                'is_featured' => false,
                'sort_order' => 6,
            ],
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => 'Octopus Yakitori',
                'name_vi' => 'Xiên Bạch Tuộc Cháy Cạnh',
                'description_en' => 'Madako · Wasabi · Sea Grapes',
                'description_vi' => 'Bạch tuộc Nhật · Mù tạt · Rong nho',
                'price' => 250,
                'is_featured' => true,
                'sort_order' => 7,
            ],
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => 'Aburi Salmon Pani Puri',
                'name_vi' => 'Cá Hồi Pani Puri',
                'description_en' => 'Salmon Tartare · Sesame Leaf',
                'description_vi' => 'Cá hồi Tartare · Lá mè',
                'price' => 195,
                'is_featured' => false,
                'sort_order' => 8,
            ],
            [
                'menu_category_id' => $smallPlates->id,
                'name_en' => 'Torched Salmon & Prawn Roulade',
                'name_vi' => 'Cá Hồi Cuộn Tôm Phô Mai',
                'description_en' => 'Salmon · Prawn · Burrata',
                'description_vi' => 'Cá hồi · Tôm · Phô mai Burrata',
                'price' => 270,
                'is_featured' => true,
                'sort_order' => 9,
            ],
            [
                'menu_category_id' => $desserts->id,
                'name_en' => 'Ivory Cloud',
                'name_vi' => 'Panna Cotta Chanh Dây',
                'description_en' => 'Passion Fruit Panna Cotta',
                'description_vi' => 'Panna Cotta chanh dây',
                'price' => 140,
                'is_featured' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::firstOrCreate(
                ['name_en' => $item['name_en']],
                $item
            );
        }
    }
}
