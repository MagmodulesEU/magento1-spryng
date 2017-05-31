<?php

require_once('BaseTest.php');

class TransactionTest extends BaseTest
{
    const TEST_TRANSACTION_ID       = '590071ec78cfeb7469c4fb7b';

    const REFUND_AMOUNT             = 1000;

    const TEST_CREATE_ARGUMENTS     = array(
        'account'               => self::TEST_ACCOUNT_ID,
        'amount'                => '10000',
        'card'                  => '58009d04e1b39b6692edee40',
        'customer_ip'           => '127.0.0.1',
        'dynamic_descriptor'    => 'Test transaction',
        'payment_product'       => 'card',
        'user_agent'            => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Win64; x64; Trident/6.0)'
    );

    public function testGetAllTransactionsDoesNotReturnNull()
    {
        $response = $this->client->transaction->getAll();

        $this->assertTrue($response !== null);
    }

    public function testGetAllTransactionsAreTransactionInstances()
    {
        $response = $this->client->transaction->getAll();

        foreach($response as $transaction)
        {
            $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Transaction', $transaction);
        }
    }

    public function testGetTransactionByIdDoesNotReturnNull()
    {
        $transaction = $this->client->transaction->getTransactionById(static::TEST_TRANSACTION_ID);

        $this->assertTrue($transaction !== null);
        $this->assertNotNull($transaction->_id);
    }

    public function testGetTransactionByIdReturnsTransactionInstance()
    {
        $transaction = $this->client->transaction->getTransactionById(static::TEST_TRANSACTION_ID);

        $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Transaction', $transaction);
    }
    public function testCreateTransaction()
    {
        $transaction = $this->client->transaction->create(static::TEST_CREATE_ARGUMENTS);

        $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Transaction', $transaction);

        // Save arguments to variable so they don't have to be called statically.
        $args = static::TEST_CREATE_ARGUMENTS;

        // Validate transaction object
        $this->assertTrue($transaction->account->_id === $args['account']);
        $this->assertTrue($transaction->amount === (int) $args['amount']);
        $this->assertTrue($transaction->card->_id === $args['card']);
        $this->assertTrue($transaction->customer_ip === $args['customer_ip']);
        $this->assertTrue($transaction->dynamic_descriptor === $args['dynamic_descriptor']);
        $this->assertTrue($transaction->payment_product === $args['payment_product']);
        $this->assertTrue($transaction->user_agent === $args['user_agent']);
    }
}