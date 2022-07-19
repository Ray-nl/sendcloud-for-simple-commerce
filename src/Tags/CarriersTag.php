<?php

namespace RayNl\SendcloudForSimpleCommerce\Tags;

use RayNl\SendcloudForSimpleCommerce\Services\SendcloudService;
use Statamic\Tags\Tags;

class CarriersTag extends Tags
{
    protected static $handle = 'carriers';

    public function index(): array
    {
        return SendcloudService::init()->getCarriers()->map(function ($_carrier) {
            return [
                'id' => $_carrier,
                'name' => ucfirst($_carrier),
            ];
        })->toArray();
    }
}