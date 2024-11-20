<?php

namespace SimpleSAML\Module\consent\Consent\Store;

/**
 * Cookie storage for consent
 *
 * This class implements a consent store which stores the consent information in cookies on the users computer.
 *
 * Example - Consent module with cookie store:
 *
 * <code>
 * 'authproc' => array(
 *   array(
 *     'consent:Consent',
 *     'store' => 'consent:Cookie',
 *     ),
 *   ),
 * </code>
 *
 * @author  Olav Morken <olav.morken@uninett.no>
 * @package SimpleSAMLphp
 */

class Cookie extends \SimpleSAML\Module\consent\Store
{
    /**
     * @var string Cookie name prefix
     */
    private $name;

    /**
     * @var int Cookie lifetime
     */
    private $lifetime;

    /**
     * @var string Cookie path
     */
    private $path;

    /**
     * @var string Cookie domain
     */
    private $domain = '';

    /**
     * @var bool Cookie secure flag
     */
    private $secure;

    /**
     * @var string|null Cookie samesite flag
     */
    private $samesite = null;

    /**
     * Parse configuration.
     *
     * This constructor parses the configuration.
     *
     * @param array $config Configuration for database consent store.
     *
     * @throws \Exception in case of a configuration error.
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        if (array_key_exists('name', $config)) {
            $this->name = $config['name'];
        } else {
            $this->name = '\SimpleSAML\Module\consent';
        }

        if (array_key_exists('lifetime', $config)) {
            $this->lifetime = (int) $config['lifetime'];
        } else {
            $this->lifetime = 7776000; // (90*24*60*60)
        }

        if (array_key_exists('path', $config)) {
            $this->path = $config['path'];
        } else {
            $globalConfig = \SimpleSAML\Configuration::getInstance();
            $this->path = $globalConfig->getBasePath();
        }

        if (array_key_exists('domain', $config)) {
            $this->domain = $config['domain'];
        }

        if (array_key_exists('secure', $config)) {
            $this->secure = (bool) $config['secure'];
        } else {
            $this->secure = \SimpleSAML\Utils\HTTP::isHTTPS();
        }

        if (array_key_exists('samesite', $config)) {
            $this->samesite = $config['samesite'];
        }
    }

    /**
     * Check for consent.
     *
     * This function checks whether a given user has authorized the release of the attributes identified by
     * $attributeSet from $source to $destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     * @param string $attributeSet  A hash which identifies the attributes.
     *
     * @return bool True if the user has given consent earlier, false if not (or on error).
     */
    public function hasConsent($userId, $destinationId, $attributeSet)
    {
        assert(is_string($userId));
        assert(is_string($destinationId));
        assert(is_string($attributeSet));

        $cookieName = $this->getCookieName($userId, $destinationId);

        $data = $userId.':'.$attributeSet.':'.$destinationId;

        \SimpleSAML\Logger::debug('Consent cookie - Get ['.$data.']');

        if (!array_key_exists($cookieName, $_COOKIE)) {
            \SimpleSAML\Logger::debug(
                'Consent cookie - no cookie with name \''.$cookieName.'\'.'
            );
            return false;
        }
        if (!is_string($_COOKIE[$cookieName])) {
            \SimpleSAML\Logger::warning(
                'Value of consent cookie wasn\'t a string. Was: '.
                var_export($_COOKIE[$cookieName], true)
            );
            return false;
        }

        $data = self::sign($data);

        if ($_COOKIE[$cookieName] !== $data) {
            \SimpleSAML\Logger::info(
                'Attribute set changed from the last time consent was given.'
            );
            return false;
        }

        \SimpleSAML\Logger::debug(
            'Consent cookie - found cookie with correct name and value.'
        );

        return true;
    }


    /**
     * Save consent.
     *
     * Called when the user asks for the consent to be saved. If consent information for the given user and destination
     * already exists, it should be overwritten.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     * @param string $attributeSet  A hash which identifies the attributes.
     *
     * @return bool
     */
    public function saveConsent($userId, $destinationId, $attributeSet)
    {
        assert(is_string($userId));
        assert(is_string($destinationId));
        assert(is_string($attributeSet));

        $name = $this->getCookieName($userId, $destinationId);
        $value = $userId.':'.$attributeSet.':'.$destinationId;

        \SimpleSAML\Logger::debug('Consent cookie - Set ['.$value.']');

        $value = self::sign($value);
        return $this->setConsentCookie($name, $value);
    }


    /**
     * Delete consent.
     *
     * Called when a user revokes consent for a given destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     *
     * @return void
     */
    public function deleteConsent($userId, $destinationId)
    {
        assert(is_string($userId));
        assert(is_string($destinationId));

        $name = $this->getCookieName($userId, $destinationId);
        $this->setConsentCookie($name, null);
    }


    /**
     * Delete consent.
     *
     * @param string $userId The hash identifying the user at an IdP.
     *
     * @return void This method does not return.
     *
     * @throws \Exception This method always throws an exception indicating that it is not possible to delete all given
     * consents with this handler.
     */
    public function deleteAllConsents($userId)
    {
        assert(is_string($userId));

        throw new \Exception(
            'The cookie consent handler does not support delete of all consents...'
        );
    }


    /**
     * Retrieve consents.
     *
     * This function should return a list of consents the user has saved.
     *
     * @param string $userId The hash identifying the user at an IdP.
     *
     * @return array Array of all destination ids the user has given consent for.
     */
    public function getConsents($userId)
    {
        assert(is_string($userId));

        $ret = [];

        $cookieNameStart = $this->name . ':';
        $cookieNameStartLen = strlen($cookieNameStart);
        foreach ($_COOKIE as $name => $value) {
            if (substr($name, 0, $cookieNameStartLen) !== $cookieNameStart) {
                continue;
            }

            $value = self::verify($value);
            if ($value === false) {
                continue;
            }

            $tmp = explode(':', $value, 3);
            if (count($tmp) !== 3) {
                \SimpleSAML\Logger::warning(
                    'Consent cookie with invalid value: '.$value
                );
                continue;
            }

            if ($userId !== $tmp[0]) {
                // Wrong user
                continue;
            }

            $destination = $tmp[2];
            $ret[] = $destination;
        }

        return $ret;
    }


    /**
     * Calculate a signature of some data.
     *
     * This function calculates a signature of the data.
     *
     * @param string $data The data which should be signed.
     *
     * @return string The signed data.
     */
    private static function sign($data)
    {
        assert(is_string($data));

        $secretSalt = \SimpleSAML\Utils\Config::getSecretSalt();

        return sha1($secretSalt.$data.$secretSalt).':'.$data;
    }


    /**
     * Verify signed data.
     *
     * This function verifies signed data.
     *
     * @param string $signedData The data which is signed.
     *
     * @return string|false The data, or false if the signature is invalid.
     */
    private static function verify($signedData)
    {
        assert(is_string($signedData));

        $data = explode(':', $signedData, 2);
        if (count($data) !== 2) {
            \SimpleSAML\Logger::warning('Consent cookie: Missing signature.');
            return false;
        }
        $data = $data[1];

        $newSignedData = self::sign($data);
        if ($newSignedData !== $signedData) {
            \SimpleSAML\Logger::warning('Consent cookie: Invalid signature.');
            return false;
        }

        return $data;
    }


    /**
     * Get cookie name.
     *
     * This function gets the cookie name for the given user & destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     *
     * @return string The cookie name
     */
    private function getCookieName($userId, $destinationId)
    {
        assert(is_string($userId));
        assert(is_string($destinationId));

        return $this->name . ':' . sha1($userId . ':' . $destinationId);
    }


    /**
     * Helper function for setting a cookie.
     *
     * @param string      $name  Name of the cookie.
     * @param string|null $value Value of the cookie. Set this to null to delete the cookie.
     *
     * @return bool
     */
    private function setConsentCookie($name, $value)
    {
        assert(is_string($name));
        assert(is_string($value) || is_null($value));

        $globalConfig = \SimpleSAML\Configuration::getInstance();
	$params = [
            'lifetime' => $this->lifetime,
            'path' => $this->path,
            'domain' => $this->domain,
            'httponly' => true,
            'secure' => $this->secure,
            'samesite' => $this->samesite,
        ];

        try {
            \SimpleSAML\Utils\HTTP::setCookie($name, $value, $params, false);
            return true;
        } catch (\SimpleSAML\Error\CannotSetCookie $e) {
            return false;
        }
    }
}
