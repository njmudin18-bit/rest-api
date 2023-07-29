<?php

namespace App\Http\Controllers;

use App\Repositories\TransRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpParser\Node\Stmt\TryCatch;
use Laravel\Passport\RefreshTokenRepository;
use GuzzleHttp\Client;
use Laravel\Passport\Client as OClient;

class TransController extends Controller
{
  public function get_nomor_do(Request $request)
  {
    $input_request  = $request->input();
    $validator      = Validator::make($input_request, [
      'no_do'     => 'required|min:5'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    $do             = $input_request['no_do'];
    $dataRepo       = new TransRepository();
    $response       = $dataRepo->cek_nomor_do($do);

    return response()->json($response, 200);
  }
}
