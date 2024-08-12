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

        $admin = User::where('username', $request->username)->first();

        if (!$admin || $request->password !== $admin->password) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }

        $token = Str::random(60);

        if (!$admin->remember_token) {
            $admin->remember_token = $token;
            $admin->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'username' => $admin->username,
                    'phone' => $admin->phone,
                    'email' => $admin->email,
                ],
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization'); 
        $user = User::where('remember_token', $token)->first();

        if ($user) {
            $user->rememberToken = null;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',

        ], 200);
    }
}
