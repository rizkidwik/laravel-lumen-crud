<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login (Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username'=>'required|string',
            'password'=> 'required|string'
        ],
        [
            'required' => ':attribute harus diisi',
            'min'=>':attribute minimal :min karakter',
            'unique'=> ':attribute sudah terdaftar'
        ]);

        $credentials = $request->only(['username','password']);

        if(!$token = Auth::attempt($credentials)){
            return response()->json(['message'=> 'Unauthorized'],401);
        }
        return $this->respondWithToken($token);
    }

    public function register (Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'address'=>'required',
            'phone'=>'required',
            'username'=>'required|min:4|unique:users,username',
            'password'=> 'required|min:6'
        ],
        [
            'required' => ':attribute harus diisi',
            'min'=>':attribute minimal :min karakter',
            'unique'=> ':attribute sudah terdaftar'
        ]);

        if (!$validator->fails())
        {
            $data = $request->all();
            // $pass_encrypt = Crypt::encrypt($data['password']);
            $data['password'] = app('hash')->make($data['password']);

            // return response()->json($data);
            Users::create($data);

            return response()->json([
                'message' => "User berhasil terdaftar",
                'data' => $data
            ],200);
        }

        $resp = [
            'metadata'=>[
                'message'=>$validator->errors()->first(),
                'code'=>422
            ]
            ];
            return response()->json($resp,422);
            die();
    }
}
