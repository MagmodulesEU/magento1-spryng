<?php

require_once('BaseTest.php');

class AccountTest extends BaseTest
{
    const INVALID_ACCOUNT_ID = '57d1b5fxxxxxxxxxxxxxxxxx';

    public function testGetAllAccounts()
    {
        $accounts = $this->client->account->getAll();
        foreach($accounts as $account)
        {
            $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Account', $account);
        }
    }

    public function testGetAccountById()
    {
        $account = $this->client->account->getAccountById(self::TEST_ACCOUNT_ID);

        $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Account', $account);
    }

    public function testInvalidAccountIdThrowsException()
    {
        $this->expectException(\SpryngPaymentsApiPhp\Exception\AccountException::class);

        $this->client->account->getAccountById(self::INVALID_ACCOUNT_ID);
    }
}