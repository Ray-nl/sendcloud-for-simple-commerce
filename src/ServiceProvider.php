<?php

namespace RayNl\SendcloudForSimpleCommerce;

use RayNl\SendcloudForSimpleCommerce\Console\Commands\GenerateShippingMethodsCommand;
use RayNl\SendcloudForSimpleCommerce\Console\Commands\TestSendcloudIntegrationCommand;
use RayNl\SendcloudForSimpleCommerce\Listeners\EntrySavedListener;
use RayNl\SendcloudForSimpleCommerce\Tags\CarriersTag;
use Statamic\Events\EntrySaved;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    // @phpstan-ignore-next-line
    protected $tags = [];

    protected $listen = [
        EntrySaved::class => [
            EntrySavedListener::class
        ],
    ];

    // @phpstan-ignore-next-line
    protected $modifiers = [];

    // @phpstan-ignore-next-line
    protected $fieldtypes = [];

    // @phpstan-ignore-next-line
    protected $widgets = [];

    // @phpstan-ignore-next-line
    protected $commands = [
        TestSendcloudIntegrationCommand::class,
        GenerateShippingMethodsCommand::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/sendcloud-simple-commerce.php', 'sendcloud-simple-commerce');
    }

    public function bootAddon(): void
    {
//        if ($this->app->runningInConsole()) {
//            $this->publishConfigFiles();
//        }
//
//        $this->bootVendorAssets();
    }

    public function bootVendorAssets()
    {
//        $this->publishes([
//            __DIR__ . '/../resources/blueprints' => resource_path('blueprints'),
//        ], 'simple-commerce-blueprints');
    }

    /**
     * Publish the config file(s).
     *
     * @return void
     */
    private function publishConfigFiles(): void
    {
//        $this->publishes([
//            __DIR__.'/config/sendcloud-simple-commerce.php' => config_path('sendcloud-simple-commerce.php'),
//        ], 'config');
    }
}
