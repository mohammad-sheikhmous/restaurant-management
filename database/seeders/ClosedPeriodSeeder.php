<?php

namespace Database\Seeders;

use App\Models\ClosedPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClosedPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            [
                'full_day' => 1,
                'type_id' => null,
                'from_date' => '2024-12-25',
                'to_date' => '2025-1-5',
                'from_time' => null,
                'to_time' => null,
                'reason' => [
                    'en' => "New Year's holiday",
                    'ar' => 'رأس السنة الميلادية'
                ],
            ],
            [
                'full_day' => 0,
                'type_id' => 2,
                'from_date' => '2025-1-25',
                'to_date' => '2025-1-25',
                'from_time' => '09:00',
                'to_time' => '13:30',
                'reason' => [
                    'en' => 'Some repair work',
                    'ar' => 'بعض أعمال الاصلاح'
                ],
            ],
            [
                'full_day' => 1,
                'type_id' => null,
                'from_date' => '2025-2-12',
                'to_date' => '2025-2-14',
                'from_time' => null,
                'to_time' => null,
            ],
            [
                'full_day' => 1,
                'type_id' => null,
                'from_date' => '2025-3-21',
                'to_date' => '2025-3-21',
                'from_time' => null,
                'to_time' => null,
                'reason' => [
                    'en' => "Mother's Day",
                    'ar' => 'عيد الأم'
                ],
            ],
            [
                'full_day' => 0,
                'type_id' => 2,
                'from_date' => '2025-4-5',
                'to_date' => '2025-4-12',
                'from_time' => '16:00',
                'to_time' => '22:00',
            ],
            [
                'full_day' => 1,
                'type_id' => null,
                'from_date' => '2025-7-10',
                'to_date' => '2025-7-13',
                'from_time' => null,
                'to_time' => null,
            ],
            [
                'full_day' => 0,
                'type_id' => 1,
                'from_date' => '2025-7-17',
                'to_date' => '2025-7-17',
                'from_time' => '12:30',
                'to_time' => '20:00',
                'reason' => [
                    'en' => 'Special occasion',
                    'ar' => 'مناسبة خاصة'
                ],
            ],
            [
                'full_day' => 1,
                'type_id' => null,
                'from_date' => '2025-07-25',
                'to_date' => '2025-07-29',
                'from_time' => null,
                'to_time' => null,
                'reason' => [
                    'en' => 'Restaurant renovation work',
                    'ar' => 'أعمال ترميم للمطعم'
                ],
            ],
            [
                'full_day' => 1,
                'type_id' => null,
                'from_date' => '2025-08-05',
                'to_date' => '2025-08-05',
                'from_time' => null,
                'to_time' => null,
            ],
            [
                'full_day' => 0,
                'type_id' => 2,
                'from_date' => '2025-08-10',
                'to_date' => '2025-08-12',
                'from_time' => '17:30',
                'to_time' => '21:30',
                'reason' => [
                    'en' => 'Special occasion',
                    'ar' => 'مناسبة خاصة'
                ],
            ]
        ];

        foreach ($periods as $period)
            ClosedPeriod::create($period);
    }
}
