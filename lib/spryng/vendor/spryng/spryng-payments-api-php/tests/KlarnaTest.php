<?php

use SpryngPaymentsApiPhp\Object\Good;
use SpryngPaymentsApiPhp\Object\GoodsList;

require_once('BaseTest.php');

class KlarnaTest extends BaseTest
{
    const TEST_INITIATE_ARGUMENTS = array(
        'account' => self::TEST_ACCOUNT_ID,
        'amount' => 1000,
        'customer' => '590073a278cfeb7469c500a1',
        'customer_ip' => '127.0.0.1',
        'dynamic_descriptor' => 'Test Klarna Transaction',
        'capture' => true,
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Win64; x64; Trident/6.0)',
        'details' => [
            'redirect_url' => 'https://spryngpayments.com/redirect/klarna',
        ]
    );

    const TEST_KLARNA_REFUND_TRANSACTION_ID = '590072d278cfeb7469c4fe85';

    const TEST_KLARNA_CAPTURE_TRANSACTION_ID = '590072d278cfeb7469c4fe85';

    public function testGetPClasses()
    {
        $pclasses = $this->client->Klarna->getPClasses(self::TEST_ACCOUNT_ID);

        foreach ($pclasses as $pclass)
        {
            $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\PClass', $pclass);
        }
    }

    public function testInitiateCaptureAndRefundKlarnaTransaction()
    {
        $arguments = self::TEST_INITIATE_ARGUMENTS;

        $pclasses = $this->client->Klarna->getPClasses($arguments['account']);
        $goodsList = new GoodsList();
        $good = new Good(0, [32], $arguments['amount'], 1, 'SHOE_001', 'Shoe', 21);

        $goodsList->add($good);
        $arguments['details']['goods_list'] = $goodsList;

        $arguments['details']['pclass'] = $pclasses[0]->_id;

        $transaction = $this->client->Klarna->initiate($arguments);

        $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Transaction', $transaction);

        $this->assertTrue($this->client->Klarna->capture($transaction->_id, array(
            'details' => array(
                'flags' => array(4)
            )
        )));

        $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Refund',
            $this->client->Klarna->refund($transaction->_id));
    }
}