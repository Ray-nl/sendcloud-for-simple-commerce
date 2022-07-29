# Sendcloud For Simple Commerce

> Sendcloud For Simple Commerce is a Statamic addon that does something pretty neat.

## Features

This addon does:

- This
- And this
- And even this

## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require ray-nl/sendcloud-for-simple-commerce
```

## How to Use

Here's where you can explain how to use this wonderful addon.

Add to your .env file:
``` bash
SENDCLOUD_PUBLIC_KEY=your-public-sendgrid-key
SENDCLOUD_SECRET_KEY=your-secret-sendgrid-key

SENDCLOUD_PARTNER_ID=your-partner_id // Default is this null
SENDCLOUD_API_BASE_URL=api-base-url //Default is this https://panel.sendcloud.sc/api/v2/
```

Publish the config file:
``` bash
php artisan vendor:publish --provider="RayNl\SendcloudForSimpleCommerce\ServiceProvider" --tag="config"
```

Publish the blueprints:
```bash
php artisan vendor:publish --provider="RayNl\SendcloudForSimpleCommerce\ServiceProvider" --tag="blueprints"
```

### Important!
You need to add the weight to the products blueprint for calculating the price.
```yaml
      -
        handle: weight
        field:
          display: Weight
          type: float
          icon: float
          instructions: 'In Kilograms (KG)'
          listable: hidden
          instructions_position: above
          visibility: visible
          validate:
            - required
```

For generating the shipping methods run the following command:
```bash
php artisan sendcloud:generate-shipping-methods
```
