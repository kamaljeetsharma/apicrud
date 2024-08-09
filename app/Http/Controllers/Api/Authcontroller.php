<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class Authcontroller extends Controller
{
    public function signup(Request $request)
    {

$validateuser=Validator::make($request->all(),
    [
        'name'=>'required',
        'email'=>'required|email|unique:users,email',
        'password'=>'required|string|min:6'
    ]);
    
    if($validateuser->fails()){
        return response()->json(
            [
            'status'=>false,
            'message'=>'validation error',
            'errors'=>$validateuser->errors()->all()
        ],401);
    }

    $user=user::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>bcrypt($request->password),
    ]);
    return response()->json([
        'status'=>true,
        'message'=>'user created succesfully',
        'user'=>$user,
        ],200);
        
    }
    public function login(Request $request){

        $validateuser=Validator::make(
            $request->all(),
        
            [
                'email'=>'required|email',
                'password'=>'required',
            ]);
        if($validateuser->fails()){
            dd($validateuser->errors()->all()); 
            return response()->json([
                'status'=>false,
                'message'=>'authencation fails',
                'errors'=>$validateuser->errors()->all()
            ],404);
            }
        
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
        $authUser=Auth::User();
        return response()->json([
            'status'=>true,
            'message'=>'user logged in succesfully',
        
            'token'=>$authUser->createToken('API token')->plainTextToken,
            'token_type'=>'bearer',
                    ],200);
        
     } 
     else{
        return response()->json([
            'status'=>false,
            'message'=>'email and password doesnot match',
                    ],401);
                }
            }
    
    public function logout(Request $request){
$user=$request->user();
$user->tokens()->delete();
return response()->json([
    'status'=>true,
    'user'=>$user,
    'message'=>'you logged out successfully',
],200);

        
    }
}
