<?php

namespace App\Helpers;

use Request;
use Illuminate\Support\Facades\DB;

class LogActivity
{
  public static function addToLog($subject, $data = null, $old_data = null)
  {
    $log                  = [];
    $log['subject']       = $subject;
    $log['url']           = Request::fullUrl();
    $log['method']        = Request::method();
    $log['data']          = $data;
    $log['old_data']      = $old_data;
    $log['agent']         = Request::header('user-agent');
    $log['user_id']       = auth()->check() ? auth()->user()->id : 'API';
    $log['created_at']    = date('Y-m-d H:i:s');

    $insert = DB::connection('MYSQL_LOCAL')->table('log_activities')->insert($log);
  }
}
