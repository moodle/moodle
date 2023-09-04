<?php

declare(strict_types=1);

namespace SimpleSAML\Metadata;

use DOMDocument;
use DOMElement;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\SignedElementHelper;
use SAML2\XML\Chunk;
use SAML2\XML\ds\X509Certificate;
use SAML2\XML\ds\X509Data;
use SAML2\XML\md\AttributeAuthorityDescriptor;
use SAML2\XML\md\AttributeConsumingService;
use SAML2\XML\md\ContactPerson;
use SAML2\XML\md\EndpointType;
use SAML2\XML\md\EntityDescriptor;
use SAML2\XML\md\EntitiesDescriptor;
use SAML2\XML\md\IDPSSODescriptor;
use SAML2\XML\md\IndexedEndpointType;
use SAML2\XML\md\KeyDescriptor;
use SAML2\XML\md\Organization;
use SAML2\XML\md\RoleDescriptor;
use SAML2\XML\md\SPSSODescriptor;
use SAML2\XML\md\SSODescriptorType;
use SAML2\XML\mdattr\EntityAttributes;
use SAML2\XML\mdrpi\RegistrationInfo;
use SAML2\XML\mdui\DiscoHints;
use SAML2\XML\mdui\Keywords;
use SAML2\XML\mdui\Logo;
use SAML2\XML\mdui\UIInfo;
use SAML2\XML\saml\Attribute;
use SAML2\XML\shibmd\Scope;
use SimpleSAML\Logger;
use SimpleSAML\Utils;

/**
 * This is class for parsing of SAML 1.x and SAML 2.0 metadata.
 *
 * Metadata is loaded by calling the static methods parseFile, parseString or parseElement.
 * These functions returns an instance of SAMLParser. To get metadata
 * from this object, use the methods getMetadata1xSP or getMetadata20SP.
 *
 * To parse a file which can contain a collection of EntityDescriptor or EntitiesDescriptor elements, use the
 * parseDescriptorsFile, parseDescriptorsString or parseDescriptorsElement methods. These functions will return
 * an array of SAMLParser elements where each element represents an EntityDescriptor-element.
 */

class SAMLParser
{
    /**
     * This is the list of SAML 1.x protocols.
     *
     * @var string[]
     */
    private static $SAML1xProtocols = [
        'urn:oasis:names:tc:SAML:1.0:protocol',
        'urn:oasis:names:tc:SAML:1.1:protocol',
    ];

    /**
     * This is the list with the SAML 2.0 protocol.
     *
     * @var string[]
     */
    private static $SAML20Protocols = [
        Constants::NS_SAMLP,
    ];

    /**
     * This is the entity id we find in the metadata.
     *
     * @var string
     */
    private $entityId;

    /**
     * This is an array with the processed SPSSODescriptor elements we have found in this
     * metadata file.
     * Each element in the array is an associative array with the elements from parseSSODescriptor and:
     * - 'AssertionConsumerService': Array with the SP's assertion consumer services.
     *   Each assertion consumer service is stored as an associative array with the
     *   elements that parseGenericEndpoint returns.
     *
     * @var array[]
     */
    private $spDescriptors;

    /**
     * This is an array with the processed IDPSSODescriptor elements we have found.
     * Each element in the array is an associative array with the elements from parseSSODescriptor and:
     * - 'SingleSignOnService': Array with the IdP's single sign on service endpoints. Each endpoint is stored
     *   as an associative array with the elements that parseGenericEndpoint returns.
     *
     * @var array[]
     */
    private $idpDescriptors;

    /**
     * List of attribute authorities we have found.
     *
     * @var array
     */
    private $attributeAuthorityDescriptors = [];

    /**
     * This is an associative array with the organization name for this entity. The key of
     * the associative array is the language code, while the value is a string with the
     * organization name.
     *
     * @var string[]
     */
    private $organizationName = [];

    /**
     * This is an associative array with the organization display name for this entity. The key of
     * the associative array is the language code, while the value is a string with the
     * organization display name.
     *
     * @var string[]
     */
    private $organizationDisplayName = [];

    /**
     * This is an associative array with the organization URI for this entity. The key of
     * the associative array is the language code, while the value is the URI.
     *
     * @var string[]
     */
    private $organizationURL = [];

    /**
     * This is an array of the Contact Persons of this entity.
     *
     * @var array[]
     */
    private $contacts = [];

    /**
     * @var array
     */
    private $scopes;

    /**
     * @var array
     */
    private $entityAttributes;

    /**
     * An associative array of attributes from the RegistrationInfo element.
     * @var array
     */
    private $registrationInfo;

    /**
     * @var array
     */
    private $tags;

    /**
     * This is an array of elements that may be used to validate this element.
     *
     * @var \SAML2\SignedElementHelper[]
     */
    private $validators = [];

    /**
     * The original EntityDescriptor element for this entity, as a base64 encoded string.
     *
     * @var string
     */
    private $entityDescriptor;


    /**
     * This is the constructor for the SAMLParser class.
     *
     * @param \SAML2\XML\md\EntityDescriptor $entityElement The EntityDescriptor.
     * @param int|null                      $maxExpireTime The unix timestamp for when this entity should expire, or
     *     NULL if unknown.
     * @param array                         $validators An array of parent elements that may validate this element.
     * @param array                         $parentExtensions An optional array of extensions from the parent element.
     */
    private function __construct(
        EntityDescriptor $entityElement,
        int $maxExpireTime = null,
        array $validators = [],
        array $parentExtensions = []
    ) {
        $this->spDescriptors = [];
        $this->idpDescriptors = [];

        $e = $entityElement->toXML();
        $e = $e->ownerDocument->saveXML($e);
        $this->entityDescriptor = base64_encode($e);
        $this->entityId = $entityElement->getEntityID();

        $expireTime = self::getExpireTime($entityElement, $maxExpireTime);

        $this->validators = $validators;
        $this->validators[] = $entityElement;

        // process Extensions element, if it exists
        $ext = self::processExtensions($entityElement, $parentExtensions);
        $this->scopes = $ext['scope'];
        $this->tags = $ext['tags'];
        $this->entityAttributes = $ext['EntityAttributes'];
        $this->registrationInfo = $ext['RegistrationInfo'];

        // look over the RoleDescriptors
        foreach ($entityElement->getRoleDescriptor() as $child) {
            if ($child instanceof SPSSODescriptor) {
                $this->processSPSSODescriptor($child, $expireTime);
            } elseif ($child instanceof IDPSSODescriptor) {
                $this->processIDPSSODescriptor($child, $expireTime);
            } elseif ($child instanceof AttributeAuthorityDescriptor) {
                $this->processAttributeAuthorityDescriptor($child, $expireTime);
            }
        }

        $organization = $entityElement->getOrganization();
        if ($organization !== null) {
            $this->processOrganization($organization);
        }

        if ($entityElement->getContactPerson() !== []) {
            foreach ($entityElement->getContactPerson() as $contact) {
                $this->processContactPerson($contact);
            }
        }
    }


    /**
     * This function parses a file which contains XML encoded metadata.
     *
     * @param string $file The path to the file which contains the metadata.
     *
     * @return SAMLParser An instance of this class with the metadata loaded.
     * @throws \Exception If the file does not parse as XML.
     */
    public static function parseFile($file)
    {
        /** @var string $data */
        $data = Utils\HTTP::fetch($file);

        try {
            $doc = DOMDocumentFactory::fromString($data);
        } catch (\Exception $e) {
            throw new \Exception('Failed to read XML from file: ' . $file);
        }

        return self::parseDocument($doc);
    }


    /**
     * This function parses a string which contains XML encoded metadata.
     *
     * @param string $metadata A string which contains XML encoded metadata.
     *
     * @return SAMLParser An instance of this class with the metadata loaded.
     * @throws \Exception If the string does not parse as XML.
     */
    public static function parseString($metadata)
    {
        try {
            $doc = DOMDocumentFactory::fromString($metadata);
        } catch (\Exception $e) {
            throw new \Exception('Failed to parse XML string.');
        }

        return self::parseDocument($doc);
    }


    /**
     * This function parses a \DOMDocument which is assumed to contain a single EntityDescriptor element.
     *
     * @param \DOMDocument $document The \DOMDocument which contains the EntityDescriptor element.
     *
     * @return SAMLParser An instance of this class with the metadata loaded.
     */
    public static function parseDocument($document)
    {
        assert($document instanceof DOMDocument);

        $entityElement = self::findEntityDescriptor($document);

        return self::parseElement($entityElement);
    }


    /**
     * This function parses a \SAML2\XML\md\EntityDescriptor object which represents a EntityDescriptor element.
     *
     * @param \SAML2\XML\md\EntityDescriptor $entityElement A \SAML2\XML\md\EntityDescriptor object which represents a
     *     EntityDescriptor element.
     *
     * @return SAMLParser An instance of this class with the metadata loaded.
     */
    public static function parseElement($entityElement)
    {
        assert($entityElement instanceof EntityDescriptor);
        return new SAMLParser($entityElement, null, []);
    }


    /**
     * This function parses a file where the root node is either an EntityDescriptor element or an
     * EntitiesDescriptor element. In both cases it will return an associative array of SAMLParser instances. If
     * the file contains a single EntityDescriptorElement, then the array will contain a single SAMLParser
     * instance.
     *
     * @param string|null $file The path to the file which contains the EntityDescriptor or EntitiesDescriptor element.
     *
     * @return SAMLParser[] An array of SAMLParser instances.
     * @throws \Exception If the file does not parse as XML.
     */
    public static function parseDescriptorsFile($file)
    {
        if ($file === null) {
            throw new \Exception('Cannot open file NULL. File name not specified.');
        }

        /** @var string $data */
        $data = Utils\HTTP::fetch($file);

        try {
            $doc = DOMDocumentFactory::fromString($data);
        } catch (\Exception $e) {
            throw new \Exception('Failed to read XML from file: ' . $file);
        }

        return self::parseDescriptorsElement($doc->documentElement);
    }


    /**
     * This function parses a string with XML data. The root node of the XML data is expected to be either an
     * EntityDescriptor element or an EntitiesDescriptor element. It will return an associative array of
     * SAMLParser instances.
     *
     * @param string $string The string with XML data.
     *
     * @return SAMLParser[] An associative array of SAMLParser instances. The key of the array will
     *     be the entity id.
     * @throws \Exception If the string does not parse as XML.
     */
    public static function parseDescriptorsString($string)
    {
        try {
            $doc = DOMDocumentFactory::fromString($string);
        } catch (\Exception $e) {
            throw new \Exception('Failed to parse XML string.');
        }

        return self::parseDescriptorsElement($doc->documentElement);
    }


    /**
     * This function parses a DOMElement which represents either an EntityDescriptor element or an
     * EntitiesDescriptor element. It will return an associative array of SAMLParser instances in both cases.
     *
     * @param \DOMElement|NULL $element The DOMElement which contains the EntityDescriptor element or the
     *     EntitiesDescriptor element.
     *
     * @return SAMLParser[] An associative array of SAMLParser instances. The key of the array will
     *     be the entity id.
     * @throws \Exception if the document is empty or the root is an unexpected node.
     */
    public static function parseDescriptorsElement(DOMElement $element = null)
    {
        if ($element === null) {
            throw new \Exception('Document was empty.');
        }

        if (Utils\XML::isDOMNodeOfType($element, 'EntityDescriptor', '@md') === true) {
            return self::processDescriptorsElement(new EntityDescriptor($element));
        } elseif (Utils\XML::isDOMNodeOfType($element, 'EntitiesDescriptor', '@md') === true) {
            return self::processDescriptorsElement(new EntitiesDescriptor($element));
        } else {
            throw new \Exception('Unexpected root node: [' . $element->namespaceURI . ']:' . $element->localName);
        }
    }


    /**
     *
     * @param \SAML2\XML\md\EntityDescriptor|\SAML2\XML\md\EntitiesDescriptor $element The element we should process.
     * @param int|NULL              $maxExpireTime The maximum expiration time of the entities.
     * @param array                 $validators The parent-elements that may be signed.
     * @param array                 $parentExtensions An optional array of extensions from the parent element.
     *
     * @return SAMLParser[] Array of SAMLParser instances.
     */
    private static function processDescriptorsElement(
        SignedElementHelper $element,
        int $maxExpireTime = null,
        array $validators = [],
        array $parentExtensions = []
    ): array {
        if ($element instanceof EntityDescriptor) {
            $ret = new SAMLParser($element, $maxExpireTime, $validators, $parentExtensions);
            $ret = [$ret->getEntityId() => $ret];
            /** @var SAMLParser[] $ret */
            return $ret;
        }

        assert($element instanceof EntitiesDescriptor);

        $extensions = self::processExtensions($element, $parentExtensions);
        $expTime = self::getExpireTime($element, $maxExpireTime);

        $validators[] = $element;

        $ret = [];
        foreach ($element->getChildren() as $child) {
            $ret += self::processDescriptorsElement($child, $expTime, $validators, $extensions);
        }

        return $ret;
    }


    /**
     * Determine how long a given element can be cached.
     *
     * This function looks for the 'validUntil' attribute to determine
     * how long a given XML-element is valid. It returns this as a unix timestamp.
     *
     * @param mixed    $element The element we should determine the expiry time of.
     * @param int|null $maxExpireTime The maximum expiration time.
     *
     * @return int|null The unix timestamp for when the element should expire. Will be NULL if no
     *             limit is set for the element.
     */
    private static function getExpireTime($element, int $maxExpireTime = null): ?int
    {
        // validUntil may be null
        $expire = $element->getValidUntil();

        if ($maxExpireTime !== null && ($expire === null || $maxExpireTime < $expire)) {
            $expire = $maxExpireTime;
        }

        return $expire;
    }


    /**
     * This function returns the entity id of this parsed entity.
     *
     * @return string The entity id of this parsed entity.
     */
    public function getEntityId()
    {
        return $this->entityId;
    }


    /**
     * @return array
     */
    private function getMetadataCommon(): array
    {
        $ret = [];
        $ret['entityid'] = $this->entityId;
        $ret['entityDescriptor'] = $this->entityDescriptor;

        // add organizational metadata
        if (!empty($this->organizationName)) {
            $ret['description'] = $this->organizationName;
            $ret['OrganizationName'] = $this->organizationName;
        }
        if (!empty($this->organizationDisplayName)) {
            $ret['name'] = $this->organizationDisplayName;
            $ret['OrganizationDisplayName'] = $this->organizationDisplayName;
        }
        if (!empty($this->organizationURL)) {
            $ret['url'] = $this->organizationURL;
            $ret['OrganizationURL'] = $this->organizationURL;
        }

        //add contact metadata
        $ret['contacts'] = $this->contacts;

        return $ret;
    }


    /**
     * Add data parsed from extensions to metadata.
     *
     * @param array &$metadata The metadata that should be updated.
     * @param array $roleDescriptor The parsed role descriptor.
     * @return void
     */
    private function addExtensions(array &$metadata, array $roleDescriptor): void
    {
        assert(array_key_exists('scope', $roleDescriptor));
        assert(array_key_exists('tags', $roleDescriptor));

        $scopes = array_merge($this->scopes, array_diff($roleDescriptor['scope'], $this->scopes));
        if (!empty($scopes)) {
            $metadata['scope'] = $scopes;
        }

        $tags = array_merge($this->tags, array_diff($roleDescriptor['tags'], $this->tags));
        if (!empty($tags)) {
            $metadata['tags'] = $tags;
        }


        if (!empty($this->registrationInfo)) {
            $metadata['RegistrationInfo'] = $this->registrationInfo;
        }

        if (!empty($this->entityAttributes)) {
            $metadata['EntityAttributes'] = $this->entityAttributes;

            // check for entity categories
            if (Utils\Config\Metadata::isHiddenFromDiscovery($metadata)) {
                $metadata['hide.from.discovery'] = true;
            }
        }

        if (!empty($roleDescriptor['UIInfo'])) {
            $metadata['UIInfo'] = $roleDescriptor['UIInfo'];
        }

        if (!empty($roleDescriptor['DiscoHints'])) {
            $metadata['DiscoHints'] = $roleDescriptor['DiscoHints'];
        }
    }


    /**
     * This function returns the metadata for SAML 1.x SPs in the format SimpleSAMLphp expects.
     * This is an associative array with the following fields:
     * - 'entityid': The entity id of the entity described in the metadata.
     * - 'AssertionConsumerService': String with the URL of the assertion consumer service which supports
     *   the browser-post binding.
     * - 'certData': X509Certificate for entity (if present).
     *
     * Metadata must be loaded with one of the parse functions before this function can be called.
     *
     * @return array|null An associative array with metadata or NULL if we are unable to
     *   generate metadata for a SAML 1.x SP.
     */
    public function getMetadata1xSP()
    {
        $ret = $this->getMetadataCommon();
        $ret['metadata-set'] = 'shib13-sp-remote';


        // find SP information which supports one of the SAML 1.x protocols
        $spd = $this->getSPDescriptors(self::$SAML1xProtocols);
        if (count($spd) === 0) {
            return null;
        }

        // we currently only look at the first SPDescriptor which supports SAML 1.x
        $spd = $spd[0];

        // add expire time to metadata
        if (array_key_exists('expire', $spd)) {
            $ret['expire'] = $spd['expire'];
        }

        // find the assertion consumer service endpoints
        $ret['AssertionConsumerService'] = $spd['AssertionConsumerService'];

        // add the list of attributes the SP should receive
        if (array_key_exists('attributes', $spd)) {
            $ret['attributes'] = $spd['attributes'];
        }
        if (array_key_exists('attributes.required', $spd)) {
            $ret['attributes.required'] = $spd['attributes.required'];
        }
        if (array_key_exists('attributes.NameFormat', $spd)) {
            $ret['attributes.NameFormat'] = $spd['attributes.NameFormat'];
        }

        // add name & description
        if (array_key_exists('name', $spd)) {
            $ret['name'] = $spd['name'];
        }
        if (array_key_exists('description', $spd)) {
            $ret['description'] = $spd['description'];
        }

        // add public keys
        if (!empty($spd['keys'])) {
            $ret['keys'] = $spd['keys'];
        }

        // add extensions
        $this->addExtensions($ret, $spd);

        // prioritize mdui:DisplayName as the name if available
        if (!empty($ret['UIInfo']['DisplayName'])) {
            $ret['name'] = $ret['UIInfo']['DisplayName'];
        }

        return $ret;
    }


    /**
     * This function returns the metadata for SAML 1.x IdPs in the format SimpleSAMLphp expects.
     * This is an associative array with the following fields:
     * - 'entityid': The entity id of the entity described in the metadata.
     * - 'name': Auto generated name for this entity. Currently set to the entity id.
     * - 'SingleSignOnService': String with the URL of the SSO service which supports the redirect binding.
     * - 'SingleLogoutService': String with the URL where we should send logout requests/responses.
     * - 'certData': X509Certificate for entity (if present).
     * - 'certFingerprint': Fingerprint of the X509Certificate from the metadata. (deprecated)
     *
     * Metadata must be loaded with one of the parse functions before this function can be called.
     *
     * @return array|null An associative array with metadata or NULL if we are unable to
     *   generate metadata for a SAML 1.x IdP.
     */
    public function getMetadata1xIdP()
    {
        $ret = $this->getMetadataCommon();
        $ret['metadata-set'] = 'shib13-idp-remote';

        // find IdP information which supports the SAML 1.x protocol
        $idp = $this->getIdPDescriptors(self::$SAML1xProtocols);
        if (count($idp) === 0) {
            return null;
        }

        // we currently only look at the first IDP descriptor which supports SAML 1.x
        $idp = $idp[0];

        // fdd expire time to metadata
        if (array_key_exists('expire', $idp)) {
            $ret['expire'] = $idp['expire'];
        }

        // find the SSO service endpoints
        $ret['SingleSignOnService'] = $idp['SingleSignOnService'];

        // find the ArtifactResolutionService endpoint
        $ret['ArtifactResolutionService'] = $idp['ArtifactResolutionService'];

        // add public keys
        if (!empty($idp['keys'])) {
            $ret['keys'] = $idp['keys'];
        }

        // add extensions
        $this->addExtensions($ret, $idp);

        // prioritize mdui:DisplayName as the name if available
        if (!empty($ret['UIInfo']['DisplayName'])) {
            $ret['name'] = $ret['UIInfo']['DisplayName'];
        }

        return $ret;
    }


    /**
     * This function returns the metadata for SAML 2.0 SPs in the format SimpleSAMLphp expects.
     * This is an associative array with the following fields:
     * - 'entityid': The entity id of the entity described in the metadata.
     * - 'AssertionConsumerService': String with the URL of the assertion consumer service which supports
     *   the browser-post binding.
     * - 'SingleLogoutService': String with the URL where we should send logout requests/responses.
     * - 'NameIDFormat': The name ID format this SP expects. This may be unset.
     * - 'certData': X509Certificate for entity (if present).
     *
     * Metadata must be loaded with one of the parse functions before this function can be called.
     *
     * @return array|null An associative array with metadata or NULL if we are unable to
     *   generate metadata for a SAML 2.x SP.
     */
    public function getMetadata20SP()
    {
        $ret = $this->getMetadataCommon();
        $ret['metadata-set'] = 'saml20-sp-remote';

        // find SP information which supports the SAML 2.0 protocol
        $spd = $this->getSPDescriptors(self::$SAML20Protocols);
        if (count($spd) === 0) {
            return null;
        }

        // we currently only look at the first SPDescriptor which supports SAML 2.0
        $spd = $spd[0];

        // add expire time to metadata
        if (array_key_exists('expire', $spd)) {
            $ret['expire'] = $spd['expire'];
        }

        // find the assertion consumer service endpoints
        $ret['AssertionConsumerService'] = $spd['AssertionConsumerService'];


        // find the single logout service endpoint
        $ret['SingleLogoutService'] = $spd['SingleLogoutService'];


        // find the NameIDFormat. This may not exist
        if (count($spd['nameIDFormats']) > 0) {
            // SimpleSAMLphp currently only supports a single NameIDFormat per SP. We use the first one
            $ret['NameIDFormat'] = $spd['nameIDFormats'][0];
        }

        // add the list of attributes the SP should receive
        if (array_key_exists('attributes', $spd)) {
            $ret['attributes'] = $spd['attributes'];
        }
        if (array_key_exists('attributes.required', $spd)) {
            $ret['attributes.required'] = $spd['attributes.required'];
        }
        if (array_key_exists('attributes.NameFormat', $spd)) {
            $ret['attributes.NameFormat'] = $spd['attributes.NameFormat'];
        }
        if (array_key_exists('attributes.index', $spd)) {
            $ret['attributes.index'] = $spd['attributes.index'];
        }
        if (array_key_exists('attributes.isDefault', $spd)) {
            $ret['attributes.isDefault'] = $spd['attributes.isDefault'];
        }

        // add name & description
        if (array_key_exists('name', $spd)) {
            $ret['name'] = $spd['name'];
        }
        if (array_key_exists('description', $spd)) {
            $ret['description'] = $spd['description'];
        }

        // add public keys
        if (!empty($spd['keys'])) {
            $ret['keys'] = $spd['keys'];
        }

        // add validate.authnrequest
        if (array_key_exists('AuthnRequestsSigned', $spd)) {
            $ret['validate.authnrequest'] = $spd['AuthnRequestsSigned'];
        }

        // add saml20.sign.assertion
        if (array_key_exists('WantAssertionsSigned', $spd)) {
            $ret['saml20.sign.assertion'] = $spd['WantAssertionsSigned'];
        }

        // add extensions
        $this->addExtensions($ret, $spd);

        // prioritize mdui:DisplayName as the name if available
        if (!empty($ret['UIInfo']['DisplayName'])) {
            $ret['name'] = $ret['UIInfo']['DisplayName'];
        }

        return $ret;
    }


    /**
     * This function returns the metadata for SAML 2.0 IdPs in the format SimpleSAMLphp expects.
     * This is an associative array with the following fields:
     * - 'entityid': The entity id of the entity described in the metadata.
     * - 'name': Auto generated name for this entity. Currently set to the entity id.
     * - 'SingleSignOnService': String with the URL of the SSO service which supports the redirect binding.
     * - 'SingleLogoutService': String with the URL where we should send logout requests(/responses).
     * - 'SingleLogoutServiceResponse': String where we should send logout responses (if this is different from
     *   the 'SingleLogoutService' endpoint.
     * - 'NameIDFormats': The name ID formats this IdP supports.
     * - 'certData': X509Certificate for entity (if present).
     * - 'certFingerprint': Fingerprint of the X509Certificate from the metadata. (deprecated)
     *
     * Metadata must be loaded with one of the parse functions before this function can be called.
     *
     * @return array|null An associative array with metadata or NULL if we are unable to
     *   generate metadata for a SAML 2.0 IdP.
     */
    public function getMetadata20IdP()
    {
        $ret = $this->getMetadataCommon();
        $ret['metadata-set'] = 'saml20-idp-remote';

        // find IdP information which supports the SAML 2.0 protocol
        $idp = $this->getIdPDescriptors(self::$SAML20Protocols);
        if (count($idp) === 0) {
            return null;
        }

        // we currently only look at the first IDP descriptor which supports SAML 2.0
        $idp = $idp[0];

        // add expire time to metadata
        if (array_key_exists('expire', $idp)) {
            $ret['expire'] = $idp['expire'];
        }

        // enable redirect.sign if WantAuthnRequestsSigned is enabled
        if ($idp['WantAuthnRequestsSigned']) {
            $ret['sign.authnrequest'] = true;
        }

        // find the SSO service endpoint
        $ret['SingleSignOnService'] = $idp['SingleSignOnService'];

        // find the single logout service endpoint
        $ret['SingleLogoutService'] = $idp['SingleLogoutService'];

        // find the ArtifactResolutionService endpoint
        $ret['ArtifactResolutionService'] = $idp['ArtifactResolutionService'];

        // add supported nameIDFormats
        $ret['NameIDFormats'] = $idp['nameIDFormats'];

        // add public keys
        if (!empty($idp['keys'])) {
            $ret['keys'] = $idp['keys'];
        }

        // add extensions
        $this->addExtensions($ret, $idp);

        // prioritize mdui:DisplayName as the name if available
        if (!empty($ret['UIInfo']['DisplayName'])) {
            $ret['name'] = $ret['UIInfo']['DisplayName'];
        }

        return $ret;
    }


    /**
     * Retrieve AttributeAuthorities from the metadata.
     *
     * @return array Array of AttributeAuthorityDescriptor entries.
     */
    public function getAttributeAuthorities()
    {
        return $this->attributeAuthorityDescriptors;
    }


    /**
     * Parse a RoleDescriptorType element.
     *
     * The returned associative array has the following elements:
     * - 'protocols': Array with the protocols supported.
     * - 'expire': Timestamp for when this descriptor expires.
     * - 'keys': Array of associative arrays with the elements from parseKeyDescriptor.
     *
     * @param \SAML2\XML\md\RoleDescriptor $element The element we should extract metadata from.
     * @param int|null $expireTime The unix timestamp for when this element should expire, or
     *                             NULL if unknown.
     *
     * @return array An associative array with metadata we have extracted from this element.
     */
    private static function parseRoleDescriptorType(RoleDescriptor $element, int $expireTime = null): array
    {
        $ret = [];

        $expireTime = self::getExpireTime($element, $expireTime);

        if ($expireTime !== null) {
            // we got an expired timestamp, either from this element or one of the parent elements
            $ret['expire'] = $expireTime;
        }

        $ret['protocols'] = $element->getProtocolSupportEnumeration();

        // process KeyDescriptor elements
        $ret['keys'] = [];
        foreach ($element->getKeyDescriptor() as $kd) {
            $key = self::parseKeyDescriptor($kd);
            if ($key !== null) {
                $ret['keys'][] = $key;
            }
        }

        $ext = self::processExtensions($element);
        $ret['scope'] = $ext['scope'];
        $ret['tags'] = $ext['tags'];
        $ret['EntityAttributes'] = $ext['EntityAttributes'];
        $ret['UIInfo'] = $ext['UIInfo'];
        $ret['DiscoHints'] = $ext['DiscoHints'];

        return $ret;
    }


    /**
     * This function extracts metadata from a SSODescriptor element.
     *
     * The returned associative array has the following elements:
     * - 'protocols': Array with the protocols this SSODescriptor supports.
     * - 'SingleLogoutService': Array with the single logout service endpoints. Each endpoint is stored
     *   as an associative array with the elements that parseGenericEndpoint returns.
     * - 'nameIDFormats': The NameIDFormats supported by this SSODescriptor. This may be an empty array.
     * - 'keys': Array of associative arrays with the elements from parseKeyDescriptor:
     *
     * @param \SAML2\XML\md\SSODescriptorType $element The element we should extract metadata from.
     * @param int|NULL                       $expireTime The unix timestamp for when this element should expire, or
     *                             NULL if unknown.
     *
     * @return array An associative array with metadata we have extracted from this element.
     */
    private static function parseSSODescriptor(SSODescriptorType $element, int $expireTime = null): array
    {
        $sd = self::parseRoleDescriptorType($element, $expireTime);

        // find all SingleLogoutService elements
        $sd['SingleLogoutService'] = self::extractEndpoints($element->getSingleLogoutService());

        // find all ArtifactResolutionService elements
        $sd['ArtifactResolutionService'] = self::extractEndpoints($element->getArtifactResolutionService());


        // process NameIDFormat elements
        $sd['nameIDFormats'] = $element->getNameIDFormat();

        return $sd;
    }


    /**
     * This function extracts metadata from a SPSSODescriptor element.
     *
     * @param \SAML2\XML\md\SPSSODescriptor $element The element which should be parsed.
     * @param int|NULL                     $expireTime The unix timestamp for when this element should expire, or
     *                             NULL if unknown.
     * @return void
     */
    private function processSPSSODescriptor(SPSSODescriptor $element, int $expireTime = null): void
    {
        $sp = self::parseSSODescriptor($element, $expireTime);

        // find all AssertionConsumerService elements
        $sp['AssertionConsumerService'] = self::extractEndpoints($element->getAssertionConsumerService());

        // find all the attributes and SP name...
        $attcs = $element->getAttributeConsumingService();
        if (count($attcs) > 0) {
            self::parseAttributeConsumerService($attcs[0], $sp);
        }

        // check AuthnRequestsSigned
        if ($element->getAuthnRequestsSigned() !== null) {
            $sp['AuthnRequestsSigned'] = $element->getAuthnRequestsSigned();
        }

        // check WantAssertionsSigned
        if ($element->wantAssertionsSigned() !== null) {
            $sp['WantAssertionsSigned'] = $element->wantAssertionsSigned();
        }

        $this->spDescriptors[] = $sp;
    }


    /**
     * This function extracts metadata from a IDPSSODescriptor element.
     *
     * @param \SAML2\XML\md\IDPSSODescriptor $element The element which should be parsed.
     * @param int|NULL                      $expireTime The unix timestamp for when this element should expire, or
     *                             NULL if unknown.
     * @return void
     */
    private function processIDPSSODescriptor(IDPSSODescriptor $element, int $expireTime = null): void
    {
        $idp = self::parseSSODescriptor($element, $expireTime);

        // find all SingleSignOnService elements
        $idp['SingleSignOnService'] = self::extractEndpoints($element->getSingleSignOnService());

        if ($element->wantAuthnRequestsSigned()) {
            $idp['WantAuthnRequestsSigned'] = true;
        } else {
            $idp['WantAuthnRequestsSigned'] = false;
        }

        $this->idpDescriptors[] = $idp;
    }


    /**
     * This function extracts metadata from a AttributeAuthorityDescriptor element.
     *
     * @param \SAML2\XML\md\AttributeAuthorityDescriptor $element The element which should be parsed.
     * @param int|NULL                                  $expireTime The unix timestamp for when this element should
     *     expire, or NULL if unknown.
     * @return void
     */
    private function processAttributeAuthorityDescriptor(
        AttributeAuthorityDescriptor $element,
        $expireTime
    ): void {
        assert($expireTime === null || is_int($expireTime));

        $aad = self::parseRoleDescriptorType($element, $expireTime);
        $aad['entityid'] = $this->getEntityId();
        $aad['metadata-set'] = 'attributeauthority-remote';

        $aad['AttributeService'] = self::extractEndpoints($element->getAttributeService());
        $aad['AssertionIDRequestService'] = self::extractEndpoints($element->getAssertionIDRequestService());
        $aad['NameIDFormat'] = $element->getNameIDFormat();

        $this->attributeAuthorityDescriptors[] = $aad;
    }


    /**
     * Parse an Extensions element. Extensions may appear in multiple elements and certain extension may get inherited
     * from a parent element.
     *
     * @param mixed $element The element which contains the Extensions element.
     * @param array $parentExtensions An optional array of extensions from the parent element.
     *
     * @return array An associative array with the extensions parsed.
     */
    private static function processExtensions($element, array $parentExtensions = []): array
    {
        $ret = [
            'scope'            => [],
            'tags'             => [],
            'EntityAttributes' => [],
            'RegistrationInfo' => [],
            'UIInfo'           => [],
            'DiscoHints'       => [],
        ];

        // Some extensions may get inherited from a parent element
        if (
            ($element instanceof EntityDescriptor
            || $element instanceof EntitiesDescriptor)
            && !empty($parentExtensions['RegistrationInfo'])
        ) {
            $ret['RegistrationInfo'] = $parentExtensions['RegistrationInfo'];
        }

        foreach ($element->getExtensions() as $e) {
            if ($e instanceof Scope) {
                $ret['scope'][] = $e->getScope();
                continue;
            }

            // Entity Attributes are only allowed at entity level extensions and not at RoleDescriptor level
            if (
                $element instanceof EntityDescriptor
                || $element instanceof EntitiesDescriptor
            ) {
                if ($e instanceof RegistrationInfo) {
                    // Registration Authority cannot be overridden (warn only if override attempts to change the value)
                    if (
                        isset($ret['RegistrationInfo']['registrationAuthority'])
                        && $ret['RegistrationInfo']['registrationAuthority'] !== $e->getRegistrationAuthority()
                    ) {
                        Logger::warning(
                            'Invalid attempt to override registrationAuthority \''
                            . $ret['RegistrationInfo']['registrationAuthority']
                            . "' with '{$e->getRegistrationAuthority()}'"
                        );
                    } else {
                        $ret['RegistrationInfo']['registrationAuthority'] = $e->getRegistrationAuthority();
                    }
                }
                if ($e instanceof EntityAttributes && !empty($e->getChildren())) {
                    foreach ($e->getChildren() as $attr) {
                        // only saml:Attribute are currently supported here. The specifications also allows
                        // saml:Assertions, which more complex processing
                        if ($attr instanceof Attribute) {
                            /** @psalm-var string|null $attrName   Remove for SSP 2.0 */
                            $attrName = $attr->getName();
                            $attrNameFormat = $attr->getNameFormat();
                            $attrValue = $attr->getAttributeValue();

                            if ($attrName === null || $attrValue === []) {
                                continue;
                            }

                            // attribute names that is not URI is prefixed as this: '{nameformat}name'
                            $name = $attrName;
                            if ($attrNameFormat === null) {
                                $name = '{' . Constants::NAMEFORMAT_UNSPECIFIED . '}' . $attrName;
                            } elseif ($attrNameFormat !== Constants::NAMEFORMAT_URI) {
                                $name = '{' . $attrNameFormat . '}' . $attrName;
                            }

                            $values = [];
                            foreach ($attrValue as $attrval) {
                                $values[] = $attrval->getString();
                            }

                            $ret['EntityAttributes'][$name] = $values;
                        }
                    }
                }
            }

            // UIInfo elements are only allowed at RoleDescriptor level extensions
            if ($element instanceof RoleDescriptor) {
                if ($e instanceof UIInfo) {
                    $ret['UIInfo']['DisplayName'] = $e->getDisplayName();
                    $ret['UIInfo']['Description'] = $e->getDescription();
                    $ret['UIInfo']['InformationURL'] = $e->getInformationURL();
                    $ret['UIInfo']['PrivacyStatementURL'] = $e->getPrivacyStatementURL();

                    foreach ($e->getKeywords() as $uiItem) {
                        $keywords = $uiItem->getKeywords();
                        /** @psalm-var string|null $language */
                        $language = $uiItem->getLanguage();
                        if (($keywords === []) || ($language === null)) {
                            continue;
                        }
                        $ret['UIInfo']['Keywords'][$language] = $keywords;
                    }
                    foreach ($e->getLogo() as $uiItem) {
                        /** @psalm-suppress TypeDoesNotContainNull  Remove in SSP 2.0 */
                        if (
                            !($uiItem instanceof Logo)
                            || ($uiItem->getUrl() === null)
                            || ($uiItem->getHeight() === null)
                            || ($uiItem->getWidth() === null)
                        ) {
                            continue;
                        }
                        $logo = [
                            'url'    => $uiItem->getUrl(),
                            'height' => $uiItem->getHeight(),
                            'width'  => $uiItem->getWidth(),
                        ];
                        if ($uiItem->getLanguage() !== null) {
                            $logo['lang'] = $uiItem->getLanguage();
                        }
                        $ret['UIInfo']['Logo'][] = $logo;
                    }
                }
            }

            // DiscoHints elements are only allowed at IDPSSODescriptor level extensions
            if ($element instanceof IDPSSODescriptor) {
                if ($e instanceof DiscoHints) {
                    $ret['DiscoHints']['IPHint'] = $e->getIPHint();
                    $ret['DiscoHints']['DomainHint'] = $e->getDomainHint();
                    $ret['DiscoHints']['GeolocationHint'] = $e->getGeolocationHint();
                }
            }

            if (!($e instanceof Chunk)) {
                continue;
            }

            if ($e->getLocalName() === 'Attribute' && $e->getNamespaceURI() === Constants::NS_SAML) {
                $attribute = $e->getXML();

                $name = $attribute->getAttribute('Name');
                $values = array_map(
                    '\SimpleSAML\Utils\XML::getDOMText',
                    Utils\XML::getDOMChildren($attribute, 'AttributeValue', '@saml2')
                );

                if ($name === 'tags') {
                    foreach ($values as $tagname) {
                        if (!empty($tagname)) {
                            $ret['tags'][] = $tagname;
                        }
                    }
                }
            }
        }
        return $ret;
    }


    /**
     * Parse and process a Organization element.
     *
     * @param \SAML2\XML\md\Organization $element The Organization element.
     * @return void
     */
    private function processOrganization(Organization $element): void
    {
        $this->organizationName = $element->getOrganizationName();
        $this->organizationDisplayName = $element->getOrganizationDisplayName();
        $this->organizationURL = $element->getOrganizationURL();
    }


    /**
     * Parse and process a ContactPerson element.
     *
     * @param \SAML2\XML\md\ContactPerson $element The ContactPerson element.
     * @return void
     */
    private function processContactPerson(ContactPerson $element): void
    {
        $contactPerson = [];
        if ($element->getContactType() !== '') {
            $contactPerson['contactType'] = $element->getContactType();
        }
        if ($element->getCompany() !== null) {
            $contactPerson['company'] = $element->getCompany();
        }
        if ($element->getGivenName() !== null) {
            $contactPerson['givenName'] = $element->getGivenName();
        }
        if ($element->getSurName() !== null) {
            $contactPerson['surName'] = $element->getSurName();
        }
        if ($element->getEmailAddress() !== []) {
            $contactPerson['emailAddress'] = $element->getEmailAddress();
        }
        if ($element->getTelephoneNumber() !== []) {
            $contactPerson['telephoneNumber'] = $element->getTelephoneNumber();
        }
        if (!empty($contactPerson)) {
            $this->contacts[] = $contactPerson;
        }
    }


    /**
     * This function parses AttributeConsumerService elements.
     *
     * @param \SAML2\XML\md\AttributeConsumingService $element The AttributeConsumingService to parse.
     * @param array $sp The array with the SP's metadata.
     * @return void
     */
    private static function parseAttributeConsumerService(AttributeConsumingService $element, array &$sp): void
    {
        $sp['name'] = $element->getServiceName();
        $sp['description'] = $element->getServiceDescription();

        $format = null;
        $sp['attributes'] = [];
        $sp['attributes.required'] = [];
        foreach ($element->getRequestedAttribute() as $child) {
            $attrname = $child->getName();
            $sp['attributes'][] = $attrname;

            if ($child->getIsRequired() === true) {
                $sp['attributes.required'][] = $attrname;
            }

            if ($child->getNameFormat() !== null) {
                $attrformat = $child->getNameFormat();
            } else {
                $attrformat = Constants::NAMEFORMAT_UNSPECIFIED;
            }

            if ($format === null) {
                $format = $attrformat;
            } elseif ($format !== $attrformat) {
                $format = Constants::NAMEFORMAT_UNSPECIFIED;
            }
        }

        if (empty($sp['attributes'])) {
            // a really invalid configuration: all AttributeConsumingServices should have one or more attributes
            unset($sp['attributes']);
        }
        if (empty($sp['attributes.required'])) {
            unset($sp['attributes.required']);
        }

        if ($format !== Constants::NAMEFORMAT_UNSPECIFIED && $format !== null) {
            $sp['attributes.NameFormat'] = $format;
        }
    }


    /**
     * This function is a generic endpoint element parser.
     *
     * The returned associative array has the following elements:
     * - 'Binding': The binding this endpoint uses.
     * - 'Location': The URL to this endpoint.
     * - 'ResponseLocation': The URL where responses should be sent. This may not exist.
     * - 'index': The index of this endpoint. This attribute is only for indexed endpoints.
     * - 'isDefault': Whether this endpoint is the default endpoint for this type. This attribute may not exist.
     *
     * @param \SAML2\XML\md\EndpointType $element The element which should be parsed.
     *
     * @return array An associative array with the data we have extracted from the element.
     */
    private static function parseGenericEndpoint(EndpointType $element): array
    {
        $ep = [];

        $ep['Binding'] = $element->getBinding();
        $ep['Location'] = $element->getLocation();

        if ($element->getResponseLocation() !== null) {
            $ep['ResponseLocation'] = $element->getResponseLocation();
        }

        if ($element instanceof IndexedEndpointType) {
            $ep['index'] = $element->getIndex();

            if ($element->getIsDefault() !== null) {
                $ep['isDefault'] = $element->getIsDefault();
            }
        }

        return $ep;
    }


    /**
     * Extract generic endpoints.
     *
     * @param array $endpoints The endpoints we should parse.
     *
     * @return array Array of parsed endpoints.
     */
    private static function extractEndpoints(array $endpoints): array
    {
        return array_map('self::parseGenericEndpoint', $endpoints);
    }


    /**
     * This function parses a KeyDescriptor element. It currently only supports keys with a single
     * X509 certificate.
     *
     * The associative array for a key can contain:
     * - 'encryption': Indicates whether this key can be used for encryption.
     * - 'signing': Indicates whether this key can be used for signing.
     * - 'type: The type of the key. 'X509Certificate' is the only key type we support.
     * - 'X509Certificate': The contents of the first X509Certificate element (if the type is 'X509Certificate ').
     *
     * @param \SAML2\XML\md\KeyDescriptor $kd The KeyDescriptor element.
     *
     * @return array|null An associative array describing the key, or null if this is an unsupported key.
     */
    private static function parseKeyDescriptor(KeyDescriptor $kd): ?array
    {
        $r = [];

        if ($kd->getUse() === 'encryption') {
            $r['encryption'] = true;
            $r['signing'] = false;
        } elseif ($kd->getUse() === 'signing') {
            $r['encryption'] = false;
            $r['signing'] = true;
        } else {
            $r['encryption'] = true;
            $r['signing'] = true;
        }

        $keyInfo = $kd->getKeyInfo();

        /** @psalm-suppress PossiblyNullReference  This will be fixed in saml2 5.0 */
        foreach ($keyInfo->getInfo() as $i) {
            if ($i instanceof X509Data) {
                foreach ($i->getData() as $d) {
                    if ($d instanceof X509Certificate) {
                        $r['type'] = 'X509Certificate';
                        $r['X509Certificate'] = $d->getCertificate();
                        return $r;
                    }
                }
            }
        }

        return null;
    }


    /**
     * This function finds SP descriptors which supports one of the given protocols.
     *
     * @param array $protocols Array with the protocols we accept.
     *
     * @return array with SP descriptors which supports one of the given protocols.
     */
    private function getSPDescriptors(array $protocols): array
    {
        $ret = [];

        foreach ($this->spDescriptors as $spd) {
            $sharedProtocols = array_intersect($protocols, $spd['protocols']);
            if (count($sharedProtocols) > 0) {
                $ret[] = $spd;
            }
        }

        return $ret;
    }


    /**
     * This function finds IdP descriptors which supports one of the given protocols.
     *
     * @param array $protocols Array with the protocols we accept.
     *
     * @return array with IdP descriptors which supports one of the given protocols.
     */
    private function getIdPDescriptors(array $protocols): array
    {
        $ret = [];

        foreach ($this->idpDescriptors as $idpd) {
            $sharedProtocols = array_intersect($protocols, $idpd['protocols']);
            if (count($sharedProtocols) > 0) {
                $ret[] = $idpd;
            }
        }

        return $ret;
    }


    /**
     * This function locates the EntityDescriptor node in a DOMDocument. This node should
     * be the first (and only) node in the document.
     *
     * This function will throw an exception if it is unable to locate the node.
     *
     * @param \DOMDocument $doc The \DOMDocument where we should find the EntityDescriptor node.
     *
     * @return \SAML2\XML\md\EntityDescriptor The \DOMEntity which represents the EntityDescriptor.
     * @throws \Exception If the document is empty or the first element is not an EntityDescriptor element.
     */
    private static function findEntityDescriptor(DOMDocument $doc): EntityDescriptor
    {
        // find the EntityDescriptor DOMElement. This should be the first (and only) child of the DOMDocument
        $ed = $doc->documentElement;

        if (Utils\XML::isDOMNodeOfType($ed, 'EntityDescriptor', '@md') === false) {
            throw new \Exception('Expected first element in the metadata document to be an EntityDescriptor element.');
        }

        return new EntityDescriptor($ed);
    }


    /**
     * If this EntityDescriptor was signed this function use the public key to check the signature.
     *
     * @param array $certificates One ore more certificates with the public key. This makes it possible
     *                      to do a key rollover.
     *
     * @return boolean True if it is possible to check the signature with the certificate, false otherwise.
     * @throws \Exception If the certificate file cannot be found.
     */
    public function validateSignature($certificates)
    {
        foreach ($certificates as $cert) {
            assert(is_string($cert));
            $certFile = Utils\Config::getCertPath($cert);
            if (!file_exists($certFile)) {
                throw new \Exception(
                    'Could not find certificate file [' . $certFile . '], which is needed to validate signature'
                );
            }
            $certData = file_get_contents($certFile);

            foreach ($this->validators as $validator) {
                $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'public']);
                $key->loadKey($certData);
                try {
                    if ($validator->validate($key)) {
                        return true;
                    }
                } catch (\Exception $e) {
                    // this certificate did not sign this element, skip
                }
            }
        }
        Logger::debug('Could not validate signature');
        return false;
    }


    /**
     * @param string $algorithm
     * @param string $data
     * @throws \UnexpectedValueException
     * @return string
     */
    private function computeFingerprint(string $algorithm, string $data): string
    {
        switch ($algorithm) {
            case XMLSecurityDSig::SHA1:
                $algo = 'SHA1';
                break;
            case XMLSecurityDSig::SHA256:
                $algo = 'SHA256';
                break;
            case XMLSecurityDSig::SHA384:
                $algo = 'SHA384';
                break;
            case XMLSecurityDSig::SHA512:
                $algo = 'SHA512';
                break;
            default:
                $known_opts = implode(", ", [
                    XMLSecurityDSig::SHA1,
                    XMLSecurityDSig::SHA256,
                    XMLSecurityDSig::SHA384,
                    XMLSecurityDSig::SHA512,
                ]);
                throw new \UnexpectedValueException(
                    "Unsupported hashing function {$algorithm}. " .
                    "Known options: [{$known_opts}]"
                );
        }
        return hash($algo, $data);
    }


    /**
     * This function checks if this EntityDescriptor was signed with a certificate with the
     * given fingerprint.
     *
     * @param string $fingerprint Fingerprint of the certificate which should have been used to sign this
     *                      EntityDescriptor.
     * @param string $algorithm Algorithm used to compute the fingerprint of the signing certicate.
     *
     * @return boolean True if it was signed with the certificate with the given fingerprint, false otherwise.
     */
    public function validateFingerprint($fingerprint, $algorithm)
    {
        assert(is_string($fingerprint));

        $fingerprint = strtolower(str_replace(":", "", $fingerprint));

        $candidates = [];
        foreach ($this->validators as $validator) {
            foreach ($validator->getValidatingCertificates() as $cert) {
                $decoded_cert = base64_decode($cert);
                $fp = $this->computeFingerprint($algorithm, $decoded_cert);
                $candidates[] = $fp;
                if ($fp === $fingerprint) {
                    return true;
                }
            }
        }
        Logger::debug('Fingerprint was [' . $fingerprint . '] not one of [' . join(', ', $candidates) . ']');
        return false;
    }
}
