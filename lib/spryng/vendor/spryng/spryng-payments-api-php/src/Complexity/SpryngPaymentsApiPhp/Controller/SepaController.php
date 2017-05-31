<?php

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Helpers\SepaHelper;
use SpryngPaymentsApiPhp\Helpers\TransactionHelper;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

class SepaController extends BaseController
{
    const SEPA_INITIATE_URI = "/transaction/sepa/initiate";

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    public function initiate(array $arguments)
    {
        SepaHelper::validateInitializeSepaArguments($arguments);

        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint(), static::SEPA_INITIATE_URI,
            array('X-APIKEY' => $this->api->getApiKey()), $arguments);

        $http->doRequest();

        $transaction = TransactionHelper::fillTransaction(json_decode($http->getResponse()));

        return $transaction;
    }
}