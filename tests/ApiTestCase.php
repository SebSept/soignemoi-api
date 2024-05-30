<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;

class ApiTestCase extends \ApiPlatform\Symfony\Bundle\Test\ApiTestCase
{
    protected static function createClientWithBearer(string $token): Client
    {
        $defaultOptions = ['headers' => ['Authorization' => 'Bearer '.$token]];

        return parent::createClient(defaultOptions: $defaultOptions);
    }

    protected function createClientWithInvalidBearer(): Client
    {
        $defaultOptions = ['headers' => ['Authorization' => 'Bearer probably-invalid-token']];

        return parent::createClient([], $defaultOptions);
    }

}
