<?php

namespace App\Http\Resources\V1;

use App\Models\Group;
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
                'group' => new GroupResource($this->whenLoaded('group')),
                'contributors' => ContributorResource::collection($this->contributors),
                'participants' => ParticipantResource::collection($this->participants),
            ],
            'links' => [
                'self' => route('groups.payments.show', ['group' => $this->group_id, 'payment' => $this->id])
            ]
        ];
    }
}
