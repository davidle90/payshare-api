<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'debt',
            'id' => $this->id,
            'attributes' => [
                'from' => $this->from_user->name,
                'to' => $this->to_user->name,
                'amount' => $this->amount,
                'group' => $this->group->reference_id,
            ],
            'includes' => [
            ],
            'links' => [
            ]
        ];
    }
}
