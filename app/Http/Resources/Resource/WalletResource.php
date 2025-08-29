<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'balance' => $this->wallet->balance,
            'transactions' => $this->walletTransactions->map(function ($transaction) {
                return [
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'order_number' => $this->whenNotNull($transaction->order?->order_number),
                    'revs_number' => $this->whenNotNull($transaction->reservation?->reservation_number),
                    'description'=> $transaction->description,
                    'created_at' => $transaction->created_at->format('Y-m-d h:i a')
                ];
            })->sortByDesc('created_at')->toArray(),
        ];
    }
}
