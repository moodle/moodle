<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

use DOMElement;

use SAML2\Constants;
use SAML2\Utils;

/**
 * Class representing SAML 2 Attribute.
 *
 * @package SimpleSAMLphp
 */
class Attribute
{
    /**
     * The Name of this attribute.
     *
     * @var string|null
     */
    private $Name = null;

    /**
     * The NameFormat of this attribute.
     *
     * @var string|null
     */
    private $NameFormat = null;

    /**
     * The FriendlyName of this attribute.
     *
     * @var string|null
     */
    private $FriendlyName = null;

    /**
     * List of attribute values.
     *
     * Array of \SAML2\XML\saml\AttributeValue elements.
     *
     * @var \SAML2\XML\saml\AttributeValue[]
     */
    private $AttributeValue = [];


    /**
     * Initialize an Attribute.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('Name')) {
            throw new \Exception('Missing Name on Attribute.');
        }
        $this->setName($xml->getAttribute('Name'));

        if ($xml->hasAttribute('NameFormat')) {
            $this->setNameFormat($xml->getAttribute('NameFormat'));
        }

        if ($xml->hasAttribute('FriendlyName')) {
            $this->setFriendlyName($xml->getAttribute('FriendlyName'));
        }

        foreach (Utils::xpQuery($xml, './saml_assertion:AttributeValue') as $av) {
            $this->addAttributeValue(new AttributeValue($av));
        }
    }


    /**
     * Collect the value of the Name-property
     *
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->Name;
    }


    /**
     * Set the value of the Name-property
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name) : void
    {
        $this->Name = $name;
    }


    /**
     * Collect the value of the NameFormat-property
     *
     * @return string|null
     */
    public function getNameFormat() : ?string
    {
        return $this->NameFormat;
    }


    /**
     * Set the value of the NameFormat-property
     *
     * @param string|null $nameFormat
     * @return void
     */
    public function setNameFormat(string $nameFormat = null) : void
    {
        $this->NameFormat = $nameFormat;
    }


    /**
     * Collect the value of the FriendlyName-property
     *
     * @return string|null
     */
    public function getFriendlyName() : ?string
    {
        return $this->FriendlyName;
    }


    /**
     * Set the value of the FriendlyName-property
     *
     * @param string|null $friendlyName
     * @return void
     */
    public function setFriendlyName(string $friendlyName = null) : void
    {
        $this->FriendlyName = $friendlyName;
    }


    /**
     * Collect the value of the AttributeValue-property
     *
     * @return \SAML2\XML\saml\AttributeValue[]
     */
    public function getAttributeValue() : array
    {
        return $this->AttributeValue;
    }


    /**
     * Set the value of the AttributeValue-property
     *
     * @param array $attributeValue
     * @return void
     */
    public function setAttributeValue(array $attributeValue) : void
    {
        $this->AttributeValue = $attributeValue;
    }


    /**
     * Add the value to the AttributeValue-property
     *
     * @param \SAML2\XML\saml\AttributeValue $attributeValue
     * @return void
     */
    public function addAttributeValue(AttributeValue $attributeValue) : void
    {
        $this->AttributeValue[] = $attributeValue;
    }


    /**
     * Internal implementation of toXML.
     * This function allows RequestedAttribute to specify the element name and namespace.
     *
     * @param \DOMElement $parent The element we should append this Attribute to.
     * @param string $namespace The namespace the element should be created in.
     * @param string $name The name of the element.
     * @return \DOMElement
     */
    protected function toXMLInternal(DOMElement $parent, string $namespace, string $name) : DOMElement
    {
        $e = $parent->ownerDocument->createElementNS($namespace, $name);
        $parent->appendChild($e);

        if (empty($this->Name)) {
            throw new \Exception('Cannot convert Attribute to XML with no Name set.');
        }
        $e->setAttribute('Name', $this->Name);

        if ($this->NameFormat !== null) {
            $e->setAttribute('NameFormat', $this->NameFormat);
        }

        if ($this->FriendlyName !== null) {
            $e->setAttribute('FriendlyName', $this->FriendlyName);
        }

        foreach ($this->AttributeValue as $av) {
            $av->toXML($e);
        }

        return $e;
    }


    /**
     * Convert this Attribute to XML.
     *
     * @param \DOMElement $parent The element we should append this Attribute to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : \DOMElement
    {
        return $this->toXMLInternal($parent, Constants::NS_SAML, 'saml:Attribute');
    }
}
