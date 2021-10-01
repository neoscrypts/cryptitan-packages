<?php

namespace NeoScrypts\Exchanger\Console;

use Akaunting\Money\Currency;
use Exception;
use Illuminate\Console\Command;
use NeoScrypts\Exchanger\Contracts\DriverInterface;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchanger:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize Exchanger';

    /**
     * Exchange rate storage instance
     *
     * @var DriverInterface
     */
    protected $storage;

    /**
     * All installable currencies.
     *
     * @var array
     */
    protected $currencies;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->currencies = Currency::getCurrencies();
        $this->storage = app('exchanger')->getDriver();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->initExchangeRates();
        $this->call('exchanger:update');
    }

    /**
     * Initialize exchange rates
     *
     * @return void
     */
    public function initExchangeRates()
    {
        foreach ($this->currencies as $code => ['name' => $name]) {
            try {
                $this->storage->create(compact('name', 'code'));
                $this->output->success("Added: $name");
            } catch (Exception $e) {
                $this->output->error("Failed: {$e->getMessage()}");
            }
        }
    }
}
