<?php

namespace Tests\Cubits;

use Cubits\ApiKeyAuthenticator;
use Cubits\Cubits;
use Cubits\RequestExecutor;
use Cubits\Rpc;

class RpcTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @throws \Cubits\ApiException
     */
    public function test_rpc()
    {
        $key = '12345';
        $secret = '1234566789900';
        $authentication = new ApiKeyAuthenticator($key, $secret);

        $requestor = $this->getMockBuilder(RequestExecutor::class)
            ->setMethods(array('executeRequest'))
            ->getMock();
        $response = array('body' => '{"x":1, "y":2}', 'statusCode' => '200');

        $cubits = Cubits::withApiKey('123', '1234567890');

        $rpc = $this->getMockBuilder(Rpc::class)
            ->setConstructorArgs(array($cubits, $requestor, $authentication))
            ->setMethods(null)
            ->getMock();

        $requestor->method('executeRequest')
            ->willReturn($response);
        $requestor->expects($this->once())
            ->method('executeRequest')
            ->will($this->returnValue($response));


        $key = $rpc->sha256hash('');
        $signature = $rpc->calcSignature('', $key);

        $this->assertEquals($key, hash('sha256', utf8_encode(''), false));
        $this->assertEquals($signature, hash_hmac("sha512", '', $key));

        $rpc->request('get', 'asd/asd/asd', array());

    }
}
