<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\User;

class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),[
            'phone' => 'required|exists:users,phone',
            'password' => 'required'
        ]);

        if (!$validator->fails()){
            $params = $request->except('_token');
            $phone = $params['phone'];
            $password = $params['password'];
            $checkUser = User::where('phone',$phone)->first();
            if(!empty($checkUser)){
                $checkPassword = Hash::check($password,$checkUser->password);
                if($checkPassword){
                    $token = Crypt::encrypt($checkUser->id);
                    return Response::json(['status' => true, 'message' => "Logged in successfully", 'data' => array('token'=>$token,'user'=>$checkUser) ],200);
                }else{
                    return Response::json(['status' => false, 'message' => "Password mismatched", 'data' => array() ],200);
                }
            }else{
                return Response::json(['status' => false, 'message' => "No user found", 'data' => array() ],200);
            }
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }
}
