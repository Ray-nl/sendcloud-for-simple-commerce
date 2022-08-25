<?php

namespace RayNl\SendcloudForSimpleCommerce\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JouwWeb\SendCloud\Client;
use JouwWeb\SendCloud\Model\Address;
use JouwWeb\SendCloud\Model\Parcel;
use RayNl\SendcloudForSimpleCommerce\Exceptions\SendcloudCountryCodeIsMissingException;
use RayNl\SendcloudForSimpleCommerce\Exceptions\SendcloudPrivateKeyException;
use RayNl\SendcloudForSimpleCommerce\Exceptions\SendcloudPublicKeyException;

class SendcloudService
{
    protected Client $client;

    protected Address $address;

    protected Parcel $parcel;

    public function __construct()
    {
        $this->setSendcloud();
    }

    public static function init(): SendcloudService
    {
        return (new self())->setSendcloud();
    }

    public function setSendcloud(): static
    {
        if (empty(config('sendcloud-simple-commerce.public_key'))) {
            Throw new SendcloudPublicKeyException();
        }

        if (empty(config('sendcloud-simple-commerce.secret_key'))) {
            Throw new SendcloudPrivateKeyException();
        }

        $this->client = new Client(
            publicKey: config('sendcloud-simple-commerce.public_key'),
            secretKey: config('sendcloud-simple-commerce.secret_key'),
            partnerId: config('sendcloud-simple-commerce.partner_id') ?? null,
            apiBaseUrl: config('sendcloud-simple-commerce.api_base_url') ?? null,
        );

        return $this;
    }

    /**
     * Check if we can connect to Sendcloud
     *
     * @return bool
     */
    public function canConnectToSendcloud(): bool
    {
        try {
            $this->client->getUser();

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * Get All available shipping Methods
     *
     * @return array
     * @throws \JouwWeb\SendCloud\Exception\SendCloudClientException
     */
    public function getShippingMethods(): array
    {
        return $this->client->getShippingMethods();
    }

    public function getShippingMethodsForCountry(string $country): array
    {
        $shippingMethods = [];
        foreach ($this->getShippingMethods() as $_shippingMethode) {
            if ($_shippingMethode->getPriceForCountry($country) !== null) {
                $shippingMethods[] = $_shippingMethode;
            }
        }

        return $shippingMethods;
    }

    public function createParcel(Address $address, int $orderNumber, int $weight, int $servicePointId = null, string $customsInvoiceNumber = null, int $customShipmentType = null, array $items = null, string $postNumber = null): void
    {
        $this->parcel = $this->client->createParcel(
            shippingAddress: $address,
            servicePointId: $servicePointId,
            orderNumber: $orderNumber,
            weight: $weight,
            customsInvoiceNumber: $customsInvoiceNumber,
            customsShipmentType: $customShipmentType,
            items: $items,
            postNumber: $postNumber,
        );
    }

    public function createLabel(int $shippingMethodId, string $defaultSenderAddress = null): void
    {
        if (config('app.env') === 'local') {
            $shippingMethodId = 8;
        }

        $parcel = $this->client->createLabel($this->parcel, $shippingMethodId, $defaultSenderAddress);
        $this->parcel = $parcel;
    }

    public function createLabelPdf()
    {
        return $this->client->getLabelPdf($this->parcel, Parcel::LABEL_FORMAT_A4_BOTTOM_RIGHT);
    }

    public function getParcel()
    {
        return $this->parcel;
    }

    public function getParcelFromId($id)
    {
        $this->parcel = $this->client->getParcel($id);

        return $this;
    }
}
