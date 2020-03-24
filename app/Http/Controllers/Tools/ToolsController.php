<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;

class ToolsController extends ApiController
{
    public function __construct() {
    }

    public function createColorsAmAreas($random=false) {

        $result = array('success'=>0);
        $pos = 0;
        $colors = array(
            array('label'=>'gray',  'color'=>'#B7B7B7', 'text_color'=>'#ffffff'),
            array('label'=>'green', 'color'=>'#58D357', 'text_color'=>'#ffffff'),
            array('label'=>'orange','color'=>'#F0BC10', 'text_color'=>'#ffffff'),
            array('label'=>'purple','color'=>'#FF47FF', 'text_color'=>'#ffffff'),
            array('label'=>'red',   'color'=>'#FF3747', 'text_color'=>'#ffffff'),
            array('label'=>'white', 'color'=>'#FFFFFF', 'text_color'=>'#000000'),
            array('label'=>'yellow','color'=>'#F0F057', 'text_color'=>'#000000'),
            array('label'=>'black', 'color'=>'#000000', 'text_color'=>'#ffffff'),
            array('label'=>'blue',  'color'=>'#4E4FFF', 'text_color'=>'#ffffff'),
            array('label'=>'brown', 'color'=>'#C5965B', 'text_color'=>'#ffffff'),
        );

        $am = DB::table('am_areas');
        $am->orderBy('area_state');
        $am->orderBy('area_name');
        $am->whereNull('label_color');
        $areas = $am->get();

        foreach ( $areas as $i => $area) {

            if($random) {
                $new_pos = rand(0, count($colors));
            } else {
                $new_pos = $pos;
            }

            $upt_am = DB::table('am_areas')->where('area_id', $area->area_id)->update([
                'label_color'=> $colors[$new_pos]['label'],
                'color'=> $colors[$new_pos]['color'],
                'text_color'=> $colors[$new_pos]['text_color'],
            ]);

            if($pos==count($colors)-1){
                $pos=0;
            }else{
                $pos++;
            };

            $result['success']++;
        }
//        exit();
        return $this->printData( $result );

    }

    public function findSlugToId($slug) {

        $group = DB::table('groups')->where('slug', $slug)->select('group_page_id')->first();

        return $group;
    }

    public function str_to_array($json) {
        $data = array();

        if ($json) {
            $_json = json_decode($json, true);

            if ($json == json_encode($_json)) {
                $data = $_json;
            }
        }

        return $data;
    }
}
