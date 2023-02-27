<?php

namespace NeoScrypts\Landing\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{
    /**
     * Cryptitan Settings
     *
     * @var mixed
     */
    protected $settings;

    /**
     * Construct Controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->settings = App::make('settings');
    }

    /**
     * Show landing page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function view()
    {
        $data = [
            'name' => Config::get('app.name'),
            'settings' => [
                'baseCurrency' => App::make('exchanger')->config('base_currency'),
                'theme' => $this->settings->theme->all(),
                'brand' => $this->settings->brand->all(),
            ],
        ];

        return View::make('landing::index', compact('data'));
    }
}