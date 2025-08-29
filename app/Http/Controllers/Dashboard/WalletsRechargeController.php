<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\WalletRechargeResource;
use App\Models\WalletRechargeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletsRechargeController extends Controller
{
    public function index()
    {
        request()->validate([
            'sort_by' => 'nullable|in:id,status,transfer_method,amount,created_at',
            'order_by' => 'nullable|in:desc,asc',
        ]);
        $limit = request()->limit ?? 10;
        $sort_by = request()->sort_by ?? 'created_at';
        $order_by = request()->order_by ?? 'desc';
        $page = numberToOrdinalWord(request()->page ?? 1);

        $requests = WalletRechargeRequest::orderBy($sort_by, $order_by)
            ->with(['user:id,first_name,last_name'])
            ->paginate($limit);

        return dataJson(
            'request',
            (WalletRechargeResource::collection($requests))->response()->getData(true),
            "All Request for {$page} page."
        );
    }

    public function acceptOrReject($id)
    {
        $status = request()->validate([
            'status' => 'required|in:accept,reject',
            'charge_value' => 'nullable|required_if:status,accept|decimal:0,1|max:1000000'
        ])['status'];

        $request = WalletRechargeRequest::whereStatus('pending')
            ->with(['user:id,first_name,last_name,mobile,email', 'user.wallet:id,user_id,balance'])
            ->whereId($id)
            ->first();
        if (!$request)
            return messageJson('Request not found.!', false, 404);

        if ($status == 'accept') {
            DB::transaction(function () use ($request) {
                // Update wallet balance.
                $wallet = $request->user->wallet;
                $wallet->balance += request()['recharge_value'];
                $wallet->save();
                // Convert request status to accepted.
                $request->status = 'accepted';
                $request->save();

                $request->user->walletTransactions()->create([
                    'user_data' => [
                        'name' => $request->user->name,
                        'mobile' => $request->user->mobile,
                        'email' => $request->user->email,
                    ],
                    'amount' => request()['charge_value'],
                    'type' => 'deposit',
                ]);
            });

            // Realtime Notification Here ....

            return messageJson("{$request->user->name}'s wallet has been charged successfully.");
        } else {
            $request->status = 'rejected';
            $request->save();

            // Realtime Notification Here ....

            return messageJson("{$request->user->name}'s request rejected.");
        }
    }

    public function chargeManually()
    {
        request()->validate([
            'users_ids' => 'array|min:1',
            'users_ids.*' => 'required|exists:users,id',
            'charge_value' => 'required|decimal:0,2|max:1000000',
            'description' => 'nullable|string|max:100'
        ]);

        $charge_value = request()->charge_value;
        $users_ids = implode(',', request()->users_ids);
        $description = request()->description;

        DB::transaction(function () use ($description, $charge_value, $users_ids) {
            DB::statement("
            insert into wallet_transactions (user_id, user_data, type, amount, description)
            select
                us.id,
                json_object('name', concat(us.first_name, ' ', us.last_name), 'mobile', us.mobile, 'email', us.email),
                'administrative_deposit',
                ?,
                ?
            from users us where us.id in ($users_ids);
            ", [$charge_value, $description]);

            DB::statement("
            update wallets set balance = balance + ? where user_id in ($users_ids);
            ", [$charge_value]);
        });

        return messageJson("Selected users' wallets has been charged successfully.");
    }
}
