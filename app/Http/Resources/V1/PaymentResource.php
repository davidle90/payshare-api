<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'payment',
            'id' => $this->id,
            'attributes' => [
                'label' => $this->label,
                'group_id' => $this->group_id,
                'total' => $this->total,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at
            ],
            'includes' => [
                'groups' => GroupResource::collection($this->whenLoaded('groups')),
            ],
            'links' => [
                'self' => route('payments.show', ['payment' => $this->id])
            ]
        ];
    }
}
