<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\VisaOnArrivalContentRequest;
use App\Services\Visa\VisaOnArrivalContentService;

class VisaOnArrivalContentController extends Controller
{
    public function edit(VisaOnArrivalContentService $content)
    {
        $content->seedDefaultNationalitiesIfEmpty();

        return view('dashboard.visa.visa-on-arrival.edit', [
            'content' => $content->get(),
        ]);
    }

    public function update(VisaOnArrivalContentRequest $request, VisaOnArrivalContentService $content)
    {
        $content->save($request->validated());

        session()->flash('message', 'Visa On Arrival page content saved successfully!');
        session()->flash('type', 'success');

        return back();
    }
}
