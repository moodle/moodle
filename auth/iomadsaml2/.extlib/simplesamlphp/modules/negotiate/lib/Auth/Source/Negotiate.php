<?php

namespace SimpleSAML\Module\negotiate\Auth\Source;

use SimpleSAML\Logger;

/**
 * The Negotiate module. Allows for password-less, secure login by Kerberos and Negotiate.
 *
 * @author Mathias Meisfjordskar, University of Oslo <mathias.meisfjordskar@usit.uio.no>
 * @package SimpleSAMLphp
 */
class Negotiate extends \SimpleSAML\Auth\Source
{
    // Constants used in the module
    const STAGEID = '\SimpleSAML\Module\negotiate\Auth\Source\Negotiate.StageId';
    const AUTHID = '\SimpleSAML\Module\negotiate\Auth\Source\Negotiate.AuthId';

    /** @var \SimpleSAML\Module\ldap\Auth\Ldap|null */
    protected $ldap = null;

    /** @var string */
    protected $backend = '';

    /** @var string*/
    protected $hostname = '';

    /** @var int */
    protected $port = 389;

    /** @var bool */
    protected $referrals = true;

    /** @var bool */
    protected $enableTLS = false;

    /** @var bool */
    protected $debugLDAP = false;

    /** @var int */
    protected $timeout = 30;

    /** @var string */
    protected $keytab = '';

    /** @var string|null */
    protected $spn = null;

    /** @var array */
    protected $base = [];

    /** @var array */
    protected $attr = ['uid'];

    /** @var array|null */
    protected $subnet = null;

    /** @var string|null */
    protected $admin_user = null;

    /** @var string|null */
    protected $admin_pw = null;

    /** @var array|null */
    protected $attributes = null;

    /** @var array */
    protected $binaryAttributes = [];


    /**
     * Constructor for this authentication source.
     *
     * @param array $info Information about this authentication source.
     * @param array $config The configuration of the module
     *
     * @throws Exception If the KRB5 extension is not installed or active.
     */
    public function __construct($info, $config)
    {
        assert(is_array($info));
        assert(is_array($config));

        if (!extension_loaded('krb5')) {
            throw new \Exception('KRB5 Extension not installed');
        }
        // call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        $cfg = \SimpleSAML\Configuration::loadFromArray($config);

        $this->backend = $cfg->getString('fallback');
        $this->hostname = $cfg->getString('hostname');
        $this->port = $cfg->getInteger('port', 389);
        $this->referrals = $cfg->getBoolean('referrals', true);
        $this->enableTLS = $cfg->getBoolean('enable_tls', false);
        $this->debugLDAP = $cfg->getBoolean('debugLDAP', false);
        $this->timeout = $cfg->getInteger('timeout', 30);
        $this->keytab = \SimpleSAML\Utils\Config::getCertPath($cfg->getString('keytab'));
        $this->base = $cfg->getArrayizeString('base');
        $this->attr = $cfg->getArrayizeString('attr', 'uid');
        $this->subnet = $cfg->getArray('subnet', null);
        $this->admin_user = $cfg->getString('adminUser', null);
        $this->admin_pw = $cfg->getString('adminPassword', null);
        $this->attributes = $cfg->getArray('attributes', null);
        $this->binaryAttributes = $cfg->getArray('attributes.binary', []);
        $this->spn = $cfg->getString('spn', null);
    }


    /**
     * The inner workings of the module.
     *
     * Checks to see if client is in the defined subnets (if defined in config). Sends the client a 401 Negotiate and
     * responds to the result. If the client fails to provide a proper Kerberos ticket, the login process is handed over
     * to the 'fallback' module defined in the config.
     *
     * LDAP is used as a user metadata source.
     *
     * @param array &$state Information about the current authentication.
     * @return void
     */
    public function authenticate(&$state)
    {
        assert(is_array($state));

        // set the default backend to config
        $state['LogoutState'] = [
            'negotiate:backend' => $this->backend,
        ];
        $state['negotiate:authId'] = $this->authId;


        // check for disabled SPs. The disable flag is stored in the SP metadata
        if (array_key_exists('SPMetadata', $state) && $this->spDisabledInMetadata($state['SPMetadata'])) {
            $this->fallBack($state);
        }
        /* Go straight to fallback if Negotiate is disabled or if you are sent back to the IdP directly from the SP
        after having logged out. */
        $session = \SimpleSAML\Session::getSessionFromRequest();
        $disabled = $session->getData('negotiate:disable', 'session');

        if (
            $disabled
            || (!empty($_COOKIE['NEGOTIATE_AUTOLOGIN_DISABLE_PERMANENT'])
                && $_COOKIE['NEGOTIATE_AUTOLOGIN_DISABLE_PERMANENT'] === 'true')
        ) {
            Logger::debug('Negotiate - session disabled. falling back');
            $this->fallBack($state);
            // never executed
            assert(false);
        }
        $mask = $this->checkMask();
        if (!$mask) {
            $this->fallBack($state);
            // never executed
            assert(false);
        }

        Logger::debug('Negotiate - authenticate(): looking for Negotiate');
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            Logger::debug('Negotiate - authenticate(): Negotiate found');
            $this->ldap = new \SimpleSAML\Module\ldap\Auth\Ldap(
                $this->hostname,
                $this->enableTLS,
                $this->debugLDAP,
                $this->timeout,
                $this->port,
                $this->referrals
            );

            list($mech,) = explode(' ', $_SERVER['HTTP_AUTHORIZATION'], 2);
            if (strtolower($mech) == 'basic') {
                Logger::debug('Negotiate - authenticate(): Basic found. Skipping.');
            } else {
                if (strtolower($mech) != 'negotiate') {
                    Logger::debug('Negotiate - authenticate(): No "Negotiate" found. Skipping.');
                }
            }

            if ($this->spn === null) {
                // old FQDN behavior
                $auth = new \KRB5NegotiateAuth($this->keytab);
            } elseif ($this->spn === '0') {
                // \KRB5NegotiateAuth constructor expects (long)0 value as second
                // parameter if you need GSS_C_NO_NAME (match any entry) behavior
                $auth = new \KRB5NegotiateAuth($this->keytab, 0);
            } else {
                $auth = new \KRB5NegotiateAuth($this->keytab, $this->spn);
            }

            // attempt Kerberos authentication
            try {
                $reply = $auth->doAuthentication();
            } catch (\Exception $e) {
                Logger::error('Negotiate - authenticate(): doAuthentication() exception: ' . $e->getMessage());
                $reply = null;
            }

            if ($reply) {
                // success! krb TGS received
                $user = $auth->getAuthenticatedUser();
                Logger::info('Negotiate - authenticate(): ' . $user . ' authenticated.');
                $lookup = $this->lookupUserData($user);
                if ($lookup !== null) {
                    $state['Attributes'] = $lookup;
                    // Override the backend so logout will know what to look for
                    $state['LogoutState'] = [
                        'negotiate:backend' => null,
                    ];
                    Logger::info('Negotiate - authenticate(): ' . $user . ' authorized.');
                    \SimpleSAML\Auth\Source::completeAuth($state);
                    // Never reached.
                    assert(false);
                }
            } else {
                // Some error in the received ticket. Expired?
                Logger::info('Negotiate - authenticate(): Kerberos authN failed. Skipping.');
            }
        } else {
            // No auth token. Send it.
            Logger::debug('Negotiate - authenticate(): Sending Negotiate.');
            // Save the $state array, so that we can restore if after a redirect
            Logger::debug('Negotiate - fallback: ' . $state['LogoutState']['negotiate:backend']);
            $id = \SimpleSAML\Auth\State::saveState($state, self::STAGEID);
            $params = ['AuthState' => $id];

            $this->sendNegotiate($params);
            exit;
        }

        Logger::info('Negotiate - authenticate(): Client failed Negotiate. Falling back');
        $this->fallBack($state);
        // The previous function never returns, so this code is never executed
        assert(false);
    }


    /**
     * @param array $spMetadata
     * @return bool
     */
    public function spDisabledInMetadata($spMetadata)
    {
        if (array_key_exists('negotiate:disable', $spMetadata)) {
            if ($spMetadata['negotiate:disable'] == true) {
                Logger::debug('Negotiate - SP disabled. falling back');
                return true;
            } else {
                Logger::debug('Negotiate - SP disable flag found but set to FALSE');
            }
        } else {
            Logger::debug('Negotiate - SP disable flag not found');
        }
        return false;
    }


    /**
     * checkMask() looks up the subnet config option and verifies
     * that the client is within that range.
     *
     * Will return TRUE if no subnet option is configured.
     *
     * @return bool
     */
    public function checkMask()
    {
        // No subnet means all clients are accepted.
        if ($this->subnet === null) {
            return true;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        foreach ($this->subnet as $cidr) {
            $ret = \SimpleSAML\Utils\Net::ipCIDRcheck($cidr);
            if ($ret) {
                Logger::debug('Negotiate: Client "' . $ip . '" matched subnet.');
                return true;
            }
        }
        Logger::debug('Negotiate: Client "' . $ip . '" did not match subnet.');
        return false;
    }


    /**
     * Send the actual headers and body of the 401. Embedded in the body is a post that is triggered by JS if the client
     * wants to show the 401 message.
     *
     * @param array $params additional parameters to the URL in the URL in the body.
     * @return void
     */
    protected function sendNegotiate($params)
    {
        $config = \SimpleSAML\Configuration::getInstance();

        $url = htmlspecialchars(\SimpleSAML\Module::getModuleURL('negotiate/backend.php', $params));

        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Negotiate', false);

        $t = new \SimpleSAML\XHTML\Template($config, 'negotiate:redirect.php');
        $t->data['baseurlpath'] = \SimpleSAML\Module::getModuleURL('negotiate');
        $t->data['url'] = $url;
        $t->show();
    }


    /**
     * Passes control of the login process to a different module.
     *
     * @param array $state Information about the current authentication.
     * @return void
     *
     * @throws \SimpleSAML\Error\Error If couldn't determine the auth source.
     * @throws \SimpleSAML\Error\Exception
     * @throws \Exception
     */
    public static function fallBack(&$state)
    {
        $authId = $state['LogoutState']['negotiate:backend'];

        if ($authId === null) {
            throw new \SimpleSAML\Error\Error([500, "Unable to determine auth source."]);
        }
        /** @var \SimpleSAML\Auth\Source $source */
        $source = \SimpleSAML\Auth\Source::getById($authId);

        try {
            $source->authenticate($state);
        } catch (\SimpleSAML\Error\Exception $e) {
            \SimpleSAML\Auth\State::throwException($state, $e);
        } catch (\Exception $e) {
            $e = new \SimpleSAML\Error\UnserializableException($e);
            \SimpleSAML\Auth\State::throwException($state, $e);
        }
        // fallBack never returns after loginCompleted()
        Logger::debug('Negotiate: backend returned');
        self::loginCompleted($state);
    }


    /**
     * Strips away the realm of the Kerberos identifier, looks up what attributes to fetch from SP metadata and
     * searches the directory.
     *
     * @param string $user The Kerberos user identifier.
     *
     * @return array|null The attributes for the user or NULL if not found.
     */
    protected function lookupUserData($user)
    {
        // Kerberos user names include realm. Strip that away.
        $pos = strpos($user, '@');
        if ($pos === false) {
            return null;
        }
        $uid = substr($user, 0, $pos);

        $this->adminBind();
        try {
            $dn = $this->ldap->searchfordn($this->base, $this->attr, $uid);
            return $this->ldap->getAttributes($dn, $this->attributes, $this->binaryAttributes);
        } catch (\SimpleSAML\Error\Exception $e) {
            Logger::debug('Negotiate - ldap lookup failed: ' . $e);
            return null;
        }
    }


    /**
     * Elevates the LDAP connection to allow restricted lookups if
     * so configured. Does nothing if not.
     *
     * @return void
     * @throws \SimpleSAML\Error\AuthSource
     */
    protected function adminBind()
    {
        if ($this->admin_user === null) {
            // no admin user
            return;
        }
        Logger::debug('Negotiate - authenticate(): Binding as system user ' . var_export($this->admin_user, true));

        if (!$this->ldap->bind($this->admin_user, $this->admin_pw)) {
            $msg = 'Unable to authenticate system user (LDAP_INVALID_CREDENTIALS) '
                . var_export($this->admin_user, true);
            Logger::error('Negotiate - authenticate(): ' . $msg);
            throw new \SimpleSAML\Error\AuthSource('negotiate', $msg);
        }
    }


    /**
     * Log out from this authentication source.
     *
     * This method either logs the user out from Negotiate or passes the
     * logout call to the fallback module.
     *
     * @param array &$state Information about the current logout operation.
     * @return void
     */
    public function logout(&$state)
    {
        assert(is_array($state));
        // get the source that was used to authenticate
        $authId = $state['negotiate:backend'];
        Logger::debug('Negotiate - logout has the following authId: "' . $authId . '"');

        if ($authId === null) {
            $session = \SimpleSAML\Session::getSessionFromRequest();
            $session->setData('negotiate:disable', 'session', true, 24 * 60 * 60);
            parent::logout($state);
        } else {
            /** @var \SimpleSAML\Auth\Source $source */
            $source = \SimpleSAML\Auth\Source::getById($authId);
            $source->logout($state);
        }
    }
}
