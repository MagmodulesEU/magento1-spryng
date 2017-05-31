<?php

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Exception\CustomerException;
use SpryngPaymentsApiPhp\Helpers\CustomerHelper;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

class CustomerController extends BaseController
{
    const CUSTOMER_URI = "/customer";

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    public function getCustomerById($id)
    {
        $http = $this->initiateRequestHandler('GET', $this->api->getApiEndpoint(), self::CUSTOMER_URI . '/' . $id,
            array('X-APIKEY' => $this->api->getApiKey()));

        $http->doRequest();

        $response = $http->getResponse();
        $jsonResponse = json_decode($response);

        if (count($jsonResponse))
        {
            $customer = CustomerHelper::fillCustomerObject($jsonResponse);
        }
        else
        {
            throw new CustomerException("Customer not found", 501);
        }

        return $customer;
    }

    public function create($arguments)
    {
        CustomerHelper::validateNewCustomerArguments($arguments);

        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint(), static::CUSTOMER_URI,
            array('X-APIKEY' => $this->api->getApiKey()), $arguments);

        $http->doRequest();

        $response = $http->getResponse();
        $json = json_decode($response);
        $newCustomer = CustomerHelper::fillCustomerObject($json);

        return $newCustomer;
    }

    public function update($id, $arguments)
    {
        CustomerHelper::validateNewCustomerArguments($arguments);

        $http = $this->initiateRequestHandler('POST', $this->api->getApiEndpoint(), static::CUSTOMER_URI . '/' . $id,
            array('X-APIKEY' => $this->api->getApiKey()), $arguments);

        $http->doRequest();

        $response = $http->getResponse();
        $json = json_decode($response);
        $newCustomer = CustomerHelper::fillCustomerObject($json);

        return $newCustomer;
    }
}