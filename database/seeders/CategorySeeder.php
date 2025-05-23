<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $categories = [
            ['name' => ['en' => 'pizza', 'ar' => 'بيتزا'], 'image' => 'pizza.jpg'],
            ['name' => ['en' => 'meshawi', 'ar' => 'مشاوي'], 'image' => 'meshawi.jpg'],
            ['name' => ['en' => 'seafood', 'ar' => 'بحري'], 'image' => 'seafood.jpg'],
            ['name' => ['en' => 'appetizers', 'ar' => 'مقبلات'], 'image' => 'appetizers.jpg'],
            ['name' => ['en' => 'drinks', 'ar' => 'مشروبات'], 'image' => 'drinks.jpg'],
            ['name' => ['en' => 'shawrma', 'ar' => 'شاورما'], 'image' => 'shawrma.jpg'],
        ];
        foreach ($categories as $category)
            Category::create($category);
    }
}
