<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Visa\AppUpdateSettingsService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class AppUpdateSettingController extends Controller
{
    use HasApiResponse;

    public function index(Request $request, AppUpdateSettingsService $appUpdateSettings)
    {
        $request->validate([
            'platform' => ['nullable', 'string', 'in:android,ios'],
            'version' => ['nullable', 'string', 'max:20', 'regex:/^\d+(\.\d+){1,3}$/'],
            'build' => ['nullable', 'integer', 'min:0'],
        ]);

        return $this->send($appUpdateSettings->apiPayload(
            $request->query('platform'),
            $request->query('version'),
            $request->filled('build') ? (int) $request->query('build') : null,
        ));
    }
}
