<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'username'=>'required|min:4',
            'password'=> 'required|min:6'
        ],
        [
            'required' => ':attribute harus diisi',
            'min'=>':attribute minimal :min karakter',
            'unique'=> ':attribute sudah terdaftar'
        ]);

        if ($validator->fails())
        {
            $resp = [
                'metadata'=>[
                    'message'=>$validator->errors()->first(),
                    'code'=>422
                ]
                ];
                return response()->json($resp,422);
                die();
        }

        $user = Users::where('username',$request->username)->first();
        if($user)
        {
            if(Crypt::decrypt($user->password)==$request->password)
            {
                $token = \Auth::login($user);
                $resp = [
                    'response'=>[
                        'token'=>$token
                    ],
                    'metadata'=>[
                        'message'=>'OK',
                        'code'=>200
                    ]
                    ];
                    return response()->json($resp);
            } else {
                $resp = [
                    'metadata'=>[
                        'message'=> 'Username atau Password tidak sesuai.',
                        'code'=>401
                    ]
                    ];
                    return response()->json($resp,401);
            }
        } else {
            $resp = [
                'metadata'=> [
                    'message'=> 'Username atau Password tidak sesuai.',
                    'code'=>401
                ]
                ];
                return response()->json($resp,401);
        }
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
            $data['password'] = Crypt::encrypt($data['password']);

            // return response()->json($data);
            Users::create($data);
            // Users::create([
            //     'name'=> $request->name,
            //     'address'=> $request->address,
            //     'phone'=> $request->phone,
            //     'username'=> $request->username,
            //     'password'=> $pass_encrypt
            // ]);

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
