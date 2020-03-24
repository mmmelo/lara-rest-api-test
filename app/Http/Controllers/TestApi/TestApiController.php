<?php

namespace App\Http\Controllers\TestApi;

use App\Http\Controllers\ApiController;

class TestApiController extends ApiController
{

    public function __construct(){

    }

    public function echo() {
        return $this->printData('hello echo');
    }
}
