<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
         
    }

    public function register(Request $request)
    {
       
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $token = auth()->login($user);

        return $this->responWithToken($token);
    }


    public function login()
    {
        $credentials = request(['email', 'password']);

        try {
            if (!$token = JWTAuth::attempt($credentials)) { 
                            $code = 404;
                            $response = ['code' => $code, 'message' => 'email yang anda masukan salah'];
                            return response()->json([$response => $code]);
            
                   }
        } catch (JWTException $e) {
            $response = ['status' => $e];
            return response()->json($response, 404);
        }
        return $this->responWithToken($token);
    }


    public function refresh()
    {
        return $this->responWithToken(auth()->refresh);
    }

    public function responWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function me(){
        return response()->json(auth()->user());
    }
}
