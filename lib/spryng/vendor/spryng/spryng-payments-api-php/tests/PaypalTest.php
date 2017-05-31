<?php

require_once('BaseTest.php');

class PaypalTest extends BaseTest
{
    const TEST_INITIATE_ARGUMENTS = array(
        'account' => self::TEST_ACCOUNT_ID,
        'amount' => 1000,
        'customer_ip' => '127.0.0.1',
        'dynamic_descriptor' => 'Test Paypal Transaction',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Win64; x64; Trident/6.0)',
        'details' => [
            'redirect_url' => 'https://spryngpayments.com/redirect/paypal',
            'capture_now' => true
        ]
    );

    public function testInitiatePaypalTransaction()
    {
        $transaction = $this->client->Paypal->initiate(static::TEST_INITIATE_ARGUMENTS);

        $this->assertNotFalse(filter_var($transaction->details->approval_url, FILTER_VALIDATE_URL));
    }
}