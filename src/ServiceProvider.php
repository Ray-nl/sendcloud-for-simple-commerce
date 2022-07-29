<?php

namespace RayNl\SendcloudForSimpleCommerce;

use DoubleThreeDigital\SimpleCommerce\Events\OrderPaid;
use RayNl\SendcloudForSimpleCommerce\Console\Commands\GenerateShippingMethodsCommand;
use RayNl\SendcloudForSimpleCommerce\Console\Commands\TestSendcloudIntegrationCommand;
use RayNl\SendcloudForSimpleCommerce\Listeners\SendOrderToSendCloudListener;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        OrderPaid::class => [
            SendOrderToSendCloudListener::class
        ],
    ];

    protected $commands = [
        TestSendcloudIntegrationCommand::class,
        GenerateShippingMethodsCommand::class,
    ];

    public function bootAddon()
    {
        $this->publishes([
            __DIR__.'/config/sendcloud-simple-commerce.php' => config_path('sendcloud-simple-commerce.php'),
        ], 'config');
    }
}
