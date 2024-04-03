<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Factory\UserFactory;

class ApiTestCase extends \ApiPlatform\Symfony\Bundle\Test\ApiTestCase
{
    /**
     * Creates a new client with a valid token and set this token in the client header.
     */
    protected static function createClientAndUserWithValidAuthHeaders(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        UserFactory::new()->withValidToken()->create()->assertPersisted();
        $defaultOptions += ['headers' => ['Authorization' => 'Bearer '.UserFactory::VALID_TOKEN]];
        return parent::createClient($kernelOptions, $defaultOptions);
    }

    protected static function createClientAndUserWithInvalidAuthHeaders(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        UserFactory::new()->withValidToken()->create();
        $defaultOptions += ['headers' => ['Authorization' => 'Bearer '.UserFactory::INVALID_TOKEN]];
        return parent::createClient($kernelOptions, $defaultOptions);
    }

    protected  static function createClientWithBearer(string $token): Client
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