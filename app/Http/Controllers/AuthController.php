<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
public function register(Request $request)
{
    // 1. تنظيف البيانات: إزالة الفراغات وتحويل slug
    $name = trim($request->name);
    $rawSlug = trim($request->slug);
    $cleanSlug = Str::slug($rawSlug); // مثل: "Hello World" => "hello-world"

    // 2. دمج القيم المعدّلة داخل الـ Request قبل التحقق منها
    $request->merge([
        'name' => $name,
        'slug' => $cleanSlug
    ]);

    // 3. التحقق من البيانات
    $request->validate([
        'name' => 'required|string',
        'slug' => 'required|string|unique:users,slug',
        'startDate' => 'required|date',
        'endDate' => 'nullable|date',
    ]);

    // 4. إنشاء المستخدم
    $user = User::create([
        'name' => $request->name,
        'slug' => $request->slug,
        'startDate' => $request->startDate,
        'endDate' => $request->endDate,
    ]);

    // 5. إنشاء التوكن
    $token = JWTAuth::fromUser($user);
    $user->token = $token;
    $user->save();

    // 6. الرد
    return response()->json([
        'message' => 'User registered successfully',
        'token' => $token,
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


    public function refresh(Request $request)
{
    try {
        $newToken = JWTAuth::parseToken()->refresh();

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $newToken,
        ]);
    } catch (TokenInvalidException $e) {
        return response()->json(['message' => 'Invalid token'], 401);
    } catch (JWTException $e) {
        return response()->json(['message' => 'Token is required'], 400);
    }
}

    public function me()
{
    try {
        $user = auth()->user();

        return response()->json([
            'message' => 'User data fetched successfully',
            'user' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}

}
