<?php

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Helpers\RefundHelper;

class RefundController extends BaseController
{

    const REFUND_URI = '/refund';

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    /**
     * @param $id
     * @return \SpryngPaymentsApiPhp\Object\Refund
     */
    public function getRefundById($id)
    {
        $http = $this->initiateRequestHandler('GET', $this->api->getApiEndpoint(), self::REFUND_URI . '/' . $id,
            array('X-APIKEY' => $this->api->getApiKey()));

        $http->doRequest();

        $response = $http->getResponse();
        $jsonResponse = json_decode($response);

        if (count($jsonResponse))
        {
            $refund = RefundHelper::fillRefundObject($jsonResponse);
        }

        return $refund;
    }
}