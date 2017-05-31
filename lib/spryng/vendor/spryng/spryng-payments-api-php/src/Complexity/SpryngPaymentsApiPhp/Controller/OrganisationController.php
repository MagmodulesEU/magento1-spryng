<?php

namespace SpryngPaymentsApiPhp\Controller;

use SpryngPaymentsApiPhp\Client;
use SpryngPaymentsApiPhp\Helpers\OrganisationHelper;
use SpryngPaymentsApiPhp\Utility\RequestHandler;

class OrganisationController extends BaseController
{
    const ORGANISATION_URI = "/organisation";

    public function __construct(Client $api)
    {
        parent::__construct($api);
    }

    public function getAll()
    {
        $http = $this->initiateRequestHandler('GET', $this->api->getApiEndpoint(), static::ORGANISATION_URI,
            array('X-APIKEY' => $this->api->getApiKey()));

        $http->doRequest();

        $response = json_decode($http->getResponse());
        $organisations = array();

        foreach ($response as $organisation)
        {
            $organisationObj = OrganisationHelper::fill($organisation);
            array_push($organisations, $organisationObj);
        }

        return $organisations;
    }
}