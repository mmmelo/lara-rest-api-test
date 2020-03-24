<?php

namespace App\Http\Controllers;


use App\QrCode_generator;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Qrcode_encoder extends Controller
{
    public function __construct()
    {
        $this->groups = new Groups();
    }

    public function test()
    {
        $data = QrCode::size(300)->generate('Hello_world');

        print($data);
    }

    public function create(Request $request)
    {
        //This method is to create an specific event sign on process
        //Param would be : event, type, school
        $event = $request->event;
        $type = $request->type;
        $school = $request->school;

        //fix html

        $website = 'http://www.beta.mygspn.com/onboarding/';

        if($school)
        {
            $data_v1 = $this->groups->find_with_address($school);
//            print($dave_v1);exit;
            $school_name = $data_v1[0]['full_name'].' | '.$data_v1[0]['address']['user_address_city'].' | '.$data_v1[0]['address']['user_address_state'];
            $slug = $data_v1[0]['slug'];
            $data = $website.$event.'/'.$type.'/'.$slug;
        }else{
            $school_name = 'School not Selected';
            $data = $website.$event.'/'.$type;
        }

        return view('qr_code_generator')
            ->with('data',$data)
            ->with('event',$event)
            ->with('type',$type)
            ->with('school',$school_name);
    }
}
