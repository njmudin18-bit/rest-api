<?php

namespace App\Repositories;

use App\Models\PoHeader;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PoHeaderRepository
{
  public function get_po_perbulan($table)
  {
    $data = DB::connection('BJGMAS01')
      ->table($table)
      ->orderByDesc('TGL')
      ->get();

    if ($data == null) {
      return $result = array(
        'code'    => 404,
        'status'  => 'error',
        'message' => 'Data PO tidak ditemukan',
        'data'    => $data
      );
    } else {

      $jlh = DB::connection('BJGMAS01')
        ->table($table)
        ->orderByDesc('TGL')
        ->count();

      $jlh_data = $jlh == 0 ? 0 : $jlh;

      return $result = array(
        'code'        => 200,
        'status'      => 'success',
        'message'     => 'Data PO ditemukan',
        'jumlah_data' => $jlh_data,
        'table'       => $table,
        'data'        => $data
      );
    }
  }


  public function get_all_po($table)
  {
    $data = DB::connection('BJGMAS01')
      ->table($table)
      ->orderByDesc('TGL')
      //->limit(2000)
      ->get();

    if ($data == null) {
      return $result = array(
        'code'    => 404,
        'status'  => 'error',
        'message' => 'Data PO tidak ditemukan',
        'data'    => $data
      );
    } else {

      $jlh = DB::connection('BJGMAS01')
        ->table($table)
        ->orderByDesc('TGL')
        ->count();

      $jlh_data = $jlh == 0 ? 0 : $jlh;

      return $result = array(
        'code'        => 200,
        'status'      => 'success',
        'message'     => 'Data PO ditemukan',
        'jumlah_data' => $jlh_data,
        'data'        => $data
      );
    }
  }
}
