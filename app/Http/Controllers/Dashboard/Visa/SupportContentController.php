<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\SupportContentRequest;
use App\Services\Visa\SupportContentService;

class SupportContentController extends Controller
{
    public function edit(SupportContentService $supportContent)
    {
        return view('dashboard.visa.support-content.edit', [
            'content' => $supportContent->get(),
        ]);
    }

    public function update(SupportContentRequest $request, SupportContentService $supportContent)
    {
        $supportContent->save($request->validated());

        session()->flash('message', 'Mobile support content updated successfully!');
        session()->flash('type', 'success');

        return back();
    }
}
