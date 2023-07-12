<?php

declare(strict_types=1);

namespace SimpleSAML\Module\multiauth\Auth\Source;

use Exception;
use SAML2\Constants;
use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Module;
use SimpleSAML\Session;
use SimpleSAML\Utils;
use SimpleSAML\Module\saml\Error\NoAuthnContext;

/**
 * Authentication source which let the user chooses among a list of
 * other authentication sources
 *
 * @author Lorenzo Gil, Yaco Sistemas S.L.
 * @package SimpleSAMLphp
 */
class MultiAuth extends \SimpleSAML\Auth\Source
{
    /**
     * The key of the AuthId field in the state.
     */
    public const AUTHID = '\SimpleSAML\Module\multiauth\Auth\Source\MultiAuth.AuthId';

    /**
     * The string used to identify our states.
     */
    public const STAGEID = '\SimpleSAML\Module\multiauth\Auth\Source\MultiAuth.StageId';

    /**
     * The key where the sources is saved in the state.
     */
    public const SOURCESID = '\SimpleSAML\Module\multiauth\Auth\Source\MultiAuth.SourceId';

    /**
     * The key where the selected source is saved in the session.
     */
    public const SESSION_SOURCE = 'multiauth:selectedSource';

    /**
     * Array of sources we let the user chooses among.
     * @var array
     */
    private $sources;

    /**
     * @var string|null preselect source in filter module configuration
     */
    private $preselect;


    /**
     * Constructor for this authentication source.
     *
     * @param array $info Information about this authentication source.
     * @param array $config Configuration.
     */
    public function __construct($info, $config)
    {
        assert(is_array($info));
        assert(is_array($config));

        // Call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        if (!array_key_exists('sources', $config)) {
            throw new Exception('The required "sources" config option was not found');
        }

        if (array_key_exists('preselect', $config) && is_string($config['preselect'])) {
            if (!array_key_exists($config['preselect'], $config['sources'])) {
                throw new Exception('The optional "preselect" config option must be present in "sources"');
            }

            $this->preselect = $config['preselect'];
        }

        $globalConfiguration = Configuration::getInstance();
        $defaultLanguage = $globalConfiguration->getString('language.default', 'en');
        $authsources = Configuration::getConfig('authsources.php');
        $this->sources = [];

        /** @psalm-var array $sources */
        $sources = $config['sources'];
        foreach ($sources as $source => $info) {
            if (is_int($source)) {
                // Backwards compatibility
                $source = $info;
                $info = [];
            }

            if (array_key_exists('text', $info)) {
                $text = $info['text'];
            } else {
                $text = [$defaultLanguage => $source];
            }

            if (array_key_exists('help', $info)) {
                $help = $info['help'];
            } else {
                $help = null;
            }
            if (array_key_exists('css-class', $info)) {
                $css_class = $info['css-class'];
            } else {
                // Use the authtype as the css class
                $authconfig = $authsources->getArray($source, null);
                if (!array_key_exists(0, $authconfig) || !is_string($authconfig[0])) {
                    $css_class = "";
                } else {
                    $css_class = str_replace(":", "-", $authconfig[0]);
                }
            }

            $class_ref = [];
            if (array_key_exists('AuthnContextClassRef', $info)) {
                $ref = $info['AuthnContextClassRef'];
                if (is_string($ref)) {
                    $class_ref = [$ref];
                } else {
                    $class_ref = $ref;
                }
            }

            $this->sources[] = [
                'source' => $source,
                'text' => $text,
                'help' => $help,
                'css_class' => $css_class,
                'AuthnContextClassRef' => $class_ref,
            ];
        }
    }


    /**
     * Prompt the user with a list of authentication sources.
     *
     * This method saves the information about the configured sources,
     * and redirects to a page where the user must select one of these
     * authentication sources.
     *
     * This method never return. The authentication process is finished
     * in the delegateAuthentication method.
     *
     * @param array &$state Information about the current authentication.
     * @return void
     */
    public function authenticate(&$state)
    {
        assert(is_array($state));

        $state[self::AUTHID] = $this->authId;
        $state[self::SOURCESID] = $this->sources;

        if (!array_key_exists('multiauth:preselect', $state) && is_string($this->preselect)) {
            $state['multiauth:preselect'] = $this->preselect;
        }

        if (
            !is_null($state['saml:RequestedAuthnContext'])
            && array_key_exists('AuthnContextClassRef', $state['saml:RequestedAuthnContext'])
        ) {
            $refs = array_values($state['saml:RequestedAuthnContext']['AuthnContextClassRef']);
            $new_sources = [];
            foreach ($this->sources as $source) {
                if (count(array_intersect($source['AuthnContextClassRef'], $refs)) >= 1) {
                    $new_sources[] = $source;
                }
            }
            $state[self::SOURCESID] = $new_sources;

            $number_of_sources = count($new_sources);
            if ($number_of_sources === 0) {
                throw new NoAuthnContext(
                    Constants::STATUS_RESPONDER,
                    'No authentication sources exist for the requested AuthnContextClassRefs: ' . implode(', ', $refs)
                );
            } else if ($number_of_sources === 1) {
                MultiAuth::delegateAuthentication($new_sources[0]['source'], $state);
            }
        }

        // Save the $state array, so that we can restore if after a redirect
        $id = Auth\State::saveState($state, self::STAGEID);

        /* Redirect to the select source page. We include the identifier of the
         * saved state array as a parameter to the login form
         */
        $url = Module::getModuleURL('multiauth/selectsource.php');
        $params = ['AuthState' => $id];

        // Allows the user to specify the auth source to be used
        if (isset($_GET['source'])) {
            $params['source'] = $_GET['source'];
        }

        Utils\HTTP::redirectTrustedURL($url, $params);

        // The previous function never returns, so this code is never executed
        assert(false);
    }


    /**
     * Delegate authentication.
     *
     * This method is called once the user has choosen one authentication
     * source. It saves the selected authentication source in the session
     * to be able to logout properly. Then it calls the authenticate method
     * on such selected authentication source.
     *
     * @param string $authId Selected authentication source
     * @param array $state Information about the current authentication.
     * @return void
     * @throws \Exception
     */
    public static function delegateAuthentication($authId, $state)
    {
        assert(is_string($authId));
        assert(is_array($state));

        $as = Auth\Source::getById($authId);
        $valid_sources = array_map(
            /**
             * @param array $src
             * @return string
             */
            function ($src) {
                return $src['source'];
            },
            $state[self::SOURCESID]
        );
        if ($as === null || !in_array($authId, $valid_sources, true)) {
            throw new Exception('Invalid authentication source: ' . $authId);
        }

        // Save the selected authentication source for the logout process.
        $session = Session::getSessionFromRequest();
        $session->setData(
            self::SESSION_SOURCE,
            $state[self::AUTHID],
            $authId,
            Session::DATA_TIMEOUT_SESSION_END
        );

        try {
            $as->authenticate($state);
        } catch (Error\Exception $e) {
            Auth\State::throwException($state, $e);
        } catch (Exception $e) {
            $e = new Error\UnserializableException($e);
            Auth\State::throwException($state, $e);
        }
        Auth\Source::completeAuth($state);
    }


    /**
     * Log out from this authentication source.
     *
     * This method retrieves the authentication source used for this
     * session and then call the logout method on it.
     *
     * @param array &$state Information about the current logout operation.
     * @return void
     */
    public function logout(&$state)
    {
        assert(is_array($state));

        // Get the source that was used to authenticate
        $session = Session::getSessionFromRequest();
        $authId = $session->getData(self::SESSION_SOURCE, $this->authId);

        $source = Auth\Source::getById($authId);
        if ($source === null) {
            throw new Exception('Invalid authentication source during logout: ' . $authId);
        }
        // Then, do the logout on it
        $source->logout($state);
    }


    /**
     * Set the previous authentication source.
     *
     * This method remembers the authentication source that the user selected
     * by storing its name in a cookie.
     *
     * @param string $source Name of the authentication source the user selected.
     * @return void
     */
    public function setPreviousSource($source)
    {
        assert(is_string($source));

        $cookieName = 'multiauth_source_' . $this->authId;

        $config = Configuration::getInstance();
        $params = [
            // We save the cookies for 90 days
            'lifetime' => 7776000, //60*60*24*90
            // The base path for cookies. This should be the installation directory for SimpleSAMLphp.
            'path' => $config->getBasePath(),
            'httponly' => false,
        ];

        Utils\HTTP::setCookie($cookieName, $source, $params, false);
    }


    /**
     * Get the previous authentication source.
     *
     * This method retrieves the authentication source that the user selected
     * last time or NULL if this is the first time or remembering is disabled.
     * @return string|null
     */
    public function getPreviousSource()
    {
        $cookieName = 'multiauth_source_' . $this->authId;
        if (array_key_exists($cookieName, $_COOKIE)) {
            return $_COOKIE[$cookieName];
        } else {
            return null;
        }
    }
}
