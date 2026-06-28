<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateLanguageRequest;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Requests\Api\V1\UploadProfilePhotoRequest;
use App\Http\Resources\Api\V1\ClientResource;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use HasApiResponse;

    public function show(Request $request)
    {
        return $this->send(new ClientResource(
            $request->user()->load(['activeMembership', 'wallet'])
        ));
    }

    public function update(UpdateProfileRequest $request)
    {
        $request->user()->update($request->validated());

        return $this->send(
            new ClientResource($request->user()->fresh()->load(['activeMembership', 'wallet'])),
            'Profile updated successfully.'
        );
    }

    public function uploadPhoto(UploadProfilePhotoRequest $request)
    {
        $client = $request->user();

        if ($client->image && str_starts_with($client->image, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $client->image);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('photo')->store('clients/photos', 'public');
        $url = Storage::url($path);

        $client->update(['image' => $url]);

        return $this->send(
            new ClientResource($client->fresh()->load(['activeMembership', 'wallet'])),
            'Profile photo updated successfully.'
        );
    }

    public function updateLanguage(UpdateLanguageRequest $request)
    {
        $request->user()->update(['language' => $request->get('language')]);

        return $this->send(
            new ClientResource($request->user()->fresh()->load(['activeMembership', 'wallet'])),
            'Language updated successfully.'
        );
    }
}
