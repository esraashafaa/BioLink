<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:users,slug',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
        ]);

        
        $token = JWTAuth::fromUser($user);
        $user->token = $token;
        $user->save();
        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            // 'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'slug' => 'required|string',
        ]);

        $user = User::where('slug', $request->slug)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        
        $token = JWTAuth::fromUser($user);

        $user->token = $token;
        $user->save();

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            // 'user' => $user
        ]);
    }
}
