<?php

namespace IMSGlobal\LTI\ToolProvider\Service;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\HTTPMessage;

/**
 * Class to implement a service
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class Service
{

/**
 * Whether service request should be sent unsigned.
 *
 * @var boolean $unsigned
 */
    public $unsigned = false;

/**
 * Service endpoint.
 *
 * @var string $endpoint
 */
    protected $endpoint;
/**
 * Tool Consumer for this service request.
 *
 * @var ToolConsumer $consumer
 */
    private $consumer;
/**
 * Media type of message body.
 *
 * @var string $mediaType
 */
    private $mediaType;

/**
 * Class constructor.
 *
 * @param ToolConsumer $consumer   Tool consumer object for this service request
 * @param string       $endpoint   Service endpoint
 * @param string       $mediaType  Media type of message body
 */
    public function __construct($consumer, $endpoint, $mediaType)
    {

        $this->consumer = $consumer;
        $this->endpoint = $endpoint;
        $this->mediaType = $mediaType;

    }

/**
 * Send a service request.
 *
 * @param string  $method      The action type constant (optional, default is GET)
 * @param array   $parameters  Query parameters to add to endpoint (optional, default is none)
 * @param string  $body        Body of request (optional, default is null)
 *
 * @return HTTPMessage HTTP object containing request and response details
 */
    public function send($method, $parameters = array(), $body = null)
    {

        $url = $this->endpoint;
        if (!empty($parameters)) {
            if (strpos($url, '?') === false) {
                $sep = '?';
            } else {
                $sep = '&';
            }
            foreach ($parameters as $name => $value) {
                $url .= $sep . urlencode($name) . '=' . urlencode($value);
                $sep = '&';
            }
        }
        if (!$this->unsigned) {
            $header = ToolProvider\ToolConsumer::addSignature($url, $this->consumer->getKey(), $this->consumer->secret, $body, $method, $this->mediaType);
        } else {
            $header = null;
        }

// Connect to tool consumer
        $http = new HTTPMessage($url, $method, $body, $header);
// Parse JSON response
        if ($http->send() && !empty($http->response)) {
            $http->responseJson = json_decode($http->response);
            $http->ok = !is_null($http->responseJson);
        }

        return $http;

    }

}
