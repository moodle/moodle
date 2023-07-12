<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml\IdP;

use Exception;
use DOMNodeList;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use SAML2\Assertion;
use SAML2\AuthnRequest;
use SAML2\Binding;
use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\EncryptedAssertion;
use SAML2\HTTPRedirect;
use SAML2\LogoutRequest;
use SAML2\LogoutResponse;
use SAML2\Response;
use SAML2\SOAP;
use SAML2\XML\ds\X509Certificate;
use SAML2\XML\ds\X509Data;
use SAML2\XML\ds\KeyInfo;
use SAML2\XML\saml\AttributeValue;
use SAML2\XML\saml\Issuer;
use SAML2\XML\saml\NameID;
use SAML2\XML\saml\SubjectConfirmation;
use SAML2\XML\saml\SubjectConfirmationData;
use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\IdP;
use SimpleSAML\Logger;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Module;
use SimpleSAML\Stats;
use SimpleSAML\Utils;

/**
 * IdP implementation for SAML 2.0 protocol.
 *
 * @package SimpleSAMLphp
 */
class SAML2
{
    /**
     * Send a response to the SP.
     *
     * @param array $state The authentication state.
     * @return void
     */
    public static function sendResponse(array $state)
    {
        assert(isset($state['Attributes']));
        assert(isset($state['SPMetadata']));
        assert(isset($state['saml:ConsumerURL']));
        assert(array_key_exists('saml:RequestId', $state)); // Can be NULL
        assert(array_key_exists('saml:RelayState', $state)); // Can be NULL.

        $spMetadata = $state["SPMetadata"];
        $spEntityId = $spMetadata['entityid'];
        $spMetadata = Configuration::loadFromArray(
            $spMetadata,
            '$metadata[' . var_export($spEntityId, true) . ']'
        );

        Logger::info('Sending SAML 2.0 Response to ' . var_export($spEntityId, true));

        $requestId = $state['saml:RequestId'];
        $relayState = $state['saml:RelayState'];
        $consumerURL = $state['saml:ConsumerURL'];
        $protocolBinding = $state['saml:Binding'];

        $idp = IdP::getByState($state);

        $idpMetadata = $idp->getConfig();

        $assertion = self::buildAssertion($idpMetadata, $spMetadata, $state);

        if (isset($state['saml:AuthenticatingAuthority'])) {
            $assertion->setAuthenticatingAuthority($state['saml:AuthenticatingAuthority']);
        }

        // create the session association (for logout)
        $association = [
            'id'                => 'saml:' . $spEntityId,
            'Handler'           => '\SimpleSAML\Module\saml\IdP\SAML2',
            'Expires'           => $assertion->getSessionNotOnOrAfter(),
            'saml:entityID'     => $spEntityId,
            'saml:NameID'       => $state['saml:idp:NameID'],
            'saml:SessionIndex' => $assertion->getSessionIndex(),
        ];

        // maybe encrypt the assertion
        $assertion = self::encryptAssertion($idpMetadata, $spMetadata, $assertion);

        // create the response
        $ar = self::buildResponse($idpMetadata, $spMetadata, $consumerURL);
        $ar->setInResponseTo($requestId);
        $ar->setRelayState($relayState);
        $ar->setAssertions([$assertion]);

        // register the session association with the IdP
        $idp->addAssociation($association);

        $statsData = [
            'spEntityID'  => $spEntityId,
            'idpEntityID' => $idpMetadata->getString('entityid'),
            'protocol'    => 'saml2',
        ];
        if (isset($state['saml:AuthnRequestReceivedAt'])) {
            $statsData['logintime'] = microtime(true) - $state['saml:AuthnRequestReceivedAt'];
        }
        Stats::log('saml:idp:Response', $statsData);

        // send the response
        $binding = Binding::getBinding($protocolBinding);
        $binding->send($ar);
    }


    /**
     * Handle authentication error.
     *
     * \SimpleSAML\Error\Exception $exception  The exception.
     *
     * @param array $state The error state.
     * @return void
     */
    public static function handleAuthError(Error\Exception $exception, array $state)
    {
        assert(isset($state['SPMetadata']));
        assert(isset($state['saml:ConsumerURL']));
        assert(array_key_exists('saml:RequestId', $state)); // Can be NULL.
        assert(array_key_exists('saml:RelayState', $state)); // Can be NULL.

        $spMetadata = $state["SPMetadata"];
        $spEntityId = $spMetadata['entityid'];
        $spMetadata = Configuration::loadFromArray(
            $spMetadata,
            '$metadata[' . var_export($spEntityId, true) . ']'
        );

        $requestId = $state['saml:RequestId'];
        $relayState = $state['saml:RelayState'];
        $consumerURL = $state['saml:ConsumerURL'];
        $protocolBinding = $state['saml:Binding'];

        $idp = IdP::getByState($state);

        $idpMetadata = $idp->getConfig();

        $error = \SimpleSAML\Module\saml\Error::fromException($exception);

        Logger::warning("Returning error to SP with entity ID '" . var_export($spEntityId, true) . "'.");
        $exception->log(Logger::WARNING);

        $ar = self::buildResponse($idpMetadata, $spMetadata, $consumerURL);
        $ar->setInResponseTo($requestId);
        $ar->setRelayState($relayState);

        $status = [
            'Code'    => $error->getStatus(),
            'SubCode' => $error->getSubStatus(),
            'Message' => $error->getStatusMessage(),
        ];
        $ar->setStatus($status);

        $statsData = [
            'spEntityID'  => $spEntityId,
            'idpEntityID' => $idpMetadata->getString('entityid'),
            'protocol'    => 'saml2',
            'error'       => $status,
        ];
        if (isset($state['saml:AuthnRequestReceivedAt'])) {
            $statsData['logintime'] = microtime(true) - $state['saml:AuthnRequestReceivedAt'];
        }
        Stats::log('saml:idp:Response:error', $statsData);

        $binding = Binding::getBinding($protocolBinding);
        $binding->send($ar);
    }


    /**
     * Find SP AssertionConsumerService based on parameter in AuthnRequest.
     *
     * @param array                     $supportedBindings The bindings we allow for the response.
     * @param \SimpleSAML\Configuration $spMetadata The metadata for the SP.
     * @param string|null               $AssertionConsumerServiceURL AssertionConsumerServiceURL from request.
     * @param string|null               $ProtocolBinding ProtocolBinding from request.
     * @param int|null                  $AssertionConsumerServiceIndex AssertionConsumerServiceIndex from request.
     *
     * @return array|null  Array with the Location and Binding we should use for the response.
     */
    private static function getAssertionConsumerService(
        array $supportedBindings,
        Configuration $spMetadata,
        string $AssertionConsumerServiceURL = null,
        string $ProtocolBinding = null,
        int $AssertionConsumerServiceIndex = null
    ): ?array {
        /* We want to pick the best matching endpoint in the case where for example
         * only the ProtocolBinding is given. We therefore pick endpoints with the
         * following priority:
         *  1. isDefault="true"
         *  2. isDefault unset
         *  3. isDefault="false"
         */
        $firstNotFalse = null;
        $firstFalse = null;
        foreach ($spMetadata->getEndpoints('AssertionConsumerService') as $ep) {
            if ($AssertionConsumerServiceURL !== null && $ep['Location'] !== $AssertionConsumerServiceURL) {
                continue;
            }
            if ($ProtocolBinding !== null && $ep['Binding'] !== $ProtocolBinding) {
                continue;
            }
            if ($AssertionConsumerServiceIndex !== null && $ep['index'] !== $AssertionConsumerServiceIndex) {
                continue;
            }

            if (!in_array($ep['Binding'], $supportedBindings, true)) {
                /* The endpoint has an unsupported binding. */
                continue;
            }

            // we have an endpoint that matches all our requirements. Check if it is the best one

            if (array_key_exists('isDefault', $ep)) {
                if ($ep['isDefault'] === true) {
                    // this is the first matching endpoint with isDefault set to true
                    return $ep;
                }
                // isDefault is set to FALSE, but the endpoint is still usable
                if ($firstFalse === null) {
                    // this is the first endpoint that we can use
                    $firstFalse = $ep;
                }
            } else {
                if ($firstNotFalse === null) {
                    // this is the first endpoint without isDefault set
                    $firstNotFalse = $ep;
                }
            }
        }

        if ($firstNotFalse !== null) {
            return $firstNotFalse;
        } elseif ($firstFalse !== null) {
            return $firstFalse;
        }

        Logger::warning('Authentication request specifies invalid AssertionConsumerService:');
        if ($AssertionConsumerServiceURL !== null) {
            Logger::warning('AssertionConsumerServiceURL: ' . var_export($AssertionConsumerServiceURL, true));
        }
        if ($ProtocolBinding !== null) {
            Logger::warning('ProtocolBinding: ' . var_export($ProtocolBinding, true));
        }
        if ($AssertionConsumerServiceIndex !== null) {
            Logger::warning(
                'AssertionConsumerServiceIndex: ' . var_export($AssertionConsumerServiceIndex, true)
            );
        }

        // we have no good endpoints. Our last resort is to just use the default endpoint
        return $spMetadata->getDefaultEndpoint('AssertionConsumerService', $supportedBindings);
    }


    /**
     * Receive an authentication request.
     *
     * @param \SimpleSAML\IdP $idp The IdP we are receiving it for.
     * @return void
     * @throws \SimpleSAML\Error\BadRequest In case an error occurs when trying to receive the request.
     */
    public static function receiveAuthnRequest(\SimpleSAML\IdP $idp)
    {
        $metadata = MetaDataStorageHandler::getMetadataHandler();
        $idpMetadata = $idp->getConfig();

        $supportedBindings = [Constants::BINDING_HTTP_POST];
        if ($idpMetadata->getBoolean('saml20.sendartifact', false)) {
            $supportedBindings[] = Constants::BINDING_HTTP_ARTIFACT;
        }
        if ($idpMetadata->getBoolean('saml20.hok.assertion', false)) {
            $supportedBindings[] = Constants::BINDING_HOK_SSO;
        }
        if ($idpMetadata->getBoolean('saml20.ecp', false)) {
            $supportedBindings[] = Constants::BINDING_PAOS;
        }

        if (isset($_REQUEST['spentityid']) || isset($_REQUEST['providerId'])) {
            /* IdP initiated authentication. */

            if (isset($_REQUEST['cookieTime'])) {
                $cookieTime = (int) $_REQUEST['cookieTime'];
                if ($cookieTime + 5 > time()) {
                    /*
                     * Less than five seconds has passed since we were
                     * here the last time. Cookies are probably disabled.
                     */
                    Utils\HTTP::checkSessionCookie(Utils\HTTP::getSelfURL());
                }
            }

            $spEntityId = (string) isset($_REQUEST['spentityid']) ? $_REQUEST['spentityid'] : $_REQUEST['providerId'];
            $spMetadata = $metadata->getMetaDataConfig($spEntityId, 'saml20-sp-remote');

            if (isset($_REQUEST['RelayState'])) {
                $relayState = (string) $_REQUEST['RelayState'];
            } elseif (isset($_REQUEST['target'])) {
                $relayState = (string) $_REQUEST['target'];
            } else {
                $relayState = null;
            }

            if (isset($_REQUEST['binding'])) {
                $protocolBinding = (string) $_REQUEST['binding'];
            } else {
                $protocolBinding = null;
            }

            if (isset($_REQUEST['NameIDFormat'])) {
                $nameIDFormat = (string) $_REQUEST['NameIDFormat'];
            } else {
                $nameIDFormat = null;
            }

            if (isset($_REQUEST['ConsumerURL'])) {
                $consumerURL = (string)$_REQUEST['ConsumerURL'];
            } elseif (isset($_REQUEST['shire'])) {
                $consumerURL = (string)$_REQUEST['shire'];
            } else {
                $consumerURL = null;
            }

            $requestId = null;
            $IDPList = [];
            $ProxyCount = null;
            $RequesterID = null;
            $forceAuthn = false;
            $isPassive = false;
            $consumerIndex = null;
            $extensions = null;
            $allowCreate = true;
            $authnContext = null;

            $idpInit = true;

            Logger::info(
                'SAML2.0 - IdP.SSOService: IdP initiated authentication: ' . var_export($spEntityId, true)
            );
        } else {
            try {
                $binding = Binding::getCurrentBinding();
            } catch (Exception $e) {
                header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
                exit;
            }
            $request = $binding->receive();

            if (!($request instanceof AuthnRequest)) {
                throw new Error\BadRequest(
                    'Message received on authentication request endpoint wasn\'t an authentication request.'
                );
            }

            /** @psalm-var null|string|\SAML2\XML\saml\Issuer $issuer   Remove in SSP 2.0 */
            $issuer = $request->getIssuer();
            if ($issuer === null) {
                throw new Error\BadRequest(
                    'Received message on authentication request endpoint without issuer.'
                );
            } elseif ($issuer instanceof Issuer) {
                /** @psalm-var string|null $spEntityId */
                $spEntityId = $issuer->getValue();
                if ($spEntityId === null) {
                    /* Without an issuer we have no way to respond to the message. */
                    throw new Error\BadRequest('Received message on logout endpoint without issuer.');
                }
            } else { // we got a string, old case
                $spEntityId = $issuer;
            }
            $spMetadata = $metadata->getMetaDataConfig($spEntityId, 'saml20-sp-remote');

            \SimpleSAML\Module\saml\Message::validateMessage($spMetadata, $idpMetadata, $request);

            $relayState = $request->getRelayState();

            $requestId = $request->getId();
            $IDPList = $request->getIDPList();
            $ProxyCount = $request->getProxyCount();
            if ($ProxyCount !== null) {
                $ProxyCount--;
            }
            $RequesterID = $request->getRequesterID();
            $forceAuthn = $request->getForceAuthn();
            $isPassive = $request->getIsPassive();
            $consumerURL = $request->getAssertionConsumerServiceURL();
            $protocolBinding = $request->getProtocolBinding();
            $consumerIndex = $request->getAssertionConsumerServiceIndex();
            $extensions = $request->getExtensions();
            $authnContext = $request->getRequestedAuthnContext();

            $nameIdPolicy = $request->getNameIdPolicy();
            if (isset($nameIdPolicy['Format'])) {
                $nameIDFormat = $nameIdPolicy['Format'];
            } else {
                $nameIDFormat = null;
            }
            if (isset($nameIdPolicy['AllowCreate'])) {
                $allowCreate = $nameIdPolicy['AllowCreate'];
            } else {
                $allowCreate = false;
            }

            $idpInit = false;

            Logger::info(
                'SAML2.0 - IdP.SSOService: incoming authentication request: ' . var_export($spEntityId, true)
            );
        }

        Stats::log('saml:idp:AuthnRequest', [
            'spEntityID'  => $spEntityId,
            'idpEntityID' => $idpMetadata->getString('entityid'),
            'forceAuthn'  => $forceAuthn,
            'isPassive'   => $isPassive,
            'protocol'    => 'saml2',
            'idpInit'     => $idpInit,
        ]);

        $acsEndpoint = self::getAssertionConsumerService(
            $supportedBindings,
            $spMetadata,
            $consumerURL,
            $protocolBinding,
            $consumerIndex
        );
        if ($acsEndpoint === null) {
            throw new Exception('Unable to use any of the ACS endpoints found for SP \'' . $spEntityId . '\'');
        }

        $IDPList = array_unique(array_merge($IDPList, $spMetadata->getArrayizeString('IDPList', [])));
        if ($ProxyCount === null) {
            $ProxyCount = $spMetadata->getInteger('ProxyCount', null);
        }

        if (!$forceAuthn) {
            $forceAuthn = $spMetadata->getBoolean('ForceAuthn', false);
        }

        $sessionLostParams = [
            'spentityid' => $spEntityId,
        ];
        if ($relayState !== null) {
            $sessionLostParams['RelayState'] = $relayState;
        }
        /*
        Putting cookieTime as the last parameter makes unit testing easier since we don't need to handle a
        changing time component in the middle of the url
        */
        $sessionLostParams['cookieTime'] = time();

        $sessionLostURL = Utils\HTTP::addURLParameters(
            Utils\HTTP::getSelfURLNoQuery(),
            $sessionLostParams
        );

        $state = [
            'Responder' => ['\SimpleSAML\Module\saml\IdP\SAML2', 'sendResponse'],
            Auth\State::EXCEPTION_HANDLER_FUNC => [
                '\SimpleSAML\Module\saml\IdP\SAML2',
                'handleAuthError'
            ],
            Auth\State::RESTART => $sessionLostURL,

            'SPMetadata'                  => $spMetadata->toArray(),
            'saml:RelayState'             => $relayState,
            'saml:RequestId'              => $requestId,
            'saml:IDPList'                => $IDPList,
            'saml:ProxyCount'             => $ProxyCount,
            'saml:RequesterID'            => $RequesterID,
            'ForceAuthn'                  => $forceAuthn,
            'isPassive'                   => $isPassive,
            'saml:ConsumerURL'            => $acsEndpoint['Location'],
            'saml:Binding'                => $acsEndpoint['Binding'],
            'saml:NameIDFormat'           => $nameIDFormat,
            'saml:AllowCreate'            => $allowCreate,
            'saml:Extensions'             => $extensions,
            'saml:AuthnRequestReceivedAt' => microtime(true),
            'saml:RequestedAuthnContext'  => $authnContext,
        ];

        $idp->handleAuthenticationRequest($state);
    }


    /**
     * Send a logout request to a given association.
     *
     * @param \SimpleSAML\IdP $idp The IdP we are sending a logout request from.
     * @param array           $association The association that should be terminated.
     * @param string|null     $relayState An id that should be carried across the logout.
     * @return void
     */
    public static function sendLogoutRequest(IdP $idp, array $association, $relayState)
    {
        assert(is_string($relayState) || $relayState === null);

        Logger::info('Sending SAML 2.0 LogoutRequest to: ' . var_export($association['saml:entityID'], true));

        $metadata = MetaDataStorageHandler::getMetadataHandler();
        $idpMetadata = $idp->getConfig();
        $spMetadata = $metadata->getMetaDataConfig($association['saml:entityID'], 'saml20-sp-remote');

        Stats::log('saml:idp:LogoutRequest:sent', [
            'spEntityID'  => $association['saml:entityID'],
            'idpEntityID' => $idpMetadata->getString('entityid'),
        ]);

        /** @var array $dst */
        $dst = $spMetadata->getEndpointPrioritizedByBinding(
            'SingleLogoutService',
            [
                Constants::BINDING_HTTP_REDIRECT,
                Constants::BINDING_HTTP_POST
            ]
        );
        $binding = Binding::getBinding($dst['Binding']);
        $lr = self::buildLogoutRequest($idpMetadata, $spMetadata, $association, $relayState);
        $lr->setDestination($dst['Location']);

        $binding->send($lr);
    }


    /**
     * Send a logout response.
     *
     * @param \SimpleSAML\IdP $idp The IdP we are sending a logout request from.
     * @param array           &$state The logout state array.
     * @return void
     */
    public static function sendLogoutResponse(IdP $idp, array $state)
    {
        assert(isset($state['saml:SPEntityId']));
        assert(isset($state['saml:RequestId']));
        assert(array_key_exists('saml:RelayState', $state)); // Can be NULL.

        $spEntityId = $state['saml:SPEntityId'];

        $metadata = MetaDataStorageHandler::getMetadataHandler();
        $idpMetadata = $idp->getConfig();
        $spMetadata = $metadata->getMetaDataConfig($spEntityId, 'saml20-sp-remote');

        $lr = \SimpleSAML\Module\saml\Message::buildLogoutResponse($idpMetadata, $spMetadata);
        $lr->setInResponseTo($state['saml:RequestId']);
        $lr->setRelayState($state['saml:RelayState']);

        if (isset($state['core:Failed']) && $state['core:Failed']) {
            $partial = true;
            $lr->setStatus([
                'Code'    => Constants::STATUS_SUCCESS,
                'SubCode' => Constants::STATUS_PARTIAL_LOGOUT,
            ]);
            Logger::info('Sending logout response for partial logout to SP ' . var_export($spEntityId, true));
        } else {
            $partial = false;
            Logger::debug('Sending logout response to SP ' . var_export($spEntityId, true));
        }

        Stats::log('saml:idp:LogoutResponse:sent', [
            'spEntityID'  => $spEntityId,
            'idpEntityID' => $idpMetadata->getString('entityid'),
            'partial'     => $partial
        ]);

        /** @var array $dst */
        $dst = $spMetadata->getEndpointPrioritizedByBinding(
            'SingleLogoutService',
            [
                Constants::BINDING_HTTP_REDIRECT,
                Constants::BINDING_HTTP_POST
            ]
        );
        $binding = Binding::getBinding($dst['Binding']);
        if (isset($dst['ResponseLocation'])) {
            $dst = $dst['ResponseLocation'];
        } else {
            $dst = $dst['Location'];
        }
        $lr->setDestination($dst);

        $binding->send($lr);
    }


    /**
     * Receive a logout message.
     *
     * @param \SimpleSAML\IdP $idp The IdP we are receiving it for.
     * @return void
     * @throws \SimpleSAML\Error\BadRequest In case an error occurs while trying to receive the logout message.
     */
    public static function receiveLogoutMessage(IdP $idp)
    {
        $binding = Binding::getCurrentBinding();
        $message = $binding->receive();

        /** @psalm-var null|string|\SAML2\XML\saml\Issuer  Remove in SSP 2.0 */
        $issuer = $message->getIssuer();
        if ($issuer === null) {
            /* Without an issuer we have no way to respond to the message. */
            throw new Error\BadRequest('Received message on logout endpoint without issuer.');
        } elseif ($issuer instanceof Issuer) {
            /** @psalm-var string|null $spEntityId */
            $spEntityId = $issuer->getValue();
            if ($spEntityId === null) {
                /* Without an issuer we have no way to respond to the message. */
                throw new Error\BadRequest('Received message on logout endpoint without issuer.');
            }
        } else {
            $spEntityId = $issuer;
        }

        $metadata = MetaDataStorageHandler::getMetadataHandler();
        $idpMetadata = $idp->getConfig();
        $spMetadata = $metadata->getMetaDataConfig($spEntityId, 'saml20-sp-remote');

        \SimpleSAML\Module\saml\Message::validateMessage($spMetadata, $idpMetadata, $message);

        if ($message instanceof LogoutResponse) {
            Logger::info('Received SAML 2.0 LogoutResponse from: ' . var_export($spEntityId, true));
            $statsData = [
                'spEntityID'  => $spEntityId,
                'idpEntityID' => $idpMetadata->getString('entityid'),
            ];
            if (!$message->isSuccess()) {
                $statsData['error'] = $message->getStatus();
            }
            Stats::log('saml:idp:LogoutResponse:recv', $statsData);

            $relayState = $message->getRelayState();

            if (!$message->isSuccess()) {
                $logoutError = \SimpleSAML\Module\saml\Message::getResponseError($message);
                Logger::warning('Unsuccessful logout. Status was: ' . $logoutError);
            } else {
                $logoutError = null;
            }

            $assocId = 'saml:' . $spEntityId;

            $idp->handleLogoutResponse($assocId, $relayState, $logoutError);
        } elseif ($message instanceof LogoutRequest) {
            Logger::info('Received SAML 2.0 LogoutRequest from: ' . var_export($spEntityId, true));
            Stats::log('saml:idp:LogoutRequest:recv', [
                'spEntityID'  => $spEntityId,
                'idpEntityID' => $idpMetadata->getString('entityid'),
            ]);

            $spStatsId = $spMetadata->getString('core:statistics-id', $spEntityId);
            Logger::stats('saml20-idp-SLO spinit ' . $spStatsId . ' ' . $idpMetadata->getString('entityid'));

            $state = [
                'Responder'       => ['\SimpleSAML\Module\saml\IdP\SAML2', 'sendLogoutResponse'],
                'saml:SPEntityId' => $spEntityId,
                'saml:RelayState' => $message->getRelayState(),
                'saml:RequestId'  => $message->getId(),
            ];

            $assocId = 'saml:' . $spEntityId;
            $idp->handleLogoutRequest($state, $assocId);
        } else {
            throw new Error\BadRequest('Unknown message received on logout endpoint: ' . get_class($message));
        }
    }


    /**
     * Retrieve a logout URL for a given logout association.
     *
     * @param \SimpleSAML\IdP $idp The IdP we are sending a logout request from.
     * @param array           $association The association that should be terminated.
     * @param string|NULL     $relayState An id that should be carried across the logout.
     *
     * @return string The logout URL.
     */
    public static function getLogoutURL(IdP $idp, array $association, $relayState)
    {
        assert(is_string($relayState) || $relayState === null);

        Logger::info('Sending SAML 2.0 LogoutRequest to: ' . var_export($association['saml:entityID'], true));

        $metadata = MetaDataStorageHandler::getMetadataHandler();
        $idpMetadata = $idp->getConfig();
        $spMetadata = $metadata->getMetaDataConfig($association['saml:entityID'], 'saml20-sp-remote');

        $bindings = [
            Constants::BINDING_HTTP_REDIRECT,
            Constants::BINDING_HTTP_POST
        ];

        /** @var array $dst */
        $dst = $spMetadata->getEndpointPrioritizedByBinding('SingleLogoutService', $bindings);

        if ($dst['Binding'] === Constants::BINDING_HTTP_POST) {
            $params = ['association' => $association['id'], 'idp' => $idp->getId()];
            if ($relayState !== null) {
                $params['RelayState'] = $relayState;
            }
            return Module::getModuleURL('core/idp/logout-iframe-post.php', $params);
        }

        $lr = self::buildLogoutRequest($idpMetadata, $spMetadata, $association, $relayState);
        $lr->setDestination($dst['Location']);

        $binding = new HTTPRedirect();
        return $binding->getRedirectURL($lr);
    }


    /**
     * Retrieve the metadata for the given SP association.
     *
     * @param \SimpleSAML\IdP $idp The IdP the association belongs to.
     * @param array           $association The SP association.
     *
     * @return \SimpleSAML\Configuration  Configuration object for the SP metadata.
     */
    public static function getAssociationConfig(IdP $idp, array $association)
    {
        $metadata = MetaDataStorageHandler::getMetadataHandler();
        try {
            return $metadata->getMetaDataConfig($association['saml:entityID'], 'saml20-sp-remote');
        } catch (Exception $e) {
            return Configuration::loadFromArray([], 'Unknown SAML 2 entity.');
        }
    }


    /**
     * Retrieve the metadata of a hosted SAML 2 IdP.
     *
     * @param string $entityid The entity ID of the hosted SAML 2 IdP whose metadata we want.
     *
     * @return array
     * @throws \SimpleSAML\Error\CriticalConfigurationError
     * @throws \SimpleSAML\Error\Exception
     * @throws \SimpleSAML\Error\MetadataNotFound
     */
    public static function getHostedMetadata($entityid)
    {
        $handler = MetaDataStorageHandler::getMetadataHandler();
        $config = $handler->getMetaDataConfig($entityid, 'saml20-idp-hosted');

        // configure endpoints
        $ssob = $handler->getGenerated('SingleSignOnServiceBinding', 'saml20-idp-hosted');
        $slob = $handler->getGenerated('SingleLogoutServiceBinding', 'saml20-idp-hosted');
        $ssol = $handler->getGenerated('SingleSignOnService', 'saml20-idp-hosted');
        $slol = $handler->getGenerated('SingleLogoutService', 'saml20-idp-hosted');

        $sso = [];
        if (is_array($ssob)) {
            foreach ($ssob as $binding) {
                $sso[] = [
                    'Binding'  => $binding,
                    'Location' => $ssol,
                ];
            }
        } else {
            $sso[] = [
                'Binding'  => $ssob,
                'Location' => $ssol,
            ];
        }

        $slo = [];
        if (is_array($slob)) {
            foreach ($slob as $binding) {
                $slo[] = [
                    'Binding'  => $binding,
                    'Location' => $slol,
                ];
            }
        } else {
            $slo[] = [
                'Binding'  => $slob,
                'Location' => $slol,
            ];
        }

        $metadata = [
            'metadata-set' => 'saml20-idp-hosted',
            'entityid' => $entityid,
            'SingleSignOnService' => $sso,
            'SingleLogoutService' => $slo,
            'NameIDFormat' => $config->getArrayizeString('NameIDFormat', Constants::NAMEID_TRANSIENT),
        ];

        // add certificates
        $keys = [];
        $certInfo = Utils\Crypto::loadPublicKey($config, false, 'new_');
        $hasNewCert = false;
        if ($certInfo !== null) {
            $keys[] = [
                'type' => 'X509Certificate',
                'signing' => true,
                'encryption' => true,
                'X509Certificate' => $certInfo['certData'],
                'prefix' => 'new_',
            ];
            $hasNewCert = true;
        }

        /** @var array $certInfo */
        $certInfo = Utils\Crypto::loadPublicKey($config, true);
        $keys[] = [
            'type' => 'X509Certificate',
            'signing' => true,
            'encryption' => $hasNewCert === false,
            'X509Certificate' => $certInfo['certData'],
            'prefix' => '',
        ];

        if ($config->hasValue('https.certificate')) {
            /** @var array $httpsCert */
            $httpsCert = Utils\Crypto::loadPublicKey($config, true, 'https.');
            $keys[] = [
                'type' => 'X509Certificate',
                'signing' => true,
                'encryption' => false,
                'X509Certificate' => $httpsCert['certData'],
                'prefix' => 'https.'
            ];
        }
        $metadata['keys'] = $keys;

        // add ArtifactResolutionService endpoint, if enabled
        if ($config->getBoolean('saml20.sendartifact', false)) {
            $metadata['ArtifactResolutionService'][] = [
                'index' => 0,
                'Binding' => Constants::BINDING_SOAP,
                'Location' => Utils\HTTP::getBaseURL() . 'saml2/idp/ArtifactResolutionService.php'
            ];
        }

        // add Holder of Key, if enabled
        if ($config->getBoolean('saml20.hok.assertion', false)) {
            array_unshift(
                $metadata['SingleSignOnService'],
                [
                    'hoksso:ProtocolBinding' => Constants::BINDING_HTTP_REDIRECT,
                    'Binding' => Constants::BINDING_HOK_SSO,
                    'Location' => Utils\HTTP::getBaseURL() . 'saml2/idp/SSOService.php',
                ]
            );
        }

        // add ECP profile, if enabled
        if ($config->getBoolean('saml20.ecp', false)) {
            $metadata['SingleSignOnService'][] = [
                'index' => 0,
                'Binding' => Constants::BINDING_SOAP,
                'Location' => Utils\HTTP::getBaseURL() . 'saml2/idp/SSOService.php',
            ];
        }

        // add organization information
        if ($config->hasValue('OrganizationName')) {
            $metadata['OrganizationName'] = $config->getLocalizedString('OrganizationName');
            $metadata['OrganizationDisplayName'] = $config->getLocalizedString(
                'OrganizationDisplayName',
                $metadata['OrganizationName']
            );

            if (!$config->hasValue('OrganizationURL')) {
                throw new Error\Exception('If OrganizationName is set, OrganizationURL must also be set.');
            }
            $metadata['OrganizationURL'] = $config->getLocalizedString('OrganizationURL');
        }

        // add scope
        if ($config->hasValue('scope')) {
            $metadata['scope'] = $config->getArray('scope');
        }

        // add extensions
        if ($config->hasValue('EntityAttributes')) {
            $metadata['EntityAttributes'] = $config->getArray('EntityAttributes');

            // check for entity categories
            if (Utils\Config\Metadata::isHiddenFromDiscovery($metadata)) {
                $metadata['hide.from.discovery'] = true;
            }
        }

        if ($config->hasValue('UIInfo')) {
            $metadata['UIInfo'] = $config->getArray('UIInfo');
        }

        if ($config->hasValue('DiscoHints')) {
            $metadata['DiscoHints'] = $config->getArray('DiscoHints');
        }

        if ($config->hasValue('RegistrationInfo')) {
            $metadata['RegistrationInfo'] = $config->getArray('RegistrationInfo');
        }

        // configure signature options
        if ($config->hasValue('validate.authnrequest')) {
            $metadata['sign.authnrequest'] = $config->getBoolean('validate.authnrequest');
        }

        if ($config->hasValue('redirect.validate')) {
            $metadata['redirect.sign'] = $config->getBoolean('redirect.validate');
        }

        // add contact information
        if ($config->hasValue('contacts')) {
            $contacts = $config->getArray('contacts');
            foreach ($contacts as $contact) {
                $metadata['contacts'][] = Utils\Config\Metadata::getContact($contact);
            }
        }

        $globalConfig = Configuration::getInstance();
        $email = $globalConfig->getString('technicalcontact_email', false);
        if ($email && $email !== 'na@example.org') {
            $contact = [
                'emailAddress' => $email,
                'name' => $globalConfig->getString('technicalcontact_name', null),
                'contactType' => 'technical',
            ];
            $metadata['contacts'][] = Utils\Config\Metadata::getContact($contact);
        }

        return $metadata;
    }


    /**
     * Calculate the NameID value that should be used.
     *
     * @param \SimpleSAML\Configuration $idpMetadata The metadata of the IdP.
     * @param \SimpleSAML\Configuration $spMetadata The metadata of the SP.
     * @param array                     &$state The authentication state of the user.
     *
     * @return string|null The NameID value.
     */
    private static function generateNameIdValue(
        Configuration $idpMetadata,
        Configuration $spMetadata,
        array &$state
    ): ?string {
        $attribute = $spMetadata->getString('simplesaml.nameidattribute', null);
        if ($attribute === null) {
            $attribute = $idpMetadata->getString('simplesaml.nameidattribute', null);
            if ($attribute === null) {
                if (!isset($state['UserID'])) {
                    Logger::error('Unable to generate NameID. Check the userid.attribute option.');
                    return null;
                }
                $attributeValue = $state['UserID'];
                $idpEntityId = $idpMetadata->getString('entityid');
                $spEntityId = $spMetadata->getString('entityid');

                $secretSalt = Utils\Config::getSecretSalt();

                $uidData = 'uidhashbase' . $secretSalt;
                $uidData .= strlen($idpEntityId) . ':' . $idpEntityId;
                $uidData .= strlen($spEntityId) . ':' . $spEntityId;
                $uidData .= strlen($attributeValue) . ':' . $attributeValue;
                $uidData .= $secretSalt;

                return hash('sha1', $uidData);
            }
        }

        $attributes = $state['Attributes'];
        if (!array_key_exists($attribute, $attributes)) {
            Logger::error('Unable to add NameID: Missing ' . var_export($attribute, true) .
                ' in the attributes of the user.');
            return null;
        }

        return $attributes[$attribute][0];
    }


    /**
     * Helper function for encoding attributes.
     *
     * @param \SimpleSAML\Configuration $idpMetadata The metadata of the IdP.
     * @param \SimpleSAML\Configuration $spMetadata The metadata of the SP.
     * @param array $attributes The attributes of the user.
     *
     * @return array  The encoded attributes.
     *
     * @throws \SimpleSAML\Error\Exception In case an unsupported encoding is specified by configuration.
     */
    private static function encodeAttributes(
        Configuration $idpMetadata,
        Configuration $spMetadata,
        array $attributes
    ): array {
        $base64Attributes = $spMetadata->getBoolean('base64attributes', null);
        if ($base64Attributes === null) {
            $base64Attributes = $idpMetadata->getBoolean('base64attributes', false);
        }

        if ($base64Attributes) {
            $defaultEncoding = 'base64';
        } else {
            $defaultEncoding = 'string';
        }

        $srcEncodings = $idpMetadata->getArray('attributeencodings', []);
        $dstEncodings = $spMetadata->getArray('attributeencodings', []);

        /*
         * Merge the two encoding arrays. Encodings specified in the target metadata
         * takes precedence over the source metadata.
         */
        $encodings = array_merge($srcEncodings, $dstEncodings);

        $ret = [];
        foreach ($attributes as $name => $values) {
            $ret[$name] = [];
            if (array_key_exists($name, $encodings)) {
                $encoding = $encodings[$name];
            } else {
                $encoding = $defaultEncoding;
            }

            foreach ($values as $value) {
                // allow null values
                if ($value === null) {
                    $ret[$name][] = $value;
                    continue;
                }

                $attrval = $value;
                if ($value instanceof DOMNodeList) {
                    /** @psalm-suppress PossiblyNullPropertyFetch */
                    $attrval = new AttributeValue($value->item(0)->parentNode);
                }

                switch ($encoding) {
                    case 'string':
                        $value = (string) $attrval;
                        break;
                    case 'base64':
                        $value = base64_encode((string) $attrval);
                        break;
                    case 'raw':
                        if (is_string($value)) {
                            $doc = DOMDocumentFactory::fromString('<root>' . $value . '</root>');
                            $value = $doc->firstChild->childNodes;
                        }
                        assert($value instanceof DOMNodeList || $value instanceof NameID);
                        break;
                    default:
                        throw new Error\Exception('Invalid encoding for attribute ' .
                            var_export($name, true) . ': ' . var_export($encoding, true));
                }
                $ret[$name][] = $value;
            }
        }

        return $ret;
    }


    /**
     * Determine which NameFormat we should use for attributes.
     *
     * @param \SimpleSAML\Configuration $idpMetadata The metadata of the IdP.
     * @param \SimpleSAML\Configuration $spMetadata The metadata of the SP.
     *
     * @return string  The NameFormat.
     */
    private static function getAttributeNameFormat(
        Configuration $idpMetadata,
        Configuration $spMetadata
    ): string {
        // try SP metadata first
        $attributeNameFormat = $spMetadata->getString('attributes.NameFormat', null);
        if ($attributeNameFormat !== null) {
            return $attributeNameFormat;
        }
        $attributeNameFormat = $spMetadata->getString('AttributeNameFormat', null);
        if ($attributeNameFormat !== null) {
            return $attributeNameFormat;
        }

        // look in IdP metadata
        $attributeNameFormat = $idpMetadata->getString('attributes.NameFormat', null);
        if ($attributeNameFormat !== null) {
            return $attributeNameFormat;
        }
        $attributeNameFormat = $idpMetadata->getString('AttributeNameFormat', null);
        if ($attributeNameFormat !== null) {
            return $attributeNameFormat;
        }

        // default
        return Constants::NAMEFORMAT_BASIC;
    }


    /**
     * Build an assertion based on information in the metadata.
     *
     * @param \SimpleSAML\Configuration $idpMetadata The metadata of the IdP.
     * @param \SimpleSAML\Configuration $spMetadata The metadata of the SP.
     * @param array &$state The state array with information about the request.
     *
     * @return \SAML2\Assertion  The assertion.
     *
     * @throws \SimpleSAML\Error\Exception In case an error occurs when creating a holder-of-key assertion.
     */
    private static function buildAssertion(
        Configuration $idpMetadata,
        Configuration $spMetadata,
        array &$state
    ): Assertion {
        assert(isset($state['Attributes']));
        assert(isset($state['saml:ConsumerURL']));

        $now = time();

        $signAssertion = $spMetadata->getBoolean('saml20.sign.assertion', null);
        if ($signAssertion === null) {
            $signAssertion = $idpMetadata->getBoolean('saml20.sign.assertion', true);
        }

        $config = Configuration::getInstance();

        $a = new Assertion();
        if ($signAssertion) {
            \SimpleSAML\Module\saml\Message::addSign($idpMetadata, $spMetadata, $a);
        }

        $issuer = new Issuer();
        $issuer->setValue($idpMetadata->getString('entityid'));
        $issuer->setFormat(Constants::NAMEID_ENTITY);
        $a->setIssuer($issuer);

        $audience = array_merge([$spMetadata->getString('entityid')], $spMetadata->getArray('audience', []));
        $a->setValidAudiences($audience);

        $a->setNotBefore($now - 30);

        $assertionLifetime = $spMetadata->getInteger('assertion.lifetime', null);
        if ($assertionLifetime === null) {
            $assertionLifetime = $idpMetadata->getInteger('assertion.lifetime', 300);
        }
        $a->setNotOnOrAfter($now + $assertionLifetime);

        if (isset($state['saml:AuthnContextClassRef'])) {
            $a->setAuthnContextClassRef($state['saml:AuthnContextClassRef']);
        } elseif (Utils\HTTP::isHTTPS()) {
            $a->setAuthnContextClassRef(Constants::AC_PASSWORD_PROTECTED_TRANSPORT);
        } else {
            $a->setAuthnContextClassRef(Constants::AC_PASSWORD);
        }

        $sessionStart = $now;
        if (isset($state['AuthnInstant'])) {
            $a->setAuthnInstant($state['AuthnInstant']);
            $sessionStart = $state['AuthnInstant'];
        }

        $sessionLifetime = $config->getInteger('session.duration', 8 * 60 * 60);
        $a->setSessionNotOnOrAfter($sessionStart + $sessionLifetime);

        $a->setSessionIndex(Utils\Random::generateID());

        $sc = new SubjectConfirmation();
        $scd = new SubjectConfirmationData();
        $scd->setNotOnOrAfter($now + $assertionLifetime);
        $scd->setRecipient($state['saml:ConsumerURL']);
        $scd->setInResponseTo($state['saml:RequestId']);
        $sc->setSubjectConfirmationData($scd);

        // ProtcolBinding of SP's <AuthnRequest> overwrites IdP hosted metadata configuration
        $hokAssertion = null;
        if ($state['saml:Binding'] === Constants::BINDING_HOK_SSO) {
            $hokAssertion = true;
        }
        if ($hokAssertion === null) {
            $hokAssertion = $idpMetadata->getBoolean('saml20.hok.assertion', false);
        }

        if ($hokAssertion) {
            // Holder-of-Key
            $sc->setMethod(Constants::CM_HOK);
            if (Utils\HTTP::isHTTPS()) {
                if (isset($_SERVER['SSL_CLIENT_CERT']) && !empty($_SERVER['SSL_CLIENT_CERT'])) {
                    // extract certificate data (if this is a certificate)
                    $clientCert = $_SERVER['SSL_CLIENT_CERT'];
                    $pattern = '/^-----BEGIN CERTIFICATE-----([^-]*)^-----END CERTIFICATE-----/m';
                    if (preg_match($pattern, $clientCert, $matches)) {
                        // we have a client certificate from the browser which we add to the HoK assertion
                        $x509Certificate = new X509Certificate();
                        $x509Certificate->setCertificate(str_replace(["\r", "\n", " "], '', $matches[1]));

                        $x509Data = new X509Data();
                        $x509Data->addData($x509Certificate);

                        $keyInfo = new KeyInfo();
                        $keyInfo->addInfo($x509Data);

                        $scd->addInfo($keyInfo);
                    } else {
                        throw new Error\Exception(
                            'Error creating HoK assertion: No valid client certificate provided during '
                            . 'TLS handshake with IdP'
                        );
                    }
                } else {
                    throw new Error\Exception(
                        'Error creating HoK assertion: No client certificate provided during TLS handshake with IdP'
                    );
                }
            } else {
                throw new Error\Exception(
                    'Error creating HoK assertion: No HTTPS connection to IdP, but required for Holder-of-Key SSO'
                );
            }
        } else {
            // Bearer
            $sc->setMethod(Constants::CM_BEARER);
        }
        $sc->setSubjectConfirmationData($scd);
        $a->setSubjectConfirmation([$sc]);

        // add attributes
        if ($spMetadata->getBoolean('simplesaml.attributes', true)) {
            $attributeNameFormat = self::getAttributeNameFormat($idpMetadata, $spMetadata);
            $a->setAttributeNameFormat($attributeNameFormat);
            $attributes = self::encodeAttributes($idpMetadata, $spMetadata, $state['Attributes']);
            $a->setAttributes($attributes);
        }

        $nameIdFormat = null;

        // generate the NameID for the assertion
        if (isset($state['saml:NameIDFormat'])) {
            $nameIdFormat = $state['saml:NameIDFormat'];
        }

        if ($nameIdFormat === null || !isset($state['saml:NameID'][$nameIdFormat])) {
            // either not set in request, or not set to a format we supply. Fall back to old generation method
            $nameIdFormat = current($spMetadata->getArrayizeString('NameIDFormat', []));
            if ($nameIdFormat === false) {
                $nameIdFormat = current($idpMetadata->getArrayizeString('NameIDFormat', [Constants::NAMEID_TRANSIENT]));
            }
        }

        if (isset($state['saml:NameID'][$nameIdFormat])) {
            $nameId = $state['saml:NameID'][$nameIdFormat];
            $nameId->setFormat($nameIdFormat);
        } else {
            $spNameQualifier = $spMetadata->getString('SPNameQualifier', null);
            if ($spNameQualifier === null) {
                $spNameQualifier = $spMetadata->getString('entityid');
            }

            if ($nameIdFormat === Constants::NAMEID_TRANSIENT) {
                // generate a random id
                $nameIdValue = Utils\Random::generateID();
            } else {
                /* this code will end up generating either a fixed assigned id (via nameid.attribute)
                   or random id if not assigned/configured */
                $nameIdValue = self::generateNameIdValue($idpMetadata, $spMetadata, $state);
                if ($nameIdValue === null) {
                    Logger::warning('Falling back to transient NameID.');
                    $nameIdFormat = Constants::NAMEID_TRANSIENT;
                    $nameIdValue = Utils\Random::generateID();
                }
            }

            $nameId = new NameID();
            $nameId->setFormat($nameIdFormat);
            $nameId->setValue($nameIdValue);
            $nameId->setSPNameQualifier($spNameQualifier);
        }

        $state['saml:idp:NameID'] = $nameId;

        $a->setNameId($nameId);

        $encryptNameId = $spMetadata->getBoolean('nameid.encryption', null);
        if ($encryptNameId === null) {
            $encryptNameId = $idpMetadata->getBoolean('nameid.encryption', false);
        }
        if ($encryptNameId) {
            $a->encryptNameId(\SimpleSAML\Module\saml\Message::getEncryptionKey($spMetadata));
        }

        return $a;
    }


    /**
     * Encrypt an assertion.
     *
     * This function takes in a \SAML2\Assertion and encrypts it if encryption of
     * assertions are enabled in the metadata.
     *
     * @param \SimpleSAML\Configuration $idpMetadata The metadata of the IdP.
     * @param \SimpleSAML\Configuration $spMetadata The metadata of the SP.
     * @param \SAML2\Assertion $assertion The assertion we are encrypting.
     *
     * @return \SAML2\Assertion|\SAML2\EncryptedAssertion  The assertion.
     *
     * @throws \SimpleSAML\Error\Exception In case the encryption key type is not supported.
     */
    private static function encryptAssertion(
        Configuration $idpMetadata,
        Configuration $spMetadata,
        Assertion $assertion
    ) {
        $encryptAssertion = $spMetadata->getBoolean('assertion.encryption', null);
        if ($encryptAssertion === null) {
            $encryptAssertion = $idpMetadata->getBoolean('assertion.encryption', false);
        }
        if (!$encryptAssertion) {
            // we are _not_ encrypting this assertion, and are therefore done
            return $assertion;
        }


        $sharedKey = $spMetadata->getString('sharedkey', null);
        if ($sharedKey !== null) {
            $algo = $spMetadata->getString('sharedkey_algorithm', null);
            if ($algo === null) {
                $algo = $idpMetadata->getString('sharedkey_algorithm');
            }

            $key = new XMLSecurityKey($algo);
            $key->loadKey($sharedKey);
        } else {
            $keys = $spMetadata->getPublicKeys('encryption', true);
            if (!empty($keys)) {
                $key = $keys[0];
                switch ($key['type']) {
                    case 'X509Certificate':
                        $pemKey = "-----BEGIN CERTIFICATE-----\n" .
                            chunk_split($key['X509Certificate'], 64) .
                            "-----END CERTIFICATE-----\n";
                        break;
                    default:
                        throw new Error\Exception('Unsupported encryption key type: ' . $key['type']);
                }

                // extract the public key from the certificate for encryption
                $key = new XMLSecurityKey(XMLSecurityKey::RSA_OAEP_MGF1P, ['type' => 'public']);
                $key->loadKey($pemKey);
            } else {
                throw new Error\ConfigurationError(
                    'Missing encryption key for entity `' . $spMetadata->getString('entityid') . '`',
                    $spMetadata->getString('metadata-set') . '.php',
                    null
                );
            }
        }

        $ea = new EncryptedAssertion();
        $ea->setAssertion($assertion, $key);
        return $ea;
    }


    /**
     * Build a logout request based on information in the metadata.
     *
     * @param \SimpleSAML\Configuration $idpMetadata The metadata of the IdP.
     * @param \SimpleSAML\Configuration $spMetadata The metadata of the SP.
     * @param array $association The SP association.
     * @param string|null $relayState An id that should be carried across the logout.
     *
     * @return \SAML2\LogoutRequest The corresponding SAML2 logout request.
     */
    private static function buildLogoutRequest(
        Configuration $idpMetadata,
        Configuration $spMetadata,
        array $association,
        string $relayState = null
    ): LogoutRequest {
        $lr = \SimpleSAML\Module\saml\Message::buildLogoutRequest($idpMetadata, $spMetadata);
        $lr->setRelayState($relayState);
        $lr->setSessionIndex($association['saml:SessionIndex']);
        $lr->setNameId($association['saml:NameID']);

        $assertionLifetime = $spMetadata->getInteger('assertion.lifetime', null);
        if ($assertionLifetime === null) {
            $assertionLifetime = $idpMetadata->getInteger('assertion.lifetime', 300);
        }
        $lr->setNotOnOrAfter(time() + $assertionLifetime);

        $encryptNameId = $spMetadata->getBoolean('nameid.encryption', null);
        if ($encryptNameId === null) {
            $encryptNameId = $idpMetadata->getBoolean('nameid.encryption', false);
        }
        if ($encryptNameId) {
            $lr->encryptNameId(\SimpleSAML\Module\saml\Message::getEncryptionKey($spMetadata));
        }

        return $lr;
    }


    /**
     * Build a authentication response based on information in the metadata.
     *
     * @param \SimpleSAML\Configuration $idpMetadata The metadata of the IdP.
     * @param \SimpleSAML\Configuration $spMetadata The metadata of the SP.
     * @param string                    $consumerURL The Destination URL of the response.
     *
     * @return \SAML2\Response The SAML2 Response corresponding to the given data.
     */
    private static function buildResponse(
        Configuration $idpMetadata,
        Configuration $spMetadata,
        string $consumerURL
    ): Response {
        $signResponse = $spMetadata->getBoolean('saml20.sign.response', null);
        if ($signResponse === null) {
            $signResponse = $idpMetadata->getBoolean('saml20.sign.response', true);
        }

        $r = new Response();
        $issuer = new Issuer();
        $issuer->setValue($idpMetadata->getString('entityid'));
        $issuer->setFormat(Constants::NAMEID_ENTITY);
        $r->setIssuer($issuer);
        $r->setDestination($consumerURL);

        if ($signResponse) {
            \SimpleSAML\Module\saml\Message::addSign($idpMetadata, $spMetadata, $r);
        }

        return $r;
    }
}
