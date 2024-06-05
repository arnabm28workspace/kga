<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\User;

class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),[
            'phone' => 'required|exists:users,phone',
            'password' => 'required',
            'mac_address' => 'required'
        ]);

        if (!$validator->fails()){
            $params = $request->except('_token');
            $phone = $params['phone'];
            $password = $params['password'];
            $checkUser = User::where('phone',$phone)->first();
            if(!empty($checkUser)){
                $checkPassword = Hash::check($password,$checkUser->password);
                if($checkPassword){
                    if(!empty($checkUser->mac_address)){
                        return Response::json(['status' => false, 'message' => "Already logged in a device. Please logout first" ],200);
                    }
                    User::where('id',$checkUser->id)->update([
                        'mac_address' => $params['mac_address']
                    ]);
                    $token = Crypt::encrypt($checkUser->id);
                    return Response::json(['status' => true, 'message' => "Logged in successfully", 'data' => array('token'=>$token,'user'=>$checkUser) ],200);
                }else{
                    return Response::json(['status' => false, 'message' => "Password mismatched" ],200);
                }
            }else{
                return Response::json(['status' => false, 'message' => "No user found" ],200);
            }
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }

    public function logout(Request $request)
    {
        # logout...

        if (! $request->hasHeader('Authorizations')) {
            response()->json(["status"=>false,"message"=>"Unauthorized"],400)->send();
            exit();
        } else {
            $bearer_token = $request->header('Authorizations');
            $token = str_replace("Bearer ","",$bearer_token);            
            try {
                $this->staff_id = Crypt::decrypt($token);  
                $staff = User::find($this->staff_id);
                
                User::where('id',$this->staff_id)->update([
                    'mac_address' => null
                ]);
                return Response::json(['status'=>true,'message'=>"Logged out successfully", 'data' => (object) array()],200);         
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
            }
        }

    }
}
