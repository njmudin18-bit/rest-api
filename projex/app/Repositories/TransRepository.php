<?php

namespace App\Repositories;

use App\Models\Trans;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransRepository
{
  public function cek_nomor_do($do)
  {
    $data = DB::connection('BJGMAS01')
      ->table('tbl_scanbarcode_approval')
      ->where('no_do', '=', $do)
      ->first();

    if ($data == null) {
      return $result = array(
        'code'    => 404,
        'status'  => 'error',
        'message' => 'Data DO: ' . $do . ' tidak ditemukan',
        'data'    => $data
      );
    } else {
      return $result = array(
        'code'    => 200,
        'status'  => 'success',
        'message' => 'Data DO: ' . $do . ' ditemukan',
        'data'    => $data
      );
    }
  }
}
