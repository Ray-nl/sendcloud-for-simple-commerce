# Sendcloud For Simple Commerce

Add the possibility to create shipments with Sendcloud directly from Statamic with Simple Commerce. You can directly download the label and mark the order as shipped.

## Get shipping methods
First you have to select the shipping methods to be used in your webshop from Sendcloud. Just run the following command to choose.

```bash
php artisan sendcloud:generate-shipping-methods
```

## Add action to CP
If you want to create a label and marked an order as shipped add the following action to your application:
```bash
<?php

namespace App\Actions;

use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use Illuminate\Support\Facades\Storage;
use RayNl\SendcloudForSimpleCommerce\Services\SendcloudService;
use Statamic\Actions\Action;
use Statamic\Contracts\Entries\Entry;

class DownloadLabel extends Action
{
    public function visibleTo($item)
    {
        if ($item instanceof Entry) {
            return $item->collection->handle === 'orders';
        }

        return false;
    }

    public function visibleToBulk($items)
    {
        return false;
    }

    public function download($items, $values)
    {
        foreach ($items as $item) {
            $shippingMethod = new ($item->shipping_method->first())();
            if ($shippingMethod->getSendCloudId() !== null) {
                if (!Storage::exists("labels/{$item->order_number}/label-{$item->order_number}.pdf")) {
                    if ($item->sendcloud_id !== null) {

                        $sendcloud = new SendcloudService();
                        $sendcloud->getParcelFromId($item->sendcloud_id);
                        $sendcloud->createLabel($shippingMethod->getSendCloudId());

                        Storage::put('labels/' . $item->order_number . '/label-' . $item->order_number . '.pdf', $sendcloud->createLabelPdf());
                    }
                }

                Order::find($item->id)->markAsShipped();

                return storage_path("app/labels/{$item->order_number}/label-{$item->order_number}.pdf");
            }
        }
    }
}

```
