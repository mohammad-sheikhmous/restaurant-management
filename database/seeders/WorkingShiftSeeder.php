<?php

namespace Database\Seeders;

use App\Models\WorkingShift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('set foreign_key_checks = 0;');
        WorkingShift::truncate();
        DB::statement('set foreign_key_checks = 1;');

        $shifts = [
            ['day_of_week' => 'monday', 'type_id' => 1, 'opening_time' => '09:00', 'closing_time' => '13:00'],
            ['day_of_week' => 'monday', 'type_id' => 1, 'opening_time' => '16:00', 'closing_time' => '22:00'],
            ['day_of_week' => 'tuesday', 'type_id' => 1, 'opening_time' => '10:00', 'closing_time' => '14:00'],
            ['day_of_week' => 'tuesday', 'type_id' => 1, 'opening_time' => '17:00', 'closing_time' => '22:00'],
            ['day_of_week' => 'wednesday', 'type_id' => 1, 'opening_time' => '09:30', 'closing_time' => '12:30'],
            ['day_of_week' => 'wednesday', 'type_id' => 1, 'opening_time' => '16:00', 'closing_time' => '22:30'],
            ['day_of_week' => 'thursday', 'type_id' => 1, 'opening_time' => '09:00', 'closing_time' => '13:00'],
            ['day_of_week' => 'thursday', 'type_id' => 1, 'opening_time' => '16:00', 'closing_time' => '22:00'],
            ['day_of_week' => 'friday', 'type_id' => 1, 'opening_time' => '16:00', 'closing_time' => '23:00'],
            ['day_of_week' => 'saturday', 'type_id' => 1, 'opening_time' => '09:00', 'closing_time' => '13:00'],
            ['day_of_week' => 'saturday', 'type_id' => 1, 'opening_time' => '16:00', 'closing_time' => '22:00'],
            ['day_of_week' => 'sunday', 'type_id' => 1, 'opening_time' => '10:00', 'closing_time' => '13:00'],
            ['day_of_week' => 'sunday', 'type_id' => 1, 'opening_time' => '06:00', 'closing_time' => '23:30'],
            ['day_of_week' => 'monday', 'type_id' => 2, 'opening_time' => '16:00', 'closing_time' => '22:00'],
            ['day_of_week' => 'tuesday', 'type_id' => 2, 'opening_time' => '10:30', 'closing_time' => '14:00'],
            ['day_of_week' => 'tuesday', 'type_id' => 2, 'opening_time' => '17:00', 'closing_time' => '22:00'],
            ['day_of_week' => 'wednesday', 'type_id' => 2, 'opening_time' => '09:30', 'closing_time' => '12:30'],
            ['day_of_week' => 'wednesday', 'type_id' => 2, 'opening_time' => '17:00', 'closing_time' => '23:00'],
            ['day_of_week' => 'thursday', 'type_id' => 2, 'opening_time' => '16:00', 'closing_time' => '22:00'],
            ['day_of_week' => 'friday', 'type_id' => 2, 'opening_time' => '16:00', 'closing_time' => '23:00'],
            ['day_of_week' => 'saturday', 'type_id' => 2, 'opening_time' => '09:30', 'closing_time' => '14:00'],
            ['day_of_week' => 'saturday', 'type_id' => 2, 'opening_time' => '16:00', 'closing_time' => '23:00'],
        ];

        WorkingShift::insert($shifts);
    }
}
