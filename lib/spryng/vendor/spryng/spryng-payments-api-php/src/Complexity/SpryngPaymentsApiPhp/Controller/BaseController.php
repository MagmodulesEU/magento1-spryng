<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

/**
 * Class Spryng_Payments_Api_Controller_BaseController
 * @package SpryngPaymentsApiPhp\Controller
 */
class BaseController
{

    /**
     * URI for the transaction endpoint
     *
     * @const String TRANSACTION_URI
     */
    const TRANSACTION_URI = "/transaction";

    /**
     * @var Client
     */
    protected $api;

    public function __construct(Client $api)
    {
        $this->api = $api;
    }

    /**
     * Prepares an instance of RequestHandler for initiating a transaction.
     *
     * @param $method
     * @param $url
     * @param $query
     * @param $headers
     * @param array $arguments
     * @return RequestHandler
     */
    public function initiateRequestHandler($method, $url, $query, $headers, $arguments = array())
    {
        $http = new RequestHandler();
        $http->setHttpMethod($method);
        $http->setBaseUrl($url);
        $http->setQueryString($query);
        foreach($headers as $name => $value)
        {
            $http->addHeader($value, $name);
        }

        if ($method === 'POST')
        {
            $http->setPostParameters($arguments);
        }

        return $http;
    }
}