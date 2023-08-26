<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array(
            'code' => $this->code,
            'title' => $this->title,
            'status_id' => $this->ticketStatus->name,
            'created_at' => $this->created_at,
            'department_id' => $this->department->name,
            'is_response' => ($this->ticketStatus->id==4) ? true : false

        );
    }
}
