<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'group',
            'id' => $this->id,
            'attributes' => [
                'owner_id' => $this->owner_id,
                'name' => $this->name,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at
            ],
            'includes' => [
                'payments' => PaymentResource::collection($this->whenLoaded('payments')),
                'members' => UserResource::collection($this->whenLoaded('members')),
            ],
            'links' => [
                'self' => route('groups.show', ['group' => $this->id])
            ]
        ];
    }
}
