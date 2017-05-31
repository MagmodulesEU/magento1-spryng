<?php

use PHPUnit\Framework\TestCase;
use SpryngPaymentsApiPhp\Client;

date_default_timezone_set('Europe/Amsterdam');

require_once(dirname(__FILE__) .'/../vendor/autoload.php');

class BaseTest extends TestCase
{
    const TEST_API_KEY = "MXQ8vCX8xc9YXfKhZdgECwGJWMQk8BfUw1QngC-Or_8";

    const TEST_ACCOUNT_ID = "58ff3bfa1c4d1f427b2b4c89";

    const TEST_CUSTOMER_ID = "590073a278cfeb7469c500a1";

    const TEST_ORGANISATION_ID = "58ff36e51c4d1f427b2aeebd";

    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = new Client(static::TEST_API_KEY, true);
    }

    public function testExceptionIsRaisedOnContruction()
    {
        $this->assertInstanceOf('SpryngPaymentsApiPhp\Client', $this->client);
    }
}