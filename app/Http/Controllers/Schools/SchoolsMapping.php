<?php

namespace App\Http\Controllers\Schools;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Tools\ToolsController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SchoolsMapping extends ApiController
{
    public function __construct() {

    }

    public function getRatioSchool(Request $request) {

        $ws_myGSPN = new Client( [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);
        $result = array();
        $params = array( 'distance'=> $request->ratio, 'zipcode' => $request->zipcode);
        $schools = $ws_myGSPN->request('POST', env( 'MYGSPN_API_URL') . '/get_ratio_schools',
            [ 'body' => json_encode($params)]
        )->getBody()->getContents();
        $result['data']['schools'] = $this->addArrayField( $this->str_to_array($schools)['data']);
        $result['data']['am']  = $this->addArrayField($this->getAreaManagement($this->str_to_array($schools)['data'][0]['address_state']), true);
        $result['data']['assign'] = $this->getAmSchoolAssign($this->str_to_array($schools)['data']);
        return $this->printData($result['data']);
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

    public function getAreaManagement($state) {

        $am = DB::table('am_areas as a');
        $am->selectRaw('distinct count(b.school_id) as  total,a.area_id, a.area_name, a.area_state, a.color, a.label_color, a.text_color');
        $am->join('am_school_to_area as b', 'b.area_id' , '=', 'a.area_id', 'left');

        $am->where('a.area_state', $state);
        $am->groupBy(DB::raw(('a.area_id, a.area_name, a.area_state ')));
//        dd($am->toSql());
        $result = $am->get();

        return $result;

    }

    private function getAmSchoolAssign( $data=false) {
        $result = array('assigned'=>0, 'unassigned'=>0);
        foreach ($data as $index => $content) {
            if( $content['assign'] == '1' ) {
                $result['assigned']++;
            } else {
                $result['unassigned']++;
            };
        }
        return $result;
    }

    private function addArrayField( $array=false, $convert=false) {
        $array = $convert?$this->str_to_array($array):$array;
        foreach ($array as $index => $content) {
            $array[$index]['selected'] = false;
        }
        return $array;
    }

    public function addSchoolToAm( Request $request) {

        $this->validate($request, [
            'area_id'    => 'required',
            'schools'    => 'required',
        ]);

        $input = $request->all();
        $schools = $input['schools'];
        $groups = new ToolsController();
        $group_id = array();
        $del = array();

        try {

            if (is_array($schools) ) {

                foreach ( $schools as $school) {
                    $school_details = array(
                        'area_id' => $request->area_id['area_id'],
                        'school_id' => $groups->findSlugToId($school['school_slug'])->group_page_id,
                    );
                    $del[] = DB::table('am_school_to_area')->where('school_id', '=', $school_details['school_id'])->delete();
                    $group_id = DB::table('am_school_to_area')->insertGetId($school_details);
                }

            } else {
                return $this->errorResponse("No schools matched");
            }

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }


        return $this->printData(array( 'area_id' => $request->area_id, 'school_id'=> $schools, 'group_id'=>$group_id, 'del'=>$del));

    }

    public function delSchoolToAm( Request $request) {

        $schools = $request->schools;
        $groups = new ToolsController();
        $group_id = array();
        $del = array();

        foreach ( $schools as $school) {

            $del[] = DB::table('am_school_to_area')->where('school_id', '=', $groups->findSlugToId($school['school_slug'])->group_page_id)->delete();

        }
        return $this->printData(array( 'del'=>$del));

    }

    public function removeSchoolMapping( Request $request) {

        $this->validate($request, [
            'group_id'   => 'required',
        ]);

        $params = $request->all();

        $ws_mygspn = new Client([
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);

        try {

            $tools = new ToolsController();
            $res = $ws_mygspn->request('POST', env('MYGSPN_API_URL') . '/action/page_status',
                ['body' => json_encode([
                    'group_id' => $params['group_id'],
                    'token' => env('MYGSPN_TOKEN')
                ])]
            )->getBody()->getContents();

            $data = $tools->str_to_array($res);
            if( !$data['success'] ) {
                return $this->errorResponse($data, 422);
            }

        } catch (\Exception $e){
            return $this->errorResponse($e->getMessage());
        }

        return $this->printData(($data));
    }

}
