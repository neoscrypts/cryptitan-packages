<?php

namespace NeoScrypts\Installer;

use Closure;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Client\RequestException;
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
     * @throws FileNotFoundException
     * @throws RequestException
     */
    public function handle(Request $request, Closure $next)
    {
        $installer = $this->getInstaller();

        if ($installer->installed()) {
            if (!is_array($installer->details())) {
                App::abort(401, Lang::get('common.license_invalid'));
            }
        } else if (!$request->is('installer/*')) {
            return Response::redirectTo('installer');
        }

        return $next($request);
    }

    /**
     * Get installer instance
     *
     * @return Installer|mixed
     */
    protected function getInstaller()
    {
        return App::make('installer');
    }
}