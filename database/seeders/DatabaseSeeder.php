<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        $this->call([
            SettingSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            DeliveryZoneSeeder::class,
            UserSeeder::class,
            UserAddressSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            FaqSeeder::class,
            ReservationSeeder::class,
            WorkingShiftSeeder::class,
            ClosedPeriodSeeder::class,
        ]);
    }
}
