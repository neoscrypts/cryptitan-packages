<?php

namespace NeoScrypts\Installer;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;

class InstallerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (App::make('installer')->hasLicenseCode()) {
            if (!App::make('installer')->hasValidLicense()) {
                App::abort(403, Lang::get('license.invalid'));
            }
        } else if (!$request->is('installer*', '*/locale/*')) {
            $message = Lang::get('license.required');

            return $request->expectsJson() ?
                Response::json(['message' => $message], 403) :
                Response::redirectTo('installer');
        }

        return $next($request);
    }
}