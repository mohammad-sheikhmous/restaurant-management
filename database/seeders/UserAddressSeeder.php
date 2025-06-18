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
            ],
        ];

        foreach ($addresses as $address)
            UserAddress::create($address);
    }
}
