<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'participant',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->member->name,
                'email' => $this->member->email,
                'payment_id' => $this->payment_id,
                'member_id' => $this->member_id,
                'amount' => $this->amount
            ],
            'includes' => [
                'groups' => GroupResource::collection($this->whenLoaded('groups'))
            ],
            'links' => [
                'self' => route('users.show', ['user' => $this->member_id])
            ]
        ];
    }
}
