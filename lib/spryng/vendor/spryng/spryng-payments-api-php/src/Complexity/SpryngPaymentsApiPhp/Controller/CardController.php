<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Helpers\CardHelper;
use SpryngPaymentsApiPhp\Object\Card;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

/**
 * Class CardController
 * @package SpryngPaymentsApiPhp\Controller
 */
class CardController extends BaseController
{

    const CARD_URI = "/card";

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    /**
     * @param $arguments
     * @return Card
     * @throws \SpryngPaymentsApiPhp\Exception\CardException
     * @throws \SpryngPaymentsApiPhp\Exception\RequestException
     */
    public function create($arguments)
    {
        CardHelper::validateNewCardArguments($arguments);

        $http = new RequestHandler();
        $http->setHttpMethod("POST");
        $http->setBaseUrl($this->api->getApiEndpoint());
        $http->setQueryString(static::CARD_URI);
        $http->addHeader($this->api->getApiKey(), 'X-APIKEY');
        $http->setPostParameters($arguments, false);
        $http->doRequest();

        $response = $http->getResponse();

        $jsonResponse = json_decode($response);
        $card = CardHelper::fillCard($jsonResponse);

        return $card;
    }
}