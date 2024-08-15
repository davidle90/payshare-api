<?php

namespace App\Http\Filters\V1;

class UserFilter extends QueryFilter {

    protected $sortable = [

    ];

    public function createdAt($value)
    {

    }

    public function include($value) {
        return $this->builder->with($value);
    }
}
