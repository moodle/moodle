<?php

namespace SimpleSAML\Module\adfs\IdP;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use SAML2\Constants;

use SimpleSAML\Module;
use SimpleSAML\Utils\Config\Metadata;
use SimpleSAML\Utils\Crypto;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\Utils\Time;

class ADFS
{
    /**
     * @param \SimpleSAML\IdP $idp
     * @return void
     * @throws \SimpleSAML\Error\Error
     */
    public static function receiveAuthnRequest(\SimpleSAML\IdP $idp)
    {
        try {
            parse_str($_SERVER['QUERY_STRING'], $query);

            $requestid = $query['wctx'];
            $issuer = $query['wtrealm'];

            $metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();
            $spMetadata = $metadata->getMetaDataConfig($issuer, 'adfs-sp-remote');

            \SimpleSAML\Logger::info('ADFS - IdP.prp: Incoming Authentication request: '.$issuer.' id '.$requestid);
        } catch (\Exception $exception) {
            throw new \SimpleSAML\Error\Error('PROCESSAUTHNREQUEST', $exception);
        }

        $state = [
            'Responder' => ['\SimpleSAML\Module\adfs\IdP\ADFS', 'sendResponse'],
            'SPMetadata' => $spMetadata->toArray(),
            'ForceAuthn' => false,
            'isPassive' => false,
            'adfs:wctx' => $requestid,
            'adfs:wreply' => false
        ];

        if (isset($query['wreply']) && !empty($query['wreply'])) {
            $state['adfs:wreply'] = HTTP::checkURLAllowed($query['wreply']);
        }

        $idp->handleAuthenticationRequest($state);
    }


    /**
     * @param string $issuer
     * @param string $target
     * @param string $nameid
     * @param array $attributes
     * @param int $assertionLifetime
     * @return string
     */
    private static function generateResponse($issuer, $target, $nameid, $attributes, $assertionLifetime)
    {
        $issueInstant = Time::generateTimestamp();
        $notBefore = Time::generateTimestamp(time() - 30);
        $assertionExpire = Time::generateTimestamp(time() + $assertionLifetime);
        $assertionID = \SimpleSAML\Utils\Random::generateID();
        $nameidFormat = 'http://schemas.xmlsoap.org/claims/UPN';
        $nameid = htmlspecialchars($nameid);

        if (HTTP::isHTTPS()) {
            $method = Constants::AC_PASSWORD_PROTECTED_TRANSPORT;
        } else {
            $method = Constants::AC_PASSWORD;
        }

        $result = <<<MSG
<wst:RequestSecurityTokenResponse xmlns:wst="http://schemas.xmlsoap.org/ws/2005/02/trust">
    <wst:RequestedSecurityToken>
        <saml:Assertion Issuer="$issuer" IssueInstant="$issueInstant" AssertionID="$assertionID" MinorVersion="1" MajorVersion="1" xmlns:saml="urn:oasis:names:tc:SAML:1.0:assertion">
            <saml:Conditions NotOnOrAfter="$assertionExpire" NotBefore="$notBefore">
                <saml:AudienceRestrictionCondition>
                    <saml:Audience>$target</saml:Audience>
                </saml:AudienceRestrictionCondition>
            </saml:Conditions>
            <saml:AuthenticationStatement AuthenticationMethod="$method" AuthenticationInstant="$issueInstant">
                <saml:Subject>
                    <saml:NameIdentifier Format="$nameidFormat">$nameid</saml:NameIdentifier>
                </saml:Subject>
            </saml:AuthenticationStatement>
            <saml:AttributeStatement>
                <saml:Subject>
                    <saml:NameIdentifier Format="$nameidFormat">$nameid</saml:NameIdentifier>
                </saml:Subject>
MSG;

        foreach ($attributes as $name => $values) {
            if ((!is_array($values)) || (count($values) == 0)) {
                continue;
            }

            list($namespace, $name) = \SimpleSAML\Utils\Attributes::getAttributeNamespace(
                $name,
                'http://schemas.xmlsoap.org/claims'
            );
            foreach ($values as $value) {
                if ((!isset($value)) || ($value === '')) {
                    continue;
                }
                $value = htmlspecialchars($value);

                $result .= <<<MSG
                <saml:Attribute AttributeNamespace="$namespace" AttributeName="$name">
                    <saml:AttributeValue>$value</saml:AttributeValue>
                </saml:Attribute>
MSG;
            }
        }

        $result .= <<<MSG
            </saml:AttributeStatement>
        </saml:Assertion>
   </wst:RequestedSecurityToken>
   <wsp:AppliesTo xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
       <wsa:EndpointReference xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing">
           <wsa:Address>$target</wsa:Address>
       </wsa:EndpointReference>
   </wsp:AppliesTo>
</wst:RequestSecurityTokenResponse>
MSG;

        return $result;
    }


    /**
     * @param string $response
     * @param string $key
     * @param string $cert
     * @param string $algo
     * @return string
     */
    private static function signResponse($response, $key, $cert, $algo, $passphrase)
    {
        $objXMLSecDSig = new XMLSecurityDSig();
        $objXMLSecDSig->idKeys = ['AssertionID'];
        $objXMLSecDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $responsedom = \SAML2\DOMDocumentFactory::fromString(str_replace("\r", "", $response));
        $firstassertionroot = $responsedom->getElementsByTagName('Assertion')->item(0);

        if (is_null($firstassertionroot)) {
            throw new \Exception("No assertion found in response.");
        }

        $objXMLSecDSig->addReferenceList(
            [$firstassertionroot],
            XMLSecurityDSig::SHA256,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::EXC_C14N],
            ['id_name' => 'AssertionID']
        );

        $objKey = new XMLSecurityKey($algo, ['type' => 'private']);
        if (is_string($passphrase)) {
            $objKey->passphrase = $passphrase;
        }
        $objKey->loadKey($key, true);
        $objXMLSecDSig->sign($objKey);
        if ($cert) {
            $public_cert = file_get_contents($cert);
            $objXMLSecDSig->add509Cert($public_cert, true);
        }

        /** @var \DOMElement $objXMLSecDSig->sigNode */
        $newSig = $responsedom->importNode($objXMLSecDSig->sigNode, true);
        $firstassertionroot->appendChild($newSig);
        return $responsedom->saveXML();
    }


    /**
     * @param string $url
     * @param string $wresult
     * @param string $wctx
     * @return void
     */
    private static function postResponse($url, $wresult, $wctx)
    {
        $wresult = htmlspecialchars($wresult);
        $wctx = htmlspecialchars($wctx);
        $javaScript = Module::getModuleURL('adfs/assets/js/postResponse.js');

        $post = <<<MSG
<!DOCTYPE html>
<html>
    <head>
        <script src="$javaScript"></script>
    </head>
    <body>
        <form method="post" action="$url">
            <input type="hidden" name="wa" value="wsignin1.0">
            <input type="hidden" name="wresult" value="$wresult">
            <input type="hidden" name="wctx" value="$wctx">
            <noscript>
                <input type="submit" value="Continue">
            </noscript>
        </form>
    </body>
</html>
MSG;

        echo $post;
        exit;
    }


    /**
     * Get the metadata of a given hosted ADFS IdP.
     *
     * @param string $entityid The entity ID of the hosted ADFS IdP whose metadata we want to fetch.
     *
     * @return array
     * @throws \SimpleSAML\Error\Exception
     * @throws \SimpleSAML\Error\MetadataNotFound
     */
    public static function getHostedMetadata($entityid)
    {
        $handler = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();
        $config = $handler->getMetaDataConfig($entityid, 'adfs-idp-hosted');

        $endpoint = \SimpleSAML\Module::getModuleURL('adfs/idp/prp.php');
        $metadata = [
            'metadata-set' => 'adfs-idp-hosted',
            'entityid' => $entityid,
            'SingleSignOnService' => [
                [
                    'Binding' => Constants::BINDING_HTTP_REDIRECT,
                    'Location' => $endpoint,
                ]
            ],
            'SingleLogoutService' => [
                'Binding' => Constants::BINDING_HTTP_REDIRECT,
                'Location' => $endpoint,
            ],
            'NameIDFormat' => $config->getString('NameIDFormat', Constants::NAMEID_TRANSIENT),
            'contacts' => [],
        ];

        // add certificates
        $keys = [];
        $certInfo = Crypto::loadPublicKey($config, false, 'new_');
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
        $certInfo = Crypto::loadPublicKey($config, true);
        $keys[] = [
            'type' => 'X509Certificate',
            'signing' => true,
            'encryption' => $hasNewCert === false,
            'X509Certificate' => $certInfo['certData'],
            'prefix' => '',
        ];

        if ($config->hasValue('https.certificate')) {
            /** @var array $httpsCert */
            $httpsCert = Crypto::loadPublicKey($config, true, 'https.');
            $keys[] = [
                'type' => 'X509Certificate',
                'signing' => true,
                'encryption' => false,
                'X509Certificate' => $httpsCert['certData'],
                'prefix' => 'https.'
            ];
        }
        $metadata['keys'] = $keys;

        // add organization information
        if ($config->hasValue('OrganizationName')) {
            $metadata['OrganizationName'] = $config->getLocalizedString('OrganizationName');
            $metadata['OrganizationDisplayName'] = $config->getLocalizedString(
                'OrganizationDisplayName',
                $metadata['OrganizationName']
            );

            if (!$config->hasValue('OrganizationURL')) {
                throw new \SimpleSAML\Error\Exception('If OrganizationName is set, OrganizationURL must also be set.');
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
            if (Metadata::isHiddenFromDiscovery($metadata)) {
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

        // add contact information
        $globalConfig = \SimpleSAML\Configuration::getInstance();
        $email = $globalConfig->getString('technicalcontact_email', false);
        if ($email && $email !== 'na@example.org') {
            $contact = [
                'emailAddress' => $email,
                'name' => $globalConfig->getString('technicalcontact_name', null),
                'contactType' => 'technical',
            ];
            $metadata['contacts'][] = Metadata::getContact($contact);
        }

        return $metadata;
    }


    /**
     * @param array $state
     * @throws \Exception
     * @return void
     */
    public static function sendResponse(array $state)
    {
        $spMetadata = $state["SPMetadata"];
        $spEntityId = $spMetadata['entityid'];
        $spMetadata = \SimpleSAML\Configuration::loadFromArray(
            $spMetadata,
            '$metadata['.var_export($spEntityId, true).']'
        );

        $attributes = $state['Attributes'];

        $nameidattribute = $spMetadata->getValue('simplesaml.nameidattribute');
        if (!empty($nameidattribute)) {
            if (!array_key_exists($nameidattribute, $attributes)) {
                throw new \Exception('simplesaml.nameidattribute does not exist in resulting attribute set');
            }
            $nameid = $attributes[$nameidattribute][0];
        } else {
            $nameid = \SimpleSAML\Utils\Random::generateID();
        }

        $idp = \SimpleSAML\IdP::getByState($state);
        $idpMetadata = $idp->getConfig();
        $idpEntityId = $idpMetadata->getString('entityid');

        $idp->addAssociation([
            'id' => 'adfs:'.$spEntityId,
            'Handler' => '\SimpleSAML\Module\adfs\IdP\ADFS',
            'adfs:entityID' => $spEntityId,
        ]);

        $assertionLifetime = $spMetadata->getInteger('assertion.lifetime', null);
        if ($assertionLifetime === null) {
            $assertionLifetime = $idpMetadata->getInteger('assertion.lifetime', 300);
        }

        $response = ADFS::generateResponse($idpEntityId, $spEntityId, $nameid, $attributes, $assertionLifetime);

        $privateKeyFile = \SimpleSAML\Utils\Config::getCertPath($idpMetadata->getString('privatekey'));
        $certificateFile = \SimpleSAML\Utils\Config::getCertPath($idpMetadata->getString('certificate'));
        $passphrase = $idpMetadata->getString('privatekey_pass', null);

        $algo = $spMetadata->getString('signature.algorithm', null);
        if ($algo === null) {
            $algo = $idpMetadata->getString('signature.algorithm', XMLSecurityKey::RSA_SHA256);
        }
        $wresult = ADFS::signResponse($response, $privateKeyFile, $certificateFile, $algo, $passphrase);

        $wctx = $state['adfs:wctx'];
        $wreply = $state['adfs:wreply'] ? : $spMetadata->getValue('prp');
        ADFS::postResponse($wreply, $wresult, $wctx);
    }


    /**
     * @param \SimpleSAML\IdP $idp
     * @param array $state
     * @return void
     */
    public static function sendLogoutResponse(\SimpleSAML\IdP $idp, array $state)
    {
        // NB:: we don't know from which SP the logout request came from
        $idpMetadata = $idp->getConfig();
        HTTP::redirectTrustedURL(
            $idpMetadata->getValue('redirect-after-logout', HTTP::getBaseURL())
        );
    }


    /**
     * @param \SimpleSAML\IdP $idp
     * @throws \Exception
     * @return void
     */
    public static function receiveLogoutMessage(\SimpleSAML\IdP $idp)
    {
        // if a redirect is to occur based on wreply, we will redirect to url as
        // this implies an override to normal sp notification
        if (isset($_GET['wreply']) && !empty($_GET['wreply'])) {
            $idp->doLogoutRedirect(HTTP::checkURLAllowed($_GET['wreply']));
            throw new \Exception("Code should never be reached");
        }

        $state = [
            'Responder' => ['\SimpleSAML\Module\adfs\IdP\ADFS', 'sendLogoutResponse'],
        ];
        $assocId = null;
        // TODO: verify that this is really no problem for:
        //       a) SSP, because there's no caller SP.
        //       b) ADFS SP because caller will be called back..
        $idp->handleLogoutRequest($state, $assocId);
    }


    /**
     * accepts an association array, and returns a URL that can be accessed to terminate the association
     *
     * @param \SimpleSAML\IdP $idp
     * @param array $association
     * @param string $relayState
     * @return string
     */
    public static function getLogoutURL(\SimpleSAML\IdP $idp, array $association, $relayState)
    {
        $metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();
        $spMetadata = $metadata->getMetaDataConfig($association['adfs:entityID'], 'adfs-sp-remote');
        $returnTo = \SimpleSAML\Module::getModuleURL(
            'adfs/idp/prp.php?assocId='.urlencode($association["id"]).'&relayState='.urlencode($relayState)
        );
        return $spMetadata->getValue('prp').'?wa=wsignoutcleanup1.0&wreply='.urlencode($returnTo);
    }
}
