<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\JsonResponse;

class CompanyProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $profile = CompanyProfile::getInstance();
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $profile->name,
                'description' => $profile->description,
                'address' => $profile->address,
                'map_embed_url' => $profile->map_embed_url,
                'phone' => $profile->phone,
                'email' => $profile->email,
                'whatsapp' => $profile->whatsapp,
                'whatsapp_link' => $profile->whatsapp_link,
                'instagram' => $profile->instagram,
                'instagram_url' => $profile->instagram_url,
                'facebook' => $profile->facebook,
                'tiktok' => $profile->tiktok,
                'logo_url' => $profile->logo_url,
                'hero_image_url' => $profile->hero_image_url,
            ],
        ]);
    }
}
