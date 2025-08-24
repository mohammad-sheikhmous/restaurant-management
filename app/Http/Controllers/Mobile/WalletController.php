<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRechargeRequestRequest;
use App\Http\Resources\Resource\WalletResource;
use App\Models\WalletRechargeRequest;
use Illuminate\Http\Request;

class   WalletController extends Controller
{
    public function getWallet()
    {
        $user = auth('user')->user()->load(['wallet', 'walletTransactions']);

        return dataJson(
            'wallet',
            WalletResource::make($user),
            'Wallet and its transactions details.'
        );
    }

    public function charge(WalletRechargeRequestRequest $request)
    {
        $user = auth('user')->user();

        $image = storeImage($user->last_name, $request->proof_image, 'wallet-requests');

        $user->walletRechargeRequests()->create([
            'user_data' => [
                'name' => $user->name,
                'email' => $user->name,
                'mobile' => $user->mobile
            ],
            'amount' => $request->amount,
            'transfer_method' => $request->transfer_method,
            'proof_image' => $image,
            'note' => $request->image
        ]);

        // Realtime Notification Here

        return messageJson(
            'Charge Request created successfully, awaiting management approval, and you will be notified of that.'
            , true,
            201
        );
    }
}
