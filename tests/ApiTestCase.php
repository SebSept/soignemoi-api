<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;

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

    /**
     * @deprecated
     */
    protected function createClientWithBearerFromUser(User $user, bool $enableProfiler = false): Client
    {
        $defaultOptions = ['headers' => ['Authorization' => 'Bearer '.$user->getAccessToken()]];
        $client = parent::createClient([], $defaultOptions);
        if ($enableProfiler) {
            $client->enableProfiler();
        }

        return $client;
    }
}
