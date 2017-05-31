<?php

require_once('BaseTest.php');

class CardTest extends BaseTest
{
    const TEST_CREATE_ARGUMENTS     = array(
        'card_number'   => '4024007108173153',
        'cvv'           => '123',
        'expiry_month'  => '12',
        'expiry_year'   => '17',
        'organisation'  => self::TEST_ORGANISATION_ID
    );

    public function testCreateCard()
    {
        $card = $this->client->card->create(static::TEST_CREATE_ARGUMENTS);

        $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Card', $card);
    }

}