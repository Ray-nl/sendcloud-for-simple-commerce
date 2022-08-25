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

            if (method_exists($shippingMethod, 'getSoundCloudId')) {
                $weight = $event->order->lineItems->map(function ($_lineItem) {
                    return $_lineItem->product->data['weight'];
                })->max();

                $houseNumber = $event->order->data['shipping_house_number'];
                if (array_key_exists('shipping_house_number_addition', $event->order->data()->toArray())) {
                    $houseNumber .= ' ' . $event->order->data['shipping_house_number_addition'];
                }

                $address = new Address(
                    name: $event->order->customer->data['name'],
                    companyName: $event->order->customer?->data['company'] ?? null,
                    street: $event->order->data['shipping_address'],
                    houseNumber: $houseNumber,
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

                // Set parcel ID to order
                $entry = Entry::query()
                    ->where('collection', 'orders')
                    ->where('id', $event->order->id())
                    ->first();
                $entry->sendcloud_id = $sendcloud->getParcel()->getId();
                $entry->save();
            }
        }
    }
}