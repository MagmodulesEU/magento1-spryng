<?php

namespace SpryngPaymentsApiPhp\Controller;
use SpryngPaymentsApiPhp\Exception\RequestException;
use SpryngPaymentsApiPhp\Helpers\BancontactHelper;
use SpryngPaymentsApiPhp\Helpers\TransactionHelper;

/**
 * Class BacontactController
 * @package SpryngPaymentsApiPhp\Controller
 */
class BancontactController extends BaseController
{
    const BANCONTACT_INITIATE_URL = '/transaction/bancontact/initiate';

    /**
     * Initiate a new Bancontact transaction.
     *
     * @param $parameters
     * @return \SpryngPaymentsApiPhp\Object\Transaction
     * @throws RequestException
     */
    public function initiate($parameters)
    {
        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint() . static::BANCONTACT_INITIATE_URL,
            null, array('X-APIKEY' => $this->api->getApiKey()), $parameters);

        BancontactHelper::validateInitiateArguments($parameters);
        $http->doRequest();
        $response = $http->getResponse();
        $jsonResponse = json_decode($response);

        if (count($jsonResponse) > 0 && $http->getResponseCode() === 200)
        {
            $transaction = TransactionHelper::fillTransaction($jsonResponse[0]);
        }
        else
        {
            throw new RequestException(sprintf('Request for %s %s was not successful. Response Code: %s. Message: %s',
                $http->getHttpMethod(), $http->prepareurl(), $http->getResponseCode(), (string) $http->getResponse()));
        }

        return $transaction;
    }
}