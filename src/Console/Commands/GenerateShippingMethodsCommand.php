<?php

namespace RayNl\SendcloudForSimpleCommerce\Console\Commands;

use Illuminate\Console\Command;
use RayNl\SendcloudForSimpleCommerce\Helpers\GenerateShippingMethodeTemplate;
use RayNl\SendcloudForSimpleCommerce\Services\SendcloudService;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Entry;

class GenerateShippingMethodsCommand extends Command
{
    use RunsInPlease;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'sendcloud:generate-shipping-methods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates all the shipping methods.';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (!$this->call('sendcloud:test-integration')) {
            return false;
        }

        $shippingMethods = SendcloudService::init()->getShippingMethods();

        if (config('sendcloud-simple-commerce.country') !== null) {
            $shippingMethods = SendcloudService::init()->getShippingMethodsForCountry(config('sendcloud-simple-commerce.country'));
        }

        $enabledShippingMethodes = [];

        foreach ($shippingMethods as $_shippingMethod) {
            if ($this->confirm('Do you want to enable ' . $_shippingMethod->getName() . ' as shipping method?', true)) {
                $enabledShippingMethodes[] = $_shippingMethod;
            }
        }

        if (count($enabledShippingMethodes) === 0) {
            return 1;
        }

        $this->info('Add the following classes to simple-commerce.php config in the shipping methods array');

        foreach ($enabledShippingMethodes as $_enabledShippingMethod) {
            $className = GenerateShippingMethodeTemplate::generate($_enabledShippingMethod);
            $this->info("\\App\\ShippingMethods\\" . $className . "::class => [],");
        }

        return 1;
    }
}