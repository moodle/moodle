<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;
use Webmozart\Assert\Assert;

use SAML2\Constants;
use SAML2\Utils;
use SAML2\XML\saml\Attribute;

/**
 * Class representing SAML 2 metadata AttributeAuthorityDescriptor.
 *
 * @package SimpleSAMLphp
 */
class AttributeAuthorityDescriptor extends RoleDescriptor
{
    /**
     * List of AttributeService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $AttributeService = [];

    /**
     * List of AssertionIDRequestService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $AssertionIDRequestService = [];

    /**
     * List of supported NameID formats.
     *
     * Array of strings.
     *
     * @var string[]
     */
    private $NameIDFormat = [];

    /**
     * List of supported attribute profiles.
     *
     * Array with strings.
     *
     * @var array
     */
    private $AttributeProfile = [];

    /**
     * List of supported attributes.
     *
     * Array with \SAML2\XML\saml\Attribute objects.
     *
     * @var \SAML2\XML\saml\Attribute[]
     */
    private $Attribute = [];


    /**
     * Initialize an IDPSSODescriptor.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('md:AttributeAuthorityDescriptor', $xml);

        if ($xml === null) {
            return;
        }

        /** @var \DOMElement $ep */
        foreach (Utils::xpQuery($xml, './saml_metadata:AttributeService') as $ep) {
            $this->addAttributeService(new EndpointType($ep));
        }
        if ($this->getAttributeService() === []) {
            throw new \Exception('Must have at least one AttributeService in AttributeAuthorityDescriptor.');
        }

        /** @var \DOMElement $ep */
        foreach (Utils::xpQuery($xml, './saml_metadata:AssertionIDRequestService') as $ep) {
            $this->addAssertionIDRequestService(new EndpointType($ep));
        }

        $this->setNameIDFormat(Utils::extractStrings($xml, Constants::NS_MD, 'NameIDFormat'));

        $this->setAttributeProfile(Utils::extractStrings($xml, Constants::NS_MD, 'AttributeProfile'));

        /** @var \DOMElement $a */
        foreach (Utils::xpQuery($xml, './saml_assertion:Attribute') as $a) {
            $this->addAttribute(new Attribute($a));
        }
    }


    /**
     * Collect the value of the AttributeService-property
     *
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getAttributeService() : array
    {
        return $this->AttributeService;
    }


    /**
     * Set the value of the AttributeService-property
     *
     * @param \SAML2\XML\md\EndpointType[] $attributeService
     * @return void
     */
    public function setAttributeService(array $attributeService) : void
    {
        $this->AttributeService = $attributeService;
    }


    /**
     * Add the value to the AttributeService-property
     *
     * @param \SAML2\XML\md\EndpointType $attributeService
     * @return void
     */
    public function addAttributeService(EndpointType $attributeService) : void
    {
        $this->AttributeService[] = $attributeService;
    }


    /**
     * Collect the value of the NameIDFormat-property
     *
     * @return string[]
     */
    public function getNameIDFormat() : array
    {
        return $this->NameIDFormat;
    }


    /**
     * Set the value of the NameIDFormat-property
     *
     * @param string[] $nameIDFormat
     * @return void
     */
    public function setNameIDFormat(array $nameIDFormat) : void
    {
        $this->NameIDFormat = $nameIDFormat;
    }


    /**
     * Collect the value of the AssertionIDRequestService-property
     *
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getAssertionIDRequestService() : array
    {
        return $this->AssertionIDRequestService;
    }


    /**
     * Set the value of the AssertionIDRequestService-property
     *
     * @param \SAML2\XML\md\EndpointType[] $assertionIDRequestService
     * @return void
     */
    public function setAssertionIDRequestService(array $assertionIDRequestService) : void
    {
        $this->AssertionIDRequestService = $assertionIDRequestService;
    }


    /**
     * Add the value to the AssertionIDRequestService-property
     *
     * @param \SAML2\XML\md\EndpointType $assertionIDRequestService
     * @return void
     */
    public function addAssertionIDRequestService(EndpointType $assertionIDRequestService) : void
    {
        $this->AssertionIDRequestService[] = $assertionIDRequestService;
    }


    /**
     * Collect the value of the AttributeProfile-property
     *
     * @return string[]
     */
    public function getAttributeProfile() : array
    {
        return $this->AttributeProfile;
    }


    /**
     * Set the value of the AttributeProfile-property
     *
     * @param string[] $attributeProfile
     * @return void
     */
    public function setAttributeProfile(array $attributeProfile) : void
    {
        $this->AttributeProfile = $attributeProfile;
    }


    /**
     * Collect the value of the Attribute-property
     *
     * @return \SAML2\XML\saml\Attribute[]
     */
    public function getAttribute() : array
    {
        return $this->Attribute;
    }


    /**
     * Set the value of the Attribute-property
     *
     * @param \SAML2\XML\saml\Attribute[] $attribute
     * @return void
     */
    public function setAttribute(array $attribute) : void
    {
        $this->Attribute = $attribute;
    }


    /**
     * Add the value to the Attribute-property
     *
     * @param \SAML2\XML\saml\Attribute $attribute
     * @return void
     */
    public function addAttribute(Attribute $attribute) : void
    {
        $this->Attribute[] = $attribute;
    }


    /**
     * Add this AttributeAuthorityDescriptor to an EntityDescriptor.
     *
     * @param \DOMElement $parent The EntityDescriptor we should append this IDPSSODescriptor to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        Assert::notEmpty($this->AttributeService);

        $e = parent::toXML($parent);

        foreach ($this->AttributeService as $ep) {
            $ep->toXML($e, 'md:AttributeService');
        }

        foreach ($this->AssertionIDRequestService as $ep) {
            $ep->toXML($e, 'md:AssertionIDRequestService');
        }

        Utils::addStrings($e, Constants::NS_MD, 'md:NameIDFormat', false, $this->NameIDFormat);

        Utils::addStrings($e, Constants::NS_MD, 'md:AttributeProfile', false, $this->AttributeProfile);

        foreach ($this->Attribute as $a) {
            $a->toXML($e);
        }

        return $e;
    }
}
