<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletRechargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => ($this->user ?? $this->user_data)['name'],
            'amount' => $this->amount,
            'transfer_method' => $this->transfer_method,
            'status' => $this->status,
            'proof_image' => $this->proof_image,
            'created_at' => $this->created_at->format('Y-m-d h:i a'),
            'note' => $this->note
        ];
    }
}
