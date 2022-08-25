<?php

namespace RayNl\SendcloudForSimpleCommerce\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JouwWeb\SendCloud\Model\ShippingMethod;

class GenerateShippingMethodeTemplate
{
    public static function generate(ShippingMethod $shippingMethod): string
    {
        // Get the stub file which is the template for the shipping method
        $content = file_get_contents(__DIR__ .'/stubs/shippingMethodeStub.php.stub');

        $className = Str::replace(' ', '', $shippingMethod->getName());
        $className = Str::replace('.', '', $className);
        $className = Str::replace('-', '', $className);
        $className = Str::replace('+', '', $className);

        $content = Str::replace('!CLASSNAME!', $className, $content);
        $content = Str::replace('!NAME!', $shippingMethod->getName(), $content);
        $content = Str::replace('!PRICE!', $shippingMethod->getPriceForCountry(config('sendcloud-simple-commerce.country')), $content);
        $content = Str::replace('!SENDCLOUDID!', $shippingMethod->getId(), $content);
        $content = Str::replace('!MINWEIGHT!', $shippingMethod->getMinimumWeight(), $content);
        $content = Str::replace('!MAXWEIGHT!', $shippingMethod->getMaximumWeight(), $content);

        $path = public_path() . '/../app/ShippingMethods/';

        if(!file_exists($path)) {
            mkdir($path);
        }

        $path .= $className . '.php';
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }


        return $className;
    }
}