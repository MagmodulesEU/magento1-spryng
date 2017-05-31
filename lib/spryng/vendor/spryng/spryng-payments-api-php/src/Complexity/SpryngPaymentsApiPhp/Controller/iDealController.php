<?php

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Helpers\iDealHelper;
use SpryngPaymentsApiPhp\Helpers\TransactionHelper;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

class iDealController extends BaseController
{
    const IDEAL_INITIATE_URI = "/transaction/ideal/initiate";

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    public function initiate(array $arguments)
    {
        iDealHelper::validateInitiateiDealArguments($arguments);

        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint(), static::IDEAL_INITIATE_URI,
            array('X-APIKEY' => $this->api->getApiKey()), $arguments);

        $http->doRequest();

        $transaction = TransactionHelper::fillTransaction(json_decode($http->getResponse()));

        return $transaction;
    }
}