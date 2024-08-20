<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class StoreGroupRequest extends BaseGroupRequest
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

        // $isGroupController = $this->routeIs('groups.store');
        // $ownerIdAttribute = $isGroupController ? 'data.relationships.owner.data.id' : 'owner';
        // $user = Auth::user();
        // $ownerRule = 'required|integer|exists:users,id';

        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.name' => 'required|string',
        ];

        // if($isGroupController){
        //     $rules['data.relationships'] = 'required|array';
        //     $rules['data.relationships.owner'] = 'required|array';
        //     $rules['data.relationships.owner.data'] = 'required|array';
        // }

        // $rules[$ownerIdAttribute] = $ownerRule . '|size:' . $user->id;

        // if($user->tokenCan(Abilities::CreateGroup)) {
        //     $rules[$ownerIdAttribute] = $ownerRule;
        // }

        return $rules;
    }
}
