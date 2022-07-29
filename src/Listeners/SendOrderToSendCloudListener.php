<?php

namespace RayNl\SendcloudForSimpleCommerce\Listeners;

use DoubleThreeDigital\SimpleCommerce\Events\OrderPaid;
use Illuminate\Support\Facades\Storage;
use JouwWeb\SendCloud\Model\Address;
use RayNl\SendcloudForSimpleCommerce\Services\SendcloudService;
use Statamic\Facades\Entry;

class SendOrderToSendCloudListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     *
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        if (class_exists($event->order->data['shipping_method'])) {
            $shippingMethod = new $event->order->data['shipping_method']();
            // Check if sendcloud ID exists
            if (method_exists($shippingMethod, 'getSendCloudId')) {
                $shippingMethodId = $shippingMethod->getSendCloudId();
                $weight = $event->order->lineItems->map(function ($_lineItem) {
                    return $_lineItem->product->data['weight'];
                })->max();

                $address = new Address(
                    name: $event->order->customer->data['name'],
                    companyName: $event->order->customer?->data['company'] ?? null,
                    street: $event->order->data['shipping_address'],
                    houseNumber: $event->order->data['shipping_house_number'],
                    city: $event->order->data['shipping_city'],
                    postalCode: $event->order->data['shipping_postal_code'],
                    countryCode: 'NL',
                    emailAddress: $event->order->customer->data['email'],
                );

                $sendcloud = new SendcloudService();
                $sendcloud->createParcel(
                    address: $address,
                    orderNumber: $event->order->orderNumber,
                    weight: $weight,
                );

                $sendcloud->createLabel($shippingMethodId);

                Storage::put('labels/'. $event->order->orderNumber . '/label-' . $event->order->orderNumber . '.pdf', $sendcloud->createLabelPdf());
            }
        }
    }
}