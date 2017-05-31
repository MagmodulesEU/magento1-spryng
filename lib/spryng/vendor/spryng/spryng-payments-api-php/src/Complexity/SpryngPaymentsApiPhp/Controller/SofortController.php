<?php

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Helpers\SofortHelper;
use SpryngPaymentsApiPhp\Helpers\TransactionHelper;

/**
 * Class SofortController
 * @package SpryngPaymentsApiPhp\Controller
 */
class SofortController extends BaseController
{
    /**
     * The endpoint used for initiating SOFORT transactions
     *
     * @var string
     */
    const SOFORT_INITIATE_URI = "/transaction/sofort/initiate";

    /**
     * SofortController constructor.
     * @param Client $api
     */
    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    /**
     * Initiates a SOFORT transaction
     *
     * @param array $arguments
     * @return \SpryngPaymentsApiPhp\Object\Transaction
     */
    public function initiate(array $arguments)
    {
        SofortHelper::validateInitiateArguments($arguments);

        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint(), static::SOFORT_INITIATE_URI,
            array('X-APIKEY' => $this->api->getApiKey()), $arguments);

        $http->doRequest();

        $transaction = TransactionHelper::fillTransaction(json_decode($http->getResponse()));

        return $transaction;
    }
}