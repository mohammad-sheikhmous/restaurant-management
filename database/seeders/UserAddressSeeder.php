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
        ];

        foreach ($addresses as $address)
            UserAddress::create($address);
    }
}
