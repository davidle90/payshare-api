<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseGroupRequest extends FormRequest
{

    public function mappedAttributes(array $otherAttributes = []) {

        $attributeMap = array_merge([
            'data.attributes.name' => 'name',
            'data.attributes.isResolved' => 'is_resolved',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.owner.data.id' => 'owner_id',
        ], $otherAttributes);

        $attributesToUpdate = [];

        foreach($attributeMap as $key => $attribute){
            if($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }

    public function messages() {
        return [
            //'data.attributes.status' => 'The data.attributes.status value is invalid. Please use A, C, H or X.'
        ];
    }
}
