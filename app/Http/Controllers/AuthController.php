<?php

namespace App\Http\Controllers;

use App\User;
Use App\Http\Requests\LoginRequest;
Use App\Http\Requests\RegisterRequest;
Use App\Http\Requests\UpdateProfileRequest;
Use App\Http\Requests\ChangePasswordRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function construct()
    {

    }

    protected function respondWithToken($token)
    {
        $code = 200;
        return response()->json([
                    'code' => $code,
                    'message' => 'success',
                    'content' => [
                                    'access_token' => $token,
                                    'token_type'   => 'bearer',
                                    'expires_in'   => auth('api')
                                    ->factory()->getTTL() * 60
                                ]
                    ], $code
                );
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $token = auth()->login($user);
        return $this->respondWithToken($token);
    }

    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                $code = 404;
                $response =['code' => $code,
                            'message' => 'email atau password yang anda masukan salah'];
                            return response()->json($response, $code);
            }
        } catch (JWTException $e) {
            $response = ['status' => $e];
            return response()->json($response, 404);
        }
            return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $logout = JWTAuth::invalidate();
        $code   = 200;
        $response = [
                     'code'    => $code,
                     'message' => 'Berhasil Logout',
                     'content' => $logout
                    ];
        return response()->json($response, $code);
    }

    public function refresh()
    {
        $code = 200;
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token);
        $response = ['code' => $code, 
                     'message' => 'new token',
                     'content' => $newToken];
        return response()->json($response, $code);

    }

    public function updateName(UpdateProfileRequest $request) 
    {  
        $cek = JWTAuth::parseToken()->authenticate();
        $user_id = $cek->id;
        $name = $request->name;
        $email = $request->email;
        $user = User::find($user_id);
        $user->name = $name;
        $user->email = $email;
        $user->save();
        $code = 200;
        $response = ['code' => $code, 
                     'message' => 'berhasil ubah profile',
                     'content' => $user];
        return response()->json($response, $code);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $cek = JWTAuth::parseToken()->authenticate();
        $oldpassword = $request->old_password;
        $credentials = ['email' => $cek->email , 'password' => $oldpassword]; 
            if (!$token = JWTAuth::attempt($credentials)) {
                $code = 404;
                $response =['code' => $code,
                            'message' => 'Old password salah'];
                            return response()->json($response, $code);
            }

        $user_id = $cek->id;
        $password = $request->password;
        $password_confirmation = $request->password_confirmation;

        $user = User::find($user_id);
        $user->password = $password;
        $user->save();
        $code = 200;
        $response = ['code' => $code, 
                     'message' => 'berhasil ubah password',
                     'content' => $user];
        return response()->json($response, $code);
    }

}
