<?php
/**
 * SAML NameIDType abstract data type.
 *
 * @author Jaime Pérez Crespo, UNINETT AS <jaime.perez@uninett.no>
 * @package simplesamlphp/saml2
 */

declare(strict_types=1);

namespace SAML2\XML\saml;

use DOMElement;

abstract class NameIDType extends BaseIDType
{
    /**
     * A URI reference representing the classification of string-based identifier information. See Section 8.3 for the
     * SAML-defined URI references that MAY be used as the value of the Format attribute and their associated
     * descriptions and processing rules. Unless otherwise specified by an element based on this type, if no Format
     * value is provided, then the value urn:oasis:names:tc:SAML:1.0:nameid-format:unspecified (see Section 8.3.1) is in
     * effect.
     *
     * When a Format value other than one specified in Section 8.3 is used, the content of an element of this type is to
     * be interpreted according to the definition of that format as provided outside of this specification. If not
     * otherwise indicated by the definition of the format, issues of anonymity, pseudonymity, and the persistence of
     * the identifier with respect to the asserting and relying parties are implementation-specific.
     *
     * @var string|null
     *
     * @see saml-core-2.0-os
     */
    protected $Format = null;

    /**
     * A name identifier established by a service provider or affiliation of providers for the entity, if different from
     * the primary name identifier given in the content of the element. This attribute provides a means of integrating
     * the use of SAML with existing identifiers already in use by a service provider. For example, an existing
     * identifier can be "attached" to the entity using the Name Identifier Management protocol defined in Section 3.6.
     *
     * @var string|null
     *
     * @see saml-core-2.0-os
     */
    protected $SPProvidedID = null;

    /**
     * The NameIDType complex type is used when an element serves to represent an entity by a string-valued name.
     *
     * @var string
     */
    protected $value = '';


    /**
     * Initialize a saml:NameIDType, either from scratch or from an existing \DOMElement.
     *
     * @param \DOMElement|null $xml The XML element we should load, if any.
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct($xml);

        if ($xml === null) {
            return;
        }

        if ($xml->hasAttribute('Format')) {
            $this->Format = $xml->getAttribute('Format');
        }

        if ($xml->hasAttribute('SPProvidedID')) {
            $this->SPProvidedID = $xml->getAttribute('SPProvidedID');
        }

        $this->value = trim($xml->textContent);
    }


    /**
     * Collect the value of the Format-property
     *
     * @return string|null
     */
    public function getFormat() : ?string
    {
        return $this->Format;
    }


    /**
     * Set the value of the Format-property
     *
     * @param string|null $format
     * @return void
     */
    public function setFormat(string $format = null) : void
    {
        $this->Format = $format;
    }


    /**
     * Collect the value of the value-property
     *
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }


    /**
     * Set the value of the value-property
     * @param string $value
     *
     * @return void
     */
    public function setValue(string $value) : void
    {
        $this->value = $value;
    }


    /**
     * Collect the value of the SPProvidedID-property
     *
     * @return string|null
     */
    public function getSPProvidedID() : ?string
    {
        return $this->SPProvidedID;
    }


    /**
     * Set the value of the SPProvidedID-property
     *
     * @param string|null $spProvidedID
     * @return void
     */
    public function setSPProvidedID(string $spProvidedID = null) : void
    {
        $this->SPProvidedID = $spProvidedID;
    }


    /**
     * Convert this NameIDType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this NameIDType.
     */
    public function toXML(DOMElement $parent = null) : DOMElement
    {
        $element = parent::toXML($parent);

        if ($this->Format !== null) {
            $element->setAttribute('Format', $this->Format);
        }

        if ($this->SPProvidedID !== null) {
            $element->setAttribute('SPProvidedID', $this->SPProvidedID);
        }

        $value = $element->ownerDocument->createTextNode($this->value);
        $element->appendChild($value);

        return $element;
    }
}
