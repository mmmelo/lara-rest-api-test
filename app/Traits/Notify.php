<?php
namespace App\Traits;


use GuzzleHttp\Client;

trait Notify {

    protected function send_notify($slack_channel=null, $title=null, $text=null) {
        $ws_mygspn = new Client([
            'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ]
        ]);

        $params = array(
            'token' => env('MYGSPN_TOKEN'),
            'slack_channel' => $slack_channel,
            'title'      => $title,
            'text'      => $text,
        );

        $res = $ws_mygspn->request('POST',  env('MYGSPN_API_URL') . '/send_slack_notify',
            ['body' => json_encode( $params)]
        )->getBody()->getContents();

        return true;
    }

}
