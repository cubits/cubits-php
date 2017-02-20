<?php

use Cubits\ApiClient\Client;
use PHPUnit\Framework\TestCase;

/**
 */
class ClientTest extends TestCase
{
    public function testFoo()
    {
        $client = new Client();
        $client->setBaseUrl('https://pay.cubits.com/api/v1');
        $client->setApiKey('');
        $client->setApiSecret('');

        $response = $client->post('test', ['variable' => 'value']);

        var_dump($response);
    }
}
