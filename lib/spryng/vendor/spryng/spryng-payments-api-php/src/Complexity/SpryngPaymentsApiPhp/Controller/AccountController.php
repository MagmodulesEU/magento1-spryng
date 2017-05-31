<?php

namespace SpryngPaymentsApiPhp\Controller;

use GuzzleHttp\Exception\ClientException;
use SpryngPaymentsApiPhp\Exception\AccountException;
use SpryngPaymentsApiPhp\Exception\RequestException;
use SpryngPaymentsApiPhp\Helpers\AccountHelper;
use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

/**
 * Class AccountController
 * @package SpryngPaymentsApiPhp\Controller
 */
class AccountController extends BaseController
{
    /**
     * @const string The account
     */
    const ACCOUNT_URI = "/account";

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    public function getAll()
    {
        $http = new RequestHandler();
        $http->setHttpMethod("GET");
        $http->setBaseUrl($this->api->getApiEndpoint());
        $http->setQueryString(static::ACCOUNT_URI);
        $http->addHeader($this->api->getApiKey(), 'X-APIKEY');
        $http->doRequest();

        $response = json_decode($http->getResponse());
        $accounts = array();

        foreach($response as $account)
        {
            $accountObj = AccountHelper::fill($account);
            array_push($accounts, $accountObj);
        }

        return $accounts;
    }

    /**
     * Get an account instance based on it's ID
     *
     * @param $id
     * @return mixed
     * @throws AccountException
     */
    public function getAccountById($id)
    {
        $http = new RequestHandler();
        $http->setHttpMethod("GET");
        $http->setBaseUrl($this->api->getApiEndpoint());
        $http->setQueryString(static::ACCOUNT_URI . '?_id='.$id);
        $http->addHeader($this->api->getApiKey(), 'X-APIKEY');

        try
        {
            $http->doRequest();
        }
        catch (RequestException $ex)
        {
            throw new AccountException("Account not found", 601);
        }

        $response = $http->getResponse();

        $jsonResponse = json_decode($response);

        if (count($jsonResponse) > 0)
        {
            $account = AccountHelper::fill($jsonResponse[0]);
        }
        else
        {
            throw new AccountException("Account not found", 601);
        }

        return $account;
    }
}