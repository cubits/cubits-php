<?php

namespace Cubits;

class Exception extends \Exception
{
    private $response;
    private $httpCode;

    public function __construct($message, $httpCode = null, $response = null)
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }
}
