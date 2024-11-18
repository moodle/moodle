<?php

declare(strict_types=1);

namespace SimpleSAML;

use SAML2\Constants as SAML2;
use SimpleSAML\Auth;
use SimpleSAML\Error;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Module\saml\Error\NoPassive;
use SimpleSAML\Utils;

/**
 * IdP class.
 *
 * This class implements the various functions used by IdP.
 *
 * @package SimpleSAMLphp
 */

class IdP
{
    /**
     * A cache for resolving IdP id's.
     *
     * @var array
     */
    private static $idpCache = [];

    /**
     * The identifier for this IdP.
     *
     * @var string
     */
    private $id;

    /**
     * The "association group" for this IdP.
     *
     * We use this to support cross-protocol logout until
     * we implement a cross-protocol IdP.
     *
     * @var string
     */
    private $associationGroup;

    /**
     * The configuration for this IdP.
     *
     * @var Configuration
     */
    private $config;

    /**
     * Our authsource.
     *
     * @var Auth\Simple
     */
    private $authSource;

    /**
     * Initialize an IdP.
     *
     * @param string $id The identifier of this IdP.
     *
     * @throws \SimpleSAML\Error\Exception If the IdP is disabled or no such auth source was found.
     */
    private function __construct(string $id)
    {
        $this->id = $id;
        $this->associationGroup = $id;

        $metadata = MetaDataStorageHandler::getMetadataHandler();
        $globalConfig = Configuration::getInstance();

        if (substr($id, 0, 6) === 'saml2:') {
            if (!$globalConfig->getBoolean('enable.saml20-idp', false)) {
                throw new Error\Exception('enable.saml20-idp disabled in config.php.');
            }
            $this->config = $metadata->getMetaDataConfig(substr($id, 6), 'saml20-idp-hosted');
        } elseif (substr($id, 0, 6) === 'saml1:') {
            if (!$globalConfig->getBoolean('enable.shib13-idp', false)) {
                throw new Error\Exception('enable.shib13-idp disabled in config.php.');
            }
            $this->config = $metadata->getMetaDataConfig(substr($id, 6), 'shib13-idp-hosted');
        } elseif (substr($id, 0, 5) === 'adfs:') {
            if (!$globalConfig->getBoolean('enable.adfs-idp', false)) {
                throw new Error\Exception('enable.adfs-idp disabled in config.php.');
            }
            $this->config = $metadata->getMetaDataConfig(substr($id, 5), 'adfs-idp-hosted');

            try {
                // this makes the ADFS IdP use the same SP associations as the SAML 2.0 IdP
                $saml2EntityId = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
                $this->associationGroup = 'saml2:' . $saml2EntityId;
            } catch (\Exception $e) {
                // probably no SAML 2 IdP configured for this host. Ignore the error
            }
        } else {
            throw new \Exception("Protocol not implemented.");
        }

        $auth = $this->config->getString('auth');
        if (Auth\Source::getById($auth) !== null) {
            $this->authSource = new Auth\Simple($auth);
        } else {
            throw new Error\Exception('No such "' . $auth . '" auth source found.');
        }
    }


    /**
     * Retrieve the ID of this IdP.
     *
     * @return string The ID of this IdP.
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Retrieve an IdP by ID.
     *
     * @param string $id The identifier of the IdP.
     *
     * @return IdP The IdP.
     */
    public static function getById($id)
    {
        assert(is_string($id));

        if (isset(self::$idpCache[$id])) {
            return self::$idpCache[$id];
        }

        $idp = new self($id);
        self::$idpCache[$id] = $idp;
        return $idp;
    }


    /**
     * Retrieve the IdP "owning" the state.
     *
     * @param array &$state The state array.
     *
     * @return IdP The IdP.
     */
    public static function getByState(array &$state)
    {
        assert(isset($state['core:IdP']));

        return self::getById($state['core:IdP']);
    }


    /**
     * Retrieve the configuration for this IdP.
     *
     * @return Configuration The configuration object.
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * Get SP name.
     *
     * @param string $assocId The association identifier.
     *
     * @return array|null The name of the SP, as an associative array of language => text, or null if this isn't an SP.
     */
    public function getSPName($assocId)
    {
        assert(is_string($assocId));

        $prefix = substr($assocId, 0, 4);
        $spEntityId = substr($assocId, strlen($prefix) + 1);
        $metadata = MetaDataStorageHandler::getMetadataHandler();

        if ($prefix === 'saml') {
            try {
                $spMetadata = $metadata->getMetaDataConfig($spEntityId, 'saml20-sp-remote');
            } catch (\Exception $e) {
                try {
                    $spMetadata = $metadata->getMetaDataConfig($spEntityId, 'shib13-sp-remote');
                } catch (\Exception $e) {
                    return null;
                }
            }
        } else {
            if ($prefix === 'adfs') {
                $spMetadata = $metadata->getMetaDataConfig($spEntityId, 'adfs-sp-remote');
            } else {
                return null;
            }
        }

        if ($spMetadata->hasValue('name')) {
            return $spMetadata->getLocalizedString('name');
        } elseif ($spMetadata->hasValue('OrganizationDisplayName')) {
            return $spMetadata->getLocalizedString('OrganizationDisplayName');
        } else {
            return ['en' => $spEntityId];
        }
    }


    /**
     * Add an SP association.
     *
     * @param array $association The SP association.
     * @return void
     */
    public function addAssociation(array $association)
    {
        assert(isset($association['id']));
        assert(isset($association['Handler']));

        $association['core:IdP'] = $this->id;

        $session = Session::getSessionFromRequest();
        $session->addAssociation($this->associationGroup, $association);
    }


    /**
     * Retrieve list of SP associations.
     *
     * @return array List of SP associations.
     */
    public function getAssociations()
    {
        $session = Session::getSessionFromRequest();
        return $session->getAssociations($this->associationGroup);
    }


    /**
     * Remove an SP association.
     *
     * @param string $assocId The association id.
     * @return void
     */
    public function terminateAssociation($assocId)
    {
        assert(is_string($assocId));

        $session = Session::getSessionFromRequest();
        $session->terminateAssociation($this->associationGroup, $assocId);
    }


    /**
     * Is the current user authenticated?
     *
     * @return boolean True if the user is authenticated, false otherwise.
     */
    public function isAuthenticated()
    {
        return $this->authSource->isAuthenticated();
    }


    /**
     * Called after authproc has run.
     *
     * @param array $state The authentication request state array.
     * @return void
     */
    public static function postAuthProc(array $state)
    {
        assert(is_callable($state['Responder']));

        if (isset($state['core:SP'])) {
            $session = Session::getSessionFromRequest();
            $session->setData(
                'core:idp-ssotime',
                $state['core:IdP'] . ';' . $state['core:SP'],
                time(),
                Session::DATA_TIMEOUT_SESSION_END
            );
        }

        call_user_func($state['Responder'], $state);
        assert(false);
    }


    /**
     * The user is authenticated.
     *
     * @param array $state The authentication request state array.
     *
     * @throws \SimpleSAML\Error\Exception If we are not authenticated.
     * @return void
     */
    public static function postAuth(array $state)
    {
        $idp = IdP::getByState($state);

        if (!$idp->isAuthenticated()) {
            throw new Error\Exception('Not authenticated.');
        }

        $state['Attributes'] = $idp->authSource->getAttributes();

        if (isset($state['SPMetadata'])) {
            $spMetadata = $state['SPMetadata'];
        } else {
            $spMetadata = [];
        }

        if (isset($state['core:SP'])) {
            $session = Session::getSessionFromRequest();
            $previousSSOTime = $session->getData('core:idp-ssotime', $state['core:IdP'] . ';' . $state['core:SP']);
            if ($previousSSOTime !== null) {
                $state['PreviousSSOTimestamp'] = $previousSSOTime;
            }
        }

        $idpMetadata = $idp->getConfig()->toArray();

        $pc = new Auth\ProcessingChain($idpMetadata, $spMetadata, 'idp');

        $state['ReturnCall'] = ['\SimpleSAML\IdP', 'postAuthProc'];
        $state['Destination'] = $spMetadata;
        $state['Source'] = $idpMetadata;

        $pc->processState($state);

        self::postAuthProc($state);
    }


    /**
     * Authenticate the user.
     *
     * This function authenticates the user.
     *
     * @param array &$state The authentication request state.
     *
     * @throws \SimpleSAML\Module\saml\Error\NoPassive If we were asked to do passive authentication.
     * @return void
     */
    private function authenticate(array &$state): void
    {
        if (isset($state['isPassive']) && (bool) $state['isPassive']) {
            throw new NoPassive(SAML2::STATUS_RESPONDER, 'Passive authentication not supported.');
        }

        $this->authSource->login($state);
    }


    /**
     * Re-authenticate the user.
     *
     * This function re-authenticates an user with an existing session. This gives the authentication source a chance
     * to do additional work when re-authenticating for SSO.
     *
     * Note: This function is not used when ForceAuthn=true.
     *
     * @param array &$state The authentication request state.
     *
     * @throws \Exception If there is no auth source defined for this IdP.
     * @return void
     */
    private function reauthenticate(array &$state): void
    {
        $sourceImpl = $this->authSource->getAuthSource();
        $sourceImpl->reauthenticate($state);
    }


    /**
     * Process authentication requests.
     *
     * @param array &$state The authentication request state.
     * @return void
     */
    public function handleAuthenticationRequest(array &$state)
    {
        assert(isset($state['Responder']));

        $state['core:IdP'] = $this->id;

        if (isset($state['SPMetadata']['entityid'])) {
            $spEntityId = $state['SPMetadata']['entityid'];
        } elseif (isset($state['SPMetadata']['entityID'])) {
            $spEntityId = $state['SPMetadata']['entityID'];
        } else {
            $spEntityId = null;
        }
        $state['core:SP'] = $spEntityId;

        // first, check whether we need to authenticate the user
        if (isset($state['ForceAuthn']) && (bool) $state['ForceAuthn']) {
            // force authentication is in effect
            $needAuth = true;
        } else {
            $needAuth = !$this->isAuthenticated();
        }

        $state['IdPMetadata'] = $this->getConfig()->toArray();
        $state['ReturnCallback'] = ['\SimpleSAML\IdP', 'postAuth'];

        try {
            if ($needAuth) {
                $this->authenticate($state);
                assert(false);
            } else {
                $this->reauthenticate($state);
            }
            $this->postAuth($state);
        } catch (Error\Exception $e) {
            Auth\State::throwException($state, $e);
        } catch (\Exception $e) {
            $e = new Error\UnserializableException($e);
            Auth\State::throwException($state, $e);
        }
    }


    /**
     * Find the logout handler of this IdP.
     *
     * @return IdP\LogoutHandlerInterface The logout handler class.
     * @throws \Exception If we cannot find a logout handler.
     */
    public function getLogoutHandler()
    {
        // find the logout handler
        $logouttype = $this->getConfig()->getString('logouttype', 'traditional');
        switch ($logouttype) {
            case 'traditional':
                $handler = '\SimpleSAML\IdP\TraditionalLogoutHandler';
                break;
            case 'iframe':
                $handler = '\SimpleSAML\IdP\IFrameLogoutHandler';
                break;
            default:
                throw new Error\Exception('Unknown logout handler: ' . var_export($logouttype, true));
        }

        /** @var IdP\LogoutHandlerInterface */
        return new $handler($this);
    }


    /**
     * Finish the logout operation.
     *
     * This function will never return.
     *
     * @param array &$state The logout request state.
     * @return void
     */
    public function finishLogout(array &$state)
    {
        assert(isset($state['Responder']));

        $idp = IdP::getByState($state);
        call_user_func($state['Responder'], $idp, $state);
        assert(false);
    }


    /**
     * Process a logout request.
     *
     * This function will never return.
     *
     * @param array       &$state The logout request state.
     * @param string|null $assocId The association we received the logout request from, or null if there was no
     * association.
     * @return void
     */
    public function handleLogoutRequest(array &$state, $assocId)
    {
        assert(isset($state['Responder']));
        assert(is_string($assocId) || $assocId === null);

        $state['core:IdP'] = $this->id;
        $state['core:TerminatedAssocId'] = $assocId;

        if ($assocId !== null) {
            $this->terminateAssociation($assocId);
            $session = Session::getSessionFromRequest();
            $session->deleteData('core:idp-ssotime', $this->id . ';' . $state['saml:SPEntityId']);
        }

        // terminate the local session
        $id = Auth\State::saveState($state, 'core:Logout:afterbridge');
        $returnTo = Module::getModuleURL('core/idp/resumelogout.php', ['id' => $id]);

        $this->authSource->logout($returnTo);

        if ($assocId !== null) {
            $handler = $this->getLogoutHandler();
            $handler->startLogout($state, $assocId);
        }
        assert(false);
    }


    /**
     * Process a logout response.
     *
     * This function will never return.
     *
     * @param string                 $assocId The association that is terminated.
     * @param string|null            $relayState The RelayState from the start of the logout.
     * @param \SimpleSAML\Error\Exception|null $error  The error that occurred during session termination (if any).
     * @return void
     */
    public function handleLogoutResponse($assocId, $relayState, Error\Exception $error = null)
    {
        assert(is_string($assocId));
        assert(is_string($relayState) || $relayState === null);

        $index = strpos($assocId, ':');
        assert(is_int($index));

        $session = Session::getSessionFromRequest();
        $session->deleteData('core:idp-ssotime', $this->id . ';' . substr($assocId, $index + 1));

        $handler = $this->getLogoutHandler();
        $handler->onResponse($assocId, $relayState, $error);

        assert(false);
    }


    /**
     * Log out, then redirect to a URL.
     *
     * This function never returns.
     *
     * @param string $url The URL the user should be returned to after logout.
     * @return void
     */
    public function doLogoutRedirect($url)
    {
        assert(is_string($url));

        $state = [
            'Responder'       => ['\SimpleSAML\IdP', 'finishLogoutRedirect'],
            'core:Logout:URL' => $url,
        ];

        $this->handleLogoutRequest($state, null);
        assert(false);
    }


    /**
     * Redirect to a URL after logout.
     *
     * This function never returns.
     *
     * @param IdP      $idp Deprecated. Will be removed.
     * @param array    &$state The logout state from doLogoutRedirect().
     * @return void
     */
    public static function finishLogoutRedirect(IdP $idp, array $state)
    {
        assert(isset($state['core:Logout:URL']));

        Utils\HTTP::redirectTrustedURL($state['core:Logout:URL']);
        assert(false);
    }
}
