<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\PoliciesContentRequest;
use App\Services\Visa\PoliciesContentService;

class PoliciesContentController extends Controller
{
    public function edit(PoliciesContentService $policies)
    {
        return view('dashboard.visa.policies.edit', [
            'content' => $policies->get(),
        ]);
    }

    public function update(PoliciesContentRequest $request, PoliciesContentService $policies)
    {
        $policies->save($request->validated());

        session()->flash('message', 'Mobile policies updated successfully!');
        session()->flash('type', 'success');

        return back();
    }
}
