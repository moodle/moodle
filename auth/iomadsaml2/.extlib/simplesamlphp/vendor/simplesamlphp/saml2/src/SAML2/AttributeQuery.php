<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;

/**
 * Class for SAML 2 attribute query messages.
 *
 * An attribute query asks for a set of attributes. The following
 * rules apply:
 *
 * - If no attributes are present in the query, all attributes should be
 *   returned.
 * - If any attributes are present, only those attributes which are present
 *   in the query should be returned.
 * - If an attribute contains any attribute values, only the attribute values
 *   which match those in the query should be returned.
 *
 * @package SimpleSAMLphp
 */
class AttributeQuery extends SubjectQuery
{
    /**
     * The attributes, as an associative array.
     *
     * @var array
     */
    private $attributes = [];

    /**
     * The NameFormat used on all attributes.
     *
     * If more than one NameFormat is used, this will contain
     * the unspecified nameformat.
     *
     * @var string
     */
    private $nameFormat;


    /**
     * Constructor for SAML 2 attribute query messages.
     *
     * @param \DOMElement|null $xml The input message.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('AttributeQuery', $xml);

        $this->attributes = [];
        $this->nameFormat = Constants::NAMEFORMAT_UNSPECIFIED;

        if ($xml === null) {
            return;
        }

        $firstAttribute = true;
        /** @var \DOMElement[] $attributes */
        $attributes = Utils::xpQuery($xml, './saml_assertion:Attribute');
        foreach ($attributes as $attribute) {
            if (!$attribute->hasAttribute('Name')) {
                throw new \Exception('Missing name on <saml:Attribute> element.');
            }
            $name = $attribute->getAttribute('Name');

            if ($attribute->hasAttribute('NameFormat')) {
                $nameFormat = $attribute->getAttribute('NameFormat');
            } else {
                $nameFormat = Constants::NAMEFORMAT_UNSPECIFIED;
            }

            if ($firstAttribute) {
                $this->nameFormat = $nameFormat;
                $firstAttribute = false;
            } else {
                if ($this->nameFormat !== $nameFormat) {
                    $this->nameFormat = Constants::NAMEFORMAT_UNSPECIFIED;
                }
            }

            if (!array_key_exists($name, $this->attributes)) {
                $this->attributes[$name] = [];
            }

            $values = Utils::xpQuery($attribute, './saml_assertion:AttributeValue');
            foreach ($values as $value) {
                $this->attributes[$name][] = trim($value->textContent);
            }
        }
    }


    /**
     * Retrieve all requested attributes.
     *
     * @return array All requested attributes, as an associative array.
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }


    /**
     * Set all requested attributes.
     *
     * @param array $attributes All requested attributes, as an associative array.
     * @return void
     */
    public function setAttributes(array $attributes) : void
    {
        $this->attributes = $attributes;
    }


    /**
     * Retrieve the NameFormat used on all attributes.
     *
     * If more than one NameFormat is used in the received attributes, this
     * returns the unspecified NameFormat.
     *
     * @return string The NameFormat used on all attributes.
     */
    public function getAttributeNameFormat() : string
    {
        return $this->nameFormat;
    }


    /**
     * Set the NameFormat used on all attributes.
     *
     * @param string $nameFormat The NameFormat used on all attributes.
     * @return void
     */
    public function setAttributeNameFormat(string $nameFormat) : void
    {
        $this->nameFormat = $nameFormat;
    }


    /**
     * Convert the attribute query message to an XML element.
     *
     * @return \DOMElement This attribute query.
     */
    public function toUnsignedXML() : DOMElement
    {
        $root = parent::toUnsignedXML();

        foreach ($this->attributes as $name => $values) {
            $attribute = $root->ownerDocument->createElementNS(Constants::NS_SAML, 'saml:Attribute');
            $root->appendChild($attribute);
            $attribute->setAttribute('Name', $name);

            if ($this->nameFormat !== Constants::NAMEFORMAT_UNSPECIFIED) {
                $attribute->setAttribute('NameFormat', $this->nameFormat);
            }

            foreach ($values as $value) {
                if (is_string($value)) {
                    $type = 'xs:string';
                } elseif (is_int($value)) {
                    $type = 'xs:integer';
                } else {
                    $type = null;
                }

                $attributeValue = Utils::addString(
                    $attribute,
                    Constants::NS_SAML,
                    'saml:AttributeValue',
                    strval($value)
                );
                if ($type !== null) {
                    $attributeValue->setAttributeNS(Constants::NS_XSI, 'xsi:type', $type);
                }
            }
        }

        return $root;
    }
}
