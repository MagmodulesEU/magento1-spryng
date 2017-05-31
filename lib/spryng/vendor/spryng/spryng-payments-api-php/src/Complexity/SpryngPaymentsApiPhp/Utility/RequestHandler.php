<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Utility;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use SpryngPaymentsApiPhp\Exception\RequestException;

/**
 * Class Spryng_Api_Utilities_RequestHandler
 * @package SpryngApiHttpPhp\Utilities
 */
class RequestHandler
{

    /**
     * GuzzleHttp Client
     *
     * @var Client
     */
    protected $http;

    /**
     * The HTTP method used for this request
     *
     * @var string
     */
    protected $httpMethod;

    /**
     * The base URL for the requests
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The query string, basically everything after baseUrl
     *
     * @var string
     */
    protected $queryString;

    /**
     * Array of GET parameters
     *
     * @var array
     */
    protected $getParameters = array();

    /**
     * Array of POST parameters
     *
     * @var array
     */
    protected $postParameters = array();

    /**
     * Array of HTTP Headers
     *
     * @var array
     */
    protected $headers;

    /**
     * Response from the request
     *
     * @var mixed
     */
    protected $response;

    /**
     * Response code from the server
     *
     * @var int
     */
    protected $responseCode;

    /**
     * Spryng_Payments_Api_Utility_RequestHandler constructor.
     * Creates instance of GuzzleHttp\Client
     */
    public function __construct()
    {
        $this->http = curl_init();
        $this->addHeader('SpryngPaymentsPHP/1.1', 'User-Agent');
    }

    /**
     * Executes the HTTP request
     *
     * @throws RequestException
     */
    public function doRequest()
    {
        curl_setopt($this->http, CURLOPT_CUSTOMREQUEST, $this->getHttpMethod());
        curl_setopt($this->http, CURLOPT_URL, $this->prepareUrl());
        curl_setopt($this->http, CURLOPT_RETURNTRANSFER, true);


        switch($this->getHttpMethod())
        {
            case 'GET':
                $this->doGetRequest();
                break;
            case 'POST':
                $this->doPostRequest();
                break;
            default:
                throw new RequestException("Invalid HTTP method.", 102);
                break;
        }

        if ($this->getResponseCode() !== 200)
        {
            $this->handleUnsuccessfulRequest();
        }
    }

    /**
     * Executes a GET Request
     */
    private function doGetRequest ()
    {
        curl_setopt($this->http, CURLOPT_HTTPHEADER, $this->getHeaders());
        $response = curl_exec($this->http);
        $this->setResponse($response);
        $this->setResponseCode(curl_getinfo($this->http, CURLINFO_RESPONSE_CODE));
    }

    /**
     * Executes a POST Request
     */
    private function doPostRequest()
    {
        $this->addHeader('application/json', 'Content-Type');
        curl_setopt($this->http, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($this->http, CURLOPT_POST, 1);
        curl_setopt($this->http, CURLOPT_POSTFIELDS, json_encode($this->getPostParameters()));

        $response = curl_exec($this->http);
        $this->setResponse($response);
        $this->setResponseCode(curl_getinfo($this->http, CURLINFO_RESPONSE_CODE));
    }

    /**
     * prepares the url by adding query string parameters
     *
     * @return string
     */
    public function prepareurl()
    {
        $url = $this->getbaseurl() . $this->getquerystring();

        if ( count( $this->getGetParameters () ) > 0 )
        {
            $url .= '?';

            $iterator = 0;
            foreach ( $this->getGetParameters() as $key => $parameter )
            {
                $iterator++;
                $url .= $key . '=' . $parameter;

                if ( $iterator != count ( $this->getGetParameters() ) )
                {
                    $url .= '&';
                }
            }
        }

        return $url;
    }

    /**
     * Handles an unsuccessful request
     *
     * @throws RequestException
     */
    protected function handleUnsuccessfulRequest()
    {
        throw new RequestException(sprintf("Request unsuccessful. Response Code: %d Message: %s",
            $this->getResponseCode(),
            $this->getResponse()
        ), 101);
    }

    /**
     * @return Client
     */
    public function getHttp()
    {
        return $this->http;
    }

    /**
     * @param Client $http
     */
    public function setHttp($http)
    {
        $this->http = $http;
    }

    /**
     * Returns HTTP method
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Sets HTTP method
     *
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod)
    {
        switch ($httpMethod)
        {
            case 'POST':

        }
        $this->httpMethod = $httpMethod;
    }

    /**
     * Returns baseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets baseUrl
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Returns the Query String
     *
     * @return string
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Sets the Query String
     *
     * @param string $queryString
     */
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
    }

    /**
     * Returns array of all GET parameters
     *
     * @return array
     */
    public function getGetParameters()
    {
        return $this->getParameters;
    }

    /**
     * Reset $this->getParameters to $getParameters. Parses as url if $parse is true.
     *
     * @param $getParameters
     * @param bool|false $parse
     */
    public function setGetParameters($getParameters, $parse = false)
    {
        $this->getParameters = array();
        if ($parse) {
            foreach ($getParameters as $key => $parameter)
            {
                $this->getParameters[$key] = urlencode($parameter);
            }
        }
        else {
            $this->getParameters = $getParameters;
        }
    }

    /**
     * Adds a new parameter to the GET parameter array
     *
     * @param $value
     * @param null $key
     * @param bool|false $parse
     */
    public function addGetParameter($value, $key = null, $parse = false)
    {
        if ($parse)
        {
            $value = urlencode($value);
        }

        if ($key === null)
        {
            array_push($this->getParameters, $value);
        }
        else
        {
            $this->getParameters[$key] = $value;
        }
    }

    /**
     * Returns all POST parameters as array
     *
     * @return array
     */
    public function getPostParameters()
    {
        return $this->postParameters;
    }

    /**
     * Sets all POST parameters at once.
     *
     * @param $postParameters
     * @param bool|false $parse
     */
    public function setPostParameters($postParameters, $parse = false)
    {
        $this->postParameters = array();
        if ($parse) {
            foreach ($postParameters as $key => $parameter)
            {
                $this->postParameters[$key] = urlencode($parameter);
            }
        }
        else {
            $this->postParameters = $postParameters;
        }
    }

    /**
     * Adds a single POST parameter
     *
     * @param $value
     * @param null $key
     * @param bool|false $parse
     */
    public function addPostParameter($value, $key = null, $parse = false)
    {
        if ($parse)
        {
            $value = urlencode($value);
        }

        if ($key === null)
        {
            array_push($this->postParameters, $value);
        }
        else
        {
            $this->postParameters[$key] = $value;
        }
    }

    /**
     * Returns array of all Http Headers
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = array();
        foreach($this->headers as $name => $header)
        {
            $headers[] = $name . ': ' . $header;
        }
        return $headers;
    }

    /**
     * Set all headers at once
     *
     * @param $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Set a single header
     *
     * @param $value
     * @param null $key
     */
    public function addHeader($value, $key)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Returns the response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the response
     *
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param int $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }
}