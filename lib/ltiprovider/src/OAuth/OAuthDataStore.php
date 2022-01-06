<?php

namespace IMSGlobal\LTI\OAuth;

/**
 * Class to represent an %OAuth Data Store
 *
 * @copyright  Andy Smith
 * @version 2008-08-04
 * @license https://opensource.org/licenses/MIT The MIT License
 */
class OAuthDataStore {
    function lookup_consumer($consumer_key) {
        // implement me
    }

    function lookup_token($consumer, $token_type, $token) {
        // implement me
    }

    function lookup_nonce($consumer, $token, $nonce, $timestamp) {
        // implement me
    }

    function new_request_token($consumer, $callback = null) {
        // return a new token attached to this consumer
    }

    function new_access_token($token, $consumer, $verifier = null) {
        // return a new access token attached to this consumer
        // for the user associated with this token if the request token
        // is authorized
        // should also invalidate the request token
    }

}
