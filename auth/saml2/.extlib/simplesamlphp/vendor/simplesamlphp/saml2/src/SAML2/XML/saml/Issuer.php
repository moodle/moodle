<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

use DOMElement;

use SAML2\Constants;
use SAML2\DOMDocumentFactory;

/**
 * Class representing the saml:Issuer element.
 *
 * @author Jaime Pérez Crespo, UNINETT AS <jaime.perez@uninett.no>
 * @package simplesamlphp/saml2
 */
class Issuer extends NameIDType
{

    /**
     * @var string
     */
    protected $nodeName = 'saml:Issuer';

    /**
     * if $this->SAML2IssuerShowAll is set false
     * From saml-core-2.0-os 8.3.6, when the entity Format is used: "The NameQualifier, SPNameQualifier, and
     * SPProvidedID attributes MUST be omitted."
     *
     * if $this->SAML2IssuerShowAll is set true
     * when the entity Format is used: "The NameQualifier, SPNameQualifier, and SPProvidedID attributes are not omitted"
     * @see saml-core-2.0-os 8.3.6
     *
     * @var boolean
     */
    private $Saml2IssuerShowAll = false; //setting true breaks saml-core-2.0-os 8.3.6


    /**
     * Initialize a saml:NameIDType, either from scratch or from an existing \DOMElement.
     *
     * @param \DOMElement|null $xml The XML element we should load, if any.
     */
    public function __construct(DOMElement $xml = null)
    {
        /**
         * The format of this NameIDType.
         *
         * Defaults to urn:oasis:names:tc:SAML:2.0:nameid-format:entity:
         *
         * Indicates that the content of the element is the identifier of an entity that provides SAML-based services
         * (such as a SAML authority, requester, or responder) or is a participant in SAML profiles (such as a service
         * provider supporting the browser SSO profile). Such an identifier can be used in the <Issuer> element to
         * identify the issuer of a SAML request, response, or assertion, or within the <NameID> element to make
         * assertions about system entities that can issue SAML requests, responses, and assertions. It can also be
         * used in other elements and attributes whose purpose is to identify a system entity in various protocol
         * exchanges.
         *
         * The syntax of such an identifier is a URI of not more than 1024 characters in length. It is RECOMMENDED that
         * a system entity use a URL containing its own domain name to identify itself.
         *
         * @see saml-core-2.0-os
         *
         * @var string
         */
        $this->setFormat(Constants::NAMEID_ENTITY);

        parent::__construct($xml);
    }


    /**
     * Collect the value of the Saml2IssuerShowAll-property
     *
     * @return bool
     */
    public function isSaml2IssuerShowAll() : bool
    {
        return $this->Saml2IssuerShowAll;
    }


    /**
     * Set the value of the Saml2IssuerShowAll-property
     *
     * @param bool $saml2IssuerShowAll
     * @return void
     */
    public function setSaml2IssuerShowAll(bool $saml2IssuerShowAll) : void
    {
        $this->Saml2IssuerShowAll = $saml2IssuerShowAll;
    }


    /**
     * Convert this Issuer to XML.
     *
     * @param \DOMElement|null $parent The element we should append to.
     * @return \DOMElement The current Issuer object converted into a \DOMElement.
     */
    public function toXML(DOMElement $parent = null) : DOMElement
    {
        if (($this->Saml2IssuerShowAll && ($this->Format === Constants::NAMEID_ENTITY))
            || ($this->Format !== Constants::NAMEID_ENTITY)
        ) {
            return parent::toXML($parent);
        }

        /*
         * if $this->Saml2IssuerShowAll is set false
         * From saml-core-2.0-os 8.3.6, when the entity Format is used: "The NameQualifier, SPNameQualifier, and
         * SPProvidedID attributes MUST be omitted."
         * if $this->isSaml2IssuerShowAll() is set true when the entity Format is used: "The NameQualifier,
         * SPNameQualifier, and SPProvidedID attributes are not omitted."
         */

        if ($parent === null) {
            $parent = DOMDocumentFactory::create();
            $doc = $parent;
        } else {
            $doc = $parent->ownerDocument;
        }
        $element = $doc->createElementNS(Constants::NS_SAML, 'saml:Issuer');
        $parent->appendChild($element);

        if (empty($this->value)) {
            throw new \Exception("Cannot convert Issuer to XML with no value.");
        }
        $value = $element->ownerDocument->createTextNode($this->value);
        $element->appendChild($value);

        return $element;
    }
}
