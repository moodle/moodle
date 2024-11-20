<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;

use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\SignedElementHelper;
use SAML2\Utils;
use SAML2\XML\Chunk;

/**
 * Class representing SAML 2 EntityDescriptor element.
 *
 * @package SimpleSAMLphp
 */
class EntityDescriptor extends SignedElementHelper
{
    /**
     * The entityID this EntityDescriptor represents.
     *
     * @var string
     */
    private $entityID;

    /**
     * The ID of this element.
     *
     * @var string|null
     */
    private $ID = null;

    /**
     * Extensions on this element.
     *
     * Array of extension elements.
     *
     * @var array
     */
    private $Extensions = [];

    /**
     * Array with all roles for this entity.
     *
     * Array of \SAML2\XML\md\RoleDescriptor objects (and subclasses of RoleDescriptor).
     *
     * @var \SAML2\XML\md\RoleDescriptor[]
     */
    private $RoleDescriptor = [];

    /**
     * AffiliationDescriptor of this entity.
     *
     * @var \SAML2\XML\md\AffiliationDescriptor|null
     */
    private $AffiliationDescriptor = null;

    /**
     * Organization of this entity.
     *
     * @var \SAML2\XML\md\Organization|null
     */
    private $Organization = null;

    /**
     * ContactPerson elements for this entity.
     *
     * @var \SAML2\XML\md\ContactPerson[]
     */
    private $ContactPerson = [];

    /**
     * AdditionalMetadataLocation elements for this entity.
     *
     * @var \SAML2\XML\md\AdditionalMetadataLocation[]
     */
    private $AdditionalMetadataLocation = [];


    /**
     * Initialize an EntitiyDescriptor.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct($xml);

        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('entityID')) {
            throw new \Exception('Missing required attribute entityID on EntityDescriptor.');
        }
        $this->entityID = $xml->getAttribute('entityID');

        if ($xml->hasAttribute('ID')) {
            $this->ID = $xml->getAttribute('ID');
        }
        if ($xml->hasAttribute('validUntil')) {
            $this->validUntil = Utils::xsDateTimeToTimestamp($xml->getAttribute('validUntil'));
        }
        if ($xml->hasAttribute('cacheDuration')) {
            $this->cacheDuration = $xml->getAttribute('cacheDuration');
        }

        $this->Extensions = Extensions::getList($xml);

        foreach ($xml->childNodes as $node) {
            if (!($node instanceof DOMElement)) {
                continue;
            }

            if ($node->namespaceURI !== Constants::NS_MD) {
                continue;
            }

            switch ($node->localName) {
                case 'RoleDescriptor':
                    $this->RoleDescriptor[] = new UnknownRoleDescriptor($node);
                    break;
                case 'IDPSSODescriptor':
                    $this->RoleDescriptor[] = new IDPSSODescriptor($node);
                    break;
                case 'SPSSODescriptor':
                    $this->RoleDescriptor[] = new SPSSODescriptor($node);
                    break;
                case 'AuthnAuthorityDescriptor':
                    $this->RoleDescriptor[] = new AuthnAuthorityDescriptor($node);
                    break;
                case 'AttributeAuthorityDescriptor':
                    $this->RoleDescriptor[] = new AttributeAuthorityDescriptor($node);
                    break;
                case 'PDPDescriptor':
                    $this->RoleDescriptor[] = new PDPDescriptor($node);
                    break;
                case 'AffiliationDescriptor':
                    if ($this->AffiliationDescriptor !== null) {
                        throw new \Exception('More than one AffiliationDescriptor in the entity.');
                    }
                    $this->AffiliationDescriptor = new AffiliationDescriptor($node);
                    break;
                case 'Organization':
                    if ($this->Organization !== null) {
                        throw new \Exception('More than one Organization in the entity.');
                    }
                    $this->Organization = new Organization($node);
                    break;
                case 'ContactPerson':
                    $this->ContactPerson[] = new ContactPerson($node);
                    break;
                case 'AdditionalMetadataLocation':
                    $this->AdditionalMetadataLocation[] = new AdditionalMetadataLocation($node);
                    break;
            }
        }

        if (empty($this->RoleDescriptor) && is_null($this->AffiliationDescriptor)) {
            throw new \Exception(
                'Must have either one of the RoleDescriptors or an AffiliationDescriptor in EntityDescriptor.'
            );
        } elseif (!empty($this->RoleDescriptor) && !is_null($this->AffiliationDescriptor)) {
            throw new \Exception(
                'AffiliationDescriptor cannot be combined with other RoleDescriptor elements in EntityDescriptor.'
            );
        }
    }


    /**
     * Collect the value of the entityID property.
     *
     * @return string
     */
    public function getEntityID() : string
    {
        return $this->entityID;
    }


    /**
     * Set the value of the entityID-property
     * @param string $entityId
     * @return void
     */
    public function setEntityID(string $entityId) : void
    {
        $this->entityID = $entityId;
    }


    /**
     * Collect the value of the ID property.
     *
     * @return string|null
     */
    public function getID() : ?string
    {
        return $this->ID;
    }


    /**
     * Set the value of the ID property.
     *
     * @param string|null $Id
     * @return void
     */
    public function setID(string $Id = null) : void
    {
        $this->ID = $Id;
    }


    /**
     * Collect the value of the validUntil-property
     * @return int|null
     */
    public function getValidUntil() : ?int
    {
        return $this->validUntil;
    }


    /**
     * Set the value of the validUntil-property
     * @param int|null $validUntil
     * @return void
     */
    public function setValidUntil(int $validUntil = null) : void
    {
        $this->validUntil = $validUntil;
    }


    /**
     * Collect the value of the cacheDuration-property
     * @return string|null
     */
    public function getCacheDuration() : ?string
    {
        return $this->cacheDuration;
    }


    /**
     * Set the value of the cacheDuration-property
     * @param string|null $cacheDuration
     * @return void
     */
    public function setCacheDuration(string $cacheDuration = null) : void
    {
        $this->cacheDuration = $cacheDuration;
    }


    /**
     * Collect the value of the Extensions property.
     *
     * @return \SAML2\XML\Chunk[]
     */
    public function getExtensions() : array
    {
        return $this->Extensions;
    }


    /**
     * Set the value of the Extensions property.
     *
     * @param array $extensions
     * @return void
     */
    public function setExtensions(array $extensions) : void
    {
        $this->Extensions = $extensions;
    }


    /**
     * Add an Extension.
     *
     * @param \SAML2\XML\Chunk $extensions The Extensions
     * @return void
     */
    public function addExtension(Extensions $extension) : void
    {
        $this->Extensions[] = $extension;
    }


    /**
     * Collect the value of the RoleDescriptor property.
     *
     * @return \SAML2\XML\md\RoleDescriptor[]
     */
    public function getRoleDescriptor() : array
    {
        return $this->RoleDescriptor;
    }


    /**
     * Set the value of the RoleDescriptor property.
     *
     * @param \SAML2\XML\md\RoleDescriptor[] $roleDescriptor
     * @return void
     */
    public function setRoleDescriptor(array $roleDescriptor) : void
    {
        $this->RoleDescriptor = $roleDescriptor;
    }


    /**
     * Add the value to the RoleDescriptor property.
     *
     * @param \SAML2\XML\md\RoleDescriptor $roleDescriptor
     * @return void
     */
    public function addRoleDescriptor(RoleDescriptor $roleDescriptor) : void
    {
        $this->RoleDescriptor[] = $roleDescriptor;
    }


    /**
     * Collect the value of the AffiliationDescriptor property.
     *
     * @return \SAML2\XML\md\AffiliationDescriptor|null
     */
    public function getAffiliationDescriptor() : ?AffiliationDescriptor
    {
        return $this->AffiliationDescriptor;
    }


    /**
     * Set the value of the AffliationDescriptor property.
     *
     * @param \SAML2\XML\md\AffiliationDescriptor|null $affiliationDescriptor
     * @return void
     */
    public function setAffiliationDescriptor(AffiliationDescriptor $affiliationDescriptor = null) : void
    {
        $this->AffiliationDescriptor = $affiliationDescriptor;
    }


    /**
     * Collect the value of the Organization property.
     *
     * @return \SAML2\XML\md\Organization|null
     */
    public function getOrganization() : ?Organization
    {
        return $this->Organization;
    }


    /**
     * Set the value of the Organization property.
     *
     * @param \SAML2\XML\md\Organization|null $organization
     * @return void
     */
    public function setOrganization(Organization $organization = null) : void
    {
        $this->Organization = $organization;
    }


    /**
     * Collect the value of the ContactPerson property.
     *
     * @return \SAML2\XML\md\ContactPerson[]
     */
    public function getContactPerson() : array
    {
        return $this->ContactPerson;
    }


    /**
     * Set the value of the ContactPerson property.
     *
     * @param array $contactPerson
     * @return void
     */
    public function setContactPerson(array $contactPerson) : void
    {
        $this->ContactPerson = $contactPerson;
    }


    /**
     * Add the value to the ContactPerson property.
     *
     * @param \SAML2\XML\md\ContactPerson $contactPerson
     * @return void
     */
    public function addContactPerson(ContactPerson $contactPerson) : void
    {
        $this->ContactPerson[] = $contactPerson;
    }


    /**
     * Collect the value of the AdditionalMetadataLocation property.
     *
     * @return \SAML2\XML\md\AdditionalMetadataLocation[]
     */
    public function getAdditionalMetadataLocation() : array
    {
        return $this->AdditionalMetadataLocation;
    }


    /**
     * Set the value of the AdditionalMetadataLocation property.
     *
     * @param array $additionalMetadataLocation
     * @return void
     */
    public function setAdditionalMetadataLocation(array $additionalMetadataLocation) : void
    {
        $this->AdditionalMetadataLocation = $additionalMetadataLocation;
    }


    /**
     * Add the value to the AdditionalMetadataLocation property.
     *
     * @param AdditionalMetadataLocation $additionalMetadataLocation
     * @return void
     */
    public function addAdditionalMetadataLocation(AdditionalMetadataLocation $additionalMetadataLocation) : void
    {
        $this->AdditionalMetadataLocation[] = $additionalMetadataLocation;
    }


    /**
     * Create this EntityDescriptor.
     *
     * @param \DOMElement|null $parent The EntitiesDescriptor we should append this EntityDescriptor to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null) : DOMElement
    {
        if (empty($this->entityID)) {
            throw new \Exception('Cannot convert EntityDescriptor to XML without an EntityID set.');
        }

        if ($parent === null) {
            $doc = DOMDocumentFactory::create();
            $e = $doc->createElementNS(Constants::NS_MD, 'md:EntityDescriptor');
            $doc->appendChild($e);
        } else {
            $e = $parent->ownerDocument->createElementNS(Constants::NS_MD, 'md:EntityDescriptor');
            $parent->appendChild($e);
        }

        $e->setAttribute('entityID', $this->entityID);

        if ($this->ID !== null) {
            $e->setAttribute('ID', $this->ID);
        }

        if ($this->validUntil !== null) {
            $e->setAttribute('validUntil', gmdate('Y-m-d\TH:i:s\Z', $this->validUntil));
        }

        if ($this->cacheDuration !== null) {
            $e->setAttribute('cacheDuration', $this->cacheDuration);
        }

        Extensions::addList($e, $this->Extensions);

        foreach ($this->RoleDescriptor as $n) {
            $n->toXML($e);
        }

        if ($this->AffiliationDescriptor !== null) {
            $this->AffiliationDescriptor->toXML($e);
        }

        if ($this->Organization !== null) {
            $this->Organization->toXML($e);
        }

        foreach ($this->ContactPerson as $cp) {
            $cp->toXML($e);
        }

        foreach ($this->AdditionalMetadataLocation as $n) {
            $n->toXML($e);
        }

        /** @var \DOMElement $child */
        $child = $e->firstChild;
        $this->signElement($e, $child);

        return $e;
    }
}
