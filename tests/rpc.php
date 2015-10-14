<?php
require_once(dirname(__FILE__) . '/../lib/Cubits.php');

class RpcTest extends PHPUnit_Framework_TestCase {

    public function test_rpc(){
        $key = '12345';
        $secret = '1234566789900';
        $authentication = new Cubits_ApiKeyAuthentication($key, $secret);

        $requestor = $this->getMockBuilder('Cubits_Requestor')
                          ->setMethods(array('doCurlRequest'))
                          ->getMock();
        $response = array( 'body'=>'{"x":1, "y":2}', 'statusCode'=>'200');

        $rpc = $this->getMockBuilder('Cubits_Rpc')
                    ->setConstructorArgs(array($requestor, $authentication))
                    ->setMethods(NULL)
                    ->getMock();

        $requestor->method('doCurlRequest')
                  ->willReturn($response);
        $requestor->expects($this->once())
                  ->method('doCurlRequest')
                  ->will($this->returnValue($response));


        $key = $rpc->sha256hash('');
        $signature = $rpc->calc_signature('', $key);

        $this->assertEquals($key, hash('sha256',  utf8_encode( '' ), false ));
        $this->assertEquals($signature, hash_hmac("sha512", '' , $key));

        $rpc->request('get','asd/asd/asd', array());

    }
}
