<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * register
     *
     * @param mixed $request
     * @return void
     */
    public function register(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|max:255|unique:users|email:strict',
            'password'  => 'required|string'
          ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //register user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'data'          => $user,
            'access_token'  => $token,
            'token_type'    => 'Bearer'
        ], 201);
    }

    /**
     * login
     *
     * @param mixed $request
     * @return void
     */
    public function login(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email:rfc,dns',
            'password'  => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'error' => $validator->errors()], 422);
        }

        //check if email or password incorrect
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email or password is incorrect'
            ], 401);
        }

        $user   = User::where('email', $request->email)->firstOrFail();
        $token  = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'   => 'Login success',
            'user'      => [
                'name' => $user['name'],
                'email' => $user['email'],
                'access_token'  => $token,],
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}
