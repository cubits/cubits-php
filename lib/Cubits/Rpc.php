<?php

namespace Cubits;

class Rpc
{
    /**
     * @var $requestExecutor RequestExecutor
     */
    private $requestExecutor;
    /**
     * @var $authenticator Authenticator
     */
    private $authenticator;
    /**
     * @var $cubitsInstance Cubits
     */
    private $cubitsInstance;

    public function __construct($cubitsInstance, $requestExecutor, $authenticator)
    {
        $this->cubitsInstance = $cubitsInstance;
        $this->requestExecutor = $requestExecutor;
        $this->authenticator = $authenticator;
    }

    /**
     * @param $method
     * @param $url
     * @param $params
     * @return mixed
     * @throws ApiException
     * @throws ConnectionException
     */
    public function request($method, $url, $params = null)
    {
        // Create query string
        $queryString = $params !== null ? json_encode($params) : '';
        $path = '/api/v1/' . $url;
        $url = $this->cubitsInstance->getApiBase() . $url;

        // Initialize CURL
        $curl = curl_init();
        $curlOpts = array();


        $method = strtolower($method);

        // Check wether CURL should verify SSL (host and peer). 
        if ($this->cubitsInstance->getSslVerify() === false) {
            $curlOpts[CURLOPT_SSL_VERIFYPEER] = 0;
            $curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
        }
        // HTTP method

        if ($method === 'get') {
            $curlOpts[CURLOPT_HTTPGET] = 1;

            if ($queryString) {
                $url .= '?' . $queryString;
            }
        } else if ($method === 'post') {
            $curlOpts[CURLOPT_POST] = 1;
            $curlOpts[CURLOPT_POSTFIELDS] = $queryString;
        } else if ($method === 'delete') {
            $curlOpts[CURLOPT_CUSTOMREQUEST] = 'DELETE';

            if ($queryString) {
                $url .= '?' . $queryString;
            }
        } else if ($method === 'put') {
            $curlOpts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $curlOpts[CURLOPT_POSTFIELDS] = $queryString;
        }

        // Headers
        $headers = array('User-Agent: Cubits/PHP v0.0.1');

        $auth = $this->authenticator->getData();

        // Get the authentication class and parse its payload into the HTTP header.
        $authenticationClass = get_class($this->authenticator);
        switch ($authenticationClass) {


            case ApiKeyAuthenticator::class:
                // Use HMAC API key

                $dataToHash = '';
                if (array_key_exists(CURLOPT_POSTFIELDS, $curlOpts)) {
                    $dataToHash .= $curlOpts[CURLOPT_POSTFIELDS];
                }
                // First i create the message
                // string hash ( string $algo , string $data [, bool $raw_output = false ] )
                $post_data = $this->sha256hash($dataToHash);
                $microseconds = sprintf('%0.0f', round(microtime(true) * 1000000));

                $message = utf8_encode($path) . $microseconds . $post_data;

                // string hash_hmac ( string $algo , string $data , string $key [, bool $raw_output = false ] )
                $hmac_key = $auth->apiKeySecret;
                $signature = $this->calcSignature($message, $hmac_key);

                $headers[] = 'X-Cubits-Key: ' . $auth->apiKey;
                $headers[] = 'X-Cubits-Signature: ' . $signature;
                $headers[] = 'X-Cubits-Nonce: ' . $microseconds;
                $headers[] = 'Accept: application/vnd.api+json';
                $headers[] = 'Content-Type: application/vnd.api+json';
                break;


            default:
                throw new ApiException('Invalid authentication mechanism');
                break;
        }

        // CURL options
        $curlOpts[CURLOPT_URL] = $url;
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;
        $curlOpts[CURLOPT_RETURNTRANSFER] = true;

        // Do request
        curl_setopt_array($curl, $curlOpts);
        $response = $this->requestExecutor->executeRequest($curl);

        // Decode response
        try {
            $json = $response['body'];
        } catch (\Exception $e) {
            throw new ConnectionException(
                "Invalid response body",
                $response['statusCode'],
                $response['body']
            );
        }
        if ($json === null) {
            throw new ApiException(
                "Invalid response body",
                $response['statusCode'],
                $response['body']
            );
        }
        if (isset($json->error)) {
            throw new ApiException(
                $json->error,
                $response['statusCode'],
                $response['body']
            );
        } else if (isset($json->errors)) {
            throw new ApiException(
                implode($json->errors, ', '),
                $response['statusCode'],
                $response['body']
            );
        }

        return $json;
    }

    public function sha256hash($data)
    {
        return hash('sha256', utf8_encode($data), false);
    }

    public function calcSignature($message, $hmacKey)
    {
        return hash_hmac("sha512", $message, $hmacKey);
    }
}
