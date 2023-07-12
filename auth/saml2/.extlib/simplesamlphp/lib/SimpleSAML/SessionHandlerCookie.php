<?php

/**
 * This file is part of SimpleSAMLphp. See the file COPYING in the root of the distribution for licence information.
 *
 * This file defines a base class for session handlers that need to store the session id in a cookie. It takes care of
 * storing and retrieving the session id.
 *
 * @author Olav Morken, UNINETT AS. <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 * @abstract
 */

declare(strict_types=1);

namespace SimpleSAML;

use SimpleSAML\Utils;

abstract class SessionHandlerCookie extends SessionHandler
{
    /**
     * This variable contains the current session id.
     *
     * @var string|null
     */
    private $session_id = null;


    /**
     * This variable contains the session cookie name.
     *
     * @var string
     */
    protected $cookie_name;


    /**
     * This constructor initializes the session id based on what we receive in a cookie. We create a new session id and
     * set a cookie with this id if we don't have a session id.
     */
    protected function __construct()
    {
        // call the constructor in the base class in case it should become necessary in the future
        parent::__construct();

        $config = Configuration::getInstance();
        $this->cookie_name = $config->getString('session.cookie.name', 'SimpleSAMLSessionID');
    }


    /**
     * Create a new session id.
     *
     * @return string The new session id.
     */
    public function newSessionId()
    {
        $this->session_id = self::createSessionID();
        Session::createSession($this->session_id);

        return $this->session_id;
    }


    /**
     * Retrieve the session ID saved in the session cookie, if there's one.
     *
     * @return string|null The session id saved in the cookie or null if no session cookie was set.
     */
    public function getCookieSessionId()
    {
        if ($this->session_id === null) {
            if ($this->hasSessionCookie()) {
                // attempt to retrieve the session id from the cookie
                $this->session_id = $_COOKIE[$this->cookie_name];
            }

            // check if we have a valid session id
            if (!is_null($this->session_id) && !self::isValidSessionID($this->session_id)) {
                // invalid, disregard this session
                return null;
            }
        }

        return $this->session_id;
    }


    /**
     * Retrieve the session cookie name.
     *
     * @return string The session cookie name.
     */
    public function getSessionCookieName()
    {
        return $this->cookie_name;
    }


    /**
     * This static function creates a session id. A session id consists of 32 random hexadecimal characters.
     *
     * @return string A random session id.
     */
    private static function createSessionID(): string
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }


    /**
     * This static function validates a session id. A session id is valid if it only consists of characters which are
     * allowed in a session id and it is the correct length.
     *
     * @param string $session_id The session ID we should validate.
     *
     * @return boolean True if this session ID is valid, false otherwise.
     */
    private static function isValidSessionID(string $session_id): bool
    {

        if (strlen($session_id) != 32) {
            return false;
        }

        if (preg_match('/[^0-9a-f]/', $session_id)) {
            return false;
        }

        return true;
    }


    /**
     * Check whether the session cookie is set.
     *
     * This function will only return false if is is certain that the cookie isn't set.
     *
     * @return boolean True if it was set, false otherwise.
     */
    public function hasSessionCookie()
    {
        return array_key_exists($this->cookie_name, $_COOKIE);
    }


    /**
     * Set a session cookie.
     *
     * @param string $sessionName The name of the session.
     * @param string|null $sessionID The session ID to use. Set to null to delete the cookie.
     * @param array|null $cookieParams Additional parameters to use for the session cookie.
     * @return void
     *
     * @throws \SimpleSAML\Error\CannotSetCookie If we can't set the cookie.
     */
    public function setCookie($sessionName, $sessionID, array $cookieParams = null)
    {
        assert(is_string($sessionName));
        assert(is_string($sessionID) || $sessionID === null);

        if ($cookieParams !== null) {
            $params = array_merge($this->getCookieParams(), $cookieParams);
        } else {
            $params = $this->getCookieParams();
        }

        Utils\HTTP::setCookie($sessionName, $sessionID, $params, true);
    }
}
