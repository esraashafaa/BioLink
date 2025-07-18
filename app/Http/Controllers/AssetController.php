<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AssetController extends Controller
{
    
    public function getMyAssets(Request $request)
    {
        // استخراج المستخدم من التوكن
        $user = JWTAuth::parseToken()->authenticate();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    

        // إعادة استخدام دالة getAssetsByUserID
        return $this->getAssetsByUserID($user->userID);
    }
    
    public function getAssetsByUserID($userID)
    {
        $assets = Asset::where('userID', $userID)->get();

        if ($assets->isEmpty()) {
            return response()->json(['message' => 'No assets found for this user.'], 404);
        }

        return response()->json([
            'message' => 'Assets retrieved successfully.',
            'assets' => $assets
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'link' => 'required|url',
            'icon' => 'nullable|string',
            'type' => 'required|in:facebook,twitter,instagram,linkedin,tiktok,youtube,snapchat,whatsapp,telegram,pinterest,reddit,other',
            'userID' => 'required|exists:users,userID',
        ]);

        $asset = Asset::create($request->all());

        return response()->json([
            'message' => 'Asset created successfully.',
            'asset' => $asset
        ], 201);
    }

   
    public function show($id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        return response()->json($asset);
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string',
            'link' => 'sometimes|required|url',
            'icon' => 'nullable|string',
            'type' => 'sometimes|required|in:facebook,twitter,instagram,linkedin,tiktok,youtube,snapchat,whatsapp,telegram,pinterest,reddit,other',
            'userID' => 'sometimes|required|exists:users,userID',
        ]);

        $asset->update($request->all());

        return response()->json([
            'message' => 'Asset updated successfully.',
            'asset' => $asset
        ]);
    }

    public function destroy($id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        $asset->delete();

        return response()->json(['message' => 'Asset deleted successfully.']);
    }
}
