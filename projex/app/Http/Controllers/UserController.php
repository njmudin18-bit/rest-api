<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpParser\Node\Stmt\TryCatch;
use Laravel\Passport\RefreshTokenRepository;

use GuzzleHttp\Client;
use Laravel\Passport\Client as OClient;

class UserController extends Controller
{
  public function register(Request $request)
  {
    $inputRequest = $request->input();
    $validator = Validator::make($inputRequest, [
      'email'     => 'unique:users|email|required|string|min:3',
      'name'      => 'required|string|min:3',
      'password'  => 'required|string|min:4'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    $userRepo   = new UserRepository();
    $response   = $userRepo->create($inputRequest);

    return response()->json($response);
  }

  public function login(Request $request)
  {
    $input_request = $request->input();
    $validator = Validator::make($input_request, [
      'email'     => 'required|exists:users|email',
      'password'  => 'required|min:7',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    $data = [
      'email'     => $input_request['email'],
      'password'  => $input_request['password']
    ];

    if (auth()->attempt($data)) {
      $user       = Auth::user();
      $objToken   = auth()->user()->createToken('LaravelAuthApp');
      $strToken   = $objToken->accessToken;
      $expiration = $objToken->token->expires_at->diffInSeconds(Carbon::now());

      return response()->json([
        'code'        => 200,
        'status'      => "success",
        'message'     => "Anda sukses login",
        'token'       => $strToken,
        'expires_in'  => $expiration,
        'data'        => $user
      ], 200);
    } else {

      return response()->json([
        'error' => 'Email atau Password salah',
        'status' => false
      ], 401);
    }
  }

  public function profile(Request $request)
  {
    $user       = Auth::user();

    $result['code']       = 200;
    $result['status']     = "success";
    $result['message']    = "Sukses menampilkan data";
    $result['data']       = $user;

    return response()->json($result, 200);
  }

  public function get_all_user()
  {
    $userRepo       = new UserRepository();
    $response       = $userRepo->show_all_data();

    return response()->json($response);
  }

  public function get_user_details(Request $request)
  {
    $input_request  = $request->input();
    $validator      = Validator::make($input_request, [
      'user_id'     => 'required|min:1'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    $id             = $input_request['user_id'];
    $userRepo       = new UserRepository();
    $response       = $userRepo->show_one_data($id);

    return response()->json($response);
  }

  public function update_user(Request $request, $id)
  {
    $inputRequest = $request->input();
    $validator = Validator::make($inputRequest, [
      'email'     => 'email|required|string|min:3',
      'name'      => 'required|string|min:3'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    $userRepo   = new UserRepository();
    $response   = $userRepo->update_user($inputRequest, $id);

    return response()->json($response);
  }

  public function update_password(Request $request, $id)
  {
    $inputRequest = $request->input();
    $validator = Validator::make($inputRequest, [
      'password'  => 'min:7|required_with:confirm_password|same:confirm_password',
      'confirm_password' => 'min:7'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    $userRepo   = new UserRepository();
    $response   = $userRepo->update_password($inputRequest, $id);

    return response()->json($response);
  }

  public function delete(Request $request)
  {
    $input_request  = $request->input();
    $validator      = Validator::make($input_request, [
      'user_id'     => 'required|min:1',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors());
    }

    $id             = $input_request['user_id'];
    $userRepo       = new UserRepository();
    $response       = $userRepo->delete($id);

    return response()->json($response);
  }

  public function logout(Request $request)
  {
    $token = auth()->user()->token();

    $token->revoke();
    $token->delete();

    $refreshTokenRepository = app(RefreshTokenRepository::class);
    $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);

    return response()->json([
      'code'    => 200,
      'status'  => 'success',
      'message' => 'Logged out successfully'
    ], 200);
  }
}
