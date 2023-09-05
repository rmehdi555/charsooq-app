<?php

namespace App\Http\Resources;

use App\Enum\CreditPaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditResource extends JsonResource
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
            'amount' => $this->amount,
            'payment_status' => CreditPaymentStatus::fromName($this->payment_status)->value,
            'description' => $this->description,
            'invoice_code' => $this->invoice->code ?? null,
        ];
    }
}
