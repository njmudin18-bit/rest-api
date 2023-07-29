<?php

namespace App\Http\Controllers;

use App\Repositories\PoHeaderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpParser\Node\Stmt\TryCatch;
use Laravel\Passport\RefreshTokenRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\DB;
use Response;

class PoController extends Controller
{
  //SEND PO TO HOSTING
  public function send_po_to_live()
  {
    //CEK STATUS UPLOAD
    $count = DB::connection('MYSQL_LOCAL')
      ->table('tbl_po')
      ->where('StatusUploadWeb', '=', 1)
      ->count();

    //CEK DATA APAKAH MASIH ADA
    if ($count > 0) {
      //GET DATA PO
      $data = DB::connection('MYSQL_LOCAL')
        ->table('tbl_po')
        ->where('StatusUploadWeb', '=', 1)
        ->get();

      $jumlah_data_kirim  = count($data);
      $data_insert        = array();
      foreach ($data as $key => $val) {
        $NoBukti  = $val->NoBukti;
        $cek_po = DB::connection('MYSQL_HOSTING')
          ->table('tbl_po_online')
          ->where('NoBukti', '=', $NoBukti)
          ->count();

        if ($cek_po == 0) {
          $data_insert[] = array(
            'NoBukti'           => $val->NoBukti,
            "POParent"          => $val->POParent,
            "TGL"               => $val->TGL,
            "Tgl_Needed"        => $val->Tgl_Needed,
            "ShipmentNotes"     => $val->ShipmentNotes,
            "TGL_JatuhTempo"    => $val->TGL_JatuhTempo,
            "isImport"          => $val->isImport,
            "isAsset"           => $val->isAsset,
            "isBDP"             => $val->isBDP,
            "Status"            => $val->Status,
            "NoContract"        => $val->NoContract,
            "SupplierID"        => $val->SupplierID,
            "ShipmentTo"        => $val->ShipmentTo,
            "Term"              => $val->Term,
            "NilaiTukar"        => $val->NilaiTukar,
            "ConditionID"       => $val->ConditionID,
            "PaymentID"         => $val->PaymentID,
            "ConsigneeID"       => $val->ConsigneeID,
            "PelabuhanID"       => $val->PelabuhanID,
            "TipePPN"           => $val->TipePPN,
            "PPN"               => $val->PPN,
            "MataUang"          => $val->MataUang,
            "Discount"          => $val->Discount,
            "Fee"               => $val->Fee,
            "isWIP"             => $val->isWIP,
            "F_Print"           => $val->F_Print,
            "InvID"             => $val->InvID,
            "JurnalID"          => $val->JurnalID,
            "OnBoardDate"       => $val->OnBoardDate,
            "Keterangan"        => $val->Keterangan,
            "KeteranganJasa"    => $val->KeteranganJasa,
            "ExportWeb"         => $val->ExportWeb,
            "StatusUploadWeb"   => $val->StatusUploadWeb,
            "CreateDate"        => $val->CreateDate,
            "CreateBy"          => $val->CreateBy,
            "CompanyCode"       => $val->CompanyCode,
            "TglUploadWeb"      => date('Y-m-d H:i:s')
          );
        }
      }

      $jumlah_data_simpan = count($data_insert);
      $data_insert        = collect($data_insert);
      $chunks             = $data_insert->chunk(500);
      $insert             = "";
      foreach ($chunks as $chunk) {
        $insert .= DB::connection('MYSQL_HOSTING')->table('tbl_po_online')->insert($chunk->toArray());
      }

      if ($insert !== null) {

        foreach ($data as $key => $value) {
          //FUNCTION UPDATE
          $update = DB::connection('MYSQL_LOCAL')
            ->table('tbl_po')
            ->where('NoBukti', $value->NoBukti)
            ->update(
              array(
                'StatusUploadWeb'     => 2,
                'TglUpdateStatusWeb'  => date('Y-m-d H:i:s')
              )
            );
        }

        $response = Response::json([
          'code'                => 200,
          'status'              => "success",
          'message'             => "Data PO berhasil disimpan.",
          'jumlah_data_kirim'   => $jumlah_data_kirim,
          'jumlah_data_simpan'  => $jumlah_data_simpan
        ], 200);

        //SAVE TO LOG
        \LogActivity::addToLog('ADD API', json_encode($response));

        return $response;
      } else {
        $response = Response::json([
          'code'                => 400,
          'status'              => "error",
          'message'             => "Data PO gagal disimpan.",
          'jumlah_data_kirim'   => $jumlah_data_kirim,
          'jumlah_data_simpan'  => $jumlah_data_simpan
        ], 400);

        //SAVE TO LOG
        \LogActivity::addToLog('ADD API', json_encode($response));

        return $response;
      }
    } else {
      $response = Response::json([
        'code'                => 404,
        'status'              => "error",
        'message'             => "Data PO dengan StatusUploadWeb = 1, tidak ditemukan.",
        'jumlah_po_terkirim'  => $count,
        'data'                => array()
      ], 404);

      //SAVE TO LOG
      \LogActivity::addToLog('ADD API', json_encode($response));

      return $response;
    }
  }

  public function send_po_to_live_OLD()
  {
    //CEK STATUS UPLOAD
    $count = DB::connection('MYSQL_LOCAL')
      ->table('tbl_po')
      ->where('StatusUploadWeb', '=', 1)
      ->count();

    //CEK DATA APAKAH MASIH ADA
    if ($count > 0) {
      //GET DATA PO
      $data = DB::connection('MYSQL_LOCAL')
        ->table('tbl_po')
        ->where('StatusUploadWeb', '=', 1)
        ->get();

      //DISINI NANTI AKAN ADA CURL DARI HOSTINGAN START
      /** YOUR API CODE HERE */
      // $curl = curl_init();
      // curl_setopt_array($curl, array(
      //   CURLOPT_URL => 'http://localhost:8012/omas-admin-page/po/save_po_from_local',
      //   CURLOPT_RETURNTRANSFER => true,
      //   CURLOPT_ENCODING => '',
      //   CURLOPT_MAXREDIRS => 10,
      //   CURLOPT_TIMEOUT => 0,
      //   CURLOPT_FOLLOWLOCATION => true,
      //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      //   CURLOPT_CUSTOMREQUEST => 'POST',
      //   CURLOPT_POSTFIELDS => array('data' => $data),
      //   CURLOPT_HTTPHEADER => array(
      //     'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL',
      //     'Cookie: ci_session=ifn8bu2lov9i84d5sn594po4t4js1suu'
      //   ),
      // ));
      // $response = curl_exec($curl);
      // curl_close($curl);

      // $curl = curl_init();
      // curl_setopt_array($curl, array(
      //   CURLOPT_URL => 'https://one-editor.omas-mfg.com/po/save_po_from_local',
      //   CURLOPT_RETURNTRANSFER => true,
      //   CURLOPT_ENCODING => '',
      //   CURLOPT_MAXREDIRS => 10,
      //   CURLOPT_TIMEOUT => 0,
      //   CURLOPT_FOLLOWLOCATION => true,
      //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      //   CURLOPT_CUSTOMREQUEST => 'POST',
      //   CURLOPT_POSTFIELDS => array('data' => $data),
      //   CURLOPT_HTTPHEADER => array(
      //     'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL'
      //   ),
      // ));
      // $response = curl_exec($curl);

      curl_close($curl);
      //echo $response;
      $hasil = json_decode($response, TRUE);

      //JIKA KIRIM KE SERVER HOSTING SUKSES
      if ($hasil['status'] == 'success') {
        $data_update = array();
        foreach ($data as $key => $value) {
          //FUNCTION UPDATE
          $update = DB::connection('MYSQL_LOCAL')
            ->table('tbl_po')
            ->where('NoBukti', $value->NoBukti)
            ->update(
              array(
                'StatusUploadWeb'     => 2,
                'TglUpdateStatusWeb'  => date('Y-m-d H:i:s')
              )
            );
        }

        if ($update) {
          $response = Response::json([
            'code'                => 200,
            'status'              => "success",
            'message'             => "Data PO sukses terkirim dan berhasil diupdate",
            'jumlah_po_terkirim'  => count($data)
          ], 200);

          \LogActivity::addToLog('ADD API', json_encode($response));

          return $response;
        } else {
          $response = Response::json([
            'code'                => 500,
            'status'              => "error",
            'message'             => "Data PO gagal terkirim dan gagal diupdate",
            'jumlah_po_terkirim'  => count($data)
          ], 500);

          \LogActivity::addToLog('ADD API', json_encode($response));

          return $response;
        }
      } else {
        return response()->json(array(
          'code'    => 500,
          'status'  => 'error',
          'message' => 'Data PO gagal terkirim ke server',
          'data'    => array()
        ), 500);
      }
      //DISINI NANTI AKAN ADA CURL DARI HOSTINGAN END

      // $response = Response::json([
      //   'code'                => 200,
      //   'status'              => "success",
      //   'message'             => "Data PO dengan StatusUploadWeb = 1, ditemukan.",
      //   'jumlah_po_terkirim'  => count($data)
      // ], 200);

      // //SAVE TO LOG
      // \LogActivity::addToLog('ADD API', json_encode($response));
    } else {
      $response = Response::json([
        'code'                => 404,
        'status'              => "error",
        'message'             => "Data PO dengan StatusUploadWeb = 1, tidak ditemukan.",
        'jumlah_po_terkirim'  => $count,
        'data'                => array()
      ], 404);

      //SAVE TO LOG
      \LogActivity::addToLog('ADD API', json_encode($response));

      return $response;
    }
  }

  //SAVE PO DARI MSSQL KE MYSQL
  public function save_po_to_local(Request $request)
  {
    //GET DATA PO FROM SERVER 3 MSSQL
    $bulan      = date('m');
    $tahun      = date('Y');
    $table      = "Trans_POHD" . $tahun . $bulan; //Trans_POHD202305;
    $data       = new PoHeaderRepository();
    $response   = $data->get_po_perbulan($table);

    //STORING DATA TO MYSQL
    $hasil              = $response;
    $jumlah_data_kirim  = $hasil['jumlah_data'];
    $insert_data        = array();
    foreach ($hasil['data'] as $key => $value) {
      $Discount     = $value->Discount == '.0000' ? 0 : $value->Discount;
      $Fee          = $value->Fee == '.0000' ? 0 : $value->Fee;

      $check = DB::connection('MYSQL_LOCAL')
        ->table('tbl_po')
        ->where('NoBukti', $value->NoBukti)
        ->count();
      if ($check == 0) {
        $insert_data[]  = array(
          'NoBukti'         => $value->NoBukti,
          'POParent'        => $value->POParent,
          'TGL'             => substr($value->TGL, 0, -4),
          'Tgl_Needed'      => substr($value->Tgl_Needed, 0, -4),
          'ShipmentNotes'   => $value->ShipmentNotes,
          'TGL_JatuhTempo'  => substr($value->TGL_JatuhTempo, 0, -4),
          'isImport'        => $value->isImport,
          'isAsset'         => $value->isAsset,
          'isBDP'           => $value->isBDP,
          'Status'          => $value->Status,
          'NoContract'      => $value->NoContract,
          'SupplierID'      => $value->SupplierID,
          'ShipmentTo'      => $value->ShipmentTo,
          'Term'            => $value->Term,
          'NilaiTukar'      => $value->NilaiTukar,
          'ConditionID'     => $value->ConditionID,
          'PaymentID'       => $value->PaymentID,
          'ConsigneeID'     => $value->ConsigneeID,
          'PelabuhanID'     => $value->PelabuhanID,
          'TipePPN'         => $value->TipePPN,
          'PPN'             => $value->PPN,
          'MataUang'        => $value->MataUang,
          'Discount'        => $Discount,
          'Fee'             => $Fee,
          'isWIP'           => $value->isWIP,
          'F_Print'         => $value->F_Print,
          'InvID'           => $value->InvID,
          'JurnalID'        => $value->JurnalID,
          'OnBoardDate'     => substr($value->OnBoardDate, 0, -4),
          'Keterangan'      => $value->Keterangan,
          'KeteranganJasa'  => $value->KeteranganJasa,
          'ExportWeb'       => $value->ExportWeb,
          'StatusUploadWeb' => 1, //status belum di upload
          'CreateDate'      => substr($value->CreateDate, 0, -4),
          'CreateBy'        => $value->CreateBy,
          'CompanyCode'     => $value->CompanyCode,
          'TglUploadWeb'    => date('Y-m-d H:i:s')
        );
      }
    }

    $jumlah_data_simpan = count($insert_data);
    $insert_data        = collect($insert_data);
    $chunks             = $insert_data->chunk(500);
    $insert             = "";
    foreach ($chunks as $chunk) {
      $insert .= DB::connection('MYSQL_LOCAL')->table('tbl_po')->insert($chunk->toArray());
    }

    if ($insert !== null) {
      $response = Response::json([
        'code'                => 200,
        'status'              => "success",
        'message'             => "Data PO berhasil disimpan.",
        'jumlah_data_kirim'   => $jumlah_data_kirim,
        'jumlah_data_simpan'  => $jumlah_data_simpan
      ], 200);

      //SAVE TO LOG
      \LogActivity::addToLog('ADD API', json_encode($response));

      return $response;
    } else {
      $response = Response::json([
        'code'                => 400,
        'status'              => "error",
        'message'             => "Data PO gagal disimpan.",
        'jumlah_data_kirim'   => $jumlah_data_kirim,
        'jumlah_data_simpan'  => $jumlah_data_simpan
      ], 400);

      //SAVE TO LOG
      \LogActivity::addToLog('ADD API', json_encode($response));

      return $response;
    }
  }

  public function get_data_po(Request $request)
  {
    $input_request  = $request->input();
    $validator      = Validator::make($input_request, [
      'key'     => 'required|min:5',
      // 'bulan'   => 'required|min:2',
      // 'tahun'   => 'required|min:4'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    //key = APIptmas2023
    if ($input_request['key'] == '$2y$10$SwtdtGKfOxC49E0uZU4biOoSdI6yxuGwLhkMdsnFQcUjBMQmcDlSu') {

      $bulan      = date('m'); //$input_request['bulan'];
      $tahun      = date('Y'); //$input_request['tahun'];
      $table      = "Trans_POHD" . $tahun . $bulan; //Trans_POHD202305;
      $data       = new PoHeaderRepository();
      $response   = $data->get_all_po($table);

      return response()->json($response, 200);
    } else {
      return response()->json(array(
        'code'    => 403,
        'status'  => 'error',
        'message' => 'Key salah',
        'data'    => array()
      ), 403);
    }
  }
}
