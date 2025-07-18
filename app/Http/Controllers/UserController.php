<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // ✅ عرض كل المستخدمين
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // ✅ عرض مستخدم واحد بالتفصيل
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }
    

    // ✅ إنشاء مستخدم جديد (نفس دالة register تقريباً)
    public function store(Request $request)
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
    


public function update(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // ✅ تنظيف القيم المُدخلة (فقط إذا تم إرسالها)
    if ($request->has('name')) {
        $request->merge([
            'name' => trim($request->name),
        ]);
    }

    if ($request->has('slug')) {
        $cleanSlug = Str::slug(trim($request->slug));
        $request->merge([
            'slug' => $cleanSlug,
        ]);
    }

    // ✅ التحقق من صحة البيانات بعد التنظيف
    $request->validate([
        'name' => 'sometimes|required|string',
        'slug' => 'sometimes|required|string|unique:users,slug,' . $user->userID . ',userID',
        'startDate' => 'sometimes|required|date',
        'endDate' => 'nullable|date',
    ]);

    // ✅ تحديث المستخدم بالبيانات النظيفة
    $user->update($request->all());

    return response()->json(['message' => 'User updated', 'user' => $user]);
}





    // ✅ حذف مستخدم
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
