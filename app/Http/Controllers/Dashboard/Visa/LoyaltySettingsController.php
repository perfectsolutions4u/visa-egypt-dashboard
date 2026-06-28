<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\LoyaltySettingsRequest;
use App\Services\Visa\LoyaltySettingsService;

class LoyaltySettingsController extends Controller
{
    public function edit(LoyaltySettingsService $loyaltySettings)
    {
        return view('dashboard.visa.loyalty.edit', [
            'settings' => $loyaltySettings->get(),
        ]);
    }

    public function update(LoyaltySettingsRequest $request, LoyaltySettingsService $loyaltySettings)
    {
        $loyaltySettings->save($request->validated());

        session()->flash('message', 'Loyalty program settings updated successfully!');
        session()->flash('type', 'success');

        return back();
    }
}
