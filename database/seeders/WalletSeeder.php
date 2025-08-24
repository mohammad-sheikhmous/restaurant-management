<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletRechargeRequest;
use Database\Factories\WalletRechargeRequestFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        WalletRechargeRequest::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $users = User::select('id')->get();
        foreach ($users as $user) {
            $user->wallet()->update([
                'balance' => rand(10000, 200000),
                'expire_date' => now()->addDays(60)
            ]);
        }

        WalletRechargeRequestFactory::setStatusArray(['accepted', 'rejected']);
        WalletRechargeRequestFactory::setMinAndMaxNumsArray(['min' => 7, 'max' => 40]);
        WalletRechargeRequest::factory(40)->create();

        WalletRechargeRequestFactory::setStatusArray(['pending']);
        WalletRechargeRequestFactory::setMinAndMaxNumsArray(['min' => 1, 'max' => 10]);
        WalletRechargeRequest::factory(20)->create();

        DB::statement("delete from wallet_transactions where description = 'مكافئة شحن المحفطة بمناسبة مرور شهر رمضان.';");
        DB::statement("
            insert into wallet_transactions (user_id, user_data, type, amount, description, created_at)
                select
                    us.id,
                    json_object('name', concat(us.first_name, '', us.last_name), 'mobile', us.mobile, 'email', us.email),
                    'administrative_deposit',
                    20000,
                    'مكافئة شحن المحفطة بمناسبة مرور شهر رمضان.',
                    subdate(now(), 13)
                from users us;
        ");
    }
}
