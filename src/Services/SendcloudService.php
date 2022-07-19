<?php

namespace RayNl\SendcloudForSimpleCommerce\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JouwWeb\SendCloud\Client;
use JouwWeb\SendCloud\Exception\SendCloudRequestException;
use JouwWeb\SendCloud\Model\Address;
use JouwWeb\SendCloud\Model\Parcel;
use RayNl\SendcloudForSimpleCommerce\Exceptions\SendcloudPrivateKeyException;
use RayNl\SendcloudForSimpleCommerce\Exceptions\SendcloudPublicKeyException;

class SendcloudService
{
    protected Client $client;

    protected Address $address;

    protected Parcel $parcel;

    public static function init(): SendcloudService
    {
        return (new self())->setSendcloud();
    }

    private function setSendcloud(): static
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

    /**
     * Get all the carriers that are enabled in Sendcloud
     *
     * @return Collection
     */
    public function getCarriers(): Collection
    {
        // Cache for an hour
        $seconds = 60 * 60;

        return Cache::remember('carriers', $seconds,function () {
            return collect($this->getShippingMethods())->map(function ($_carrier) {
                return $_carrier->getCarrier();
            })->unique()->values();
        });
    }

    public function createParcel(): void
    {
    }

    public function createLabel(): void
    {
    }
}
