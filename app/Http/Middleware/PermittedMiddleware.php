<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermittedMiddleware
{
    private array $excluded =[
        'dashboard.toggle-theme',
        'dashboard.cache.clear',
        'dashboard.currencies.rates.update',
        'dashboard.model.auto.translate',
        'dashboard.sitemap.generate',
        'dashboard.car-routes.template',
    ];
    public function handle(Request $request, Closure $next)
    {
        try {
            $permission = Str::of($request->route()->getName())
                ->remove('dashboard.')
                ->replace('app-update-settings.', 'visa-settings.')
                ->replace('loyalty-settings.', 'visa-settings.')
                ->replace('visa-on-arrival.', 'visa-settings.')
                ->replace('visa-nationalities.', 'visa-settings.')
                ->replace('policies.', 'visa-settings.')
                ->replace('toggle-active', 'edit')
                ->replace('toggle-featured', 'edit')
                ->replace('store', 'create')
                ->replace('index', 'list')
                ->replace('update', 'edit')
                ->replace('destroy', 'delete')
                ->replace('manage', 'list');

            $permission = (string) $permission;

            // Mobile content screens share the single visa-settings.edit permission.
            if (str_starts_with($permission, 'visa-settings.') && $permission !== 'visa-settings.edit') {
                $permission = 'visa-settings.edit';
            }

        } catch (\Exception $exception) {
            report($exception);
            $permission = '';
        }

        if ($permission && !in_array($request->route()->getName(), $this->excluded)) {
            abort_if(admin()->cannot($permission), 403);
        }

        return $next($request);
    }
}
