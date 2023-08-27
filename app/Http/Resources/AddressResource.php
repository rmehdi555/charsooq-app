<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'postalcode' => $this->postalcode,
            'content' => $this->content,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id
        ];
    }
}
