<?php

namespace App\Http\Controllers\Schedules;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Custom;

class ScheduleController extends ApiController
{
    public function find_schedule(Request $request)
    {
        $state = $request->state;
        $sport = $request->sport;
        $shrink =  $request->shrink;
        $group_id = $request->group_id;

//        echo($state); exit;
        $data = Custom::get_schedule_season($state,$sport,$shrink,$group_id);

        return $this->printData($data);
    }
}
