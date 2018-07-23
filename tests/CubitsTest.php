<?php

namespace Tests\Cubits;

use Cubits\Cubits;

class CubitsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @throws \Cubits\ApiException
     */
    public function test_core()
    {
        $key = '12345';
        $secret = '1234566789900';
        $core = Cubits::withApiKey($key, $secret);
        $resp_create = new \stdClass();
        $resp_create->id = '12';
        $resp_create->invoice_url = 'invurl';
        $resp_create->address = 'address';
        $resp_create->created_at = 'today';
        $resp_create->valid_until = '777';

        $options = array(
            'success_url' => 'success_url',
            'callback_url' => 'callback_url',
            'cancel_url' => 'cancel_url',
            'reference' => '123'
        );

        $this->assertObjectHasAttribute('authenticator', $core);

        $cubits = $this->getMockBuilder(Cubits::class)
            ->setConstructorArgs(array($key, null, $secret))
            ->setMethods(array('post', 'get', 'createInvoiceWithOptions'))
            ->getMock();
        $cubits->method('post')
            ->willReturn($resp_create);

        $cubits->expects($this->once())
            ->method('createInvoiceWithOptions')
            ->willReturn($resp_create);
        $x = $cubits->createInvoice('EUR', '30.00', 'Test', $options);
        $this->assertEquals($x, $resp_create);


        $resp_get = new \stdClass();
        $resp_get->id = '12';
        $resp_get->status = 'pending';
        $resp_get->address = 'address';

        $resp_get->merchant_currency = 'USD';
        $resp_get->merchant_amount = '123.56';

        $resp_get->invoice_currency = 'BTC';
        $resp_get->invoice_amount = '0.3';
        $resp_get->invoice_url = 'url';

        $resp_get->paid_currency = 'USD';
        $resp_get->paid_amount = '123.56';

        $resp_get->name = 'name';
        $resp_get->description = 'description';
        $resp_get->reference = '1234';

        $resp_get->callback_url = 'callback_url';
        $resp_get->success_url = 'success_url';
        $resp_get->cancel_url = 'cancel_url';
        $resp_get->notify_email = 'asdf@asdf.de';

        $cubits->expects($this->once())
            ->method('get')
            ->willReturn(json_encode($resp_get));

        $x = $cubits->getInvoice(1234);
        $this->assertEquals($x, $resp_get);

    }
}
