<?php

namespace IMSGlobal\LTI\ToolProvider;

use IMSGlobal\LTI\OAuth;

/**
 * Class to represent an OAuth datastore
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.2
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class OAuthDataStore extends OAuth\OAuthDataStore
{

/**
 * Tool Provider object.
 *
 * @var ToolProvider $toolProvider
 */
    private $toolProvider = null;

/**
 * Class constructor.
 *
 * @param ToolProvider $toolProvider Tool_Provider object
 */
    public function __construct($toolProvider)
    {

        $this->toolProvider = $toolProvider;

    }

/**
 * Create an OAuthConsumer object for the tool consumer.
 *
 * @param string $consumerKey Consumer key value
 *
 * @return OAuthConsumer OAuthConsumer object
 */
    function lookup_consumer($consumerKey)
    {

        return new OAuth\OAuthConsumer($this->toolProvider->consumer->getKey(),
           $this->toolProvider->consumer->secret);

    }

/**
 * Create an OAuthToken object for the tool consumer.
 *
 * @param string $consumer   OAuthConsumer object
 * @param string $tokenType  Token type
 * @param string $token      Token value
 *
 * @return OAuthToken OAuthToken object
 */
    function lookup_token($consumer, $tokenType, $token)
    {

        return new OAuth\OAuthToken($consumer, '');

    }

/**
 * Lookup nonce value for the tool consumer.
 *
 * @param OAuthConsumer $consumer  OAuthConsumer object
 * @param string        $token     Token value
 * @param string        $value     Nonce value
 * @param string        $timestamp Date/time of request
 *
 * @return boolean True if the nonce value already exists
 */
    function lookup_nonce($consumer, $token, $value, $timestamp)
    {

        $nonce = new ConsumerNonce($this->toolProvider->consumer, $value);
        $ok = !$nonce->load();
        if ($ok) {
            $ok = $nonce->save();
        }
        if (!$ok) {
            $this->toolProvider->reason = 'Invalid nonce.';
        }

        return !$ok;

    }

/**
 * Get new request token.
 *
 * @param OAuthConsumer $consumer  OAuthConsumer object
 * @param string        $callback  Callback URL
 *
 * @return string Null value
 */
    function new_request_token($consumer, $callback = null)
    {

        return null;

    }

/**
 * Get new access token.
 *
 * @param string        $token     Token value
 * @param OAuthConsumer $consumer  OAuthConsumer object
 * @param string        $verifier  Verification code
 *
 * @return string Null value
 */
    function new_access_token($token, $consumer, $verifier = null)
    {

        return null;

    }

}
