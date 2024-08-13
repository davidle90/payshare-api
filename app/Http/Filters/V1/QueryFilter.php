<?php

namespace App\Http\Filters\V1;

use Illuminate\Http\Request;

abstract class QueryFilter {

    protected $builder;
    protected $request;
    protected $sortable = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
