<?php

namespace App\Http\Controllers\Porting;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Tools\ToolsController;
use App\MaxPrepsScores;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortingController extends ApiController
{

    public function __construct() {
    }

    public function getPorting () {
        $scores = MaxPrepsScores::whereRaw('status is null or status = 0');
//        $scores->where('event_type', '=' , 1);
        $scores->selectRaw("date_format(created_at, '%m/%d/%Y') as 'date'
                        , count(created_at) as 'total'
                        , event_type as type
                        , case when event_type = 1 then 'score' 
                               when  event_type = 2 then 'schedule' 
                          end type_desc
                        , state
                        , sport");
        $scores->groupBy(DB::raw("date_format(created_at, '%m/%d/%Y'), event_type, state, sport"));
        $scores->orderBy('date', 'desc');
        $scores->orderBy('state', 'asc');
        $scores->orderBy('sport', 'asc');
        $scores->orderBy('total', 'asc');
//        dd($scores->toSql());
        $scores = $scores->get();


        return $this->printAll($scores);
    }

    /**
     * Create new an Area Manager
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setAmArea (Request $request) {

        $this->validate($request, [
            'area_name'    => 'required',
            'area_state'   => 'required',
        ]);

        $params = $request->all();

        $ws_mygspn = new Client([
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);

        try {

            $tools = new ToolsController();
            $res = $ws_mygspn->request('POST', env('MYGSPN_API_URL') . '/admin/create_am_area',
                ['body' => json_encode([
                    'area_name' => $params['area_name'],
                    'area_state' => $params['area_state'],
                    'token' => env('MYGSPN_TOKEN')
                ])]
            )->getBody()->getContents();
            $data = $tools->str_to_array($res);
            if( !$data['success'] ) {
                return $this->errorResponse($data, 422);
            } else {
                $tools->createColorsAmAreas(true);
            }

        } catch (\Exception $e){
            return $this->errorResponse($e->getMessage());
        }

        return $this->printData(($data));

    }

    public function updAmArea (Request $request) {

        $this->validate($request, [
            'area_name'    => 'required',
            'area_state'   => 'required',
            'area_id'   => 'required',
        ]);

        $params = $request->all();

        $ws_mygspn = new Client([
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);

        try {

            $tools = new ToolsController();
            $res = $ws_mygspn->request('POST', env('MYGSPN_API_URL') . '/admin/update_am_area',
                ['body' => json_encode([
                    'area_name' => $params['area_name'],
                    'area_state' => $params['area_state'],
                    'area_id' => $params['area_id'],
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

    public function delAmArea (Request $request) {

        $this->validate($request, [
            'area_id'   => 'required',
        ]);

        $params = $request->all();

        $ws_mygspn = new Client([
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);

        try {

            $tools = new ToolsController();
            $res = $ws_mygspn->request('POST', env('MYGSPN_API_URL') . '/admin/delete_area',
                ['body' => json_encode([
                    'area_id' => $params['area_id'],
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
