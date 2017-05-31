<?php

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Helpers\KlarnaHelper;
use SpryngPaymentsApiPhp\Helpers\TransactionHelper;
use SpryngPaymentsApiPhp\Object\Account;
use SpryngPaymentsApiPhp\Object\PClass;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

class KlarnaController extends BaseController
{
    const KLARNA_INITIATE_URI = "/transaction/klarna/initiate";

    const KLARNA_PCLASS_URI = "/account/{{ACCOUNT}}/klarna/pclasses";

    const KLARNA_CAPTURE_URI = "/transaction/{{ID}}/klarna/klarna/capture";

    const REFUND_TRANSACTION_URI = '/klarna/klarna/refund';

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    public function refund($transactionId, $amount = null, $reason = null)
    {
        $transactionController = new TransactionController($this->api);
        $customQuery = self::TRANSACTION_URI . '/'. $transactionId . self::REFUND_TRANSACTION_URI;

        return $transactionController->refund($transactionId, $amount, $reason, $customQuery);
    }

    public function capture($transactionId, array $arguments)
    {
        KlarnaHelper::validateCaptureArguments($arguments);

        $uri = str_replace('{{ID}}', $transactionId, static::KLARNA_CAPTURE_URI);
        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint(), $uri, array('X-APIKEY' =>
            $this->api->getApiKey()), $arguments);
        $http->doRequest();

        return $http->getResponseCode() == 200 ? true : false;
    }

    public function initiate(array $arguments)
    {
        KlarnaHelper::validateKlarnaInitializeArguments($arguments);
        $arguments['details']['goods_list'] = KlarnaHelper::parseGoodsList($arguments['details']['goods_list']);

        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint(), static::KLARNA_INITIATE_URI,
            array('X-APIKEY' => $this->api->getApiKey()), $arguments);

        $http->doRequest();

        $transaction = TransactionHelper::fillTransaction(json_decode($http->getResponse()));

        return $transaction;
    }

    public function getPClasses($account, $asPClassObjects = true)
    {
        if ($account instanceof Account)
        {
            $account = $account->_id;
        }

        $http = new RequestHandler();
        $http->setHttpMethod("GET");
        $http->setBaseUrl($this->api->getApiEndpoint());
        $http->setQueryString(str_replace('{{ACCOUNT}}', $account, static::KLARNA_PCLASS_URI));
        $http->doRequest();

        $response = json_decode($http->getResponse());

        if ($asPClassObjects)
        {
            $pclasses = array();

            foreach ($response->details as $pclass)
            {
                $pclassObject = new PClass();

                $pclassObject->_id = $pclass->_id;
                $pclassObject->description = $pclass->description;
                $pclassObject->interest_rate = $pclass->interest_rate;
                $pclassObject->invoice_fee = $pclass->invoice_fee;
                $pclassObject->nb_months = $pclass->nb_months;
                $pclassObject->start_Fee = $pclass->start_fee;
                $pclassObject->type = $pclass->type;

                array_push($pclasses, $pclassObject);
            }

            return $pclasses;
        }
        else
        {
            return $response->details;
        }
    }
}