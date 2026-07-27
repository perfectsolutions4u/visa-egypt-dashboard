<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\AppUpdateSettingsRequest;
use App\Services\Visa\AppUpdateSettingsService;

class AppUpdateSettingsController extends Controller
{
    public function edit(AppUpdateSettingsService $appUpdateSettings)
    {
        return view('dashboard.visa.app-update.edit', [
            'settings' => $appUpdateSettings->get(),
        ]);
    }

    public function update(AppUpdateSettingsRequest $request, AppUpdateSettingsService $appUpdateSettings)
    {
        $appUpdateSettings->save($request->validated());

        session()->flash('message', 'App update settings saved successfully!');
        session()->flash('type', 'success');

        return back();
    }
}
