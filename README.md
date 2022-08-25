# Sendcloud For Simple Commerce

Sendcloud For Simple Commerce.

## Get shipping methods
To get all the available shipping methods run the following command:
```bash
php artisan sendcloud:generate-shipping-methods
```

## Add action to CP
If you want to create an label and marked an order as shipped add the following action to your application:
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
