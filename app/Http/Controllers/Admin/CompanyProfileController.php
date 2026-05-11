<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyProfileRequest;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompanyProfileController extends Controller
{
    /**
     * Show the form for editing the company profile.
     */
    public function edit(): Response
    {
        $profile = CompanyProfile::getInstance();

        return Inertia::render('Admin/CompanyProfile/Edit', [
            'profile' => [
                'id' => $profile->id,
                'name' => $profile->name,
                'description' => $profile->description,
                'address' => $profile->address,
                'latitude' => $profile->latitude,
                'longitude' => $profile->longitude,
                'map_embed_url' => $profile->map_embed_url,
                'phone' => $profile->phone,
                'email' => $profile->email,
                'whatsapp' => $profile->whatsapp,
                'instagram' => $profile->instagram,
                'facebook' => $profile->facebook,
                'tiktok' => $profile->tiktok,
                'logo' => $profile->logo,
                'logo_url' => $profile->logo_url,
                'hero_image' => $profile->hero_image,
                'hero_image_url' => $profile->hero_image_url,
            ],
        ]);
    }

    /**
     * Update the company profile.
     */
    public function update(CompanyProfileRequest $request): RedirectResponse
    {
        $profile = CompanyProfile::getInstance();
        $validated = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            // Delete old hero image
            if ($profile->hero_image) {
                Storage::disk('public')->delete($profile->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('company', 'public');
        }

        $profile->update($validated);

        return redirect()
            ->route('admin.company-profile.edit')
            ->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}
