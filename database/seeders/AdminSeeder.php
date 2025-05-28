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
    }
}
