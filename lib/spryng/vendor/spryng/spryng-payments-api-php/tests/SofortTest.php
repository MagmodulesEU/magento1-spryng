<?php

require_once('BaseTest.php');

class SofortTest extends BaseTest
{
    const TEST_PROJECT_ID = '336039';

    const TEST_INITIATE_ARGUMENTS = array(
        'account' => self::TEST_ACCOUNT_ID,
        'amount' => 1000,
        'customer_ip' => '127.0.0.1',
        'dynamic_descriptor' => 'Test SOFORT Transaction',
        'user_agent' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Win64; x64; Trident/6.0)',
        'details' => [
            'redirect_url' => 'https://spryngpayments.com/redirect/sofort',
            'project_id'   => self::TEST_PROJECT_ID
        ],
        'country_code' => 'NL'
    );

    public function testInitiateSOFORTTransaction()
    {
        $transaction = $this->client->SOFORT->initiate(static::TEST_INITIATE_ARGUMENTS);

        $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Transaction', $transaction);
    }
}