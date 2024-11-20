<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml\Auth\Source;

use SAML2\AuthnRequest;
use SAML2\Binding;
use SAML2\Constants;
use SAML2\XML\saml\NameID;
use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\IdP;
use SimpleSAML\Logger;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Module;
use SimpleSAML\Session;
use SimpleSAML\Store;
use SimpleSAML\Utils;
use SimpleSAML\XML\Shib13;

class SP extends \SimpleSAML\Auth\Source
{
    /**
     * The entity ID of this SP.
     *
     * @var string
     */
    private $entityId;

    /**
     * The metadata of this SP.
     *
     * @var \SimpleSAML\Configuration
     */
    private $metadata;

    /**
     * The IdP the user is allowed to log into.
     *
     * @var string|null  The IdP the user can log into, or null if the user can log into all IdPs.
     */
    private $idp;

    /**
     * URL to discovery service.
     *
     * @var string|null
     */
    private $discoURL;

    /**
     * Flag to indicate whether to disable sending the Scoping element.
     *
     * @var bool
     */
    private $disable_scoping;

    /**
     * A list of supported protocols.
     *
     * @var string[]
     */
    private $protocols = [];


    /**
     * Constructor for SAML SP authentication source.
     *
     * @param array $info  Information about this authentication source.
     * @param array $config  Configuration.
     */
    public function __construct($info, $config)
    {
        assert(is_array($info));
        assert(is_array($config));

        // Call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        if (!isset($config['entityID'])) {
            $config['entityID'] = $this->getMetadataURL();
        }

        /* For compatibility with code that assumes that $metadata->getString('entityid')
         * gives the entity id. */
        $config['entityid'] = $config['entityID'];

        $this->metadata = Configuration::loadFromArray(
            $config,
            'authsources[' . var_export($this->authId, true) . ']'
        );
        $this->entityId = $this->metadata->getString('entityID');
        $this->idp = $this->metadata->getString('idp', null);
        $this->discoURL = $this->metadata->getString('discoURL', null);
        $this->disable_scoping = $this->metadata->getBoolean('disable_scoping', false);

        if (empty($this->discoURL) && Module::isModuleEnabled('discojuice')) {
            $this->discoURL = Module::getModuleURL('discojuice/central.php');
        }
    }


    /**
     * Retrieve the URL to the metadata of this SP.
     *
     * @return string  The metadata URL.
     */
    public function getMetadataURL()
    {
        return Module::getModuleURL('saml/sp/metadata.php/' . urlencode($this->authId));
    }


    /**
     * Retrieve the entity id of this SP.
     *
     * @return string  The entity id of this SP.
     */
    public function getEntityId()
    {
        return $this->entityId;
    }


    /**
     * Retrieve the metadata array of this SP, as a remote IdP would see it.
     *
     * @return array The metadata array for its use by a remote IdP.
     */
    public function getHostedMetadata()
    {
        $entityid = $this->getEntityId();
        $metadata = [
            'entityid' => $entityid,
            'metadata-set' => 'saml20-sp-remote',
            'SingleLogoutService' => $this->getSLOEndpoints(),
            'AssertionConsumerService' => $this->getACSEndpoints(),
        ];

        // add NameIDPolicy
        if ($this->metadata->hasValue('NameIDPolicy')) {
            $format = $this->metadata->getValue('NameIDPolicy');
            if (is_array($format)) {
                $metadata['NameIDFormat'] = Configuration::loadFromArray($format)->getString(
                    'Format',
                    Constants::NAMEID_TRANSIENT
                );
            } elseif (is_string($format)) {
                $metadata['NameIDFormat'] = $format;
            }
        }

        // add attributes
        $name = $this->metadata->getLocalizedString('name', null);
        $attributes = $this->metadata->getArray('attributes', []);
        if ($name !== null) {
            if (!empty($attributes)) {
                $metadata['name'] = $name;
                $metadata['attributes'] = $attributes;
                if ($this->metadata->hasValue('attributes.required')) {
                    $metadata['attributes.required'] = $this->metadata->getArray('attributes.required');
                }
                if ($this->metadata->hasValue('description')) {
                    $metadata['description'] = $this->metadata->getArray('description');
                }
                if ($this->metadata->hasValue('attributes.NameFormat')) {
                    $metadata['attributes.NameFormat'] = $this->metadata->getString('attributes.NameFormat');
                }
                if ($this->metadata->hasValue('attributes.index')) {
                    $metadata['attributes.index'] = $this->metadata->getInteger('attributes.index');
                }
                if ($this->metadata->hasValue('attributes.isDefault')) {
                    $metadata['attributes.isDefault'] = $this->metadata->getBoolean('attributes.isDefault');
                }
            }
        }

        // add organization info
        $org = $this->metadata->getLocalizedString('OrganizationName', null);
        if ($org !== null) {
            $metadata['OrganizationName'] = $org;
            $metadata['OrganizationDisplayName'] = $this->metadata->getLocalizedString('OrganizationDisplayName', $org);
            $metadata['OrganizationURL'] = $this->metadata->getLocalizedString('OrganizationURL', null);
            if ($metadata['OrganizationURL'] === null) {
                throw new Error\Exception(
                    'If OrganizationName is set, OrganizationURL must also be set.'
                );
            }
        }

        // add contacts
        $contacts = $this->metadata->getArray('contacts', []);
        foreach ($contacts as $contact) {
            $metadata['contacts'][] = Utils\Config\Metadata::getContact($contact);
        }

        // add technical contact
        $globalConfig = Configuration::getInstance();
        $email = $globalConfig->getString('technicalcontact_email', 'na@example.org');
        if ($email && $email !== 'na@example.org') {
            $contact = [
                'emailAddress' => $email,
                'name' => $globalConfig->getString('technicalcontact_name', null),
                'contactType' => 'technical',
            ];
            $metadata['contacts'][] = Utils\Config\Metadata::getContact($contact);
        }

        // add certificate(s)
        $certInfo = Utils\Crypto::loadPublicKey($this->metadata, false, 'new_');
        $hasNewCert = false;
        if ($certInfo !== null && array_key_exists('certData', $certInfo)) {
            $hasNewCert = true;
            $metadata['keys'][] = [
                'type' => 'X509Certificate',
                'signing' => true,
                'encryption' => true,
                'X509Certificate' => $certInfo['certData'],
                'prefix' => 'new_',
                'url' => Module::getModuleURL(
                    'admin/federation/cert',
                    [
                        'set' => 'saml20-sp-hosted',
                        'source' => $this->getAuthId(),
                        'prefix' => 'new_'
                    ]
                ),
                'name' => 'sp',
            ];
        }

        $certInfo = Utils\Crypto::loadPublicKey($this->metadata);
        if ($certInfo !== null && array_key_exists('certData', $certInfo)) {
            $metadata['keys'][] = [
                'type' => 'X509Certificate',
                'signing' => true,
                'encryption' => $hasNewCert ? false : true,
                'X509Certificate' => $certInfo['certData'],
                'prefix' => '',
                'url' => Module::getModuleURL(
                    'admin/federation/cert',
                    [
                        'set' => 'saml20-sp-hosted',
                        'source' => $this->getAuthId(),
                        'prefix' => ''
                    ]
                ),
                'name' => 'sp',
            ];
        }

        // add EntityAttributes extension
        if ($this->metadata->hasValue('EntityAttributes')) {
            $metadata['EntityAttributes'] = $this->metadata->getArray('EntityAttributes');
        }

        // add UIInfo extension
        if ($this->metadata->hasValue('UIInfo')) {
            $metadata['UIInfo'] = $this->metadata->getArray('UIInfo');
        }

        // add RegistrationInfo extension
        if ($this->metadata->hasValue('RegistrationInfo')) {
            $metadata['RegistrationInfo'] = $this->metadata->getArray('RegistrationInfo');
        }

        // add signature options
        if ($this->metadata->hasValue('WantAssertionsSigned')) {
            $metadata['saml20.sign.assertion'] = $this->metadata->getBoolean('WantAssertionsSigned');
        }
        if ($this->metadata->hasValue('redirect.sign')) {
            $metadata['redirect.validate'] = $this->metadata->getBoolean('redirect.sign');
        } elseif ($this->metadata->hasValue('sign.authnrequest')) {
            $metadata['validate.authnrequest'] = $this->metadata->getBoolean('sign.authnrequest');
        }

        return $metadata;
    }


    /**
     * Retrieve the metadata of an IdP.
     *
     * @param string $entityId  The entity id of the IdP.
     * @return \SimpleSAML\Configuration  The metadata of the IdP.
     */
    public function getIdPMetadata($entityId)
    {
        assert(is_string($entityId));

        global $saml2auth;
        // Moodle customization. If we have multiple IdP's configured
        // then accept requests from the other IdP's even if they are
        // not the default IdP.
        foreach ($saml2auth->metadataentities as $idpentity) {
            if ($idpentity->entityid == $entityId) {
                $this->idp = null;
                break;
            }
        }

        if ($this->idp !== null && $this->idp !== $entityId) {
            throw new Error\Exception('Cannot retrieve metadata for IdP ' .
                var_export($entityId, true) . ' because it isn\'t a valid IdP for this SP.');
        }

        $metadataHandler = MetaDataStorageHandler::getMetadataHandler();

        // First, look in saml20-idp-remote.
        try {
            return $metadataHandler->getMetaDataConfig($entityId, 'saml20-idp-remote');
        } catch (\Exception $e) {
            // Metadata wasn't found
            Logger::debug('getIdpMetadata: ' . $e->getMessage());
        }

        // Not found in saml20-idp-remote, look in shib13-idp-remote
        try {
            return $metadataHandler->getMetaDataConfig($entityId, 'shib13-idp-remote');
        } catch (\Exception $e) {
            // Metadata wasn't found
            Logger::debug('getIdpMetadata: ' . $e->getMessage());
        }

        // Not found
        throw new Error\Exception('Could not find the metadata of an IdP with entity ID ' .
            var_export($entityId, true));
    }


    /**
     * Retrieve the metadata of this SP.
     *
     * @return \SimpleSAML\Configuration  The metadata of this SP.
     */
    public function getMetadata()
    {
        return $this->metadata;
    }


    /**
     * Get a list with the protocols supported by this SP.
     *
     * @return array
     */
    public function getSupportedProtocols()
    {
        return $this->protocols;
    }


    /**
     * Get the AssertionConsumerService endpoints for a given local SP.
     *
     * @return array
     * @throws \Exception
     */
    private function getACSEndpoints(): array
    {
        // If a list of endpoints is specified in config, take that at face value
        if ($this->metadata->hasValue('AssertionConsumerService')) {
            return $this->metadata->getArray('AssertionConsumerService');
        }

        $endpoints = [];
        $default = [
            Constants::BINDING_HTTP_POST,
            'urn:oasis:names:tc:SAML:1.0:profiles:browser-post',
            Constants::BINDING_HTTP_ARTIFACT,
            'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01',
        ];
        if ($this->metadata->getString('ProtocolBinding', '') === Constants::BINDING_HOK_SSO) {
            $default[] = Constants::BINDING_HOK_SSO;
        }

        $bindings = $this->metadata->getArray('acs.Bindings', $default);
        $index = 0;
        foreach ($bindings as $service) {
            switch ($service) {
                case Constants::BINDING_HTTP_POST:
                    $acs = [
                        'Binding' => Constants::BINDING_HTTP_POST,
                        'Location' => Module::getModuleURL('saml/sp/saml2-acs.php/' . $this->getAuthId()),
                    ];
                    if (!in_array(Constants::NS_SAMLP, $this->protocols, true)) {
                        $this->protocols[] = Constants::NS_SAMLP;
                    }
                    break;
                case 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post':
                    $acs = [
                        'Binding' => 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post',
                        'Location' => Module::getModuleURL('saml/sp/saml1-acs.php/' . $this->getAuthId()),
                    ];
                    if (!in_array('urn:oasis:names:tc:SAML:1.0:profiles:browser-post', $this->protocols, true)) {
                        $this->protocols[] = 'urn:oasis:names:tc:SAML:1.1:protocol';
                    }
                    break;
                case Constants::BINDING_HTTP_ARTIFACT:
                    $acs = [
                        'Binding' => Constants::BINDING_HTTP_ARTIFACT,
                        'Location' => Module::getModuleURL('saml/sp/saml2-acs.php/' . $this->getAuthId()),
                    ];
                    if (!in_array(Constants::NS_SAMLP, $this->protocols, true)) {
                        $this->protocols[] = Constants::NS_SAMLP;
                    }
                    break;
                case 'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01':
                    $acs = [
                        'Binding' => 'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01',
                        'Location' => Module::getModuleURL(
                            'saml/sp/saml1-acs.php/' . $this->getAuthId() . '/artifact'
                        ),
                    ];
                    if (!in_array('urn:oasis:names:tc:SAML:1.1:protocol', $this->protocols, true)) {
                        $this->protocols[] = 'urn:oasis:names:tc:SAML:1.1:protocol';
                    }
                    break;
                case Constants::BINDING_HOK_SSO:
                    $acs = [
                        'Binding' => Constants::BINDING_HOK_SSO,
                        'Location' => Module::getModuleURL('saml/sp/saml2-acs.php/' . $this->getAuthId()),
                        'hoksso:ProtocolBinding' => Constants::BINDING_HTTP_REDIRECT,
                    ];
                    if (!in_array(Constants::NS_SAMLP, $this->protocols, true)) {
                        $this->protocols[] = Constants::NS_SAMLP;
                    }
                    break;
                default:
                    $acs = [];
            }
            $acs['index'] = $index;
            $endpoints[] = $acs;
            $index++;
        }
        return $endpoints;
    }


    /**
     * Get the SingleLogoutService endpoints available for a given local SP.
     *
     * @return array
     * @throws \SimpleSAML\Error\CriticalConfigurationError
     */
    private function getSLOEndpoints(): array
    {
        $store = Store::getInstance();
        $bindings = $this->metadata->getArray(
            'SingleLogoutServiceBinding',
            [
                Constants::BINDING_HTTP_REDIRECT,
                Constants::BINDING_SOAP,
            ]
        );
        $defaultLocation = Module::getModuleURL('saml/sp/saml2-logout.php/' . $this->getAuthId());
        $location = $this->metadata->getString('SingleLogoutServiceLocation', $defaultLocation);

        $endpoints = [];
        foreach ($bindings as $binding) {
            if ($binding == Constants::BINDING_SOAP && !($store instanceof Store\SQL)) {
                // we cannot properly support SOAP logout
                continue;
            }
            $endpoints[] = [
                'Binding' => $binding,
                'Location' => $location,
            ];
        }
        return $endpoints;
    }


    /**
     * Send a SAML1 SSO request to an IdP.
     *
     * @param \SimpleSAML\Configuration $idpMetadata  The metadata of the IdP.
     * @param array $state  The state array for the current authentication.
     * @return void
     * @deprecated will be removed in a future version
     */
    private function startSSO1(Configuration $idpMetadata, array $state): void
    {
        global $CFG;
        $idpEntityId = $idpMetadata->getString('entityid');

        $state['saml:idp'] = $idpEntityId;

        $ar = new Shib13\AuthnRequest();
        $ar->setIssuer($this->entityId);

        $id = Auth\State::saveState($state, 'saml:sp:sso');
        $ar->setRelayState($id);
        $shire = $CFG->wwwroot . '/auth/saml2/sp/saml1-acs.php/' . $this->authId;

        /* MOODLE CHANGE;
        $useArtifact = $idpMetadata->getBoolean('saml1.useartifact', null);
        if ($useArtifact === null) {
            $useArtifact = $this->metadata->getBoolean('saml1.useartifact', false);
        }

        if ($useArtifact) {
            $shire = Module::getModuleURL('saml/sp/saml1-acs.php/' . $this->authId . '/artifact');
        } else {
            $shire = Module::getModuleURL('saml/sp/saml1-acs.php/' . $this->authId);
        }
        */

        $url = $ar->createRedirect($idpEntityId, $shire);

        Logger::debug('Starting SAML 1 SSO to ' . var_export($idpEntityId, true) .
            ' from ' . var_export($this->entityId, true) . '.');
        Utils\HTTP::redirectTrustedURL($url);
    }


    /**
     * Send a SAML2 SSO request to an IdP
     *
     * @param \SimpleSAML\Configuration $idpMetadata  The metadata of the IdP.
     * @param array $state  The state array for the current authentication.
     * @return void
     */
    private function startSSO2(Configuration $idpMetadata, array $state): void
    {
        if (isset($state['saml:ProxyCount']) && $state['saml:ProxyCount'] < 0) {
            Auth\State::throwException(
                $state,
                new Module\saml\Error\ProxyCountExceeded(Constants::STATUS_RESPONDER)
            );
        }

        $ar = Module\saml\Message::buildAuthnRequest($this->metadata, $idpMetadata);

         // auth_saml2 modification
        $baseurl = \SimpleSAML\Module::getModuleURL('saml/sp/saml2-acs.php/' . $this->authId);
        $baseurl = str_replace('module.php/saml/sp/', '', $baseurl);
        $ar->setAssertionConsumerServiceURL($baseurl);

        if (isset($state['\SimpleSAML\Auth\Source.ReturnURL'])) {
            $ar->setRelayState($state['\SimpleSAML\Auth\Source.ReturnURL']);
        }

        $accr = null;
        if ($idpMetadata->getString('AuthnContextClassRef', false)) {
            $accr = Utils\Arrays::arrayize($idpMetadata->getString('AuthnContextClassRef'));
        } elseif (isset($state['saml:AuthnContextClassRef'])) {
            $accr = Utils\Arrays::arrayize($state['saml:AuthnContextClassRef']);
        }

        if ($accr !== null) {
            $comp = Constants::COMPARISON_EXACT;
            if ($idpMetadata->getString('AuthnContextComparison', false)) {
                $comp = $idpMetadata->getString('AuthnContextComparison');
            } elseif (
                isset($state['saml:AuthnContextComparison'])
                && in_array($state['saml:AuthnContextComparison'], [
                    Constants::COMPARISON_EXACT,
                    Constants::COMPARISON_MINIMUM,
                    Constants::COMPARISON_MAXIMUM,
                    Constants::COMPARISON_BETTER,
                ], true)
            ) {
                $comp = $state['saml:AuthnContextComparison'];
            }
            $ar->setRequestedAuthnContext(['AuthnContextClassRef' => $accr, 'Comparison' => $comp]);
        }

        if (isset($state['saml:Audience'])) {
            $ar->setAudiences($state['saml:Audience']);
        }

        if (isset($state['ForceAuthn'])) {
            $ar->setForceAuthn((bool) $state['ForceAuthn']);
        }

        if (isset($state['isPassive'])) {
            $ar->setIsPassive((bool) $state['isPassive']);
        }

        if (isset($state['saml:NameID'])) {
            if (!is_array($state['saml:NameID']) && !is_a($state['saml:NameID'], NameID::class)) {
                throw new Error\Exception('Invalid value of $state[\'saml:NameID\'].');
            }

            $nameId = $state['saml:NameID'];
            if (is_array($nameId)) {
                // Must be an array > convert to object

                $nid = new NameID();
                if (!array_key_exists('Value', $nameId)) {
                    throw new \InvalidArgumentException('Missing "Value" in array, cannot create NameID from it.');
                }

                $nid->setValue($nameId['Value']);
                if (array_key_exists('NameQualifier', $nameId) && $nameId['NameQualifier'] !== null) {
                    $nid->setNameQualifier($nameId['NameQualifier']);
                }
                if (array_key_exists('SPNameQualifier', $nameId) && $nameId['SPNameQualifier'] !== null) {
                    $nid->setSPNameQualifier($nameId['SPNameQualifier']);
                }
                if (array_key_exists('SPProvidedID', $nameId) && $nameId['SPProvidedId'] !== null) {
                    $nid->setSPProvidedID($nameId['SPProvidedID']);
                }
                if (array_key_exists('Format', $nameId) && $nameId['Format'] !== null) {
                    $nid->setFormat($nameId['Format']);
                }
            } else {
                $nid = $nameId;
            }

            $ar->setNameId($nid);
        }

        if (isset($state['saml:NameIDPolicy'])) {
            $policy = null;
            if (is_string($state['saml:NameIDPolicy'])) {
                $policy = [
                    'Format' => (string) $state['saml:NameIDPolicy'],
                    'AllowCreate' => true,
                ];
            } elseif (is_array($state['saml:NameIDPolicy'])) {
                $policy = $state['saml:NameIDPolicy'];
            } elseif ($state['saml:NameIDPolicy'] === null) {
                $policy = ['Format' => Constants::NAMEID_TRANSIENT];
            }
            if ($policy !== null) {
                $ar->setNameIdPolicy($policy);
            }
        }

        $IDPList = [];
        $requesterID = [];

        /* Only check for real info for Scoping element if we are going to send Scoping element */
        if ($this->disable_scoping !== true && $idpMetadata->getBoolean('disable_scoping', false) !== true) {
            if (isset($state['saml:IDPList'])) {
                $IDPList = $state['saml:IDPList'];
            }

            if (isset($state['saml:ProxyCount']) && $state['saml:ProxyCount'] !== null) {
                $ar->setProxyCount($state['saml:ProxyCount']);
            } elseif ($idpMetadata->getInteger('ProxyCount', null) !== null) {
                $ar->setProxyCount($idpMetadata->getInteger('ProxyCount', null));
            } elseif ($this->metadata->getInteger('ProxyCount', null) !== null) {
                $ar->setProxyCount($this->metadata->getInteger('ProxyCount', null));
            }

            $requesterID = [];
            if (isset($state['saml:RequesterID'])) {
                $requesterID = $state['saml:RequesterID'];
            }

            if (isset($state['core:SP'])) {
                $requesterID[] = $state['core:SP'];
            }
        } else {
            Logger::debug('Disabling samlp:Scoping for ' . var_export($idpMetadata->getString('entityid'), true));
        }

        $ar->setIDPList(
            array_unique(
                array_merge(
                    $this->metadata->getArray('IDPList', []),
                    $idpMetadata->getArray('IDPList', []),
                    (array) $IDPList
                )
            )
        );

        $ar->setRequesterID($requesterID);

        // If the downstream SP has set extensions then use them.
        // Otherwise use extensions that might be defined in the local SP (only makes sense in a proxy scenario)
        if (isset($state['saml:Extensions']) && count($state['saml:Extensions']) > 0) {
            $ar->setExtensions($state['saml:Extensions']);
        } else if ($this->metadata->getArray('saml:Extensions', null) !== null) {
            $ar->setExtensions($this->metadata->getArray('saml:Extensions'));
        }

        $providerName = $this->metadata->getString("ProviderName", null);
        if ($providerName !== null) {
            $ar->setProviderName($providerName);
        }


        // save IdP entity ID as part of the state
        $state['ExpectedIssuer'] = $idpMetadata->getString('entityid');

        $id = Auth\State::saveState($state, 'saml:sp:sso', true);
        $ar->setId($id);

        Logger::debug(
            'Sending SAML 2 AuthnRequest to ' . var_export($idpMetadata->getString('entityid'), true)
        );

        // Select appropriate SSO endpoint
        if ($ar->getProtocolBinding() === Constants::BINDING_HOK_SSO) {
            /** @var array $dst */
            $dst = $idpMetadata->getDefaultEndpoint(
                'SingleSignOnService',
                [
                    Constants::BINDING_HOK_SSO
                ]
            );
        } else {
            /** @var array $dst */
            $dst = $idpMetadata->getEndpointPrioritizedByBinding(
                'SingleSignOnService',
                [
                    Constants::BINDING_HTTP_REDIRECT,
                    Constants::BINDING_HTTP_POST,
                ]
            );
        }
        $ar->setDestination($dst['Location']);

        $b = Binding::getBinding($dst['Binding']);

        // This is a Moodle hack. Both moodle and SSPHP rely on automatic
        // destructors to cleanup the $DB var and the SSPHP session but
        // this order is not guaranteed, so we force session saving here.
        $session = \SimpleSAML\Session::getSessionFromRequest();
        $session->save();

        $this->sendSAML2AuthnRequest($state, $b, $ar);

        assert(false);
    }


    /**
     * Function to actually send the authentication request.
     *
     * This function does not return.
     *
     * @param array &$state  The state array.
     * @param \SAML2\Binding $binding  The binding.
     * @param \SAML2\AuthnRequest  $ar  The authentication request.
     * @return void
     */
    public function sendSAML2AuthnRequest(array &$state, Binding $binding, AuthnRequest $ar)
    {
        $binding->send($ar);
        assert(false);
    }


    /**
     * Send a SSO request to an IdP.
     *
     * @param string $idp  The entity ID of the IdP.
     * @param array $state  The state array for the current authentication.
     * @return void
     */
    public function startSSO($idp, array $state)
    {
        assert(is_string($idp));

        $idpMetadata = $this->getIdPMetadata($idp);

        $type = $idpMetadata->getString('metadata-set');
        switch ($type) {
            case 'shib13-idp-remote':
                $this->startSSO1($idpMetadata, $state);
                assert(false); // Should not return
            case 'saml20-idp-remote':
                $this->startSSO2($idpMetadata, $state);
                assert(false); // Should not return
            default:
                // Should only be one of the known types
                assert(false);
        }
    }


    /**
     * Start an IdP discovery service operation.
     *
     * @param array $state  The state array.
     * @return void
     */
    private function startDisco(array $state): void
    {
        $id = Auth\State::saveState($state, 'saml:sp:sso');

        $discoURL = $this->discoURL;
        if ($discoURL === null) {
            // Fallback to internal discovery service
            $discoURL = Module::getModuleURL('saml/disco.php');
        }

        $returnTo = Module::getModuleURL('saml/sp/discoresp.php', ['AuthID' => $id]);

        $params = [
            'entityID' => $this->entityId,
            'return' => $returnTo,
            'returnIDParam' => 'idpentityid'
        ];

        if (isset($state['saml:IDPList'])) {
            $params['IDPList'] = $state['saml:IDPList'];
        }

        if (isset($state['isPassive']) && $state['isPassive']) {
            $params['isPassive'] = 'true';
        }

        Utils\HTTP::redirectTrustedURL($discoURL, $params);
    }


    /**
     * Start login.
     *
     * This function saves the information about the login, and redirects to the IdP.
     *
     * @param array &$state  Information about the current authentication.
     * @return void
     */
    public function authenticate(&$state)
    {
        assert(is_array($state));

        // We are going to need the authId in order to retrieve this authentication source later
        $state['saml:sp:AuthId'] = $this->authId;

        $idp = $this->idp;

        if (isset($state['saml:idp'])) {
            $idp = (string) $state['saml:idp'];
        }

        if (isset($state['saml:IDPList']) && sizeof($state['saml:IDPList']) > 0) {
            // we have a SAML IDPList (we are a proxy): filter the list of IdPs available
            $mdh = MetaDataStorageHandler::getMetadataHandler();
            $matchedEntities = $mdh->getMetaDataForEntities($state['saml:IDPList'], 'saml20-idp-remote');

            if (empty($matchedEntities)) {
                // all requested IdPs are unknown
                throw new Module\saml\Error\NoSupportedIDP(
                    Constants::STATUS_REQUESTER,
                    'None of the IdPs requested are supported by this proxy.'
                );
            }

            if (!is_null($idp) && !array_key_exists($idp, $matchedEntities)) {
                // the IdP is enforced but not in the IDPList
                throw new Module\saml\Error\NoAvailableIDP(
                    Constants::STATUS_REQUESTER,
                    'None of the IdPs requested are available to this proxy.'
                );
            }

            if (is_null($idp) && sizeof($matchedEntities) === 1) {
                // only one IdP requested or valid
                $idp = key($matchedEntities);
            }
        }

        if ($idp === null) {
            $this->startDisco($state);
            assert(false);
        }

        $this->startSSO($idp, $state);
        assert(false);
    }


    /**
     * Re-authenticate an user.
     *
     * This function is called by the IdP to give the authentication source a chance to
     * interact with the user even in the case when the user is already authenticated.
     *
     * @param array &$state  Information about the current authentication.
     * @return void
     */
    public function reauthenticate(array &$state)
    {
        $session = Session::getSessionFromRequest();
        $data = $session->getAuthState($this->authId);
        if ($data === null) {
            throw new Error\NoState();
        }

        foreach ($data as $k => $v) {
            $state[$k] = $v;
        }

        // check if we have an IDPList specified in the request
        if (
            isset($state['saml:IDPList'])
            && sizeof($state['saml:IDPList']) > 0
            && !in_array($state['saml:sp:IdP'], $state['saml:IDPList'], true)
        ) {
            /*
             * The user has an existing, valid session. However, the SP
             * provided a list of IdPs it accepts for authentication, and
             * the IdP the existing session is related to is not in that list.
             *
             * First, check if we recognize any of the IdPs requested.
             */

            $mdh = MetaDataStorageHandler::getMetadataHandler();
            $known_idps = $mdh->getList();
            $intersection = array_intersect($state['saml:IDPList'], array_keys($known_idps));

            if (empty($intersection)) {
                // all requested IdPs are unknown
                throw new Module\saml\Error\NoSupportedIDP(
                    Constants::STATUS_REQUESTER,
                    'None of the IdPs requested are supported by this proxy.'
                );
            }

            /*
             * We have at least one IdP in the IDPList that we recognize, and
             * it's not the one currently in use. Let's see if this proxy
             * enforces the use of one single IdP.
             */
            if (!is_null($this->idp) && !in_array($this->idp, $intersection, true)) {
                // an IdP is enforced but not requested
                throw new Module\saml\Error\NoAvailableIDP(
                    Constants::STATUS_REQUESTER,
                    'None of the IdPs requested are available to this proxy.'
                );
            }

            /*
             * We need to inform the user, and ask whether we should logout before
             * starting the authentication process again with a different IdP, or
             * cancel the current SSO attempt.
             */
            Logger::warning(
                "Reauthentication after logout is needed. The IdP '${state['saml:sp:IdP']}' is not in the IDPList " .
                "provided by the Service Provider '${state['core:SP']}'."
            );

            $state['saml:sp:IdPMetadata'] = $this->getIdPMetadata($state['saml:sp:IdP']);
            $state['saml:sp:AuthId'] = $this->authId;
            self::askForIdPChange($state);
        }
    }


    /**
     * Ask the user to log out before being able to log in again with a
     * different identity provider. Note that this method is intended for
     * instances of SimpleSAMLphp running as a SAML proxy, and therefore
     * acting both as an SP and an IdP at the same time.
     *
     * This method will never return.
     *
     * @param array $state The state array.
     * The following keys must be defined in the array:
     * - 'saml:sp:IdPMetadata': a \SimpleSAML\Configuration object containing
     *   the metadata of the IdP that authenticated the user in the current
     *   session.
     * - 'saml:sp:AuthId': the identifier of the current authentication source.
     * - 'core:IdP': the identifier of the local IdP.
     * - 'SPMetadata': an array with the metadata of this local SP.
     *
     * @return void
     * @throws \SimpleSAML\Error\NoPassive In case the authentication request was passive.
     */
    public static function askForIdPChange(array &$state)
    {
        assert(array_key_exists('saml:sp:IdPMetadata', $state));
        assert(array_key_exists('saml:sp:AuthId', $state));
        assert(array_key_exists('core:IdP', $state));
        assert(array_key_exists('SPMetadata', $state));

        if (isset($state['isPassive']) && (bool) $state['isPassive']) {
            // passive request, we cannot authenticate the user
            throw new Module\saml\Error\NoPassive(
                Constants::STATUS_REQUESTER,
                'Reauthentication required'
            );
        }

        // save the state WITHOUT a restart URL, so that we don't try an IdP-initiated login if something goes wrong
        $id = Auth\State::saveState($state, 'saml:proxy:invalid_idp', true);
        $url = Module::getModuleURL('saml/proxy/invalid_session.php');
        Utils\HTTP::redirectTrustedURL($url, ['AuthState' => $id]);
        assert(false);
    }


    /**
     * Log the user out before logging in again.
     *
     * This method will never return.
     *
     * @param array $state The state array.
     * @return void
     */
    public static function reauthLogout(array $state)
    {
        Logger::debug('Proxy: logging the user out before re-authentication.');

        if (isset($state['Responder'])) {
            $state['saml:proxy:reauthLogout:PrevResponder'] = $state['Responder'];
        }
        $state['Responder'] = [SP::class, 'reauthPostLogout'];

        $idp = IdP::getByState($state);
        $idp->handleLogoutRequest($state, null);
        assert(false);
    }


    /**
     * Complete login operation after re-authenticating the user on another IdP.
     *
     * @param array $state  The authentication state.
     * @return void
     */
    public static function reauthPostLogin(array $state)
    {
        assert(isset($state['ReturnCallback']));

        // Update session state
        $session = Session::getSessionFromRequest();
        $authId = $state['saml:sp:AuthId'];
        $session->doLogin($authId, Auth\State::getPersistentAuthData($state));

        // resume the login process
        call_user_func($state['ReturnCallback'], $state);
        assert(false);
    }


    /**
     * Post-logout handler for re-authentication.
     *
     * This method will never return.
     *
     * @param \SimpleSAML\IdP $idp The IdP we are logging out from.
     * @param array &$state The state array with the state during logout.
     * @return void
     */
    public static function reauthPostLogout(IdP $idp, array $state)
    {
        assert(isset($state['saml:sp:AuthId']));

        Logger::debug('Proxy: logout completed.');

        if (isset($state['saml:proxy:reauthLogout:PrevResponder'])) {
            $state['Responder'] = $state['saml:proxy:reauthLogout:PrevResponder'];
        }

        /** @var \SimpleSAML\Module\saml\Auth\Source\SP $sp */
        $sp = Auth\Source::getById($state['saml:sp:AuthId'], Module\saml\Auth\Source\SP::class);

        Logger::debug('Proxy: logging in again.');
        $sp->authenticate($state);
        assert(false);
    }


    /**
     * Start a SAML 2 logout operation.
     *
     * @param array $state  The logout state.
     * @return void
     */
    public function startSLO2(&$state)
    {
        assert(is_array($state));
        assert(array_key_exists('saml:logout:IdP', $state));
        assert(array_key_exists('saml:logout:NameID', $state));
        assert(array_key_exists('saml:logout:SessionIndex', $state));

        $id = Auth\State::saveState($state, 'saml:slosent');

        $idp = $state['saml:logout:IdP'];
        $nameId = $state['saml:logout:NameID'];
        $sessionIndex = $state['saml:logout:SessionIndex'];

        $idpMetadata = $this->getIdPMetadata($idp);

        /** @var array $endpoint */
        $endpoint = $idpMetadata->getEndpointPrioritizedByBinding(
            'SingleLogoutService',
            [
                Constants::BINDING_HTTP_REDIRECT,
                Constants::BINDING_HTTP_POST
            ],
            false
        );
        if ($endpoint === false) {
            Logger::info('No logout endpoint for IdP ' . var_export($idp, true) . '.');
            return;
        }

        $lr = Module\saml\Message::buildLogoutRequest($this->metadata, $idpMetadata);
        $lr->setNameId($nameId);
        $lr->setSessionIndex($sessionIndex);
        $lr->setRelayState($id);
        $lr->setDestination($endpoint['Location']);

        $encryptNameId = $idpMetadata->getBoolean('nameid.encryption', null);
        if ($encryptNameId === null) {
            $encryptNameId = $this->metadata->getBoolean('nameid.encryption', false);
        }
        if ($encryptNameId) {
            $lr->encryptNameId(Module\saml\Message::getEncryptionKey($idpMetadata));
        }

        $b = Binding::getBinding($endpoint['Binding']);
        $b->send($lr);

        assert(false);
    }


    /**
     * Start logout operation.
     *
     * @param array $state  The logout state.
     * @return void
     */
    public function logout(&$state)
    {
        assert(is_array($state));
        assert(array_key_exists('saml:logout:Type', $state));

        $logoutType = $state['saml:logout:Type'];
        switch ($logoutType) {
            case 'saml1':
                // Nothing to do
                return;
            case 'saml2':
                $this->startSLO2($state);
                return;
            default:
                // Should never happen
                assert(false);
        }
    }


    /**
     * Handle a response from a SSO operation.
     *
     * @param array $state  The authentication state.
     * @param string $idp  The entity id of the IdP.
     * @param array $attributes  The attributes.
     * @return void
     */
    public function handleResponse(array $state, $idp, array $attributes)
    {
        assert(is_string($idp));
        assert(array_key_exists('LogoutState', $state));
        assert(array_key_exists('saml:logout:Type', $state['LogoutState']));

        $idpMetadata = $this->getIdPMetadata($idp);

        $spMetadataArray = $this->metadata->toArray();
        $idpMetadataArray = $idpMetadata->toArray();

        /* Save the IdP in the state array. */
        $state['saml:sp:IdP'] = $idp;
        $state['PersistentAuthData'][] = 'saml:sp:IdP';

        $authProcState = [
            'saml:sp:IdP' => $idp,
            'saml:sp:State' => $state,
            'ReturnCall' => [SP::class, 'onProcessingCompleted'],

            'Attributes' => $attributes,
            'Destination' => $spMetadataArray,
            'Source' => $idpMetadataArray,
        ];

        if (isset($state['saml:sp:NameID'])) {
            $authProcState['saml:sp:NameID'] = $state['saml:sp:NameID'];
        }
        if (isset($state['saml:sp:SessionIndex'])) {
            $authProcState['saml:sp:SessionIndex'] = $state['saml:sp:SessionIndex'];
        }

        $pc = new Auth\ProcessingChain($idpMetadataArray, $spMetadataArray, 'sp');
        $pc->processState($authProcState);

        self::onProcessingCompleted($authProcState);
    }


    /**
     * Handle a logout request from an IdP.
     *
     * @param string $idpEntityId  The entity ID of the IdP.
     * @return void
     */
    public function handleLogout($idpEntityId)
    {
        assert(is_string($idpEntityId));

        /* Call the logout callback we registered in onProcessingCompleted(). */
        $this->callLogoutCallback($idpEntityId);
    }


    /**
     * Handle an unsolicited login operations.
     *
     * This method creates a session from the information received. It will
     * then redirect to the given URL. This is used to handle IdP initiated
     * SSO. This method will never return.
     *
     * @param string $authId The id of the authentication source that received the request.
     * @param array $state A state array.
     * @param string $redirectTo The URL we should redirect the user to after updating
     * the session. The function will check if the URL is allowed, so there is no need to
     * manually check the URL on beforehand. Please refer to the 'trusted.url.domains'
     * configuration directive for more information about allowing (or disallowing) URLs.
     * @return void
     */
    public static function handleUnsolicitedAuth($authId, array $state, $redirectTo)
    {
        global $SESSION, $saml2auth;
        assert(is_string($authId));
        assert(is_string($redirectTo));

        $session = Session::getSessionFromRequest();
        $session->doLogin($authId, Auth\State::getPersistentAuthData($state));

        // Moodle hack to handle IdP unsolicited logins.
        $wantsurl = (new \moodle_url($redirectTo))->out(false);
        $SESSION->wantsurl = $wantsurl;
        $saml2auth->saml_login_complete($state['Attributes']);
        // Should never get to here.

        Utils\HTTP::redirectUntrustedURL($redirectTo);
    }


    /**
     * Called when we have completed the procssing chain.
     *
     * @param array $authProcState  The processing chain state.
     * @return void
     */
    public static function onProcessingCompleted(array $authProcState)
    {
        assert(array_key_exists('saml:sp:IdP', $authProcState));
        assert(array_key_exists('saml:sp:State', $authProcState));
        assert(array_key_exists('Attributes', $authProcState));

        $idp = $authProcState['saml:sp:IdP'];
        $state = $authProcState['saml:sp:State'];

        $sourceId = $state['saml:sp:AuthId'];

        /** @var \SimpleSAML\Module\saml\Auth\Source\SP $source */
        $source = Auth\Source::getById($sourceId);
        if ($source === null) {
            throw new \Exception('Could not find authentication source with id ' . $sourceId);
        }

        // Register a callback that we can call if we receive a logout request from the IdP
        $source->addLogoutCallback($idp, $state);

        $state['Attributes'] = $authProcState['Attributes'];

        if (isset($state['saml:sp:isUnsolicited']) && (bool) $state['saml:sp:isUnsolicited']) {
            if (!empty($state['saml:sp:RelayState'])) {
                $redirectTo = $state['saml:sp:RelayState'];
            } else {
                $redirectTo = $source->getMetadata()->getString('RelayState', '/');
            }
            self::handleUnsolicitedAuth($sourceId, $state, $redirectTo);
        }

        Auth\Source::completeAuth($state);
    }
}
