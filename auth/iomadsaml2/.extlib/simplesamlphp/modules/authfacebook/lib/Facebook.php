<?php

namespace SimpleSAML\Module\authfacebook;

require_once(dirname(dirname(__FILE__)) . '/extlibinc/base_facebook.php');

/**
 * Extends the BaseFacebook class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */

class Facebook extends \BaseFacebook
{
    const FBSS_COOKIE_NAME = 'fbss';

    // We can set this to a high number because the main session
    // expiration will trump this
    const FBSS_COOKIE_EXPIRE = 31556926; // 1 year

    /**
     * Stores the shared session ID if one is set
     * @var string
     */
    protected $sharedSessionID = '';

    /**
     * SimpleSAMLphp state array
     * @var array
     */
    protected $ssp_state = [];

    /** @var string|null */
    protected $state = null;

    /** @var array */
    protected static $kSupportedKeys = ['state', 'code', 'access_token', 'user_id'];


    /**
     * Identical to the parent constructor, except that
     * we start a PHP session to store the user ID and
     * access token if during the course of execution
     * we discover them.
     *
     * @param array $config the application configuration. Additionally
     * @param array &$ssp_state
     * accepts "sharedSession" as a boolean to turn on a secondary
     * cookie for environments with a shared session (that is, your app
     * shares the domain with other apps).
     * @see BaseFacebook::__construct in base_facebook.php
     */
    public function __construct(array $config, &$ssp_state)
    {
        $this->ssp_state = &$ssp_state;

        parent::__construct($config);
        if (!empty($config['sharedSession'])) {
            $this->initSharedSession();
        }
    }


    /**
     * @return void
     */
    protected function initSharedSession()
    {
        $cookie_name = $this->getSharedSessionCookieName();
        if (isset($_COOKIE[$cookie_name])) {
            $data = $this->parseSignedRequest($_COOKIE[$cookie_name]);
            if (
                !empty($data)
                && !empty($data['domain'])
                && self::isAllowedDomain($this->getHttpHost(), $data['domain'])
            ) {
                // good case
                $this->sharedSessionID = $data['id'];
                return;
            }
            // ignoring potentially unreachable data
        }
        // evil/corrupt/missing case
        $base_domain = $this->getBaseDomain();
        $this->sharedSessionID = md5(uniqid(strval(mt_rand()), true));
        $cookie_value = $this->makeSignedRequest(
            [
                'domain' => $base_domain,
                'id' => $this->sharedSessionID,
            ]
        );
        $_COOKIE[$cookie_name] = $cookie_value;
        if (!headers_sent()) {
            $expire = time() + self::FBSS_COOKIE_EXPIRE;
            setcookie($cookie_name, $cookie_value, $expire, '/', '.' . $base_domain);
        } else {
            // @codeCoverageIgnoreStart
            \SimpleSAML\Logger::debug(
                'Shared session ID cookie could not be set! You must ensure you ' .
                'create the Facebook instance before headers have been sent. This ' .
                'will cause authentication issues after the first request.'
            );
            // @codeCoverageIgnoreEnd
        }
    }


    /**
     * Provides the implementations of the inherited abstract
     * methods.  The implementation uses PHP sessions to maintain
     * a store for authorization codes, user ids, CSRF states, and
     * access tokens.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setPersistentData($key, $value)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            \SimpleSAML\Logger::debug("Unsupported key passed to setPersistentData: " . var_export($key, true));
            return;
        }

        $session_var_name = $this->constructSessionVariableName($key);
        $this->ssp_state[$session_var_name] = $value;
    }


    /**
     * @param string $key
     * @param bool $default
     * @return mixed
     */
    protected function getPersistentData($key, $default = false)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            \SimpleSAML\Logger::debug("Unsupported key passed to getPersistentData: " . var_export($key, true));
            return $default;
        }

        $session_var_name = $this->constructSessionVariableName($key);
        return isset($this->ssp_state[$session_var_name]) ? $this->ssp_state[$session_var_name] : $default;
    }


    /**
     * @param string $key
     * @return void
     */
    protected function clearPersistentData($key)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            \SimpleSAML\Logger::debug("Unsupported key passed to clearPersistentData: " . var_export($key, true));
            return;
        }

        $session_var_name = $this->constructSessionVariableName($key);
        if (isset($this->ssp_state[$session_var_name])) {
            unset($this->ssp_state[$session_var_name]);
        }
    }


    /**
     * @return void
     */
    protected function clearAllPersistentData()
    {
        foreach (self::$kSupportedKeys as $key) {
            $this->clearPersistentData($key);
        }
        if ($this->sharedSessionID) {
            $this->deleteSharedSessionCookie();
        }
    }


    /**
     * @return void
     */
    protected function deleteSharedSessionCookie()
    {
        $cookie_name = $this->getSharedSessionCookieName();
        unset($_COOKIE[$cookie_name]);
        $base_domain = $this->getBaseDomain();
        setcookie($cookie_name, '', 1, '/', '.' . $base_domain);
    }


    /**
     * @return string
     */
    protected function getSharedSessionCookieName()
    {
        return self::FBSS_COOKIE_NAME . '_' . $this->getAppId();
    }


    /**
     * @param string $key
     * @return string
     */
    protected function constructSessionVariableName($key)
    {
        $parts = ['authfacebook:authdata:fb', $this->getAppId(), $key];
        if ($this->sharedSessionID) {
            array_unshift($parts, $this->sharedSessionID);
        }
        return implode('_', $parts);
    }


    /**
     * @return void
     */
    protected function establishCSRFTokenState()
    {
        if ($this->state === null) {
            $this->state = \SimpleSAML\Auth\State::getStateId($this->ssp_state);
            $this->setPersistentData('state', $this->state);
        }
    }
}
