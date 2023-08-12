<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice_id' => $this->invoice_id,
            'amount' => $this->amount,
            'comment' => $this->comment,
            'issuccess' => $this->issuccess,
            'date' => $this->date,
            'payment_method' => $this->payment_method->title_fa,
        ];
    }
}
