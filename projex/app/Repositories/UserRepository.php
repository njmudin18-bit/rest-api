<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserRepository
{

  public function create($input)
  {
    try {
      $user = User::create([
        'name'      => $input['name'],
        'email'     => $input['email'],
        'password'  => bcrypt($input['password']),
      ]);

      if ($user->save()) {
        $result['code']     = 201;
        $result['status']   = "success";
        $result['message']  = "Berhasil Membuat User baru";

        $authUser = [
          'email'     => $input['email'],
          'password'  => $input['password'],
        ];

        if (auth()->attempt($authUser)) {
          $user   = Auth::user();
          $result = array_merge($result, [
            'data'  => $user
          ]);
        }

        return $result;
      } else {
        $result['code']     = 500;
        $result['status']   = "error";
        $result['message']  = "Gagal Membuat User baru";
        $result['data']     = (object)[];
        return $result;
      }
    } catch (\Exception $e) {
      $result['code']         = 500;
      $result['status']       = "error";
      $result['message']      = "Gagal Update User";
      $result['data']         = (object)[];
      $result['error'] = [
        'message'       => $e->getMessage(),
        'file'          => $e->getFile(),
        'line_of_code'  => $e->getLine(),
        'code'          => $e->getCode(),
      ];
      return $result;
    }
  }

  public function delete($id)
  {
    $user   = User::where('id', $id)->first();
    if ($user != NULL) {
      try {
        if ($user->delete($id)) {
          $result['code']       = 200;
          $result['status']     = "success";
          $result['message']    = "Berhasil Delete user";
          $result['data']       = $user;
        }
        return $result;
      } catch (\Exception $e) {
        $result['code']         = 500;
        $result['status']       = "error";
        $result['message']      = "Gagal Delete User";
        $result['data']         = (object)[];
        $result['error'] = [
          'message'       => $e->getMessage(),
          'file'          => $e->getFile(),
          'line_of_code'  => $e->getLine(),
          'code'          => $e->getCode(),
        ];
        return $result;
      }
    } else {
      $result['code']       = 404;
      $result['status']     = "error";
      $result['message']    = "Data tidak ditemukan";
      $result['data']       = array();

      return $result;
    }
  }

  public function show_all_data()
  {
    $user = User::select('*')
      ->orderByDesc('id')
      ->get();

    $result['code']       = 200;
    $result['status']     = "success";
    $result['message']    = "Sukses menampilkan data";
    $result['data']       = $user;

    return $result;
  }

  public function show_one_data($id)
  {
    $user = User::where('id', $id)->first();
    if ($user != NULL) {
      $result['code']       = 200;
      $result['status']     = "success";
      $result['message']    = "Data ditemukan";
      $result['data']       = $user;

      return $result;
    } else {
      $result['code']       = 404;
      $result['status']     = "error";
      $result['message']    = "Data tidak ditemukan";
      $result['data']       = array();

      return $result;
    }
  }

  public function update_user($inputRequest, $id)
  {
    try {
      $user = User::where('id', $id)->update($inputRequest);

      if ($user) {
        $user_update        = User::where('id', $id)->first();

        $result['code']     = 200;
        $result['status']   = "success";
        $result['message']  = "Sukses mengupdate user";
        $result['data']     = $user_update;

        return $result;
      } else {
        $result['code']     = 500;
        $result['status']   = "error";
        $result['message']  = "Gagal mengupdate user";
        $result['data']     = (object)[];
        return $result;
      }
    } catch (\Exception $e) {
      $result['code']         = 500;
      $result['status']       = "error";
      $result['message']      = "Gagal Update User";
      $result['data']         = (object)[];
      $result['error'] = [
        'message'       => $e->getMessage(),
        'file'          => $e->getFile(),
        'line_of_code'  => $e->getLine(),
        'code'          => $e->getCode(),
      ];
      return $result;
    }
  }

  public function update_password($inputRequest, $id)
  {
    try {
      $user = User::where('id', $id)->update([
        'password'  => bcrypt($inputRequest['password'])
      ]);

      if ($user) {
        $result['code']     = 200;
        $result['status']   = "success";
        $result['message']  = "Sukses mengupdate password";

        return $result;
      } else {
        $result['code']     = 500;
        $result['status']   = "error";
        $result['message']  = "Gagal mengupdate password";

        return $result;
      }
    } catch (\Exception $e) {
      $result['code']         = 500;
      $result['status']       = "error";
      $result['message']      = "Gagal mengupdate password";
      $result['error'] = [
        'message'       => $e->getMessage(),
        'file'          => $e->getFile(),
        'line_of_code'  => $e->getLine(),
        'code'          => $e->getCode(),
      ];

      return $result;
    }
  }
}
