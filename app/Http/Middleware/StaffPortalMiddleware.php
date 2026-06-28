<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffPortalMiddleware
{
    private array $excluded = [
        'staff.dashboard',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = auth('web')->user();

        if (! $user) {
            abort(403);
        }

        if ($user->hasAnyRole(['Administrator', 'super_admin', 'operator', 'field_staff'])) {
            // continue to permission check below
        } elseif (! $user->hasAnyPermission([
            'guest-requests.list',
            'guest-requests.show',
            'guest-requests.advance',
            'guest-requests.note',
        ])) {
            abort(403);
        }

        try {
            $permission = Str::of($request->route()->getName())
                ->remove('staff.')
                ->replace('requests.index', 'guest-requests.list')
                ->replace('requests.show', 'guest-requests.show')
                ->replace('requests.advance', 'guest-requests.advance')
                ->replace('requests.complete', 'guest-requests.advance')
                ->replace('requests.update-note', 'guest-requests.note');
        } catch (\Exception $exception) {
            report($exception);
            $permission = '';
        }

        if ($permission && ! in_array($request->route()->getName(), $this->excluded, true)) {
            abort_if(admin()->cannot((string) $permission), 403);
        }

        return $next($request);
    }
}
