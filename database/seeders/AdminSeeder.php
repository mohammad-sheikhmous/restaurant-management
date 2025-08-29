<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Admin::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        Admin::create([
            'name' => 'manager admin',
            'email' => 'admin@gmail.com',
            'image' => 'default.png',
            'password' => 'password',
            'status' => 1,
            'role_id' => Role::first()->id,
        ]);

        $drivers = [
            [
                'name' => 'Ali Hesen',
                'email' => 'ali@gmail.com',
                'mobile' => fake()->regexify('09[345689][0-9]{7}'),
                'password' => 'password',
                'status' => 1,
                'role_id' => Role::where('id', 2)->first()->id,
            ],
            [
                'name' => 'Mohammad Ali',
                'email' => 'alimoahhamd@gmail.com',
                'mobile' => fake()->regexify('09[345689][0-9]{7}'),
                'password' => 'password',
                'status' => 1,
                'role_id' => Role::where('id', 2)->first()->id,
            ],
            [
                'name' => 'Ahmed Al-Shahada',
                'email' => 'Ahmed@gmail.com',
                'mobile' => fake()->regexify('09[345689][0-9]{7}'),
                'password' => 'password',
                'status' => 1,
                'role_id' => Role::where('id', 2)->first()->id,
            ],
            [
                'name' => 'Abdullah Al-Hussien',
                'email' => 'Abdullah@gmail.com',
                'mobile' => fake()->regexify('09[345689][0-9]{7}'),
                'password' => 'password',
                'status' => 1,
                'role_id' => Role::where('id', 2)->first()->id,
            ],
            [
                'name' => 'Ronnie Jean',
                'email' => 'Ronnie@gmail.com',
                'mobile' => fake()->regexify('09[345689][0-9]{7}'),
                'password' => 'password',
                'status' => 1,
                'role_id' => Role::where('id', 2)->first()->id,
            ],
        ];

        foreach ($drivers as $driver)
            Admin::create($driver);
    }
}
