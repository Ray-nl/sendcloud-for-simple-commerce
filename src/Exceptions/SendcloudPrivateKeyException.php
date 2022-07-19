<?php

namespace RayNl\SendcloudForSimpleCommerce\Exceptions;

use Exception;

class SendcloudPrivateKeyException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: 'Your Sendcloud private key is missing or is wrong.');
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}