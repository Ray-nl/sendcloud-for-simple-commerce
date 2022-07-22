<?php

namespace RayNl\SendcloudForSimpleCommerce\Listeners;

use RayNl\SendcloudForSimpleCommerce\Helpers\ShippingMethodGeneratorHelper;
use Statamic\Events\EntrySaved;

class EntrySavedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param EntrySaved $event
     * @return void
     */
    public function handle(EntrySaved $event)
    {
        if ($event->entry === 'shipment_methods') {
            ShippingMethodGeneratorHelper::generateTemplate($event->entry);
        }
    }
}