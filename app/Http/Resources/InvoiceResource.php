<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'code' => $this->code,
            'sum_count' => $this->sum_count,
            'user_show_status' => invoiceStatusUserPanel($this->status, $this->orderlevel),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
