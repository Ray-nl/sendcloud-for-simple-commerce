<?php

namespace RayNl\SendcloudForSimpleCommerce\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Statamic\Entries\Entry;
use Statamic\Facades\Config;

class ShippingMethodGeneratorHelper
{
    public static function generateTemplate(Entry $shipmentMethod): void
    {
        $content = file_get_contents(__DIR__ .'/stubs/shippingMethodeStub.php.stub');

        $className = Str::replace(' ', '', $shipmentMethod->title);
        $className = Str::replace('.', '', $className);
        $className = Str::replace('-', '', $className);

        $price = $shipmentMethode->prices[config('sendcloud-simple-commerce.country')] ?? $shipmentMethod->prices[array_key_first($shipmentMethod->prices)];

        $content = Str::replace('!CLASSNAME!', $className, $content);
        $content = Str::replace('!NAME!', $shipmentMethod->title, $content);
        $content = Str::replace('!PRICE!', $price, $content);
        $content = Str::replace('!SENDCLOUDID!', $shipmentMethod->sendcloud_id, $content);

        $path = public_path() . '/../app/ShippingMethods/' . $className . '.php';

        file_put_contents($path, $content);

        if ($shipmentMethod->published) {
            self::addToShipmentSimpleCommerce($className);
        }

        // set config file permanently
        self::setConfigFilePermanently();
    }

    public static function setConfigFilePermanently(): void
    {
        $data = self::varexport(Config::get('simple-commerce'));
        File::put(app_path() . '/../config/simple-commerce.php', "<?php\n return $data ;");
    }

    private static function varexport($expression): array|string|null
    {
        $export = var_export($expression, TRUE);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);

        return $export;
    }

    public static function addToShipmentSimpleCommerce(string $className): void
    {
        $shippingMethods = Config::get('simple-commerce.sites.default.shipping.methods');
        $className = 'App\\ShippingMethods\\' . $className;
        if (! in_array($className, $shippingMethods)) {
            $shippingMethods[] = $className;
        }

        Config::set('simple-commerce.sites.default.shipping.methods', $shippingMethods);
    }
}