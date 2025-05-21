<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Attribute::truncate();
        AttributeOption::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $attributes = [
            ['name' => ['en' => 'pieces number', 'ar' => 'عدد القطع'], 'type' => 'basic'],
            ['name' => ['en' => 'size', 'ar' => 'الحجم'], 'type' => 'basic'],
            ['name' => ['en' => 'sauce', 'ar' => 'الصوص'], 'type' => 'additional'],
            ['name' => ['en' => 'addons', 'ar' => 'إضافات'], 'type' => 'additional'],
        ];
        $attribute_options = [
            ['name' => ['en' => '2 pieces', 'ar' => 'قطعتان'], 'attribute_id' => 1],
            ['name' => ['en' => '4 pieces', 'ar' => 'اربع قطع'], 'attribute_id' => 1],
            ['name' => ['en' => '6 pieces', 'ar' => 'ست قطع'], 'attribute_id' => 1],
            ['name' => ['en' => '8 pieces', 'ar' => 'ثماني قطع'], 'attribute_id' => 1],
            ['name' => ['en' => 'small', 'ar' => 'صغير'], 'attribute_id' => 2],
            ['name' => ['en' => 'middle', 'ar' => 'وسط'], 'attribute_id' => 2],
            ['name' => ['en' => 'big', 'ar' => 'كبير'], 'attribute_id' => 2],
            ['name' => ['en' => 'Fiery Peri Peri', 'ar' => 'بيري بيري حار'], 'attribute_id' => 3],
            ['name' => ['en' => 'Cool Ranch', 'ar' => 'كول رانش'], 'attribute_id' => 3],
            ['name' => ['en' => 'Bold BBQ', 'ar' => 'باربكيو شهي'], 'attribute_id' => 3],
            ['name' => ['en' => 'Pepperoni', 'ar' => 'ببروني'], 'attribute_id' => 4],
            ['name' => ['en' => 'Chicken Fajita', 'ar' => 'دجاج فاهيتا'], 'attribute_id' => 4],
            ['name' => ['en' => 'Green Chilies', 'ar' => 'فلفل أخضر حار'], 'attribute_id' => 4],
            ['name' => ['en' => 'Ground Beef', 'ar' => 'لحم بقري'], 'attribute_id' => 4],
        ];
        foreach ($attributes as $attribute)
            Attribute::create($attribute);

        foreach ($attribute_options as $option)
            AttributeOption::create($option);
    }
}
