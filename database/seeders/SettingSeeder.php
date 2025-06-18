<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'support_email' => 'fake@gmail.com',
            'mobile' => '0997740137',
            'landline' => '0114735244',
            'address' => 'دمشق, مزة جبل 86, ساحة العروس',
            'latitude' => 33.501753,
            'longitude' => 36.241828,
            'facebook' => 'https://facebook.com',
            'instagram' => 'https://instagram.com',
            'whatsapp' => '0997740137',
        ]);
    }
}
