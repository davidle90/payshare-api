<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BasePaymentRequest extends FormRequest
{

    public function mappedAttributes(array $otherAttributes = []) {

        $attributeMap = array_merge([
            'data.attributes.label' => 'label',
        ], $otherAttributes);

        $attributesToUpdate = [];

        foreach($attributeMap as $key => $attribute){

            if($this->has($key)) {
                $value = $this->input($key);
                $attributesToUpdate[$attribute] = $value;
            }
        }

        return $attributesToUpdate;
    }
}
