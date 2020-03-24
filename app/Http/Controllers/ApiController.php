<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Traits\Notify;
use Illuminate\Http\Request;

/**
 * @SWG\Swagger(
 *   schemes={"http"},
 *   basePath="/",
 *   @SWG\Info(
 *     title="myGSPN Doc FI",
 *     description="This is a documentation about Sportive Solutions API",
 *     termsOfService="",
 *         @SWG\Contact(
 *             email="info@mygspn.com"
 *         ),
 *     version="1.0.0"
 *   )
 * )
 */

class ApiController extends Controller
{
    use ApiResponser;
    use Notify;

    public function __construct(){
        $this->middleware('auth:api');
    }

}
