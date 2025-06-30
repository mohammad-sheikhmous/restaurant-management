<?php

namespace App\Http\Resources\Resource;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClosedPeriodResource extends JsonResource
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
            'type' => $request->is('*/periods/*') ? $this->type :
                $this->whenNotNull($this->type?->name, 'whole restaurant'),
            'full_day' => boolval($this->full_day),
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'from_time' => $this->when($this->from_time, Carbon::parse($this->from_time)->format('H:i'),
                $request->is('*/periods/*') ? null : 'unspecified'),
            'to_time' => $this->when($this->to_time, Carbon::parse($this->to_time)->format('H:i'),
                $request->is('*/periods/*') ? null : 'unspecified'),
            'reason' => $request->is('*/periods/*') ? $this->getTranslations('reason') : ($this->reason ?: null),
        ];
    }
}
