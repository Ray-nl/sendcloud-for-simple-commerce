<?php

namespace RayNl\SendcloudForSimpleCommerce\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use RayNl\SendcloudForSimpleCommerce\Helpers\ShippingMethodGeneratorHelper;
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

        foreach ($shippingMethods as $_shippingMethod) {
            $entry = Entry::query()->where('collection', 'shipment_methods')->where('sendcloud_id', $_shippingMethod->getId())->first();

            $this->info($_shippingMethod->getName());

            if ($entry === null) {
                $entry = Entry::make()->collection('shipment_methods');
                $published = false;
                $prices = $_shippingMethod->getPrices();
            } else {
                $prices = $entry->prices;
                $published = $entry->published;
            }

            $entry
                ->set('sendcloud_id', $_shippingMethod->getId())
                ->set('title', $_shippingMethod->getName())
                ->set('minimum_weight', $_shippingMethod->getMinimumWeight())
                ->set('maximum_weight', $_shippingMethod->getMaximumWeight())
                ->set('carrier', $_shippingMethod->getCarrier())
                ->set('prices', $prices)
                ->published($published)
                ->save();
        }

        // Generates the templates and add only the active to the shipments config.
        $this->info('Generate the templates');

        return 1;
    }
}