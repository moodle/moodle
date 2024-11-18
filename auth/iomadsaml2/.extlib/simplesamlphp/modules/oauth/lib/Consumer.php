<?php

namespace SimpleSAML\Module\oauth;

require_once(dirname(dirname(__FILE__)).'/libextinc/OAuth.php');

/**
 * OAuth Consumer
 *
 * @author Andreas Åkre Solberg, <andreas.solberg@uninett.no>, UNINETT AS.
 * @package SimpleSAMLphp
 */
class Consumer
{
    /** @var \OAuthConsumer */
    private $consumer;

    /** @var \OAuthSignatureMethod */
    private $signer;


    /**
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret)
    {
        $this->consumer = new \OAuthConsumer($key, $secret, null);
        $this->signer = new \OAuthSignatureMethod_HMAC_SHA1();
    }


    /**
     * Used only to load the libextinc library early
     * @return void
     */
    public static function dummy()
    {
    }


    /**
     * @param array $hrh
     * @return string|null
     */
    public static function getOAuthError($hrh)
    {
        foreach ($hrh as $h) {
            if (preg_match('|OAuth-Error:\s([^;]*)|i', $h, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }


    /**
     * @param array $hrh
     * @return string|null
     */
    public static function getContentType($hrh)
    {
        foreach ($hrh as $h) {
            if (preg_match('|Content-Type:\s([^;]*)|i', $h, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }


    /**
     * This static helper function wraps \SimpleSAML\Utils\HTTP::fetch
     * and throws an exception with diagnostics messages if it appear
     * to be failing on an OAuth endpoint.
     *
     * If the status code is not 200, an exception is thrown. If the content-type
     * of the response if text/plain, the content of the response is included in
     * the text of the Exception thrown.
     *
     * @param string $url
     * @param string $context
     * @return string
     * @throws \Exception
     */
    public static function getHTTP($url, $context = '')
    {
        try {
            $response = \SimpleSAML\Utils\HTTP::fetch($url, [], false);
        } catch (\SimpleSAML\Error\Exception $e) {
            $statuscode = 'unknown';

            /** @psalm-suppress UndefinedVariable */
            if (preg_match('/^HTTP.*\s([0-9]{3})/', $http_response_header[0], $matches)) {
                $statuscode = $matches[1];
            }

            $error = $context.' [statuscode: '.$statuscode.']: ';
            /** @psalm-suppress UndefinedVariable */
            $oautherror = self::getOAuthError($http_response_header);

            if (!empty($oautherror)) {
                $error .= $oautherror;
            }

            throw new \Exception($error.':'.$url);
        }
        // Fall back to return response, if could not reckognize HTTP header. Should not happen.
        /** @var string $response */
        return $response;
    }


    /**
     * @param string $url
     * @param array|null $parameters
     * @return \OAuthToken
     * @throws \Exception
     */
    public function getRequestToken($url, $parameters = null)
    {
        $req_req = \OAuthRequest::from_consumer_and_token($this->consumer, null, "GET", $url, $parameters);
        $req_req->sign_request($this->signer, $this->consumer, null);

        $response_req = self::getHTTP(
            $req_req->to_url(),
            'Contacting request_token endpoint on the OAuth Provider'
        );

        parse_str($response_req, $responseParsed);

        if (array_key_exists('error', $responseParsed)) {
            throw new \Exception('Error getting request token: '.$responseParsed['error']);
        }

        $requestToken = $responseParsed['oauth_token'];
        $requestTokenSecret = $responseParsed['oauth_token_secret'];

        return new \OAuthToken($requestToken, $requestTokenSecret);
    }


    /**
     * @param string $url
     * @param \OAuthToken $requestToken
     * @param bool $redirect
     * @param callable|null $callback
     * @return string
     */
    public function getAuthorizeRequest($url, $requestToken, $redirect = true, $callback = null)
    {
        $params = ['oauth_token' => $requestToken->key];
        if ($callback) {
            $params['oauth_callback'] = $callback;
        }
        $authorizeURL = \SimpleSAML\Utils\HTTP::addURLParameters($url, $params);
        if ($redirect) {
            \SimpleSAML\Utils\HTTP::redirectTrustedURL($authorizeURL);
            exit;
        }
        return $authorizeURL;
    }


    /**
     * @param string $url
     * @param \OAuthToken $requestToken
     * @param array|null $parameters
     * @return \OAuthToken
     * @throws \Exception
     */
    public function getAccessToken($url, $requestToken, $parameters = null)
    {
        $acc_req = \OAuthRequest::from_consumer_and_token($this->consumer, $requestToken, "GET", $url, $parameters);
        $acc_req->sign_request($this->signer, $this->consumer, $requestToken);

        try {
            /** @var string $response_acc */
            $response_acc = \SimpleSAML\Utils\HTTP::fetch($acc_req->to_url(), [], false);
        } catch (\SimpleSAML\Error\Exception $e) {
            throw new \Exception('Error contacting request_token endpoint on the OAuth Provider');
        }

        \SimpleSAML\Logger::debug('oauth: Reponse to get access token: '.$response_acc);

        $accessResponseParsed = [];
        parse_str($response_acc, $accessResponseParsed);

        if (array_key_exists('error', $accessResponseParsed)) {
            throw new \Exception('Error getting request token: '.$accessResponseParsed['error']);
        }

        $accessToken = $accessResponseParsed['oauth_token'];
        $accessTokenSecret = $accessResponseParsed['oauth_token_secret'];

        return new \OAuthToken($accessToken, $accessTokenSecret);
    }


    /**
     * @param string $url
     * @param \OAuthToken $accessToken
     * @param array $parameters
     * @return array|string
     * @throws \SimpleSAML\Error\Exception
     */
    public function postRequest($url, $accessToken, $parameters)
    {
        $data_req = \OAuthRequest::from_consumer_and_token($this->consumer, $accessToken, "POST", $url, $parameters);
        $data_req->sign_request($this->signer, $this->consumer, $accessToken);
        $postdata = $data_req->to_postdata();

        $opts = [
            'ssl' => [
                'verify_peer' => false,
                'capture_peer_cert' => true,
                'capture_peer_chain' => true
            ],
            'http' => [
                'method' => 'POST',
                'content' => $postdata,
                'header' => 'Content-Type: application/x-www-form-urlencoded',
            ],
        ];

        try {
            $response = \SimpleSAML\Utils\HTTP::fetch($url, $opts);
        } catch (\SimpleSAML\Error\Exception $e) {
            throw new \SimpleSAML\Error\Exception('Failed to push definition file to '.$url);
        }
        return $response;
    }


    /**
     * @param string $url
     * @param \OAuthToken $accessToken
     * @param array $opts
     * @return array|null
     */
    public function getUserInfo($url, $accessToken, $opts = [])
    {
        $data_req = \OAuthRequest::from_consumer_and_token($this->consumer, $accessToken, "GET", $url, null);
        $data_req->sign_request($this->signer, $this->consumer, $accessToken);

        /** @var string $data */
        $data = \SimpleSAML\Utils\HTTP::fetch($data_req->to_url(), $opts);

        return json_decode($data, true);
    }
}
