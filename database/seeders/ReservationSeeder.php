<?php

namespace Database\Seeders;

use App\Models\BookingPolicy;
use App\Models\Reservation;
use App\Models\ReservationType;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('set foreign_key_checks = 0;');
        BookingPolicy::truncate();
        Table::truncate();
        ReservationType::truncate();
        Reservation::truncate();
        DB::statement('set foreign_key_checks = 1;');

        $types = [
            ['name' => ['en' => 'indoor', 'ar' => 'داخلي'], 'deposit_value' => 500],
            ['name' => ['en' => 'outdoor', 'ar' => 'خارجي'], 'deposit_value' => 800],
        ];

        foreach ($types as $type)
            ReservationType::create($type);

        BookingPolicy::create([
            'max_revs_duration_hours' => 8,
            'max_pre_booking_days' => 45,
            'min_pre_booking_minutes' => 240,
            'revs_cancellability' => 1,
            'min_revs_cancellability_minutes' => 45,
            'revs_cancellability_ratio' => 10,
            'revs_modifiability' => 1,
            'min_revs_modifiability_minutes' => 60,
            'revs_modifiability_ratio' => 10,
            'table_combinability' => 1,
            'manual_confirmation' => 1,
            'temp_revs_conf_minutes' => 5,
            'min_people' => 2,
            'max_people' => 20,
            'interval_minutes' => 30,
            'auto_no_show_minutes' => 20,
            'deposit_system' => 1,
            'deposit_value' => 500,
            'num_of_person_per_deposit' => 2,
            'time_per_deposit' => 60,
            'deposit_customizability' => 1,
            'explanatory_notes' => [
                'en' => 'Welcome to our restaurant, dear guests.
Restaurant Policies :
- Children are welcome from 10:00 AM to 10:00 PM.
- Indoor seating is 500 SY and outdoor seating is 800 SY.
- The deposit will be refunded upon arrival.
- You can cancel your reservation before the specified time.
- You can change the number of people or the reservation time before the specified time.
- Smoking is prohibited.
- If you miss your reservation by 20 minutes or cancel your reservation before the specified time,
    you will lose your deposit.
- One deposit is for two people for 60 minutes. Whenever the number of people or time doubles, the deposit will double as well.
    ',
                'ar' => 'اهلا بكم في مطعمنا ضيوفنا الاعزاء
سياسات المطعم:
- الاطفال من الساعة العاشرة صباحا للساعة عاسرة مساءا
 - قيمة العربون للداخلي ٥٠٠ وولخارجي ٨٠٠
- قيمة العربون سيتم استردادها بعد الحضور
- يمكنك الغاء الحجز قبل الفترة المحددة
- يمكنك تعديل عدد الاشخاص او توقيت الحجز قبل الفترة المحددة
- التدخين ممنوع
- في حال تخلفك عن توقيت الحجز عشرين دقيقة او في حال الغاءك الحجز قبل الفترة المحددة فأن ذلك سيؤدي الى خسارتك للعربون
- العربون الواحد يكون لكون لشخصان ولمدة ٦٠ دقيقة وكلما تتضاعف عدد الاشخاص أو الوقت فأن ذلك سيؤدي الي تضاعف العربون ايضا
'
            ],
        ]);

        $tables = [
            [
                'table_num' => 'T1',
                'seats_count' => 4,
                'is_combinable' => 1,
                'type_id' => 1
            ],
            [
                'table_num' => 'T2',
                'seats_count' => 2,
                'is_combinable' => 1,
                'type_id' => 2
            ],
            [
                'table_num' => 'T3',
                'seats_count' => 5,
                'is_combinable' => 1,
                'type_id' => 1
            ],
            [
                'table_num' => 'T4',
                'seats_count' => 4,
                'is_combinable' => 0,
                'type_id' => 1
            ],
            [
                'table_num' => 'R1',
                'seats_count' => 8,
                'is_combinable' => 1,
                'type_id' => 1
            ],
            [
                'table_num' => 'R2',
                'seats_count' => 2,
                'is_combinable' => 1,
                'type_id' => 1
            ],
            [
                'table_num' => 'R3',
                'seats_count' => 7,
                'is_combinable' => 0,
                'type_id' => 2
            ],
            [
                'table_num' => 'R4',
                'seats_count' => 10,
                'is_combinable' => 0,
                'type_id' => 2
            ],
        ];

        $type1 = ReservationType::find(1);
        $type2 = ReservationType::find(2);
        $reservation = [];
        $reservation_table = [];
        $user = User::find(1);
        $reservation[] = [
            'user_id' => 1,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(185)->format('Y-m-d'),
            'revs_time' => '10:00',
            'revs_duration' => '02:00',
            'guests_count' => 4,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'note' => 'I want the best table.',
            'created_at' => now()->subDays(189),
        ];
        $reservation_table[] = [
            'reservation_id' => 1,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(2);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(180)->format('Y-m-d'),
            'revs_time' => '17:00',
            'revs_duration' => '01:00',
            'guests_count' => 6,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(183),
        ];
        $reservation_table[] = [
            'reservation_id' => 2,
            'table_id' => 1,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $reservation_table[] = [
            'reservation_id' => 2,
            'table_id' => 2,
            'table_data' => json_encode([
                'table_num' => 'T2',
                'seats_count' => 2,
                'type' => $type2->getTranslations('name')
            ]),
        ];
        $user = User::find(3);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(182)->format('Y-m-d'),
            'revs_time' => '20:00',
            'revs_duration' => '01:30',
            'guests_count' => 4,
            'status' => 'cancelled',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(184)
        ];
        $reservation_table[] = [
            'reservation_id' => 3,
            'table_id' => 1,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(4);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(174)->format('Y-m-d'),
            'revs_time' => '11:00',
            'revs_duration' => '02:00',
            'guests_count' => 4,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'note' => 'I want the best table.',
            'created_at' => now()->subDays(175),
        ];
        $reservation_table[] = [
            'reservation_id' => 4,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(5);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(168)->format('Y-m-d'),
            'revs_time' => '15:00',
            'revs_duration' => '05:00',
            'guests_count' => 8,
            'status' => 'rejected',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(171),
        ];
        $reservation_table[] = [
            'reservation_id' => 5,
            'table_id' => 5,
            'table_data' => json_encode([
                'table_num' => 'R1',
                'seats_count' => 8,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(6);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(160)->format('Y-m-d'),
            'revs_time' => '18:00',
            'revs_duration' => '02:00',
            'guests_count' => 14,
            'status' => 'rejected',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(164)
        ];
        $reservation_table[] = [
            'reservation_id' => 6,
            'table_id' => 8,
            'table_data' => json_encode([
                'table_num' => 'R4',
                'seats_count' => 10,
                'type' => $type2->getTranslations('name')
            ]),
        ];
        $reservation_table[] = [
            'reservation_id' => 6,
            'table_id' => 2,
            'table_data' => json_encode([
                'table_num' => 'T2',
                'seats_count' => 2,
                'type' => $type2->getTranslations('name')
            ]),
        ];
        $user = User::find(7);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(171)->format('Y-m-d'),
            'revs_time' => '17:30',
            'revs_duration' => '01:00',
            'guests_count' => 3,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(172)
        ];
        $reservation_table[] = [
            'reservation_id' => 7,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(8);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(157)->format('Y-m-d'),
            'revs_time' => '12:15',
            'revs_duration' => '01:15',
            'guests_count' => 2,
            'status' => 'no_show',
            'deposit_value' => 500,
            'deposit_status' => 'forfeited',
            'created_at' => now()->subDays(158),
        ];
        $reservation_table[] = [
            'reservation_id' => 8,
            'table_id' => 6,
            'table_data' => json_encode([
                'table_num' => 'R2',
                'seats_count' => 2,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(9);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(145)->format('Y-m-d'),
            'revs_time' => '15:00',
            'revs_duration' => '02:00',
            'guests_count' => 8,
            'status' => 'no_show',
            'deposit_value' => 500,
            'deposit_status' => 'forfeited',
            'created_at' => now()->subDays(150),
        ];
        $reservation_table[] = [
            'reservation_id' => 9,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $reservation_table[] = [
            'reservation_id' => 9,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(1);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(135)->format('Y-m-d'),
            'revs_time' => '12:00',
            'revs_duration' => '01:00',
            'guests_count' => 5,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(139),
        ];
        $reservation_table[] = [
            'reservation_id' => 10,
            'table_id' => 3,
            'table_data' => json_encode([
                'table_num' => 'T3',
                'seats_count' => 5,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(2);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(125)->format('Y-m-d'),
            'revs_time' => '18:00',
            'revs_duration' => '00:30',
            'guests_count' => 7,
            'status' => 'rejected',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(131),
        ];
        $reservation_table[] = [
            'reservation_id' => 11,
            'table_id' => 7,
            'table_data' => json_encode([
                'table_num' => 'R3',
                'seats_count' => 7,
                'type' => $type2->getTranslations('name')
            ]),
        ];
        $user = User::find(1);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(123)->format('Y-m-d'),
            'revs_time' => '19:00',
            'revs_duration' => '01:15',
            'guests_count' => 6,
            'status' => 'cancelled',
            'deposit_value' => 500,
            'deposit_status' => 'forfeited',
            'created_at' => now()->subDays(125),
        ];
        $reservation_table[] = [
            'reservation_id' => 12,
            'table_id' => 1,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $reservation_table[] = [
            'reservation_id' => 12,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(5);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(145)->format('Y-m-d'),
            'revs_time' => '19:00',
            'revs_duration' => '01:00',
            'guests_count' => 4,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(155),
        ];
        $reservation_table[] = [
            'reservation_id' => 13,
            'table_id' => 1,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(9);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(120)->format('Y-m-d'),
            'revs_time' => '18:00',
            'revs_duration' => '01:30',
            'guests_count' => 6,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(121),
        ];
        $reservation_table[] = [
            'reservation_id' => 14,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $reservation_table[] = [
            'reservation_id' => 14,
            'table_id' => 6,
            'table_data' => json_encode([
                'table_num' => 'R2',
                'seats_count' => 2,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(3);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(105)->format('Y-m-d'),
            'revs_time' => '17:00',
            'revs_duration' => '04:00',
            'guests_count' => 5,
            'status' => 'rejected',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(108),
        ];
        $reservation_table[] = [
            'reservation_id' => 15,
            'table_id' => 3,
            'table_data' => json_encode([
                'table_num' => 'T3',
                'seats_count' => 5,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(8);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(45)->format('Y-m-d'),
            'revs_time' => '19:00',
            'revs_duration' => '03:00',
            'guests_count' => 8,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(47),
        ];
        $reservation_table[] = [
            'reservation_id' => 16,
            'table_id' => 5,
            'table_data' => json_encode([
                'table_num' => 'R1',
                'seats_count' => 8,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(7);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(35)->format('Y-m-d'),
            'revs_time' => '20:00',
            'revs_duration' => '02:00',
            'guests_count' => 2,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(39),
        ];
        $reservation_table[] = [
            'reservation_id' => 17,
            'table_id' => 1,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(10);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(25)->format('Y-m-d'),
            'revs_time' => '11:00',
            'revs_duration' => '00:30',
            'guests_count' => 2,
            'status' => 'cancelled',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(26),
        ];
        $reservation_table[] = [
            'reservation_id' => 18,
            'table_id' => 1,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(10);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(25)->format('Y-m-d'),
            'revs_time' => '17:00',
            'revs_duration' => '00:30',
            'guests_count' => 2,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(27),
        ];
        $reservation_table[] = [
            'reservation_id' => 19,
            'table_id' => 2,
            'table_data' => json_encode([
                'table_num' => 'T2',
                'seats_count' => 2,
                'type' => $type2->getTranslations('name')
            ]),
        ];
        $user = User::find(6);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(15)->format('Y-m-d'),
            'revs_time' => '18:00',
            'revs_duration' => '01:30',
            'guests_count' => 4,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(17),
        ];
        $reservation_table[] = [
            'reservation_id' => 20,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(4);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(10)->format('Y-m-d'),
            'revs_time' => '18:00',
            'revs_duration' => '04:30',
            'guests_count' => 9,
            'status' => 'completed',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(12),
        ];
        $reservation_table[] = [
            'reservation_id' => 21,
            'table_id' => 8,
            'table_data' => json_encode([
                'table_num' => 'R4',
                'seats_count' => 10,
                'type' => $type2->getTranslations('name')
            ]),
        ];
        $user = User::find(12);
        $reservation[] = [
            'user_id' => null,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(3)->format('Y-m-d'),
            'revs_time' => '20:00',
            'revs_duration' => '01:30',
            'guests_count' => 3,
            'status' => 'cancelled',
            'deposit_value' => 500,
            'deposit_status' => 'forfeited',
            'created_at' => now()->subDays(6),
        ];
        $reservation_table[] = [
            'reservation_id' => 22,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(12);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->addDays(2)->format('Y-m-d'),
            'revs_time' => '18:00',
            'revs_duration' => '01:30',
            'guests_count' => 4,
            'status' => 'accepted',
            'deposit_value' => 500,
            'deposit_status' => 'pending',
            'created_at' => now()->subDays(3),
        ];
        $reservation_table[] = [
            'reservation_id' => 23,
            'table_id' => 1,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(3);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->addDays(2)->format('Y-m-d'),
            'revs_time' => '11:00',
            'revs_duration' => '01:00',
            'guests_count' => 6,
            'status' => 'accepted',
            'deposit_value' => 500,
            'deposit_status' => 'pending',
            'created_at' => now()->subDays(5),
        ];
        $reservation_table[] = [
            'reservation_id' => 24,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $reservation_table[] = [
            'reservation_id' => 24,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'R2',
                'seats_count' => 2,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(1);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->addDays(3)->format('Y-m-d'),
            'revs_time' => '17:00',
            'revs_duration' => '02:30',
            'guests_count' => 7,
            'status' => 'accepted',
            'deposit_value' => 500,
            'deposit_status' => 'pending',
            'created_at' => now()->subDays(5),
        ];
        $reservation_table[] = [
            'reservation_id' => 25,
            'table_id' => 7,
            'table_data' => json_encode([
                'table_num' => 'R3',
                'seats_count' => 7,
                'type' => $type2->getTranslations('name')
            ]),
        ];
        $user = User::find(4);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->addDays(5)->format('Y-m-d'),
            'revs_time' => '16:00',
            'revs_duration' => '03:30',
            'guests_count' => 4,
            'status' => 'cancelled',
            'deposit_value' => 500,
            'deposit_status' => 'refunded',
            'created_at' => now()->subDays(8),
        ];
        $reservation_table[] = [
            'reservation_id' => 26,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(15);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->addDays(13)->format('Y-m-d'),
            'revs_time' => '17:30',
            'revs_duration' => '00:30',
            'guests_count' => 5,
            'status' => 'accepted',
            'deposit_value' => 500,
            'deposit_status' => 'pending',
            'created_at' => now()->subDays(17),
        ];
        $reservation_table[] = [
            'reservation_id' => 27,
            'table_id' => 3,
            'table_data' => json_encode([
                'table_num' => 'T3',
                'seats_count' => 5,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(8);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->subDays(16)->format('Y-m-d'),
            'revs_time' => '17:00',
            'revs_duration' => '01:00',
            'guests_count' => 5,
            'status' => 'accepted',
            'deposit_value' => 500,
            'deposit_status' => 'pending',
            'created_at' => now()->subDays(28),
        ];
        $reservation_table[] = [
            'reservation_id' => 28,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $reservation_table[] = [
            'reservation_id' => 28,
            'table_id' => 6,
            'table_data' => json_encode([
                'table_num' => 'R2',
                'seats_count' => 2,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(4);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->addDays(17)->format('Y-m-d'),
            'revs_time' => '19:45',
            'revs_duration' => '02:15',
            'guests_count' => 8,
            'status' => 'accepted',
            'deposit_value' => 500,
            'deposit_status' => 'pending',
            'created_at' => now()->subDays(19),
        ];
        $reservation_table[] = [
            'reservation_id' => 29,
            'table_id' => 5,
            'table_data' => json_encode([
                'table_num' => 'R1',
                'seats_count' => 8,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(12);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->addDays(27)->format('Y-m-d'),
            'revs_time' => '20:00',
            'revs_duration' => '01:30',
            'guests_count' => 8,
            'status' => 'pending',
            'deposit_value' => 500,
            'deposit_status' => 'pending',
            'created_at' => now()->subDays(28),
        ];
        $reservation_table[] = [
            'reservation_id' => 30,
            'table_id' => 1,
            'table_data' => json_encode([
                'table_num' => 'T1',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $reservation_table[] = [
            'reservation_id' => 30,
            'table_id' => 4,
            'table_data' => json_encode([
                'table_num' => 'T4',
                'seats_count' => 4,
                'type' => $type1->getTranslations('name')
            ]),
        ];
        $user = User::find(7);
        $reservation[] = [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'revs_date' => now()->addDays(22)->format('Y-m-d'),
            'revs_time' => '18:00',
            'revs_duration' => '01:00',
            'guests_count' => 4,
            'status' => 'pending',
            'deposit_value' => 500,
            'deposit_status' => 'pending',
            'created_at' => now()->subDays(24),
        ];
        $reservation_table[] = [
            'reservation_id' => 31,
            'table_id' => 3,
            'table_data' => json_encode([
                'table_num' => 'T3',
                'seats_count' => 5,
                'type' => $type1->getTranslations('name')
            ]),
        ];

        foreach ($tables as $table)
            Table::create($table);

        foreach ($reservation as $res)
            Reservation::create($res);

        DB::table('reservation_table')->insert($reservation_table);
    }
}
