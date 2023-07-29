<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\TransController;
use App\Http\Controllers\PoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api']], function () {

  Route::patch('/user', [UserController::class, 'put']);

  Route::get('/user', [UserController::class, 'profile']);
  Route::post('/user/logout', [UserController::class, 'logout']);
  Route::post('/user/delete', [UserController::class, 'delete']);
  Route::post('/user/all', [UserController::class, 'get_all_user']);
  Route::post('/user/show-one', [UserController::class, 'get_user_details']);
  Route::post('/user/update/{id}', [UserController::class, 'update_user']);
  Route::post('/user/update-password/{id}', [UserController::class, 'update_password']);

  //BARU
  Route::post('/do/get-nomor-do', [TransController::class, 'get_nomor_do']);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);

Route::post('/po/get_po', [PoController::class, 'get_data_po']);
Route::get('/po/save_po_to_local', [PoController::class, 'save_po_to_local']);
Route::get('/po/send_po_to_live', [PoController::class, 'send_po_to_live']);

Route::get('unauthorized', function () {
  return response()->json([
    'code'    => 401,
    'status'  => 'unauthorized',
    'message' => 'Unauthorization.'
  ], 401);
})->name('unauthorized');
