<?php

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Helpers\PaypalHelper;
use SpryngPaymentsApiPhp\Helpers\TransactionHelper;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

class PaypalController extends BaseController
{
    const PAYPAL_INITIATE_URI = "/transaction/paypal/initiate";

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    public function initiate(array $arguments)
    {
        PaypalHelper::validateInitializePaypalArguments($arguments);

        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint(), static::PAYPAL_INITIATE_URI,
            array('X-APIKEY' => $this->api->getApiKey()), $arguments);

        $http->doRequest();

        $transaction = TransactionHelper::fillTransaction(json_decode($http->getResponse()));

        return $transaction;
    }
}