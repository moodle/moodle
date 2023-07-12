<?php

namespace SimpleSAML\Module\cdc;

/**
 * CDC client class.
 *
 * @package SimpleSAMLphp
 */

class Client
{
    /**
     * Our CDC domain.
     *
     * @var string
     */
    private $domain;


    /**
     * The CDC server we send requests to.
     *
     * @var Server
     */
    private $server;


    /**
     * Initialize a CDC client.
     *
     * @param string $domain  The domain we should query the server for.
     */
    public function __construct($domain)
    {
        assert(is_string($domain));

        $this->domain = $domain;
        $this->server = new Server($domain);
    }


    /**
     * Receive a CDC response.
     *
     * @return array|null  The response, or NULL if no response is received.
     */
    public function getResponse()
    {
        return $this->server->getResponse();
    }


    /**
     * Send a request.
     *
     * @param string $returnTo  The URL we should return to afterwards.
     * @param string $op  The operation we are performing.
     * @param array $params  Additional parameters.
     * @return void
     */
    public function sendRequest($returnTo, $op, array $params = [])
    {
        assert(is_string($returnTo));
        assert(is_string($op));

        $params['op'] = $op;
        $params['return'] = $returnTo;
        $this->server->sendRequest($params);
    }
}
