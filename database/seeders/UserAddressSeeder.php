<?php

namespace Database\Seeders;

use App\Models\UserAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addresses = [
            [
                'user_id' => 1,
                'latitude' => 33.497514,
                'longitude' => 36.3195355,
                'label' => 'university',
                'name' => 'Hamak university',
                'city' => 'Damascus',
                'area' => 'بستان الدور',
                'street' => 'طريق المطار',
                'mobile' => '0997740137',
                'additional_details' => 'بجانب كلية الهمك',
                'delivery_zone_id' => 1,
                'duration' => '23 mins',
                'distance' => 12.3
            ],
            [
                'user_id' => 1,
                'latitude' => 33.502050,
                'longitude' => 36.308156,
                'label' => 'منزل',
                'name' => 'كراج الست',
                'city' => 'دمشق',
                'area' => 'باب مصلى',
                'street' => 'ابن العساكر',
                'mobile' => '0997740137',
                'additional_details' => 'مقابل كراج الست ',
                'delivery_zone_id' => 1,
                'duration' => '33 mins',
                'distance' => 18.3
            ],
            [
                'user_id' => 1,
                'latitude' => 33.503302,
                'longitude' => 36.242035,
                'label' => 'house',
                'name' => 'منزل الحافظ',
                'city' => 'Damascus',
                'area' => 'مزة جبل 86',
                'street' => 'شارع الخزان الرئيسي',
                'mobile' => '0997740137',
                'additional_details' => 'بجانب مطعم الحافظ',
                'delivery_zone_id' => 2,
                'duration' => '18 mins',
                'distance' => 8.3
            ],
            [
                'user_id' => 1,
                'latitude' => 33.512871,
                'longitude' => 36.295026,
                'label' => 'مكان عمل',
                'name' => 'سعد الله الجابري',
                'city' => 'دمشق',
                'area' => 'الحجاز',
                'street' => 'سعدالله الجابري',
                'mobile' => '0997740137',
                'duration' => '25 mins',
                'distance' => 14.3
            ],

            [
                'user_id' => 2,
                'latitude' => 33.512871,
                'longitude' => 36.295026,
                'label' => 'مكان عمل',
                'name' => 'شيخ العقل حكت الهجري',
                'city' => 'دمشق',
                'area' => 'أشرفية صحنايا',
                'street' => 'شارع حكمت الهجري',
                'mobile' => '0937121137',
                'duration' => '12 mins',
                'distance' => 9.4
            ],
            [
                'user_id' => 2,
                'latitude' => 33.512871,
                'longitude' => 36.295026,
                'label' => 'منزل',
                'name' => 'وليد جنبلاط',
                'city' => 'دمشق',
                'area' => 'جرمانا',
                'street' => 'شارع وليد الجنبلاط',
                'mobile' => '0993920137',
                'duration' => '10 mins',
                'distance' => 7.8
            ],
            [
                'user_id' => 3,
                'latitude' => 33.512871,
                'longitude' => 36.295026,
                'label' => 'مكان جديد',
                'name' => 'شارع منير الحبيب',
                'city' => 'دمشق',
                'area' => 'ساحة الامويين',
                'street' => 'منير الحبيب',
                'mobile' => '0997742345',
                'duration' => '18 mins',
                'distance' => 14
            ],
            [
                'user_id' => 3,
                'latitude' => 33.512871,
                'longitude' => 36.295026,
                'label' => 'مكان عمل',
                'name' => 'سعد الله الجابري',
                'city' => 'دمشق',
                'area' => 'الحجاز',
                'street' => 'سعدالله الجابري',
                'mobile' => '0957743131',
                'duration' => '30 mins',
                'distance' => 17.2
            ],
            [
                'user_id' => 4,
                'latitude' => 33.512871,
                'longitude' => 36.295026,
                'label' => 'مكان عمل',
                'name' => 'منطقة الاقليات',
                'city' => 'دمشق',
                'area' => 'مزة 86',
                'street' => 'ساحة العروس',
                'mobile' => '0997440187',
                'duration' => '14 mins',
                'distance' => 9.5
            ],
            [
                'user_id' => 5,
                'latitude' => 33.512871,
                'longitude' => 36.295026,
                'label' => 'مكان عمل',
                'name' => 'منطقة الاكثرية',
                'city' => 'دمشق',
                'area' => 'ميدان',
                'street' => 'شارع مستشفى المهايني',
                'mobile' => '0943740137',
                'duration' => '19 mins',
                'distance' => 11.6
            ],
            [
                'user_id' => 5,
                'latitude' => 33.512871,
                'longitude' => 36.295026,
                'label' => 'منزل',
                'name' => 'بيتي الجديد',
                'city' => 'دمشق',
                'area' => 'الطبالة',
                'street' => 'طريق المطار',
                'mobile' => '0997743137',
                'duration' => '17 mins',
                'distance' => 10.3
            ],
        ];

        foreach ($addresses as $address)
            UserAddress::create($address);
    }
}
