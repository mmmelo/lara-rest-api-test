<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class QrCode_generator extends Model
{
    public function create(){
        $Qrcode = QrCode::generate('hello world');

        return Qrcode;
    }
}
