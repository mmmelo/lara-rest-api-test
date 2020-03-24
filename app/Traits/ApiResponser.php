<?php
/**
 * Created by PhpStorm.
 * User: marcelmelo
 * Date: 11/30/18
 * Time: 12:51 PM
 */

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Cache;

trait ApiResponser {

    protected function successStatus() {
        return "";
    }

    protected function cacherResponse($data){
        $url = request()->url();

        $queryParams = request()->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}";


        return Cache::remember($fullUrl, 30/60, function () use($data){
            return $data;
        });
    }

    private function successResponse($data, $code){
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code=500){
        return response()->json(array(
            'error' => $message,
            'code' => $code
        ), $code);
    }

    protected function printData($message, $code = 200){
        return response()->json(array(
            'data' => $message,
            'code' => $code
        ), $code);
    }

    protected function printAll(Collection $collection, $code = 200){

        if($collection->isEmpty()){
            return $this->successResponse(array('data' => $collection), $code);
        };

        return $this->successResponse(array('data' => $collection), $code);
    }

    protected function printSingle(Model $model, $code=200){
        return $this->successResponse(array(
            'data' => $model,
        ), $code);
    }

}
