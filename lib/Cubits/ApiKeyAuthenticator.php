<?php

namespace Cubits;

class ApiKeyAuthenticator extends Authenticator
{
    private $apiKey;
    private $apiKeySecret;

    public function __construct($apiKey, $apiKeySecret)
    {
        $this->apiKey = $apiKey;
        $this->apiKeySecret = $apiKeySecret;
    }

    public function getData()
    {
        $data = new \stdClass();
        $data->apiKey = $this->apiKey;
        $data->apiKeySecret = $this->apiKeySecret;
        return $data;
    }
}
