<?php
/**
 * Created by PhpStorm.
 * User: hschulz
 * Date: 16/02/2017
 * Time: 12:01
 */

namespace Cubits\ApiClient;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

require_once __DIR__ . '/../lib/Cubits/Exception.php';
require_once __DIR__ . '/../lib/Cubits/Requestor.php';
require_once __DIR__ . '/../lib/Cubits/ApiException.php';


/**
 * Cubits API client.
 *
 * @author hannes.schulz@cubits.com
 */
class Client
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger == null ? new NullLogger() : $logger;
        $this->httpClient = new \GuzzleHttp\Client();
    }

    /**
     * @param string $baseUrl The base URL
     */
    public function setBaseUrl(string $baseUrl)
    {
        $lastStr = substr($baseUrl, -1);
        if ($lastStr != '/') {
            $baseUrl .= '/';
        }

        $this->baseUrl = $baseUrl;
        $this->basePath = parse_url($this->baseUrl, PHP_URL_PATH);
    }

    /**
     * @param string $apiKey The Cubits API key
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $apiSecret The Cubits API secret
     */
    public function setApiSecret(string $apiSecret)
    {
        $this->apiSecret = $apiSecret;
    }


    public function post($uriPath, array $params = [])
    {
        $options = ['headers' => $this->buildHeaders($uriPath, count($params) > 0 ? json_encode($params) : null)];
        $options['verify'] = 0;
        if (count($params) > 0) {
            $options['form_params'] = $params;
        }


        try {

            //return $this->curlRequest($uriPath, $options, $params);

            return $this->httpClient->request('POST', $this->baseUrl . $uriPath, $options);

        } catch (ClientException $e) {
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
        catch (\Cubits_ApiException $e) {
            echo $e->getResponse();
        }
    }


    private function curlRequest($uriPath, $options, $params) {
        $headers = [];
        foreach ($options['headers'] as $key => $value) {
            $headers[] = $key . ': '. $value;
        }


        $curl = curl_init();

        $curlOpts[CURLOPT_POST] = 1;
        $curlOpts[CURLOPT_POSTFIELDS] = json_encode($params);

        $curlOpts[CURLOPT_URL] = $this->baseUrl . $uriPath;
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;

        // Do request
        curl_setopt_array($curl, $curlOpts);

        var_dump($curlOpts);
        $requestor = new \Cubits_Requestor();
        return $requestor->doCurlRequest($curl);

    }

    /**
     * @param string $uriPath
     * @return array An associative array of HTTP headers
     */
    private function buildHeaders($uriPath, $requestData = null)
    {
        $nonce = sprintf('%0.0f', round(microtime(true) * 1000000));
        // $nonce = '1487603151930234';

        $headers = [];
        $headers['User-Agent'] = 'Cubits/PHP v0.0.1';
        $headers['X-Cubits-Key'] = $this->apiKey;
        $headers['X-Cubits-Signature'] = $this->calcSignature($uriPath, $nonce, $requestData);
        $headers['X-Cubits-Nonce'] = $nonce;
        $headers['Accept'] = 'application/vnd.api+json';
        $headers['Content-Type'] = 'application/vnd.api+json';

        //print_r($headers);

        return $headers;
    }


    /**
     * @param string $uriPath The URI path (e.g. "test")
     * @param string $nonce The nonce
     * @param string $requestData The JSON excoded request data
     * @return string The X-Cubits-Signature
     */
    private function calcSignature($uriPath, $nonce, $requestData = null)
    {
        $hashedRequestData = $requestData == null ? null : hash('sha256', utf8_encode($requestData), false);

        $message = utf8_encode($this->basePath . $uriPath) . $nonce . $hashedRequestData;

        return hash_hmac("sha512", $message, $this->apiSecret);
    }
}