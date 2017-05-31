<?php

require_once('BaseTest.php');

class RefundTest extends BaseTest
{
    const REFUND_ID = "590071a878cfeb7469c4fb67";

    public function testGetRefundById()
    {
        $refund = $this->client->refund->getRefundById(static::REFUND_ID);

        $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Refund', $refund);
    }
}