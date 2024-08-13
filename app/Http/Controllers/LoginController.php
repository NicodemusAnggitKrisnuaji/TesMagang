<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'data' => null
            ], 422);
        }

        $User = User::where('username', $request->username)->first();

        if (!$User || $request->password !== $User->password) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }

        $token = Str::random(60);

        if (!$User->remember_token) {
            $User->remember_token = $token;
            $User->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'admin' => [
                    'id' => $User->id,
                    'name' => $User->name,
                    'username' => $User->username,
                    'phone' => $User->phone,
                    'email' => $User->email,
                ],
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $user = User::where('remember_token', $token)->first();
    
        if ($user) {
            $user->remember_token = null;
            $user->save();
        } 

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ], 200);
    }
    
}
