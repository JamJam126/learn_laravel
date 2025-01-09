<?php

namespace App\Http\Controllers;

use App\Models\User; // FOR USER::
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // FOR HASH::  
use Illuminate\Support\Str; // FOR STR::

class AuthController extends Controller
{  
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'api_token' => Str::random(60),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $user->api_token,
            'user' => $user,
        ]);
    }

    public function login(Request $request) 
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = Str::random(60);
        $user->api_token = $token;
        $user->save();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        // Attempt to get the user based on the provided token
        $user = User::where('api_token', $request->bearerToken())->first();

        if ($user) {
            // CLEAR THE USER API TOKEN TO LOG THEM OUT
            $user->api_token = null;
            $user->save();

            return response()->json(['message' => 'Logged out successfully'], 200);
        }

    return response()->json(['message' => 'Unauthorized'], 401);
    }
}
