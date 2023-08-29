<?php

namespace App\Http\Resources;

use App\Models\Exchanges;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'link' => $this->link,
            'count' => $this->count,
            'cost' => $this->cost,
            'exchange_id' => $this->exchange_id,
            'exchange'=>Exchanges::whereId($this->exchange_id)->value('name'),
            'firstweight'=>$this->firstweight,
            'itemprice'=>$this->itemprice,
            'transportprice'=>$this->transportprice,
            'description' => $this->description,
            'all_price'=>$this->singleitemfullprice * $this->count,
            'image'=>$this->image,
        ];
    }
}
