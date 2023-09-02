<?php

namespace App\Http\Resources;

use App\Models\Exchanges;
use App\Models\OthercostType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'method' => $this->method,
            'amount' => $this->amount,
            'comment' => $this->comment,
            'issuccess' => $this->issuccess
        ];
    }
}
