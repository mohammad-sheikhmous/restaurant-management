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
            ['name' => ["en" => "Spicy", "ar" => "حار"]],
            ['name' => ["en" => "Cheesy", "ar" => "بالجبنة"]],
            ['name' => ["en" => "Popular", "ar" => "شائع"], 'status' => 0],
            ['name' => ["en" => "New", "ar" => "جديد"]],
            ['name' => ["en" => "Vegetarian", "ar" => "نباتي"]],
            ['name' => ["en" => "Not Vegetarian", "ar" => "غير نباتي"]],
            ['name' => ["en" => "Kids", "ar" => "أطفال"], 'status' => 0],
            ['name' => ["en" => "Saucy", "ar" => "بالصوص"]],
            ['name' => ["en" => "Extra Meat", "ar" => "لحم إضافي"]],
        ];
        foreach ($tags as $tag)
            Tag::create($tag);
    }
}
