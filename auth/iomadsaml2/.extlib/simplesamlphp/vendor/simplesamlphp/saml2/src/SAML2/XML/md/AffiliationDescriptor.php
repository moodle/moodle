<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;
use Webmozart\Assert\Assert;

use SAML2\Constants;
use SAML2\SignedElementHelper;
use SAML2\Utils;

/**
 * Class representing SAML 2 AffiliationDescriptor element.
 *
 * @package SimpleSAMLphp
 */
class AffiliationDescriptor extends SignedElementHelper
{
    /**
     * The affiliationOwnerID.
     *
     * @var string
     */
    public $affiliationOwnerID = '';

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
     * The AffiliateMember(s).
     *
     * Array of entity ID strings.
     *
     * @var array
     */
    private $AffiliateMember = [];

    /**
     * KeyDescriptor elements.
     *
     * Array of \SAML2\XML\md\KeyDescriptor elements.
     *
     * @var \SAML2\XML\md\KeyDescriptor[]
     */
    private $KeyDescriptor = [];


    /**
     * Initialize a AffiliationDescriptor.
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

        if (!$xml->hasAttribute('affiliationOwnerID')) {
            throw new \Exception('Missing affiliationOwnerID on AffiliationDescriptor.');
        }
        $this->setAffiliationOwnerID($xml->getAttribute('affiliationOwnerID'));

        if ($xml->hasAttribute('ID')) {
            $this->setID($xml->getAttribute('ID'));
        }

        if ($xml->hasAttribute('validUntil')) {
            $this->setValidUntil(Utils::xsDateTimeToTimestamp($xml->getAttribute('validUntil')));
        }

        if ($xml->hasAttribute('cacheDuration')) {
            $this->setCacheDuration($xml->getAttribute('cacheDuration'));
        }

        $this->setExtensions(Extensions::getList($xml));

        $this->setAffiliateMember(Utils::extractStrings($xml, Constants::NS_MD, 'AffiliateMember'));
        if (empty($this->AffiliateMember)) {
            throw new \Exception('Missing AffiliateMember in AffiliationDescriptor.');
        }

        /** @var \DOMElement $kd */
        foreach (Utils::xpQuery($xml, './saml_metadata:KeyDescriptor') as $kd) {
            $this->addKeyDescriptor(new KeyDescriptor($kd));
        }
    }


    /**
     * Collect the value of the affiliationOwnerId-property
     *
     * @return string
     */
    public function getAffiliationOwnerID() : string
    {
        return $this->affiliationOwnerID;
    }


    /**
     * Set the value of the affiliationOwnerId-property
     *
     * @param string $affiliationOwnerId
     * @return void
     */
    public function setAffiliationOwnerID(string $affiliationOwnerId) : void
    {
        $this->affiliationOwnerID = $affiliationOwnerId;
    }


    /**
     * Collect the value of the ID-property
     *
     * @return string|null
     */
    public function getID() : ?string
    {
        return $this->ID;
    }


    /**
     * Set the value of the ID-property
     *
     * @param string|null $Id
     * @return void
     */
    public function setID(string $Id = null) : void
    {
        $this->ID = $Id;
    }


    /**
     * Collect the value of the Extensions-property
     *
     * @return \SAML2\XML\Chunk[]
     */
    public function getExtensions() : array
    {
        return $this->Extensions;
    }


    /**
     * Set the value of the Extensions-property
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
     * @param Extensions $extensions The Extensions
     * @return void
     */
    public function addExtension(Extensions $extension) : void
    {
        $this->Extensions[] = $extension;
    }


    /**
     * Collect the value of the AffiliateMember-property
     *
     * @return array
     */
    public function getAffiliateMember() : array
    {
        return $this->AffiliateMember;
    }


    /**
     * Set the value of the AffiliateMember-property
     *
     * @param array $affiliateMember
     * @return void
     */
    public function setAffiliateMember(array $affiliateMember) : void
    {
        $this->AffiliateMember = $affiliateMember;
    }


    /**
     * Collect the value of the KeyDescriptor-property
     *
     * @return \SAML2\XML\md\KeyDescriptor[]
     */
    public function getKeyDescriptor() : array
    {
        return $this->KeyDescriptor;
    }


    /**
     * Set the value of the KeyDescriptor-property
     *
     * @param array $keyDescriptor
     * @return void
     */
    public function setKeyDescriptor(array $keyDescriptor) : void
    {
        $this->KeyDescriptor = $keyDescriptor;
    }


    /**
     * Add the value to the KeyDescriptor-property
     *
     * @param \SAML2\XML\md\KeyDescriptor $keyDescriptor
     * @return void
     */
    public function addKeyDescriptor(KeyDescriptor $keyDescriptor) : void
    {
        $this->KeyDescriptor[] = $keyDescriptor;
    }


    /**
     * Add this AffiliationDescriptor to an EntityDescriptor.
     *
     * @param \DOMElement $parent The EntityDescriptor we should append this endpoint to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        Assert::notEmpty($this->affiliationOwnerID);

        $e = $parent->ownerDocument->createElementNS(Constants::NS_MD, 'md:AffiliationDescriptor');
        $parent->appendChild($e);

        $e->setAttribute('affiliationOwnerID', $this->affiliationOwnerID);

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

        Utils::addStrings($e, Constants::NS_MD, 'md:AffiliateMember', false, $this->AffiliateMember);

        foreach ($this->KeyDescriptor as $kd) {
            $kd->toXML($e);
        }

        $this->signElement($e, $e->firstChild);

        return $e;
    }
}
