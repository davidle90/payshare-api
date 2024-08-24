<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends BasePaymentRequest
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
        return [
            'data.attributes.label' => 'sometimes|string',
            'data.relationships.contributors' => 'sometimes|array',
            'data.relationships.contributors.*.id' => 'required|integer',
            'data.relationships.contributors.*.amount' => 'required|numeric',
            'data.relationships.participants' => 'sometimes|array',
            'data.relationships.participants.*.id' => 'required|integer',
            'data.relationships.participants.*.amount' => 'sometimes|numeric',
            'data.attributes.reference_id' => 'prohibited'
        ];
    }
}
