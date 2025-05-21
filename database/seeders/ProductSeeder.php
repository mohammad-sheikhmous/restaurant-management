<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttributeOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Product::truncate();
        ProductAttributeOption::truncate();
        Db::table('product_tags')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $products = [
            // pizza
            [
                'name' => ['en' => 'Margherita', 'ar' => 'مارغريتا'],
                'description' => ['en' => 'Classic pizza with tomato and cheese', 'ar' => 'بيتزا كلاسيكية مع الطماطم والجبن'],
                'image' => 'margherita.jpg',
                'is_simple' => false,
                'price' => 1000,
                'category_id' => 1,
            ],
            [
                'name' => ['en' => 'Pepperoni', 'ar' => 'بيبروني'],
                'description' => ['en' => 'Spicy pepperoni slices on melted cheese', 'ar' => 'شرائح البيبروني الحار على جبن ذائب'],
                'image' => 'pepperoni.jpg',
                'is_simple' => false,
                'price' => 2000,
                'category_id' => 1,
            ],
            [
                'name' => ['en' => 'Vegetarian', 'ar' => 'نباتية'],
                'description' => ['en' => 'Loaded with fresh vegetables', 'ar' => 'محشوة بالخضروات الطازجة'],
                'image' => 'vegetarian_pizza.jpg',
                'is_simple' => false,
                'price' => 3000,
                'category_id' => 1,
            ],
            // meshawi
            [
                'name' => ['en' => 'Mixed Grill', 'ar' => 'مشاوي مشكلة'],
                'description' => ['en' => 'A mix of grilled meats', 'ar' => 'تشكيلة من اللحوم المشوية'],
                'image' => 'mixed_grill.jpg',
                'is_simple' => false,
                'is_recommended' => true,
                'price' => 500,
                'category_id' => 2,
            ],
            [
                'name' => ['en' => 'Grilled Chicken', 'ar' => 'دجاج مشوي'],
                'description' => ['en' => 'Tender grilled chicken pieces', 'ar' => 'قطع دجاج طرية مشوية'],
                'image' => 'grilled_chicken.jpg',
                'is_simple' => false,
                'is_recommended' => true,
                'price' => 1500,
                'category_id' => 2,
            ],
            [
                'name' => ['en' => 'Kebab Skewers', 'ar' => 'أسياخ كباب'],
                'description' => ['en' => 'Juicy lamb kebabs on skewers', 'ar' => 'كباب لحم طري على أسياخ'],
                'image' => 'kebab_skewers.jpg',
                'is_simple' => false,
                'is_recommended' => true,
                'price' => 2200,
                'category_id' => 2,
            ],
            // seafood
            [
                'name' => ['en' => 'Grilled Salmon', 'ar' => 'سلمون مشوي'],
                'description' => ['en' => 'Fresh salmon grilled with herbs', 'ar' => 'سلمون طازج مشوي بالأعشاب'],
                'image' => 'grilled_salmon.jpg',
                'is_simple' => true,
                'price' => 4000,
                'category_id' => 3,
            ],
            [
                'name' => ['en' => 'Fried Shrimp', 'ar' => 'جمبري مقلي'],
                'description' => ['en' => 'Golden fried shrimp with sauce', 'ar' => 'جمبري مقلي ذهبي مع صوص'],
                'image' => 'fried_shrimp.jpg',
                'is_simple' => true,
                'price' => 3400,
                'category_id' => 3,
            ],
            [
                'name' => ['en' => 'Seafood Platter', 'ar' => 'طبق بحري مشكل'],
                'description' => ['en' => 'Assortment of seafood items', 'ar' => 'تشكيلة متنوعة من المأكولات البحرية'],
                'image' => 'seafood_platter.jpg',
                'is_simple' => true,
                'is_recommended' => true,
                'price' => 2900,
                'category_id' => 3,
            ],
            // appetizers
            [
                'name' => ['en' => 'Hummus', 'ar' => 'حمص'],
                'description' => ['en' => 'Creamy chickpea dip', 'ar' => 'حمص مهروس كريمي'],
                'image' => 'hummus.jpg',
                'is_simple' => true,
                'price' => 500,
                'category_id' => 4,
            ],
            [
                'name' => ['en' => 'Stuffed Grape Leaves', 'ar' => 'ورق عنب'],
                'description' => ['en' => 'Rice-filled grape leaves', 'ar' => 'أوراق عنب محشوة بالأرز'],
                'image' => 'stuffed_grape_leaves.jpg',
                'is_simple' => true,
                'price' => 750,
                'category_id' => 4,
            ],
            [
                'name' => ['en' => 'Falafel', 'ar' => 'فلافل'],
                'description' => ['en' => 'Crispy fried chickpea balls', 'ar' => 'كرات حمص مقلية مقرمشة'],
                'image' => 'falafel.jpg',
                'is_simple' => true,
                'is_recommended' => true,
                'price' => 600,
                'category_id' => 4,
            ],
            // drinks
            [
                'name' => ['en' => 'Orange Juice', 'ar' => 'عصير برتقال'],
                'description' => ['en' => 'Freshly squeezed orange juice', 'ar' => 'عصير برتقال طازج ومعصور'],
                'image' => 'orange_juice.jpg',
                'is_simple' => true,
                'price' => 850,
                'category_id' => 5,
            ],
            [
                'name' => ['en' => 'Cola', 'ar' => 'كولا'],
                'description' => ['en' => 'Chilled cola drink', 'ar' => 'مشروب كولا بارد'],
                'image' => 'cola.jpg',
                'is_simple' => true,
                'price' => 1000,
                'category_id' => 5,
            ],
            [
                'name' => ['en' => 'Mint Lemonade', 'ar' => 'ليمون بالنعناع'],
                'description' => ['en' => 'Refreshing mint and lemon drink', 'ar' => 'مشروب منعش من الليمون والنعناع'],
                'image' => 'mint_lemonade.jpg',
                'is_simple' => true,
                'price' => 800,
                'category_id' => 5,
            ],
            // shawrma
            [
                'name' => ['en' => 'Chicken Shawarma', 'ar' => 'شاورما دجاج'],
                'description' => ['en' => 'Sliced grilled chicken with garlic sauce', 'ar' => 'شرائح دجاج مشوي مع صلصة الثوم'],
                'image' => 'chicken_shawarma.jpg',
                'is_simple' => false,
                'price' => 2000,
                'category_id' => 6,
            ],
            [
                'name' => ['en' => 'Beef Shawarma', 'ar' => 'شاورما لحم'],
                'description' => ['en' => 'Tender beef shawarma wrap', 'ar' => 'شاورما لحم ملفوفة طرية'],
                'image' => 'beef_shawarma.jpg',
                'is_simple' => false,
                'price' => 2300,
                'category_id' => 6,
            ],
            [
                'name' => ['en' => 'Shawarma Plate', 'ar' => 'طبق شاورما'],
                'description' => ['en' => 'Shawarma served with fries and salad', 'ar' => 'شاورما مع بطاطس وسلطة'],
                'image' => 'shawarma_plate.jpg',
                'is_simple' => false,
                'price' => 2500,
                'category_id' => 6,
            ],
        ];
        $product_attribute_options = [
            // Margherita
            ['product_id' => 1, 'attribute_option_id' => 5, 'extra_price' => 800],
            ['product_id' => 1, 'attribute_option_id' => 6, 'extra_price' => 1000],
            ['product_id' => 1, 'attribute_option_id' => 7, 'extra_price' => 1200],
            ['product_id' => 1, 'attribute_option_id' => 8, 'extra_price' => 100],
            ['product_id' => 1, 'attribute_option_id' => 9, 'extra_price' => 75],
            ['product_id' => 1, 'attribute_option_id' => 10, 'extra_price' => 50],
            ['product_id' => 1, 'attribute_option_id' => 11, 'extra_price' => 50],
            ['product_id' => 1, 'attribute_option_id' => 12, 'extra_price' => 90],
            ['product_id' => 1, 'attribute_option_id' => 13, 'extra_price' => 102],
            ['product_id' => 1, 'attribute_option_id' => 14, 'extra_price' => 75],

            // Pepperoni
            ['product_id' => 2, 'attribute_option_id' => 5, 'extra_price' => 1800],
            ['product_id' => 2, 'attribute_option_id' => 6, 'extra_price' => 2000],
            ['product_id' => 2, 'attribute_option_id' => 7, 'extra_price' => 2350],
            ['product_id' => 2, 'attribute_option_id' => 8, 'extra_price' => 50],
            ['product_id' => 2, 'attribute_option_id' => 9, 'extra_price' => 50],
            ['product_id' => 2, 'attribute_option_id' => 10, 'extra_price' => 75],
            ['product_id' => 2, 'attribute_option_id' => 11, 'extra_price' => 90],
            ['product_id' => 2, 'attribute_option_id' => 12, 'extra_price' => 85],
            ['product_id' => 2, 'attribute_option_id' => 14, 'extra_price' => 80],

            // Vegetarian
            ['product_id' => 3, 'attribute_option_id' => 5, 'extra_price' => 3000],
            ['product_id' => 3, 'attribute_option_id' => 6, 'extra_price' => 3240],
            ['product_id' => 3, 'attribute_option_id' => 7, 'extra_price' => 3450],
            ['product_id' => 3, 'attribute_option_id' => 8, 'extra_price' => 50],
            ['product_id' => 3, 'attribute_option_id' => 9, 'extra_price' => 60],
            ['product_id' => 3, 'attribute_option_id' => 10, 'extra_price' => 70],
            ['product_id' => 3, 'attribute_option_id' => 11, 'extra_price' => 55],
            ['product_id' => 3, 'attribute_option_id' => 12, 'extra_price' => 100],

            // Mixed Grill
            ['product_id' => 4, 'attribute_option_id' => 1, 'extra_price' => 500],
            ['product_id' => 4, 'attribute_option_id' => 2, 'extra_price' => 650],
            ['product_id' => 4, 'attribute_option_id' => 3, 'extra_price' => 700],
            ['product_id' => 4, 'attribute_option_id' => 4, 'extra_price' => 800],
            ['product_id' => 4, 'attribute_option_id' => 8, 'extra_price' => 50],
            ['product_id' => 4, 'attribute_option_id' => 9, 'extra_price' => 40],
            ['product_id' => 4, 'attribute_option_id' => 10, 'extra_price' => 75],

            // Grilled Chicken
            ['product_id' => 5, 'attribute_option_id' => 2, 'extra_price' => 1350],
            ['product_id' => 5, 'attribute_option_id' => 3, 'extra_price' => 1500],
            ['product_id' => 5, 'attribute_option_id' => 4, 'extra_price' => 1800],
            ['product_id' => 5, 'attribute_option_id' => 8, 'extra_price' => 90],
            ['product_id' => 5, 'attribute_option_id' => 9, 'extra_price' => 85],
            ['product_id' => 5, 'attribute_option_id' => 10, 'extra_price' => 80],

            // Kebab Skewers
            ['product_id' => 6, 'attribute_option_id' => 1, 'extra_price' => 2200],
            ['product_id' => 6, 'attribute_option_id' => 2, 'extra_price' => 2500],
            ['product_id' => 6, 'attribute_option_id' => 8, 'extra_price' => 50],
            ['product_id' => 6, 'attribute_option_id' => 9, 'extra_price' => 80],
            ['product_id' => 6, 'attribute_option_id' => 10, 'extra_price' => 90],

            // Chicken Shawarma
            ['product_id' => 16, 'attribute_option_id' => 5, 'extra_price' => 2000],
            ['product_id' => 16, 'attribute_option_id' => 6, 'extra_price' => 2200],
            ['product_id' => 16, 'attribute_option_id' => 7, 'extra_price' => 2400],
            ['product_id' => 16, 'attribute_option_id' => 13, 'extra_price' => 80],
            ['product_id' => 16, 'attribute_option_id' => 14, 'extra_price' => 100],

            // Beef Shawarma
            ['product_id' => 17, 'attribute_option_id' => 3, 'extra_price' => 2300],
            ['product_id' => 17, 'attribute_option_id' => 4, 'extra_price' => 2600],
            ['product_id' => 17, 'attribute_option_id' => 13, 'extra_price' => 60],
            ['product_id' => 17, 'attribute_option_id' => 14, 'extra_price' => 90],

            // Shawarma Plate
            ['product_id' => 18, 'attribute_option_id' => 5, 'extra_price' => 2500],
            ['product_id' => 18, 'attribute_option_id' => 7, 'extra_price' => 2800],
            ['product_id' => 18, 'attribute_option_id' => 11, 'extra_price' => 50],
            ['product_id' => 18, 'attribute_option_id' => 12, 'extra_price' => 40],
            ['product_id' => 18, 'attribute_option_id' => 13, 'extra_price' => 70],
            ['product_id' => 18, 'attribute_option_id' => 14, 'extra_price' => 100],
        ];
        $products_tags = [
            ['product_id' => 1, 'tag_id' => 1],
            ['product_id' => 1, 'tag_id' => 2],
            ['product_id' => 1, 'tag_id' => 3],
            ['product_id' => 1, 'tag_id' => 8],

            ['product_id' => 2, 'tag_id' => 1],
            ['product_id' => 2, 'tag_id' => 3],
            ['product_id' => 2, 'tag_id' => 8],

            ['product_id' => 3, 'tag_id' => 3],
            ['product_id' => 3, 'tag_id' => 4],
            ['product_id' => 3, 'tag_id' => 5],
            ['product_id' => 3, 'tag_id' => 8],

            ['product_id' => 4, 'tag_id' => 3],
            ['product_id' => 4, 'tag_id' => 9],

            ['product_id' => 5, 'tag_id' => 1],
            ['product_id' => 5, 'tag_id' => 9],

            ['product_id' => 6, 'tag_id' => 9],

            ['product_id' => 7, 'tag_id' => 4],
            ['product_id' => 7, 'tag_id' => 6],

            ['product_id' => 8, 'tag_id' => 4],
            ['product_id' => 8, 'tag_id' => 8],

            ['product_id' => 9, 'tag_id' => 4],
            ['product_id' => 9, 'tag_id' => 6],

            ['product_id' => 11, 'tag_id' => 4],
            ['product_id' => 11, 'tag_id' => 5],

            ['product_id' => 13, 'tag_id' => 7],
            ['product_id' => 13, 'tag_id' => 4],
            ['product_id' => 13, 'tag_id' => 3],

            ['product_id' => 14, 'tag_id' => 3],
            ['product_id' => 14, 'tag_id' => 7],

            ['product_id' => 15, 'tag_id' => 3],
            ['product_id' => 15, 'tag_id' => 7],

            ['product_id' => 16, 'tag_id' => 1],
            ['product_id' => 16, 'tag_id' => 8],
            ['product_id' => 16, 'tag_id' => 9],

            ['product_id' => 17, 'tag_id' => 4],
            ['product_id' => 17, 'tag_id' => 8],
            ['product_id' => 17, 'tag_id' => 9],

            ['product_id' => 18, 'tag_id' => 8],
            ['product_id' => 18, 'tag_id' => 9],
        ];

        foreach ($products as $product)
            Product::create($product);

        foreach ($product_attribute_options as $option)
            ProductAttributeOption::create($option);

        foreach ($products_tags as $tag)
            DB::table('product_tags')->insert($tag);
    }
}
