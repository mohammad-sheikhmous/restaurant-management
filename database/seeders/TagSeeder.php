<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Tag::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $tags = [
            ['name' => ["en" => "Spicy", "ar" => "حار"], 'icon' => '🌶'],
            ['name' => ["en" => "Cheesy", "ar" => "بالجبنة"], 'icon' => '🧀'],
            ['name' => ["en" => "Popular", "ar" => "شائع"], 'icon' => '📈'],
            ['name' => ["en" => "New", "ar" => "جديد"], 'icon' => '🆕'],
            ['name' => ["en" => "Vegetarian", "ar" => "نباتي"], 'icon' => '🥬'],
            ['name' => ["en" => "Not Vegetarian", "ar" => "غير نباتي"], 'icon' => '🍗'],
            ['name' => ["en" => "Kids", "ar" => "أطفال"], 'icon' => '🚼'],
            ['name' => ["en" => "Saucy", "ar" => "بالصوص"], 'icon' => '🍛'],
            ['name' => ["en" => "Extra Meat", "ar" => "لحم إضافي"], 'icon' => '🥩'],
        ];
        foreach ($tags as $tag)
            Tag::create($tag);
    }
}
