<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Support\Facades\Auth;

class UpdateGroupRequest extends BaseGroupRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'data.attributes.name' => 'sometimes|string',
            'data.attributes.isResolved' => 'sometimes|boolean',
            'data.relationships.owner.data.id' => 'prohibited',
            'data.attributes.reference_id' => 'prohibited'
        ];

        return $rules;
    }
}
