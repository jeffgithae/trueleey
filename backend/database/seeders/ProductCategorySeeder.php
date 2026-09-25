<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    // Placeholder categories for local development; production has its own list.
    public function run()
    {
        if (DB::table('ProductCategories')->exists()) {
            return;
        }

        foreach (['Hair Care', 'Skin Care', 'Nail Care', 'Makeup', 'Fragrance', 'Tools & Equipment'] as $name) {
            DB::table('ProductCategories')->insert(['name' => $name]);
        }
    }
}
