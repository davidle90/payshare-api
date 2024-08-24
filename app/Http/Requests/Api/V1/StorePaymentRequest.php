<?php

namespace App\Http\Requests\Api\V1;

class StorePaymentRequest extends BasePaymentRequest
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
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.label' => 'required|string',
            'data.relationships.contributors' => 'sometimes|array',
            'data.relationships.contributors.*.id' => 'required|integer',
            'data.relationships.contributors.*.amount' => 'required|numeric',
            'data.relationships.participants' => 'sometimes|array',
            'data.relationships.participants.*.id' => 'required|integer',
            'data.relationships.participants.*.amount' => 'sometimes|numeric',
        ];
    }
}
