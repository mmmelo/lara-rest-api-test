<?php
/**
 * Created by PhpStorm.
 * User: rubencastro
 * Date: 2019-02-13
 * Time: 12:15
 */

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait Update_media_pivot{
    //Soft Delete
    public function softDelete_media_pivot($group_id,$media_id)
    {
        DB::table('media_relation')
            ->where('group_id',$group_id)
            ->where('media_id',$media_id)
            ->update(['deleted_at'=> DB::raw('NOW()')]);
    }
    //soft Restore
    public function softRestore_media_pivot($group_id,$media_id)
    {
        DB::table('media_relation')
            ->where('group_id',$group_id)
            ->where('media_id',$media_id)
            ->update(['deleted_at'=> null]);
    }
}